<?php

namespace App\Contributors\Command;

use App\Contributors\Service\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand.
 */
class UpdateCommand extends Command
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;

        // you *must* call the parent constructor
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:contributors:update')
            // the short description shown while running "php bin/console list"
            ->setDescription('Update contributors.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command update contributors of badge poser...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Update Contributors',
            '===================',
            '',
        ]);

        $count = $this->repository->updateCache();

        // outputs a message followed by a "\n"
        $output->writeln('Whoa!');

        // outputs a message without adding a "\n" at the end of the line
        $output->write('We have '.$count.' contributors!!!');

        return 0;
    }
}
