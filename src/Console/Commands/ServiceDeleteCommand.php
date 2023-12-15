<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class ServiceDeleteCommand extends SymfonyCommand
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
    protected string $name = 'delete:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Service';

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
        if ($this->isMicroservice()) {
            $this->error('This functionality is disabled in a Microservice');
        }

        try {
            $name = Str::service($this->argument('name'));

            if (! $this->exists($service = $this->findServicePath($name))) {
                $this->error('Service '.$name.' cannot be found.');
            }

            $this->delete($service);

            $this->info('Service <comment>'.$name.'</comment> deleted successfully.'."\n");

            $this->info('Please remove your registered service providers, if any.');
        } catch (Exception $e) {
            $this->error($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    public function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The service name.'],
        ];
    }
}
