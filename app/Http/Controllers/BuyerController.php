<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index()
    {
        $buyers = Buyer::all(); // Retrieve all buyers
        return view('buyers.index', compact('buyers'));
    }

    public function create()
    {
        return view('buyers.create'); // Return the create buyer form view
    }

    public function store(Request $request)
    {
        $request->merge([
            'service_out' => filter_var($request->service_out, FILTER_VALIDATE_BOOLEAN),
        ]);
        
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:255',
            'credit_limit' => 'required|numeric|min:0',
            'service_out' => 'boolean',
            'address_1' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'secondary_phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        // Create the new buyer using mass assignment
        Buyer::create($request->all());

        // Redirect to the buyers list with a success message
        return redirect()->route('buyers.index')->with('success', 'Buyer created successfully.');
    }
}
