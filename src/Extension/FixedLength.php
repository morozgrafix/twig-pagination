<?php

namespace DevotedCode\Twig\Pagination\Extension;

class FixedLength extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'fixed_length_pagination';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'fixed_length_pagination_data',
                array($this, 'determinePaginationData')
            ),
        );
    }

    /**
     * @param int $lastPage
     *   Number of the very last page.
     *
     * @param int $currentPage
     *   Number of the current page.
     *
     * @param int $maximumVisible
     *   Maximum number of pages that should be visible, including chunks of
     *   omitted pages (each chunk counts as 1). Has to be at least 7.
     *   (1 first and last, 1 omitted chunk on each side, and 3 in the middle.)
     *   (Example: [1][...][11][12][13][...][20])
     *
     * @param string|int $omittedPagesIndicator
     *   What value to use as indicator for chunks of omitted pages.
     *
     * @return array
     *   Array of page numbers to display. Omissions are indicated as -1.
     */
    public function determinePaginationData(
        $lastPage,
        $currentPage,
        $maximumVisible = 15,
        $omittedPagesIndicator = -1
    ) {
        $lastPage = (int) $lastPage;
        $currentPage = (int) $currentPage;
        $maximumVisible = (int) $maximumVisible;

        if ($currentPage < 1) {
            throw new \InvalidArgumentException('Current page should never be lower than 1.');
        }
        if ($currentPage > $lastPage) {
            throw new \InvalidArgumentException('Current page should never be higher than last page.');
        }
        if (is_int($omittedPagesIndicator) && $omittedPagesIndicator >= 1 && $omittedPagesIndicator <= $lastPage) {
            throw new \InvalidArgumentException('Omitted page indicator should not be an int that could be a page.');
        }

        // Maximum number of allowed visible pages should never be lower than 7.
        // 1 on each edge, 1 omitted chunk on each side, and 3 in the middle.
        // For example: [1][...][11][12][13][...][20]
        if ($maximumVisible < 7) {
            throw new \InvalidArgumentException(
                'Maximum of number of visible pages should be at least 7.'
            );
        }

        // If the total number of pages is less than the maximum number of
        // allowed visible pages, we don't need to omit anything.
        if ($lastPage <= $maximumVisible) {
            return $this->getPaginationDataWithNoOmittedChunks($lastPage);
        }

        // Check if we can omit a single chunk of pages, depending on the
        // position of the current page relative to the first and last page.
        if ($this->hasSingleOmittedChunk($lastPage, $currentPage, $maximumVisible)) {
            return $this->getPaginationDataWithSingleOmittedChunk(
                $lastPage,
                $currentPage,
                $maximumVisible,
                $omittedPagesIndicator
            );
        }

        // Otherwise omit two chunks of pages, one on each side of the current
        // page.
        return $this->getPaginationDataWithMultipleOmittedChunks(
            $lastPage,
            $currentPage,
            $maximumVisible,
            $omittedPagesIndicator
        );
    }

    /**
     * @param int $lastPage
     * @return array
     */
    private function getPaginationDataWithNoOmittedChunks($lastPage)
    {
        $lastPage = (int) $lastPage;
        return range(1, $lastPage);
    }

    /**
     * @param $maximumVisible
     * @return int
     */
    private function getSingleOmissionBreakpoint($maximumVisible)
    {
        $maximumVisible = (int) $maximumVisible;
        return (int) ceil($maximumVisible / 2);
    }

    /**
     * @param int $lastPage
     * @param int $currentPage
     * @param int $maximumVisible
     * @return bool
     */
    private function hasSingleOmittedChunk($lastPage, $currentPage, $maximumVisible)
    {
        $lastPage = (int) $lastPage;
        $currentPage = (int) $currentPage;
        $maximumVisible = (int) $maximumVisible;

        return $this->hasSingleOmittedChunkNearLastPage($currentPage, $maximumVisible) ||
            $this->hasSingleOmittedChunkNearStartPage($lastPage, $currentPage, $maximumVisible);
    }

    /**
     * @param int $currentPage
     * @param int $maximumVisible
     * @return bool
     */
    private function hasSingleOmittedChunkNearLastPage($currentPage, $maximumVisible)
    {
        $currentPage = (int) $currentPage;
        $maximumVisible = (int) $maximumVisible;

        return $currentPage <= $this->getSingleOmissionBreakpoint($maximumVisible);
    }

    /**
     * @param int $lastPage
     * @param int $currentPage
     * @param int $maximumVisible
     * @return bool
     */
    private function hasSingleOmittedChunkNearStartPage($lastPage, $currentPage, $maximumVisible)
    {
        $lastPage = (int) $lastPage;
        $currentPage = (int) $currentPage;
        $maximumVisible = (int) $maximumVisible;

        return $currentPage >= $lastPage - $this->getSingleOmissionBreakpoint($maximumVisible) + 1;
    }

    /**
     * @param int $lastPage
     * @param int $currentPage
     * @param int $maximumVisible
     * @param string|int $omittedPagesIndicator
     * @return array
     */
    private function getPaginationDataWithSingleOmittedChunk(
        $lastPage,
        $currentPage,
        $maximumVisible = 15,
        $omittedPagesIndicator = -1
    ) {
        $lastPage = (int) $lastPage;
        $currentPage = (int) $currentPage;
        $maximumVisible = (int) $maximumVisible;

        // Check if we're working from the first page to the last page, or
        // the other way around.
        $fillLtr = $this->hasSingleOmittedChunkNearLastPage($currentPage, $maximumVisible);

        if ($fillLtr) {
            // Determine where the omitted pages will be, and fill from the
            // start page until that point.
            $rest = $maximumVisible - $currentPage;
            $omittedPagesPosition = (int) ceil($rest / 2);
            $pages = range(1, $omittedPagesPosition - 1);
        } else {
            // Determine where the omitted pages will be, and fill from
            // that point until the last page.
            $rest = $maximumVisible - ($lastPage - $currentPage + 1);
            $omittedPagesPosition = (int) floor($rest / 2);
            $pages = range($omittedPagesPosition + 1, $lastPage);
        }

        // Set the correct index for each page. Eg. Page 1 should be at
        // index 1, page 5 at index 5, etc...
        $pages = array_combine($pages, $pages);

        // Add the indicator for the omitted pages.
        $pages[$omittedPagesPosition] = $omittedPagesIndicator;

        if ($fillLtr) {
            // Pages after the chunk of omitted pages.
            $restPages = range($omittedPagesPosition + 1, $maximumVisible);
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
     * @param int $lastPage
     * @param int $currentPage
     * @param int $maximumVisible
     * @param string|int $omittedPagesIndicator
     * @return array
     */
    private function getPaginationDataWithMultipleOmittedChunks(
        $lastPage,
        $currentPage,
        $maximumVisible = 15,
        $omittedPagesIndicator = -1
    ) {
        // @todo Implement...
        return array();
    }
}
