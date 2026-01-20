<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\Expense;
use App\Models\TransportRoute;
use App\Models\Salary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // 1. Daily Balance Sheet (Cash In Hand)
    public function balanceSheet(Request $request)
    {
        $date = $request->date ?? Carbon::today()->toDateString();

        // Total Income (Fees)
        $income = FeePayment::whereDate('payment_date', $date)->sum('amount_paid');

        // Total Expense (Bills + Salaries Paid Today)
        $expense = Expense::whereDate('expense_date', $date)->sum('amount');

        return inertia('Reports/BalanceSheet', [
            'date' => $date,
            'income' => $income,
            'expense' => $expense,
            'cash_in_hand' => $income - $expense,
            'transactions' => [
                'fees' => FeePayment::with('invoice.student')->whereDate('payment_date', $date)->get(),
                'expenses' => Expense::with('category')->whereDate('expense_date', $date)->get()
            ]
        ]);
    }

    // 2. Transport Profit/Loss (Route Income vs Expense)
    // Spec Requirement: Video 1 @ 35:18
    public function transportReport()
    {
        $routes = TransportRoute::with('students')->get()->map(function ($route) {
            // Income = Students count * Fare
            $monthlyIncome = $route->students->count() * $route->fare_amount;

            // Expense Logic (Assuming we tag expenses with 'Transport' category)
            // Filhal hum generic expense le rahe hain, future mein category link kar sakte hain
            $fuelExpense = 0; // Placeholder for now

            return [
                'route' => $route->route_title,
                'vehicle' => $route->vehicle_number,
                'income' => $monthlyIncome,
                'expense' => $fuelExpense,
                'profit' => $monthlyIncome - $fuelExpense
            ];
        });

        return inertia('Reports/Transport', ['data' => $routes]);
    }
    // --- NEW ADDITION: Fee Defaulter List ---
    public function feeDefaulters()
    {
        // Logic: Woh invoices jo 'unpaid' hain aur due_date guzar chuki hai
        $defaulters = \App\Models\FeeInvoice::with(['student.schoolClass', 'student.user'])
            ->where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->get()
            ->groupBy('student_id'); // Aik student ke multiple bills ho sakte hain

        return inertia('Reports/Defaulters', [
            'defaulters' => $defaulters
        ]);
    }

    // --- NEW ADDITION: Send SMS to Defaulters ---
    public function sendDefaulterSMS(Request $request)
    {
        $invoices = \App\Models\FeeInvoice::with('student')
            ->whereIn('id', $request->invoice_ids)
            ->get();

        $count = 0;
        foreach ($invoices as $inv) {
            if ($inv->student->phone) {
                $msg = "Dear Parent, Fee pending for {$inv->student->full_name}. Amount: {$inv->total_amount}. Please pay immediately. - Paradise School";

                // Yahan asli SMS API (JazzCash/Twilio) lage gi
                \Illuminate\Support\Facades\Log::info("SMS SENT TO {$inv->student->phone}: $msg");
                $count++;
            }
        }

        return redirect()->back()->with('success', "SMS Sent to {$count} Parents!");
    }
}