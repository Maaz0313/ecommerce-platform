<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);
        return view('admin.contact-messages.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Mark as read if it's not already
        if (!$message->is_read) {
            $message->is_read = true;
            $message->save();
        }
        
        return view('admin.contact-messages.show', compact('message'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->is_read = true;
        $message->save();
        
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message marked as read.');
    }

    /**
     * Mark a message as unread.
     */
    public function markAsUnread($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->is_read = false;
        $message->save();
        
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message marked as unread.');
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
