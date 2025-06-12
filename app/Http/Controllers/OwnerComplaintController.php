<?php 

namespace App\Http\Controllers;

use App\Models\OwnerComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OwnerComplaintController extends Controller
{
    /**
     * Show the form for creating a new complaint.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('owner_complaints.create');
    }

    /**
     * Store a newly created complaint in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    { 
        // Validate the request
        $request->validate([
            'complaint_text' => 'nullable',
            'complaint_voice' => 'nullable',
        ]);

        // Handle voice complaint
        $complaintVoicePath = null;
        if ($request->hasFile('complaint_voice')) {
            // Store the voice file in the "complaint_voices" directory
            $complaintVoicePath = $request->file('complaint_voice')->store('complaint_voices', 'public');
        }

        // Create the complaint
        OwnerComplaint::create([
            'owner_id' => auth('owner')->id(),
            'complaint_text' => $request->complaint_text,
            'complaint_voice' => $complaintVoicePath,
            'type' => 'text',
            'status' => 'pending',
        ]);

        // Redirect with success message
        return redirect()->route('owner.complaints.index')->with('success', 'Complaint lodged successfully.');
    }

    /**
     * Display a listing of the complaints.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $ownerId = auth('owner')->id(); 

$complaints = OwnerComplaint::with('owner')
    ->where('owner_id', $ownerId)
    ->latest()
    ->get();
        return view('owner_complaints.index', compact('complaints'));
    }
}