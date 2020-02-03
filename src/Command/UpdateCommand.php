<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Command;

use Kmchan\Sculpin\UpdateBundle\Propagator\PropagatorInterface;
use Sculpin\Bundle\SculpinBundle\Command\GenerateCommand;
use Sculpin\Bundle\SculpinBundle\Console\Application;
use Sculpin\Core\Io\ConsoleIo;
use Sculpin\Core\Io\IoInterface;
use Sculpin\Core\Source\ConfigFilesystemDataSource;
use Sculpin\Core\Source\DataSourceInterface;
use Sculpin\Core\Source\FilesystemDataSource;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Update command expands the original generate command. It hacks the data
 * sources such that only sources that are changed after the last build is
 * returned.
 */
class UpdateCommand extends GenerateCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        parent::configure();

        if ($this->getName() == 'generate') {
            $this->setName('update');
        } else if ($this->getName() == 'sculpin:generate') {
            $this->setName('sculpin:update');
        } else {
            // should not happen
            $this->setName('update');
        }

        $this->setDescription('Update a site from source');
        $this->setHelp("\nThe <info>update</info> command updates a site by rebuilding any files that have been changed.\n\n");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();

        /** @var PropagatorInterface $propagator */
        $propagator = $container->get('sculpin_update.propagator');

        try {
            $propagator->propagate();
        } catch (\Throwable $ex) {
            $message = $ex->getMessage();
            throw new \Exception("Cannot setup incremental build: $message");
        }

        parent::execute($input, $output);
    }
}
