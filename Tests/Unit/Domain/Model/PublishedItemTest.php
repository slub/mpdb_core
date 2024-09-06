<?php
namespace Slub\MpdbCore\Tests\Unit\Domain\Model;

use Slub\DmNorm\Domain\Model\GndGenre;
use Slub\DmNorm\Domain\Model\GndInstrument;
use Slub\DmNorm\Domain\Model\GndPerson;
use Slub\DmNorm\Domain\Model\GndWork;
use Slub\MpdbCore\Domain\Model\Publisher;
use Slub\MpdbCore\Domain\Model\PublishedItem;
use Slub\MpdbCore\Domain\Model\PublishedSubitem;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 *
 * @author Matthias Richter <matthias.richter@slub-dresden.de>
 */
class PublishedItemTest extends UnitTestCase
{
    /**
     * @var \SLUB\PublisherDb\Domain\Model\PublishedItem
     */
    protected $subject = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new PublishedItem();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function getTypeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getType()
        );
    }

    /**
     * @test
     */
    public function setTypeForStringSetsType()
    {
        $this->subject->setType('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getType()
        );
    }

    /**
     * @test
     */
    public function getInstrumentationReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getInstrumentation()
        );
    }

    /**
     * @test
     */
    public function setInstrumentationForStringSetsInstrumentation()
    {
        $this->subject->setInstrumentation('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getInstrumentation()
        );
    }

    /**
     * @test
     */
    public function getDataAcquisitionCertainReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getDataAcquisitionCertain()
        );
    }

    /**
     * @test
     */
    public function setDataAcquisitionCertainForBoolSetsDataAcquisitionCertain()
    {
        $this->subject->setDataAcquisitionCertain(true);

        self::assertSame(
            true,
            $this->subject->getDataAcquisitionCertain()
        );
    }

    /**
     * @test
     */
    public function getRelatedPersonsKnownReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getRelatedPersonsKnown()
        );
    }

    /**
     * @test
     */
    public function setRelatedPersonsKnownForBoolSetsRelatedPersonsKnown()
    {
        $this->subject->setRelatedPersonsKnown(true);

        self::assertSame(
            true,
            $this->subject->getRelatedPersonsKnown()
        );
    }

    /**
     * @test
     */
    public function getWorkExaminedReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getWorkExamined()
        );
    }

    /**
     * @test
     */
    public function setWorkExaminedForBoolSetsWorkExamined()
    {
        $this->subject->setWorkExamined(true);

        self::assertSame(
            true,
            $this->subject->getWorkExamined()
        );
    }

    /**
     * @test
     */
    public function getDataSetManuallyCheckedReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getDataSetManuallyChecked()
        );
    }

    /**
     * @test
     */
    public function setDataSetManuallyCheckedForBoolSetsDataSetManuallyChecked()
    {
        $this->subject->setDataSetManuallyChecked(true);

        self::assertSame(
            true,
            $this->subject->getDataSetManuallyChecked()
        );
    }

    /**
     * @test
     */
    public function getContainedWorksIdentifiedReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getContainedWorksIdentified()
        );
    }

    /**
     * @test
     */
    public function setContainedWorksIdentifiedForBoolSetsContainedWorksIdentified()
    {
        $this->subject->setContainedWorksIdentified(true);

        self::assertSame(
            true,
            $this->subject->getContainedWorksIdentified()
        );
    }

    /**
     * @test
     */
    public function getResponsiblePersonReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getResponsiblePerson()
        );
    }

    /**
     * @test
     */
    public function setResponsiblePersonForStringSetsResponsiblePerson()
    {
        $this->subject->setResponsiblePerson('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getResponsiblePerson()
        );
    }

    /**
     * @test
     */
    public function getFinalReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getFinal()
        );
    }

    /**
     * @test
     */
    public function setFinalForIntSetsFinal()
    {
        $this->subject->setFinal(12);

        self::assertSame(
            12,
            $this->subject->getFinal()
        );
    }

    /**
     * @test
     */
    public function getLanguageReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getLanguage()
        );
    }

    /**
     * @test
     */
    public function setLanguageForStringSetsLanguage()
    {
        $this->subject->setLanguage('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getLanguage()
        );
    }

    /**
     * @test
     */
    public function getCommentReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getComment()
        );
    }

    /**
     * @test
     */
    public function setCommentForStringSetsComment()
    {
        $this->subject->setComment('Conceived at T3CON10');

        self::assertSame(
            'Conceived at T3CON10',
            $this->subject->getComment()
        );
    }

    /**
     * @test
     */
    public function getContainedWorksReturnsInitialValueForWork()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getContainedWorks()
        );
    }

    /**
     * @test
     */
    public function setContainedWorksForObjectStorageContainingWorkSetsContainedWorks()
    {
        $containedWork = new GndWork();
        $objectStorageHoldingExactlyOneContainedWorks = new ObjectStorage();
        $objectStorageHoldingExactlyOneContainedWorks->attach($containedWork);
        $this->subject->setContainedWorks($objectStorageHoldingExactlyOneContainedWorks);

        self::assertSame(
            $objectStorageHoldingExactlyOneContainedWorks,
            $this->subject->getContainedWorks()
        );
    }


    /**
     * @test
     */
    public function getEditorsReturnsInitialValueForPerson()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getEditors()
        );
    }

    /**
     * @test
     */
    public function setEditorsForObjectStorageContainingPersonSetsEditors()
    {
        $editor = new GndPerson();
        $objectStorageHoldingExactlyOneEditors = new ObjectStorage();
        $objectStorageHoldingExactlyOneEditors->attach($editor);
        $this->subject->setEditors($objectStorageHoldingExactlyOneEditors);

        self::assertSame(
            $objectStorageHoldingExactlyOneEditors,
            $this->subject->getEditors()
        );
    }

    /**
     * @test
     */
    public function getInstrumentsReturnsInitialValueForInstrument()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getGndInstrument()
        );
    }

    /**
     * @test
     */
    public function setInstrumentsForObjectStorageContainingInstrumentSetsInstruments()
    {
        $instrument = new GndInstrument;
        $objectStorageHoldingExactlyOneInstruments = new ObjectStorage();
        $objectStorageHoldingExactlyOneInstruments->attach($instrument);
        $this->subject->setGndInstrument($objectStorageHoldingExactlyOneInstruments);

        self::assertSame(
            $objectStorageHoldingExactlyOneInstruments,
            $this->subject->getGndInstrument()
        );
    }

    /**
     * @test
     */
    public function getGenreReturnsInitialValueForGenre()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getGndGenre()
        );
    }

    /**
     * @test
     */
    public function setGenreForObjectStorageContainingGenreSetsForm()
    {
        $form = new GndGenre();
        $objectStorageHoldingExactlyOneForm = new ObjectStorage();
        $objectStorageHoldingExactlyOneForm->attach($form);
        $this->subject->setGndGenre($objectStorageHoldingExactlyOneForm);

        self::assertSame(
            $objectStorageHoldingExactlyOneForm,
            $this->subject->getGndGenre()
        );
    }

    /**
     * @test
     */
    public function getFirstComposerReturnsInitialValueForPerson()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getFirstComposer()
        );
    }

    /**
     * @test
     */
    public function setFirstComposerForObjectStorageContainingPersonSetsFirstComposer()
    {
        $firstComposer = new GndPerson();
        $objectStorageHoldingExactlyOneFirstComposer = new ObjectStorage();
        $objectStorageHoldingExactlyOneFirstComposer->attach($firstComposer);
        $this->subject->setFirstComposer($objectStorageHoldingExactlyOneFirstComposer);

        self::assertSame(
            $objectStorageHoldingExactlyOneFirstComposer,
            $this->subject->getFirstComposer()
        );
    }

    /**
     * @test
     */
    public function getPublishedSubitemsReturnsInitialValueForPublishedSubitem()
    {
        $newObjectStorage = new ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getPublishedSubitems()
        );
    }

    /**
     * @test
     */
    public function setPublishedSubitemsForObjectStorageContainingPublishedSubitemSetsPublishedSubitems()
    {
        $publishedSubitem = new PublishedSubitem();
        $objectStorageHoldingExactlyOnePublishedSubitems = new ObjectStorage();
        $objectStorageHoldingExactlyOnePublishedSubitems->attach($publishedSubitem);
        $this->subject->setPublishedSubitems($objectStorageHoldingExactlyOnePublishedSubitems);

        self::assertSame(
            $objectStorageHoldingExactlyOnePublishedSubitems,
            $this->subject->getPublishedSubitems()
        );
    }

    /**
     * @test
     */
    public function getPublisherReturnsInitialValueForPublisher()
    {
        self::assertEquals(
            null,
            $this->subject->getPublisher()
        );
    }

    /**
     * @test
     */
    public function setPublisherForPublisherSetsPublisher()
    {
        $publisherFixture = new Publisher();
        $this->subject->setPublisher($publisherFixture);

        self::assertSame(
            $publisherFixture,
            $this->subject->getPublisher()
        );
    }
}
