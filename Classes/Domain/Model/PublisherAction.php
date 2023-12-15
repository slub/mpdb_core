<?php
namespace Slub\MpdbCore\Domain\Model;


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
 * PublisherAction
 */
class PublisherAction extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * dateOfAction
     * 
     * @var \DateTime
     */
    protected $dateOfAction = null;

    /**
     * quantity
     * 
     * @var int
     */
    protected $quantity = 0;

    /**
     * type
     * 
     * @var string
     */
    protected $type = '';

    /**
     * certain
     * 
     * @var bool
     */
    protected $certain = false;

    /**
     * inferred
     * 
     * @var bool
     */
    protected $inferred = false;

    /**
     * inStore
     * 
     * @var bool
     */
    protected $inStore = false;

    /**
     * Returns the quantity
     * 
     * @return int $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets the quantity
     * 
     * @param int $quantity
     * @return void
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Returns the type
     * 
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     * 
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the certain
     * 
     * @return bool $certain
     */
    public function getCertain()
    {
        return $this->certain;
    }

    /**
     * Sets the certain
     * 
     * @param bool $certain
     * @return void
     */
    public function setCertain($certain)
    {
        $this->certain = $certain;
    }

    /**
     * Returns the boolean state of certain
     * 
     * @return bool
     */
    public function isCertain()
    {
        return $this->certain;
    }

    /**
     * Returns the dateOfAction
     * 
     * @return \DateTime dateOfAction
     */
    public function getDateOfAction()
    {
        return $this->dateOfAction;
    }

    /**
     * Sets the dateOfAction
     * 
     * @param \DateTime $dateOfAction
     * @return void
     */
    public function setDateOfAction(\DateTime $dateOfAction)
    {
        $this->dateOfAction = $dateOfAction;
    }

    /**
     * Returns the inferred
     * 
     * @return bool $inferred
     */
    public function getInferred()
    {
        return $this->inferred;
    }

    /**
     * Sets the inferred
     * 
     * @param bool $inferred
     * @return void
     */
    public function setInferred($inferred)
    {
        $this->inferred = $inferred;
    }

    /**
     * Returns the boolean state of inferred
     * 
     * @return bool
     */
    public function isInferred()
    {
        return $this->inferred;
    }

    /**
     * Returns a copy of the publisherAction
     * 
     * @return \SLUB\PublisherDb\Domain\Model\PublisherAction
     */
    public function getCopy()
    {
        $newPublisherAction = new PublisherAction();
        $newPublisherAction->setQuantity($this->quantity);
        $newPublisherAction->setType($this->type);
        $newPublisherAction->setCertain($this->certain);
        $newPublisherAction->setDateOfAction($this->dateOfAction);
        $newPublisherAction->setInferred($this->inferred);
        return $newPublisherAction;
    }

    /**
     * Returns the inStore
     * 
     * @return bool $inStore
     */
    public function getInStore()
    {
        return $this->inStore;
    }

    /**
     * Sets the inStore
     * 
     * @param bool $inStore
     * @return void
     */
    public function setInStore($inStore)
    {
        $this->inStore = $inStore;
    }

    /**
     * Returns the boolean state of inStore
     * 
     * @return bool
     */
    public function isInStore()
    {
        return $this->inStore;
    }
}
