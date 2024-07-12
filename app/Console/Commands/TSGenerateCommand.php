<?php

namespace App\Console\Commands;

use Base\Tools\TSGenerator\TSBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionException;

class TSGenerateCommand extends Command
{
    protected $signature = 'ts:generate';
    protected $description = 'Generate typescript interfaces from app models';
    protected string $destination = 'frontend/app/src/models';
    protected string $source = 'app/Models';

    /**
     * @return void
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $dir = base_path($this->destination);
        File::ensureDirectoryExists($dir . '/interfaces');

        foreach (File::allFiles(base_path($this->source)) as $file) {
            if ($file->getExtension() != 'php') {
                continue;
            }

            $class = $this->classFromFile($file->getRealPath());
            $path = $dir . '/interfaces/I' . class_basename($class) . '.d.ts';
            $contents = (new TSBuilder($class))->buildInterface();

            file_put_contents($path, $contents);
        }
    }

    /**
     * Extract the command class name from the given file path.
     *
     * @param string $file
     * @return string
     */
    private function classFromFile(string $file): string
    {
        return str_replace(
            ['/', '.php'],
            ['\\', ''],
            ucfirst(Str::after($file, base_path() . DIRECTORY_SEPARATOR)),
        );
    }
}
