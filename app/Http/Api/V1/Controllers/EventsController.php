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
    public const POSTER_IMAGE_MAX_WIDTH = 800;
    public const POSTER_IMAGE_MAX_HEIGHT = 800;

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
     * @throws \ImagickException
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
     * @param string $base64
     * @return void
     * @throws \ImagickException
     *
     * @todo handle with File model, after implementing nested models validation.
     */
    private function handlePosterFile(Event $model, string $base64): void
    {
        if (str_starts_with($base64, 'data:image/')) {
            list($head, $b64) = explode(';', $base64);
            list(, $b64) = explode(',', $b64);
            $ext = substr($head, strlen('data:image/'));
            $this->cropPosterImage($b64);

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

    /**
     * @param string $b64
     * @return void
     * @throws \ImagickException
     *
     * @todo handle with File (based) model, after implementing nested models validation.
     */
    private function cropPosterImage(string &$b64): void
    {
        $imagick = new \Imagick();
        $imagick->readImageBlob(base64_decode($b64));
        $imagick->scaleImage(
            self::POSTER_IMAGE_MAX_WIDTH,
            self::POSTER_IMAGE_MAX_HEIGHT,
            true,
        );

        $b64 = base64_encode($imagick->getImageBlob());
    }
}
