<?php

namespace DevotedCode\Twig\Pagination\FixedLength;

class FixedLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FixedLength
     */
    private $behaviour;

    public function setUp()
    {
        // Set the omission indicator to 3 dots for better test legibility.
        // Total pages, current page, and minimum visible should be
        // configured per test.
        $this->behaviour = new FixedLength(1, 1, 7, '...');
    }

    public function testTotalShorterThanMaximumVisible()
    {
        $this->behaviour = $this->behaviour
            ->withTotalPages(11)
            ->withCurrentPage(6)
            ->withMaximumVisible(12);

        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }

    public function testTotalEqualToMaximumVisible()
    {
        $this->behaviour = $this->behaviour
            ->withTotalPages(11)
            ->withCurrentPage(6)
            ->withMaximumVisible(11);

        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that setting the 1st page as current page omits a single chunk of
     * pages at the right position.
     */
    public function testSingleOmissionCurrentPageIsFirstPage()
    {
        $this->behaviour = $this->behaviour
            ->withTotalPages(20)
            ->withCurrentPage(1)
            ->withMaximumVisible(15);

        $expected = [1, 2, 3, 4, 5, 6, 7, '...', 14, 15, 16, 17, 18, 19, 20];
        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that setting the "single omission breaking point" near the first
     * page as current page omits a single chunk of pages at the right
     * position.
     *
     * In the case of 15 visible pages, we can omit a single chunk of pages up
     * until the 8th page.
     */
    public function testSingleOmissionCurrentPageIsBreakpointLtr()
    {
        $this->behaviour = $this->behaviour
            ->withTotalPages(20)
            ->withCurrentPage(8)
            ->withMaximumVisible(15);

        $expected = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, '...', 18, 19, 20];

        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that setting the last page as current page omits a single chunk of
     * pages at the right position.
     */
    public function testSingleOmissionCurrentPageIsLastPage()
    {
        $this->behaviour = $this->behaviour
            ->withTotalPages(20)
            ->withCurrentPage(20)
            ->withMaximumVisible(15);

        $expected = [1, 2, 3, 4, 5, 6, 7, '...', 14, 15, 16, 17, 18, 19, 20];
        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests that setting the "single omission breaking point" near the last
     * page as current page omits a single chunk of pages at the right
     * position.
     *
     * In the case of 15 visible pages and 20 pages total, we can omit a single
     * chunk of pages starting from the 13th page.
     */
    public function testSingleOmissionCurrentPageIsBreakpointRtl()
    {
        $this->behaviour = $this->behaviour
            ->withTotalPages(20)
            ->withCurrentPage(13)
            ->withMaximumVisible(15);

        $expected = [1, 2, 3, '...', 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }
}
