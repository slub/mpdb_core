<?php

namespace Slub\MpdbCore\Controller;

use Slub\DmNorm\Domain\Model\GndPerson;
use Slub\MpdbCore\Command\IndexCommand;
use Slub\MpdbCore\Domain\Model\Publisher;
use Slub\MpdbCore\Domain\Model\PublisherAction;
use Slub\MpdbCore\Domain\Model\PublishedItem;
use Slub\MpdbCore\Domain\Model\PublishedSubitem;
use Slub\MpdbCore\Domain\Model\Work;
use Slub\MpdbCore\Lib\DbArray;
use Slub\MpdbCore\Lib\Tools;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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

        $publisherMakroItems = $this->publishedItemRepository->
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

    private function searchAction(array $config)
    {
        $publishedItems = $this->searchService->
            setPublisher($config['publisher'] ?? '')->
            setIndex(IndexCommand::PUBLISHED_ITEM_INDEX)->
            setSearchterm($config['searchTerm'] ?? '')->
            setFrom($config['from'] ?? 0)->
            search();
        $totalItems = $this->searchService->count();
        $publishers = $this->searchService->
            reset()->
            setIndex(IndexCommand::PUBLISHER_INDEX_NAME)->
            search()->
            pluck('_source');

        $this->view->assign('entities', $publishedItems->all());
        $this->view->assign('config', $config);
        $this->view->assign('totalItems', $totalItems);
        $this->view->assign('publishers', $publishers->all());
        $this->view->assign('resultCount', self::RESULT_COUNT);

        return $this->htmlResponse();
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
                $this->publisherActionRepository->
                    findByPublisherMikroItem($publisherMikroItem)->
                    toArray()
            );
        }
        usort($publisherActions, $sortByDate);

        $document = $this->elasticClient->get([
            'index' => IndexCommand::PUBLISHED_ITEM_INDEX,
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
     * action getNext
     * 
     * @param Slub\MpdbCore\Domain\Model\PublishedItem $publisherMakroItem
     * @return void
     */
    public function getNextAction(PublishedItem $publisherMakroItem)
    {
        $next = $this->publishedItemRepository->findNext($publisherMakroItem);
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
        $previous = $this->publishedItemRepository->findPrevious($publisherMakroItem);
        if ($previous) {
            $this->redirect('edit', null, null, ['publisherMakroItem' => $previous]);
        } else {
            $this->redirect('list');
        }
    }

}
