<?php

namespace App\Http\Controllers;

use App\Models\StaffDetail;
use App\Models\Salary;
use App\Models\Attendance;
use App\Models\StaffLoan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    // 1. Salary Generation Page
    public function index()
    {
        // Pichlay maheenay ki salary dikhane ke liye
        $salaries = Salary::with('staff.user')->latest()->paginate(10);
        return inertia('HR/Salaries', ['salaries' => $salaries]);
    }

    // 2. Generate Logic (Bulk) - Yeh Spec ka main feature hai
    public function generate(Request $request)
    {
        $request->validate(['month_year' => 'required']); // e.g., "01-2026"

        $month = Carbon::parse($request->month_year)->month;
        $year = Carbon::parse($request->month_year)->year;
        $monthStr = Carbon::parse($request->month_year)->format('F Y'); // "January 2026"

        $staffMembers = StaffDetail::where('is_active', true)->get();
        $generatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($staffMembers as $staff) {
                // Check if already generated
                $exists = Salary::where('staff_id', $staff->id)
                    ->where('salary_month', $monthStr)
                    ->exists();

                if ($exists)
                    continue;

                // A. Attendance Calculation
                // Staff Attendance count karo (using updated table)
                $presentDays = Attendance::where('staff_id', $staff->id)
                    ->whereMonth('attendance_date', $month)
                    ->whereYear('attendance_date', $year)
                    ->where('status', 1) // 1 = Present
                    ->count();

                // Basic Formula: (Basic Salary / 30) * Present Days
                // Lekin Spec mein fixed salary ka bhi option ho sakta hai. 
                // Filhal hum simple deduction logic lagate hain agar absent ho.

                // Let's assume 30 days standard
                $perDaySalary = $staff->basic_salary / 30;
                $calculatedSalary = $perDaySalary * $presentDays;
                // Note: Agar Sunday off paid hai to logic mazeed complex hogi, 
                // filhal Spec ke mutabiq "Biometric base" hai to present days count kar rahe hain.

                // B. Loan Deduction (Video 1 @ 15:25)
                $deduction = 0;
                $activeLoan = StaffLoan::where('staff_id', $staff->id)
                    ->where('status', 'active')
                    ->first();

                if ($activeLoan) {
                    $deduction = $activeLoan->monthly_installment;
                    // Check karein ke deduction salary se zyada na ho
                    if ($deduction > $calculatedSalary) {
                        $deduction = $calculatedSalary / 2; // Safety cap
                    }
                }

                // C. Final Net Salary
                $netSalary = $calculatedSalary - $deduction;

                Salary::create([
                    'staff_id' => $staff->id,
                    'salary_month' => $monthStr,
                    'basic_salary' => $staff->basic_salary,
                    'bonus' => 0, // Admin baad mein add kar sakta hai
                    'deductions' => $deduction,
                    'net_salary' => round($netSalary, 2),
                    'status' => 'unpaid'
                ]);

                $generatedCount++;
            }
            DB::commit();
            return redirect()->back()->with('success', "Generated Salaries for {$generatedCount} Staff Members.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 3. Pay Salary (Status Change)
    public function pay($id)
    {
        $salary = Salary::findOrFail($id);

        DB::beginTransaction();
        try {
            // Agar loan deduction thi, to Loan Balance kam karo
            if ($salary->deductions > 0) {
                $loan = StaffLoan::where('staff_id', $salary->staff_id)
                    ->where('status', 'active')
                    ->first();

                if ($loan) {
                    $loan->decrement('remaining_balance', $salary->deductions);
                    if ($loan->remaining_balance <= 0) {
                        $loan->update(['status' => 'cleared']);
                    }
                }
            }

            // Salary ko 'Paid' mark karo aur Expense mein daalo
            $salary->update(['status' => 'paid']);

            // Automatic Expense Entry (Accounting Logic)
            \App\Models\Expense::create([
                'title' => 'Salary Payment: ' . $salary->salary_month,
                'amount' => $salary->net_salary,
                'expense_category_id' => 1, // Ensure ID 1 is 'Salaries' category
                'expense_date' => now(),
                'added_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Salary Paid & Loan Deducted!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}