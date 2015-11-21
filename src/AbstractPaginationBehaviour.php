<?php

namespace DevotedCode\Twig\Pagination;

abstract class AbstractPaginationBehaviour implements PaginationBehaviourInterface
{
    /**
     * @var int|string
     */
    protected $omittedPagesIndicator = -1;

    /**
     * @param int|string $indicator
     * @return static
     */
    public function withOmittedPagesIndicator($indicator)
    {
        $c = clone $this;
        $c->setOmittedPagesIndicator($indicator);
        return $c;
    }

    /**
     * @param int|string $indicator
     */
    protected function setOmittedPagesIndicator($indicator)
    {
        $this->guardOmittedPagesIndicatorType($indicator);
        $this->guardOmittedPagesIndicatorIntValue($indicator);
        $this->omittedPagesIndicator = $indicator;
    }

    /**
     * @return int|string
     */
    public function getOmittedPagesIndicator()
    {
        return $this->omittedPagesIndicator;
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     *
     * @throws \InvalidArgumentException
     *
     */
    protected function guardTotalPagesAndCurrentPageAreValid($totalPages, $currentPage)
    {
        $this->guardTotalPagesMinimumValue($totalPages);
        $this->guardCurrentPageMinimumValue($currentPage);
        $this->guardCurrentPageExistsInTotalPages($totalPages, $currentPage);
    }

    /**
     * @param int $totalPages
     *
     * @throws \InvalidArgumentException
     *   If total number of pages is lower than 1.
     */
    protected function guardTotalPagesMinimumValue($totalPages)
    {
        if ($totalPages < 1) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Total number of pages (%d) should not be lower than 1.',
                    $totalPages
                )
            );
        }
    }

    /**
     * @param int $currentPage
     *
     * @throws \InvalidArgumentException
     *   If current page is lower than 1.
     */
    protected function guardCurrentPageMinimumValue($currentPage)
    {
        if ($currentPage < 1) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Current page (%d) should not be lower than 1.',
                    $currentPage
                )
            );
        }
    }

    /**
     * @param int $totalPages
     * @param int $currentPage
     *
     * @throws \InvalidArgumentException
     *   If current page is higher than total number of pages.
     */
    protected function guardCurrentPageExistsInTotalPages($totalPages, $currentPage)
    {
        if ($currentPage > $totalPages) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Current page (%d) should not be higher than total number of pages (%d).',
                    $currentPage,
                    $totalPages
                )
            );
        }
    }

    /**
     * @param int|string $indicator
     *
     * @throws \InvalidArgumentException
     *   If omitted pages indicator is not an int or a string.
     */
    protected function guardOmittedPagesIndicatorType($indicator)
    {
        if (!is_int($indicator) && !is_string($indicator)) {
            throw new \InvalidArgumentException(
                'Omitted pages indicator should either be a string or an int.'
            );
        }
    }

    /**
     * @param int|string $indicator
     *
     * @throws \InvalidArgumentException
     *   If omitted pages indicator is an int in the range of 1 and the total
     *   number of pages (if both are set).
     */
    protected function guardOmittedPagesIndicatorIntValue($indicator)
    {
        if (is_int($indicator) && $indicator >= 1) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Omitted pages indicator (%d) should not be higher than 0 as it may not be a possible page number.',
                    $indicator
                )
            );
        }
    }
}
