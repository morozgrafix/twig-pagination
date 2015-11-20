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
     * @param int $totalPages
     * @param int $currentPage
     * @param int $maximumVisible
     * @param array $expected
     *
     * @dataProvider paginationTestDataProvider
     */
    public function testPaginationData(
        $totalPages,
        $currentPage,
        $maximumVisible,
        $expected
    ) {
        $this->behaviour = $this->behaviour
            ->withTotalPages($totalPages)
            ->withCurrentPage($currentPage)
            ->withMaximumVisible($maximumVisible);

        $actual = $this->behaviour->getPaginationData();

        $this->assertEquals($expected, $actual);
    }

    public function paginationTestDataProvider()
    {
        return [
            // 20 pages, 15 visible
            [
                20,
                1,
                15,
                [1, 2, 3, 4, 5, 6, 7, '...', 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                8,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, '...', 18, 19, 20],
            ],
            [
                20,
                13,
                15,
                [1, 2, 3, '...', 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                20,
                15,
                [1, 2, 3, 4, 5, 6, 7, '...', 14, 15, 16, 17, 18, 19, 20],
            ],
        ];
    }
}
