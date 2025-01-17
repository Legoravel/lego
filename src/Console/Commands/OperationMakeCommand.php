<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\OperationGenerator;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class OperationMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:operation {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Operation in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Operation';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $generator = new OperationGenerator();

        $service = Str::studly($this->argument('service'));
        $title = $this->parseName($this->argument('operation'));
        $isQueueable = $this->option('queue');
        try {
            $operation = $generator->generate($title, $service, $isQueueable);

            $this->info(
                'Operation class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$operation->relativePath.'</comment>'."\n"
            );
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments(): array
    {
        return [
            ['operation', InputArgument::REQUIRED, 'The operation\'s name.'],
            ['service', InputArgument::OPTIONAL, 'The service in which the operation should be implemented.'],
            ['jobs', InputArgument::IS_ARRAY, 'A list of Jobs Operation calls'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['queue', 'Q', InputOption::VALUE_NONE, 'Whether a operation is queueable or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
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
