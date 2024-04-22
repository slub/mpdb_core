<?php
namespace Slub\MpdbCore\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
 * Publisher
 */
class Publisher extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     * 
     * @var string
     */
    protected $name = '';

    /**
     * shorthand
     * 
     * @var string
     */
    protected $shorthand = '';

    /**
     * location
     * 
     * @var string
     */
    protected $location = '';

    /**
     * alternateName
     * 
     * @var string
     */
    protected $alternateName = '';

    /**
     * activeFrom
     * 
     * @var \DateTime
     */
    protected $activeFrom = null;

    /**
     * activeTo
     * 
     * @var \DateTime
     */
    protected $activeTo = null;

    /**
     * publishedItems
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\MpdbCore\Domain\Model\PublishedItem>
     */
    protected $publishedItems = null;

    /**
     * responsiblePersons
     * 
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\DmNorm\Domain\Model\GndPerson>
     */
    protected $responsiblePersons = null;

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
        $this->responsiblePersons = new ObjectStorage();
        $this->publishedItems = new ObjectStorage();
    }

    /**
     * Returns the name
     * 
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the shorthand
     * 
     * @return string $shorthand
     */
    public function getShorthand()
    {
        return $this->shorthand;
    }

    /**
     * Sets the shorthand
     * 
     * @param string $shorthand
     * @return void
     */
    public function setShorthand($shorthand)
    {
        $this->shorthand = $shorthand;
    }

    /**
     * Returns the location
     * 
     * @return string $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the location
     * 
     * @param string $location
     * @return void
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Returns the alternateName
     * 
     * @return string $alternateName
     */
    public function getAlternateName()
    {
        return $this->alternateName;
    }

    /**
     * Sets the alternateName
     * 
     * @param string $alternateName
     * @return void
     */
    public function setAlternateName($alternateName)
    {
        $this->alternateName = $alternateName;
    }

    /**
     * Returns the activeFrom
     * 
     * @return \DateTime $activeFrom
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Sets the activeFrom
     * 
     * @param \DateTime $activeFrom
     * @return void
     */
    public function setActiveFrom(\DateTime $activeFrom = null)
    {
        $this->activeFrom = $activeFrom;
    }

    /**
     * Returns the activeTo
     * 
     * @return \DateTime $activeTo
     */
    public function getActiveTo()
    {
        return $this->activeTo;
    }

    /**
     * Sets the activeTo
     * 
     * @param \DateTime $activeTo
     * @return void
     */
    public function setActiveTo(\DateTime $activeTo = null)
    {
        $this->activeTo = $activeTo;
    }

    /**
     * Returns the publisherMakroItems
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\PublisherMakroItem> $publisherMakroItems
     */
    public function getPublishedItems()
    {
        return $this->publishedItems;
    }

    /**
     * Sets the publisherMakroItems
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SLUB\PublisherDb\Domain\Model\PublisherMakroItem> $publisherMakroItems
     * @return void
     */
    public function setPublisherMakroItems(ObjectStorage $publisherMakroItems = null)
    {
        $publisherMakroItems = $publisherMakroItems ?? new ObjectStorage();
        $this->publishedItems = $publisherMakroItems;
    }

}
