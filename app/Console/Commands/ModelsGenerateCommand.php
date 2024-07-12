<?php

namespace App\Console\Commands;

use Base\Tools\ModelsGenerator\ModelsBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModelsGenerateCommand extends Command
{
    protected $signature = 'models:generate {path=base}';
    protected $description = 'Generate Models from database';
    protected array $skipTables = ['migrations', 'personal_access_tokens', 'password_reset_tokens'];
    protected string $baseNamespace = 'Base\\Models';
    protected string $basePath = 'base/Models';
    protected ModelsBuilder $builder;

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->builder = new ModelsBuilder($this->baseNamespace, $this->basePath);

        match ($this->argument('path')) {
            'base' => $this->generateBase(),
            default => $this->generateInherits($this->argument('path')),
        };
    }

    /**
     * @return void
     */
    protected function generateBase(): void
    {
        $args = [
            '--output-path=' . base_path($this->basePath),
            '--namespace=' . str_replace('\\', '\\\\', $this->baseNamespace),
            '--no-backup',
        ];

        foreach ($this->skipTables as $table) {
            $args[] = '--skip-table=' . $table;
        }

        Artisan::call('krlove:generate:models ' . implode(' ', $args));

        foreach (File::allFiles(base_path($this->basePath)) as $file) {
            if (
                $file->getExtension() != 'php' ||
                str_starts_with($file->getBasename(), 'Base')
            ) {
                continue;
            }

            $contents = $this->builder->modifyBaseModel($file);
            file_put_contents($file->getPath() . '/Base' . $file->getBasename(), $contents);
            unlink($file->getRealPath());
        }
    }

    /**
     * @param string $type
     * @return void
     */
    protected function generateInherits(string $type): void
    {
        foreach (File::allFiles(base_path($this->basePath)) as $file) {
            if (
                $file->getExtension() != 'php' ||
                !str_starts_with($file->getBasename(), 'Base')
            ) {
                continue;
            }

            $name = substr($file->getBasename(), strlen('base'), -strlen('.php'));
            $path = $type . substr($this->basePath, strlen('base')) . '/' . $name . '.php';
            $contents = $this->builder->modifyInheritsModel($file, $type, $name);

            if (!file_exists($path)) {
                file_put_contents($path, $contents);
            }
        }
    }
}
