<?php

namespace Lego\Console\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     */
    protected function configure(): void
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Lego-architected project')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('laravel', null, InputOption::VALUE_NONE, 'Specify the Laravel version you wish to install');
    }

    /**
     * Execute the command.
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->verifyApplicationDoesntExist(
            $directory = ($input->getArgument('name')) ? getcwd().'/'.$input->getArgument('name') : getcwd(),
            $output
        );

        $output->writeln('<info>Crafting Lego application...</info>');

        /*
         * @TODO: Get Lego based on the Laravel version.
         */
        $process = new Process([$this->findComposer() , ' create-project laravel/laravel ', $directory]);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $output->writeln('<comment>Application ready! Make your dream a reality.</comment>');
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param string $directory
     */
    protected function verifyApplicationDoesntExist(string $directory, OutputInterface $output): void
    {
        if ($directory !== getcwd() && (is_dir($directory) || is_file($directory))) {
            throw new RuntimeException('Application already exists!');
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer(): string
    {
        $composer = 'composer';

        if (file_exists(getcwd().'/composer.phar')) {
            $composer = '"'.PHP_BINARY.'" composer.phar';
        }

        return $composer;
    }
}
