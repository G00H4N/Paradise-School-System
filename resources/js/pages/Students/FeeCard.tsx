import React from 'react';
import { Head, usePage, Link } from '@inertiajs/react';

interface FeeInvoice {
    id: number;
    invoice_title: string;
    total_amount: number;
    discount_amount: number;
    paid_amount: number;
    status: string;
    due_date: string;
    payments: { id: number; amount_paid: number; payment_date: string; payment_method: string }[];
}

interface PageProps {
    student: {
        id: number;
        full_name: string;
        admission_no: string;
        father_name: string;
        school_class: { class_name: string };
    };
    invoices: FeeInvoice[];
    wallet_balance: number;
    [key: string]: any;
}

export default function FeeCard() {
    const { student, invoices, wallet_balance } = usePage<PageProps>().props;

    return (
        <div className="min-h-screen bg-gray-50 p-6 md:p-10">
            <Head title={`Fee Card - ${student.full_name}`} />

            <div className="max-w-6xl mx-auto">
                {/* Profile Header */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h2 className="text-2xl font-black text-gray-800 uppercase">{student.full_name}</h2>
                        <p className="text-gray-500 text-sm">Father: {student.father_name} | Class: {student.school_class.class_name}</p>
                    </div>
                    <div className="mt-4 md:mt-0 text-right">
                        <div className="text-sm text-gray-500">Wallet Balance</div>
                        <div className="text-2xl font-bold text-green-600">Rs. {wallet_balance.toLocaleString()}</div>
                    </div>
                </div>

                {/* Ledger Table */}
                <div className="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-blue-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-bold text-blue-900 uppercase">Details</th>
                                <th className="px-6 py-3 text-center text-xs font-bold text-blue-900 uppercase">Status</th>
                                <th className="px-6 py-3 text-right text-xs font-bold text-blue-900 uppercase">Total</th>
                                <th className="px-6 py-3 text-right text-xs font-bold text-blue-900 uppercase">Paid</th>
                                <th className="px-6 py-3 text-right text-xs font-bold text-blue-900 uppercase">Balance</th>
                                <th className="px-6 py-3 text-center text-xs font-bold text-blue-900 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {invoices.map((inv) => {
                                const netTotal = inv.total_amount - inv.discount_amount;
                                const balance = netTotal - inv.paid_amount;
                                
                                return (
                                    <tr key={inv.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-gray-800">{inv.invoice_title}</div>
                                            <div className="text-xs text-gray-500">Due: {inv.due_date}</div>
                                            {inv.payments.length > 0 && (
                                                <div className="mt-1 text-xs text-gray-400">
                                                    Paid: {inv.payments.map(p => `${p.payment_date} (${p.payment_method})`).join(', ')}
                                                </div>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <span className={`px-2 py-1 rounded text-xs font-bold border uppercase 
                                                ${inv.status === 'paid' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'}`}>
                                                {inv.status}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right font-mono text-sm">{netTotal.toLocaleString()}</td>
                                        <td className="px-6 py-4 text-right font-mono text-sm text-green-600">{inv.paid_amount.toLocaleString()}</td>
                                        <td className="px-6 py-4 text-right font-mono text-sm font-bold text-red-600">{balance.toLocaleString()}</td>
                                        <td className="px-6 py-4 text-center">
                                            <a href={route('fees.challan', inv.id)} target="_blank" className="text-blue-600 hover:underline text-xs font-bold">Print</a>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}