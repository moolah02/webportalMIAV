<?php

namespace App\Support;

/**
 * Photo links are stored as absolute URLs, built from whichever address the
 * tablet used when it uploaded (normally http on port 80). The portal is also
 * reachable over https on port 8080, and a browser refuses to load an http
 * image on an https page, so a stored link is put back onto the address the
 * page itself is being served from.
 *
 * Links to anywhere else (old Firebase photos) are left exactly as they are.
 */
class MediaUrl
{
    public static function display(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return $url;
        }

        $url = trim($url);

        // Already relative: url() gives it the current scheme, host and port.
        if (str_starts_with($url, '/')) {
            return url(ltrim($url, '/'));
        }

        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH);

        if ($host === null || $path === null || $path === '') {
            return $url;
        }

        // Only rewrite files that live on this server.
        if (! request() || strcasecmp($host, request()->getHost()) !== 0) {
            return $url;
        }

        $query = parse_url($url, PHP_URL_QUERY);

        return url(ltrim($path, '/')).($query ? '?'.$query : '');
    }
}
