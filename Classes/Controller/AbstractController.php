<?php
namespace Slub\MpdbCore\Controller;

use Slub\DmNorm\Domain\Repository\GndInstrumentRepository;
use Slub\DmNorm\Domain\Repository\GndGenreRepository;
use Slub\DmOnt\Domain\Repository\GenreRepository as MpdbGenreRepository;
use Slub\DmOnt\Domain\Repository\InstrumentRepository as MpdbInstrumentRepository;
use Slub\DmOnt\Domain\Repository\MediumOfPerformanceRepository;
use Slub\MpdbCore\Domain\Repository\PublishedItemRepository;
use Slub\MpdbCore\Domain\Repository\PublisherRepository;
use Slub\DmNorm\Domain\Repository\GndPersonRepository;
use Slub\DmNorm\Domain\Repository\GndWorkRepository;
use Slub\MpdbCore\Common\ElasticClientBuilder;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;

/***
 *
 * This file is part of the "Publisher Database" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *	(c) 2020 Matthias Richter <matthias.richter@slub-dresden.de>, SLUB Dresden
 *
 ***/
/**
 * AbstractController
 */
class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * finality level
     *
     * @var int
     */
    protected $level = 2;

    /**
     * gndInstrumentRepository
     * 
     * @var GndInstrumentRepository
     */
    protected $gndInstrumentRepository = null;

    /**
     * gndGenreRepository
     * 
     * @var GndGenreRepository
     */
    protected $gndGenreRepository = null;

    /**
     * gndWorkRepository
     * 
     * @var GndWorkRepository
     */
    protected $gndWorkRepository = null;

    /**
     * publishedItemRepository
     * 
     * @var PublishedItemRepository
     */
    protected $publishedItemRepository = null;

    /**
     * gndPersonRepository
     * 
     * @var GndPersonRepository
     */
    protected $gndPersonRepository = null;

    /**
     * publisherRepository
     * 
     * @var PublisherRepository
     */
    protected $publisherRepository = null;

    /**
     * mpdbGenreRepository
     * 
     * @var MpdbGenreRepository
     */
    protected $mpdbGenreRepository = null;

    /**
     * mediumOfPerformanceRepository
     * 
     * @var MediumOfPerformanceRepository
     */
    protected $mediumOfPerformanceRepository = null;

    /**
     * mpdbInstrumentRepository
     * 
     * @var MpdbInstrumentRepository
     */
    protected $mpdbInstrumentRepository = null;

    /**
     * elasticClient
     * @var Client
     */
    protected $elasticClient = null;

    /**
     * @param GndGenreRepository $gndGenreRepository
     */
    public function injectGndGenreRepository(GndGenreRepository $gndGenreRepository)
    {
        $this->gndGenreRepository = $gndGenreRepository;
    }

    /**
     * @param GndInstrumentRepository $gndInstrumentRepository
     */
    public function injectGndInstrumentRepository(GndInstrumentRepository $gndInstrumentRepository)
    {
        $this->gndInstrumentRepository = $gndInstrumentRepository;
    }

    /**
     * @param MpdbInstrumentRepository $mpdbInstrumentRepository
     */
    public function injectMpdbInstrumentRepository(MpdbInstrumentRepository $mpdbInstrumentRepository)
    {
        $this->mpdbInstrumentRepository = $mpdbInstrumentRepository;
    }

    /**
     * @param MediumOfPerformanceRepository $mediumOfPerformanceRepository
     */
    public function injectMediumOfPerformanceRepository(MediumOfPerformanceRepository $mediumOfPerformanceRepository)
    {
        $this->mediumOfPerformanceRepository = $mediumOfPerformanceRepository;
    }


    /**
     * @param MpdbGenreRepository $mpdbGenreRepository
     */
    public function injectMpdbGenreRepository(MpdbGenreRepository $mpdbGenreRepository)
    {
        $this->mpdbGenreRepository = $mpdbGenreRepository;
    }

    /**
     * @param GndWorkRepository $gndWorkRepository
     */
    public function injectGndWorkRepository(GndWorkRepository $gndWorkRepository)
    {
        $this->gndWorkRepository = $gndWorkRepository;
    }

    /**
     * @param PublishedItemRepository $publishedItemRepository
     */
    public function injectPublishedItemRepository(PublishedItemRepository $publishedItemRepository)
    {
        $this->publishedItemRepository = $publishedItemRepository;
    }

    /**
     * @param GndPersonRepository $gndPersonRepository
     */
    public function injectGndPersonRepository(GndPersonRepository $gndPersonRepository)
    {
        $this->gndPersonRepository = $gndPersonRepository;
    }

    /**
     * @param PublisherRepository $publisherRepository
     */
    public function injectPublisherRepository(PublisherRepository $publisherRepository)
    {
        $this->publisherRepository = $publisherRepository;
    }

    /**
     * Construct all Controllers with access to global finality level
     * which depends on whether user is logged in in backend
     */
    function __construct() {
        $this->level = $GLOBALS['BE_USER'] ? -1 : 2;
    }

    /**
     * initialize show action: firing up client
     */
    public function initializeSearchAction()
    {
        $this->elasticClient = ElasticClientBuilder::create()->
            autoconfig()->
            build();
    }

    /**
     * initialize show action: firing up client
     */
    public function initializeShowAction()
    {
        $this->elasticClient = ElasticClientBuilder::create()->
            autoconfig()->
            build();
    }

    /**
     * initialize create action to convert date
     */
    public function initializeCreateAction()
    {
        $this->formatDate();
    }

    /**
     * initialize update action to convert date
     */
    public function initializeUpdateAction()
    {
        $this->formatDate();
    }

    /**
     * initialize edit action to convert date
     */
    public function initializeEditAction()
    {
        $this->formatDate();
    }

    protected function formatDate()
    {
        $formatArgument = function (string $argument) {
            $formatField = function (string $field, string $format) use ($argument) {
                if ($this->arguments->hasArgument($argument)) {
                    $this->arguments
                        ->getArgument($argument)
                        ->getPropertyMappingConfiguration()
                        ->forProperty($field)
                        ->setTypeConverterOption(DateTimeConverter::class, DateTimeConverter::CONFIGURATION_DATE_FORMAT, $format);
                }
            };

            $formatField('dateOfBirth', 'd.m.Y');
            $formatField('dateOfDeath', 'd.m.Y');
            $formatField('dateOfAction', 'd.m.Y');
            $formatField('activeFrom', 'd.m.Y');
            $formatField('activeTo', 'd.m.Y');
            $formatField('dateOfProduction', 'Y');
        };

        $formatArgument('newWork');
        $formatArgument('work');
        $formatArgument('newPerson');
        $formatArgument('person');
        $formatArgument('publisher');
        $formatArgument('newPublisher');
    }

}
