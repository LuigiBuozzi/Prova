<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('index', function () {
    it('returns a paginated list of items, newest first', function () {
        $this->travelTo('2026-01-01 10:00:00');
        Item::factory()->create(['name' => 'Older']);
        $this->travelTo('2026-01-02 10:00:00');
        Item::factory()->create(['name' => 'Newer']);

        $response = $this->getJson(route('api.v1.items.index'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'color', 'quantity', 'is_low_stock', 'created_at', 'updated_at'],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.0.name', 'Newer')
            ->assertJsonPath('data.1.name', 'Older')
            ->assertJsonPath('meta.total', 2);
    });
});

describe('show', function () {
    it('returns the item in the documented contract', function () {
        $this->travelTo('2026-01-01 10:00:00');
        $item = Item::factory()->create([
            'name' => 'Eraser',
            'color' => 'Pink',
            'quantity' => 2,
        ]);

        $response = $this->getJson(route('api.v1.items.show', $item));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $item->id,
                'name' => 'Eraser',
                'color' => 'Pink',
                'quantity' => 2,
                'is_low_stock' => true,
                'created_at' => '2026-01-01T10:00:00+00:00',
                'updated_at' => '2026-01-01T10:00:00+00:00',
            ],
        ]);
    });

    it('returns 404 when the item does not exist', function () {
        $response = $this->getJson(route('api.v1.items.show', 999));

        $response->assertNotFound()->assertJsonStructure(['message']);
    });
});

describe('store', function () {
    it('creates an item and returns 201', function () {
        $response = $this->postJson(route('api.v1.items.store'), [
            'name' => 'Pencil',
            'color' => 'Yellow',
            'quantity' => 12,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Pencil')
            ->assertJsonPath('data.color', 'Yellow')
            ->assertJsonPath('data.quantity', 12)
            ->assertJsonPath('data.is_low_stock', false);

        $this->assertDatabaseHas('items', [
            'name' => 'Pencil',
            'color' => 'Yellow',
            'quantity' => 12,
        ]);
    });

    it('returns 422 with every required field when the payload is empty', function () {
        $response = $this->postJson(route('api.v1.items.store'), []);

        $response->assertUnprocessable()->assertJsonValidationErrors([
            'name' => 'The name field is required.',
            'color' => 'The color field is required.',
            'quantity' => 'The quantity field is required.',
        ]);
        $this->assertDatabaseCount('items', 0);
    });

    it('returns 422 when a field is invalid', function (array $payload, string $invalidField, string $message) {
        $response = $this->postJson(route('api.v1.items.store'), $payload);

        $response->assertUnprocessable()
            ->assertOnlyJsonValidationErrors([$invalidField => $message]);
        $this->assertDatabaseCount('items', 0);
    })->with([
        'name too long' => [['name' => str_repeat('a', 256), 'color' => 'Red', 'quantity' => 1], 'name', 'The name field must not be greater than 255 characters.'],
        'color too long' => [['name' => 'Box', 'color' => str_repeat('a', 256), 'quantity' => 1], 'color', 'The color field must not be greater than 255 characters.'],
        'non-integer quantity' => [['name' => 'Box', 'color' => 'Red', 'quantity' => 'many'], 'quantity', 'The quantity field must be an integer.'],
        'negative quantity' => [['name' => 'Box', 'color' => 'Red', 'quantity' => -1], 'quantity', 'The quantity field must be at least 0.'],
    ]);
});

describe('update', function () {
    it('updates only the fields that are sent', function () {
        $item = Item::factory()->create(['name' => 'Box', 'color' => 'Red', 'quantity' => 10]);

        $response = $this->patchJson(route('api.v1.items.update', $item), ['quantity' => 3]);

        $response->assertOk()
            ->assertJsonPath('data.quantity', 3)
            ->assertJsonPath('data.is_low_stock', true);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Box',
            'color' => 'Red',
            'quantity' => 3,
        ]);
    });

    it('returns 422 when a sent field is invalid', function () {
        $item = Item::factory()->create(['name' => 'Box', 'quantity' => 10]);

        $response = $this->patchJson(route('api.v1.items.update', $item), [
            'name' => '',
            'quantity' => -1,
        ]);

        $response->assertUnprocessable()->assertOnlyJsonValidationErrors([
            'name' => 'The name field is required.',
            'quantity' => 'The quantity field must be at least 0.',
        ]);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Box', 'quantity' => 10]);
    });
});

describe('destroy', function () {
    it('deletes the item and returns 204', function () {
        $item = Item::factory()->create();

        $response = $this->deleteJson(route('api.v1.items.destroy', $item));

        $response->assertNoContent();
        $this->assertModelMissing($item);
    });
});
