<?php

namespace Lego\Console;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

trait Command
{
    /**
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName($this->name)
            ->setDescription($this->description);

        foreach ($this->getArguments() as $arguments) {
            call_user_func_array([$this, 'addArgument'], $arguments);
        }

        foreach ($this->getOptions() as $options) {
            call_user_func_array([$this, 'addOption'], $options);
        }
    }

    /**
     * Default implementation to get the arguments of this command.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return [];
    }

    /**
     * Default implementation to get the options of this command.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [];
    }

    /**
     * Execute the command.
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        return (int) $this->handle();
    }

    /**
     * Get an argument from the input.
     *
     * @param  string  $key
     * @return string
     */
    public function argument(string $key): string
    {
        return $this->input->getArgument($key);
    }

    /**
     * Get an option from the input.
     *
     * @param  string  $key
     * @return string
     */
    public function option(string $key): string
    {
        return $this->input->getOption($key);
    }

    /**
     * Write a string as information output.
     *
     * @param  string  $string
     */
    public function info(string $string): void
    {
        $this->output->writeln("<info>$string</info>");
    }

    /**
     * Write a string as comment output.
     *
     * @param  string  $string
     * @return void
     */
    public function comment(string $string): void
    {
        $this->output->writeln("<comment>$string</comment>");
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @return void
     */
    public function error(string $string): void
    {
        $this->output->writeln("<error>$string</error>");
    }

    /**
     * Format input to textual table.
     *
     * @param  array  $headers
     * @param  array|Arrayable  $rows
     * @param  string  $style
     * @return void
     */
    public function table(array $headers, array|Arrayable $rows, string $style = 'default'): void
    {
        $table = new Table($this->output);

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        $table->setHeaders($headers)->setRows($rows)->setStyle($style)->render();
    }

    /**
     * Ask the user the given question.
     *
     * @param  string  $question
     * @param  bool  $default
     * @return string
     */
    public function ask(string $question, $default = false): string
    {
        $question = '<comment>'.$question.'</comment> ';

        $confirmation = new ConfirmationQuestion($question, false);

        return $this->getHelperSet()?->get('question')->ask($this->input, $this->output, $confirmation);
    }

    /**
     * Ask the user the given secret question.
     *
     * @param  string  $question
     * @return string
     */
    public function secret(string $question): string
    {
        $question = '<comment>'.$question.'</comment> ';

        return $this->getHelperSet()?->get('dialog')->askHiddenResponse($this->output, $question, false);
    }
}
