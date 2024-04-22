<?php
namespace Slub\MpdbCore\Tests\Functional\Domain\Model;

use Slub\DmNorm\Domain\Model\GndPerson;
use Slub\DmNorm\Domain\Model\GndWork;
use Slub\MpdbCore\Domain\Model\PublishedItem;
use Slub\MpdbCore\Domain\Model\PublishedSubitem;
use Slub\MpdbCore\Domain\Model\Publisher;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 *
 * @author Matthias Richter <matthias.richter@slub-dresden.de>
 */
class PublishedItemMvdbIdTest extends FunctionalTestCase
{
    /**
     * @var \Slub\PublisherDb\Domain\Model\PublishedItem
     */
    protected ?PublishedItem $subject = null;

    /**
     * @var \Slub\MpdbCore\Domain\Model\PublishedSubitem
     */
    protected ?PublishedSubitem $subitem1 = null;

    /**
     * @var \Slub\MpdbCore\Domain\Model\PublishedSubitem
     */
    protected ?PublishedSubitem $subitem2 = null;

    /**
     * @var \Slub\MpdbCore\Domain\Model\Publisher
     */
    protected ?Publisher $publisher = null;

    /**
     * @var string
     */
    protected string $voice1 = 'N';

    /**
     * @var string
     */
    protected string $voice2 = 'Vl';

    /**
     * @var string
     */
    protected string $part1 = 'NN';

    /**
     * @var string
     */
    protected string $part2 = 'H1';

    /**
     * @var string
     */
    protected string $plateId1 = '00001';

    /**
     * @var string
     */
    protected string $plateId2 = '00002';

    /**
     * @var string
     */
    protected string $partialId1 = '';

    /**
     * @var string
     */
    protected string $partialId2 = '';

    /**
     * @var string
     */
    protected string $shorthand = 'HO';

    /**
     * @var string
     */
    protected string $separator = '_';

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new PublishedItem();
        $this->subitem1 = new PublishedSubitem();
        $this->subitem2 = new PublishedSubitem();
        $this->publisher = new Publisher();

        $this->publisher->setShorthand($this->shorthand);
        $this->subitem1->setPart($this->part1);
        $this->subitem1->setPlateId($this->plateId1);
        $this->subitem1->setVoice($this->voice1);
        $this->subitem2->setPart($this->part2);
        $this->subitem2->setPlateId($this->plateId2);
        $this->subitem2->setVoice($this->voice2);

        $this->partialId1 = implode($this->separator, [ $this->plateId1, $this->part1, $this->voice1 ]);
        $this->partialId2 = implode($this->separator, [ $this->plateId2, $this->part2, $this->voice2 ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function newPublishedItemHasEmptyId()
    {
        self::assertSame(
            '',
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function publishedItemWithPublisherHasShorthand()
    {
        $this->subject->setPublisher($this->publisher);
        $id = $this->shorthand . $this->separator;

        self::assertSame(
            $id,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function publishedItemHasNoShorthandAfterPublisherRemoved()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->setPublisher();

        self::assertSame(
            '',
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function publishedItemWithSubitemHasPlateId()
    {
        $this->subject->addPublishedSubitem($this->subitem1);

        self::assertSame(
            $this->plateId1,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function completePublishedItemHasCompleteId()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem1);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1 ]);

        self::assertSame(
            $id,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function idChangesWhenSubitemWithLowerIdIsAdded()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem2);
        $this->subject->addPublishedSubitem($this->subitem1);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1 ]);

        self::assertSame(
            $id,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function idChangesWhenSubitemWithLowerIdIsRemoved()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem2);
        $this->subject->addPublishedSubitem($this->subitem1);
        $this->subject->removePublishedSubitem($this->subitem1);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId2 ]);

        self::assertSame(
            $id,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function idRemainsWhenSubitemWithHigherIdIsAdded()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem1);
        $this->subject->addPublishedSubitem($this->subitem2);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1 ]);

        self::assertSame(
            $id,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function idRemainsWhenSubitemWithHigherIdIsRemoved()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem1);
        $this->subject->addPublishedSubitem($this->subitem2);
        $this->subject->removePublishedSubitem($this->subitem2);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1 ]);

        self::assertSame(
            $id,
            $this->subject->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function subitemIdsAreCalculatedCorrectly()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem1);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1, $this->partialId1 ]);

        self::assertSame(
            $id,
            $this->subitem1->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function subitemIdChangesWhenSubitemWithLowerIdIsAdded()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem2);
        $this->subject->addPublishedSubitem($this->subitem1);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1, $this->partialId1 ]);

        self::assertSame(
            $id,
            $this->subitem1->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function subitemIdChangesWhenSubitemWithLowerIdIsRemoved()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem2);
        $this->subject->addPublishedSubitem($this->subitem1);
        $this->subject->removePublishedSubitem($this->subitem1);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1, $this->partialId1 ]);

        self::assertSame(
            $id,
            $this->subitem1->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function subitemIdRemainsWhenSubitemWithHigherIdIsAdded()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem1);
        $this->subject->addPublishedSubitem($this->subitem2);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1, $this->partialId1 ]);

        self::assertSame(
            $id,
            $this->subitem1->getMvdbId()
        );
    }

    /**
     * @test
     */
    public function subitemIdRemainsWhenSubitemWithHigherIdIsRemoved()
    {
        $this->subject->setPublisher($this->publisher);
        $this->subject->addPublishedSubitem($this->subitem1);
        $this->subject->addPublishedSubitem($this->subitem2);
        $this->subject->removePublishedSubitem($this->subitem2);
        $id = implode($this->separator, [ $this->shorthand, $this->plateId1, $this->partialId1 ]);

        self::assertSame(
            $id,
            $this->subitem1->getMvdbId()
        );
    }
}
