<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyMaterial::with(['schoolClass', 'subject']);
        if ($request->class_id)
            $query->where('class_id', $request->class_id);

        return inertia('Academic/StudyMaterial', [
            'materials' => $query->latest()->get(),
            'classes' => SchoolClass::all(),
            'subjects' => Subject::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'class_id' => 'required',
            'file' => 'nullable|file|max:10240', // 10MB Max
            'youtube_url' => 'nullable|url'
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('study_materials', 'public');
        }

        StudyMaterial::create([
            'title' => $request->title,
            'description' => $request->description,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'file_path' => $path,
            'youtube_url' => $request->youtube_url,
            'uploaded_by' => auth()->id()
        ]);

        return back()->with('success', 'Material Uploaded!');
    }
}