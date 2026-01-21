import React, { useState } from 'react';
import { Head, usePage, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

// Types define kar rahe hain taake error na aaye
interface FeeInvoice {
    id: number;
    invoice_title: string;
    total_amount: number;
    paid_amount: number;
    discount_amount: number;
    status: 'paid' | 'unpaid' | 'partial';
    due_date: string;
    student: {
        id: number;
        full_name: string;
        admission_no: string;
        school_class: { class_name: string };
    };
    fee_type: { fee_title: string };
}

interface PageProps {
    invoices: { data: FeeInvoice[]; links: any[] };
    flash: { success?: string; error?: string };
    [key: string]: any;
}

export default function Index() {
    const { invoices, flash } = usePage<PageProps>().props;
    const [search, setSearch] = useState('');

    // Status ka color set karne ka logic
    const getStatusBadge = (status: string) => {
        const colors = {
            paid: 'bg-green-100 text-green-800 border-green-200',
            unpaid: 'bg-red-100 text-red-800 border-red-200',
            partial: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        };
        return colors[status as keyof typeof colors] || 'bg-gray-100';
    };

    // Wallet se payment katne ka function
    const handleWalletPay = (id: number) => {
        if(confirm("Kya aap waqayi Parent ke Wallet se paise katna chahte hain?")) {
            router.post(route('fees.pay_wallet', id));
        }
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Fees', href: '/fees' }]}>
            <Head title="Fee Management" />

            <div className="p-6">
                
                {/* Header Section */}
                <div className="flex flex-col md:flex-row justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                    <div>
                        <h2 className="text-2xl font-black text-gray-800 uppercase">Fee Invoices</h2>
                        <p className="text-gray-500 text-sm">Fees manage karein aur payment collect karein</p>
                    </div>
                    <div className="flex gap-3 mt-4 md:mt-0">
                        {/* New Fee Generate Button */}
                        <Link href={route('fees.create')} className="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-indigo-700 transition flex items-center gap-2">
                            <span>+</span> Generate Bulk Fees
                        </Link>
                    </div>
                </div>

                {/* Notifications (Success/Error) */}
                {flash.success && <div className="bg-green-100 text-green-800 p-3 rounded-lg mb-4 border border-green-200 shadow-sm">✅ {flash.success}</div>}
                {flash.error && <div className="bg-red-100 text-red-800 p-3 rounded-lg mb-4 border border-red-200 shadow-sm">⚠️ {flash.error}</div>}

                {/* Main Data Table */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Invoice ID</th>
                                <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Student Info</th>
                                <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Month / Title</th>
                                <th className="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {invoices.data.map((inv) => (
                                <tr key={inv.id} className="hover:bg-gray-50 transition duration-150">
                                    <td className="px-6 py-4 font-mono text-xs text-indigo-600 font-bold">
                                        #{inv.id}
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="font-bold text-gray-900">{inv.student.full_name}</div>
                                        <div className="text-xs text-gray-500">
                                            {inv.student.school_class.class_name} <span className="text-gray-300">|</span> {inv.student.admission_no}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="text-sm text-gray-700 font-medium">{inv.invoice_title}</div>
                                        <div className="text-xs text-gray-400">Due: {inv.due_date}</div>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="font-bold text-gray-900">
                                            Rs. {Number(inv.total_amount - inv.discount_amount).toLocaleString()}
                                        </div>
                                        {inv.discount_amount > 0 && (
                                            <span className="text-xs text-green-600 block">(Disc: {inv.discount_amount})</span>
                                        )}
                                        {inv.paid_amount > 0 && inv.status !== 'paid' && (
                                             <span className="text-xs text-blue-600 block">Paid: {inv.paid_amount}</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-center">
                                        <span className={`px-3 py-1 rounded-full text-xs font-bold border uppercase ${getStatusBadge(inv.status)}`}>
                                            {inv.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-center space-x-2">
                                        {/* Print Challan Button */}
                                        <a href={`/fees/challan/${inv.id}`} target="_blank" className="text-gray-500 hover:text-indigo-600 text-xs font-bold border border-gray-300 px-2 py-1 rounded hover:bg-gray-50">
                                            🖨 Print
                                        </a>
                                        
                                        {/* Collect Fee Button (Only if unpaid) */}
                                        {inv.status !== 'paid' && (
                                            <Link href={`/fees/collect/${inv.id}`} className="bg-green-600 text-white text-xs px-3 py-1 rounded hover:bg-green-700 font-bold shadow-sm">
                                                Collect 💵
                                            </Link>
                                        )}
                                    </td>
                                </tr>
                            ))}
                            
                            {invoices.data.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="px-6 py-12 text-center text-gray-400 italic bg-gray-50">
                                        Abhi tak koi fees generate nahi hui. <br/>
                                        <Link href={route('fees.create')} className="text-indigo-600 underline">Create First Invoice</Link>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}