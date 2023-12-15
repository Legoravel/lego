<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class RequestDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'delete:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Request.';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected string $type = 'Request';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $request = $this->parseRequestName($this->argument('request'));
            $service = Str::service($this->argument('service'));

            if (! $this->exists($path = $this->findRequestPath($service, $request))) {
                $this->error('Request class '.$request.' cannot be found.');
            } else {
                $this->delete($path);

                $this->info('Request class <comment>'.$request.'</comment> deleted successfully.');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return [
            ['request', InputArgument::REQUIRED, 'The Request\'s name.'],
            ['service', InputArgument::REQUIRED, 'The Service\'s name.'],
        ];
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

    /**
     * Parse the model name.
     *
     * @param string $name
     * @return string
     */
    public function parseRequestName(string $name): string
    {
        return Str::request($name);
    }
}
