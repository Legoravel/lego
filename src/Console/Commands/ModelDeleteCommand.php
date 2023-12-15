<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class ModelDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'delete:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Eloquent Model.';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected string $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(): ?bool
    {
        try {
            $model = $this->parseModelName($this->argument('model'));

            if (! $this->exists($path = $this->findModelPath($model))) {
                $this->error('Model class '.$model.' cannot be found.');
            } else {
                $this->delete($path);

                $this->info('Model class <comment>'.$model.'</comment> deleted successfully.');
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

    /**
     * Parse the model name.
     *
     * @param string $name
     * @return string
     */
    public function parseModelName(string $name): string
    {
        return Str::model($name);
    }
}
