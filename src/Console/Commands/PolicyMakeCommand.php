<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\PolicyGenerator;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class PolicyMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a Policy.';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected string $type = 'Policy';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $generator = new PolicyGenerator();

        $name = $this->argument('policy');

        try {
            $policy = $generator->generate($name);

            $this->info('Policy class created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$policy->relativePath.'</comment>'."\n"
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
            ['policy', InputArgument::REQUIRED, 'The Policy\'s name.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/policy.stub';
    }
}
