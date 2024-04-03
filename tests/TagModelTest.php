<?php

use Anil\FastApiCrud\Tests\TestClasses\Models\PostModel;
use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;

beforeEach(function () {
});

describe(description: 'testing_tag_model_data_seeding ', tests: function () {
    it(description: 'can_create_a_tag_using_factory', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => $inputName = 'Tag 1',
                'desc' => $inputDesc = 'Tag 1 Description',
                'status' => $active = true,
                'active' => $inActive = false,
            ]
        );

        expect($tag->name)
            ->toBe(expected: $inputName)
            ->and($tag->desc)
            ->toBe(expected: $inputDesc)
            ->and($tag->status)
            ->toBe(expected: $active)
            ->and($tag->active)
            ->toBe(expected: $inActive);

        $this->assertDatabaseHas(table: 'tags', data: [
            'name' => $inputName,
            'desc' => $inputDesc,
            'status' => $active,
            'active' => $inActive,
        ]);
        expect($tag->posts)->toBeEmpty();
        expect($tag->posts->count())->toBe(0);
    });

    it(description: 'can_update_a_tag_using_factory', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => $inputName = 'Tag 1',
                'desc' => $inputDesc = 'Tag 1 Description',
                'status' => $active = 1,
                'active' => $inActive = 0,
            ]
        );

        $tag->update(
            [
                'name' => $inputName = 'Tag 2',
                'desc' => $inputDesc = 'Tag 2 Description',
                'status' => $active = 0,
                'active' => $inActive = 1,
            ]
        );

        expect($tag->name)
            ->toBe(expected: $inputName)
            ->and($tag->desc)
            ->toBe(expected: $inputDesc)
            ->and($tag->status)
            ->toBe(expected: $active)
            ->and($tag->active)
            ->toBe(expected: $inActive);

        $this->assertDatabaseHas('tags', [
            'name' => $inputName,
            'desc' => $inputDesc,
            'status' => $active,
            'active' => $inActive,
        ]);
    });

    it(description: 'can_delete_a_tag_using_factory', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => $inputName = 'Tag 1',
                'desc' => $inputDesc = 'Tag 1 Description',
                'status' => $active = true,
                'active' => $inActive = false,
            ]
        );

        $tag->delete();

        $this->assertDatabaseMissing('tags', [
            'name' => $inputName,
            'desc' => $inputDesc,
            'status' => $active,
            'active' => $inActive,
        ]);
    });
});

describe(description: 'test_tag_controller', tests: function () {
    it(description: 'can_create_a_tag_in_api', closure: function () {

        $tag = TagModel::factory()->raw(
            [
                'status' => 1,
                'active' => 0,
            ]
        );
        $response = $this->postJson(uri: 'tags', data: $tag);
        $response->assertStatus(status: 201);
        $this->assertDatabaseHas(table: 'tags', data: [
            ...$tag,
            'status' => 1,
            'active' => 0,
        ]);
    });

    it(description: 'can_update_a_tag', closure: function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
                'desc' => 'Tag 1 Description',
                'status' => 1,
                'active' => 0,
            ]
        );

        $response = $this->putJson(uri: "tags/{$tag->id}", data: $data = [
            'name' => 'Tag 2',
            'desc' => 'Tag 2 Description',
            'status' => 0,
            'active' => 1,
        ]);
        $response->assertStatus(status: 200);
        $this->assertDatabaseHas('tags', $data);
    });

    it(description: 'can_delete_a_tag', closure: function () {
        $tag = TagModel::factory()->create([
            'name' => 'Tag 1',
        ]);

        $response = $this->deleteJson(uri: "tags/{$tag->id}");
        $response->assertOk();
        $response->assertJsonCount(count: 0, key: 'data');
        $this->assertDatabaseMissing('tags', ['name' => 'Tag 1']);
        $this->assertDatabaseMissing('post_tag', ['tag_id' => $tag->id]);
        $this->assertSame(0, TagModel::query()->count());

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
        $postIds = PostModel::factory(2)->create()->modelKeys();
        $tag = TagModel::factory()->raw([
            'name' => 'tag1',
            'desc' => 'tag1 description',
            'status' => 1,
            'active' => 0,
        ]);

        $response = $this->postJson(uri: 'tags', data: [
            ...$tag,
            'post_ids' => $postIds,
        ]);

        $response->assertStatus(status: 201);
        $this->assertDatabaseHas(table: 'tags', data: $tag);
        $this->assertDatabaseHas(table: 'post_tag', data: ['tag_id' => 1, 'post_id' => 1]);
        $this->assertDatabaseHas(table: 'post_tag', data: ['tag_id' => 1, 'post_id' => 2]);
        $this->assertSame(2, TagModel::find(1)->posts->count());
    });
});
