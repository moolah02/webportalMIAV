<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function index()
    {
        return view('docs.index');
    }

    public function system()
    {
        return view('docs.system');
    }

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

    public function overview()
    {
        return view('docs.overview');
    }

    public function deployment()
    {
        return view('docs.deployment');
    }

    public function mobile()
    {
        return view('docs.mobile');
    }

    public function reports()
    {
        return view('docs.reports');
    }

    public function projects()
    {
        return view('docs.projects');
    }

    public function srs()
    {
        return view('docs.srs');
    }
}
