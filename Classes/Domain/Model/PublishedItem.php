<?php
namespace Slub\MpdbCore\Domain\Model;

use Illuminate\Support\Collection;
use Slub\DmNorm\Domain\Model\GndWork;
use Slub\DmNorm\Domain\Model\GndPerson;
use Slub\DmOnt\Domain\Model\Instrument;
use Slub\DmOnt\Domain\Model\Genre;
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
    public function setPianoCombination($pianoCombination): void
    {
        if (in_array($pianoCombination, array_keys(self::pianoCombinations)))
            $this->pianoCombination = $pianoCombination;
    }

    /**
     * gets pianoCombination
     *
     * @return void
     */
    public function getPianoCombination(): string
    {
        return $this->pianoCombination;
    }

    /**
     * gets if published item is a piano reduction
     *
     * @return bool
     */
    public function getIsPianoReduction(): bool
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
    public function setMvdbId(): void
    {
        $minPlateId = $this->getPlateIds()->min();
        $this->mvdbId = $this->publisher->getShorthand() .
            '_' . $minPlateId;

        Collection::wrap($this->publishedSubitems)->
            each( function($subitem) { $this->setSubitemMvdbId($subitem); } );
    }

    protected function setSubitemMvdbId(PublishedSubitem $subitem): void
    {
        $subitem->setMvdbId(
            $this->mvdbId . '_' .
            $subitem->getPlateId() . '_' .
            $subitem->getPart() . '_' .
            $subitem->getVoice());
    }

    /**
     * Returns the type
     * 
     * @return string type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type
     * 
     * @param int $type
     * @return void
     */
    public function setType($type): void
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

    public function initializeObject(): void
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
    public function getInstrumentation(): string
    {
        return $this->instrumentation;
    }

    /**
     * Sets the instrumentation
     * 
     * @param string $instrumentation
     * @return void
     */
    public function setInstrumentation($instrumentation): void
    {
        $this->instrumentation = $instrumentation;
    }

    /**
     * Returns the publisher
     * 
     * @return \Slub\MpdbCore\Domain\Model\Publisher $publisher
     */
    public function getPublisher(): Publisher
    {
        return $this->publisher;
    }

    /**
     * Sets the publisher
     * 
     * @param \Slub\MpdbCore\Domain\Model\Publisher $publisher
     * @return void
     */
    public function setPublisher(Publisher $publisher = null): void
    {
        $this->publisher = $publisher;
        $this->setMvdbId();
    }

    /**
     * Returns the title
     * 
     * @return string title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title
     * 
     * @param int $title
     * @return void
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * Adds a PublishedSubitem
     * 
     * @param \Slub\PublisherDb\Domain\Model\PublishedSubitem $publishedSubitem
     * @return void
     */
    public function addPublishedSubitem(PublishedSubitem $publishedSubitem = null): void
    {
        if ($publishedSubitem != null) {
            $this->publishedSubitems->attach($publishedSubitem);
        }
        $this->setMvdbId();
    }

    /**
     * Removes a PublishedSubitem
     * 
     * @param \Slub\MpdbCore\Domain\Model\PublishedSubitem $publishedSubitemToRemove
     * @return void
     */
    public function removePublishedSubitem(PublishedSubitem $publishedSubitemToRemove): void
    {
        $this->publishedSubitems->detach($publishedSubitemToRemove);
    }

    /**
     * Adds a Work
     * 
     * @param \Slub\DmNorm\Domain\Model\GndWork $containedWork
     * @return void
     */
    public function addContainedWork(GndWork $containedWork = null): void
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
    public function removeContainedWork(GndWork $containedWorkToRemove): void
    {
        $this->containedWorks->detach($containedWorkToRemove);
    }

    /**
     * Returns the publisherActions
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublisherActions> publisherActions
     */
    public function getPublisherActions(): ObjectStorage
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
     * @return bool
     */
    public function getHasPublisherActions(): bool
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
    public function getNumberOfMikroString(): string
    {
        return $this->publishedSubitems->count();
    }

    /**
     * Returns the publishedSubitems
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublishedSubitem> $publishedSubitems
     */
    public function getPublishedSubitems(): ObjectStorage
    {
        return $this->publishedSubitems;
    }

    /**
     * Sets the publishedSubitems
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\MpdbCore\Domain\Model\PublishedSubitem> $publishedSubitems
     * @return void
     */
    public function setPublishedSubitems(ObjectStorage $publishedSubitems = null): void
    {
        $publishedSubitems = $publishedSubitems ?? new ObjectStorage();
        $this->publishedSubitems = $publishedSubitems;
    }

    /**
     * Returns the containedWorks
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndWork> $containedWorks
     */
    public function getContainedWorks(): ObjectStorage
    {
        return $this->containedWorks;
    }

    /**
     * Sets the containedWorks
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndWork> $containedWorks
     * @return void
     */
    public function setContainedWorks(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $containedWorks = null): void
    {
        $containedWorks = $containedWorks ?? new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->containedWorks = $containedWorks;
    }

    /**
     * Returns the dataAcquisitionCertain
     * 
     * @return bool $dataAcquisitionCertain
     */
    public function getDataAcquisitionCertain(): bool
    {
        return $this->dataAcquisitionCertain;
    }

    /**
     * Sets the dataAcquisitionCertain
     * 
     * @param bool $dataAcquisitionCertain
     * @return void
     */
    public function setDataAcquisitionCertain($dataAcquisitionCertain): void
    {
        $this->dataAcquisitionCertain = $dataAcquisitionCertain;
    }

    /**
     * Returns the boolean state of dataAcquisitionCertain
     * 
     * @return bool
     */
    public function isDataAcquisitionCertain(): bool
    {
        return $this->dataAcquisitionCertain;
    }

    /**
     * Returns the relatedPersonsKnown
     * 
     * @return bool $relatedPersonsKnown
     */
    public function getRelatedPersonsKnown(): bool
    {
        return $this->relatedPersonsKnown;
    }

    /**
     * Sets the relatedPersonsKnown
     * 
     * @param bool $relatedPersonsKnown
     * @return void
     */
    public function setRelatedPersonsKnown($relatedPersonsKnown): void
    {
        $this->relatedPersonsKnown = $relatedPersonsKnown;
    }

    /**
     * Returns the boolean state of relatedPersonsKnown
     * 
     * @return bool
     */
    public function isRelatedPersonsKnown(): bool
    {
        return $this->relatedPersonsKnown;
    }

    /**
     * Returns the workExamined
     * 
     * @return bool $workExamined
     */
    public function getWorkExamined(): bool
    {
        return $this->workExamined;
    }

    /**
     * Sets the workExamined
     * 
     * @param bool $workExamined
     * @return void
     */
    public function setWorkExamined($workExamined): void
    {
        $this->workExamined = $workExamined;
    }

    /**
     * Returns the boolean state of workExamined
     * 
     * @return bool
     */
    public function isWorkExamined(): bool
    {
        return $this->workExamined;
    }

    /**
     * Returns the dataSetManuallyChecked
     * 
     * @return bool $dataSetManuallyChecked
     */
    public function getDataSetManuallyChecked(): bool
    {
        return $this->dataSetManuallyChecked;
    }

    /**
     * Sets the dataSetManuallyChecked
     * 
     * @param bool $dataSetManuallyChecked
     * @return void
     */
    public function setDataSetManuallyChecked(bool $dataSetManuallyChecked): void
    {
        $this->dataSetManuallyChecked = $dataSetManuallyChecked;
    }

    /**
     * Returns the boolean state of dataSetManuallyChecked
     * 
     * @return bool
     */
    public function isDataSetManuallyChecked(): bool
    {
        return $this->dataSetManuallyChecked;
    }

    /**
     * Returns the containedWorksIdentified
     * 
     * @return bool $containedWorksIdentified
     */
    public function getContainedWorksIdentified(): bool
    {
        return $this->containedWorksIdentified;
    }

    /**
     * Sets the containedWorksIdentified
     * 
     * @param bool $containedWorksIdentified
     * @return void
     */
    public function setContainedWorksIdentified(bool $containedWorksIdentified): void
    {
        $this->containedWorksIdentified = $containedWorksIdentified;
    }

    /**
     * Returns the boolean state of containedWorksIdentified
     * 
     * @return bool
     */
    public function isContainedWorksIdentified(): bool
    {
        return $this->containedWorksIdentified;
    }

    /**
     * Returns the responsiblePerson
     * 
     * @return string $responsiblePerson
     */
    public function getResponsiblePerson(): GndPerson
    {
        return $this->responsiblePerson;
    }

    /**
     * Sets the responsiblePerson
     * 
     * @param string $responsiblePerson
     * @return void
     */
    public function setResponsiblePerson(GndPerson $responsiblePerson): void
    {
        $this->responsiblePerson = $responsiblePerson;
    }

    /**
     * Returns the dateOfPublishing
     * 
     * @return \DateTime $dateOfPublishing
     */
    public function getDateOfPublishing(): \DateTime
    {
        return $this->dateOfPublishing;
    }

    /**
     * Sets the dateOfPublishing
     * 
     * @param \DateTime $dateOfPublishing
     * @return void
     */
    public function setDateOfPublishing(\DateTime $dateOfPublishing): void
    {
        $this->dateOfPublishing = $dateOfPublishing;
    }

    /**
     * Returns the mvdbId
     * 
     * @return string mvdbId
     */
    public function getMvdbId(): string
    {
        return $this->mvdbId;
    }

    /**
     * Adds an Instrument
     * 
     * @param \Slub\DmOnt\Domain\Model\Instrument $instrument
     * @return void
     */
    public function addInstrument(Instrument $instrument): void
    {
        $this->instruments->attach($instrument);
    }

    /**
     * Removes a
     * 
     * @param \Slub\DmOnt\Domain\Model\Instrument $instrumentToRemove The Instrument to be removed
     * @return void
     */
    public function removeInstrument(Instrument $instrumentToRemove): void
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
    public function getInstruments(): ObjectStorage
    {
        return $this->instruments;
    }

    /**
     * Sets the instruments
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Instrument> $instruments
     * @return void
     */
    public function setInstruments(ObjectStorage $instruments): void
    {
        $this->instruments = $instruments;
    }

    /**
     * Adds a
     * 
     * @param \Slub\DmOnt\Domain\Model\Genre $genre
     * @return void
     */
    public function addForm(Genre $form): void
    {
        $this->form->attach($form);
    }

    /**
     * Removes a
     * 
     * @param \Slub\DmOnt\Domain\Model\Genre $formToRemove The Form to be removed
     * @return void
     */
    public function removeForm(Genre $formToRemove): void
    {
        $this->form->detach($formToRemove);
    }

    /**
     * Returns the form
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Genre> form
     */
    public function getForm(): Genre
    {
        return $this->form;
    }

    /**
     * Sets the form
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmOnt\Domain\Model\Genre> $form
     * @return void
     */
    public function setForm(ObjectStorage $form): void
    {
        $this->form = $form;
    }

    /**
     * Adds a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $editor
     * @return void
     */
    public function addEditor(GndPerson $editor): void
    {
        $this->editors->attach($editor);
    }

    /**
     * Removes a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $editorToRemove
     * @return void
     */
    public function removeEditor(GndPerson $editorToRemove): void
    {
        $this->editors->detach($editorToRemove);
    }

    /**
     * Returns the editors
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson>
     */
    public function getEditors(): ObjectStorage
    {
        return $this->editors;
    }

    /**
     * Sets the editors
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson> $editors
     * @return void
     */
    public function setEditors(ObjectStorage $editors): void
    {
        $this->editors = $editors;
    }

    /**
     * Returns the final
     * 
     * @return int final
     */
    public function getFinal(): int
    {
        return $this->final;
    }

    /**
     * Sets the final
     * 
     * @param bool $final
     * @return void
     */
    public function setFinal(bool $final): void
    {
        $this->final = $final;
        Collection::wrap($this->getContainedWorks()->toArray())->
            each( function($work) { $this->updateFinalWork($work); } );
    }

    protected function updateFinalWork(GndWork $work): void
    {
        $work->updateFinal($this->final, $this);
        $this->gndWorkRepository->update($work);
    }


    /**
     * Returns the language
     * 
     * @return string $language
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Sets the language
     * 
     * @param string $language
     * @return void
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    /**
     * Adds a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $firstComposer
     * @return void
     */
    public function addFirstComposer(GndPerson $firstComposer): void
    {
        $this->firstComposer->attach($firstComposer);
    }

    /**
     * Removes a Person
     * 
     * @param \Slub\DmNorm\Domain\Model\GndPerson $firstComposerToRemove
     * @return void
     */
    public function removeFirstComposer(GndPerson $firstComposerToRemove): void
    {
        $this->firstComposer->detach($firstComposerToRemove);
    }

    /**
     * Returns the firstComposer
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson> $firstComposer
     */
    public function getFirstComposer(): ObjectStorage
    {
        return $this->firstComposer;
    }

    /**
     * Sets the firstComposer
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Slub\DmNorm\Domain\Model\GndPerson> $firstComposer
     * @return void
     */
    public function setFirstComposer(ObjectStorage $firstComposer): void
    {
        $this->firstComposer = $firstComposer;
    }

    /**
     * Returns firstComposer if available, comma separated list of composers of contained works if not
     * 
     * @return string
     */
    public function getComposers(): string
    {
        $composers = Collection::wrap($this->firstComposer);
        if ($composers->count() > 0) {
            return $composers->map( function ($composer) { return self::getComposerName($composer); } )->
                unique()->
                join('; ');
        }
        return Collection::wrap($this->containedWorks->toArray())->
            map( function($work) { return self::getWorkComposerName($work); } )->
            unique()->
            join(';');
    }

    protected static function getComposerName(GndPerson $composer): string
    {
        return $composer->getName();
    }

    protected static function getWorkComposerName(GndWork $work): string
    {
        return self::getComposerName($work->getFirstComposer());
    }

    /**
     * Returns the plateIds
     * 
     * @return string $plateIds
     */
    public function getPlateIds(): Collection
    {
        return Collection::wrap($this->publishedSubitems)->
            map( function($subitem) { return self::getSubitemPlateId($subitem); } );
    }

    /**
     * Returns the plateIds as a newline separated string
     * 
     * @return string $plateIds
     */
    public function getPlateIdsString(): string
    {
        return $this->getPlateIds()->join('\n');
    }

    protected static function getSubitemPlateId(PublishedSubitem $subitem): string
    {
        return $subitem->getPlateId();
    }

    /**
     * Returns the comment
     * 
     * @return string $comment
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Sets the comment
     * 
     * @param string $comment
     * @return void
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * Returns bool indicating if there is a PublishedSubitem not
     * Linking a workRepository
     * 
     * @return bool
     */
    public function getHasMikroWithoutWork(): bool
    {
        return Collection::wrap($this->publishedSubitems)->
            map(function ($subitem) { return self::subitemWithoutWorks($subitem); } )->
            reduce(function ($a, $b) { return self::or($a, $b); } );
    }

    protected static function subitemWithoutWorks(PublishedSubitem $subitem): bool
    {
        return $subitem->getContainedWorks()->count() > 0;
    }

    protected static function or(bool $a, bool $b): bool
    {
        return $a || $b;
    }
}
