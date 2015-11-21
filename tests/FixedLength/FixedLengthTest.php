<?php

namespace DevotedCode\Twig\Pagination\FixedLength;

class FixedLengthTest extends \PHPUnit_Framework_TestCase
{
    public function testMaximumVisibleMinimumValue()
    {
        $behaviour = new FixedLength(1, 1, 7);

        // Maximum visible should be at least 7.
        $this->setExpectedException(\InvalidArgumentException::class);
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
        // Total pages, current page, and minimum visible should be
        // configured per test.
        $behaviour = new FixedLength($totalPages, $currentPage, $maximumVisible, '...');
        $actual = $behaviour->getPaginationData();

        if ($actual !== $expected) {
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
            // Max visible more or equal to total pages.
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

            // 20 pages, 15 visible
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

            // 99999 pages, 7 visible
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
        ];
    }
}
