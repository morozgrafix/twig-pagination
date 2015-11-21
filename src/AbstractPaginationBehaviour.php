<?php

namespace DevotedCode\Twig\Pagination;

abstract class AbstractPaginationBehaviour implements PaginationBehaviourInterface
{
    /**
     * @var int
     */
    protected $totalPages;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int|string
     */
    protected $omittedPagesIndicator;

    /**
     * @param int $totalPages
     * @param int $currentPage
     * @param int|string $omittedPagesIndicator
     */
    public function __construct(
        $totalPages,
        $currentPage,
        $omittedPagesIndicator = -1
    ) {
        $this->setTotalPages($totalPages);
        $this->setCurrentPage($currentPage);
        $this->setOmittedPagesIndicator($omittedPagesIndicator);
    }

    /**
     * @param int $totalPages
     * @return static
     */
    public function withTotalPages($totalPages)
    {
        $c = clone $this;
        $c->setTotalPages($totalPages);
        return $c;
    }

    /**
     * @param int $totalPages
     */
    protected function setTotalPages($totalPages)
    {
        $totalPages = (int) $totalPages;
        $this->guardTotalPagesMinimumValue($totalPages);

        if (!is_null($this->currentPage)) {
            $this->guardCurrentPageLowerThanTotalPages($this->currentPage, $totalPages);
        }

        $this->totalPages = $totalPages;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param $currentPage
     * @return static
     */
    public function withCurrentPage($currentPage)
    {
        $c = clone $this;
        $c->setCurrentPage($currentPage);
        return $c;
    }

    /**
     * @param int $currentPage
     */
    protected function setCurrentPage($currentPage)
    {
        $currentPage = (int) $currentPage;
        $this->guardCurrentPageMinimumValue($currentPage);

        if (!is_null($this->totalPages)) {
            $this->guardCurrentPageLowerThanTotalPages($currentPage, $this->totalPages);
        }

        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
       return $this->currentPage;
    }

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
     * @param int $currentPage
     * @param int $totalPages
     *
     * @throws \InvalidArgumentException
     *   If current page is higher than total number of pages.
     */
    protected function guardCurrentPageLowerThanTotalPages($currentPage, $totalPages)
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
        if (is_int($indicator) &&
            !is_null($this->totalPages) &&
            !is_null($this->currentPage) &&
            $indicator >= $this->currentPage &&
            $indicator <= $this->totalPages) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Omitted pages indicator (%d) should not be between 1 and total number of pages (%d) (if int).',
                    $indicator,
                    $this->totalPages
                )
            );
        }
    }
}
