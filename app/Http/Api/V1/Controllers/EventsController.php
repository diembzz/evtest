<?php

namespace App\Http\Api\V1\Controllers;

use App\Http\Api\V1\Requests\Event\SaveEventRequest;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\File;
use Base\Custom\Spatie\QueryBuilder\Sorts\RelationSort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EventsController extends Controller
{
    public const POSTERS_PATH = 'images/events/posters';
    public const POSTERS_FULL_PATH = File::PUBLIC_PATH . '/' . self::POSTERS_PATH;

    /**
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return QueryBuilder::for(Event::class)
            ->with('venue', 'poster')
            ->defaultSort(['-id'])
            ->allowedSorts([
                'name',
                'event_date',
                AllowedSort::custom('venue.name', new RelationSort()),
            ])
            ->paginate();
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): Event
    {
        return $event->load('venue', 'poster');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveEventRequest $request, Event $event): Event
    {
        $event->fill($request->validated());
        $this->handlePosterFile($event, $request->validated('poster.src'));
        $event->save();

        return $event;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveEventRequest $request, Event $event): Event
    {
        $event->fill($request->validated());
        $this->handlePosterFile($event, $request->validated('poster.src'));
        $event->save();

        return $event;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): array
    {
        $event->delete();

        return [];
    }

    /**
     * @param Event $model
     * @param string $b64
     * @return void
     */
    private function handlePosterFile(Event $model, string $b64): void
    {
        if (str_starts_with($b64, 'data:image/')) {
            list($type, $b64) = explode(';', $b64);
            list(, $b64) = explode(',', $b64);
            $ext = substr($type, strlen('data:image/'));

            do {
                $name = md5(uniqid(rand(), true));
            } while (
                file_exists(self::POSTERS_FULL_PATH . '/' . $name . '.' . $b64)
            );

            file_put_contents(
                base_path(self::POSTERS_FULL_PATH . '/' . $name . '.' . $ext),
                base64_decode($b64),
            );

            $file = File::create([
                'base' => self::POSTERS_PATH,
                'path' => $name . '.' . $ext,
            ]);

            if ($model->exists) {
                $old = $model->poster;
                $model->update(['poster_id' => $file->id]);
                $old && $old->delete();
            } else {
                $model->poster_id = $file->id;
            }
        }
    }
}
