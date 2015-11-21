<?php

namespace DevotedCode\Twig\Pagination;

interface PaginationBehaviourInterface
{
    /**
     * @param int $totalPages
     *   Total number of pages. Should never be lower than 1.
     *   MUST NOT be less than the current page.
     *
     * @param int $currentPage
     *   Number of the current page. MUST NOT be lower than 1.
     *   MUST NOT be higher than the total number of pages.
     *
     * @return array
     *   Array of page numbers to display. Chunks of omitted pages are
     *   indicated as -1 by default, or the value set using
     *   withOmittedPagesIndicator().
     */
    public function getPaginationData($totalPages, $currentPage);

    /**
     * @param int|string $indicator
     *   What value to use to indicate an omitted chunk of pages.
     *   MUST be an int or a string. MUST be set to -1 by default.
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
