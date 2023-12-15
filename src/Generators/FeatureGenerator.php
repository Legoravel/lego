<?php

namespace Lego\Generators;

use Exception;
use Lego\Entities\Feature;
use Lego\Str;
use RuntimeException;

class FeatureGenerator extends Generator
{
    /**
     * @throws Exception
     */
    public function generate($feature, $service, array $jobs = []): Feature
    {
        $feature = Str::feature($feature);
        $service = Str::service($service);

        $path = $this->findFeaturePath($service, $feature);
        $classname = $this->classname($feature);

        if ($this->exists($path)) {
            throw new RuntimeException('Feature already exists!');
        }

        $namespace = $this->findFeatureNamespace($service, $feature);

        $content = file_get_contents($this->getStub());

        $useJobs = ''; // stores the `use` statements of the jobs
        $runJobs = ''; // stores the `$this->run` statements of the jobs

        foreach ($jobs as $index => $job) {
            $useJobs .= 'use '.$job['namespace'].'\\'.$job['className'].";\n";
            $runJobs .= "\t\t".'$this->run('.$job['className'].'::class);';

            // only add carriage returns when it's not the last job
            if ($index !== count($jobs) - 1) {
                $runJobs .= "\n\n";
            }
        }

        $content = str_replace(
            ['{{feature}}', '{{namespace}}', '{{unit_namespace}}', '{{use_jobs}}', '{{run_jobs}}'],
            [$classname, $namespace, $this->findUnitNamespace(), $useJobs, $runJobs],
            $content
        );

        $this->createFile($path, $content);

        // generate test file
        $this->generateTestFile($feature, $service);

        return new Feature(
            $feature,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            ($service) ? $this->findService($service) : null,
            $content
        );
    }

    private function classname($feature)
    {
        $parts = explode(DS, $feature);

        return array_pop($parts);
    }

    /**
     * Generate the test file.
     *
     * @param string $feature
     * @param string $service
     * @throws Exception
     */
    private function generateTestFile(string $feature, string $service): void
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findFeatureTestNamespace($service);
        $featureClass = $this->classname($feature);
        $featureNamespace = $this->findFeatureNamespace($service, $feature).'\\'.$featureClass;
        $testClass = $featureClass.'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{feature}}', '{{feature_namespace}}'],
            [$namespace, $testClass, Str::snake(str_replace(DS, '', $feature)), $featureNamespace],
            $content
        );

        $path = $this->findFeatureTestPath($service, $feature.'Test');

        $this->createFile($path, $content);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/feature.stub';
    }

    /**
     * Get the test stub file for the generator.
     *
     * @return string
     */
    private function getTestStub(): string
    {
        return __DIR__.'/stubs/feature-test.stub';
    }
}
