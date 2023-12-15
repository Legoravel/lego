<?php

namespace Lego\Console\Commands;

use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;

class JobDeleteCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'delete:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Delete an existing Job in a domain';

    /**
     * The type of class being deleted.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(): ?bool
    {
        try {
            $domain = Str::studly($this->argument('domain'));
            $title = $this->parseName($this->argument('job'));

            if (!$this->exists($job = $this->findJobPath($domain, $title))) {
                $this->error('Job class ' . $title . ' cannot be found.');
            } else {
                $this->delete($job);

                if (count($this->listJobs($domain)->first()) === 0) {
                    $this->delete($this->findDomainPath($domain));
                }

                $this->info('Job class <comment>' . $title . '</comment> deleted successfully.');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     * @return string
     */
    protected function parseName($name): string
    {
        return Str::job($name);
    }

    public function getArguments()
    {
        return [
            ['job', InputArgument::REQUIRED, 'The job\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain from which the job will be deleted.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__ . '/../Generators/stubs/job.stub';
    }
}
