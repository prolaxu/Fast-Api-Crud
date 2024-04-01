<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TagModel extends Model
{
    use HasFactory;
    protected $table = 'tags';

    protected $fillable = [
        'name',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            related: PostModel::class,
            table: 'post_tag',
            foreignPivotKey: 'tag_id',
            relatedPivotKey: 'post_id',
            parentKey: 'id',
            relatedKey: 'id'
        );
    }

    public function afterCreateProcess(): void
    {
        $request = request();

        if ($request->filled('post_ids')) {
            $this->posts()->sync($request->input('post_ids'));
        }
    }
}
