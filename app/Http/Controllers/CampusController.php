<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index()
    {
        return inertia('Settings/Campuses', [
            'campuses' => Campus::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'campus_name' => 'required|string',
            'contact_number' => 'required|string'
        ]);

        Campus::create($request->all());
        return back()->with('success', 'Campus Added Successfully');
    }
}