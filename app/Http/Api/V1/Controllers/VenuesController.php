<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Api\V1\Requests\Venue\SaveVenueRequest;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class VenuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return QueryBuilder::for(Venue::class)
            ->allowedSorts(['name'])
            ->paginate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function list(): array
    {
        return QueryBuilder::for(Venue::class)
            ->select(['id', 'name'])
            ->allowedSorts(['name'])
            ->defaultSort(['name'])
            ->get()->toArray();
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue): Venue
    {
        return $venue;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveVenueRequest $request, Venue $venue): Venue
    {
        $venue->fill($request->validated())->save();

        return $venue;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveVenueRequest $request, Venue $venue): Venue
    {
        $venue->fill($request->validated())->save();

        return $venue;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue): array
    {
        $venue->delete();

        return [];
    }
}
