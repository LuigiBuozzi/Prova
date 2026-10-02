<?php

use App\Models\Item;

it('flags items below 5 as low stock', function (int $quantity, bool $isLowStock) {
    $item = new Item(['quantity' => $quantity]);

    expect($item->isLowStock())->toBe($isLowStock);
})->with([
    'zero' => [0, true],
    'just below the limit' => [4, true],
    'exactly at the limit' => [5, false],
    'above the limit' => [6, false],
]);
