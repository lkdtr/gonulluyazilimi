<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PhotoReviewController extends Controller
{
    public function index(): View
    {
        return view('admin::photos.index', [
            'photos' => ContactPhoto::pending()->with('contact.approvedPhoto')->oldest()->paginate(24),
        ]);
    }

    public function approve(ContactPhoto $photo): RedirectResponse
    {
        abort_unless($photo->isPending(), 404);

        $photo->approve(Auth::user());
        $this->set_log('change', "Profil fotoğrafı onaylandı: {$photo->contact->display_name}");

        return back()->with('success-status', "{$photo->contact->display_name} fotoğrafı onaylandı.");
    }

    public function reject(Request $request, ContactPhoto $photo): RedirectResponse
    {
        abort_unless($photo->isPending(), 404);

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']], [], ['reason' => 'Gerekçe']);

        $photo->reject(Auth::user(), $data['reason'] ?? null);
        $this->set_log('change', "Profil fotoğrafı reddedildi: {$photo->contact->display_name}");

        return back()->with('success-status', "{$photo->contact->display_name} fotoğrafı reddedildi.");
    }
}
