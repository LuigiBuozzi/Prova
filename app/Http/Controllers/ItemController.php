<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Display the item form and the list of stored items.
     */
    public function index(): View
    {
        return view('items.index', [
            'items' => Item::latest()->get(),
        ]);
    }

    /**
     * Store a newly created item.
     */
    public function store(StoreItemRequest $request): RedirectResponse
    {
        Item::create($request->validated());

        return to_route('items.index')->with('status', 'Item added.');
    }
}
