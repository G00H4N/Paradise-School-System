import React from 'react';
import { useForm, usePage } from '@inertiajs/react';

interface PageProps {
    classes: { id: number | string; class_name: string }[];
    feeTypes: { id: number; fee_title: string; default_amount: number }[];
    flash: { success?: string; error?: string };
    [key: string]: any;
}

export default function Generate() {
    const { classes, feeTypes, flash } = usePage<PageProps>().props;
    
    const { data, setData, post, processing, errors } = useForm({
        class_id: '',
        fee_type_id: '',
        month_year: '', // e.g., "August 2026"
        due_date: ''
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if(!confirm('Are you sure you want to generate fees for all selected students?')) return;
        post('/fees/store');
    };

    return (
        <div className="min-h-screen bg-gray-50 p-6 md:p-12">
            <div className="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                
                {/* Header */}
                <div className="bg-indigo-700 p-6 text-white">
                    <h2 className="text-2xl font-bold uppercase tracking-wide">Generate Monthly Fees</h2>
                    <p className="opacity-80 text-sm mt-1">Bulk Invoice Generation Module</p>
                </div>

                {/* Success/Error Message */}
                {flash.success && (
                    <div className="bg-green-100 text-green-800 p-4 border-b border-green-200 font-medium">
                        ✅ {flash.success}
                    </div>
                )}
                {flash.error && (
                    <div className="bg-red-100 text-red-800 p-4 border-b border-red-200 font-medium">
                        ⚠️ {flash.error}
                    </div>
                )}

                <form onSubmit={handleSubmit} className="p-8 space-y-6">
                    
                    {/* Month Selection */}
                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Billing Month (Month Year)</label>
                        <input 
                            type="month" 
                            className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                            onChange={e => {
                                // Convert "2026-08" to "August 2026" for cleaner invoice titles
                                const date = new Date(e.target.value);
                                const formatted = date.toLocaleString('default', { month: 'long', year: 'numeric' });
                                setData('month_year', formatted);
                            }}
                            required
                        />
                         <p className="text-xs text-gray-400 mt-1">Selected: {data.month_year}</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Class Selection */}
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Target Class</label>
                            <select 
                                value={data.class_id} 
                                onChange={e => setData('class_id', e.target.value)} 
                                className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                                required
                            >
                                <option value="">Select Class</option>
                                <option value="all" className="font-bold text-indigo-600">-- ALL CLASSES (Whole School) --</option>
                                {classes.map(c => (
                                    <option key={c.id} value={c.id}>{c.class_name}</option>
                                ))}
                            </select>
                        </div>

                        {/* Fee Type Selection */}
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Fee Type</label>
                            <select 
                                value={data.fee_type_id} 
                                onChange={e => setData('fee_type_id', e.target.value)} 
                                className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                                required
                            >
                                <option value="">Select Fee Type</option>
                                {feeTypes.map(ft => (
                                    <option key={ft.id} value={ft.id}>
                                        {ft.fee_title} (Rs. {ft.default_amount})
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Due Date */}
                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Due Date</label>
                        <input 
                            type="date" 
                            value={data.due_date}
                            onChange={e => setData('due_date', e.target.value)}
                            className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                            required
                        />
                    </div>

                    {/* Submit Button */}
                    <div className="pt-4 border-t border-gray-100">
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold text-lg shadow-md hover:bg-indigo-700 transition-all disabled:opacity-50"
                        >
                            {processing ? 'Processing Bulk Invoices...' : 'Generate Invoices Now'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}