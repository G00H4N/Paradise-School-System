import React, { useEffect } from 'react';
import { Head, useForm, usePage, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

// Interfaces
interface Student {
    id: number;
    full_name: string;
    roll_no: string;
    obtained?: number; // Pehlay se agar number lagay hain
    comment?: string;
}

interface PageProps {
    exams: { id: number; exam_title: string }[];
    classes: { id: number; class_name: string }[];
    subjects: { id: number; subject_name: string; total_marks: number }[];
    students: Student[];
    filters: { exam_id?: string; class_id?: string; subject_id?: string };
    flash: { success?: string; error?: string };
    [key: string]: any;
}

export default function MarksEntry() {
    const { exams, classes, subjects, students, filters, flash } = usePage<PageProps>().props;

    // Form data for filtering
    const { data, setData, get } = useForm({
        exam_id: filters.exam_id || '',
        class_id: filters.class_id || '',
        subject_id: filters.subject_id || '',
    });

    // Form data for Marks Submission
    const marksForm = useForm({
        exam_id: filters.exam_id,
        subject_id: filters.subject_id,
        marks_data: [] as { student_id: number; obtained: number; comment: string }[]
    });

    // Auto-fetch data when filters change
    const handleFilterChange = (key: string, value: string) => {
        setData(key as any, value);
        // Sirf tab refresh karo agar teeno select na hon (UX Logic)
        // Ya hum button bhi laga sakte hain "Load Students"
    };

    const loadStudents = (e: React.FormEvent) => {
        e.preventDefault();
        get(route('exams.marks_entry'), { preserveState: true });
    };

    // Initialize Marks Data when students load
    useEffect(() => {
        if (students.length > 0) {
            const initialData = students.map(std => ({
                student_id: std.id,
                obtained: std.obtained || 0,
                comment: std.comment || ''
            }));
            marksForm.setData('marks_data', initialData);
            marksForm.setData('exam_id', data.exam_id);
            marksForm.setData('subject_id', data.subject_id);
        }
    }, [students]);

    // Handle Input Change for a specific student
    const handleMarkChange = (index: number, field: 'obtained' | 'comment', value: any) => {
        const newData = [...marksForm.data.marks_data];
        newData[index] = { ...newData[index], [field]: value };
        marksForm.setData('marks_data', newData);
    };

    const saveMarks = (e: React.FormEvent) => {
        e.preventDefault();
        marksForm.post(route('exams.save_marks'), {
            preserveScroll: true,
            onSuccess: () => alert('Marks Saved Successfully!')
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Exams', href: '/exams' }, { title: 'Marks Entry', href: '#' }]}>
            <Head title="Marks Entry" />

            <div className="p-6 max-w-7xl mx-auto min-h-screen">
                
                {/* 1. Filter Section */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
                    <h2 className="text-xl font-bold text-gray-800 mb-4">Select Criteria</h2>
                    <form onSubmit={loadStudents} className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label className="block text-sm font-bold text-gray-600 mb-1">Exam Term</label>
                            <select value={data.exam_id} onChange={e => handleFilterChange('exam_id', e.target.value)} className="w-full border rounded-lg p-2 bg-gray-50">
                                <option value="">Select Exam</option>
                                {exams.map(e => <option key={e.id} value={e.id}>{e.exam_title}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-gray-600 mb-1">Class</label>
                            <select value={data.class_id} onChange={e => handleFilterChange('class_id', e.target.value)} className="w-full border rounded-lg p-2 bg-gray-50">
                                <option value="">Select Class</option>
                                {classes.map(c => <option key={c.id} value={c.id}>{c.class_name}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-gray-600 mb-1">Subject</label>
                            <select value={data.subject_id} onChange={e => handleFilterChange('subject_id', e.target.value)} className="w-full border rounded-lg p-2 bg-gray-50">
                                <option value="">Select Subject</option>
                                {subjects.map(s => <option key={s.id} value={s.id}>{s.subject_name} (Max: {s.total_marks})</option>)}
                            </select>
                        </div>
                        <button type="submit" className="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 h-10 shadow-md">
                            Load Students
                        </button>
                    </form>
                </div>

                {/* 2. Marks Entry Grid */}
                {students.length > 0 && (
                    <form onSubmit={saveMarks} className="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <div className="p-4 bg-blue-50 border-b border-blue-100 flex justify-between items-center">
                            <h3 className="font-bold text-blue-900">Enter Marks for Class</h3>
                            <span className="text-sm bg-white px-3 py-1 rounded border">Total Students: {students.length}</span>
                        </div>

                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Roll No</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Student Name</th>
                                    <th className="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Obtained Marks</th>
                                    <th className="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Comment</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200">
                                {students.map((student, index) => (
                                    <tr key={student.id} className="hover:bg-blue-50/30">
                                        <td className="px-6 py-4 font-mono text-sm">{student.roll_no}</td>
                                        <td className="px-6 py-4 font-bold text-gray-800">{student.full_name}</td>
                                        <td className="px-6 py-4 text-center">
                                            <input 
                                                type="number" 
                                                min="0" 
                                                // Max marks check logic yahan add ki ja sakti hai backend subject data se
                                                className="w-24 text-center border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-bold"
                                                value={marksForm.data.marks_data[index]?.obtained || ''}
                                                onChange={(e) => handleMarkChange(index, 'obtained', e.target.value)}
                                                required
                                            />
                                        </td>
                                        <td className="px-6 py-4">
                                            <input 
                                                type="text" 
                                                placeholder="Good/Poor..." 
                                                className="w-full border-gray-300 rounded-md text-sm"
                                                value={marksForm.data.marks_data[index]?.comment || ''}
                                                onChange={(e) => handleMarkChange(index, 'comment', e.target.value)}
                                            />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        <div className="p-6 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button 
                                type="submit" 
                                disabled={marksForm.processing}
                                className="bg-green-600 text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-green-700 transition disabled:opacity-50"
                            >
                                {marksForm.processing ? 'Saving Results...' : '💾 Save All Marks'}
                            </button>
                        </div>
                    </form>
                )}

                {/* Empty State */}
                {students.length === 0 && (
                    <div className="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300 text-gray-400">
                        Please select Exam, Class and Subject to load the student list.
                    </div>
                )}

            </div>
        </AppLayout>
    );
}