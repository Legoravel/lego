<?php

namespace Lego\Console\Commands;

use Lego\Console\Command;
use Lego\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ServicesListCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'list:services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List the services in this project.';

    public function handle(): void
    {
        $services = $this->listServices()->all();

        $this->table(['Service', 'Slug', 'Path'], array_map(function ($service) {
            return [$service->name, $service->slug, $service->relativePath];
        }, $services));
    }
}
