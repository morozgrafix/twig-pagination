<?php

namespace DevotedCode\Twig\Pagination\FixedLength;

use DevotedCode\Twig\Pagination\PaginationBehaviourTest;

class FixedLengthTest extends PaginationBehaviourTest
{
    /**
     * @inheritdoc
     */
    public function getBehaviour()
    {
        return new FixedLength(7);
    }

    public function testMaximumVisibleMinimumValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Maximum of number of visible pages (6) should be at least 7.'
        );

        new FixedLength(6);
    }

    public function testMaximumVisibleCanBeUpdated()
    {
        $behaviour = new FixedLength(7);
        $behaviour = $behaviour->withMaximumVisible(10);
        $this->assertEquals(10, $behaviour->getMaximumVisible());
    }

    public function testMaximumVisibleCanNotBeLoweredToLessThanMinimumValue()
    {
        $behaviour = new FixedLength(7);

        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Maximum of number of visible pages (6) should be at least 7.'
        );

        $behaviour->withMaximumVisible(6);
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
        $expectedMaxVisible = $totalPages > $maximumVisible ? $maximumVisible : $totalPages;
        $expectedLength = count($expected);
        if ($expectedLength != $expectedMaxVisible) {
            throw new \LogicException(
                sprintf(
                    'The provided expected pagination length (%d) is incorrect (should be %d).',
                    $expectedLength,
                    $expectedMaxVisible
                )
            );
        }

        // Set the omission indicator to 3 dots for better test legibility.
        // Total pages, current page, and maximum visible should be
        // configured per test.
        $behaviour = new FixedLength($maximumVisible);
        $actual = $behaviour->getPaginationData($totalPages, $currentPage, '...');

        if ($actual != $expected) {
            $this->fail(
                'Actual pagination data did not match expected pagination data:' . PHP_EOL .
                'Total pages: ' . $totalPages . PHP_EOL .
                'Current page: ' . $currentPage . PHP_EOL .
                'Maximum visible pages: ' . $maximumVisible . PHP_EOL .
                'Expected: [' . implode(', ', $expected) . ']' . PHP_EOL .
                'Actual: [' . implode(', ', $actual) . ']' . PHP_EOL
            );
        }
    }

    public function paginationTestDataProvider()
    {
        return [
            // Maximum visible is more or equal to total pages.
            [
                11,
                1,
                12,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            [
                11,
                1,
                11,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],

            // 20 pages, 15 visible (all possibilities).
            [
                20,
                1,
                15,
                [1, 2, 3, 4, 5, 6, 7, '...', 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                2,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, '...', 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                3,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, '...', 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                4,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, '...', 16, 17, 18, 19, 20],
            ],
            [
                20,
                5,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, '...', 16, 17, 18, 19, 20],
            ],
            [
                20,
                6,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, '...', 17, 18, 19, 20],
            ],
            [
                20,
                7,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, '...', 17, 18, 19, 20],
            ],
            [
                20,
                8,
                15,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, '...', 18, 19, 20],
            ],
            [
                20,
                9,
                15,
                [1, 2, 3, '...', 6, 7, 8, 9, 10, 11, 12, '...', 18, 19, 20],
            ],
            [
                20,
                10,
                15,
                [1, 2, 3, '...', 7, 8, 9, 10, 11, 12, 13, '...', 18, 19, 20],
            ],
            [
                20,
                11,
                15,
                [1, 2, 3, '...', 8, 9, 10, 11, 12, 13, 14, '...', 18, 19, 20],
            ],
            [
                20,
                12,
                15,
                [1, 2, 3, '...', 9, 10, 11, 12, 13, 14, 15, '...', 18, 19, 20],
            ],
            [
                20,
                13,
                15,
                [1, 2, 3, '...', 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                14,
                15,
                [1, 2, 3, 4, '...', 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                15,
                15,
                [1, 2, 3, 4, '...', 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                16,
                15,
                [1, 2, 3, 4, 5, '...', 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                17,
                15,
                [1, 2, 3, 4, 5, '...', 12, 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                18,
                15,
                [1, 2, 3, 4, 5, 6, '...', 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                19,
                15,
                [1, 2, 3, 4, 5, 6, '...', 13, 14, 15, 16, 17, 18, 19, 20],
            ],
            [
                20,
                20,
                15,
                [1, 2, 3, 4, 5, 6, 7, '...', 14, 15, 16, 17, 18, 19, 20],
            ],

            // 99999 pages, 7 visible. (All single omitted chunk possibilities,
            // and some test samples in between.)
            [
                99999,
                1,
                7,
                [1, 2, 3, '...', 99997, 99998, 99999]
            ],
            [
                99999,
                2,
                7,
                [1, 2, 3, 4, '...', 99998, 99999]
            ],
            [
                99999,
                3,
                7,
                [1, 2, 3, 4, '...', 99998, 99999]
            ],
            [
                99999,
                4,
                7,
                [1, 2, 3, 4, 5, '...', 99999]
            ],
            [
                9999,
                1000,
                7,
                [1, '...', 999, 1000, 1001, '...', 9999],
            ],
            [
                9999,
                2345,
                7,
                [1, '...', 2344, 2345, 2346, '...', 9999],
            ],
            [
                99999,
                99996,
                7,
                [1, '...', 99995, 99996, 99997, 99998, 99999]
            ],
            [
                99999,
                99997,
                7,
                [1, 2, '...', 99996, 99997, 99998, 99999]
            ],
            [
                99999,
                99998,
                7,
                [1, 2, '...', 99996, 99997, 99998, 99999]
            ],
            [
                99999,
                99999,
                7,
                [1, 2, 3, '...', 99997, 99998, 99999]
            ],

            // 100 pages, 10 (even!) visible. (All single omitted chunk
            // possibilities, and some test cases in between.)
            [
                100,
                1,
                10,
                [1, 2, 3, 4, 5, '...', 97, 98, 99, 100],
            ],
            [
                100,
                2,
                10,
                [1, 2, 3, 4, 5, '...', 97, 98, 99, 100],
            ],
            [
                100,
                3,
                10,
                [1, 2, 3, 4, 5, 6, '...', 98, 99, 100],
            ],
            [
                100,
                4,
                10,
                [1, 2, 3, 4, 5, 6, '...', 98, 99, 100],
            ],
            [
                100,
                5,
                10,
                [1, 2, 3, 4, 5, 6, 7, '...', 99, 100],
            ],
            [
                100,
                6,
                10,
                [1, 2, 3, 4, 5, 6, 7, '...', 99, 100],
            ],
            [
                100,
                7,
                10,
                [1, 2, '...', 5, 6, 7, 8, '...', 99, 100],
            ],
            [
                100,
                46,
                10,
                [1, 2, '...', 44, 45, 46, 47, '...', 99, 100],
            ],
            [
                // Note that now that the current page is higher than 50,
                // there's 2 pages to the right of the current page and only
                // one to the left, instead of the other way around before 50.
                100,
                73,
                10,
                [1, 2, '...', 72, 73, 74, 75, '...', 99, 100],
            ],
            [
                100,
                100,
                10,
                [1, 2, 3, 4, '...', 96, 97, 98, 99, 100],
            ],
        ];
    }
}
