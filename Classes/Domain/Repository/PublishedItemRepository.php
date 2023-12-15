<?php
namespace Slub\MpdbCore\Domain\Repository;

use Slub\MpdbCore\Domain\Model\Publisher;
use Slub\DmNorm\Domain\Model\GndWork;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * The repository for PublisherMakroItems
 */
class PublishedItemRepository extends Repository
{

    /**
     * @var array
     */
    protected $defaultOrderings = [
    'mvdb_id' => QueryInterface::ORDER_ASCENDING, 
    'title' => QueryInterface::ORDER_ASCENDING
    ];

    /**
     * Find all publisherMakroItems which link the work
     * 
     * @param Work $work
     * @param int $level
     * @return boolean|QueryResult
     */
    public function lookupByWork(GndWork $work, int $level = -2)
    {
        $query = $this->createQuery();
        $condition = $query->logicalAnd(
            [
                $query->contains('containedWorks', $work),
                $query->greaterThanOrEqual('final', $level)
            ]
        );
        return $query->
            matching($condition)->
            execute();
    }

    /**
     * Find next item by uid
     * 
     * @param integer $uid
     * @return boolean|QueryResult
     */
    public function findNext($publisherMakroItem)
    {
        $id = $publisherMakroItem->getMvdbId();
        $query = $this->createQuery();
        $result = $query->matching($query->greaterThan('mvdb_id', $id))->setLimit(1)->execute();
        if ($query->count()) {
            return $result->getFirst();
        } else {
            return false;
        }
    }

    /**
     * Find previous item by uid
     * 
     * @param integer $uid
     * @return boolean|QueryResult
     */
    public function findPrevious($publisherMakroItem)
    {
        $mvdbId = $publisherMakroItem->getMvdbId();
        $query = $this->createQuery();
        $ordering = ['mvdb_id' => QueryInterface::ORDER_DESCENDING];
        $result = $query->matching($query->lessThan('mvdb_id', $mvdbId))->setOrderings($ordering)->setLimit(1)->execute();
        if ($query->count()) {
            return $result->getFirst();
        } else {
            return false;
        }
    }

    /**
     * Find Items, select sorting, filter by publisher
     * 
     * @param Publisher $publisher
     * @param string $sortString
     * @param bool $desc
     * @param int $from
     * @param int $level
     * @return boolean|QueryResult
     */
    public function dbListFe(Publisher $publisher = null, string $sortString = '', bool $desc = false, int $final = -3)
    {
        $query = $this->createQuery();
        $conditions = [];

        if ($publisher)
            $conditions[] = $query->equals('publisher', $publisher);
        if ($final > -3)
            $conditions[] = $query->equals('final', $final);
        if ($conditions)
            $query->matching($query->logicalAnd($conditions));

        if ($sortString) {
            if ($desc) {
                $query->setOrderings([$sortString => QueryInterface::ORDER_DESCENDING]);
            }
            $query->setOrderings([$sortString => QueryInterface::ORDER_ASCENDING]);
        }

        return $query->execute();
    }

    /*
     * Count Makros of supplied publisher and supplied level
     *
     * @param Publisher $publisher
     * @param int $level
     * @return int
     */
    public function countMakros(Publisher $publisher = null, int $level = -2)
    {
        $query = $this->createQuery();
        if ($publisher) {
            $query->matching(
            $query->logicalAnd(
            [
            $query->equals('publisher', $publisher), 
            $query->greaterThanOrEqual('final', $level)
            ]
            )
            );
        } else {
            $query->matching($query->greaterThanOrEqual('final', $level));
        }
        return $query->count();
    }

    /**
     * Find Items, select sorting, filter by publisher
     * 
     * @param Publisher $publisher
     * @param string $sortString
     * @param bool $desc
     * @param int $final
     * @return boolean|QueryResult
     */
    public function dbSearch(Publisher $publisher = null, string $sortString = '', bool $desc = false, int $final = -3)
    {
        $query = $this->createQuery();
        if ($publisher && $final == -3) {
            $query->matching($query->equals('publisher', $publisher));
        } else {
            if (!$publisher && $final > -3) {
                $query->matching($query->equals('final', $final));
            } else {
                if ($publisher && $final > -3) {
                    $query->logicalAnd([
                        $query->matching($query->equals('publisher', $publisher)), 
                        $query->matching($query->equals('final', $final))
                    ]);
                }
            }
        }
        if ($sortString) {
            if ($desc) {
                return $query->setOrderings([$sortString => QueryInterface::ORDER_DESCENDING])->execute();
            }
            return $query->setOrderings([$sortString => QueryInterface::ORDER_ASCENDING])->execute();
        }
        return $query->execute();
    }

    /**
     * Count ready and finished items in database
     * 
     * @param $objectManager
     */
    public function counter($objectManager)
    {
        $testPublisher = $objectManager->get('SLUB\\PublisherDb\\Domain\\Repository\\PublisherRepository')->findOneByShorthand('AA');
        $query = $this->createQuery();
        $resultArray = [];
        $resultArray['ready'] = $query->count();
        $resultArray['finished'] = $query->matching($query->logicalNot($query->equals('title', '')))->count();
        $resultArray['lastNew'] = $query->setOrderings(['tstamp' => QueryInterface::ORDER_DESCENDING])->matching(
        $query->logicalNot(
        $query->logicalOr(
        [
        $query->equals('publisher', $testPublisher), 
        $query->equals('title', ''), 
        $query->equals('title', '0'), 
        $query->lessThan('final', '2')
        ]
        )
        )
        )->setLimit(12)->execute();
        return $resultArray;
    }

    /**
     * lookupItem
     * 
     * @param string $name
     */
    public function lookupItem(string $name)
    {
        $identityQuery = $this->createQuery();
        $identityQuery->matching($identityQuery->equals('title', $name, $caseSensitive = FALSE));
        $likeQuery = $this->createQuery();
        $likeQuery->matching($likeQuery->like('title', '%' . $name . '%'));
        $items = $identityQuery->execute()->toArray();
        $items = array_merge($items, $likeQuery->execute()->toArray());
        return array_unique($items);
    }

    /**
     * find a makro by its id
     * 
     * @param string $id
     */
    public function getMakroById(string $id)
    {
        $mikro = GeneralUtility::makeInstance('SLUB\\PublisherDb\\Domain\\Repository\\PublisherMikroItemRepository')->getSomeMikro($id);
        return $mikro->getPublisherMakroItem();
    }

    /**
     * find all public makros
     */
    public function findAllPublic()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('final', '2'));
        return $query->execute();
    }

    /**
     * Provides search functionality for frontend
     * 
     * @param int $level
     * @param string $searchTerm
     * @param Publisher $publisher
     */
    public function dbSearchFe(int $level, string $searchTerm, Publisher $publisher = null)
    {
        $query = $this->createQuery();
        if ($publisher) {
            $query->matching(
                $query->logicalAnd(
                    [
                        $query->like('title', '%' . $searchTerm . '%'), 
                        $query->greaterThanOrEqual('final', $level), 
                        $query->equals('publisher', $publisher)
                    ]
                )
            );
        } else {
            $query->matching(
                $query->logicalAnd(
                    [
                        $query->like('title', '%' . $searchTerm . '%'), 
                        $query->greaterThanOrEqual('final', $level)
                    ]
                )
            );
        }
        return $query->execute();
    }

    public function lookupPlateId(string $plateId, Publisher $publisher, $level)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                [
                    $query->like('plate_ids', '%' . $plateId . '%'), 
                    $query->greaterThanOrEqual('final', $level), 
                    $query->equals('publisher', $publisher)
                ]
            )
        );
        return $query->execute()->getFirst();
    }
}
