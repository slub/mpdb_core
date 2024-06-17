<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem',
        'label' => 'title',
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
        'searchFields' => 'title,type,instrumentation,responsible_person,language,id,comment',
        'iconfile' => 'EXT:mpdb_core/Resources/Public/Icons/tx_mpdbcore_domain_model_publishermakroitem.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, type, instrumentation, data_acquisition_certain, related_persons_known, work_examined, data_set_manually_checked, contained_works_identified, responsible_person, date_of_publishing, final, language, id, comment, contained_works, editors, instruments, form, first_composer, published_subitems, publisher',
    ],
    'types' => [
        '1' => ['showitem' => 'title, type, instrumentation, data_acquisition_certain, related_persons_known, work_examined, data_set_manually_checked, contained_works_identified, responsible_person, date_of_publishing, final, language, id, comment, contained_works, editors, instruments, form, first_composer, published_subitems, publisher, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
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
                'foreign_table' => 'tx_mpdbcore_domain_model_publisheditem',
                'foreign_table_where' => 'AND {#tx_mpdbcore_domain_model_publisheditem}.{#pid}=###CURRENT_PID### AND {#tx_mpdbcore_domain_model_publisheditem}.{#sys_language_uid} IN (-1,0)',
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
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'piano_combination' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.piano_combination',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'instrumentation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.instrumentation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'data_acquisition_certain' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.data_acquisition_certain',
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
        'related_persons_known' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.related_persons_known',
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
        'work_examined' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.work_examined',
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
        'data_set_manually_checked' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.data_set_manually_checked',
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
        'contained_works_identified' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.contained_works_identified',
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
        'responsible_person' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.responsible_person',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'date_of_publishing' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.date_of_publishing',
            'config' => [
                'dbType' => 'date',
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 7,
                'eval' => 'date',
                'default' => null,
            ],
        ],
        'final' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.final',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'language' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.language',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'plate_ids' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.plate_ids',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'mvdb_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.mvdb_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'comment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.comment',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'contained_works' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.contained_works',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_dmnorm_domain_model_gndwork',
                'MM' => 'tx_mpdbcore_publisheditem_work_mm',
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
        'editors' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.editors',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_dmnorm_domain_model_gndperson',
                'MM' => 'tx_mpdbcore_publisheditem_person_mm',
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
        'instruments' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.instruments',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_dmont_domain_model_instrument',
                'MM' => 'tx_mpdbcore_publisheditem_instrument_mm',
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
        'form' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.form',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_dmont_domain_model_genre',
                'MM' => 'tx_mpdbcore_publisheditem_genre_mm',
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
        'first_composer' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.first_composer',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_dmnorm_domain_model_gndperson',
                'MM' => 'tx_mpdbcore_publisheditem_firstcomposer_person_mm',
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
        'published_subitems' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.published_subitems',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_mpdbcore_domain_model_publishedsubitem',
                'foreign_field' => 'publisheditem',
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
        'publisher' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mpdb_core/Resources/Private/Language/locallang_db.xlf:tx_mpdbcore_domain_model_publishermakroitem.publisher',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_mpdbcore_domain_model_publisher',
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            
        ],
    ]
];
