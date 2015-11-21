<?php

namespace DevotedCode\Twig\Pagination;

class AbstractPaginationBehaviourTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorProperties()
    {
        $totalPages = 10;
        $currentPage = 5;
        $omittedPagesIndicator = '...';

        $behaviour = new MockPaginationBehaviour(
            $totalPages,
            $currentPage,
            $omittedPagesIndicator
        );

        $this->assertEquals($totalPages, $behaviour->getTotalPages());
        $this->assertEquals($currentPage, $behaviour->getCurrentPage());
        $this->assertEquals($omittedPagesIndicator, $behaviour->getOmittedPagesIndicator());
    }

    public function testTotalPagesCanBeUpdated()
    {
        $behaviour = new MockPaginationBehaviour(10, 1);
        $behaviour = $behaviour->withTotalPages(15);
        $this->assertEquals(15, $behaviour->getTotalPages());
    }

    public function testCurrentPageCanBeUpdated()
    {
        $behaviour = new MockPaginationBehaviour(10, 1);
        $behaviour = $behaviour->withCurrentPage(2);
        $this->assertEquals(2, $behaviour->getCurrentPage());
    }

    public function testOmittedPagesIndicatorCanBeUpdated()
    {
        $behaviour = new MockPaginationBehaviour(10, 1, '...');
        $behaviour = $behaviour->withOmittedPagesIndicator('|');
        $this->assertEquals('|', $behaviour->getOmittedPagesIndicator());
    }

    public function testCurrentPageInTotalPages()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (11) should not be higher than total number of pages (10).'
        );

        new MockPaginationBehaviour(10, 11);
    }

    public function testTotalPagesCanNotBeLoweredToLessThanCurrentPage()
    {
        $behaviour = new MockPaginationBehaviour(11, 6);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (6) should not be higher than total number of pages (5).'
        );

        $behaviour->withTotalPages(5);
    }

    public function testCurrentPageCanNotBeRaisedHigherThanTotalPages()
    {
        $behaviour = new MockPaginationBehaviour(11, 6);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (12) should not be higher than total number of pages (11).'
        );

        $behaviour->withCurrentPage(12);
    }

    public function testTotalPagesMinimumValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Total number of pages (0) should not be lower than 1.'
        );

        new MockPaginationBehaviour(0, 5);
    }

    public function testTotalPagesCanNotBeLoweredToLessThanMinimumValue()
    {
        $behaviour = new MockPaginationBehaviour(1, 1);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Total number of pages (0) should not be lower than 1.'
        );

        $behaviour->withTotalPages(0);
    }

    public function testCurrentPageMinimumValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (0) should not be lower than 1.'
        );

        new MockPaginationBehaviour(5, 0);
    }

    public function testCurrentPageCanNotBeLoweredToLessThanMinimumValue()
    {
        $behaviour = new MockPaginationBehaviour(5, 1);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (0) should not be lower than 1.'
        );

        $behaviour->withCurrentPage(0);
    }

    public function testOmittedPagesIndicatorTypeValidation()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator should either be a string or an int.'
        );

        new MockPaginationBehaviour(1, 1, new \stdClass());
    }

    public function testOmittedPagesIndicatorTypeValidationWhenUpdating()
    {
        $behaviour = new MockPaginationBehaviour(1, 1, -1);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator should either be a string or an int.'
        );

        $behaviour->withOmittedPagesIndicator(false);
    }

    public function testOmittedPagesIndicatorCanNotBeAPageNumber()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator (3) should not be between 1 and total number of pages (10) (if int).'
        );

        new MockPaginationBehaviour(10, 5, 3);
    }

    public function testOmittedPagesIndicatorCanNotBeStartPage()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator (1) should not be between 1 and total number of pages (10) (if int).'
        );

        new MockPaginationBehaviour(10, 5, 1);
    }

    public function testOmittedPagesIndicatorCanNotBeLastPage()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator (10) should not be between 1 and total number of pages (10) (if int).'
        );

        new MockPaginationBehaviour(10, 5, 10);
    }

    public function testOmittedPagesIndicatorCanNotBeChangedToAPageNumber()
    {
        $behaviour = new MockPaginationBehaviour(10, 5, -1);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator (5) should not be between 1 and total number of pages (10) (if int).'
        );

        $behaviour->withOmittedPagesIndicator(5);
    }
}
