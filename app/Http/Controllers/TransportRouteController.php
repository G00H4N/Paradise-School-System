<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    public function index()
    {
        return inertia('Transport/Index', [
            'routes' => TransportRoute::withCount('students')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_title' => 'required|string',
            'vehicle_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'fare_amount' => 'required|numeric'
        ]);

        TransportRoute::create($request->all());
        return redirect()->back()->with('success', 'Route Added!');
    }

    public function destroy($id)
    {
        TransportRoute::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Route Deleted');
    }
}