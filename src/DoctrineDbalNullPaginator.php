<?php declare(strict_types=1);

namespace Tolkam\Pagination\Paginator;

use Doctrine\DBAL\Query\QueryBuilder;
use Generator;
use Tolkam\Pagination\PaginationResult;
use Tolkam\Pagination\PaginationResultInterface;
use Tolkam\Pagination\PaginatorInterface;

class DoctrineDbalNullPaginator implements PaginatorInterface
{
    /**
     * @var QueryBuilder
     */
    protected QueryBuilder $queryBuilder;
    
    /**
     * @var int|null
     */
    protected ?int $maxResults = null;
    
    /**
     * @var array
     */
    protected array $items = [];
    
    /**
     * @param QueryBuilder $queryBuilder
     * @param int|null     $maxResults
     */
    public function __construct(QueryBuilder $queryBuilder, int $maxResults = null)
    {
        $this->queryBuilder = $queryBuilder;
        $this->setMaxResults($maxResults);
    }
    
    /**
     * Sets the max results
     *
     * @param int|null $maxResults
     *
     * @return self
     */
    public function setMaxResults(?int $maxResults): self
    {
        if ($maxResults !== null) {
            $this->maxResults = $maxResults > 0
                ? $maxResults
                : $this->maxResults;
        }
        else {
            $this->maxResults = $maxResults;
        }
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function paginate(): PaginationResultInterface
    {
        $this->items = $this->queryBuilder
            ->setMaxResults($this->maxResults)
            ->executeQuery()
            ->fetchAllAssociative();
        
        return new PaginationResult(count($this->items), null, null, null);
    }
    
    /**
     * @inheritDoc
     */
    public function getItems(): Generator
    {
        foreach ($this->items as $k => $item) {
            yield $k => $item;
        }
    }
}
