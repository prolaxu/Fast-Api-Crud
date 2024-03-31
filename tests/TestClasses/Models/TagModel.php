<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagModel extends Model
{

    use HasFactory;
    protected $table = 'tags';

    protected $fillable = [
        'name',
    ];

}
