<?php

namespace Lego\Console\Commands;

use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\MicroGenerator;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class InitMicroCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;
    use InitCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'init:micro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Initialize Lego Micro in current project.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $version = app()->version();
        $this->info("Initializing Lego Micro for Laravel $version\n");

        $generator = new MicroGenerator();
        $paths = $generator->generate();

        $this->comment('Created directories:');
        $this->comment(implode("\n", $paths));

        $this->welcome();

        return 0;
    }
}
