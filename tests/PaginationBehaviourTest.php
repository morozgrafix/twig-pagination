<?php

namespace DevotedCode\Twig\Pagination;

abstract class PaginationBehaviourTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaginationBehaviourInterface
     */
    protected $behaviour;

    public function setUp()
    {
        $this->behaviour = $this->getBehaviour();
    }

    /**
     * @return PaginationBehaviourInterface
     */
    abstract public function getBehaviour();

    public function testTotalPagesMinimumValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Total number of pages (0) should not be lower than 1.'
        );

        $this->behaviour->getPaginationData(0, 1);
    }

    public function testCurrentPageMinimumValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (0) should not be lower than 1.'
        );

        $this->behaviour->getPaginationData(10, 0);
    }

    public function testCurrentPageExistsInTotalPages()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Current page (11) should not be higher than total number of pages (10).'
        );

        $this->behaviour->getPaginationData(10, 11);
    }

    public function testOmittedPagesIndicatorType()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator should either be a string or an int.'
        );

        $this->behaviour->getPaginationData(10, 1, new \stdClass());
    }

    public function testOmittedPagesIntValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator (5) should not be between 1 and total number of pages (10).'
        );

        $this->behaviour->getPaginationData(10, 1, 5);
    }
}
