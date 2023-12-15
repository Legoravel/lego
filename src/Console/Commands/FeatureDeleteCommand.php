<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class FeatureDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'delete:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Feature in a service';

    /**
     * The type of class being deleted.
     *
     * @var string
     */
    protected string $type = 'Feature';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $service = Str::service($this->argument('service'));
            $title = $this->parseName($this->argument('feature'));

            if (! $this->exists($feature = $this->findFeaturePath($service, $title))) {
                $this->error('Feature class '.$title.' cannot be found.');
            } else {
                $this->delete($feature);

                $this->info('Feature class <comment>'.$title.'</comment> deleted successfully.');
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
            ['feature', InputArgument::REQUIRED, 'The feature\'s name.'],
            ['service', InputArgument::OPTIONAL, 'The service from which the feature should be deleted.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/feature.stub';
    }

    /**
     * Parse the feature name.
     *  remove the Feature.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     * @return string
     */
    protected function parseName(string $name): string
    {
        return Str::feature($name);
    }
}
