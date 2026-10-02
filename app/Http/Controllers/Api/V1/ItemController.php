<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    /**
     * Display a paginated listing of the items, newest first.
     */
    public function index(): AnonymousResourceCollection
    {
        return ItemResource::collection(Item::latest()->paginate());
    }

    /**
     * Store a newly created item.
     */
    public function store(StoreItemRequest $request): JsonResponse
    {
        $item = Item::create($request->validated());

        return ItemResource::make($item)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): ItemResource
    {
        return ItemResource::make($item);
    }

    /**
     * Update the specified item.
     */
    public function update(UpdateItemRequest $request, Item $item): ItemResource
    {
        $item->update($request->validated());

        return ItemResource::make($item);
    }

    /**
     * Remove the specified item.
     */
    public function destroy(Item $item): Response
    {
        $item->delete();

        return response()->noContent();
    }
}
