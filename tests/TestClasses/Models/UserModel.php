<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserModel extends Model
{

    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(related: PostModel::class,foreignKey:  'user_id',localKey:  'id');
    }
}
