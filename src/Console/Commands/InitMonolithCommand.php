<?php

namespace Lego\Console\Commands;

use Illuminate\Support\Composer;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\MonolithGenerator;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

class InitMonolithCommand extends SymfonyCommand
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
    protected string $name = 'init:monolith';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Initialize Lego Monolith in current project.';

    /**
     * The Composer class instance.
     *
     * @var Composer
     */
    protected Composer $composer;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $version = app()->version();
        $this->info("Initializing Lego Monolith for Laravel $version\n");

        $service = $this->argument('service');

        $directories = (new MonolithGenerator())->generate();
        $this->comment('Created directories:');
        $this->comment(implode("\n", $directories));

        // create service
        if ($service) {
            $this->getApplication()
                ->find('make:service')
                ->run(new ArrayInput(['name' => $service]), $this->output);

            $this->ask('Once done, press Enter/Return to continue...');
        }

        $this->welcome($service);

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['service', InputArgument::OPTIONAL, 'Your first service.'],
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
        ];
    }
}
