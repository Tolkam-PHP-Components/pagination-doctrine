<?php declare(strict_types=1);

namespace Tolkam\Pagination\Tests;

use Tolkam\Pagination\Paginator\DoctrineDbalNullPaginator;

class DoctrineDbalNullPaginatorTest extends AbstractDoctrineDbalTest
{
    /**
     * @return DoctrineDbalNullPaginator
     */
    public function makeInstance(): DoctrineDbalNullPaginator
    {
        return new DoctrineDbalNullPaginator(
            $this->queryFactory()
        );
    }
    
    /**
     * @dataProvider tableStructure
     */
    public function testResultsWithoutPagination(
        array $tableStructure
    ) {
        $paginator = $this->makeInstance();
        
        // no limit
        $paginator->paginate();
        $this->assertEquals(
            $tableStructure,
            iterator_to_array($paginator->getItems()),
            'should return all rows without optional parameters'
        );
        
        // limit
        $maxResults = 5;
        $result = $paginator
            ->setMaxResults($maxResults)
            ->paginate();
        
        $expected = array_slice($tableStructure, 0, $maxResults);
        $actual = iterator_to_array($paginator->getItems());
        
        $this->assertEquals(
            $expected,
            $actual,
            'should apply limit correctly and return exact results'
        );
        
        $this->assertEquals(
            $maxResults,
            count($actual),
            'should apply limit correctly and return exact count'
        );
        
        $this->assertEquals(
            $maxResults,
            $result->resultsCount(),
            'should apply limit correctly and return exact results count'
        );
        
        // cursor values
        $this->assertNull(
            $result->getPreviousCursor(),
            'should not return previous cursor without configured offset'
        );
        
        $this->assertNull(
            $result->getCurrentCursor(),
            'should return expected current cursor without configured offset'
        );
        
        $this->assertNull(
            $result->getNextCursor(),
            'should return next cursor without configured offset'
        );
    }
}
