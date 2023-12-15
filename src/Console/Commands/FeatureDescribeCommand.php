<?php

namespace Lego\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Lego\Finder;
use Lego\Parser;
use Symfony\Component\Console\Input\InputArgument;

class FeatureDescribeCommand extends Command
{
    use Finder;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'describe:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'List the jobs of the specified feature in sequential order.';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(): ?bool
    {
        if ($feature = $this->findFeature($this->argument('feature'))) {
            $parser = new Parser();
            $jobs = $parser->parseFeatureJobs($feature);

            $features = [];
            foreach ($jobs as $index => $job) {
                $features[$feature->title][] = [$index + 1, $job->title, $job->domain->name, $job->relativePath];
            }

            foreach ($features as $feature => $jobs) {
                $this->comment("\n$feature\n");
                $this->table(['', 'Job', 'Domain', 'Path'], $jobs);
            }

            return true;
        }

        throw new InvalidArgumentException('Feature with name "'.$this->argument('feature').'" not found.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['feature', InputArgument::REQUIRED, 'The feature name to list the jobs of.'],
        ];
    }
}
