<?php

namespace Base\Tools\ModelsGenerator;

use Base\Custom\Illuminate\Database\Eloquent\Model as BaseEModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;


class ModelsBuilder
{
    public function __construct(
        public string $baseNamespace,
        public string $basePath,
    ) {
    }

    /**
     * @param SplFileInfo $file
     * @param string $type
     * @param string $name
     * @return string
     */
    public function modifyInheritsModel(SplFileInfo $file, string $type, string $name): string
    {
        $relations = '';

        if (preg_match_all('/ \* @property [A-Z].+? \\$.+/', $file->getContents(), $ms)) {
            $relations = '/**' . "\n" . implode("\n", $ms[0]) . "\n" . '*/' . "\n";
        }

        return '<?php' . "\n\n" .
            'namespace ' . ucfirst(Str::camel($type)) . '\Models;' . "\n\n" .
            'use Base\Models\Base' . $name . ';' . "\n\n" .
            $relations .
            'class ' . $name . ' extends Base' . $name . "\n" .
            '{' . "\n\n" .
            '}';
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    public function modifyBaseModel(SplFileInfo $file): string
    {
        $contents = $file->getContents();
        $relationClasses = $this->parseRelationClasses($contents);
        $relationModels = $this->parseRelationModels($contents);
        $class = $this->classFromFile($file->getRealPath());
        $name = class_basename($class);

        $replaces = [
            '@property integer' => '@property int',
            '@property boolean' => '@property bool',
            'class ' . $name . ' extends Model' => 'abstract class Base' . $name . ' extends Model',
        ];

        foreach ($relationClasses as $name => $class) {
            $replaces['@return ' . '\\' . $class] = '@return ' . $name;
        }

        foreach ($relationModels as $name => &$class) {
            $replaces["'" . $class . "'"] = $name . '::class';
            $class = preg_replace('/\\\\' . $name . '$/', '\\Base' . $name, $class);
            $class .= ' as ' . $name;
        }

        unset($class);

        $contents = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $contents,
        );

        $this->modifyMethods($contents);
        $this->modifyUses($contents, array_merge($relationClasses, $relationModels));

        return $contents;
    }

    /**
     * @param string $contents
     * @param array $uses
     * @return void
     */
    protected function modifyUses(string &$contents, array $uses): void
    {
        $replace = [];

        if (preg_match_all('/^use (.+?);/m', $contents, $matches)) {
            foreach ($matches[1] ?? [] as $class) {
                $replace[] = 'use ' . $class . ';';
                $class = ($class == Model::class) ? BaseEModel::class : $class;
                $uses[class_basename($class)] = $class;
            }
        }

        sort($uses);
        $result = array_map(fn($class) => 'use ' . $class . ';', $uses);
        $contents = str_replace(implode("\n", $replace), implode("\n", $result), $contents);
    }

    /**
     * @param string $contents
     * @return void
     */
    protected function modifyMethods(string &$contents): void
    {
        $pattern = preg_quote('/**', '/') . '\\s+' .
            preg_quote('* @return ') . '(.+?)' . '\\s+' .
            preg_quote('*/', '/') . '\\s+' .
            'public function .+?' . preg_quote('()');

        $contents = preg_replace_callback('/' . $pattern . '/m', function ($ms) {
            return $ms[0] . ': ' . $ms[1];
        }, $contents);
    }

    /**
     * @param string $contents
     * @return array
     */
    protected function parseRelationClasses(string $contents): array
    {
        $result = [];
        $pattern = '@return (' . preg_quote('\\Illuminate\\Database\\Eloquent\\Relations\\') . '.+)';

        if (preg_match_all('/' . $pattern . '/m', $contents, $matches)) {
            foreach ($matches[1] ?? [] as $class) {
                $result[class_basename($class)] = ltrim($class, '\\');
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * @param string $contents
     * @return array
     */
    protected function parseRelationModels(string $contents): array
    {
        $result = [];
        $pattern = "'(" . preg_quote($this->baseNamespace) . "\\\.+?)'";

        if (preg_match_all('/' . $pattern . '/m', $contents, $matches)) {
            foreach ($matches[1] ?? [] as $class) {
                $result[class_basename($class)] = $class;
            }
        }

        ksort($result);

        return $result;
    }

    /**
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
