<?php
namespace Slub\MpdbCore\Domain\Model;

use Slub\DmNorm\Domain\Model\GndWork;
use Slub\DmNorm\Domain\Model\GndPerson;
use Slub\MpdbCore\Lib\DbArray;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/***
 *
 * This file is part of the "Publisher Database" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Matthias Richter <matthias.richter@slub-dresden.de>, SLUB Dresden
 *
 ***/
/**
 * Manifestation
 */
class PublishedItem extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    const pianoCombinations = [
        "" => "keine",
        "one piano two hands" => "ein Klavier zwei Hände",
        "one piano four hands" => "ein Klavier vier Hände",
        "one piano six hands" => "ein Klavier sechs Hände",
        "two pianos four hands" => "zwei Klaviere vier Hände",
        "two pianos six hands" => "zwei Klaviere sechs Hände",
        "two pianos eight hands" => "zwei Klaviere acht Hände",
        "else" => "sonstige" ];

    const types = [
        "" => "",
        "work" => "Werk",
        "adaptation" => "Version oder Bearbeitung",
        "collection full" => "Sammlung (erschlossen)",
        "collection part" => "Sammlung (teilerschlossen)",
        "collection blackbox" => "Sammlung (unerschlossen)",
        "educational" => "Schulwerk",
        "theoretic" => "theoretisches Werk",
        "else" => "sonstige" ];

    /**
     * publishedSubitems
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublishedSubitem>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $publishedSubitems = null;

    /**
     * personRepository
     * 
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \SLUB\PublisherDb\Domain\Repository\PersonRepository
     */
    //public $personRepository = null;

    /**
     * workRepository
     * 
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \Slub\DmNorm\Domain\Repository\GndWorkRepository
     */
    public $gndWorkRepository = null;

    /**
     * publisherActionRepository
     * 
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \Slub\MpdbCore\Domain\Repository\PublisherActionRepository
     */
    public $publisherActionRepository = null;

    /**
     * title
     * 
     * @var string
     */
    protected $title = '';

    /**
     * type
     * 
     * @var string
     */
    protected $type = '';

    /**
     * instrumentation
     * 
     * @var string
     */
    protected $instrumentation = '';

    /**
     * dataAcquisitionCertain
     * 
     * @var bool
     */
    protected $dataAcquisitionCertain = false;

    /**
     * relatedPersonsKnown
     * 
     * @var bool
     */
    protected $relatedPersonsKnown = false;

    /**
     * workExamined
     * 
     * @var bool
     */
    protected $workExamined = false;

    /**
     * dataSetManuallyChecked
     * 
     * @var bool
     */
    protected $dataSetManuallyChecked = false;

    /**
     * containedWorksIdentified
     * 
     * @var bool
     */
    protected $containedWorksIdentified = false;

    /**
     * responsiblePerson
     * 
     * @var string
     */
    protected $responsiblePerson = '';

    /**
     * dateOfPublishing
     * 
     * @var \DateTime
     */
    protected $dateOfPublishing = null;

    /**
     * final
     * 
     * @var int
     */
    protected $final = 0;

    /**
     * language
     * 
     * @var string
     */
    protected $language = '';

    /**
     * mvdbId
     * 
     * @var string
     */
    protected $mvdbId = '';

    /**
     * comment
     * 
     * @var string
     */
    protected $comment = '';

    /**
     * plateIds
     * 
     * @var string
     */
    protected $plateIds = '';

    /**
     * publisher
     * 
     * @var \Slub\MpdbCore\Domain\Model\Publisher
     */
    protected $publisher = null;

    /**
     * containedWorks
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndWork>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $containedWorks = null;

    /**
     * editors
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $editors = null;

    /**
     * instruments
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndInstrument>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $instruments = null;

    /**
     * form
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndGenre>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $form = null;

    /**
     * firstComposer
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $firstComposer = null;

    /**
     * pianoCombination
     *
     * @var string
     */
    protected $pianoCombination = '';

    /**
     * sets pianoCombination
     *
     * @param string $pianoCombination
     * @return void
     */
    public function setPianoCombination($pianoCombination)
    {
        if (in_array($pianoCombination, array_keys(self::pianoCombinations)))
            $this->pianoCombination = $pianoCombination;
    }

    /**
     * gets pianoCombination
     *
     * @return void
     */
    public function getPianoCombination()
    {
        return $this->pianoCombination;
    }

    /**
     * gets if published item is a piano reduction
     *
     * @return boolean
     */
    public function getIsPianoReduction()
    {
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mpdb_core');

        $pianoId = $extConf['pianoGndId'];
        $pianoIsLinked = false;
        foreach($this->instruments as $instrument)
            if ($instrument->getGndId() == $pianoId)
                $pianoIsLinked = true;

        return $this->type == "adaptation" && $pianoIsLinked;
    }

    /**
     * Sets the mvdbId
     * 
     * @param string $mvdbId
     * @return void
     */
    public function setMvdbId()
    {
        $this->setPlateIds();
        $minPlateId = $this->getPlateIds() ? min($this->getPlateIds()) : '';
        $this->mvdbId = $this->publisher->getShorthand() .
            '_' . $minPlateId;

        foreach($this->publishedSubitems as $publishedSubitem) {
            $publishedSubitem->setMvdbId(
                $this->mvdbId . '_' .
                $publishedSubitem->getPlateId() . '_' .
                $publishedSubitem->getPart() . '_' .
                $publishedSubitem->getVoice());
        }
    }

    /**
     * Returns the type
     * 
     * @return string type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     * 
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * __construct
     */
    public function __construct()
    {

        //Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    public function initializeObject()
    {
        $this->containedWorks = new ObjectStorage();
        $this->editors = new ObjectStorage();
        $this->instruments = new ObjectStorage();
        $this->form = new ObjectStorage();
        $this->firstComposer = new ObjectStorage();
        $this->publishedSubitems = new ObjectStorage();
    }

    /**
     * Returns the instrumentation
     * 
     * @return string $instrumentation
     */
    public function getInstrumentation()
    {
        return $this->instrumentation;
    }

    /**
     * Sets the instrumentation
     * 
     * @param string $instrumentation
     * @return void
     */
    public function setInstrumentation($instrumentation)
    {
        $this->instrumentation = $instrumentation;
    }

    /**
     * Returns the publisher
     * 
     * @return \Slub\MpdbCore\Domain\Model\Publisher $publisher
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Sets the publisher
     * 
     * @param \Slub\MpdbCore\Domain\Model\Publisher $publisher
     * @return void
     */
    public function setPublisher(Publisher $publisher = null)
    {
        $this->mvdbId = $this->getPlateIdFrom();
        if ($publisher !== null) {
            $this->mvdbId = $publisher->getShorthand() . '_' . $this->mvdbId;
        }
        $this->publisher = $publisher;
    }

    /**
     * Returns the title
     * 
     * @return string title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     * 
     * @param int $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Adds a PublishedSubitem
     * 
     * @param \Slub\PublisherDb\Domain\Model\PublishedSubitem $publishedSubitem
     * @return void
     */
    public function addPublishedSubitem(PublishedSubitem $publishedSubitem = null)
    {
        if ($publishedSubitem != null) {
            $this->publishedSubitems->attach($publishedSubitem);
        }
    }

    /**
     * Removes a PublishedSubitem
     * 
     * @param \Slub\MpdbCore\Domain\Model\PublishedSubitem $publishedSubitemToRemove
     * @return void
     */
    public function removePublishedSubitem(PublishedSubitem $publishedSubitemToRemove)
    {
        $this->publishedSubitems->detach($publishedSubitemToRemove);
    }

    /**
     * Adds a Work
     * 
     * @param \Slub\DmNorm\Domain\Model\GndWork $containedWork
     * @return void
     */
    public function addContainedWork(GndWork $containedWork = null)
    {
        if ($containedWork != null) {
            $this->containedWorks->attach($containedWork);
        }
    }

    /**
     * Removes a Work
     * 
     * @param \Slub\DmNorm\Domain\Model\GndWork $containedWorkToRemove The Work to be removed
     * @return void
     */
    public function removeContainedWork(GndWork $containedWorkToRemove)
    {
        $this->containedWorks->detach($containedWorkToRemove);
    }

    /**
     * Returns the publisherActions
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublisherActions> publisherActions
     */
    public function getPublisherActions()
    {
        return array_merge(
        ...array_map(
        function ($mikro) {
            return $this->publisherActionRepository->findByPublishedSubitem($mikro)->toArray();
        }, 
        $this->getPublishedSubitems()->toArray()
        )
        );
    }

    /**
     * Returns boolean indicating if there are publisherActions pointing
     * to the Makro
     * 
     * @return boolean
     */
    public function getHasPublisherActions()
    {
        $mikros = $this->getPublishedSubitems()->toArray();
        foreach ($mikros as $mikro) {
            if ($this->publisherActionRepository->findOneByPublishedSubitem($mikro)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a string indicating the number of Mikros
     * 
     * @return string
     */
    public function getNumberOfMikroString()
    {
        return $this->publishedSubitemRepository->getNumberOfMikroString($this);
    }

    /**
     * Returns the publishedSubitems
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublishedSubitem> $publishedSubitems
     */
    public function getPublishedSubitems()
    {
        return $this->publishedSubitems;
    }

    /**
     * Sets the publishedSubitems
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublishedSubitem> $publishedSubitems
     * @return void
     */
    public function setPublishedSubitems(ObjectStorage $publishedSubitems = null)
    {
        $publishedSubitems = $publishedSubitems ?? new ObjectStorage();
        $this->publishedSubitems = $publishedSubitems;
    }

    /**
     * Returns the containedWorks
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndWork> $containedWorks
     */
    public function getContainedWorks()
    {
        return $this->containedWorks;
    }

    /**
     * Sets the containedWorks
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndWork> $containedWorks
     * @return void
     */
    public function setContainedWorks(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $containedWorks = null)
    {
        $containedWorks = $containedWorks ?? new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->containedWorks = $containedWorks;
    }

    /**
     * Returns the plateIdFrom
     * 
     * @return string $plateIdFrom
     */
    public function getPlateIdFrom()
    {
        // ??
        return (new \SLUB\PublisherDb\Lib\DbArray())->set($this->getPublishedSubitems())->map(
        function ($mikro) {
            return $mikro->getPlateId();
        }
        )->reduce(
        function ($a, $b) {
            return $a < $b ? $a : $b;
        }, 
        '10000000'
        );
    }

    /**
     * Checks if current plateIdFrom is minimal plateId of contained Mikros
     * 
     * @param array $mikros
     * @return void
     */
    public function minPlateIdFromAdd(array $mikros)
    {
        // throw away!!
        $getPlateId = function(PublishedSubitem $publishedSubitem): string {
            return $publishedSubitem->getPlateId();
        };

        $this->plateIdFrom = (new DbArray())
            ->set( $mikros )
            ->map( $getPlateId )
            ->min();
        $this->updateIdentifier();
    }

    /**
     * Checks if current plateIdFrom is minimal plateId of contained Mikros
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $mikros
     * @param string $oldPlateId
     * @return void
     */
    public function minPlateIdFromRemove(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $mikros, string $oldPlateId)
    {
        // throw away!
        $min = '10000000';
        $encounteredSameNumber = false;
        foreach ($mikros as $mikro) {
            if (!$encounteredSameNumber && $mikro->getPlateId() != $oldPlateId) {
                continue;
            }
            if ($mikro->getPlateId() < $min) {
                $min = $mikro->getPlateId();
            }
        }
        $this->plateIdFrom = $min;
        $this->updateIdentifier();
    }

    public function updateMvdbId() {
        $getPlateId = function(PublishedSubitem $publishedSubitem): string {
            return $publishedSubitem->getPlateId();
        };

        $this->plateIdFrom = (new DbArray())
            ->set( $this->getPublishedSubitems()->toArray() )
            ->map( $getPlateId )
            ->min();

        $this->mvdbId = $this->plateIdFrom;
        if (!is_null($this->publisher)) {
            $this->mvdbId = $this->publisher->getShorthand() . '_' . $this->plateIdFrom;
        }
    }
    /**
     * Updates Identifier according to publisher name and plateIdFrom
     * 
     * @return void
     */
    public function updateIdentifier()
    {
        // throw away!
        $this->mvdbId = $this->plateIdFrom;
        if (!is_null($this->publisher)) {
            $this->mvdbId = $this->publisher->getShorthand() . '_' . $this->plateIdFrom;
        }
    }

    /**
     * Returns the dataAcquisitionCertain
     * 
     * @return bool $dataAcquisitionCertain
     */
    public function getDataAcquisitionCertain()
    {
        return $this->dataAcquisitionCertain;
    }

    /**
     * Sets the dataAcquisitionCertain
     * 
     * @param bool $dataAcquisitionCertain
     * @return void
     */
    public function setDataAcquisitionCertain($dataAcquisitionCertain)
    {
        $this->dataAcquisitionCertain = $dataAcquisitionCertain;
    }

    /**
     * Returns the boolean state of dataAcquisitionCertain
     * 
     * @return bool
     */
    public function isDataAcquisitionCertain()
    {
        return $this->dataAcquisitionCertain;
    }

    /**
     * Returns the relatedPersonsKnown
     * 
     * @return bool $relatedPersonsKnown
     */
    public function getRelatedPersonsKnown()
    {
        return $this->relatedPersonsKnown;
    }

    /**
     * Sets the relatedPersonsKnown
     * 
     * @param bool $relatedPersonsKnown
     * @return void
     */
    public function setRelatedPersonsKnown($relatedPersonsKnown)
    {
        $this->relatedPersonsKnown = $relatedPersonsKnown;
    }

    /**
     * Returns the boolean state of relatedPersonsKnown
     * 
     * @return bool
     */
    public function isRelatedPersonsKnown()
    {
        return $this->relatedPersonsKnown;
    }

    /**
     * Returns the workExamined
     * 
     * @return bool $workExamined
     */
    public function getWorkExamined()
    {
        return $this->workExamined;
    }

    /**
     * Sets the workExamined
     * 
     * @param bool $workExamined
     * @return void
     */
    public function setWorkExamined($workExamined)
    {
        $this->workExamined = $workExamined;
    }

    /**
     * Returns the boolean state of workExamined
     * 
     * @return bool
     */
    public function isWorkExamined()
    {
        return $this->workExamined;
    }

    /**
     * Returns the dataSetManuallyChecked
     * 
     * @return bool $dataSetManuallyChecked
     */
    public function getDataSetManuallyChecked()
    {
        return $this->dataSetManuallyChecked;
    }

    /**
     * Sets the dataSetManuallyChecked
     * 
     * @param bool $dataSetManuallyChecked
     * @return void
     */
    public function setDataSetManuallyChecked($dataSetManuallyChecked)
    {
        $this->dataSetManuallyChecked = $dataSetManuallyChecked;
    }

    /**
     * Returns the boolean state of dataSetManuallyChecked
     * 
     * @return bool
     */
    public function isDataSetManuallyChecked()
    {
        return $this->dataSetManuallyChecked;
    }

    /**
     * Returns the containedWorksIdentified
     * 
     * @return bool $containedWorksIdentified
     */
    public function getContainedWorksIdentified()
    {
        return $this->containedWorksIdentified;
    }

    /**
     * Sets the containedWorksIdentified
     * 
     * @param bool $containedWorksIdentified
     * @return void
     */
    public function setContainedWorksIdentified($containedWorksIdentified)
    {
        $this->containedWorksIdentified = $containedWorksIdentified;
    }

    /**
     * Returns the boolean state of containedWorksIdentified
     * 
     * @return bool
     */
    public function isContainedWorksIdentified()
    {
        return $this->containedWorksIdentified;
    }

    /**
     * Returns the responsiblePerson
     * 
     * @return string $responsiblePerson
     */
    public function getResponsiblePerson()
    {
        return $this->responsiblePerson;
    }

    /**
     * Sets the responsiblePerson
     * 
     * @param string $responsiblePerson
     * @return void
     */
    public function setResponsiblePerson($responsiblePerson)
    {
        $this->responsiblePerson = $responsiblePerson;
    }

    /**
     * Returns the dateOfPublishing
     * 
     * @return \DateTime $dateOfPublishing
     */
    public function getDateOfPublishing()
    {
        return $this->dateOfPublishing;
    }

    /**
     * Sets the dateOfPublishing
     * 
     * @param \DateTime $dateOfPublishing
     * @return void
     */
    public function setDateOfPublishing(\DateTime $dateOfPublishing)
    {
        $this->dateOfPublishing = $dateOfPublishing;
    }

    /**
     * Returns the mvdbId
     * 
     * @return string mvdbId
     */
    public function getMvdbId()
    {
        return $this->mvdbId;
    }

    /**
     * Adds an Instrument
     * 
     * @param \Slub\DmOnt\Domain\Model\Instrument $instrument
     * @return void
     */
    public function addInstrument($instrument)
    {
        $this->instruments->attach($instrument);
    }

    /**
     * Removes a
     * 
     * @param \Slub\DmOnt\Domain\Model\Instrument $instrumentToRemove The Instrument to be removed
     * @return void
     */
    public function removeInstrument($instrumentToRemove)
    {
		$extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mpdb_core');
        if ($instrumentToRemove->getGndId() == $extConf['pianoGndId']) {
            $this->pianoCombination = "";
        }
        $this->instruments->detach($instrumentToRemove);
    }

    /**
     * Returns the instruments
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Instrument> instruments
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * Sets the instruments
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Instrument> $instruments
     * @return void
     */
    public function setInstruments(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $instruments)
    {
        $this->instruments = $instruments;
    }

    /**
     * Adds a
     * 
     * @param \Slub\DmOnt\Domain\Model\Genre $genre
     * @return void
     */
    public function addForm($form)
    {
        $this->form->attach($form);
    }

    /**
     * Removes a
     * 
     * @param \Slub\DmOnt\Domain\Model\Genre $formToRemove The Form to be removed
     * @return void
     */
    public function removeForm($formToRemove)
    {
        $this->form->detach($formToRemove);
    }

    /**
     * Returns the form
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Genre> form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Sets the form
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Genre> $form
     * @return void
     */
    public function setForm(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $form)
    {
        $this->form = $form;
    }

    /**
     * Adds a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $editor
     * @return void
     */
    public function addEditor(GndPerson $editor)
    {
        $this->editors->attach($editor);
    }

    /**
     * Removes a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $editorToRemove
     * @return void
     */
    public function removeEditor(GndPerson $editorToRemove)
    {
        $this->editors->detach($editorToRemove);
    }

    /**
     * Returns the editors
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson>
     */
    public function getEditors()
    {
        return $this->editors;
    }

    /**
     * Sets the editors
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson> $editors
     * @return void
     */
    public function setEditors(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $editors)
    {
        $this->editors = $editors;
    }

    /**
     * Returns the final
     * 
     * @return int final
     */
    public function getFinal()
    {
        return $this->final;
    }

    /**
     * Sets the final
     * 
     * @param bool $final
     * @return void
     */
    public function setFinal($final)
    {
        $works = (new \SLUB\PublisherDb\Lib\DbArray())->set($this->getContainedWorks()->toArray());
        $works->each(
        function ($work) use($final) {
            $work->updateFinal($final, $this);
            $this->gndWorkRepository->update($work);
        }
        );
        $this->final = $final;
    }

    /**
     * Returns the language
     * 
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the language
     * 
     * @param string $language
     * @return void
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Adds a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $firstComposer
     * @return void
     */
    public function addFirstComposer(GndPerson $firstComposer)
    {
        $this->firstComposer->attach($firstComposer);
    }

    /**
     * Removes a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $firstComposerToRemove
     * @return void
     */
    public function removeFirstComposer(GndPerson $firstComposerToRemove)
    {
        $this->firstComposer->detach($firstComposerToRemove);
    }

    /**
     * Returns the firstComposer
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson> $firstComposer
     */
    public function getFirstComposer()
    {
        return $this->firstComposer;
    }

    /**
     * Sets the firstComposer
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson> $firstComposer
     * @return void
     */
    public function setFirstComposer(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $firstComposer)
    {
        $this->firstComposer = $firstComposer;
    }

    /**
     * Returns firstComposer if available, comma separated list of composers of contained works if not
     * 
     * @return string
     */
    public function getComposers()
    {
        if ($this->firstComposer && $this->firstComposer->toArray() != []) {
            return (new \SLUB\PublisherDb\Lib\DbArray())->set($this->firstComposer)->map(
            function ($composer) {
                return $composer->getName();
            }
            )->unique()->implode('; ');
        }
        return (new \SLUB\PublisherDb\Lib\DbArray())->set($this->getContainedWorks()->toArray())->map(
        function ($work) {
            return $work->getFirstComposer() ? $work->getFirstComposer()->getName() : '';
        }
        )->filter(
        function ($name) {
            return $name != '';
        }
        )->unique()->implode('; ');
    }

    /**
     * Returns the plateIds
     * 
     * @return string $plateIds
     */
    public function getPlateIds()
    {
        return json_decode($this->plateIds);
    }

    /**
     * Returns a string for plateIds
     * 
     * @return string $plateIdsString
     */
    public function getPlateIdsString()
    {
        return implode("\n", $this->getPlateIds());
    }

    /**
     * Returns the comment
     * 
     * @return string $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment
     * 
     * @param string $comment
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Sets the plateIds
     * 
     * @param string $plateIds
     * @return void
     */
    public function setPlateIds()
    {
        $plateIds = [];

        foreach($this->publishedSubitems as $publishedSubitem) {
            if(!in_array($publishedSubitem->getPlateId(), $plateIds)) {
                $plateIds[] = $publishedSubitem->getPlateId();
            }
        }

        $this->plateIds = json_encode($plateIds);
    }

    /**
     * Returns bool indicating if there is a PublishedSubitem not
     * Linking a workRepository
     * 
     * @return bool
     */
    public function getHasMikroWithoutWork()
    {
        $hasNoLinkedWorks = function (PublishedSubitem $mikro) {
            return $mikro->getContainedWorks()->toArray() == [];
        };
        $or = function (bool $a, bool $b) {
            return $a || $b;
        };
        return (new DbArray())->set($this->getPublishedSubitems())->map($hasNoLinkedWorks)->reduce($or, false);
    }

    // TODO
    public function proposeDataAcquisitionCertain()
    {
        return false;
    }
}
