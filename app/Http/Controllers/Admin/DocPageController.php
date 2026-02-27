<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocPage;
use Illuminate\Http\Request;

class DocPageController extends Controller
{
    /**
     * List all editable documentation pages.
     */
    public function index()
    {
        $pages = DocPage::orderBy('slug')->get();

        return view('admin.docs.index', compact('pages'));
    }

    /**
     * Show the editor for a specific page.
     */
    public function edit(string $slug)
    {
        if (!in_array($slug, DocPage::editableSlugs())) {
            abort(404, 'Unknown documentation page.');
        }

        $page = DocPage::firstOrCreate(
            ['slug' => $slug],
            [
                'title'   => ucwords(str_replace('-', ' ', $slug)),
                'content' => '',
            ]
        );

        return view('admin.docs.edit', compact('page'));
    }

    /**
     * Save updated content for the page.
     */
    public function update(Request $request, string $slug)
    {
        if (!in_array($slug, DocPage::editableSlugs())) {
            abort(404, 'Unknown documentation page.');
        }

        $request->validate([
            'title'    => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'content'  => 'required|string',
        ]);

        DocPage::updateOrCreate(
            ['slug' => $slug],
            [
                'title'          => $request->title,
                'subtitle'       => $request->subtitle,
                'content'        => $request->content,
                'last_edited_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('admin.docs.index')
            ->with('success', 'Documentation page "' . $request->title . '" saved successfully.');
    }
}
