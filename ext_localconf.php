<?php

use \Slub\MpdbCore\Services\ElasticSearchService;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die('Access denied.');

ExtensionManagementUtility::addService(
    'MpdbCore',
    'search',
    'tx_mpdbcore_elasticsearch',
    [
        'title' => 'Elasticsearch Service',
        'description' => 'Provides the frontend with a connection to elasticsearch',
        'subtype' => '',
        'available' => true,
        'priority' => 50,
        'quality' => 50,
        'os' => '',
        'exec' => '',
        'className' => ElasticSearchService::class,
    ]
);
