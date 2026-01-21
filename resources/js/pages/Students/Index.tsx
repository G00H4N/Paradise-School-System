// @ts-ignore

import React from 'react';
import { Link, usePage, router, Head } from '@inertiajs/react';

// Interfaces match Backend JSON Structure
interface SchoolClass {
    id: number;
    class_name: string;
    section_name?: string;
}

interface TransportRoute {
    id: number;
    route_title: string;
}

interface Student {
    id: number;
    admission_no: string;
    full_name: string;
    father_name: string;
    father_phone: string; // Backend mein hum 'father_phone' use kar rahe hain (Check Create.tsx)
    phone?: string;       // Fallback
    school_class: SchoolClass;
    transport?: TransportRoute;
    is_active: number;
}

interface PageProps {
    students: {
        data: Student[];
        links: any[]; // Pagination links
        total: number;
    };
    classes: SchoolClass[];
    filters: {
        search?: string;
        class_id?: string;
    };
    [key: string]: any; 
}

export default function Index() {
    const { students, classes, filters } = usePage<PageProps>().props;

    // Search Logic (Debounce recommended but direct for MVP)
    const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        router.get(route('students.index'), 
            { search: e.target.value, class_id: filters.class_id }, 
            { preserveState: true, replace: true }
        );
    };

    const handleClassFilter = (classId: string) => {
        router.get(route('students.index'), 
            { search: filters.search, class_id: classId }, 
            { preserveState: true }
        );
    };

    return (
        <div className="min-h-screen bg-gray-50 p-6 md:p-10">
            <Head title="Students Directory" />
            
            <div className="max-w-7xl mx-auto">
                {/* --- Header & Actions --- */}
                <div className="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div>
                        <h2 className="text-2xl font-black text-gray-800">STUDENTS LIST</h2>
                        <p className="text-sm text-gray-500">Total Records: {students.total}</p>
                    </div>

                    <div className="flex gap-3 mt-4 md:mt-0 w-full md:w-auto">
                        <input 
                            type="text" 
                            placeholder="Search by Name/ID..." 
                            className="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500"
                            defaultValue={filters.search}
                            onChange={handleSearch}
                        />
                        <select 
                            className="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500"
                            value={filters.class_id || ''}
                            onChange={(e) => handleClassFilter(e.target.value)}
                        >
                            <option value="">All Classes</option>
                            {classes.map(c => (
                                <option key={c.id} value={c.id}>{c.class_name}</option>
                            ))}
                        </select>
                        <Link href={route('students.create')} className="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold shadow hover:bg-blue-700">
                            + New
                        </Link>
                    </div>
                </div>

                {/* --- Data Table --- */}
                <div className="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Info</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Father Details</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Class</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Transport</th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {students.data.length > 0 ? students.data.map((std) => (
                                    <tr key={std.id} className="hover:bg-blue-50 transition">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-gray-900">{std.full_name}</div>
                                            <div className="text-xs text-blue-600 font-mono">#{std.admission_no}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="text-sm text-gray-900">{std.father_name}</div>
                                            <div className="text-xs text-gray-500">{std.phone || std.father_phone || 'N/A'}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                                {std.school_class.class_name}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-600">
                                            {std.transport ? (
                                                <span className="flex items-center gap-1 text-orange-600 font-medium">
                                                    🚌 {std.transport.route_title}
                                                </span>
                                            ) : (
                                                <span className="text-gray-400 text-xs">Self</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-center space-x-2">
                                            <Link href={route('fee.card', std.id)} className="text-green-600 hover:underline text-sm font-bold">
                                                Fee Card
                                            </Link>
                                            <span className="text-gray-300">|</span>
                                            <a href={route('students.id_cards', { class_id: std.school_class.id })} target="_blank" className="text-blue-600 hover:underline text-sm font-bold">
                                                ID Card
                                            </a>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={5} className="px-6 py-12 text-center text-gray-400 italic">
                                            No students found matching your criteria.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
}