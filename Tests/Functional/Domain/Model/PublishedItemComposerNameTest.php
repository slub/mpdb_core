<?php
namespace Slub\MpdbCore\Tests\Functional\Domain\Model;

use Slub\DmNorm\Domain\Model\GndPerson;
use Slub\DmNorm\Domain\Model\GndWork;
use Slub\DmNorm\Domain\Repository\GndPersonRepository;
use Slub\DmNorm\Domain\Repository\GndWorkRepository;
use Slub\DmNorm\Domain\Repository\GndInstrumentRepository;
use Slub\DmNorm\Domain\Repository\GndGenreRepository;
use Slub\MpdbCore\Domain\Model\PublishedItem;
use Slub\MpdbCore\Domain\Model\PublishedSubitem;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 *
 * @author Matthias Richter <matthias.richter@slub-dresden.de>
 */
class PublishedItemComposerNameTest extends FunctionalTestCase
{
    /**
     * @var \Slub\PublisherDb\Domain\Model\PublishedItem
     */
    protected ?PublishedItem $subject = null;

    /**
     * @var \Slub\DmNorm\Domain\Model\GndWork
     */
    protected ?GndWork $work1 = null;

    /**
     * @var \Slub\DmNorm\Domain\Model\GndWork
     */
    protected ?GndWork $work2 = null;

    /**
     * @var \Slub\DmNorm\Domain\Model\GndWork
     */
    protected ?GndWork $work3 = null;

    /**
     * @var \Slub\DmNorm\Domain\Model\GndPerson
     */
    protected ?GndPerson $composer1 = null;

    /**
     * @var \Slub\DmNorm\Domain\Model\GndPerson
     */
    protected ?GndPerson $composer2 = null;

    /**
     * @var string
     */
    protected string $nameComposer1 = 'J. S. Bach';

    /**
     * @var string
     */
    protected string $nameComposer2 = 'J. Brahms';

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/dm-norm'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new PublishedItem();

        $this->work1 = new GndWork();
        $this->work2 = new GndWork();
        $this->work3 = new GndWork();

        $this->composer1 = new GndPerson();
        $this->composer2 = new GndPerson();

        $this->composer1->setName($this->nameComposer1);
        $this->composer2->setName($this->nameComposer2);

        $this->work1->setFirstcomposer($this->composer1);
        $this->work2->setFirstcomposer($this->composer2);
        $this->work3->setFirstcomposer($this->composer1);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function publishedItemWithOneComposerReturnsComposer()
    {
        $this->subject->addFirstComposer($this->composer1);

        self::assertSame(
            $this->nameComposer1,
            $this->subject->getComposers()
        );
    }

    /**
     * @test
     */
    public function publishedItemWithManyComposersReturnsComposers()
    {
        $this->subject->addFirstComposer($this->composer1);
        $this->subject->addFirstComposer($this->composer2);
        $namesString = implode('; ', [$this->nameComposer1, $this->nameComposer2]);

        self::assertSame(
            $namesString,
            $this->subject->getComposers()
        );
    }

    /**
     * @test
     */
    public function publishedItemWithoutComposersReturnsOneWorkComposer()
    {
        $this->subject->addContainedWork($this->work1);

        self::assertSame(
            $this->nameComposer1,
            $this->subject->getComposers()
        );
    }

    /**
     * @test
     */
    public function publishedItemWithoutComposerReturnsManyWorkComposers()
    {
        $this->subject->addContainedWork($this->work1);
        $this->subject->addContainedWork($this->work2);
        $namesString = implode('; ', [$this->nameComposer1, $this->nameComposer2]);

        self::assertSame(
            $namesString,
            $this->subject->getComposers()
        );
    }

    /**
     * @test
     */
    public function publishedItemWithoutComposerWithoutWorkcomposersReturnsString()
    {
        self::assertSame(
            '',
            $this->subject->getComposers()
        );
    }

    /**
     * @test
     */
    public function identicalComposersAreUniquedOut()
    {
        $this->subject->addContainedWork($this->work1);
        $this->subject->addContainedWork($this->work3);

        self::assertSame(
            $this->nameComposer1,
            $this->subject->getComposers()
        );
    }

}
