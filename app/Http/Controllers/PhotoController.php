<?php

namespace App\Http\Controllers;

use App\Models\ContactPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The signed-in person's profile photo: upload for review, remove, and the
 * authorised file route every photo is served through.
 */
class PhotoController extends Controller
{
    public function edit(): View
    {
        $contact = Auth::user()->contact;

        return view('profile.photo', [
            'approved' => $contact?->approvedPhoto,
            'upload' => $contact?->latestPhotoUpload,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'file', 'image', 'mimes:jpeg,png,webp', 'max:4096', 'dimensions:min_width=240,min_height=240'],
        ], [], ['photo' => 'Fotoğraf']);

        $contact = Auth::user()->syncContact();

        // A new upload replaces the one waiting for review (or rejected); the
        // approved photo stays until the new one is approved.
        $contact->photos()->where('status', '!=', ContactPhoto::APPROVED)->get()->each->delete();

        $contact->photos()->create([
            'path' => $request->file('photo')->store(ContactPhoto::DIRECTORY, 'local'),
        ]);

        $this->set_log('create', 'Profil fotoğrafı onaya gönderildi.');

        return redirect()->route('my-photo')->with('success-status', 'Fotoğrafınız onaya gönderildi. Onaylanınca kartlarınızda görünecek.');
    }

    public function destroy(): RedirectResponse
    {
        Auth::user()->contact?->photos()->get()->each->delete();
        $this->set_log('delete', 'Profil fotoğrafı kaldırıldı.');

        return redirect()->route('my-photo')->with('success-status', 'Fotoğrafınız kaldırıldı.');
    }

    /**
     * Serve a photo file to its owner and to those who see contacts or review
     * photos. Photos are personal data and never public.
     */
    public function show(ContactPhoto $photo): StreamedResponse
    {
        $user = Auth::user();
        $isOwner = $user->contact_id !== null && $user->contact_id === $photo->contact_id;

        abort_unless($isOwner || $user->hasPermission('contacts.view') || $user->hasPermission('photos.review'), 403);
        abort_unless($photo->status !== ContactPhoto::REJECTED && Storage::disk('local')->exists($photo->path), 404);

        return Storage::disk('local')->response($photo->path, null, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
