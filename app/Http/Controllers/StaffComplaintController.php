<?php 

namespace App\Http\Controllers;

use App\Models\OwnerComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffComplaintController extends Controller
{
    public function index()
    {
        $complaints = OwnerComplaint::latest()->get();
        return view('staff_complaints.index', compact('complaints'));
    }

    public function show(OwnerComplaint $complaint)
    {
        return view('staff_complaints.show', compact('complaint'));
    }

    public function assign(Request $request, OwnerComplaint $complaint)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $complaint->update([
            'user_assigned' => $request->user_id,
            'user_assigned_by' => auth('web')->id(),
            'status' => 'in_progress'
        ]);
        return back()->with('success', 'Complaint assigned.');
    }

    public function reply(Request $request, OwnerComplaint $complaint)
    {
        $request->validate(['reply_text' => 'required|string']);
        $complaint->update([
            'reply_text' => $request->reply_text,
            'replied_by' => auth('web')->id(),
            'status' => 'resolved'
        ]);
        return back()->with('success', 'Reply sent.');
    }
}