<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Expense;
use App\Models\StaffDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;

        // 1. Cards Data (Video 1 @ 02:11)
        $unpaidInvoices = FeeInvoice::where('status', '!=', 'paid')->sum('total_amount') - FeeInvoice::where('status', '!=', 'paid')->sum('paid_amount');
        $incomeToday = FeePayment::whereDate('payment_date', $today)->sum('amount_paid');
        $incomeMonth = FeePayment::whereMonth('payment_date', $thisMonth)->sum('amount_paid');
        $expenseToday = Expense::whereDate('expense_date', $today)->sum('amount');

        // 2. Counters (Video 1 @ 02:26)
        $totalStudents = Student::where('is_active', true)->count();
        $totalStaff = StaffDetail::where('is_active', true)->count();
        $presentToday = \App\Models\Attendance::whereDate('attendance_date', $today)
            ->where('status', 1)->count();

        // 3. Graph Data (Monthly Income vs Expense)
        $graphData = $this->getMonthlyGraphData();

        return inertia('Dashboard', [
            'stats' => [
                'unpaid_dues' => $unpaidInvoices,
                'income_today' => $incomeToday,
                'income_month' => $incomeMonth,
                'expense_today' => $expenseToday,
                'profit_loss' => $incomeToday - $expenseToday
            ],
            'counts' => [
                'students' => $totalStudents,
                'staff' => $totalStaff,
                'present' => $presentToday
            ],
            'graph' => $graphData
        ]);
    }

    private function getMonthlyGraphData()
    {
        // Last 6 months graph logic
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');

            $income = FeePayment::whereMonth('payment_date', $date->month)->sum('amount_paid');
            $expense = Expense::whereMonth('expense_date', $date->month)->sum('amount');

            $data[] = ['name' => $monthName, 'income' => $income, 'expense' => $expense];
        }
        return $data;
    }
}