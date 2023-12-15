<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\ControllerGenerator;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new resource Controller class in a service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Controller';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $generator = new ControllerGenerator();

        $service = $this->argument('service');
        $name = $this->argument('controller');

        try {
            $controller = $generator->generate($name, $service, $this->option('resource'));

            $this->info('Controller class created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$controller.'</comment>'."\n"
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
    protected function getArguments(): array
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The controller\'s name.'],
            ['service', InputArgument::OPTIONAL, 'The service in which the controller should be generated.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['resource', null, InputOption::VALUE_NONE, 'Generate a resource controller class.'],
        ];
    }

    /**
     * Parse the feature name.
     *  remove the Controller.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     * @return string
     */
    protected function parseName(string $name): string
    {
        return Str::studly(preg_replace('/Controller(\.php)?$/', '', $name).'Controller');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('plain')) {
            return __DIR__.'/../Generators/stubs/controller.plain.stub';
        }

        return __DIR__.'/../Generators/stubs/controller.resource.stub';
    }
}
