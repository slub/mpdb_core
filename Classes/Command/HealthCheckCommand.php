<?php

/**
 *
 */

namespace SLUB\MpdbCore\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use SLUB\MpdbCore\Lib\DbArray;
use SLUB\MpdbCore\Common\ElasticClientBuilder;
use SLUB\MpdbCore\Domain\Repository\PublishedItemRepository;
use SLUB\MpdbCore\Domain\Repository\PersonRepository;
use SLUB\MpdbCore\Domain\Repository\WorkRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * HealthCheck Command class
 *
 * @author Matthias Richter <matthias.richter@slub-dresden.de>
 * @package TYPO3
 * @subpackage mpdb_core
 * @access public
 */

class HealthCheckCommand extends Command
{

    const choiceCheckMvdbIds = 'Check MVDB IDs';
    const choiceSetWorkTitles = 'Set full work titles';
    const choiceRemoveWorkDoubles = 'Remove double works';
    const choiceRemovePersonDoubles = 'Remove double persons';
    const choiceAll = 'Perform all checks';

    protected function initialize(InputInterface $input, OutputInterface $output) {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());
    }

    /**
     * Executes the command to build indices from Database
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'What healthcheck should be executed?', 
            [
                self::choiceCheckMvdbIds,
                self::choiceSetWorkTitles,
                self::choiceRemoveWorkDoubles,
                self::choiceRemovePersonDoubles,
                self::choiceAll
            ],
            2);
        $checks = $helper->ask($input, $output, $question);

        $this->initializeRepositories();
        if ($checks == self::choiceCheckMvdbIds || $checks == self::choiceAll)
            $this->checkMvdbIds();
        if ($checks == self::choiceSetWorkTitles || $checks == self::choiceAll)
            $this->setWorkTitles();
        if ($checks == self::choiceRemoveWorkDoubles || $checks == self::choiceAll)
            $this->removeDoubleWorks();
        if ($checks == self::choiceRemovePersonDoubles || $checks == self::choiceAll)
            $this->removeDoublePersons();

        GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();
        return Command::SUCCESS;
    }

    protected function checkMvdbIds()
    {
        $this->io->section('Checking all MVDB IDs');
        $publishedItems = $this->publishedItemRepository->findAll();

        $this->io->progressStart(count($publishedItems));
        foreach($publishedItems as $publishedItem) {
            $oldMvdbId = $publishedItem->getMvdbid();
            $publishedItem->setMvdbId();
            $newMvdbId = $publishedItem->getMvdbId();
            if ($newMvdbId != $oldMvdbId) {
                $this->io->text('Changing ' . $oldMvdbId . ' to ' . $newMvdbId . '.');
            }
            $this->io->progressAdvance();
            $this->publishedItemRepository->update($publishedItem);
        }
        $this->io->progressFinish();

    }

    /**
     * Pre-Execution configuration
     *
     * @return array
     */
    protected function configure()
    {
        $this->setHelp('Check Database Consistency');
    }

    protected function setFinal()
    {
    }

    protected function setWorkTitles()
    {
        $this->io->section('Checking all work titles');
        $works = $this->workRepository->findAll();

        $this->io->progressStart(count($works));
        foreach($works as $work) {
            $this->io->progressAdvance();
            $oldTitle = $work->getFullTitle();
            $work->setFullTitle();
            $work->setPublishers();
            $newTitle = $work->getFullTitle();
            if ($newTitle != $oldTitle) {
                $this->io->text('Changing ' . $oldTitle . ' to ' . $newTitle . ' in ' . $work->getGndId() . '.');
            }
            $this->io->progressAdvance();
            $this->workRepository->update($work);
        }
        $this->io->progressFinish();
    }

    protected function setInstrumentationNames()
    {
    }

    protected function removeDoubleWorks()
    {
        $this->io->section('Removing double works');
        $publishedItems = $this->publishedItemRepository->findAll();
        $this->io->progressStart(count($publishedItems));
        foreach ($publishedItems as $publishedItem) {
            $this->io->progressAdvance();
            foreach ($publishedItem->getContainedWorks() as $work) {
                if (
                    isset($works[$work->getGndId()]) &&
                    $works[$work->getGndId()]->getUid() != $work->getUid() &&
                    $work->getGndId() != 'lokal'
                ) {
                    $publishedItem->removeContainedWork($work);
                    $publishedItem->addContainedWork($works[$work->getGndId()]);
                    $this->io->text('detected double ' . $work->getGndId() . ', ' . $work->getFullTitle() . '.');
                } else {
                    $works[$work->getGndId()] = $work;
                }
            }
            foreach ($publishedItem->getPublishedSubitems() as $publishedSubitem) {
                foreach ($publishedSubitem->getContainedWorks() as $work) {
                    if (isset($works[$work->getGndId()]) && $works[$work->getGndId()]->getUid() != $work->getUid()) {
                        $publishedSubitem->removeContainedWork($work);
                        $publishedSubitem->addContainedWork($works[$work->getGndId()]);
                        $this->io->text('detected double ' . $work->getGndId() . ', ' . $work->getFullTitle() . '.');
                    } else {
                        $works[$work->getGndId()] = $work;
                    }
                }
            }
            $this->publishedItemRepository->update($publishedItem);
        }
        $this->io->progressFinish();
        $this->removeUnusedWorks();
    }

    protected function removeDoublePersons()
    {
        $this->io->section('Removing double persons');
        $works = $this->workRepository->findAll();

        $this->io->text('Checking work composers');
        $this->io->progressStart(count($works));
        foreach ($works as $work) {
            $this->io->progressAdvance();
            $person = $work->getFirstcomposer();
            if ($person) {
                if (isset($persons[$person->getGndId()]) &&
                    $persons[$person->getGndId()]->getUid() != $person->getUid() &&
                    $person->getGndId() != 'lokal'
                ) {
                    $work->setFirstcomposer($persons[$person->getGndId()]);
                    $this->workRepository->update($work);
                    $this->io->text('detected double ' . $person->getGndId() . ', ' . $person->getName() . '.');
                } else {
                    $persons[$person->getGndId()] = $person;
                }
            }
        }
        $this->io->progressFinish();

        $publishedItems = $this->publishedItemRepository->findAll();
        $this->io->text('Checking editors and collection composers');
        $this->io->progressStart(count($publishedItems));
        foreach ($publishedItems as $publishedItem) {
            $this->io->progressAdvance();
            foreach ($publishedItem->getEditors() as $person) {
                if (isset($persons[$person->getGndId()]) &&
                    $persons[$person->getGndId()]->getUid() != $person->getUid() &&
                    $person->getGndId() != 'lokal'
                ) {
                    $publishedItem->removeEditor($person);
                    $publishedItem->addEditor($persons[$person->getGndId()]);
                    $this->publishedItemRepository->update($publishedItem);
                    $this->io->text('detected double ' . $person->getGndId() . ', ' . $person->getName() . '.');
                } else {
                    $persons[$person->getGndId()] = $person;
                }
            }
            if ($publishedItem->getFirstComposer()) {
                foreach ($publishedItem->getFirstComposer() as $person) {
                    if (isset($persons[$person->getGndId()]) &&
                        $persons[$person->getGndId()]->getUid() != $person->getUid() &&
                        $person->getGndId() != 'lokal'
                    ) {
                        $publishedItem->removeFirstComposer($person);
                        $publishedItem->addFirstComposer($persons[$person->getGndId()]);
                        $this->publishedItemRepository->update($publishedItem);
                        $this->io->text('detected double ' . $person->getGndId() . ', ' . $person->getName() . '.');
                    } else {
                        $persons[$person->getGndId()] = $person;
                    }
                }
            }
        }
        $this->io->progressFinish();
        $this->removeUnusedPersons();
    }

    protected function removeDoublePlaces()
    {
    }

    protected function removeUnusedWorks()
    {
        $this->io->section('Removing unused works');
        $publishedItems = $this->publishedItemRepository->findAll();
        foreach ($publishedItems as $publishedItem) {
            foreach ($publishedItem->getContainedWorks() as $work) {
                $works[$work->getUid()] = $work;
            }
            foreach ($publishedItem->getPublishedSubitems() as $publishedSubitem) {
                foreach ($publishedSubitem->getContainedWorks() as $work) {
                    $works[$work->getUid()] = $work;
                }
            }
        }
        $worksFromDb = $this->workRepository->findAll();
        $this->io->progressStart(count($works));
        foreach ($worksFromDb as $work) {
            $this->io->progressAdvance();
            if (!isset($works[$work->getUid()])) {
                $this->workRepository->remove($work);
            }
        }
        $this->io->progressFinish();
    }

    protected function removeUnusedPersons()
    {
        $this->io->section('Removing unused persons');
        $works = $this->workRepository->findAll();

        $this->io->progressStart(count($works));
        $this->io->text('Collecting persons used by works');
        foreach ($works as $work) {
            $this->io->progressAdvance();
            $person = $work->getFirstcomposer();
            if ($person) $persons[$person->getUid()] = 1;
        }
        $this->io->progressFinish();

        $publishedItems = $this->publishedItemRepository->findAll();
        $this->io->text('Collecting persons used by published items');
        $this->io->progressStart(count($publishedItems));
        foreach ($publishedItems as $publishedItem) {
            $this->io->progressAdvance();
            if ($publishedItem->getEditors()) {
                foreach ($publishedItem->getEditors() as $person) {
                    $persons[$person->getUid()] = 1;
                }
            }
            if ($publishedItem->getFirstComposer()) {
                foreach ($publishedItem->getFirstComposer() as $person) {
                    $persons[$person->getUid()] = 1;
                }
            }
        }
        $this->io->progressFinish();
        $personsFromDb = $this->personRepository->findAll();
        $this->io->progressStart(count($persons));
        $this->io->text('removing unused persons');
        foreach ($personsFromDb as $person) {
            $this->io->progressAdvance();
            if (!isset($persons[$person->getUid()])) {
                $this->personRepository->remove($person);
                $this->io->text('removing ' . $person->getUid() . ', ' . $person->getName());
            }
        }
        $this->io->progressFinish();
    }

    protected function removeUnusedPlaces()
    {
    }

    /**
     * Initialize the extbase repository based on the given storagePid.
     *
     * TYPO3 10+: Find a better solution e.g. based on Symfony Dependency Injection.
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
        $this->publishedItemRepository = $objectManager->get(publishedItemRepository::class);
        $this->personRepository = $objectManager->get(PersonRepository::class);
    }
}
