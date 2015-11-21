<?php

namespace DevotedCode\Twig\Pagination;

class PaginationExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $extensionName;

    /**
     * @var string
     */
    private $extensionNameSuffixed;

    /**
     * @var PaginationBehaviourInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $behaviour;

    /**
     * @var PaginationExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extensionName = 'mock';
        $this->extensionNameSuffixed = 'mock_pagination';
        $this->behaviour = $this->getMock(PaginationBehaviourInterface::class);

        $this->extension = new PaginationExtension(
            $this->extensionName,
            $this->behaviour
        );
    }

    public function testNaming()
    {
        // Make sure the extension's name is suffixed with '_pagination'.
        $this->assertEquals($this->extensionNameSuffixed, $this->extension->getName());

        // Make sure the extension's name is never suffixed twice.
        $extension = new PaginationExtension($this->extensionNameSuffixed, $this->behaviour);
        $this->assertEquals($this->extensionNameSuffixed, $extension->getName());
    }

    public function testFunctionInfo()
    {
        $expected = [
            new \Twig_SimpleFunction(
                $this->extensionNameSuffixed,
                array($this->extension, 'getPaginationData')
            )
        ];

        $actual = $this->extension->getFunctions();

        $this->assertEquals($expected, $actual);
    }

    public function testPaginationDataFunction()
    {
        $totalPages = 20;
        $currentPage = 10;
        $expected = [1, -1, 10, -1, 20];

        $this->behaviour->expects($this->once())
            ->method('getPaginationData')
            ->with($totalPages, $currentPage)
            ->willReturn($expected);

        $actual = $this->extension->getPaginationData($totalPages, $currentPage);

        $this->assertEquals($expected, $actual);
    }

    public function testPaginationDataFunctionWithOmittedPagesIndicator()
    {
        $totalPages = 20;
        $currentPage = 10;
        $omittedPagesIndicator = '...';
        $expected = [1, '...', 10, '...', 20];

        $this->behaviour->expects($this->once())
            ->method('withOmittedPagesIndicator')
            ->with($omittedPagesIndicator)
            ->willReturn($this->behaviour);

        $this->behaviour->expects($this->once())
            ->method('getPaginationData')
            ->with($totalPages, $currentPage)
            ->willReturn($expected);

        $actual = $this->extension->getPaginationData(
            $totalPages,
            $currentPage,
            $omittedPagesIndicator
        );

        $this->assertEquals($expected, $actual);
    }
}
