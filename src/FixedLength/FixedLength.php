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
        $maximumVisible = 15,
        $omittedPagesIndicator = -1
    ) {
        parent::__construct($totalPages, $currentPage, $omittedPagesIndicator);
        $this->setMaximumVisible($maximumVisible);
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
    private function hasSingleOmittedChunk()
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
        // the other way around.
        $fillLtr = $this->hasSingleOmittedChunkNearLastPage();

        if ($fillLtr) {
            // Determine where the omitted pages will be, and fill from the
            // start page until that point.
            $rest = $this->maximumVisible - $this->currentPage;
            $omittedPagesPosition = (int) ceil($rest / 2);
            $pages = range(1, $omittedPagesPosition - 1);
        } else {
            // Determine where the omitted pages will be, and fill from
            // that point until the last page.
            $rest = $this->maximumVisible - ($this->totalPages - $this->currentPage + 1);
            $omittedPagesPosition = (int) floor($rest / 2);
            $pages = range($omittedPagesPosition + 1, $this->totalPages);
        }

        // Set the correct index for each page. Eg. Page 1 should be at
        // index 1, page 5 at index 5, etc...
        $pages = array_combine($pages, $pages);

        // Add the indicator for the omitted pages.
        $pages[$omittedPagesPosition] = $this->omittedPagesIndicator;

        if ($fillLtr) {
            // Pages after the chunk of omitted pages.
            $restPages = range($omittedPagesPosition + 1, $this->maximumVisible);
        } else {
            // Pages before the chunk of omitted pages.
            $restPages = range(1, $omittedPagesPosition - 1);
        }

        // Set the correct index for the rest of the pages.
        $restPages = array_combine($restPages, $restPages);

        // Merge everything together.
        $pages = array_merge($pages, $restPages);

        // Reset the indexes to a 0-based index and we're done.
        return array_values($pages);
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
