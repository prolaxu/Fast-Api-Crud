<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Controllers;

use Anil\FastApiCrud\Controller\CrudBaseController;
use Anil\FastApiCrud\Tests\TestClasses\Models\PostModel;
use Anil\FastApiCrud\Tests\TestClasses\Requests\Post\StorePostRequest;
use Anil\FastApiCrud\Tests\TestClasses\Requests\Post\UpdatePostRequest;
use Anil\FastApiCrud\Tests\TestClasses\Resources\PostResource;
use Exception;

class TagController extends CrudBaseController
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(
            model: PostModel::class,
            storeRequest: StorePostRequest::class,
            updateRequest: UpdatePostRequest::class,
            resource: PostResource::class
        );
    }
}
