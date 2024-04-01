<?php

use Anil\FastApiCrud\Tests\TestClasses\Models\UserModel;

describe('hlle',function(){
    it('model seed using factory',function(){

        $user = UserModel::factory()->create();
      
    });
});