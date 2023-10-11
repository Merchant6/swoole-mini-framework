<?php

namespace App\Utils;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;

class Paginator
{
    /**
     * Total no. of items per page
     * @var int
     */
    private int $total;

    /**
     * Stores the total number of pages in the paginated result
     * @var int
     */
    private int $lastPage;
    /**
     * Summary of items
     * @var 
     */
    private $items;

    /**
     * Paginate the query results
     * @param \Doctrine\ORM\QueryBuilder|\Doctrine\ORM\Query $query
     * @param int $page
     * @param int $limit
     * @return \App\Utils\Paginator
     */
    public function paginate(QueryBuilder|Query $query, int $page = 1, int $limit = 10): Paginator
    {
        $paginator = new OrmPaginator($query);

        $totalResults = count($paginator);

        $paginator
        ->getQuery()
        ->setFirstResult($limit * ($page - 1))
        ->setMaxResults($limit);

        $this->total = $totalResults;
        $this->lastPage = (int) ceil($totalResults / $paginator->getQuery()->getMaxResults());
        $this->items = $paginator;
        
        return $this;
    }

	/**
	 * Total no. of items per page
	 * @return int
	 */
	public function getTotal(): int {
		return $this->total;
	}

	/**
	 * Stores the total number of pages in the paginated result
	 * @return int
	 */
	public function getLastPage(): int {
		return $this->lastPage;
	}

	/**
	 * Summary of items
	 * @return 
	 */
	public function getItems() {
		return $this->items;
	}
}
