<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\FeatureGenerator;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class FeatureMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Feature in a service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Feature';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $service = Str::studly($this->argument('service'));
            $title = $this->parseName($this->argument('feature'));

            $generator = new FeatureGenerator();
            $feature = $generator->generate($title, $service);

            $this->info(
                'Feature class '.$feature->title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$feature->relativePath.'</comment>'."\n"
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
    protected function getArguments(): array
    {
        return [
            ['feature', InputArgument::REQUIRED, 'The feature\'s name.'],
            ['service', InputArgument::OPTIONAL, 'The service in which the feature should be implemented.'],
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
