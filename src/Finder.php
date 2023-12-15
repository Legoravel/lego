<?php

namespace Lego;

use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Lego\Entities\Domain;
use Lego\Entities\Feature;
use Lego\Entities\Job;
use Lego\Entities\Service;
use RuntimeException;
use Symfony\Component\Finder\Finder as SymfonyFinder;

trait Finder
{
    public function fuzzyFind($query): array
    {
        $finder = new SymfonyFinder();

        $files = $finder->in($this->findServicesRootPath() . '/*/Features') // features
        ->in($this->findDomainsRootPath() . '/*/Jobs') // jobs
        ->name('*.php')
            ->files();

        $matches = [
            'jobs' => [],
            'features' => [],
        ];

        foreach ($files as $file) {
            $base = $file->getBaseName();
            $name = str_replace(['.php', ' '], '', $base);

            $query = str_replace(' ', '', trim($query));

            similar_text($query, mb_strtolower($name), $percent);

            if ($percent > 35) {
                if (strpos($base, 'Feature.php')) {
                    $matches['features'][] = [$this->findFeature($name)->toArray(), $percent];
                } elseif (strpos($base, 'Job.php')) {
                    $matches['jobs'][] = [$this->findJob($name)->toArray(), $percent];
                }
            }
        }

        // sort the results by their similarity percentage
        $this->sortFuzzyResults($matches['jobs']);
        $this->sortFuzzyResults($matches['features']);

        $matches['features'] = $this->mapFuzzyResults($matches['features']);
        $matches['jobs'] = array_map(static function ($result) {
            return $result[0];
        }, $matches['jobs']);

        return $matches;
    }

    /**
     * Find the root path of all the services.
     *
     * @return string
     */
    public function findServicesRootPath(): string
    {
        return $this->findSourceRoot() . DIRECTORY_SEPARATOR . 'Services';
    }

    /**
     * get the root of the source directory.
     *
     * @return string
     */
    public function findSourceRoot(): string
    {
        return app_path();
    }

    /**
     * Find the root path of domains.
     *
     * @return string
     */
    public function findDomainsRootPath(): string
    {
        return $this->findSourceRoot() . DIRECTORY_SEPARATOR . 'Domains';
    }

    /**
     * Find the feature for the given feature name.
     *
     * @param string $name
     * @return Feature
     * @throws Exception
     */
    public function findFeature($name): Feature
    {
        $name = Str::feature($name);
        $fileName = "$name.php";

        $finder = new SymfonyFinder();
        $files = $finder->name($fileName)->in($this->findServicesRootPath())->files();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $serviceName = strstr($file->getRelativePath(), DIRECTORY_SEPARATOR, true);
            $service = $this->findService($serviceName);
            $content = file_get_contents($path);

            return new Feature(
                Str::realName($name, '/Feature/'),
                $fileName,
                $path,
                $this->relativeFromReal($path),
                $service,
                $content
            );
        }
    }

    /**
     * Find the service for the given service name.
     *
     * @param string $service
     * @return Service
     *
     * @throws Exception
     */
    public function findService(string $service): Service
    {
        $finder = new SymfonyFinder();
        $dirs = $finder->name($service)->in($this->findServicesRootPath())->directories();
        if ($dirs->count() < 1) {
            throw new Exception('Service "' . $service . '" could not be found.');
        }

        foreach ($dirs as $dir) {
            $path = $dir->getRealPath();

            return new Service(Str::service($service), $path, $this->relativeFromReal($path));
        }
    }

    /**
     * Get the relative version of the given real path.
     *
     * @param string $path
     * @param string $needle
     * @return string
     */
    protected function relativeFromReal(string $path, string $needle = ''): string
    {
        if (!$needle) {
            $needle = $this->getSourceDirectoryName() . DIRECTORY_SEPARATOR;
        }

        return strstr($path, $needle);
    }

    /**
     * Get the source directory name.
     *
     * @return string
     */
    public function getSourceDirectoryName(): string
    {
        return 'app';
    }

    /**
     * Find the feature for the given feature name.
     *
     * @param string $name
     * @return Job
     * @throws Exception
     */
    public function findJob(string $name): Job
    {
        $name = Str::job($name);
        $fileName = "$name.php";

        $finder = new SymfonyFinder();
        $files = $finder->name($fileName)->in($this->findDomainsRootPath())->files();
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $domainName = strstr($file->getRelativePath(), DIRECTORY_SEPARATOR, true);
            $domain = $this->findDomain($domainName);
            $content = file_get_contents($path);

            return new Job(
                Str::realName($name, '/Job/'),
                $this->findDomainJobsNamespace($domainName),
                $fileName,
                $path,
                $this->relativeFromReal($path),
                $domain,
                $content
            );
        }
    }

    /**
     * Find the domain for the given domain name.
     *
     * @param string $domain
     * @return Domain
     * @throws Exception
     */
    public function findDomain(string $domain): Domain
    {
        $finder = new SymfonyFinder();
        $dirs = $finder->name($domain)->in($this->findDomainsRootPath())->directories();
        if ($dirs->count() < 1) {
            throw new RuntimeException('Domain "' . $domain . '" could not be found.');
        }

        foreach ($dirs as $dir) {
            $path = $dir->getRealPath();

            return new Domain(
                Str::service($domain),
                $this->findDomainNamespace($domain),
                $path,
                $this->relativeFromReal($path)
            );
        }
    }

    /**
     * Find the namespace for the given domain.
     *
     * @param string $domain
     * @return string
     *
     * @throws Exception
     */
    public function findDomainNamespace(string $domain): string
    {
        return $this->findRootNamespace() . '\\Domains\\' . $domain;
    }

    public function findRootNamespace(): string
    {
        return $this->findNamespace($this->getSourceDirectoryName());
    }

    /**
     * Get the namespace used for the application.
     *
     * @param string $dir
     * @return string
     *
     * @throws \JsonException
     */
    public function findNamespace(string $dir): string
    {
        // read composer.json file contents to determine the namespace
        $composer = json_decode(file_get_contents(base_path() . DIRECTORY_SEPARATOR . 'composer.json'), true, 512, JSON_THROW_ON_ERROR);

        // see which one refers to the "src/" directory
        foreach ($composer['autoload']['psr-4'] as $namespace => $directory) {
            $directory = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $directory);
            if ($directory === $dir . DIRECTORY_SEPARATOR) {
                return trim($namespace, '\\');
            }
        }

        throw new Exception('App namespace not set in composer.json');
    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     * @return string
     *
     * @throws Exception
     */
    public function findDomainJobsNamespace(string $domain): string
    {
        return $this->findDomainNamespace($domain) . '\Jobs';
    }

    /**
     * Sort the fuzzy-find results.
     *
     * @param array  &$results
     * @return bool
     */
    private function sortFuzzyResults(array &$results): bool
    {
        return usort($results, static function ($resultLeft, $resultRight) {
            return $resultLeft[1] < $resultRight[1];
        });
    }

    /**
     * Map the fuzzy-find results into the data
     * that should be returned.
     *
     * @param array $results
     * @return array
     */
    private function mapFuzzyResults(array $results): array
    {
        return array_map(static function ($result) {
            return $result[0];
        }, $results);
    }

    /**
     * Determines whether this is a lego microservice installation.
     *
     * @return bool
     */
    public function isMicroservice(): bool
    {
        return !file_exists(base_path() . DIRECTORY_SEPARATOR . $this->getSourceDirectoryName() . DIRECTORY_SEPARATOR . 'Services');
    }

    public function findAppNamespace(): string
    {
        return $this->findNamespace('app');
    }

    /**
     * Find the namespace of the foundation.
     *
     * @return string
     */
    public function findFoundationNamespace(): string
    {
        return 'Lego\Foundation';
    }

    /**
     * Find the namespace of a unit.
     *
     * @return string
     */
    public function findUnitNamespace(): string
    {
        return 'Lego\Units';
    }

    /**
     * Find the path to the directory of the given service name.
     * In the case of a microservice service installation this will be app path.
     *
     * @param string $service
     * @return string
     */
    public function findMigrationPath(string $service): string
    {
        return (!$service) ?
            'database/migrations' :
            $this->relativeFromReal($this->findServicesRootPath() . DIRECTORY_SEPARATOR . $service . '/database/migrations');
    }

    /**
     * Find the file path for the given feature.
     *
     * @param string $service
     * @param string $feature
     * @return string
     */
    public function findFeaturePath(string $service, string $feature): string
    {
        return $this->findFeaturesRootPath($service) . DIRECTORY_SEPARATOR . "$feature.php";
    }

    /**
     * Find the features root path in the given service.
     *
     * @param string $service
     * @return string
     */
    public function findFeaturesRootPath(string $service): string
    {
        return $this->findServicePath($service) . DIRECTORY_SEPARATOR . 'Features';
    }

    /**
     * Find the path to the directory of the given service name.
     * In the case of a microservice service installation this will be app path.
     *
     * @param string $service
     * @return string
     */
    public function findServicePath(string $service): string
    {
        return (!$service) ? app_path() : $this->findServicesRootPath() . DIRECTORY_SEPARATOR . $service;
    }

    /**
     * Find the test file path for the given feature.
     *
     * @param string $service
     * @param string $test
     * @return string
     */
    public function findFeatureTestPath(string $service, string $test): string
    {
        $root = $this->findFeatureTestsRootPath();

        if ($service) {
            $root .= DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $service;
        }

        return implode(DIRECTORY_SEPARATOR, [$root, "$test.php"]);
    }

    /**
     * Get the root path to feature tests directory.
     *
     * @return string
     */
    protected function findFeatureTestsRootPath(): string
    {
        return base_path() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Feature';
    }

    /**
     * Find the namespace for features in the given service.
     *
     * @param string $service
     * @param string $feature
     * @return string
     *
     * @throws Exception
     */
    public function findFeatureNamespace(string $service, string $feature): string
    {
        $dirs = implode('\\', explode(DIRECTORY_SEPARATOR, dirname($feature)));

        $base = $this->findServiceNamespace($service) . '\\Features';

        // greater than 1 because when there aren't subdirectories it will be "."
        if (strlen($dirs) > 1) {
            return $base . '\\' . $dirs;
        }

        return $base;
    }

    /**
     * Find the namespace for the given service name.
     *
     * @param string|null $service
     * @return string
     *
     * @throws Exception
     */
    public function findServiceNamespace(string $service = null): string
    {
        $root = $this->findRootNamespace();

        return (!$service) ? $root : "$root\\Services\\$service";
    }

    /**
     * Find the namespace for features tests in the given service.
     *
     * @param string|null $service
     * @return string
     */
    public function findFeatureTestNamespace(string $service = null): string
    {
        $namespace = $this->findFeatureTestsRootNamespace();

        if ($service) {
            $namespace .= "\\Services\\$service";
        }

        return $namespace;
    }

    protected function findFeatureTestsRootNamespace(): string
    {
        return 'Tests\\Feature';
    }

    /**
     * Find the file path for the given operation.
     *
     * @param string $service
     * @param string $operation
     * @return string
     */
    public function findOperationPath(string $service, string $operation): string
    {
        return $this->findOperationsRootPath($service) . DIRECTORY_SEPARATOR . "$operation.php";
    }

    /**
     * Find the operations root path in the given service.
     *
     * @param string $service
     * @return string
     */
    public function findOperationsRootPath(string $service): string
    {
        return $this->findServicePath($service) . DIRECTORY_SEPARATOR . 'Operations';
    }

    /**
     * Find the test file path for the given operation.
     *
     * @param string $service
     * @param string $test
     * @return string
     */
    public function findOperationTestPath(string $service, string $test): string
    {
        $root = $this->findUnitTestsRootPath();

        if ($service) {
            $root .= DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . $service;
        }

        return implode(DIRECTORY_SEPARATOR, [$root, 'Operations', "$test.php"]);
    }

    /**
     * Get the root path to unit tests directory.
     *
     * @return string
     */
    protected function findUnitTestsRootPath(): string
    {
        return base_path() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Unit';
    }

    /**
     * Find the namespace for operations in the given service.
     *
     * @param string $service
     * @return string
     *
     * @throws Exception
     */
    public function findOperationNamespace(string $service): string
    {
        return $this->findServiceNamespace($service) . '\\Operations';
    }

    /**
     * Find the namespace for operations tests in the given service.
     *
     * @param string|null $service
     * @return string
     *
     * @throws Exception
     */
    public function findOperationTestNamespace(string $service = null): string
    {
        $namespace = $this->findUnitTestsRootNamespace();

        if ($service) {
            $namespace .= "\\Services\\$service";
        }

        return $namespace . '\\Operations';
    }

    /**
     * Get the root namespace for unit tests
     *
     * @return string
     */
    protected function findUnitTestsRootNamespace(): string
    {
        return 'Tests\\Unit';
    }

    /**
     * List the jobs per domain,
     * optionally provide a domain name to list its jobs.
     *
     * @param string|null $domainName
     * @return Collection
     * @throws Exception
     */
    public function listJobs(string $domainName = null): Collection
    {
        $domains = ($domainName) ? [$this->findDomain(Str::domain($domainName))] : $this->listDomains();

        $jobs = new Collection();
        foreach ($domains as $domain) {
            $path = $domain->realPath;

            $finder = new SymfonyFinder();
            $files = $finder
                ->name('*Job.php')
                ->in($path . DIRECTORY_SEPARATOR . 'Jobs')
                ->files();

            $jobs[$domain->name] = new Collection();

            foreach ($files as $file) {
                $name = $file->getRelativePathName();
                $job = new Job(
                    Str::realName($name, '/Job.php/'),
                    $this->findDomainJobsNamespace($domain->name),
                    $name,
                    $file->getRealPath(),
                    $this->relativeFromReal($file->getRealPath()),
                    $domain,
                    file_get_contents($file->getRealPath())
                );

                $jobs[$domain->name]->push($job);
            }
        }

        return $jobs;
    }

    /**
     * Get the list of domains.
     *
     * @return Collection;
     * @throws Exception
     */
    public function listDomains(): Collection
    {
        $finder = new SymfonyFinder();
        $directories = $finder
            ->depth(0)
            ->in($this->findDomainsRootPath())
            ->directories();

        $domains = new Collection();
        foreach ($directories as $directory) {
            $name = $directory->getRelativePathName();

            $domain = new Domain(
                Str::realName($name),
                $this->findDomainNamespace($name),
                $directory->getRealPath(),
                $this->relativeFromReal($directory->getRealPath())
            );

            $domains->push($domain);
        }

        return $domains;
    }

    /**
     * Find the path for the given job name.
     *
     * @param string $domain
     * @param string $job
     * @return string
     */
    public function findJobPath(string $domain, string $job): string
    {
        return $this->findDomainPath($domain) . DIRECTORY_SEPARATOR . 'Jobs' . DIRECTORY_SEPARATOR . $job . '.php';
    }

    /**
     * Find the path for the given domain.
     *
     * @param string $domain
     * @return string
     */
    public function findDomainPath(string $domain): string
    {
        return $this->findDomainsRootPath() . DIRECTORY_SEPARATOR . $domain;
    }

    /**
     * Find the namespace for the given domain's Jobs.
     *
     * @param string $domain
     * @return string
     *
     * @throws Exception
     */
    public function findDomainJobsTestsNamespace(string $domain): string
    {
        return $this->findUnitTestsRootNamespace() . "\\Domains\\$domain\\Jobs";
    }

    /**
     * Find the test path for the given job.
     *
     * @param string $domain
     * @param string $jobTest
     * @return string
     */
    public function findJobTestPath(string $domain, string $jobTest): string
    {
        return $this->findDomainTestsPath($domain) . DIRECTORY_SEPARATOR . 'Jobs' . DIRECTORY_SEPARATOR . "$jobTest.php";
    }

    /**
     * Get the path to the tests of the given domain.
     *
     * @param string $domain
     * @return string
     */
    public function findDomainTestsPath(string $domain): string
    {
        return $this->findUnitTestsRootPath() . DIRECTORY_SEPARATOR . 'Domains' . DIRECTORY_SEPARATOR . $domain;
    }

    /**
     * Find the path for the give controller class.
     *
     * @param string $service
     * @param string $controller
     * @return string
     */
    public function findControllerPath(string $service, string $controller): string
    {
        return $this->findServicePath($service) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['Http', 'Controllers', "$controller.php"]);
    }

    /**
     * Find the namespace of controllers in the given service.
     *
     * @param string $service
     * @return string
     * @throws Exception
     */
    public function findControllerNamespace(string $service): string
    {
        return $this->findServiceNamespace($service) . '\\Http\\Controllers';
    }

    /**
     * Get the list of features,
     * optionally withing a specified service.
     *
     * @param string $serviceName
     * @return array of Feature
     *
     * @throws Exception
     */
    public function listFeatures(string $serviceName = ''): array
    {
        $services = $this->listServices();

        if (!empty($serviceName)) {
            $services = $services->filter(fn($service) => $service->name === $serviceName || $service->slug === $serviceName
            );

            if ($services->isEmpty()) {
                throw new InvalidArgumentException('Service "' . $serviceName . '" could not be found.');
            }
        }

        $features = [];
        foreach ($services as $service) {
            $serviceFeatures = new Collection();
            $finder = new SymfonyFinder();
            $files = $finder
                ->name('*Feature.php')
                ->in($this->findFeaturesRootPath($service->name))
                ->files();
            foreach ($files as $file) {
                $fileName = $file->getRelativePathName();
                $title = Str::realName($fileName, '/Feature.php/');
                $realPath = $file->getRealPath();
                $relativePath = $this->relativeFromReal($realPath);

                $serviceFeatures->push(new Feature($title, $fileName, $realPath, $relativePath, $service));
            }

            // add to the features array as [service_name => Collection(Feature)]
            $features[$service->name] = $serviceFeatures;
        }

        return $features;
    }

    /**
     * Get the list of services.
     *
     * @return Collection
     */
    public function listServices(): Collection
    {
        $services = new Collection();

        if (file_exists($this->findServicesRootPath())) {
            $finder = new SymfonyFinder();

            foreach ($finder->directories()->depth('== 0')->in($this->findServicesRootPath())->directories() as $dir) {
                $realPath = $dir->getRealPath();
                $services->push(
                    new Service($dir->getRelativePathName(), $realPath, $this->relativeFromReal($realPath))
                );
            }
        }

        return $services;
    }

    /**
     * Get the path to the passed model.
     *
     * @param string $model
     * @return string
     */
    public function findModelPath(string $model): string
    {
        return $this->getSourceDirectoryName() . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . "$model.php";
    }

    /**
     * Get the path to the passed policy.
     *
     * @param string $policy
     * @return string
     */
    public function findPolicyPath(string $policy): string
    {
        return $this->findPoliciesPath() . DIRECTORY_SEPARATOR . $policy . '.php';
    }

    /**
     * Get the path to the policies directory.
     *
     * @return string
     */
    public function findPoliciesPath(): string
    {
        return $this->getSourceDirectoryName() . DIRECTORY_SEPARATOR . 'Policies';
    }

    /**
     * Get the path to a specific request.
     *
     * @param string $domain
     * @param string $request
     * @return string
     */
    public function findRequestPath(string $domain, string $request): string
    {
        return $this->findRequestsPath($domain) . DIRECTORY_SEPARATOR . $request . '.php';
    }

    /**
     * Get the path to the request directory of a specific service.
     *
     * @param string $domain
     * @return string
     */
    public function findRequestsPath(string $domain): string
    {
        return $this->findDomainPath($domain) . DIRECTORY_SEPARATOR . 'Requests';
    }

    /**
     * Get the namespace for the Models.
     *
     * @return string
     *
     * @throws Exception
     */
    public function findModelNamespace(): string
    {
        return $this->findRootNamespace() . '\\Data\\Models';
    }

    /**
     * Get the namespace for Policies.
     *
     * @return string
     *
     * @throws Exception
     */
    public function findPolicyNamespace(): string
    {
        return $this->findRootNamespace() . '\\Policies';
    }

    /**
     * Get the requests namespace for the service passed in.
     *
     * @param string $domain
     * @return string
     *
     * @throws Exception
     */
    public function findRequestsNamespace(string $domain): string
    {
        return $this->findDomainNamespace($domain) . '\\Requests';
    }

    /**
     * Get the path to the Composer.json file.
     *
     * @return string
     */
    protected function getComposerPath(): string
    {
        return app()->basePath() . DIRECTORY_SEPARATOR . 'composer.json';
    }

    /**
     * Get the path to the given configuration file.
     *
     * @param string $name
     * @return string
     */
    protected function getConfigPath(string $name): string
    {
        return app()['path.config'] . DIRECTORY_SEPARATOR . "$name.php";
    }
}
