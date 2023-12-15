<?php

namespace Lego\Console\Commands;

use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class OperationDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'delete:operation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Operation in a service';

    /**
     * The type of class being deleted.
     *
     * @var string
     */
    protected string $type = 'Operation';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(): ?bool
    {
        try {
            $service = Str::service($this->argument('service'));
            $title = $this->parseName($this->argument('operation'));

            if (! $this->exists($operation = $this->findOperationPath($service, $title))) {
                $this->error('Operation class '.$title.' cannot be found.');
            } else {
                $this->delete($operation);

                $this->info('Operation class <comment>'.$title.'</comment> deleted successfully.');
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
    protected function getArguments(): array
    {
        return [
            ['operation', InputArgument::REQUIRED, 'The operation\'s name.'],
            ['service', InputArgument::OPTIONAL, 'The service from which the operation should be deleted.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/operation.stub';
    }

    /**
     * Parse the operation name.
     *  remove the Operation.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     * @return string
     */
    protected function parseName(string $name): string
    {
        return Str::operation($name);
    }
}
