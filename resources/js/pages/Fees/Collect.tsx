import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';

interface FeeInvoice {
    id: number;
    invoice_title: string;
    total_amount: number;
    paid_amount: number;
    discount_amount: number;
    status: string;
    student: {
        id: number;
        full_name: string;
        admission_no: string;
        school_class: { class_name: string };
    };
}

interface PageProps {
    invoice: FeeInvoice;
    [key: string]: any;
}

export default function Collect() {
    const { invoice } = usePage<PageProps>().props;
    const pendingAmount = invoice.total_amount - invoice.discount_amount - invoice.paid_amount;

    const { data, setData, post, processing, errors } = useForm({
        amount_paid: pendingAmount, // Auto-fill pending amount
        payment_method: 'Cash',
        payment_date: new Date().toISOString().split('T')[0],
        remarks: ''
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (confirm(`Confirm payment of Rs. ${data.amount_paid}?`)) {
            // Note: Route check kar lein, shayad humne 'fees.pay' banaya tha ya 'pay-wallet'. 
            // Agar generic payment route nahi hai to hum 'pay-wallet' use kar sakte hain ya naya route add karna parega.
            // Backend Audit: Humne 'payFromWallet' banaya tha lekin 'Cash' payment ka route miss ho gaya tha shayad.
            // Temporary Fix: Hum isay wallet route par bhejte hain ya naya route banate hain.
            // Sahi tareeqa: Naya route hona chahiye. 
            // Lekin abhi ke liye main assume kar raha hoon ke aap 'fees.pay' route add kar lenge.
            
            // Wait! Route list mein check karte hain...
            // Route List: Route::post('/pay-wallet/{invoice}') hai. Manual cash payment ka route MISSING hai.
            // FIX: Main neechay route update de raha hoon.
            post(`/fees/pay-cash/${invoice.id}`);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50 p-6 flex items-center justify-center">
            <Head title="Collect Fee" />
            
            <div className="bg-white w-full max-w-lg rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div className="bg-green-700 p-6 text-white text-center">
                    <h2 className="text-2xl font-bold uppercase">Collect Fee Payment</h2>
                    <p className="opacity-90">{invoice.invoice_title}</p>
                </div>

                <div className="p-6 space-y-6">
                    {/* Student Info Card */}
                    <div className="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm">
                        <div className="flex justify-between mb-2">
                            <span className="text-gray-500">Student:</span>
                            <span className="font-bold text-gray-800">{invoice.student.full_name}</span>
                        </div>
                        <div className="flex justify-between mb-2">
                            <span className="text-gray-500">Class:</span>
                            <span className="font-bold text-gray-800">{invoice.student.school_class.class_name}</span>
                        </div>
                        <div className="flex justify-between border-t pt-2 mt-2">
                            <span className="text-gray-600 font-bold">Total Pending:</span>
                            <span className="text-red-600 font-bold text-lg">Rs. {pendingAmount.toLocaleString()}</span>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-1">Amount Receiving (Rs) *</label>
                            <input 
                                type="number" 
                                value={data.amount_paid} 
                                onChange={e => setData('amount_paid', Number(e.target.value))}
                                className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 font-mono text-lg"
                                max={pendingAmount}
                                required 
                            />
                            {errors.amount_paid && <p className="text-red-500 text-xs mt-1">{errors.amount_paid}</p>}
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-bold text-gray-700 mb-1">Payment Method</label>
                                <select 
                                    value={data.payment_method} 
                                    onChange={e => setData('payment_method', e.target.value)}
                                    className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500"
                                >
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-gray-700 mb-1">Date</label>
                                <input 
                                    type="date" 
                                    value={data.payment_date} 
                                    onChange={e => setData('payment_date', e.target.value)}
                                    className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-1">Remarks (Optional)</label>
                            <textarea 
                                value={data.remarks}
                                onChange={e => setData('remarks', e.target.value)}
                                className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500"
                                rows={2}
                            ></textarea>
                        </div>

                        <button 
                            type="submit" 
                            disabled={processing}
                            className="w-full bg-green-600 text-white py-3 rounded-lg font-bold shadow-md hover:bg-green-700 transition disabled:opacity-50"
                        >
                            {processing ? 'Processing...' : 'Confirm Payment'}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}