<?php

namespace Anil\FastApiCrud\Tests\TestClasses\Controllers;

use Anil\FastApiCrud\Controller\CrudBaseController;
use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;
use Anil\FastApiCrud\Tests\TestClasses\Requests\Tag\StoreTagRequest;
use Anil\FastApiCrud\Tests\TestClasses\Requests\Tag\UpdateTagRequest;
use Anil\FastApiCrud\Tests\TestClasses\Resources\TagResource;
use Exception;

class TagController extends CrudBaseController
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(
            model: TagModel::class,
            storeRequest: StoreTagRequest::class,
            updateRequest: UpdateTagRequest::class,
            resource: TagResource::class
        );
    }
}
