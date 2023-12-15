<?php

namespace Lego\Generators;

use Exception;
use Lego\Entities\Policy;
use Lego\Str;
use RuntimeException;

class PolicyGenerator extends Generator
{
    /**
     * Generate the file.
     *
     * @param string $name
     * @return Policy
     *
     * @throws Exception
     */
    public function generate(string $name): Policy
    {
        $policy = Str::policy($name);
        $path = $this->findPolicyPath($policy);

        if ($this->exists($path)) {
            throw new RuntimeException('Policy already exists');
        }

        $this->createPolicyDirectory();

        $namespace = $this->findPolicyNamespace();

        $content = file_get_contents($this->getStub());
        $content = str_replace(
            ['{{policy}}', '{{namespace}}'],
            [$policy, $namespace],
            $content
        );

        $this->createFile($path, $content);

        return new Policy(
            $policy,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $content
        );
    }

    /**
     * Create Policies directory.
     */
    public function createPolicyDirectory(): void
    {
        $this->createDirectory($this->findPoliciesPath());
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/policy.stub';
    }
}
