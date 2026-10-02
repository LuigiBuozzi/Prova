<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the item form and existing items', function () {
    Item::factory()->create(['name' => 'Widget']);

    $this->get(route('items.index'))
        ->assertOk()
        ->assertSee('Add an item')
        ->assertSee('Widget');
});

it('stores a new item', function () {
    $this->post(route('items.store'), [
        'name' => 'Pencil',
        'color' => 'Yellow',
        'quantity' => 12,
    ])->assertRedirect(route('items.index'));

    $this->assertDatabaseHas('items', [
        'name' => 'Pencil',
        'color' => 'Yellow',
        'quantity' => 12,
    ]);
});

it('validates the item fields', function (array $payload, string $invalidField) {
    $this->post(route('items.store'), $payload)
        ->assertSessionHasErrors($invalidField);

    expect(Item::count())->toBe(0);
})->with([
    'missing name' => [['color' => 'Red', 'quantity' => 1], 'name'],
    'missing color' => [['name' => 'Box', 'quantity' => 1], 'color'],
    'non-numeric quantity' => [['name' => 'Box', 'color' => 'Red', 'quantity' => 'many'], 'quantity'],
    'negative quantity' => [['name' => 'Box', 'color' => 'Red', 'quantity' => -1], 'quantity'],
]);

it('shows a low stock badge only for items running low', function () {
    Item::factory()->create(['name' => 'Eraser', 'quantity' => 2]);
    Item::factory()->create(['name' => 'Notebook', 'quantity' => 50]);

    $response = $this->get(route('items.index'))->assertOk();

    expect(substr_count($response->getContent(), 'Low stock'))->toBe(1);
});
