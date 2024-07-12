<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\File;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public const BASE = 'images/events/posters';
    public const SOURCE = 'database/seeders/files/' . self::BASE;
    public const DEST = 'storage/app/public/' . self::BASE;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Throwable
     */
    public function definition(): array
    {
        try {
            $index = File::where('base', self::BASE)->count() ?? 0;
            $files = glob(base_path(self::SOURCE) . '/*.jpg');
            $source = $files[$index];

            $file = File::create([
                'base' => self::BASE,
                'path' => basename($source),
            ]);

            if (!FileFacade::exists(base_path(self::DEST))) {
                FileFacade::makeDirectory(base_path(self::DEST), recursive: true);
            }

            copy($source, base_path(self::DEST) . '/' . $file->path);
        } catch (\Throwable $e) {
            array_map('unlink', glob(base_path(self::DEST) . '/*.jpg'));
            is_dir(base_path(self::DEST)) && rmdir(base_path(self::DEST));
            throw $e;
        }

        return [
            'name' => Str::title($this->faker->unique()->words(2, true)),
            'venue_id' => Venue::select('id')->inRandomOrder()->value('id'),
            'event_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'poster_id' => $file->id,
        ];
    }
}
