<?php

namespace Slub\MpdbCore\Services;

use Elastic\Elasticsearch\Client;
use Slub\MpdbCore\Common\Collection;
use Slub\MpdbCore\Domain\Model\Publisher;
use Slub\MpdbCore\Common\ElasticClientBuilder;
use Slub\MpdbCore\Command\IndexCommand;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ElasticSearchService implements SearchServiceInterface
{
    const DEFAULT_RESULT_COUNT = 25;
    protected string $prefix = '';
    protected string $index = '';
    protected string $publisher = '';
    protected string $searchTerm = '';
    protected string $id = '';
    protected int $from = 0;
    protected int $size = 0;
    protected array $params = [];
    protected ?Client $client = null;
    protected string $method = 'search';
    public array $info = [];

    public function setIndex(string $index = ''): SearchServiceInterface
    {
        if ($index == '') {
            $this->index = '';
        } else {
            $this->index = $this->prefix . $index;
        }

        return $this;
    }

    public function setPublisher(string $publisher = ''): SearchServiceInterface
    {
        if ($publisher != '' && $this->id != '') {
            throw new InvalidParamsException('Attempted to restrict search for publisher while searching for id');
        }

        $this->publisher = $publisher;

        return $this;
    }

    public function setSearchterm(string $searchTerm = ''): SearchServiceInterface
    {
        if ($searchTerm != '' && $this->id != '') {
            throw new InvalidParamsException('Attempted to search for term and id simultaneously');
        }

        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function setId(string $id = ''): SearchServiceInterface
    {
        if ($id != '' && $this->searchTerm != '') {
            throw new InvalidParamsException('Attempted to search for term and id simultaneously');
        }
        if ($id != '' && $this->publisher != '') {
            throw new InvalidParamsException('Attempted to restrict search for publisher while searching for id');
        }

        if ($id == '') {
            $this->method = 'search';
        } else {
            $this->method = 'get';
        }

        $this->id = $id;

        return $this;
    }

    public function setFrom(int $from = 0): SearchServiceInterface
    {
        $this->from = $from;

        return $this;
    }

    public function setSize(int $size = self::DEFAULT_RESULT_COUNT): SearchServiceInterface
    {
        $this->size = $size;

        return $this;
    }

    public function search(): Collection
    {
        if ($this->id != '' && $this->index == '') {
            throw new InvalidParamsException('Id specified but index unspecified');
        }

        $this->createParams();

        switch ($this->method) {
            case 'search':
                $result = $this->client->search($this->params);
                return Collection::wrap($result['hits']['hits'])->recursive();
                break;
            case 'get':
                $result = $this->client->get($this->params);
                return Collection::wrap($result['_source'])->recursive();
                break;
        }

        return Collection::wrap([]);
    }

    public function count(): int
    {
        if ($this->method == 'get') {
            throw new InvalidOperationException('Attempt to count a id based search');
        }

        $this->createParams();
        unset($this->params['body']['size']);
        unset($this->params['body']['from']);

        return $this->client->count($this->params)['count'];
    }

    public function reset(): SearchServiceInterface
    {
        $this->setIndex();
        $this->setPublisher();
        $this->setSearchterm();
        $this->setId();
        $this->setFrom();
        $this->setSize();
        $this->method = 'search';

        return $this;
    }

    public function init(): bool
    {
        $this->client = ElasticClientBuilder::create()->
            autoconfig()->
            build();
		$coreExtConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mpdb_core');
        $this->prefix = $coreExtConf['prefix'];

        return true;
    }

    private function createParams(): void
    {
        $this->params = [];

        if ($this->index != '') {
            $this->params['index'] = $this->index;
        } else {
            $this->params['index'] = Collection::wrap([
                $this->prefix . IndexCommand::PUBLISHED_ITEM_INDEX,
                $this->prefix . IndexCommand::WORK_INDEX,
                $this->prefix . IndexCommand::PERSON_INDEX])->
                join(',');
        }

        if ($this->id != '') {
            $this->params['id'] = $this->id;
        }

        if ($this->method == 'search') {
            $this->params['body'] = [ 'query' => [] ];
            $this->params['body']['size'] = $this->size;
            $this->params['body']['from'] = $this->from;

            if ($this->searchTerm == '') {
                $this->params['body']['query'] = [
                    'bool' => [
                        'must' => [ [
                            'match_all' => new \stdClass()
                        ] ]
                    ]
                ];
            } else {
                $this->params['body']['query'] = [
                    'bool' => [
                        'must' => [ [
                            'query_string' => [
                                'query' => $this->searchTerm
                            ]
                        ] ]
                    ]
                ];
            }

            if ($this->publisher != '') {
                $this->params['body']['query']['bool']['must'][] =
                    [ 'query_string' => [
                        'query' => $this->publisher . '_*',
                        'fields' => [ 'mvdb_id', 'published_items.mvdb_id', 'works.published_items.mvdb_id' ]
                    ]
                ];
            }
        }
    }
}
