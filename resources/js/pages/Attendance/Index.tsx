import React, { useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

export default function AttendanceIndex() {
    const { classes, sections, flash } = usePage<any>().props;
    const [students, setStudents] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);

    // Filter Form
    const { data, setData, post, errors } = useForm({
        class_id: '',
        date: new Date().toISOString().split('T')[0],
        attendance_data: [] as { student_id: number; status: string }[]
    });

    // Load Students for selected class
    const fetchStudents = async () => {
        if (!data.class_id) return;
        setLoading(true);
        try {
            // Note: Backend par ek route hona chahiye jo students return kare class_id k basis par
            const response = await fetch(`/api/students?class_id=${data.class_id}`);
            const result = await response.json();
            setStudents(result);
            
            // Initialize attendance data
            const initialData = result.map((std: any) => ({
                student_id: std.id,
                status: 'present' // Default Present
            }));
            setData('attendance_data', initialData);
        } catch (error) {
            console.error(error);
        }
        setLoading(false);
    };

    const handleStatusChange = (index: number, status: string) => {
        const updated = [...data.attendance_data];
        updated[index].status = status;
        setData('attendance_data', updated);
    };

    const submitAttendance = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('attendance.store'), {
            onSuccess: () => alert('Attendance Saved & SMS Sent (if absent)!')
        });
    };

    return (
        <AppLayout breadcrumbs={[{ title: 'Attendance', href: '/attendance' }]}>
            <Head title="Mark Attendance" />
            <div className="p-6 max-w-7xl mx-auto">
                <div className="bg-white p-6 rounded-xl shadow-sm border mb-6">
                    <h2 className="text-xl font-bold mb-4">Daily Attendance Register</h2>
                    <div className="flex gap-4 items-end">
                        <div className="w-1/3">
                            <label className="block text-sm font-bold mb-1">Select Class</label>
                            <select className="w-full border rounded p-2" onChange={e => setData('class_id', e.target.value)}>
                                <option value="">-- Choose Class --</option>
                                {classes.map((c: any) => <option key={c.id} value={c.id}>{c.class_name}</option>)}
                            </select>
                        </div>
                        <div className="w-1/3">
                            <label className="block text-sm font-bold mb-1">Date</label>
                            <input type="date" value={data.date} onChange={e => setData('date', e.target.value)} className="w-full border rounded p-2" />
                        </div>
                        <button onClick={fetchStudents} className="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                            {loading ? 'Loading...' : 'Load Students'}
                        </button>
                    </div>
                </div>

                {students.length > 0 && (
                    <form onSubmit={submitAttendance} className="bg-white rounded-xl shadow border overflow-hidden">
                        <table className="w-full text-left">
                            <thead className="bg-gray-100 border-b">
                                <tr>
                                    <th className="p-4">Roll No</th>
                                    <th className="p-4">Name</th>
                                    <th className="p-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {students.map((std, index) => (
                                    <tr key={std.id} className="border-b hover:bg-gray-50">
                                        <td className="p-4 font-mono">{std.roll_no}</td>
                                        <td className="p-4 font-bold">{std.full_name}</td>
                                        <td className="p-4 text-center">
                                            <div className="flex justify-center gap-2">
                                                {['present', 'absent', 'late', 'leave'].map((status) => (
                                                    <label key={status} className={`cursor-pointer px-4 py-1 rounded border capitalize ${
                                                        data.attendance_data[index]?.status === status 
                                                        ? (status === 'absent' ? 'bg-red-600 text-white' : 'bg-green-600 text-white') 
                                                        : 'bg-white text-gray-600'
                                                    }`}>
                                                        <input type="radio" name={`status_${std.id}`} value={status} 
                                                            checked={data.attendance_data[index]?.status === status}
                                                            onChange={() => handleStatusChange(index, status)}
                                                            className="hidden" 
                                                        />
                                                        {status}
                                                    </label>
                                                ))}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        <div className="p-4 bg-gray-50 border-t text-right">
                            <button type="submit" className="bg-green-600 text-white px-8 py-3 rounded-lg font-bold shadow hover:bg-green-700">
                                Save Attendance
                            </button>
                        </div>
                    </form>
                )}
            </div>
        </AppLayout>
    );
}