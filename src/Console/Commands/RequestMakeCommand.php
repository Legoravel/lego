<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\RequestGenerator;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class RequestMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a Request in a domain.';

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
        $generator = new RequestGenerator();

        $name = $this->argument('name');
        $service = $this->argument('domain');

        try {
            $request = $generator->generate($name, $service);

            $this->info('Request class created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$request->relativePath.'</comment>'."\n"
            );
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
            ['name', InputArgument::REQUIRED, 'The name of the class.'],
            ['domain', InputArgument::REQUIRED, 'The Domain in which this request should be generated.'],
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
}
