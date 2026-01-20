import React from 'react';
import { Link, usePage, router } from '@inertiajs/react';

// 1. Data ki shape (Interfaces)
interface SchoolClass {
    id: number;
    class_name: string;
    section_name?: string;
}

interface Student {
    id: number;
    admission_no: string;
    full_name: string;
    father_name: string;
    phone: string;
    school_class: SchoolClass;
}

// Yahan '[key: string]: any' ka izafa kiya hai error khatam karne ke liye
interface PageProps {
    students: {
        data: Student[];
        links: any[];
    };
    classes: SchoolClass[];
    filters: {
        search?: string;
        class_id?: string;
    };
    [key: string]: any; 
}

export default function Index() {
    // 2. Type-safe props access
    const { students, classes, filters } = usePage<PageProps>().props;

    // Search Handler (As per video demo functionality)
    const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        router.get('/students', 
            { search: e.target.value, class_id: filters.class_id }, 
            { preserveState: true }
        );
    };

    // Filter Handler
    const handleClassFilter = (id: string) => {
        router.get('/students', 
            { search: filters.search, class_id: id }, 
            { preserveState: true }
        );
    };

    return (
        <div className="min-h-screen bg-gray-50 p-6 md:p-10">
            <div className="max-w-7xl mx-auto">
                
                {/* Header Section */}
                <div className="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div>
                        <h2 className="text-2xl font-black text-blue-900 uppercase tracking-tight">Student Directory</h2>
                        <p className="text-gray-500 text-sm">Paradise Public Girls Elementary School</p>
                    </div>
                    
                    <div className="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4 mt-6 md:mt-0 w-full md:w-auto">
                        <input 
                            type="text" 
                            placeholder="Search Name or ID..." 
                            className="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-64"
                            defaultValue={filters.search}
                            onChange={handleSearch}
                        />
                        <select 
                            className="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            value={filters.class_id || ''}
                            onChange={(e) => handleClassFilter(e.target.value)}
                        >
                            <option value="">All Classes</option>
                            {classes.map(c => (
                                <option key={c.id} value={c.id}>{c.class_name}</option>
                            ))}
                        </select>
                        <Link 
                            href="/students/create" 
                            className="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-md transition-all text-center"
                        >
                            + New Admission
                        </Link>
                    </div>
                </div>

                {/* Table Section (Strictly following Video Attributes) */}
                <div className="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-blue-50">
                                <tr>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Adm. No</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Student Name</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Class (Section)</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Father Name</th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Contact</th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-blue-900 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-100">
                                {students.data.length > 0 ? students.data.map((student) => (
                                    <tr key={student.id} className="hover:bg-blue-50/50 transition duration-150">
                                        <td className="px-6 py-4 whitespace-nowrap font-black text-blue-700">#{student.admission_no}</td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{student.full_name}</td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span className="bg-gray-100 px-2 py-1 rounded text-xs font-semibold">
                                                {student.school_class.class_name} ({student.school_class.section_name || 'N/A'})
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{student.father_name}</td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{student.phone || '---'}</td>
                                        <td className="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                            <button className="text-blue-600 hover:text-blue-800 font-bold">View</button>
                                            <button className="text-green-600 hover:text-green-800 font-bold">Fee Card</button>
                                            <button className="text-red-600 hover:text-red-800 font-bold">Edit</button>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={6} className="px-6 py-10 text-center text-gray-500 italic">No student records found.</td>
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