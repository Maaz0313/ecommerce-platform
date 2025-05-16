<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function index()
    {
        return view('contact');
    }
    
    /**
     * Process the contact form submission.
     */
    public function send(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // In a real application, you would send an email here
        // For example:
        // Mail::to('info@example.com')->send(new ContactFormMail($validated));
        
        // For now, we'll just redirect with a success message
        return redirect()->route('contact')->with('message_sent', true);
    }
}
