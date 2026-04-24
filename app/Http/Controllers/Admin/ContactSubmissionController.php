<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;

class ContactSubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::query()
            ->orderByDesc('id')
            ->paginate(30);

        return view('admin.contact.index', compact('submissions'));
    }

    public function show(ContactSubmission $contact)
    {
        if ($contact->read_at === null) {
            $contact->read_at = now();
            $contact->save();
        }

        return view('admin.contact.show', ['submission' => $contact]);
    }

    public function markUnread(ContactSubmission $contact): RedirectResponse
    {
        $contact->read_at = null;
        $contact->save();
        AdminActivityLogger::log('contact.mark_unread', ContactSubmission::class, (int) $contact->id);

        return back()->with('status', 'Marked as unread.');
    }
}
