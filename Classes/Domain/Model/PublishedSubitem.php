<?php
namespace Slub\MpdbCore\Domain\Model;

use \TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * PublisherItem
 */
class PublishedSubitem extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    const PARTMAP = array('NN' => 'nicht nummeriert', 'N' => 'Nummer', 'H' => 'Heft', 'Bd' => 'Band', 'Ouv' => 'Ouvertüre', 'Vsp' => 'Vorspiel', 'NE' => 'Einzelnummern', 'Text' => 'Textband', 'Engl' => 'Englisch', 'Franz' => 'Französisch', 'Ital' => 'Italienisch', 'Dt' => 'Deutsch');
    const SINGLEVOICEMAP = array('N' => 'undifferenziert', 'Cplt' => 'Complett', 'PStC' => 'Partitur und Stimmen', 'P' => 'Partitur', 'K' => 'Klavier (2hd.)', 'K4h' => 'Klavier (4hd.)', '2K4h' => '2 Klaviere (4hd.)', '2K8h' => '2 Klaviere (8hd.)', 'KA' => 'Klavierauszug', 'KA4h' => 'Klavierauszug 4-händig', 'KAmT' => 'Klavierauszug mit Text', 'KAoT' => 'Klavierauszug ohne Text', 'StC' => 'Kompletter Stimmensatz', 'PCh' => 'Chorstimmenpartitur', 'Or' => 'Orchester', 'POr' => 'Orchesterstimmenpartitur', 'PQu' => 'Quartettstimmenpartitur', 'KAB' => 'Klavierauszug Begleitung', 'StCOr' => 'Kompletter Orchesterstimmensatz', 'StCQu' => 'Kompletter Quartettstimmensatz', 'StCCh' => 'Kompletter Chorstimmensatz', 'StCChSATB' => 'Kompletter Chorstimmensatz SATB', 'StCChTTBB' => 'Kompletter Chorstimmensatz TTBB', 'StCSolo' => 'Kompletter Solostimmensatz', 'StInc' => 'Inkompletter Stimmensatz', 'StInst' => 'Instrumentalstimmensatz', 'StOber' => 'Oberstimmensatz', 'hS' => 'Ausgabe für Hohe Stimme', 'mS' => 'Ausgabe für Mittlere Stimme', 'tS' => 'Ausgabe für Tiefe Stimme', 'tA' => 'Ausgabe für Tiefen Alt', 'TrA' => 'Transponierte Ausgabe', 'Nbsp' => 'Notenbeispiele', 'Kad' => 'Kadenzen');
    const MULTIVOICEMAP = array('ESt' => 'Einzelstimme', 'Solo' => 'Solostimme', 'A' => 'Alt', 'Blä' => 'Bläsersatz', 'Ba' => 'Bariton', 'BC' => 'Basso Continuo', 'BT' => 'Bassi Tutti', 'B' => 'Bass', 'C' => 'Cembalo', 'Div' => 'Diverse', 'Fg' => 'Fagott', 'Fl' => 'Flöte', 'Gi' => 'Gitarre', 'Gs' => 'Gesang (Solo)', 'Hr' => 'Horn', 'Ha' => 'Harfe', 'K4h' => 'Klavier 4-händig', 'Kl' => 'Klarinette', 'Kb' => 'Kontrabass', 'K' => 'Klavier', 'Ma' => 'Mandola', 'Ml' => 'Mandoline', 'Ms' => 'Mezzosopran', 'Ob' => 'Oboe', 'Or' => 'Orgel', 'Prc' => 'Schlag-/Percussionstimen', 'Pa' => 'Pauke', 'Pi' => 'Piccoloflöte', 'Po' => 'Posaune', 'Qui' => 'Quintettstimmen', 'Qu' => 'Quartettstimmen', 'Str' => 'Streichersatz', 'St' => 'Singstimme', 'S' => 'Sopran', 'TnTm' => 'Triangel und Tamburin', 'Tm' => 'Tamburin', 'Ti' => 'Timpani', 'Tn' => 'Triangel', 'Tr' => 'Trompete', 'Tu' => 'Tuba', 'T' => 'Tenor', 'VaA' => 'Viola alta', 'Va' => 'Viola', 'Vl' => 'Violine', 'Vc' => 'Violoncello', 'Zi' => 'Zither');

    /**
     * mvdbId
     * 
     * @var string
     */
    protected $mvdbId = '';

    /**
     * plateId
     * 
     * @var string
     */
    protected $plateId = '';

    /**
     * part
     * 
     * @var string
     */
    protected $part = '';

    /**
     * voice
     * 
     * @var string
     */
    protected $voice = '';

    /**
     * price
     * 
     * @var int
     */
    protected $price = 0;

    /**
     * comment
     * 
     * @var string
     */
    protected $comment = '';

    /**
     * isPianoReduction
     * 
     * @var bool
     */
    protected $isPianoReduction = false;

    /**
     * pianoReductionType
     * 
     * @var string
     */
    protected $pianoReductionType = '';

    /**
     * dateOfPublishing
     * 
     * @var \DateTime
     */
    protected $dateOfPublishing = null;

    /**
     * hasNegativeStore
     * 
     * @var bool
     */
    protected $hasNegativeStore = false;

    /**
     * approveNegativeStore
     * 
     * @var bool
     */
    protected $approveNegativeStore = false;

    /**
     * startStore
     * 
     * @var int
     */
    protected $startStore = 0;

    /**
     * containedWorks
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\Work>
     */
    protected $containedWorks = null;

    /**
     * publisherActions
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\PublisherAction>
     */
    protected $publisherActions = null;

    /**
     * Returns the mvdbId
     * 
     * @return string $mvdbId
     */
    public function getMvdbId()
    {
        return $this->mvdbId;
    }

    /**
     * Sets the mvdbId
     * 
     * @param string $mvdbId
     * @return void
     */
    public function setMvdbId($mvdbId)
    {
        $this->mvdbId = $mvdbId;
    }

    /**
     * Returns the plateId
     * 
     * @return string $plateId
     */
    public function getPlateId()
    {
        return $this->plateId;
    }

    /**
     * Sets the plateId
     * 
     * @param string $plateId
     * @return void
     */
    public function setPlateId($plateId)
    {
        $this->plateId = $plateId;
    }

    /**
     * Returns the part
     * 
     * @return string $part
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Sets the part
     * 
     * @param string $part
     * @return void
     */
    public function setPart($part)
    {
        $this->part = $part;
    }

    /**
     * Returns the voice
     * 
     * @return string $voice
     */
    public function getVoice()
    {
        return $this->voice;
    }

    /**
     * Sets the voice
     * 
     * @param string $voice
     * @return void
     */
    public function setVoice($voice)
    {
        $this->voice = $voice;
    }

    /**
     * __construct
     */
    public function __construct()
    {

        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     * 
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->containedWorks = new ObjectStorage();
    }

    /**
     * Returns the dbIdentifier
     * 
     * @return string dbIdentifier
     */
    public function getDbIdentifier()
    {
        return $this->mvdbId;
    }

    /**
     * Returns the price
     * 
     * @return int $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets the price
     * 
     * @param int $price
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     * Adds a PublisherAction
     * 
     * @param \SLUB\PublisherDb\Domain\Model\PublisherAction $publisherAction
     * @return void
     */
    public function addPublisherAction(\SLUB\PublisherDb\Domain\Model\PublisherAction $publisherAction = null)
    {
        if ($publisherAction != null) {
            $this->publisherActions->attach($publisherAction);
        }
    }

    /**
     * Removes a PublisherAction
     * 
     * @param \SLUB\PublisherDb\Domain\Model\PublisherAction $publisherActionToRemove The PublisherAction to be removed
     * @return void
     */
    public function removePublisherAction(\SLUB\PublisherDb\Domain\Model\PublisherAction $publisherActionToRemove)
    {
        $this->publisherActions->detach($publisherActionToRemove);
    }

    /**
     * Returns the publisherActions
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\PublisherAction> publisherActions
     */
    public function getPublisherActions()
    {
        return $this->publisherActions;
    }

    /**
     * Sets the publisherActions
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\PublisherAction> $publisherActions
     * @return void
     */
    public function setPublisherActions(ObjectStorage $publisherActions = null)
    {
        $publisherActions = $publisherActions ?? new ObjectStorage();
        $this->publisherActions = $publisherActions;
    }

    /**
     * Adds a Work
     * 
     * @param \SLUB\PublisherDb\Domain\Model\Work $containedWork
     * @return void
     */
    public function addContainedWork(\SLUB\PublisherDb\Domain\Model\Work $containedWork = null)
    {
        if ($containedWork != null) {
            $this->containedWorks->attach($containedWork);
        }
    }

    /**
     * Removes a Work
     * 
     * @param \SLUB\PublisherDb\Domain\Model\Work $containedWorkToRemove The Work to be removed
     * @return void
     */
    public function removeContainedWork(\SLUB\PublisherDb\Domain\Model\Work $containedWorkToRemove)
    {
        $this->containedWorks->detach($containedWorkToRemove);
    }

    /**
     * Returns the containedWorks
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\Work> containedWorks
     */
    public function getContainedWorks()
    {
        return $this->containedWorks;
    }

    /**
     * Sets the containedWorks
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\Work> $containedWorks
     * @return void
     */
    public function setContainedWorks(ObjectStorage $containedWorks = null)
    {
        $containedWorks = $containedWorks ?? new ObjectStorage();
        $this->containedWorks = $containedWorks;
    }

    /**
     * Returns the isPianoReduction
     * 
     * @return bool $isPianoReduction
     */
    public function getIsPianoReduction()
    {
        return $this->isPianoReduction;
    }

    /**
     * Sets the isPianoReduction
     * 
     * @param bool $isPianoReduction
     * @return void
     */
    public function setIsPianoReduction($isPianoReduction)
    {
        $this->isPianoReduction = $isPianoReduction;
    }

    /**
     * Returns the boolean state of isPianoReduction
     * 
     * @return bool
     */
    public function isIsPianoReduction()
    {
        return $this->isPianoReduction;
    }

    /**
     * Returns the pianoReductionType
     * 
     * @return string $pianoReductionType
     */
    public function getPianoReductionType()
    {
        return $this->pianoReductionType;
    }

    /**
     * Sets the pianoReductionType
     * 
     * @param string $pianoReductionType
     * @return void
     */
    public function setPianoReductionType($pianoReductionType)
    {
        $this->pianoReductionType = $pianoReductionType;
    }

    /**
     * Updates dateOfPublishing after adding an action
     * 
     * @param \DateTime $date
     * @return void
     */
    public function updateDateOfPublishingAdd(\DateTime $date)
    {
        if (!$this->dateOfPublishing) {
            $this->dateOfPublishing = $date;
        } else {
            if ($this->dateOfPublishing > $date) {
                $this->dateOfPublishing = $date;
            }
        }
    }

    /**
     * Updates dateOfPublishing after removing an action
     * 
     * @param \DateTime $dateToRemove
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $actions
     * @return void
     */
    public function updateDateOfPublishingRemove(
        \DateTime $dateToRemove, 
        ObjectStorage $actions
    )
    {
        if ($actions->count() == 1) {
            $this->dateOfPublishing = null;
        } else {
            if ($this->dateOfPublishing == $dateToRemove) {
                $this->dateOfPublishing = new \DateTime(

                    // calc min
                    min(

                        // subtract deleted date
                        array_diff(

                            // retrieve dates
                            array_map(
                            function ($action) {
                                return $action->getDateOfAction()->format('Y-m-d');
                            }, 
                            $actions->toArray()
                            ),
                            [$dateToRemove->format('Y-m-d')]
                        )
                    )
                );
            }
        }
    }

    /**
     * Returns the dateOfPublishing
     * 
     * @return \DateTime dateOfPublishing
     */
    public function getDateOfPublishing()
    {
        return $this->dateOfPublishing;
    }

    /**
     * Sets the dateOfPublishing
     * 
     * @param string $dateOfPublishing
     * @return void
     */
    public function setDateOfPublishing($dateOfPublishing)
    {
        $this->dateOfPublishing = $dateOfPublishing;
    }

    /**
     * Returns the hasNegativeStore
     * 
     * @return bool $hasNegativeStore
     */
    public function getHasNegativeStore()
    {
        return $this->hasNegativeStore;
    }

    /**
     * Sets the hasNegativeStore
     * 
     * @param bool $hasNegativeStore
     * @return void
     */
    public function setHasNegativeStore($hasNegativeStore)
    {
        $this->hasNegativeStore = $hasNegativeStore;
    }

    /**
     * Returns the boolean state of hasNegativeStore
     * 
     * @return bool
     */
    public function isHasNegativeStore()
    {
        return $this->hasNegativeStore;
    }

    /**
     * Returns the approveNegativeStore
     * 
     * @return bool $approveNegativeStore
     */
    public function getApproveNegativeStore()
    {
        return $this->approveNegativeStore;
    }

    /**
     * Sets the approveNegativeStore
     * 
     * @param bool $approveNegativeStore
     * @return void
     */
    public function setApproveNegativeStore($approveNegativeStore)
    {
        $this->approveNegativeStore = $approveNegativeStore;
    }

    /**
     * Returns the boolean state of approveNegativeStore
     * 
     * @return bool
     */
    public function isApproveNegativeStore()
    {
        return $this->approveNegativeStore;
    }

    /**
     * Returns the startStore
     * 
     * @return int $startStore
     */
    public function getStartStore()
    {
        return $this->startStore;
    }

    /**
     * Sets the startStore
     * 
     * @param int $startStore
     * @return void
     */
    public function setStartStore($startStore)
    {
        $this->startStore = $startStore;
    }

    /**
     * Returns a list of all composers of containedWorks
     * 
     * @return string
     */
    public function getComposers()
    {
        return (new \SLUB\PublisherDb\Lib\DbArray())->set($this->getContainedWorks()->toArray())->map(
        function ($work) {
            return $work->getFirstComposer() ? $work->getFirstComposer()->getName() : '';
        }
        )->filter(
        function ($name) {
            return $name != '';
        }
        )->unique()->implode(', ');
    }

    /**
     * Returns readable mikro information
     * 
     * @return string
     */
    public function getReadable()
    {
        $out = '';
        foreach (self::PARTMAP as $short => $long) {
            if (str_contains($this->part, $short)) {
                $out = str_replace($short, $long . ' ', $this->part);
                break;
            }
        }
        $out = trim($out) . ', ';
        $multi = true;
        foreach (self::SINGLEVOICEMAP as $short => $long) {
            if (str_contains($this->voice, $short)) {
                $multi = false;
                $out = $out . str_replace($short, $long . ' ', $this->voice);
                break;
            }
        }
        if ($multi) {
            $str = $this->voice;
            $buff = [];
            $i = 0;
            foreach (self::MULTIVOICEMAP as $short => $long) {
                if (str_contains($str, $short)) {
                    $buff[$i] = $long;
                    $str = str_replace($short, '.' . $i, $str);
                    $i = $i + 1;
                }
            }
            for ($a = 0; $a < $i; $a++) {
                if ($buff[$a] == 'ESt' || $buff[$a] == 'Solo') {
                    $str = str_replace('.' . $a, ', ' . $buff[$a], $str);
                } else {
                    $str = str_replace('.' . $a, ' ' . $buff[$a] . ' ', $str);
                }
            }
            $out = $out . trim($str, ', ');
        }
        return $out;
    }
}
