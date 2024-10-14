<?php

namespace Slub\MpdbCore\Command;

use Elastic\Elasticsearch\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Slub\MpdbPresentation\Lib\DbArray;
use Slub\MpdbCore\Common\ElasticClientBuilder;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

/**
 * Index Command class
 *
 * @author Matthias Richter <matthias.richter@slub-dresden.de>
 * @package TYPO3
 * @subpackage mpdb_core
 * @access public
 */

class IndexCommand extends Command
{
    const PUBLISHED_ITEM_INDEX = 'published_item';
    const WORK_INDEX = 'work';
    const PERSON_INDEX = 'person';

    const NAME_COLNAME = 'name';
    const SHORTHAND_COLNAME = 'shorthand';
    const PUBLIC_COLNAME = 'public';
    const PUBLISHER_TABLE_NAME = 'tx_mpdbcore_domain_model_publisher';
    const PUBLISHER_INDEX_NAME = 'publishers';

    protected string $prefix = '';
    protected ?Client $client = null;
    protected array $extConf = [];

    protected static $personData = [
        [ 'name', '', 'string' ],
        [ 'gnd_id', '', 'string' ],
        [ 'gender', '', 'string' ],
        [ 'date_of_birth', '', 'date' ],
        [ 'date_of_death', '', 'date' ],
        [ 'place_of_birth', '', 'string' ],
        [ 'place_of_death', '', 'string' ],
        [ 'place_of_activity', '', 'string' ],
        [ 'geographic_area_code', '', 'string' ] ];
    protected static $workData = [
        [ 'title', 'generic_title', 'string' ],
        [ 'individual_title', '', 'string' ],
        [ 'gnd_id', '', 'string' ],
        [ 'title_instrument', '', 'string' ],
        [ 'opus_no', '', 'string' ],
        [ 'title_no', 'title_number', 'string' ],
        [ 'firstcomposer', '', '' ],
        [ 'main_instrumentation', '', '' ],
        [ 'publishers', '', '' ],
        [ 'full_title', '', '' ],
        [ 'date_of_production', '', '' ],
        [ 'tonality', '', 'string' ] ];
    protected static $instrumentationData = [
        [ 'has_orchestra', '', 'bool' ],
        [ 'has_choir', '', 'bool' ],
        [ 'instrumental_soloists', '', 'string' ],
        [ 'vocal_soloists', '', 'string' ] ];
    protected static $instrumentData = [
        [ 'name', '', 'string' ],
        [ 'gnd_id', '', 'string' ] ];
    protected static $genreData = [
        [ 'name', '', 'string' ],
        [ 'gnd_id', '', 'string' ] ];
    protected static $mikroData = [
        [ 'mvdb_id', '', 'string' ],
        [ 'plate_id', 'publisher_number', 'string' ],
        [ 'part', '', 'string' ],
        [ 'voice', '', 'string' ],
        [ 'publisheditem', 'published_item', '' ] ];
    protected static $makroData = [
        [ 'title', '', 'string' ],
        [ 'type', '', 'string' ],
        [ 'mvdb_id', '', 'string' ],
        [ 'piano_combination', '', 'string' ],
        [ 'public_comment', 'comment', 'string' ],
        [ 'final', '', '' ] ];
    protected static $actionData = [
        [ 'quantity', '', 'int' ],
        [ 'date_of_action', '', 'date' ],
        [ 'type', '', 'string' ],
        [ 'inferred', '', 'bool' ],
        [ 'publishedsubitem', 'published_subitem', '' ],
        [ 'certain', '', 'bool' ] ];

    protected static $publishedItemSeq = [
        [
            'super' => 'published_subitem',
            'sub' => 'prints.published_subitem',
            'name' => 'prints'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'sales.published_subitem',
            'name' => 'sales'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'maculation.published_subitem',
            'name' => 'maculation'
        ],
        [
            'super' => 'work',
            'sub' => 'gnd_instrument',
            'name' => 'gnd_instruments',
            'mm' => 'work_gndinstrument_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ],
        [
            'super' => 'work',
            'sub' => 'gnd_genre',
            'name' => 'gnd_genres',
            'mm' => 'work_gndgenre_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ],
        [
            'super' => 'work',
            'sub' => 'genre',
            'name' => 'mvdb_genres',
            'mm' => 'work_genre_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ],
        [
            'super' => 'instrumentation',
            'sub' => 'instrument',
            'name' => 'instruments',
            'mm' => 'instrumentation_instrument_mm',
            'local' => 'instrumentation',
            'foreign' => 'instrument'
        ],
        [
            'super' => 'published_item',
            'sub' => 'person',
            'name' => 'editors',
            'mm' => 'published_item_editor_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'published_item',
            'sub' => 'person',
            'name' => 'composers',
            'mm' => 'published_item_composer_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'work.main_instrumentation',
            'sub' => 'instrumentation',
            'name' => 'mvdb_instrumentation',
        ],
        [
            'super' => 'work.firstcomposer',
            'sub' => 'person',
            'name' => 'composers'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'work',
            'name' => 'works',
            'mm' => 'published_subitem_work_mm',
            'local' => 'published_subitem',
            'foreign' => 'work'
        ],
        [
            'super' => 'published_item',
            'sub' => 'work',
            'name' => 'works',
            'mm' => 'published_item_work_mm',
            'local' => 'published_item',
            'foreign' => 'work'
        ],
        [
            'super' => 'published_item',
            'sub' => 'published_subitem.published_item',
            'name' => 'published_subitems'
        ],
        [
            'super' => 'published_item',
            'sub' => 'gnd_instrument',
            'name' => 'instruments',
            'mm' => 'published_item_instrument_mm',
            'local' => 'published_item',
            'foreign' => 'instrument'
        ],
        [
            'super' => 'published_item',
            'sub' => 'gnd_genre',
            'name' => 'genres',
            'mm' => 'published_item_genre_mm',
            'local' => 'published_item',
            'foreign' => 'genre'
        ]
    ];

    protected static $workSeq = [
        [
            'super' => 'published_subitem',
            'sub' => 'prints.published_subitem',
            'name' => 'prints'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'sales.published_subitem',
            'name' => 'sales'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'maculation.published_subitem',
            'name' => 'maculation'
        ],
        [
            'super' => 'work',
            'sub' => 'genre',
            'name' => 'genres',
            'mm' => 'work_genre_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ],
        [
            'super' => 'instrumentation',
            'sub' => 'instrument',
            'name' => 'instruments',
            'mm' => 'instrumentation_instrument_mm',
            'local' => 'instrumentation',
            'foreign' => 'instrument'
        ],
        [
            'super' => 'published_item',
            'sub' => 'person',
            'name' => 'editors',
            'mm' => 'published_item_editor_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'work.main_instrumentation',
            'sub' => 'instrumentation',
            'name' => 'instrumentation',
        ],
        [
            'super' => 'work.firstcomposer',
            'sub' => 'person',
            'name' => 'composers'
        ],
        [
            'super' => 'published_item',
            'sub' => 'published_subitem.publisheditem',
            'name' => 'published_subitems'
        ],
        [
            'super' => 'work',
            'sub' => 'published_subitem',
            'name' => 'published_subitems',
            'mm' => 'published_subitem_work_mm',
            'local' => 'published_subitem',
            'foreign' => 'work'
        ],
        [
            'super' => 'work',
            'sub' => 'published_item',
            'name' => 'published_items',
            'mm' => 'published_item_work_mm',
            'local' => 'published_item',
            'foreign' => 'work'
        ]
    ];

    protected static $personSeq = [
        [
            'super' => 'published_subitem',
            'sub' => 'prints.published_subitem',
            'name' => 'prints'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'sales.published_subitem',
            'name' => 'sales'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'maculation.published_subitem',
            'name' => 'maculation'
        ],
        [
            'super' => 'work',
            'sub' => 'genre',
            'name' => 'genres',
            'mm' => 'work_genre_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ],
        [
            'super' => 'instrumentation',
            'sub' => 'instrument',
            'name' => 'instruments',
            'mm' => 'instrumentation_instrument_mm',
            'local' => 'instrumentation',
            'foreign' => 'instrument'
        ],
        [
            'super' => 'work.main_instrumentation',
            'sub' => 'instrumentation',
            'name' => 'instrumentation',
        ],
        [
            'super' => 'published_item',
            'sub' => 'published_subitem.published_item',
            'name' => 'published_subitems'
        ],
        [
            'super' => 'work',
            'sub' => 'published_subitem',
            'name' => 'published_subitems',
            'mm' => 'published_subitem_work_mm',
            'local' => 'published_subitem',
            'foreign' => 'work'
        ],
        [
            'super' => 'work',
            'sub' => 'published_item',
            'name' => 'published_items',
            'mm' => 'published_item_work_mm',
            'local' => 'published_item',
            'foreign' => 'work'
        ],
        [
            'super' => 'person',
            'sub' => 'published_item',
            'name' => 'edited_published_items',
            'mm' => 'published_item_editor_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'person',
            'sub' => 'published_item',
            'name' => 'composed_published_items',
            'mm' => 'published_item_composer_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'person',
            'sub' => 'work.firstcomposer',
            'name' => 'works'
        ]
    ];

    protected static $genreSeq = [
        [
            'super' => 'published_subitem',
            'sub' => 'prints.published_subitem',
            'name' => 'prints'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'sales.published_subitem',
            'name' => 'sales'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'maculation.published_subitem',
            'name' => 'maculation'
        ],
        [
            'super' => 'instrumentation',
            'sub' => 'instrument',
            'name' => 'instruments',
            'mm' => 'instrumentation_instrument_mm',
            'local' => 'instrumentation',
            'foreign' => 'instrument'
        ],
        [
            'super' => 'published_item',
            'sub' => 'person',
            'name' => 'editors',
            'mm' => 'published_item_editor_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'work.main_instrumentation',
            'sub' => 'instrumentation',
            'name' => 'instrumentation',
        ],
        [
            'super' => 'work.firstcomposer',
            'sub' => 'person',
            'name' => 'composers'
        ],
        [
            'super' => 'published_item',
            'sub' => 'published_subitem.published_item',
            'name' => 'published_subitems'
        ],
        [
            'super' => 'work',
            'sub' => 'published_subitem',
            'name' => 'published_subitems',
            'mm' => 'published_subitem_work_mm',
            'local' => 'published_subitem',
            'foreign' => 'work'
        ],
        [
            'super' => 'work',
            'sub' => 'published_item',
            'name' => 'published_items',
            'mm' => 'published_item_work_mm',
            'local' => 'published_item',
            'foreign' => 'work'
        ],
        [
            'super' => 'genre',
            'sub' => 'work',
            'name' => 'works',
            'mm' => 'work_genre_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ]
    ];

    protected static $instrumentSeq = [
        [
            'super' => 'published_subitem',
            'sub' => 'prints.published_subitem',
            'name' => 'prints'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'sales.published_subitem',
            'name' => 'sales'
        ],
        [
            'super' => 'published_subitem',
            'sub' => 'maculation.published_subitem',
            'name' => 'maculation'
        ],
        [
            'super' => 'work',
            'sub' => 'genre',
            'name' => 'genres',
            'mm' => 'work_genre_mm',
            'local' => 'work',
            'foreign' => 'genre'
        ],
        [
            'super' => 'published_item',
            'sub' => 'person',
            'name' => 'editors',
            'mm' => 'published_item_editor_mm',
            'local' => 'published_item',
            'foreign' => 'person'
        ],
        [
            'super' => 'work.firstcomposer',
            'sub' => 'person',
            'name' => 'composers'
        ],
        [
            'super' => 'published_item',
            'sub' => 'published_subitem.published_item',
            'name' => 'published_subitems'
        ],
        [
            'super' => 'work',
            'sub' => 'published_subitem',
            'name' => 'published_subitems',
            'mm' => 'published_subitem_work_mm',
            'local' => 'published_subitem',
            'foreign' => 'work'
        ],
        [
            'super' => 'work',
            'sub' => 'published_item',
            'name' => 'published_items',
            'mm' => 'published_item_work_mm',
            'local' => 'published_item',
            'foreign' => 'work'
        ],
        [
            'super' => 'instrumentation',
            'sub' => 'work.main_instrumentation',
            'name' => 'works',
        ],
        [
            'super' => 'instrument',
            'sub' => 'instrumentation',
            'name' => 'instrumentations',
            'mm' => 'instrumentation_instrument_mm',
            'local' => 'instrumentation',
            'foreign' => 'instrument'
        ]
    ];

    protected $dataObjectList;

    protected $dataObjects;

    protected $indices;

    protected $indexList;

    protected ?SymfonyStyle $io = null;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->dataObjectList = [
            'person' => [
                'table' => 'tx_dmnorm_domain_model_gndperson',
                'finality' => TRUE,
                'key' => 'gnd_id',
                'fields' => self::$personData
            ],
            'published_item' => [
                'table' => 'tx_mpdbcore_domain_model_publisheditem',
                'finality' => TRUE,
                'key' => 'mvdb_id',
                'fields' => self::$makroData
            ],
            'published_subitem' => [
                'table' => 'tx_mpdbcore_domain_model_publishedsubitem',
                'key' => 'mvdb_id',
                'fields' => self::$mikroData
            ],
            'action' => [
                'table' => 'tx_mpdbcore_domain_model_publisheraction',
                'fields' => self::$actionData
            ],
            'work' => [
                'table' => 'tx_dmnorm_domain_model_gndwork',
                'finality' => TRUE,
                'key' => 'gnd_id',
                'fields' => self::$workData
            ],
            'work_gndgenre_mm' => [
                'table' => 'tx_dmnorm_gndwork_gndgenre_mm',
            ],
            'work_gndinstrument_mm' => [
                'table' => 'tx_dmnorm_gndwork_gndinstrument_mm',
            ],
            'published_item_work_mm' => [
                'table' => 'tx_mpdbcore_publisheditem_work_mm',
            ],
            'published_item_instrument_mm' => [
                'table' => 'tx_mpdbcore_publisheditem_instrument_mm',
            ],
            'published_item_genre_mm' => [
                'table' => 'tx_mpdbcore_publisheditem_genre_mm'
            ],
            'published_subitem_work_mm' => [
                'table' => 'tx_mpdbcore_publishedsubitem_work_mm',
            ],
            'genre' => [
                'table' => 'tx_dmont_domain_model_genre',
                'key' => 'gnd_id',
                'fields' => self::$genreData
            ],
            'work_genre_mm' => [
                'table' => 'tx_dmont_work_genre_mm'
            ],
            'instrumentation' => [
                'table' => 'tx_dmont_domain_model_mediumofperformance',
                'fields' => self::$instrumentationData
            ],
            'instrumentation_instrument_mm' => [
                'table' => 'tx_dmont_mediumofperformance_instrument_mm'
            ],
            'instrument' => [
                'table' => 'tx_dmont_domain_model_instrument',
                'key' => 'gnd_id',
                'fields' => self::$instrumentData
            ],
            'published_item_editor_mm' => [
                'table' => 'tx_mpdbcore_publisheditem_person_mm',
            ],
            'published_item_composer_mm' => [
                'table' => 'tx_mpdbcore_publisheditem_firstcomposer_person_mm',
            ],
            'gnd_instrument' => [
                'table' => 'tx_dmnorm_domain_model_gndinstrument',
                'key' => 'gnd_id',
                'fields' => self::$instrumentData
            ],
            'gnd_genre' => [
                'table' => 'tx_dmnorm_domain_model_gndgenre',
                'key' => 'gnd_id',
                'fields' => self::$genreData
            ]
        ];

        $this->indexList = [
            self::PUBLISHED_ITEM_INDEX => self::$publishedItemSeq,
            self::PERSON_INDEX => self::$personSeq,
            self::WORK_INDEX => self::$workSeq
        ];

        $this->extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mpdb_core');
        $this->prefix = $this->extConf['prefix'];

        $this->client = ElasticClientBuilder::create()->
            autoconfig()->
            build();
    }

    /**
     * Executes the command to build indices from Database
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //$this->initialize();

        $this->io = new SymfonyStyle($input, $output);
        $this->io->title($this->getDescription());

        $this->io->section('Fetching Data Objects');
        $this->fetchObjects();

        $this->io->section('Building Indices');
        $this->buildIndices();

        $this->io->section('Committing Indices');
        $this->commitIndices();

        $this->io->section('Indexing Publishers');
        $this->indexPublishers();

        $this->io->success('All indices built and committed.');
        return 0;

    }

    /**
     * Indexes public publishers
     *
     * @return void
     */
    protected function indexPublishers(): void
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::PUBLISHER_TABLE_NAME);
        $qb->select(
                'uid',
                self::NAME_COLNAME . ' AS name',
                self::SHORTHAND_COLNAME . ' AS shorthand',
                self::PUBLIC_COLNAME . ' AS public'
            )->
            from(self::PUBLISHER_TABLE_NAME);

        if ($this->client->indices()->exists(['index' => $this->prefix . self::PUBLISHER_INDEX_NAME])->asString()) {
            $this->client->indices()->delete(['index' => $this->prefix . self::PUBLISHER_INDEX_NAME]);
        }

        Collection::wrap($qb->execute()->fetchAll())->
            filter(function ($publisher) { return self::isPublic($publisher); })->
            each(function ($publisher) { self::indexPublisher($publisher); });
    }

    private static function isPublic(array $publisher): bool {
        return (bool) $publisher[self::PUBLIC_COLNAME];
    }

    private function indexPublisher(array $publisher): void {
        unset($publisher[self::PUBLIC_COLNAME]);

        $params = [
            'index' => $this->prefix . self::PUBLISHER_INDEX_NAME,
            'id' => $publisher['uid'],
            'body' => $publisher ];

        $this->client->index($params);
    }

    /**
     * Commits indices to Elasticsearch
     *
     * @return void
     */
    protected function commitIndices(): void
    {

        foreach ($this->indices as $name => $index) {
            $this->io->text('Committing the ' . $name . ' index');
            $idField = $this->dataObjectList[$name]['key'];

            $params = [];
            $params = [ 'body' => [] ];
            $bulkCount = 0;

            $this->io->progressStart(count($index));
            foreach ($index as $document) {
                $document['type'] = $name;
                $this->io->progressAdvance();
                $params['body'][] = [ 'index' => 
                    [ 
                        '_index' => 'temp' . $this->prefix . $name,
                        '_id' => $document[$idField]
                    ] 
                ];
                $params['body'][] = json_encode($document);

                // commit bulk
                if (!(++$bulkCount % $this->extConf['bulkSize'])) {
                    $this->client->bulk($params);
                    $params = [ 'body' => [] ];
                }
            }
            $this->io->progressFinish();
            $this->client->bulk($params);

            if ($this->client->indices()->exists(['index' => $this->prefix . $name])->asBool()) {
                $this->client->indices()->delete(['index' => $this->prefix . $name]);
            }

            $params = [
                'index' => 'temp' . $this->prefix . $name,
                'body' => [
                    'index.blocks.write' => TRUE
                ]
            ];
            $this->client->indices()->putSettings($params);
            $params = [
                'index' => 'temp' . $this->prefix . $name,
                'target' => $this->prefix . $name
            ];
            $this->client->indices()->clone($params);
            $this->client->indices()->delete(['index' => 'temp' . $this->prefix . $name]);
        }
    }

    /**
     * Returns an array for writing out SELECT statements
     *
     * @return array
     */
    protected static function getSelectStmt(array $fields = null, bool $mm): array
    {
        if ($mm) {
            return [ 'uid_local', 'uid_foreign' ];
        }
        $stmt = [ 'uid' ];

        foreach($fields as $field) {
            if ($field[1]) {
                $stmt[] = $field[0] . ' AS ' . $field[1];
            } else {
                $stmt[] = $field[0];
            }
        }

        return $stmt;
    }

    /**
     * Reads data objects from SQL tables to dataObjectList
     *
     * @return void
     */
    protected function fetchObjects(): void
    {
        foreach ($this->dataObjectList as $name => $object) {
            $mm = preg_match('/_mm/', $name) ? TRUE : FALSE;
            $fields = isset($object['fields']) ? $object['fields'] : null;
            $selectStmt = self::getSelectStmt($fields, $mm);

            $qb = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($object['table']);
            $qb->select(...$selectStmt)->
                from($object['table']);
            $eb = $qb->expr();

            if ($name == 'published_item') {
                $qb->where(
                    $eb->notLike('mvdb_id', $qb->createNamedParameter('AA%')),
                    $eb->eq('final', 2)
                );
            }

            $data = $qb->execute()->fetchAll();
            if ($name == 'person') { 
                $data = Collection::wrap($data)->
                    map(function ($person) { return self::removeSortingSymbols($person); });
            }

            $this->dataObjects[$name] = $data;
        }
    }

    protected static function removeSortingSymbols(array $person): array
    {
        $name = Str::of($person['name'])->
            replace('', '')->
            replace('', '');
        $person['name'] = $name;
        return $person;
    }

    /**
     * Executes an indexing sequence
     *
     * @return array
     */
    protected function buildIndex($indexSeq): array
    {
        // TODO rewrite to static function?
        $buffer = [];
        foreach ($indexSeq as $step) {
            $super = explode('.', $step['super']);
            $sub = explode('.', $step['sub']);
            $config = [
                'superKeyField' => $super[1] ?? 'uid',
                'superObject' => $super[0],
                'subKeyField' => $sub[1] ?? 'uid',
                'subObject' => $sub[0],
                'name' => $step['name'],
                'mm' => $step['mm'] ?? '',
                'foreign' => $step['foreign'] ?? '',
                'local' => $step['local'] ?? ''
            ];
            $this->io->text('subordinating ' . $config['subObject'] . ' to ' . $config['superObject']);

            $superDataObjects = $buffer[$config['superObject']] ?? $this->dataObjects[$config['superObject']];
            $subBuffer = isset($buffer[$config['subObject']]) ? $buffer[$config['subObject']] : null;
            $subDataObjects = $this->index($config, $subBuffer);
            $this->io->progressStart(count($superDataObjects));
            $buffer[$config['superObject']] = [];
            foreach ($superDataObjects as $dataObject) {
                $this->io->progressAdvance();
                $keyVal = $dataObject[$config['superKeyField']];
                $dataObject[$config['name']] = isset($subDataObjects[$keyVal]) ? $subDataObjects[$keyVal] : null;
                $buffer[$config['superObject']][] = $dataObject;
            }
            $this->io->progressFinish();
        }

        return $buffer[$config['superObject']];
    }

    /**
     * Returns an associative array of subordinated objects ready to connect to superordinated objects
     *
     * @param array $config
     * @param array $bufferedObject
     * @return array
     */
    protected function index(array $config, array $bufferedObject = null): array
    {
        $indexedObjects = [];

        if ($config['mm']) {
            $subConfig['mm'] = '';
            $subConfig['subObject'] = $config['subObject'];
            $subConfig['subKeyField'] = $config['subKeyField'];
            $subDataObjects = $this->index($subConfig, $bufferedObject);
            $mmObjects = $this->dataObjects[$config['mm']];
            $subKeyField = $config['local'] == $config['subObject'] ? 'uid_local' : 'uid_foreign';
            $superKeyField = $config['local'] == $config['superObject'] ? 'uid_local' : 'uid_foreign';

            foreach($mmObjects as $object) {
                $subKey = $object[$subKeyField];
                $superKey = $object[$superKeyField];
                if (isset($subDataObjects[$subKey][0])) {
                    $indexedObjects[$superKey][] = $subDataObjects[$subKey][0];
                }
            }
        } else {
            $subDataObjects = $bufferedObject ?? $this->dataObjects[$config['subObject']];
            foreach($subDataObjects as $object) {
                $keyField = $config['subKeyField'];
                $key = $object[$keyField];
                $indexedObjects[$key][] = $object;
            }
        }

        return $indexedObjects;
    }

    /**
     * Builds indices
     *
     * @return void
     */
    protected function buildIndices(): void
    {
        $this->io->text('Separating Publisher Actions');
        $this->dataObjects['prints'] = [];
        $this->dataObjects['sales'] = [];
        $this->dataObjects['maculation'] = [];

        $this->io->progressStart(count($this->dataObjects['action']));
        foreach ($this->dataObjects['action'] as $action) {
            $this->io->progressAdvance();
            switch ($action['type']) {
                case 'print': 
                    $this->dataObjects['prints'][] = $action;
                    break;
                case 'sales': 
                    $this->dataObjects['sales'][] = $action;
                    break;
                case 'maculation': 
                    $this->dataObjects['maculation'][] = $action;
                    break;
            }
        }
        $this->io->progressFinish();

        foreach ($this->indexList as $name => $indexSeq) {
            $this->io->text('Building the ' . $name . ' index');
            $this->indices[$name] = $this->buildIndex($indexSeq);
        }
    }

    /**
     * Pre-Execution configuration
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setHelp('Update elasticsearch index.');
        $this->setDescription('Updating the elasticsearch index.');
    }

    protected function correctDatatypes(array $data, array $tables): array
    {
        $mapper = [];
        foreach($tables as $key => $table) {
            foreach($table as $entry) {
                $mapper[$key . '_' . $entry[0]] = $entry[2];
            }
        }

        $this->io->text('Correcting Datatypes');
        $result = [];
        $this->io->progressStart(count($data));
        foreach($data as $entry) {
            $this->io->progressAdvance();
            $resultEntry = [];
            foreach($entry as $key => $value) {
                if ($value === null) {
                    $value = '';
                }
                if ($mapper[$key] === 'bool') {
                    $value = $value ? true : false;
                }
                $resultEntry[$key] = $value;
            }
            $result[] = $resultEntry;
        }
        $this->io->progressFinish();

        return $result;
    }
}
