<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem',
        'label' => 'plate_id',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'plate_id,part,voice,comment,piano_reduction_type',
        'iconfile' => 'EXT:mpdb_core/Resources/Public/Icons/tx_mpdbcore_domain_model_publishedsubitem.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, plate_id, part, voice, price, comment, is_piano_reduction, piano_reduction_type, date_of_publishing, has_negative_store, approve_negative_store, start_store, db_identifier, contained_works, publisher_actions',
    ],
    'types' => [
        '1' => ['showitem' => 'plate_id, part, voice, price, comment, is_piano_reduction, piano_reduction_type, date_of_publishing, has_negative_store, approve_negative_store, start_store, db_identifier, contained_works, publisher_actions, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_mpdbcore_domain_model_publishedsubitem',
                'foreign_table_where' => 'AND {#tx_mpdbcore_domain_model_publishedsubitem}.{#pid}=###CURRENT_PID### AND {#tx_mpdbcore_domain_model_publishedsubitem}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'plate_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.plate_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'part' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.part',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'voice' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.voice',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'price' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.price',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'comment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.comment',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'is_piano_reduction' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.is_piano_reduction',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
                'default' => 0,
            ]
        ],
        'mvdb_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.mvdb_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'piano_reduction_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.piano_reduction_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'date_of_publishing' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.date_of_publishing',
            'config' => [
                'dbType' => 'date',
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date',
                'default' => null,
            ],
        ],
        'has_negative_store' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.has_negative_store',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
                'default' => 0,
            ]
        ],
        'approve_negative_store' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.approve_negative_store',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
                'default' => 0,
            ]
        ],
        'start_store' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.start_store',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'db_identifier' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.db_identifier',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'contained_works' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.contained_works',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_dmnorm_domain_model_work',
                'MM' => 'tx_mpdbcore_publishedsubitem_work_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
            
        ],
        'publisher_actions' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishedsubitem.publisher_actions',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_mpdbcore_domain_model_publisheraction',
                'foreign_field' => 'published_subitem',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'useSortable' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],

        ],
    
        'published_item' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
