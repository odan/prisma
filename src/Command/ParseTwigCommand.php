<?php

namespace App\Command;

use Exception;
use Odan\Twig\TwigCompiler;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Views\Twig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command
 */
class ParseTwigCommand extends AbstractCommand
{

    /**
     * Configure
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('parse-twig');
        $this->setDescription('Parse twig templates');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int integer 0 on success, or an error code.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var Twig $twigView */
        $twigView = $this->container->get(Twig::class);

        $settings = $this->container->get('settings');
        $cachePath = $settings['twig']['cache_path'];

        // Get the Twig Environment instance from the Twig View instance
        $twig = $twigView->getEnvironment();
        $twig->setCache($cachePath);

        // Compile all Twig templates into cache directory
        $compiler = new TwigCompiler($twig, $cachePath);
        $compiler->compile();

        $output->write('Done');

        return 0;
    }
}
