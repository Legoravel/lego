<?php

namespace Lego\Console\Commands;

use Exception;
use Lego\Console\Command;
use Lego\Filesystem;
use Lego\Finder;
use Lego\Generators\JobGenerator;
use Lego\Str;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class JobMakeCommand extends SymfonyCommand
{
    use Finder;
    use Command;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected string $name = 'make:job {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Job in a domain';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Job';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $generator = new JobGenerator();

        $domain = Str::studly($this->argument('domain'));
        $title = $this->parseName($this->argument('job'));
        $isQueueable = $this->option('queue');
        try {
            $job = $generator->generate($title, $domain, $isQueueable);

            $this->info(
                'Job class '.$title.' created successfully.'.
                "\n".
                "\n".
                'Find it at <comment>'.$job->relativePath.'</comment>'."\n"
            );
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments(): array
    {
        return [
            ['job', InputArgument::REQUIRED, 'The job\'s name.'],
            ['domain', InputArgument::REQUIRED, 'The domain to be responsible for the job.'],
        ];
    }

    public function getOptions(): array
    {
        return [
            ['queue', 'Q', InputOption::VALUE_NONE, 'Whether a job is queueable or not.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub(): string
    {
        return __DIR__.'/../Generators/stubs/job.stub';
    }

    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     * @return string
     */
    protected function parseName(string $name): string
    {
        return Str::job($name);
    }
}
