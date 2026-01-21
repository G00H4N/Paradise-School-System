<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeeInvoice;
use App\Models\ParentWallet;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\FeeType;
use App\Models\User; // ✅ Added User Model Import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeController extends Controller
{
    // ... (Baqi purane functions same rahenge: create, store, showFeeCard, payFromWallet) ...

    public function create()
    {
        return inertia('Fees/Generate', [
            'classes' => SchoolClass::all(),
            'feeTypes' => FeeType::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'fee_type_id' => 'required|exists:fee_types,id',
            'month_year' => 'required',
            'due_date' => 'required|date'
        ]);

        $feeType = FeeType::find($request->fee_type_id);

        $query = Student::where('is_active', true);
        if ($request->class_id !== 'all') {
            $query->where('class_id', $request->class_id);
        }
        $students = $query->get();

        $generatedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                // Duplicate Check
                $exists = FeeInvoice::where('student_id', $student->id)
                    ->where('fee_type_id', $request->fee_type_id)
                    ->where('invoice_title', 'like', "%{$request->month_year}%")
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                // Sibling Discount Logic
                $discount = 0;
                if ($student->parent_id) {
                    $siblings = Student::where('parent_id', $student->parent_id)->count();
                    if ($siblings > 1) {
                        $discount = $feeType->default_amount * 0.10; // 10% Discount
                    }
                }

                FeeInvoice::create([
                    'student_id' => $student->id,
                    'fee_type_id' => $feeType->id,
                    'invoice_title' => "{$feeType->fee_title} - {$request->month_year}",
                    'total_amount' => $feeType->default_amount,
                    'discount_amount' => $discount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                    'due_date' => $request->due_date,
                    'session_year' => '2026-2027',
                ]);

                $generatedCount++;
            }

            DB::commit();
            return redirect()->route('fees.create')->with('success', "Done! Generated: {$generatedCount}, Skipped: {$skippedCount}");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function showFeeCard(Student $student)
    {
        $invoices = FeeInvoice::where('student_id', $student->id)->orderBy('id', 'desc')->get();
        $wallet = ParentWallet::where('parent_user_id', $student->parent_id)->first();

        return inertia('Students/FeeCard', [
            'student' => $student,
            'invoices' => $invoices,
            'wallet_balance' => $wallet ? $wallet->balance : 0
        ]);
    }

    public function payFromWallet($invoice_id)
    {
        $invoice = FeeInvoice::with('student')->findOrFail($invoice_id);
        $wallet = ParentWallet::where('parent_user_id', $invoice->student->parent_id)->first();

        if ($wallet && $wallet->balance >= ($invoice->total_amount - $invoice->discount_amount)) {
            DB::beginTransaction();
            try {
                $payable = $invoice->total_amount - $invoice->discount_amount;
                $wallet->decrement('balance', $payable);
                $invoice->update(['paid_amount' => $payable, 'status' => 'paid']);

                FeePayment::create([
                    'fee_invoice_id' => $invoice->id,
                    'amount_paid' => $payable,
                    'payment_date' => now(),
                    'received_by' => auth()->id(),
                    'payment_method' => 'Wallet'
                ]);

                DB::commit();
                return response()->json(['message' => 'Paid via Wallet!']);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'Insufficient Balance'], 400);
    }

    public function downloadChallan($id)
    {
        $invoice = FeeInvoice::with(['student.schoolClass', 'feeType'])->findOrFail($id);
        $grandTotal = $invoice->total_amount - $invoice->discount_amount;

        $data = [
            'invoice' => $invoice,
            'student' => $invoice->student,
            'class' => $invoice->student->schoolClass,
            'grandTotal' => $grandTotal,
            'school_name' => 'Paradise Public Girls Elementary School',
            'due_date_formatted' => \Carbon\Carbon::parse($invoice->due_date)->format('d-M-Y'),
        ];

        $pdf = Pdf::loadView('fees.challan_pdf', $data);
        return $pdf->stream("Challan-{$invoice->student->admission_no}.pdf");
    }

    // ✅ NEW: Family Fee Voucher (Spec Requirement)
    public function downloadFamilyChallan($parent_id, $month_title)
    {
        // 1. Validate Parent
        $parent = User::findOrFail($parent_id);
        if ($parent->role !== 'parent') {
            return back()->withErrors(['error' => 'User is not a parent.']);
        }

        // 2. Parent ke tamam bachon ki IDs nikalo
        $studentIds = Student::where('parent_id', $parent_id)->pluck('id');

        // 3. Un bachon ki specific month ki invoices fetch karo
        $invoices = FeeInvoice::with(['student.schoolClass', 'feeType'])
            ->whereIn('student_id', $studentIds)
            ->where('invoice_title', 'like', "%{$month_title}%")
            ->get();

        if ($invoices->isEmpty()) {
            return back()->withErrors(['error' => 'Is maheenay ki koi fees nahi mili is family ke liye.']);
        }

        // 4. Totals calculate karo
        $totalAmount = $invoices->sum('total_amount');
        $totalDiscount = $invoices->sum('discount_amount');
        $grandTotal = $totalAmount - $totalDiscount;

        $data = [
            'invoices' => $invoices,
            'parent' => $parent,
            'month' => $month_title,
            'totalAmount' => $totalAmount,
            'totalDiscount' => $totalDiscount,
            'grandTotal' => $grandTotal,
            'school_name' => 'Paradise Public Girls Elementary School',
            'due_date' => $invoices->first()->due_date,
        ];

        $pdf = Pdf::loadView('fees.family_challan_pdf', $data);
        return $pdf->stream("Family-Challan-{$parent->cnic}.pdf");
    }
    // Add this inside FeeController class
// ✅ NEW: Cash Payment Collection Logic
    public function payCash(Request $request, $id)
    {
        $invoice = FeeInvoice::findOrFail($id);

        $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|string'
        ]);

        DB::transaction(function () use ($invoice, $request) {
            // Update Paid Amount
            $invoice->paid_amount += $request->amount_paid;

            // Status Update Logic
            $payable = $invoice->total_amount - $invoice->discount_amount;
            if ($invoice->paid_amount >= $payable) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();

            // Record Transaction
            FeePayment::create([
                'fee_invoice_id' => $invoice->id,
                'amount_paid' => $request->amount_paid,
                'payment_method' => $request->payment_method, // 'Cash', 'Cheque' etc.
                'payment_date' => $request->payment_date ?? now(),
                'received_by' => auth()->id()
            ]);
        });

        return redirect()->route('fees.index')->with('success', 'Payment Collected Successfully!');
    }
}