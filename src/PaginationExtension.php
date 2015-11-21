<?php

namespace DevotedCode\Twig\Pagination;

class PaginationExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var PaginationBehaviourInterface
     */
    private $behaviour;

    /**
     * @param string $name
     * @param PaginationBehaviourInterface $behaviour
     */
    public function __construct(
        $name,
        PaginationBehaviourInterface $behaviour
    ) {
        $this->setName($name);
        $this->behaviour = $behaviour;
    }

    /**
     * @param string $name
     */
    private function setName($name)
    {
        $name = preg_replace('/(_pagination)$/', '', (string) $name);
        $this->name = $name . '_pagination';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                $this->name,
                array($this, 'getPaginationData')
            ),
        );
    }

    /**
     * @param int $totalPages
     *   Total number of pages. Should never be lower than 1.
     *   MUST NOT be less than the current page.
     *
     * @param int $currentPage
     *   Number of the current page. MUST NOT be lower than 1.
     *   MUST NOT be higher than the total number of pages.
     *
     * @param int|string|bool $omittedPagesIndicator
     *   What value to use to indicate an omitted chunk of pages.
     *   Use false to use default value.
     *
     * @return array
     *   Array of page numbers to display. Chunks of omitted pages are
     *   indicated as -1 by default, or the value set using
     *   withOmittedPagesIndicator().
     */
    public function getPaginationData(
        $totalPages,
        $currentPage,
        $omittedPagesIndicator = false
    ) {
        $behaviour = $this->behaviour;

        if ($omittedPagesIndicator) {
            $behaviour = $this->behaviour
                ->withOmittedPagesIndicator($omittedPagesIndicator);
        }

        return $behaviour->getPaginationData($totalPages, $currentPage);
    }
}
