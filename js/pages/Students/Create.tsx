import React from 'react';
import { useForm, usePage } from '@inertiajs/react';

// 1. Backend se aane wale data ki 'Types' define karna
interface SchoolClass {
    id: number;
    class_name: string;
    section_name?: string;
}

interface PageProps {
    classes: SchoolClass[];
    [key: string]: any; 
}

export default function Create() {
    // 2. Props ko Type-safe banana
    const { props } = usePage<PageProps>();
    const classes = props.classes || [];

    // 3. Form fields jo hum ne Migration mein banaye thay
    const { data, setData, post, processing, errors } = useForm({
        admission_no: '',
        roll_no: '',
        class_id: '',
        admission_date: new Date().toISOString().split('T')[0],
        full_name: '',
        email: '', 
        password: 'password123',
        gender: 'Female', 
        birthday: '',
        religion: 'Islam',
        caste: '',
        blood_group: '',
        father_name: '',
        mother_name: '',
        phone: '',
        address: '',
    });

    // 4. Form submission handler with Type
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/students/store');
    };

    return (
        <div className="min-h-screen bg-gray-100 p-4 md:p-8">
            <div className="max-w-5xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                
                {/* School Header */}
                <div className="bg-blue-600 p-6">
                    <h2 className="text-white text-2xl font-bold text-center uppercase tracking-wider">
                        Paradise Public Girls Elementary School
                    </h2>
                    <p className="text-blue-100 text-center text-sm mt-1 font-medium">Student Admission Portal</p>
                </div>

                <form onSubmit={handleSubmit} className="p-8 space-y-8">
                    
                    {/* Section 1: Academic Info */}
                    <div className="border-b border-gray-200 pb-6">
                        <h3 className="text-lg font-bold text-blue-800 mb-4 flex items-center">
                            <span className="bg-blue-100 text-blue-800 p-1 px-3 rounded-full mr-2">1</span>
                            Academic Information
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Admission No *</label>
                                <input type="text" value={data.admission_no} onChange={e => setData('admission_no', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required />
                                {errors.admission_no && <span className="text-red-500 text-xs mt-1">{errors.admission_no}</span>}
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Class *</label>
                                <select value={data.class_id} onChange={e => setData('class_id', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Select Class</option>
                                    {classes.map(c => <option key={c.id} value={c.id}>{c.class_name} ({c.section_name || 'No Section'})</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Admission Date</label>
                                <input type="date" value={data.admission_date} onChange={e => setData('admission_date', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" />
                            </div>
                        </div>
                    </div>

                    {/* Section 2: Personal Details */}
                    <div className="border-b border-gray-200 pb-6">
                        <h3 className="text-lg font-bold text-blue-800 mb-4 flex items-center">
                            <span className="bg-blue-100 text-blue-800 p-1 px-3 rounded-full mr-2">2</span>
                            Student Details
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700">Full Name *</label>
                                <input type="text" value={data.full_name} onChange={e => setData('full_name', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" required />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Gender</label>
                                <select value={data.gender} onChange={e => setData('gender', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm">
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Caste (Zaat)</label>
                                <input type="text" value={data.caste} onChange={e => setData('caste', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" placeholder="e.g. Awan" />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Religion</label>
                                <input type="text" value={data.religion} onChange={e => setData('religion', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Blood Group</label>
                                <select value={data.blood_group} onChange={e => setData('blood_group', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm">
                                    <option value="">Unknown</option>
                                    <option value="A+">A+</option><option value="B+">B+</option><option value="O+">O+</option><option value="AB+">AB+</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Section 3: Parent & Contact Info */}
                    <div className="pb-6">
                        <h3 className="text-lg font-bold text-blue-800 mb-4 flex items-center">
                            <span className="bg-blue-100 text-blue-800 p-1 px-3 rounded-full mr-2">3</span>
                            Parent & Contact Information
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Father Name *</label>
                                <input type="text" value={data.father_name} onChange={e => setData('father_name', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" required />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Mother Name</label>
                                <input type="text" value={data.mother_name} onChange={e => setData('mother_name', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">WhatsApp / Phone</label>
                                <input type="text" value={data.phone} onChange={e => setData('phone', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" placeholder="03001234567" />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Home Address</label>
                                <textarea value={data.address} onChange={e => setData('address', e.target.value)} className="mt-1 w-full border-gray-300 rounded-lg shadow-sm" rows={1}></textarea>
                            </div>
                        </div>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex justify-end items-center border-t pt-8 space-x-4">
                        <button type="button" className="text-gray-600 font-medium hover:text-gray-800">Clear Form</button>
                        <button 
                            type="submit" 
                            disabled={processing} 
                            className="bg-green-600 text-white px-12 py-3 rounded-lg font-bold text-lg shadow-md hover:bg-green-700 transition-all active:scale-95 disabled:opacity-50"
                        >
                            {processing ? 'Registering...' : 'Register Student'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}