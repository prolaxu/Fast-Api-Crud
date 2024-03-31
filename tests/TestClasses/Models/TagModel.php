<?php

namespace Anil\FastApiCrud\Tests\TestClasses\TestModel;

use Illuminate\Database\Eloquent\Model;

class TagModel extends Model
{

    protected $table = 'tags';

    protected $fillable = [
        'name',
    ];

}
