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
            ['Content-Type' => 'application/vnd.android.package-archive']
        );
    }
}
