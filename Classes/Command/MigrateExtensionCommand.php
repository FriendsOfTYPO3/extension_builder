<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace EBT\ExtensionBuilder\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateExtensionCommand extends Command
{
    public function __construct(
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'extension',
            'e',
            InputOption::VALUE_OPTIONAL,
            'Extension which should be migrated'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get all given parameters
        $extension = $input->getOption('extension');

        // first check, if all given parameters are valid
        if ($extension === null || $extension === '') {
            $output->writeln('<error>No extension key is give. You must provide an extension key for migration.</error>');
            return Command::INVALID;
        }
        // Get a list of all available extensions

        // compare the list, if the given extension is available

        // if yes, then migrate the extension

        // if succeeded, then show a success message
        return Command::SUCCESS;
    }
}
