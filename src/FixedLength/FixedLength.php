<?php

namespace DevotedCode\Twig\Pagination\FixedLength;

use DevotedCode\Twig\Pagination\AbstractPaginationBehaviour;

final class FixedLength extends AbstractPaginationBehaviour
{
    /**
     * @var int
     */
    private $maximumVisible;

    /**
     * @param int $totalPages
     *   Total number of pages.
     *
     * @param int $currentPage
     *   Number of the current page.
     *
     * @param int $maximumVisible
     *   Maximum number of visible pages. Should never be lower than 7.
     *   1 on each edge, 1 omitted chunk on each side, and 3 in the middle.
     *   For example: [1][...][11][12][13][...][20]
     *
     * @param int|string $omittedPagesIndicator
     *   Value to use as indicator for omitted chunks of pages.
     */
    public function __construct(
        $totalPages,
        $currentPage,
        $maximumVisible,
        $omittedPagesIndicator = -1
    ) {
        parent::__construct($totalPages, $currentPage, $omittedPagesIndicator);
        $this->setMaximumVisible($maximumVisible);
    }

    /**
     * @param int $maximumVisible
     * @return static
     */
    public function withMaximumVisible($maximumVisible)
    {
        $c = clone $this;
        $c->setMaximumVisible($maximumVisible);
        return $c;
    }

    /**
     * @param int $maximumVisible
     */
    private function setMaximumVisible($maximumVisible)
    {
        $maximumVisible = (int) $maximumVisible;
        $this->guardMaximumVisibleMinimumValue($maximumVisible);
        $this->maximumVisible = $maximumVisible;
    }

    /**
     * @return int
     */
    public function getMaximumVisible()
    {
        return $this->maximumVisible;
    }

    /**
     * @param $maximumVisible
     *
     * @throws \InvalidArgumentException
     *   If the maximum number of visible pages is lower than 7.
     */
    private function guardMaximumVisibleMinimumValue($maximumVisible)
    {
        // Maximum number of allowed visible pages should never be lower than 7.
        // 1 on each edge, 1 omitted chunk on each side, and 3 in the middle.
        // For example: [1][...][11][12][13][...][20]
        if ($maximumVisible < 7) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Maximum of number of visible pages (%d) should be at least 7.',
                    $maximumVisible
                )
            );
        }
    }

    public function getPaginationData()
    {
        // If the total number of pages is less than the maximum number of
        // allowed visible pages, we don't need to omit anything.
        if ($this->totalPages <= $this->maximumVisible) {
            return $this->getPaginationDataWithNoOmittedChunks();
        }

        // Check if we can omit a single chunk of pages, depending on the
        // position of the current page relative to the first and last page.
        if ($this->hasSingleOmittedChunk()) {
            return $this->getPaginationDataWithSingleOmittedChunk();
        }

        // Otherwise omit two chunks of pages, one on each side of the current
        // page.
        return $this->getPaginationDataWithMultipleOmittedChunks();
    }

    /**
     * @return array
     */
    private function getPaginationDataWithNoOmittedChunks()
    {
        return range(1, $this->totalPages);
    }

    /**
     * @return int
     */
    private function getSingleOmissionBreakpoint()
    {
        return (int) ceil($this->maximumVisible / 2);
    }

    /**
     * @return bool
     */
    public function hasSingleOmittedChunk()
    {
        return $this->hasSingleOmittedChunkNearLastPage() || $this->hasSingleOmittedChunkNearStartPage();
    }

    /**
     * @return bool
     */
    private function hasSingleOmittedChunkNearLastPage()
    {
        return $this->currentPage <= $this->getSingleOmissionBreakpoint();
    }

    /**
     * @return bool
     */
    private function hasSingleOmittedChunkNearStartPage()
    {
        return $this->currentPage >= $this->totalPages - $this->getSingleOmissionBreakpoint() + 1;
    }

    /**
     * @return array
     */
    private function getPaginationDataWithSingleOmittedChunk()
    {
        // Check if we're working from the first page to the last page, or
        // the other way around. If omitted chunk is near the last page work
        // from LTR, otherwise work RTL.
        if ($this->hasSingleOmittedChunkNearLastPage()) {

            // Determine where the omitted pages will be.
            $rest = $this->maximumVisible - $this->currentPage;
            $omittedPagesPosition = ((int) ceil($rest / 2)) + $this->currentPage;

            // Fill from the first page until the the position of the omitted
            // chunk.
            $pagesLeft = range(1, $omittedPagesPosition - 1);

            // Pick up from the first page visible after the omitted chunk and
            // fill until the last page.
            $continueFromPage = $this->totalPages - ($this->maximumVisible - $omittedPagesPosition) + 1;
            $pagesRight = range($continueFromPage, $this->totalPages);

        } else {

            // Determine where the omitted pages will be.
            $rest = $this->maximumVisible - ($this->totalPages - $this->currentPage) + 1;
            $omittedPagesPosition = $this->currentPage - (((int) ceil($rest / 2))  - 1);

            // Fill from the position of the omitted chunk until the last page.
            $pagesRight = range($omittedPagesPosition + 1, $this->totalPages);

            // Fill from the first page until the omitted chunk.
            $stopAtPage = $this->maximumVisible - count($pagesRight) - 1;
            $pagesLeft = range(1, $stopAtPage);
        }

        // Merge left side, omitted pages indicator, and right side together.
        return array_merge(
            $pagesLeft,
            [$this->omittedPagesIndicator],
            $pagesRight
        );
    }

    /**
     * @return array
     */
    private function getPaginationDataWithMultipleOmittedChunks()
    {
        // @todo Implement...
        return array();
    }
}
