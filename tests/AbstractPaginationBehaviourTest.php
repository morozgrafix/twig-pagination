<?php

namespace DevotedCode\Twig\Pagination;

class AbstractPaginationBehaviourTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractPaginationBehaviour
     */
    private $behaviour;

    public function setUp()
    {
        $this->behaviour = new MockPaginationBehaviour();
    }

    public function testOmittedPagesIndicatorIsNegativeOneByDefault()
    {
        $this->assertEquals(-1, $this->behaviour->getOmittedPagesIndicator());
    }

    public function testOmittedPagesIndicatorCanBeConfigured()
    {
        $this->behaviour = $this->behaviour
            ->withOmittedPagesIndicator('|');

        $this->assertEquals('|', $this->behaviour->getOmittedPagesIndicator());
    }

    public function testOmittedPagesIndicatorTypeValidationWhenUpdating()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator should either be a string or an int.'
        );

        $this->behaviour->withOmittedPagesIndicator(new \stdClass());
    }

    public function testOmittedPagesIndicatorCanNotBeChangedToAPageNumber()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Omitted pages indicator (1) should not be higher than 0 as it may not be a possible page number.'
        );

        $this->behaviour->withOmittedPagesIndicator(1);
    }
}
