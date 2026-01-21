import React, { useState } from 'react';
import { Head, useForm, usePage, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

interface Exam {
    id: number;
    exam_title: string;
    start_date: string;
    session_year: string;
}

interface PageProps {
    exams: Exam[];
    flash: { success?: string; error?: string };
    [key: string]: any;
}

export default function Index() {
    const { exams, flash } = usePage<PageProps>().props;
    const [showForm, setShowForm] = useState(false);

    // Form for New Exam
    const { data, setData, post, processing, reset, errors } = useForm({
        exam_title: '',
        start_date: '',
        session_year: '2026-2027'
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('exams.store'), {
            onSuccess: () => {
                reset();
                setShowForm(false);
            }
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Academics', href: '#' }, { title: 'Exams', href: '/exams' }]}>
            <Head title="Exam Management" />

            <div className="p-6 max-w-7xl mx-auto">
                
                {/* Header & Actions */}
                <div className="flex justify-between items-center mb-6 bg-white p-5 rounded-xl shadow-sm border">
                    <div>
                        <h2 className="text-2xl font-black text-gray-800 uppercase">Examination Control</h2>
                        <p className="text-gray-500 text-sm">Manage Datesheets & Results</p>
                    </div>
                    <div className="flex gap-3">
                         <Link href={route('exams.marks_entry')} className="bg-green-600 text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-green-700 transition">
                            📊 Marks Entry
                        </Link>
                        <button onClick={() => setShowForm(!showForm)} className="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-indigo-700 transition">
                            {showForm ? 'Close Form' : '+ New Exam'}
                        </button>
                    </div>
                </div>

                {/* Notification */}
                {flash.success && <div className="bg-green-100 text-green-800 p-3 rounded mb-4">{flash.success}</div>}

                {/* Create Exam Form (Toggle) */}
                {showForm && (
                    <div className="bg-indigo-50 p-6 rounded-xl border border-indigo-100 mb-8 animation-fade-in">
                        <h3 className="font-bold text-lg text-indigo-900 mb-4">Create New Exam Term</h3>
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label className="block text-sm font-bold text-gray-700 mb-1">Exam Title</label>
                                <input type="text" placeholder="e.g. Mid Term 2026" className="w-full border rounded-lg p-2" 
                                    value={data.exam_title} onChange={e => setData('exam_title', e.target.value)} required />
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-gray-700 mb-1">Start Date</label>
                                <input type="date" className="w-full border rounded-lg p-2" 
                                    value={data.start_date} onChange={e => setData('start_date', e.target.value)} required />
                            </div>
                            <div>
                                <label className="block text-sm font-bold text-gray-700 mb-1">Session</label>
                                <input type="text" className="w-full border rounded-lg p-2" 
                                    value={data.session_year} onChange={e => setData('session_year', e.target.value)} required />
                            </div>
                            <button type="submit" disabled={processing} className="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 h-10">
                                {processing ? 'Saving...' : 'Save Exam'}
                            </button>
                        </form>
                    </div>
                )}

                {/* Exams List */}
                <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs">Exam Title</th>
                                <th className="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs">Session</th>
                                <th className="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs">Start Date</th>
                                <th className="px-6 py-4 text-center font-bold text-gray-500 uppercase text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {exams.map((exam) => (
                                <tr key={exam.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-bold text-gray-800">{exam.exam_title}</td>
                                    <td className="px-6 py-4 text-gray-600">{exam.session_year}</td>
                                    <td className="px-6 py-4 text-gray-600">{exam.start_date}</td>
                                    <td className="px-6 py-4 text-center">
                                        <div className="flex justify-center gap-2">
                                            {/* Reports Links */}
                                            <a href={`/exams/tabulation?exam_id=${exam.id}&class_id=1`} target="_blank" className="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold hover:bg-blue-200">
                                                Gazette
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {exams.length === 0 && (
                                <tr>
                                    <td colSpan={4} className="px-6 py-10 text-center text-gray-400">No exams created yet.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

            </div>
        </AppLayout>
    );
}