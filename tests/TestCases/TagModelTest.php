<?php

use Anil\FastApiCrud\Tests\TestClasses\Models\PostModel;
use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;

describe(description: 'Testing_Tag_Model_Factory', tests: function () {
    it(description: 'test_tag_model_fillable', closure: function () {
        $tag = new TagModel();
        $fillableKeys = array_keys($tag->getFillable());
        sort($fillableKeys);
        $expectedKeys = array_keys([
            'name',
            'desc',
            'status',
            'active',
        ]);
        sort($expectedKeys);
        expect($fillableKeys)
            ->toBeArray()
            ->and($fillableKeys)
            ->toBe($expectedKeys);
    });
    it(description: 'can_create_a_tag_using_factory', closure: function () {
        $tag = TagModel::factory()
            ->create(
                [
                    'name'   => $inputName = 'Tag 1',
                    'desc'   => $inputDesc = 'Tag 1 Description',
                    'status' => true,
                    'active' => false,
                ],
            );
        expect($tag->name)
            ->toBe(expected: $inputName)
            ->and($tag->desc)
            ->toBe(expected: $inputDesc)
            ->and($tag->status)
            ->toBe(expected: true)
            ->and($tag->active)
            ->toBe(expected: false);
        $this->assertDatabaseHas(table: 'tags', data: [
            'name'       => $inputName,
            'desc'       => $inputDesc,
            'status'     => true,
            'active'     => false,
            'deleted_at' => null,
        ]);
        expect($tag->posts)
            ->toBeEmpty()
            ->and($tag->posts()
                ->count())
            ->toBe(0);
    });
    it(description: 'can_update_a_tag_using_factory', closure: function () {
        $tag = TagModel::factory()
            ->create(
                [
                    'name'   => 'Tag 1',
                    'desc'   => 'Tag 1 Description',
                    'status' => 1,
                    'active' => 0,
                ],
            );
        $tag->update(
            [
                'name'   => $inputName = 'Tag 2',
                'desc'   => $inputDesc = 'Tag 2 Description',
                'status' => $active = 0,
                'active' => $inActive = 1,
            ],
        );
        expect($tag->name)
            ->toBe(expected: $inputName)
            ->and($tag->desc)
            ->toBe(expected: $inputDesc)
            ->and($tag->status)
            ->toBe(expected: $active)
            ->and($tag->active)
            ->toBe(expected: $inActive)
            ->and($tag->deleted_at)
            ->toBeNull();
        $this->assertDatabaseHas('tags', [
            'name'       => $inputName,
            'desc'       => $inputDesc,
            'status'     => $active,
            'active'     => $inActive,
            'deleted_at' => null,
        ]);
    });
    it(description: 'can_delete_a_tag_using_factory', closure: function () {
        $tag = TagModel::factory()
            ->create(
                [
                    'name'   => $inputName = 'Tag 1',
                    'desc'   => $inputDesc = 'Tag 1 Description',
                    'status' => $active = true,
                    'active' => $inActive = false,
                ]
            );
        $tag->forceDelete();
        $this->assertDatabaseMissing('tags', [
            'name'       => $inputName,
            'desc'       => $inputDesc,
            'status'     => $active,
            'active'     => $inActive,
            'deleted_at' => null,
        ]);
    });
});
describe(description: 'test_tag_controller', tests: function () {
    it(description: 'can_get_all_tags', closure: function () {
        TagModel::factory()
            ->createMany([
                [
                    'name'   => 'Tag 1',
                    'desc'   => 'Tag 1 Description',
                    'status' => 1,
                    'active' => 1,
                ],
                [
                    'name'   => 'Tag 2',
                    'desc'   => 'Tag 2 Description',
                    'status' => 0,
                    'active' => 0,
                ],
                [
                    'name'   => 'Tag 3',
                    'desc'   => 'Tag 3 Description',
                    'status' => 1,
                    'active' => 0,
                ],
                [
                    'name'   => 'Tag 4',
                    'desc'   => 'Tag 4 Description',
                    'status' => 0,
                    'active' => 1,
                ],
                [
                    'name'   => 'Tag 5',
                    'desc'   => 'Tag 5 Description',
                    'status' => 1,
                    'active' => 1,
                ],
            ]);
        $response = $this->get(uri: 'tags');
        $response->assertOk();
        $response->assertJsonCount(count: 5, key: 'data');
        $response->assertJsonStructure(
            [
                'data'  => [
                    [
                        'id',
                        'name',
                        'desc',
                        'status',
                        'active',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta'  => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]
        );
        $response = $this->call(method: 'get', uri: 'tags', parameters: [
            'page'        => 2,
            'rowsPerPage' => 2,
        ]);
        $response->assertOk();
        $response->assertJsonCount(count: 2, key: 'data');
    });
    it(description: 'can_create_a_tag_in_api', closure: function () {
        $tag = TagModel::factory()
            ->raw(
                [
                    'status' => 1,
                    'active' => 0,
                ]
            );
        $response = $this->postJson(uri: 'tags', data: $tag);
        $response->assertStatus(status: 201);
        $this->assertDatabaseHas(table: 'tags', data: [
            ...$tag,
            'status'     => 1,
            'active'     => 0,
            'deleted_at' => null,
        ]);
    });
    it(description: 'can_update_a_tag', closure: function () {
        $tag = TagModel::factory()
            ->create(
                [
                    'name'   => 'Tag 1',
                    'desc'   => 'Tag 1 Description',
                    'status' => 1,
                    'active' => 0,
                ]
            );
        $response = $this->putJson(uri: "tags/{$tag->id}", data: $data = [
            'name'   => 'Tag 2',
            'desc'   => 'Tag 2 Description',
            'status' => 0,
            'active' => 1,
        ]);
        $response->assertStatus(status: 200);
        $this->assertDatabaseHas('tags', [
            ...$data,
            'deleted_at' => null,
        ]);
    });
    it(description: 'can_delete_a_tag', closure: function () {
        $tag = TagModel::factory()
            ->create([
                'name' => 'Tag 1',
            ]);
        $response = $this->deleteJson(uri: "tags/{$tag->id}");
        $response->assertOk();
        $response->assertJsonCount(count: 0, key: 'data');
        $this->assertDatabaseHas('tags', [
            'name'       => 'Tag 1',
            'deleted_at' => now(),
        ]);
        $this->assertDatabaseMissing('post_tag', [
            'tag_id' => $tag->id,
        ]);
        $this->assertSame(0, TagModel::query()
            ->count());
    });
    it(description: 'can_get_a_tag', closure: function () {
        $tag = TagModel::factory()
            ->create();
        $response = $this->get(uri: 'tags/'.$tag->id);
        $response->assertStatus(status: 200);
        $response->assertJson(['data' => ['name' => $tag->name]]);
        $response->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'name',
                    'desc',
                    'status',
                    'active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ]
        );
    });
    it(description: 'can_post_a_tag_with_posts_ids', closure: function () {
        $postIds = PostModel::factory(2)
            ->create()
            ->modelKeys();
        $tag = TagModel::factory()
            ->raw([
                'name'   => 'tag1',
                'desc'   => 'tag1 description',
                'status' => 1,
                'active' => 0,
            ]);
        $response = $this->postJson(uri: 'tags', data: [
            ...$tag,
            'post_ids' => $postIds,
        ]);
        $response->assertStatus(status: 201);
        $this->assertDatabaseHas(table: 'tags', data: [
            ...$tag,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas(table: 'post_tag', data: ['tag_id' => 1, 'post_id' => 1]);
        $this->assertDatabaseHas(table: 'post_tag', data: ['tag_id' => 1, 'post_id' => 2]);
        $this->assertSame(2, TagModel::query()->find(1)->posts()->count());
    });
});
