<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\ModelGenerator;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class ModelMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Eloquent Model.';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected string $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $generator = new ModelGenerator();

        $name = $this->argument('model');

        try {
            $model = $generator->generate($name);

            $this->info('Model class created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$model->relativePath.'</comment>'."\n"
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
            ['model', InputArgument::REQUIRED, 'The Model\'s name.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/model.stub';
    }
}
