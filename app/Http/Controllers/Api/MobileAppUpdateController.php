<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MobileAppRelease;
use Illuminate\Http\Request;

/**
 * What the tablet asks before it updates itself.
 *
 * The browser refuses to download an APK quietly — every Android browser warns
 * about the file type, whatever the server sends. So the app fetches its own
 * update instead: it asks what the newest build is, downloads it with its own
 * token, checks the checksum, and hands the file to Android's installer.
 */
class MobileAppUpdateController extends Controller
{
    private function channel(Request $request): string
    {
        $asked = (string) $request->query('channel', '');
        if (array_key_exists($asked, MobileAppRelease::CHANNELS)) {
            return $asked;
        }
        // An app that does not name a channel is identified by its package.
        return str_contains((string) $request->query('package'), 'newdesign') ? 'new-design' : 'current';
    }

    /** GET /api/mobile-app/latest — what the newest published build is. */
    public function latest(Request $request)
    {
        $channel = $this->channel($request);
        $release = MobileAppRelease::latest($channel);

        if (! $release) {
            return response()->json([
                'success' => false,
                'message' => 'No build has been published on this channel yet.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'channel'      => $channel,
                'version'      => $release['version'],
                'build'        => (int) $release['build'],
                'size'         => (int) $release['size'],
                'sha256'       => $release['sha256'],
                'released_at'  => $release['released_at'] ?? null,
                'notes'        => array_values($release['notes'] ?? []),
                'download_url' => url('/api/mobile-app/download?channel='.$channel),
            ],
        ]);
    }

    /** GET /api/mobile-app/download — the APK itself, for the app to install. */
    public function download(Request $request)
    {
        $channel = $this->channel($request);
        $release = MobileAppRelease::latest($channel);
        abort_unless($release, 404, 'This version of the app has not been published yet.');

        return response()->download(
            MobileAppRelease::path($release['file']),
            basename($release['file']),
            [
                'Content-Type'           => 'application/vnd.android.package-archive',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'no-store, must-revalidate',
            ]
        );
    }
}
