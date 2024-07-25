<?php

/**
 *
 */

namespace Slub\MpdbCore\Command;

use Slub\DmNorm\Domain\Repository\GndInstrumentRepository;
use Slub\DmNorm\Domain\Repository\GndGenreRepository;
use Slub\DmNorm\Domain\Repository\GndPersonRepository;
use Slub\DmNorm\Domain\Repository\GndWorkRepository;
use Slub\MpdbCore\Lib\DbArray;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * CompleteGndWorks Command class
 *
 * @author Matthias Richter <matthias.richter@slub-dresden.de>
 * @package TYPO3
 * @subpackage mpdb_core
 * @access public
 */

class CompleteGndWorksCommand extends Command
{

    /**
     * workRepository
     * 
     * @var WorkRepository
     */
    protected $workRepository = null;

    /**
     * personRepository
     * 
     * @var PersonRepository
     */
    protected $personRepository = null;

    /**
     * instrumentRepository
     * 
     * @var InstrumentRepository
     */
    protected $instrumentRepository = null;

    protected ?SymfonyStyle $io = null;

    protected array $extConf = [];

    /**
     * formRepository
     * 
     * @var FormRepository
     */
    protected $formRepository = null;

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());
        $this->initializeRepositories($input);
    }

    /**
     * Executes the command to build indices from Database
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countWorks = $this->workRepository->findAll()->count();
        $nonTitleWorks = $this->workRepository->findByTitle('');
        $countNonTitleWorks = $nonTitleWorks->count();
        $this->io->text('Found ' . $countWorks . ' works and ' . $countNonTitleWorks . ' works without title.');
        $this->io->text('Found ' . $countWorks . ' works and ' . $countNonTitleWorks . ' works without title.');

        $count = 0;
        $workCount = count($nonTitleWorks);
        foreach ($nonTitleWorks as $work) {
            $text = ++$count . '/' . $workCount;
            $text .= ' Fetching ' . $work->getGndId();
            $this->io->text($text);
            $work->pullGndInfo(
                $this->workRepository,
                $this->personRepository,
                $this->instrumentRepository, 
                $this->formRepository
            );
            $this->workRepository->update($work);
        }

        GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('Fetch GND data for new works (works without title).');
        $this->setDescription('Fetching GND data for new works (works without title).');

        $this->addArgument('storagePid', InputArgument::REQUIRED, 'Storage pid to retrieve works from.');
    }

    /**
     * Initialize the extbase repository based on the given storagePid.
     *
     * TYPO3 10+: Find a better solution e.g. based on Symfonie Dependency Injection.
     *
     * @param int $storagePid The storage pid
     *
     * @return void
     */
    protected function initializeRepositories(InputInterface $input): void
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $frameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $frameworkConfiguration['persistence']['storagePid'] = $input->getArgument('storagePid');
        $configurationManager->setConfiguration($frameworkConfiguration);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->workRepository = $objectManager->get(GndWorkRepository::class);
        $this->personRepository = $objectManager->get(GndPersonRepository::class);
        $this->instrumentRepository = $objectManager->get(GndInstrumentRepository::class);
        $this->formRepository = $objectManager->get(GndGenreRepository::class);
        $this->extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mpdb_core');
    }

}
