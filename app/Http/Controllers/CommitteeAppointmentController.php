<?php

namespace App\Http\Controllers;

use App\Models\CommitteeAppointment;
use Illuminate\Http\Request;

class CommitteeAppointmentController extends Controller
{
    /**
     * Store a new committee appointment (PA-15/29).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'appointable_type' => 'required|string',
            'appointable_id'   => 'required|integer',
            'role'             => 'required|string|max:255',
            'valid_from'       => 'required|date',
            'valid_until'      => 'required|date|after_or_equal:valid_from',
            'notes'            => 'nullable|string|max:5000',
        ]);

        CommitteeAppointment::create($validated);

        return redirect()->back()->with('success', 'Committee member appointed successfully.');
    }

    /**
     * Update the specified committee appointment.
     */
    public function update(Request $request, CommitteeAppointment $committeeAppointment)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'role'             => 'required|string|max:255',
            'valid_from'       => 'required|date',
            'valid_until'      => 'required|date|after_or_equal:valid_from',
            'notes'            => 'nullable|string|max:5000',
        ]);

        $committeeAppointment->update($validated);

        return redirect()->back()->with('success', 'Committee appointment updated successfully.');
    }

    /**
     * Remove the specified committee appointment.
     */
    public function destroy(CommitteeAppointment $committeeAppointment)
    {
        $committeeAppointment->delete();

        return redirect()->back()->with('success', 'Committee appointment removed successfully.');
    }
}
