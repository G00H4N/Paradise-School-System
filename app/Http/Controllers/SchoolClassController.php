<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    // 1. Manage Classes Page (View)
    public function index()
    {
        // JSON ki jagah Inertia Page return karein
        // numeric_name se sort kiya taake '1st' pehle aye aur '10th' baad mein
        return inertia('Classes/Index', [
            'classes' => SchoolClass::orderBy('numeric_name', 'asc')->get()
        ]);
    }

    // 2. Save Class Logic
    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'section_name' => 'nullable|string', // e.g. "Pink", "Blue"
            'numeric_name' => 'required|integer', // Sorting ke liye (e.g. Nursery=0, One=1)
        ]);

        SchoolClass::create($request->all());

        // JSON return karne ki bajaye wapis bhejain (Inertia auto-update karega)
        return redirect()->back()->with('success', 'Class Created Successfully!');
    }

    // 3. Delete Class (Optional but Recommended)
    public function destroy($id)
    {
        SchoolClass::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Class Deleted!');
    }
}