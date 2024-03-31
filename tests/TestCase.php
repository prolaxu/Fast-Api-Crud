<?php

namespace Anil\FastApiCrud\Tests;

use Anil\FastApiCrud\Providers\ApiCrudServiceProvider;
use Anil\FastApiCrud\Tests\TestClasses\Controllers\PostController;
use Anil\FastApiCrud\Tests\TestClasses\Controllers\TagController;
use Anil\FastApiCrud\Tests\TestClasses\Models\PostModel;
use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;
use Anil\FastApiCrud\Tests\TestClasses\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Anil\FastApiCrud\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function setUpDatabase(Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->string(column: 'email');
            $table->string(column: 'password');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->string(column: 'description');
            $table->foreignIdFor(UserModel::class, 'user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('tags', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->timestamps();
        });

        $app['db']->connection()->getSchemaBuilder()->create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PostModel::class, 'post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignIdFor(TagModel::class, 'tag_id')->constrained('tags')->cascadeOnDelete();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            ApiCrudServiceProvider::class,
        ];
    }

    /**
     * Define routes setup.
     * @param Router $router
     * @return void
     */
    protected function defineRoutes(Router $router): void
    {
        $router->apiResource('posts', PostController::class);
        $router->apiResource('tags', TagController::class);
    }
}
