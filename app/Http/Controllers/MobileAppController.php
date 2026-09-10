<?php

namespace App\Http\Controllers;

use App\Services\MobileAppRelease;

/**
 * "Mobile App" page: shows the latest Android app version and lets logged-in
 * users download the APK (e.g. to update the field tablets).
 */
class MobileAppController extends Controller
{
    public function index()
    {
        return view('mobile-app.index', ['release' => MobileAppRelease::latest()]);
    }

    public function download()
    {
        $release = MobileAppRelease::latest();
        abort_unless($release, 404, 'No app version has been published yet.');

        return response()->download(
            MobileAppRelease::path($release['file']),
            basename($release['file']),
            ['Content-Type' => 'application/vnd.android.package-archive']
        );
    }
}
