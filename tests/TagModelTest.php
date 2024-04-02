<?php

use Anil\FastApiCrud\Tests\TestClasses\Models\PostModel;
use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;

beforeEach(function () {
});

describe(description: 'testing_tag_model_data_seeding ', tests: function () {
    it(description: 'can_create_a_tag', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        expect($tag->name)->toBe(expected: 'Tag 1');
        $this->assertDatabaseHas(table: 'tags', data: ['name' => 'Tag 1']);
        expect($tag->posts)->toBeEmpty();
        expect($tag->posts->count())->toBe(0);
    });

    it(description: 'can_update_a_tag', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $tag->update(['name' => 'Tag 2']);

        expect($tag->name)->toBe(expected: 'Tag 2');
        $this->assertDatabaseHas('tags', ['name' => 'Tag 2']);
    });

    it(description: 'can_delete_a_tag', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $tag->delete();

        $this->assertDatabaseMissing(table: 'tags', data: ['name' => 'Tag 1']);
    });
});

describe(description: 'test_tag_controller', tests: function () {
    it(description: 'can_create_a_tag', closure: function () {
        $response = $this->post('tags', [
            'name' => 'tag1',
        ]);
        $response->assertStatus(status: 201);
        $this->assertDatabaseHas(table: 'tags', data: ['name' => 'tag1']);
    });

    it(description: 'can_update_a_tag', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $response = $this->put(uri: 'tags/'.$tag->id, data: [
            'name' => 'tag2',
        ]);
        $response->assertStatus(status: 200);
        $this->assertDatabaseHas('tags', ['name' => 'tag2']);
    });

    it(description: 'can_delete_a_tag', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $response = $this->delete(uri: 'tags/'.$tag->id);
        $response->assertOk();
        $response->assertJsonCount(count: 0, key: 'data');
        $this->assertDatabaseMissing('tags', ['name' => 'Tag 1']);
    });

    it(description: 'can_get_all_tags', closure: function () {
        $tags = TagModel::factory()->count(count: 5)->create();
        $response = $this->get(uri: 'tags');
        $response->assertStatus(status: 200);
        $response->assertJsonCount(5, 'data');
    });

    it(description: 'can_get_a_tag', closure: function () {
        $tag = TagModel::factory()->create();
        $response = $this->get(uri: 'tags/'.$tag->id);
        $response->assertStatus(status: 200);
        $response->assertJson(['data' => ['name' => $tag->name]]);
    });

    it(description: 'can_post_a_tag_with_posts_ids', closure: function () {
        $post = PostModel::factory()->create([
            'name' => 'Post 1',
            'desc' => 'Post 1 Description',
        ]);
        $response = $this->post(uri: 'tags', data: [
            'name'     => 'tag1',
            'post_ids' => [$post->id],
        ]);
        $response->assertStatus(status: 201);
        $this->assertDatabaseHas(table: 'tags', data: ['name' => 'tag1']);
        $this->assertDatabaseHas(table: 'post_tag', data: ['tag_id' => 1, 'post_id' => 1]);
        $this->assertDatabaseHas(table: 'posts', data: ['name' => 'Post 1', 'desc' => 'Post 1 Description']);
    });
});
