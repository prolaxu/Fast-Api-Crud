<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tags';
    protected $fillable = [
        'name',
        'desc',
        'status',
        'active',
    ];

    public function afterCreateProcess(): void
    {
        $request = request();
        if ($request->filled('post_ids')) {
            $this->posts()->sync($request->input('post_ids'));
        }
    }

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

    public function afterUpdateProcess(): void
    {
        $request = request();
        if ($request->filled('post_ids')) {
            $this->posts()->sync($request->input('post_ids'));
        }
    }

    public function scopeQueryFilter(Builder $query, $search): Builder
    {
        return $query->likeWhere(
            attributes: ['name', 'desc'],
            searchTerm: $search
        );
    }

    public function scopeActive(Builder $query, bool $active = true): Builder
    {
        return $query->where(column: 'active', value: $active);
    }

    public function scopeStatus(Builder $query, bool $status = true): Builder
    {
        return $query->where(column: 'status', value: $status);
    }
}
