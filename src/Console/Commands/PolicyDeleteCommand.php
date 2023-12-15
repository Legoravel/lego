<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class PolicyDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'delete:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Policy.';

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
        try {
            $policy = $this->parsePolicyName($this->argument('policy'));

            if (! $this->exists($path = $this->findPolicyPath($policy))) {
                $this->error('Policy class '.$policy.' cannot be found.');
            } else {
                $this->delete($path);

                $this->info('Policy class <comment>'.$policy.'</comment> deleted successfully.');
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

    /**
     * Parse the model name.
     *
     * @param string $name
     * @return string
     */
    public function parsePolicyName(string $name): string
    {
        return Str::policy($name);
    }
}
