<?php

namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
class UpdateAssetsCommand extends AbstractCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('update-assets');
        $this->setDescription('Update all assets');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return int integer 0 on success, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Updating assets...');

        $settings = $this->container->get('settings');
        $public = $settings['public'];

        $files = [];

        // Bootstrap
        $files[] = [$public . '/css/bootstrap.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.css'];
        $files[] = [$public . '/css/bootstrap.min.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'];
        $files[] = [$public . '/js/bootstrap.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.js'];
        $files[] = [$public . '/js/bootstrap.min.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'];

        // jQuery
        $files[] = [$public . '/js/jquery.js', 'https://code.jquery.com/jquery-3.2.1.js'];
        $files[] = [$public . '/js/jquery.min.js', 'https://code.jquery.com/jquery-3.2.1.min.js'];
        $files[] = [$public . '/js/jquery.min.map', 'https://code.jquery.com/jquery-3.2.1.min.map'];

        // mustache.js
        $files[] = [$public . '/js/mustache.js', 'https://raw.githubusercontent.com/janl/mustache.js/v2.3.0/mustache.js'];
        $files[] = [$public . '/js/mustache.min.js', 'https://raw.githubusercontent.com/janl/mustache.js/v2.3.0/mustache.min.js'];

        // Utils
        //$files[] = [$public . '/js/sprintf.min.js', 'https://raw.githubusercontent.com/alexei/sprintf.js/master/dist/sprintf.min.js'];

        foreach ($files as $file) {
            $output->writeln(basename($file[1]));
            file_put_contents($file[0], file_get_contents($file[1]));
        }

        $output->writeln('<info>Done</info>');

        return 0;
    }
}
