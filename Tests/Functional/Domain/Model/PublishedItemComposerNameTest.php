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
     * @var \Slub\DmNorm\Domain\Model\GndPerson
     */
    protected ?GndPerson $composer3 = null;

    /**
     * @var string
     */
    protected string $nameComposer1 = '';

    /**
     * @var string
     */
    protected string $nameComposer2 = '';

    /**
     * @var string
     */
    protected string $nameComposer3 = '';

    /**
     * Musik für Orgel; J. S. Bach
     * @var string
     */
    protected string $gndId1 = '300568517';

    /**
     * Gesänge, op. 43; J. Brahms
     * @var string
     */
    protected string $gndId2 = '107761277X';

    /**
     * Six trio sonatas; J. S. Bach
     * @var string
     */
    protected string $gndId3 = '300011040';

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

        $this->importCSVDataSet(__DIR__ . '/Fixtures/empty_persons.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/empty_works.csv');

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $workRepository = $objectManager->get(GndWorkRepository::class);
        $personRepository = $objectManager->get(GndPersonRepository::class);
        $instrumentRepository = $objectManager->get(GndInstrumentRepository::class);
        $genreRepository = $objectManager->get(GndGenreRepository::class);

        $this->work1->setGndId($this->gndId1)->pullGndInfo(
            $workRepository,
            $personRepository,
            $instrumentRepository,
            $genreRepository
        );
        $this->work2->setGndId($this->gndId2)->pullGndInfo(
            $workRepository,
            $personRepository,
            $instrumentRepository,
            $genreRepository
        );
        $this->work3->setGndId($this->gndId3)->pullGndInfo(
            $workRepository,
            $personRepository,
            $instrumentRepository,
            $genreRepository
        );

        $this->composer1 = $this->work1->getFirstComposer();
        $this->composer2 = $this->work2->getFirstComposer();
        $this->composer3 = $this->work3->getFirstComposer();

        $this->nameComposer1 = $this->composer1->getName();
        $this->nameComposer2 = $this->composer2->getName();
        $this->nameComposer3 = $this->composer3->getName();
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
        var_dump($this->nameComposer1);
        var_dump($this->nameComposer2);
        var_dump($namesString);die;

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
