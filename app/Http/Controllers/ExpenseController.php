<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    // 1. Expense List & Daily Report
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'user']);

        // Date Filter (Agar user ne date select ki ho, warna Aaj ki date)
        $date = $request->date ?? Carbon::today()->toDateString();
        $query->whereDate('expense_date', $date);

        $expenses = $query->latest()->get();

        // --- GAP ANALYSIS: Daily Balance Sheet Logic ---
        // Specification Video 1 @ 17:20 mein "Cash in Hand" dikhaya gaya hai
        $todaysIncome = FeePayment::whereDate('payment_date', $date)->sum('amount_paid');
        $todaysExpense = $expenses->sum('amount');
        $cashInHand = $todaysIncome - $todaysExpense;

        return inertia('Expenses/Index', [
            'expenses' => $expenses,
            'categories' => ExpenseCategory::all(),
            'summary' => [
                'income' => $todaysIncome,
                'expense' => $todaysExpense,
                'cash_in_hand' => $cashInHand,
                'date' => $date
            ]
        ]);
    }

    // 2. Store New Expense
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_category_id' => $request->expense_category_id,
            'expense_date' => $request->expense_date,
            'added_by' => auth()->id(), // Kon admin add kar raha hai
            'note' => $request->note
        ]);

        return redirect()->back()->with('success', 'Expense Added Successfully!');
    }

    // 3. Add Category (Dynamic Categories)
    public function storeCategory(Request $request)
    {
        $request->validate(['category_name' => 'required|string|unique:expense_categories,category_name']);
        ExpenseCategory::create(['category_name' => $request->category_name]);
        return redirect()->back();
    }
}