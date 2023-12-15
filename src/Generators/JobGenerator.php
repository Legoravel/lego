<?php

namespace Lego\Generators;

use Exception;
use Lego\Entities\Job;
use Lego\Str;
use RuntimeException;

class JobGenerator extends Generator
{
    /**
     * @throws Exception
     */
    public function generate($job, $domain, $isQueueable = false): Job
    {
        $job = Str::job($job);
        $domain = Str::domain($domain);
        $path = $this->findJobPath($domain, $job);

        if ($this->exists($path)) {
            throw new RuntimeException('Job already exists');
        }

        // Make sure the domain directory exists
        $this->createDomainDirectory($domain);

        // Create the job
        $namespace = $this->findDomainJobsNamespace($domain);

        $content = file_get_contents($this->getStub($isQueueable));
        $content = str_replace(
            ['{{job}}', '{{namespace}}', '{{unit_namespace}}'],
            [$job, $namespace, $this->findUnitNamespace()],
            $content
        );

        $this->createFile($path, $content);

        $this->generateTestFile($job, $domain);

        return new Job(
            $job,
            $namespace,
            basename($path),
            $path,
            $this->relativeFromReal($path),
            $this->findDomain($domain),
            $content
        );
    }

    /**
     * Create domain directory.
     *
     * @param string $domain
     */
    private function createDomainDirectory(string $domain): void
    {
        $this->createDirectory($this->findDomainPath($domain) . '/Jobs');
        $this->createDirectory($this->findDomainTestsPath($domain) . '/Jobs');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub($isQueueable = false): string
    {
        if ($isQueueable) {
            $stubName = '/stubs/job-queueable.stub';
        } else {
            $stubName = '/stubs/job.stub';
        }

        return __DIR__ . $stubName;
    }

    /**
     * Generate test file.
     *
     * @param string $job
     * @param string $domain
     * @throws Exception
     */
    private function generateTestFile(string $job, string $domain): void
    {
        $content = file_get_contents($this->getTestStub());

        $namespace = $this->findDomainJobsTestsNamespace($domain);
        $jobNamespace = $this->findDomainJobsNamespace($domain) . "\\$job";
        $testClass = $job . 'Test';

        $content = str_replace(
            ['{{namespace}}', '{{testclass}}', '{{job}}', '{{job_namespace}}'],
            [$namespace, $testClass, Str::snake($job), $jobNamespace],
            $content
        );

        $path = $this->findJobTestPath($domain, $testClass);

        $this->createFile($path, $content);
    }

    /**
     * Get the test stub file for the generator.
     *
     * @return string
     */
    public function getTestStub(): string
    {
        return __DIR__ . '/stubs/job-test.stub';
    }
}
