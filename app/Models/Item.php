<?php

namespace App\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'color', 'quantity'])]
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    /**
     * Items with a quantity below this value are considered low on stock.
     */
    public const LOW_STOCK_THRESHOLD = 5;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * Determine whether the item is running low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->quantity < self::LOW_STOCK_THRESHOLD;
    }
}
