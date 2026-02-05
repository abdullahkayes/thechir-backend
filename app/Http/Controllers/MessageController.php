<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index()
    {
        $messages = Message::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        Message::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully! We will get back to you soon.',
        ]);
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        $message->markAsRead();
        return view('admin.messages.show', compact('message'));
    }

    /**
     * Show the form for editing the specified message.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified message in storage.
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Get unread messages count for notifications.
     */
    public function unreadCount()
    {
        $count = Message::whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Get unread messages for notifications dropdown.
     */
    public function unread()
    {
        $messages = Message::whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($messages);
    }

    /**
     * Mark all messages as read.
     */
    public function markAllAsRead()
    {
        Message::whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
