<?php

namespace App\Http\Controllers;

use App\Support\Organization;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public files of the organization: logo, favicon and images placed on the
 * home page. They are not personal data, so they are served to everyone.
 */
class OrganizationFileController extends Controller
{
    public function logo(Organization $organization): Response
    {
        return $this->serve($organization->get('logo_path'));
    }

    public function favicon(Organization $organization): Response
    {
        return $this->serve($organization->get('favicon_path'));
    }

    public function image(string $name): Response
    {
        return $this->serve(Organization::DIRECTORY.'/images/'.$name);
    }

    private function serve(?string $path): Response
    {
        abort_unless(Organization::storedFile($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'public, max-age=604800',
            'X-Content-Type-Options' => 'nosniff',
            // Uploaded SVG must not run scripts when opened directly.
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
        ]);
    }
}
