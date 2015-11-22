<?php

namespace DevotedCode\Twig\Pagination;

use DevotedCode\Twig\Pagination\FixedLength\FixedLength;

class PaginationExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaginationExtension
     */
    private $extension;

    public function setUp()
    {
        $this->extension = new PaginationExtension();
    }

    public function testExtensionName()
    {
        $this->assertEquals(
            'pagination_data',
            $this->extension->getName()
        );
    }

    public function testFunctionConfiguration()
    {
        // Should not have any functions by default.
        $this->assertEquals([], $this->extension->getFunctions());

        // Should be able to add functions with a custom name.
        $small = new FixedLength(7);
        $large = new FixedLength(21);

        $this->extension = $this->extension
            ->withFunction('small', $small)
            ->withFunction('large', $large);

        $expected = [
            new \Twig_SimpleFunction(
                'small_pagination',
                array($small, 'getPaginationData')
            ),
            new \Twig_SimpleFunction(
                'large_pagination',
                array($large, 'getPaginationData')
            ),
        ];

        $actual = $this->extension->getFunctions();

        $this->assertEquals($expected, $actual);

        // Should be able to remove functions by their non-suffixed name.
        $this->extension = $this->extension
            ->withoutFunction('small');

        $expected = [
            new \Twig_SimpleFunction(
                'large_pagination',
                array($large, 'getPaginationData')
            ),
        ];

        $actual = $this->extension->getFunctions();

        $this->assertEquals($expected, $actual);
    }

    public function testFunctionIsCallable()
    {
        $this->extension = $this->extension
            ->withFunction('small', new FixedLength(7));

        $twigFunctions = $this->extension->getFunctions();
        $twigFunction = $twigFunctions[0];
        $callable = $twigFunction->getCallable();

        $totalPages = 4;
        $currentPage = 1;
        $expected = [1, 2, 3, 4];

        $actual = call_user_func($callable, $totalPages, $currentPage);

        $this->assertEquals($expected, $actual);
    }
}
