<?php

namespace Base\Tools\TSGenerator;

use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlockFactory;

class TSBuilder
{
    protected readonly DocBlockFactory $factory;

    protected array $types = [
        'string' => ['string'],
        'number' => ['int', 'integer', 'float', 'double'],
        'boolean' => ['bool', 'boolean'],
    ];

    public function __construct(
        public string $class,
    ) {
        $this->factory = DocBlockFactory::createInstance();
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function buildInterface(): string
    {
        $result = ['declare interface I' . class_basename($this->class) . ' {'];
        $properties = $this->convertProperties(
            $this->extractProperties($this->class),
        );

        foreach ($properties as $name => $type) {
            $result[] = '  ' . $name . ': ' . $type;
        }

        $result[] = '}';

        return implode("\n", $result);
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    protected function convertProperties(array $properties): array
    {
        return array_map([$this, 'convertType'], $properties);
    }

    /**
     * @param string $phpType
     *
     * @return string
     */
    protected function convertType(string $phpType): string
    {
        foreach ($this->types as $tsType => $phpTypes) {
            if (in_array($phpType, $phpTypes)) {
                return $tsType;
            }
        }

        if ($this->isModelType($phpType)) {
            return 'I' . $phpType;
        }

        return $phpType;
    }

    /**
     * @param string $class
     *
     * @return array<string, string>
     * @throws \ReflectionException
     */
    protected function extractProperties(string $class): array
    {
        $result = [];
        $reflection = new \ReflectionClass($class);
        $parent = $reflection->getParentClass();
        $parentDoc = $this->factory->create($parent->getDocComment() ?: '/**  */');
        $tags = $parentDoc->getTagsWithTypeByName('property');
        $doc = $this->factory->create($reflection->getDocComment() ?: '/**  */');
        $tags = array_merge($tags, $doc->getTagsWithTypeByName('property'));

        /** @var Property $tag */
        foreach ($tags as $tag) {
            $type = (string)$tag->getType();

            if ($type == 'static') {
                $type = class_basename($class);
            } elseif (str_starts_with($type, '\\')) {
                $type = substr($type, 1);
            }

            $result[$tag->getVariableName()] = $type;
        }

        return $result;
    }

    /**
     * @param string $phpType
     *
     * @return bool
     */
    protected function isModelType(string $phpType): bool
    {
        return ctype_upper($phpType[0]);
    }
}
