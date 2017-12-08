<?php

namespace App\Command;

use Exception;
use Odan\Migration\Command\GenerateCommand;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command
 */
class PhinxCommand extends AbstractCommand
{

    /**
     * Configure
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('phinx');
        $this->setDescription('Phinx migrations');

        $this->addArgument('arguments', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, "Required Placeholders for route");
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int integer 0 on success, or an error code.
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $argv;
        $argv = array_slice($argv, 1);

        // The path with the phinx.php file
        chdir(__DIR__ . '/../../config');

        // Start console
        $argInput = new ArgvInput($argv);

        $application = new PhinxApplication();
        $application->add(new GenerateCommand());
        return $application->run($argInput);
    }
}
