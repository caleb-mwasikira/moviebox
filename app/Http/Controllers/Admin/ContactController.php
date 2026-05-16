<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
        $unreadCount = $contacts->filter->isUnread()->count();

        return view('pages.admin.contacts.index', compact('contacts', 'unreadCount'));
    }

    public function show(Contact $contact)
    {
        $contact->markRead();

        return response()->json([
            'id'         => $contact->id,
            'name'       => $contact->name,
            'email'      => $contact->email,
            'message'    => $contact->message,
            'created_at' => $contact->created_at->format('M j, Y \a\t g:i A'),
            'read_at'    => $contact->read_at?->toIso8601String(),
        ]);
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(['ok' => true]);
    }
}
