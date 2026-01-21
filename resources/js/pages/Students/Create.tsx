// @ts-ignore


import React, { useState } from 'react';
import { useForm, usePage, Head } from '@inertiajs/react';

// 1. Backend se aane wale data ki Types (Strictly Backend Models ke mutabiq)
interface SchoolClass {
    id: number;
    class_name: string;
    section_name?: string;
}

interface TransportRoute {
    id: number;
    route_title: string;
    vehicle_number?: string;
    fare_amount: number;
}

interface PageProps {
    classes: SchoolClass[];
    routes: TransportRoute[]; // Backend se aa raha hai
    [key: string]: any; 
}

export default function Create() {
    const { classes, routes } = usePage<PageProps>().props;
    const [photoPreview, setPhotoPreview] = useState<string | null>(null);

    // 2. Form State (Woh sab fields jo Controller validate kar raha hai)
    const { data, setData, post, processing, errors } = useForm({
        full_name: '',
        admission_no: '',
        roll_no: '',
        class_id: '',
        transport_route_id: '', // New Field
        gender: 'Female', 
        birthday: '',
        father_name: '',
        father_cnic: '', // Critical for Parent Login
        father_phone: '',
        mother_name: '',
        caste: '',
        blood_group: '',
        religion: 'Islam',
        address: '',
        student_photo: '', // Base64 string jayegi
    });

    // 3. Photo Handle Logic
    const handlePhotoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPhotoPreview(reader.result as string);
                setData('student_photo', reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('students.store')); // Named route use karein
    };

    return (
        <div className="min-h-screen bg-gray-50 p-6">
            <Head title="New Admission" />
            
            <div className="max-w-5xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                
                {/* Header */}
                <div className="bg-gradient-to-r from-blue-700 to-blue-900 p-8 text-white flex justify-between items-center">
                    <div>
                        <h2 className="text-3xl font-black uppercase tracking-wide">Student Admission</h2>
                        <p className="text-blue-100 text-sm mt-1">Paradise Public Girls Elementary School</p>
                    </div>
                    {/* Live Photo Preview */}
                    <div className="h-24 w-24 bg-white/20 rounded-full border-2 border-white flex items-center justify-center overflow-hidden">
                        {photoPreview ? (
                            <img src={photoPreview} className="h-full w-full object-cover" />
                        ) : (
                            <span className="text-2xl">📷</span>
                        )}
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="p-8 space-y-8">
                    
                    {/* --- Section 1: Academic Info --- */}
                    <div>
                        <h3 className="text-xl font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                            <span className="bg-blue-600 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs">1</span>
                            Academic Details
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Admission No *</label>
                                <input type="text" value={data.admission_no} onChange={e => setData('admission_no', e.target.value)} 
                                    className="w-full border-gray-300 rounded-lg focus:ring-blue-500" required />
                                {errors.admission_no && <p className="text-red-500 text-xs mt-1">{errors.admission_no}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Select Class *</label>
                                <select value={data.class_id} onChange={e => setData('class_id', e.target.value)} 
                                    className="w-full border-gray-300 rounded-lg focus:ring-blue-500" required>
                                    <option value="">-- Choose Class --</option>
                                    {classes.map(c => (
                                        <option key={c.id} value={c.id}>{c.class_name} {c.section_name && `(${c.section_name})`}</option>
                                    ))}
                                </select>
                                {errors.class_id && <p className="text-red-500 text-xs mt-1">{errors.class_id}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Roll Number</label>
                                <input type="text" value={data.roll_no} onChange={e => setData('roll_no', e.target.value)} 
                                    className="w-full border-gray-300 rounded-lg focus:ring-blue-500" />
                            </div>
                        </div>
                    </div>

                    {/* --- Section 2: Student Personal --- */}
                    <div>
                        <h3 className="text-xl font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                            <span className="bg-blue-600 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs">2</span>
                            Personal Information
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="md:col-span-1">
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Upload Photo</label>
                                <input type="file" onChange={handlePhotoChange} className="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Full Name *</label>
                                <input type="text" value={data.full_name} onChange={e => setData('full_name', e.target.value)} className="w-full border-gray-300 rounded-lg" required />
                            </div>
                            
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Date of Birth *</label>
                                <input type="date" value={data.birthday} onChange={e => setData('birthday', e.target.value)} className="w-full border-gray-300 rounded-lg" required />
                            </div>
                            
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Gender *</label>
                                <select value={data.gender} onChange={e => setData('gender', e.target.value)} className="w-full border-gray-300 rounded-lg">
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Religion</label>
                                <input type="text" value={data.religion} onChange={e => setData('religion', e.target.value)} className="w-full border-gray-300 rounded-lg" />
                            </div>
                        </div>
                    </div>

                    {/* --- Section 3: Parent & Guardian (Critical for Auth) --- */}
                    <div>
                        <h3 className="text-xl font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                            <span className="bg-blue-600 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs">3</span>
                            Parent / Guardian Details
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Father's Name *</label>
                                <input type="text" value={data.father_name} onChange={e => setData('father_name', e.target.value)} className="w-full border-gray-300 rounded-lg" required />
                            </div>
                            
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Father's CNIC *</label>
                                <input type="text" placeholder="31303-1234567-8" value={data.father_cnic} onChange={e => setData('father_cnic', e.target.value)} 
                                    className="w-full border-gray-300 rounded-lg" required />
                                <p className="text-xs text-gray-500 mt-1">Required for Parent Login & Family grouping.</p>
                                {errors.father_cnic && <p className="text-red-500 text-xs">{errors.father_cnic}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Mobile Number *</label>
                                <input type="text" value={data.father_phone} onChange={e => setData('father_phone', e.target.value)} className="w-full border-gray-300 rounded-lg" required />
                            </div>

                            <div className="md:col-span-3">
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Home Address</label>
                                <textarea value={data.address} onChange={e => setData('address', e.target.value)} className="w-full border-gray-300 rounded-lg" rows={2}></textarea>
                            </div>
                        </div>
                    </div>

                    {/* --- Section 4: Transport (Module 6) --- */}
                    <div>
                        <h3 className="text-xl font-bold text-gray-800 border-b pb-2 mb-4 flex items-center gap-2">
                            <span className="bg-blue-600 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs">4</span>
                            Transport Service (Optional)
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">Select Route</label>
                                <select value={data.transport_route_id} onChange={e => setData('transport_route_id', e.target.value)} 
                                    className="w-full border-gray-300 rounded-lg">
                                    <option value="">-- No Transport Required --</option>
                                    {routes.map(r => (
                                        <option key={r.id} value={r.id}>{r.route_title} (Rs. {r.fare_amount})</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Footer Actions */}
                    <div className="flex items-center justify-end gap-4 pt-6 border-t">
                        <button type="button" className="text-gray-500 hover:text-gray-700" onClick={() => window.history.back()}>Cancel</button>
                        <button type="submit" disabled={processing} 
                            className="bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg hover:bg-blue-800 transition disabled:opacity-50">
                            {processing ? 'Processing...' : 'Submit Admission'}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    );
}