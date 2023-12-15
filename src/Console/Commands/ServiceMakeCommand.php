<?php

namespace Lego\Console\Commands;

use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\ServiceGenerator;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class ServiceMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The base namespace for this command.
     *
     * @var string
     */
    private string $namespace;

    /**
     * The Services path.
     *
     * @var string
     */
    private string $path;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/service.stub';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $name = $this->argument('name');

            $generator = new ServiceGenerator();
            $service = $generator->generate($name);

            $this->info('Service '.$service->name.' created successfully.'."\n");

            $rootNamespace = $this->findRootNamespace();
            $serviceNamespace = $this->findServiceNamespace($service->name);

            $serviceProvider = $serviceNamespace.'\\Providers\\'.$service->name.'ServiceProvider';

            $this->info('Activate it by adding '.
                '<comment>'.$serviceProvider.'::class</comment> '.
                "\nto <comment>'providers' in config/app.php</comment>".
                "\n"
            );
        } catch (\Exception $e) {
            $this->error($e->getMessage()."\n".$e->getFile().' at '.$e->getLine());
        }
    }

    public function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The service name.'],
        ];
    }
}
