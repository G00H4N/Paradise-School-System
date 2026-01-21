import React from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

export default function Salaries() {
    const { salaries, staff_list } = usePage<any>().props; // Backend se data aana chahiye
    const { data, setData, post, processing } = useForm({
        month: new Date().toISOString().slice(0, 7), // YYYY-MM
    });

    const generatePayroll = (e: React.FormEvent) => {
        e.preventDefault();
        if(confirm(`Generate Salaries for ${data.month}?`)) {
            post(route('hr.salaries.generate'));
        }
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'HR', href: '#' }, { title: 'Payroll', href: '#' }]}>
            <Head title="Payroll Management" />
            <div className="p-6 max-w-7xl mx-auto">
                
                {/* Generation Card */}
                <div className="bg-white p-6 rounded-xl shadow-sm border mb-8 flex justify-between items-center">
                    <div>
                        <h2 className="text-2xl font-bold">Staff Payroll</h2>
                        <p className="text-gray-500">Auto-calculate salaries based on attendance</p>
                    </div>
                    <form onSubmit={generatePayroll} className="flex gap-3 items-center">
                        <input type="month" value={data.month} onChange={e => setData('month', e.target.value)} className="border rounded p-2" required />
                        <button disabled={processing} className="bg-indigo-600 text-white px-6 py-2 rounded font-bold hover:bg-indigo-700">
                            {processing ? 'Calculating...' : 'Generate Payroll'}
                        </button>
                    </form>
                </div>

                {/* Salary List */}
                <div className="bg-white rounded-xl shadow border overflow-hidden">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 border-b">
                            <tr>
                                <th className="p-4">Staff Name</th>
                                <th className="p-4">Basic Salary</th>
                                <th className="p-4 text-center">Deductions (Loan/Absent)</th>
                                <th className="p-4 text-right">Net Payable</th>
                                <th className="p-4 text-center">Status</th>
                                <th className="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {salaries?.data?.map((salary: any) => (
                                <tr key={salary.id} className="border-b hover:bg-gray-50">
                                    <td className="p-4 font-bold">{salary.staff.name}</td>
                                    <td className="p-4">Rs. {salary.basic_salary.toLocaleString()}</td>
                                    <td className="p-4 text-center text-red-500">- Rs. {salary.total_deductions}</td>
                                    <td className="p-4 text-right font-bold text-green-700">Rs. {salary.net_salary.toLocaleString()}</td>
                                    <td className="p-4 text-center">
                                        <span className={`px-2 py-1 rounded text-xs uppercase ${salary.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`}>
                                            {salary.status}
                                        </span>
                                    </td>
                                    <td className="p-4 text-center">
                                        {salary.status !== 'paid' && (
                                            <button className="text-blue-600 font-bold hover:underline">Pay Now</button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                            {!salaries?.data?.length && (
                                <tr><td colSpan={6} className="p-8 text-center text-gray-400">No payroll generated for this month.</td></tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}