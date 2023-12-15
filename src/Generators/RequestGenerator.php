<?php

namespace Lego\Generators;

use Exception;
use Lego\Entities\Request;
use Lego\Str;
use RuntimeException;

class RequestGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param string $name
     * @param string $domain
     * @return Request|bool
     *
     * @throws Exception
     */
    public function generate(string $name, string $domain): Request|bool
    {
        $request = Str::request($name);
        $domain = Str::domain($domain);

        $path = $this->findRequestPath($domain, $request);

        if ($this->exists($path)) {
            throw new RuntimeException('Request already exists');
        }

        $namespace = $this->findRequestsNamespace($domain);

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{request}}', '{{namespace}}'],
            [$request, $namespace],
            $content
        );

        $this->createFile($path, $content);

        return new Request(
            $request,
            $domain,
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
        return __DIR__.'/../Generators/stubs/request.stub';
    }
}
