<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocPage extends Model
{
    protected $table = 'doc_pages';

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'content',
        'last_edited_by',
    ];

    /**
     * The employee who last edited this page.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'last_edited_by');
    }

    /**
     * Find a page by slug or return null.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * All slugs that are editable via the admin interface.
     */
    public static function editableSlugs(): array
    {
        return ['system', 'mobile', 'reports', 'projects', 'srs', 'overview'];
    }
}
