<?php

namespace App\Http\Controllers;

use App\Models\StaffLoan;
use App\Models\StaffDetail;
use Illuminate\Http\Request;

class StaffLoanController extends Controller
{
    public function index()
    {
        return inertia('HR/Loans', [
            'loans' => StaffLoan::with('staff.user')->latest()->get(),
            'staff' => StaffDetail::with('user')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_details,id',
            'loan_amount' => 'required|numeric|min:1000',
            'monthly_installment' => 'required|numeric',
        ]);

        // Auto-calculate installments count
        $installments = ceil($request->loan_amount / $request->monthly_installment);

        StaffLoan::create([
            'staff_id' => $request->staff_id,
            'loan_amount' => $request->loan_amount,
            'monthly_installment' => $request->monthly_installment,
            'total_installments' => $installments,
            'remaining_balance' => $request->loan_amount,
            'status' => 'active'
        ]);

        return redirect()->back()->with('success', 'Loan Approved Successfully!');
    }
}