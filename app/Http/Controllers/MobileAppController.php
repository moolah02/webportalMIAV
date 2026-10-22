<?php

namespace App\Http\Controllers;

use App\Services\MobileAppRelease;
use Illuminate\Http\Request;

/**
 * "Mobile App" page: the Android app versions available to install on the
 * field tablets, and the APK downloads (logged-in users only).
 */
class MobileAppController extends Controller
{
    public function index()
    {
        return view('mobile-app.index', [
            'release'   => MobileAppRelease::latest('current'),
            'newDesign' => MobileAppRelease::latest('new-design'),
            // Chrome refuses to download an app file over a plain connection,
            // so the buttons point at the secure address when one is set.
            'secureBase' => rtrim((string) \App\Models\SystemSetting::get('secure_base_url', ''), '/'),
        ]);
    }

    public function download(Request $request)
    {
        $channel = $request->query('channel') === 'new-design' ? 'new-design' : 'current';
        $release = MobileAppRelease::latest($channel);
        abort_unless($release, 404, 'This version of the app has not been published yet.');

        return response()->download(
            MobileAppRelease::path($release['file']),
            basename($release['file']),
            [
                'Content-Type' => 'application/vnd.android.package-archive',
                // Keep the browser from second-guessing the type, and from
                // serving a stale copy after a new version is published.
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'no-store, must-revalidate',
            ]
        );
    }
}
