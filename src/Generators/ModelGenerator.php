<?php

namespace Lego\Generators;

use Exception;
use Lego\Entities\Model;
use Lego\Str;
use RuntimeException;

class ModelGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param $name
     * @return Model|bool
     *
     * @throws Exception
     */
    public function generate($name): bool|Model
    {
        $model = Str::model($name);
        $path = $this->findModelPath($model);

        if ($this->exists($path)) {
            throw new RuntimeException('Model already exists');
        }

        $namespace = $this->findModelNamespace();

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{model}}', '{{namespace}}', '{{unit_namespace}}'],
            [$model, $namespace, $this->findUnitNamespace()],
            $content
        );

        $this->createFile($path, $content);

        return new Model(
            $model,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $content
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        if ($this->laravelVersion() > 7) {
            return __DIR__.'/../Generators/stubs/model-8.stub';
        }

        return __DIR__.'/../Generators/stubs/model.stub';
    }
}
