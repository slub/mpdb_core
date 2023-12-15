<?php

namespace Slub\MpdbPresentation\Controller;

use \TYPO3\CMS\Core\Http\ApplicationType;
use \TYPO3\CMS\Core\Messaging\AbstractMessage;
use \TYPO3\CMS\Core\Pagination\SimplePagination;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use \TYPO3\CMS\Extbase\Persistence\QueryInterface;
use \Slub\DmNorm\Domain\Model\Person;
use \Slub\MpdbCore\Domain\Model\Publisher;
use \Slub\MpdbCore\Domain\Model\PublisherAction;
use \Slub\MpdbCore\Domain\Model\PublishedItem;
use \Slub\MpdbCore\Domain\Model\PublishedSubitem;
use \Slub\MpdbCore\Domain\Model\Work;
use \Slub\MpdbCore\Lib\DbArray;
use \Slub\MpdbCore\Lib\Tools;

/***
 *
 * This file is part of the "Publisher Database" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 Matthias Richter <matthias.richter@slub-dresden.de>, SLUB Dresden
 *
 ***/
/**
 * PublishedItemController
 */
class PublishedItemController extends AbstractController
{

    const QUICK_PAGINATOR = [ -200, -100, -50, -20, -10, -5, -2, -1, 0, 1, 2, 5, 10, 20, 50, 100, 200 ];
    const TABLE_TARGET = 'published_item_table';
    const DASHBOARD_TARGET = 'published_item_dashboard';
    const GRAPH_TARGET = 'published_item_graph';

    /**
     * action list
     * 
     * @param Publisher $publisher
     * @param string $sortString
     * @param string $desc
     * @param string $searchTerm
     * @param int $final
     * @param int $from
     * @return void
     */
    public function listAction(
        Publisher $publisher = null, 
        string $sortString = '', 
        string $desc = '', 
        string $searchTerm = '',
        int $final = -3,
        int $from = 1,
        int $itemsPerPage = 50
    )
    {
        $publishers = $this->publisherRepository->findAll();
        $desc = false;

        $publisherMakroItems = $this->publisherMakroItemRepository->
            dbListFe($publisher, $sortString, $desc, $final);

        $publisherMakroItemPaginator = new QueryResultPaginator($publisherMakroItems, $from, $itemsPerPage);
        $publisherMakroItemPagination = new SimplePagination($publisherMakroItemPaginator);

        $quickpager = $this->makeQuickpager($from, $publisherMakroItemPagination);

        $this->view->assign('currentPublisher', $publisher);
        $this->view->assign('publishers', $publishers);
        $this->view->assign('publisherMakroItems', $publisherMakroItemPaginator);
        $this->view->assign('publisherMakroItemPagination', range(1, $publisherMakroItemPagination->getLastPageNumber()));
        $this->view->assign('quickpager', $quickpager);
        $this->view->assign('currentPage', $from);
        $this->view->assign('currentFinal', $final);
        $this->view->assign('sortString', $sortString);
        $this->view->assign('currentPage', $from);
    }

    private function makeQuickpager(int $current, SimplePagination $pagination): array
    {
        $pages = [];

        foreach (self::QUICK_PAGINATOR as $page) {
            $index = $current + $page;
            if ($index >= $pagination->getFirstPageNumber() && $index <= $pagination->getLastPageNumber()) {
                if ($page == -1) {
                    $pages[$index] = 'letzte Seite';
                } else if ($page == 1) {
                    $pages[$index] = 'nächste Seite';
                } else {
                    $pages[$index] = $index;
                }
            }
        }

        return $pages;
    }

    private function search($level, $searchTerm, $publisher)
    {
        $umlauts = ['ae', 'oe', 'ue', 'ss', 'ä', 'ö', 'ü', 'é'];
        $purgedTerm = str_replace($umlauts, ' ', $searchTerm);
        $makrosFromPersons = (new DbArray(explode(' ', $purgedTerm)))->
            filter(function ($term) { return strlen($term) != 1; })->
            map( function ($term) use ($level) { return $this->personRepository->search($term, $level)->toArray(); } )->
            merge()->
            unique()->
            map( function ($person) { return $person->getWorks()->toArray(); } )->
            merge()->
            map( function ($work) { return $work->getPublisherMakroItems()->toArray(); } )->
            merge()->
            filter( function ($makro) { return $makro->getFinal() >= $level; } );
        if ($publisher) {
            $makrosFromPersons = $makrosFromPersons
                ->filter( function ($makro) use ($publisher) { 
                    return $makro->getPublisher() == $publisher; } );
        }
        return (new DbArray())->
            set(explode(' ', $purgedTerm))->
            filter(function ($term) { return strlen($term) != 1; })->
            map(function ($term) use ($level, $publisher) { return $this->publisherMakroItemRepository->dbSearchFe($level, $term, $publisher)->toArray(); })->
            merge()->
            concat($makrosFromPersons->toArray())->
            group(
                function ($item) { return $item; }, 
                function ($item) { return $item->getMvdbId(); }
            )->
            map(
                function ($groupedItem) use($searchTerm) {
                    $baseRelevance = 0;
                    if ($groupedItem['groupObject']->getTitle() === $searchTerm) {
                        $baseRelevance = 1;
                    }
                    return ['item' => $groupedItem['groupObject'], 'relevance' => count($groupedItem['group'])];
                }
            )->
            sort(function ($a, $b) { return $a['relevance'] - $b['relevance']; })->
            map(function ($groupedItem) { return $groupedItem['item']; })->
            toArray();
    }

    /**
     * action show
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @return void
     */
    public function showAction(PublishedItem $publishedItem)
    {
        $sortByDate = function (PublisherAction $a, PublisherAction $b) {
            return $a->getDateOfAction() < $b->getDateOfAction() ?
                -1 : ( $a->getDateOfAction() == $b->getDateOfAction() ? 0 : 1 );
        };
        $publisherMikroItems = $publishedItem->getPublishedSubitems()->toArray();
        $publisherActions = [];
        // use dbarray!
        foreach ($publisherMikroItems as $publisherMikroItem) {
            $publisherActions = array_merge(
            $publisherActions, 
            $this->publisherActionRepository->findByPublisherMikroItem($publisherMikroItem)->toArray()
            );
        }
        usort($publisherActions, $sortByDate);

        $document = $this->elasticClient->get([
            'index' => PublishedItem::TABLE_INDEX_NAME,
            'id' => $publishedItem->getMvdbId()
        ]);
        $jsonDocument = json_encode($document['_source']);

        $visualizationCall = $this->getJsCall($jsonDocument);
        //var_dump($visualizationCall);die;
        $publishers = $this->publisherRepository->findAll();
        $this->view->assign('publishedItem', $publishedItem);
        $this->view->assign('publisherMikroItems', $publisherMikroItems);
        $this->view->assign('publisherActions', $publisherActions);
        $this->view->assign('visualizationCall', $visualizationCall);
        $this->view->assign('tableTarget', self::TABLE_TARGET);
        $this->view->assign('graphTarget', self::GRAPH_TARGET);
        $this->view->assign('dashboardTarget', self::DASHBOARD_TARGET);
    }

    protected function getJsCall(string $data): string
    {
        $movingAverages = explode(',', $this->extConf['movingAverages']);
        $config = [
            'movingAverages' => $movingAverages,
            'tableTarget' => self::TABLE_TARGET,
            'graphTarget' => self::GRAPH_TARGET,
            'dashboardTarget' => self::DASHBOARD_TARGET
        ];
        
        return self::scriptWrap('document.addEventListener("DOMContentLoaded", _ => {' .
            'tx_publisherdb_visualizationController.data = ' . $data . ';' .
            'tx_publisherdb_visualizationController.config = ' . json_encode($config) . ';' .
            '})');
    }

    protected static function scriptWrap(string $call): string
    {
        return '<script>' . $call . '</script>';
    }

    /**
     * action lookup
     * 
     * @param string $searchString
     * @return void
     */
    public function lookupAction(string $searchString)
    {
        $delim = substr_count($searchString, '_') ? '_' : ',';
        $query = explode($delim, $searchString);
        if (isset($query[1]))
            $plate = trim($query[1]);
        $publisherShorthand = trim($query[0]);
        $publisher = $this->publisherRepository->findOneByShorthand($publisherShorthand);

        if ($publisher && $plate) {
            $publisherMakroItem = $this->publisherMakroItemRepository->lookupPlateId($plate, $publisher, $this->level);
            if ($publisherMakroItem) {
                foreach ($publisherMakroItem->getPublisherMikroItems() as $publisherMikroItem) {
                    if ($publisherMikroItem->getPlateId() == $plate) break;
                }
                if (TYPO3_MODE === 'BE')
                    $this->redirect('edit', null, null, ['publisherMakroItem' => $publisherMakroItem, 'activeMikro' => $publisherMikroItem]);
                else
                    $this->redirect('show', null, null, ['publisherMakroItem' => $publisherMakroItem]);
            } else {
                $this->addFlashMessage('Verlagsartikel nicht gefunden', 'Fehler', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                $this->redirect('list');
            }
        }
        $this->redirect('search', null, null, ['term' => $searchString]);
    }

    /**
     * action getNext
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @return void
     */
    public function getNextAction(PublishedItem $publisherMakroItem)
    {
        $next = $this->publisherMakroItemRepository->findNext($publisherMakroItem);
        if ($next) {
            $this->redirect('edit', null, null, ['publisherMakroItem' => $next]);
        } else {
            $this->redirect('list');
        }
    }

    /**
     * action getPrevious
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @return void
     */
    public function getPreviousAction(PublishedItem $publisherMakroItem)
    {
        $previous = $this->publisherMakroItemRepository->findPrevious($publisherMakroItem);
        if ($previous) {
            $this->redirect('edit', null, null, ['publisherMakroItem' => $previous]);
        } else {
            $this->redirect('list');
        }
    }

    /**
     * action lookupWork
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @param string $name
     * @return void
     */
    public function lookupWorkAction(PublisherMakroItem $publisherMakroItem, string $name)
    {
        $works = $this->workRepository->lookupWork($name);
        $this->view->assign('works', $works);
        $this->view->assign('name', $name);
        $this->view->assign('publisherMakroItem', $publisherMakroItem);
    }

    /**
     * action lookupForm
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @param string $name
     * @return void
     */
    public function lookupFormAction(
        PublisherMakroItem $publisherMakroItem,
        string $name
    )
    {
        //throw away or refactor
        $forms = $this->objectManager->get('SLUB\\PublisherDb\\Domain\\Repository\\FormRepository')->lookupForm($name);
        $forms = Tools::elimDuplicates($forms);
        $this->view->assign('forms', $forms);
        $this->view->assign('publisherMakroItem', $publisherMakroItem);
    }

    /**
     * action lookupInstrument
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @param string $name
     * @return void
     */
    public function lookupInstrumentAction(PublishedItem $publisherMakroItem, string $name)
    {
        //throw away or refactor
        $instruments = $this->objectManager->get('SLUB\\PublisherDb\\Domain\\Repository\\InstrumentRepository')->lookupInstrument($name);
        $instruments = Tools::elimDuplicates($instruments);
        $url = 'http://sdvlodpro.slub-dresden.de:9200/gnd_marc21/_search?q=150.__.a.keyword:' . $name;
        $query = json_decode(file_get_contents($url), true);
        foreach ($query['hits']['hits'] as $set) {
            $superWord = '';
            $flatSet = \SLUB\PublisherDb\Lib\GndLib::flattenDataSet($set['_source']);
            foreach ($flatSet[550] as $row) {
                if ($row['i'] == 'Oberbegriff allgemein') {
                    if ($superWord) {
                        $superWord = $superWord . ', ' . $row['a'];
                    } else {
                        $superWord = $row['a'];
                    }
                }
            }
            $queryOut[] = [
            'name' => $flatSet[150][0]['a'], 
            'superWord' => $superWord, 
            'id' => $flatSet['024'][0]['a']
            ];
        }
        $this->view->assign('query', $queryOut);
        $this->view->assign('instruments', $instruments);
        $this->view->assign('publisherMakroItem', $publisherMakroItem);
    }

    /**
     * action search
     * 
     * @param string $term
     * @return void
     */
    public function searchAction(string $term)
    {
        $params = [
            'index' => 'published_item',
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => $term
                    ]
                ]
            ]
        ];
        $elasticResults = $this->elasticClient->search($params)['hits']['hits'];

        foreach ($elasticResults as $elasticResult) {
            $results[] = $this->publisherMakroItemRepository->findByUid($elasticResult['_source']['uid']);
        }

        $this->view->assign('publisherMakroItems', $results ?? []);
    }

    /**
     * action lookupPerson
     * 
     * @param PublisherMakroItem $publisherMakroItem
     * @param string $name
     * @param string $role
     * @return void
     */
    public function lookupPersonAction(PublishedItem $publisherMakroItem, string $name, string $role)
    {
        $persons = $this->personRepository->lookupComposer($name);
        $persons = Tools::elimDuplicates($persons);
        $this->view->assign('persons', $persons);
        $this->view->assign('publisherMakroItem', $publisherMakroItem);
        $this->view->assign('role', $role);
    }
}
