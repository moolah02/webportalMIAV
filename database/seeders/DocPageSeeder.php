<?php

namespace Database\Seeders;

use App\Models\DocPage;
use Illuminate\Database\Seeder;

class DocPageSeeder extends Seeder
{
    /**
     * Editable pages with their metadata.
     */
    protected array $pages = [
        'system' => [
            'title'    => 'System Manual',
            'subtitle' => 'Complete guide to the MIAV Dashboard — for all staff, managers, and admins.',
        ],
        'mobile' => [
            'title'    => 'Mobile App Guide',
            'subtitle' => 'For technicians and field staff — how to use the MIAV mobile interface to manage jobs, tickets, and visits from anywhere.',
        ],
        'reports' => [
            'title'    => 'Reports Manual',
            'subtitle' => 'Step-by-step guide to generating, filtering, and exporting every report available in the MIAV Dashboard.',
        ],
        'projects' => [
            'title'    => 'Project Flow Guide',
            'subtitle' => 'End-to-end walkthrough of the project lifecycle in MIAV — from creation through to closure and reporting.',
        ],
        'srs' => [
            'title'    => 'Software Requirements Specification',
            'subtitle' => 'MIAV Dashboard — Formal requirements, functional specifications, and system constraints.',
        ],
        'overview' => [
            'title'    => 'Business Overview',
            'subtitle' => 'System summary, module features, and high-level operational workflows.',
        ],
    ];

    public function run(): void
    {
        foreach ($this->pages as $slug => $meta) {
            $content = $this->extractBladeContent($slug);

            DocPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'    => $meta['title'],
                    'subtitle' => $meta['subtitle'],
                    'content'  => $content,
                ]
            );

            $this->command->info("Seeded doc page: {$slug}");
        }
    }

    /**
     * Read the existing Blade view for a doc page and extract the hardcoded fallback HTML.
     *
     * The blade views now have the pattern:
     *   @section('content')
     *   @if(!empty(...$page->content...))
     *       {!! $page->content !!}
     *   @else
     *       [hardcoded HTML we want to seed]
     *   @endif
     *   @endsection
     *
     * We extract the @else block so the seed data matches what users saw originally.
     */
    protected function extractBladeContent(string $slug): string
    {
        $path = resource_path("views/docs/{$slug}.blade.php");

        if (!file_exists($path)) {
            return "<p>Content for <strong>{$slug}</strong> has not been written yet.</p>";
        }

        $blade = file_get_contents($path);

        // Try to extract the @else block (hardcoded fallback inside the DB-override wrapper)
        if (preg_match('/@else\s*(.*?)@endif\s*@endsection/s', $blade, $elseMatches)) {
            $html = $elseMatches[1];
        } elseif (preg_match('/@section\s*\(\s*[\'"]content[\'"]\s*\)(.*?)@endsection/s', $blade, $sectionMatches)) {
            // Fallback: original format without wrapper — strip any stray @if block
            $html = $sectionMatches[1];
            $html = preg_replace('/@if\s*\(!empty.*?@else\s*/s', '', $html);
        } else {
            return "<p>Could not extract content from blade file for: {$slug}</p>";
        }

        // Convert {{ url('/path') }} → /path
        $html = preg_replace('/\{\{\s*url\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*\}\}/', '$1', $html);

        // Convert {{ asset('path') }} → /path
        $html = preg_replace('/\{\{\s*asset\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*\}\}/', '/$1', $html);

        // Remove any leftover {{ ... }} expressions
        $html = preg_replace('/\{\{[^}]+\}\}/', '', $html);

        // Remove {!! ... !!} (not expected in @else block but safety net)
        $html = preg_replace('/\{!![^!]+!!\}/', '', $html);

        return trim($html);
    }
}
