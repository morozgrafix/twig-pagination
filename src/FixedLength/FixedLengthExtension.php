<?php

namespace DevotedCode\Twig\Pagination\FixedLength;

final class FixedLengthExtension extends \Twig_Extension
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
     *
     * @return array
     *   Array of page numbers to display. Chunks of omitted pages are
     *   indicated as -1 by default, or the value you provided as
     *   $omittedPagesIndicator parameter.
     */
    public function determinePaginationData(
        $totalPages,
        $currentPage,
        $maximumVisible = 15,
        $omittedPagesIndicator = -1
    ) {
        return (
            new FixedLength(
                $totalPages,
                $currentPage,
                $maximumVisible,
                $omittedPagesIndicator
            )
        )->getPaginationData();
    }
}
