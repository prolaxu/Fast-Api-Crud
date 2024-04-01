<?php

use Anil\FastApiCrud\Tests\TestClasses\Models\TagModel;

beforeEach(function () {
});

describe('testing_tag_model_data_seeding ', function () {
    it('can_create_a_tag', function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        expect($tag->name)->toBe('Tag 1');
        $this->assertDatabaseHas('tags', ['name' => 'Tag 1']);
    });

    it('can_update_a_tag', function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $tag->update(['name' => 'Tag 2']);

        expect($tag->name)->toBe('Tag 2');
        $this->assertDatabaseHas('tags', ['name' => 'Tag 2']);
    });

    it('can_delete_a_tag', function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $tag->delete();

        $this->assertDatabaseMissing('tags', ['name' => 'Tag 1']);
    });
});

describe('test_tag_controller', function () {
    it('can_create_a_tag', function () {
        $response = $this->post('tags', [
            'name' => 'tag1',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', ['name' => 'tag1']);
    });

    it('can_update_a_tag', function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $response = $this->put('tags/'.$tag->id, [
            'name' => 'tag2',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('tags', ['name' => 'tag2']);
    });

    it('can_delete_a_tag', function () {
        $tag = TagModel::factory()->create(
            [
                'name' => 'Tag 1',
            ]
        );

        $response = $this->delete('tags/'.$tag->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tags', ['name' => 'Tag 1']);
    });

    it('can_get_all_tags', function () {
        $tags = TagModel::factory()->count(5)->create();
        $response = $this->get('tags');
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    });

    it('can_get_a_tag', function () {
        $tag = TagModel::factory()->create();
        $response = $this->get('tags/'.$tag->id);
        $response->assertStatus(200);
        $response->assertJson(['data' => ['name' => $tag->name]]);
    });

});
