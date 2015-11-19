<?php

namespace DevotedCode\Twig\Pagination;

interface PaginationBehaviourInterface
{
    /**
     * @return array
     *   Array of page numbers to display. Chunks of omitted pages are
     *   indicated as -1 by default, or the value you configured using
     *   withOmittedPagesIndicator().
     */
    public function getPaginationData();

    /**
     * @param int $totalPages
     *   Total number of pages. Should never be lower than 1.
     *   Should never be less than the current page (if set).
     *
     * @return static
     */
    public function withTotalPages($totalPages);

    /**
     * @return int
     *   Total number of pages.
     */
    public function getTotalPages();

    /**
     * @param $currentPage
     *   Number of the current page. Should never be lower than 1.
     *   Should never be higher than the total number of pages (if set).
     *
     * @return static
     */
    public function withCurrentPage($currentPage);

    /**
     * @return int
     *   Number of the current page.
     */
    public function getCurrentPage();

    /**
     * @param int|string $indicator
     *   What value to use to indicate an omitted chunk of pages.
     *   MUST be an int or a string.
     *
     * @return static
     */
    public function withOmittedPagesIndicator($indicator);

    /**
     * @return string|int
     *   Value used to indicate an omitted chunk of pages.
     *   MUST default to -1 if not configured manually using
     *   withOmittedPagesIndicator().
     */
    public function getOmittedPagesIndicator();
}
