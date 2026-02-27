<?php

namespace App\Http\Controllers;

use App\Models\DocPage;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    /**
     * Load a doc page from the DB, falling back to an empty object so the blade
     * can always use $page->content, $page->title, etc.
     */
    protected function loadPage(string $slug): DocPage
    {
        return DocPage::where('slug', $slug)->first()
            ?? new DocPage(['slug' => $slug, 'title' => '', 'subtitle' => '', 'content' => '']);
    }

    public function index()
    {
        return view('docs.index');
    }

    public function system()
    {
        $page = $this->loadPage('system');
        return view('docs.system', compact('page'));
    }

    public function mobile()
    {
        $page = $this->loadPage('mobile');
        return view('docs.mobile', compact('page'));
    }

    public function reports()
    {
        $page = $this->loadPage('reports');
        return view('docs.reports', compact('page'));
    }

    public function projects()
    {
        $page = $this->loadPage('projects');
        return view('docs.projects', compact('page'));
    }

    public function srs()
    {
        $page = $this->loadPage('srs');
        return view('docs.srs', compact('page'));
    }

    public function overview()
    {
        $page = $this->loadPage('overview');
        return view('docs.overview', compact('page'));
    }

    // ── Legacy routes kept for backward compatibility ──────────────

    public function testing()
    {
        return view('docs.testing');
    }

    public function tickets()
    {
        return view('docs.tickets');
    }

    public function stagedResolution()
    {
        return view('docs.staged-resolution');
    }

    public function api()
    {
        return view('docs.api');
    }

    public function deployment()
    {
        return view('docs.deployment');
    }
}
