<?php

namespace Lego\Console\Commands;

use Lego\Console\Command;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class MigrationMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Migration class in a service';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $service = $this->argument('service');
        $migration = $this->argument('migration');

        $path = $this->findMigrationPath(Str::service($service));

        $output = shell_exec('php artisan make:migration '.$migration.' --path='.$path);

        $this->info($output);
        $this->info("\n".'Find it at <comment>'.$path.'</comment>'."\n");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['migration', InputArgument::REQUIRED, 'The migration\'s name.'],
            ['service', InputArgument::OPTIONAL, 'The service in which the migration should be generated.'],
        ];
    }
}
