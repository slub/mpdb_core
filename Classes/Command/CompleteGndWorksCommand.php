<?php

/**
 *
 */

namespace SLUB\MpdbCore\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use SLUB\DMNorm\Domain\Repository\InstrumentRepository;
use SLUB\DMNorm\Domain\Repository\FormRepository;
use SLUB\MpdbCore\Lib\DbArray;
use SLUB\MpdbCore\Domain\Repository\PersonRepository;
use SLUB\MpdbCore\Domain\Repository\WorkRepository;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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

    /**
     * formRepository
     * 
     * @var FormRepository
     */
    protected $formRepository = null;

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());
        $this->initializeRepositories();
    }

    /**
     * Executes the command to build indices from Database
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $tmpConfiguration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'publisherDb'
        );
        $configurationManager->setConfiguration($tmpConfiguration);
        $om = GeneralUtility::makeInstance(ObjectManager::class);
        $workRepository = $om->get(WorkRepository::class);
         */

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
            $work->getGndInfo(
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

    protected function configure()
    {
        $this->setHelp('Fetch GND data for new works (works without title).');
        $this->setDescription('Fetching GND data for new works (works without title).');
    }

    /**
     * Initialize the extbase repository based on the given storagePid.
     *
     * TYPO3 10+: Find a better solution e.g. based on Symfonie Dependency Injection.
     *
     * @param int $storagePid The storage pid
     *
     * @return bool
     */
    protected function initializeRepositories()
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $frameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $frameworkConfiguration['persistence']['storagePid'] = 0;
        $configurationManager->setConfiguration($frameworkConfiguration);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->workRepository = $objectManager->get(WorkRepository::class);
        $this->personRepository = $objectManager->get(PersonRepository::class);
        $this->instrumentRepository = $objectManager->get(InstrumentRepository::class);
        $this->formRepository = $objectManager->get(FormRepository::class);
        $this->extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('publisher_db');
    }

}
