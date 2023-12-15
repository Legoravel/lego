<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Finder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class FeaturesListCommand extends SymfonyCommand
{
    use Finder;
    use Command;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'list:features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List the features.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        foreach ($this->listFeatures($this->argument('service')) as $service => $features) {
            $this->comment("\n$service\n");
            $features = array_map(static function ($feature) {
                return [$feature->title, $feature->service->name, $feature->file, $feature->relativePath];
            }, $features->all());
            $this->table(['Feature', 'Service', 'File', 'Path'], $features);
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
            ['service', InputArgument::OPTIONAL, 'The service to list the features of.'],
        ];
    }
}
