<?php

use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;
use Anil\FastApiCrud\Tests\TestClasses\Models\UserModel;

beforeEach(function () {
});

describe('hlle',function(){
    it('model seed using factory',function(){
        $data=TagModel::query()->create([
            'name'=>'tag1'
        ]);
        dd($data);
      
    });
});