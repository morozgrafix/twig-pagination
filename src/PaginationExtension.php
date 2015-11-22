<?php

namespace DevotedCode\Twig\Pagination;

use DevotedCode\Twig\Pagination\Behaviour\PaginationBehaviourInterface;

final class PaginationExtension extends \Twig_Extension
{
    /**
     * @var PaginationBehaviourInterface[]
     */
    private $functions;

    public function __construct()
    {
        $this->functions = [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagination_data';
    }

    /**
     * @param string $functionName
     *   Name to use for the twig function. Will be suffixed with "_pagination"
     *   unless it's already suffixed.
     *
     * @param PaginationBehaviourInterface $behaviour
     *   Pagination behaviour to use in the twig function.
     *
     * @return PaginationExtension
     */
    public function withFunction($functionName, PaginationBehaviourInterface $behaviour)
    {
        $functionName = $this->suffixFunctionName($functionName);

        $c = clone $this;

        $c->functions[$functionName] = new \Twig_SimpleFunction(
            $functionName,
            array($behaviour, 'getPaginationData')
        );

        return $c;
    }

    /**
     * @param string $functionName
     *   Name of the twig function to remove. Will be suffixed with
     *   "_pagination" unless it's already suffixed.
     *
     * @return PaginationExtension
     */
    public function withoutFunction($functionName)
    {
        $functionName = $this->suffixFunctionName($functionName);

        $c = clone $this;
        unset($c->functions[$functionName]);
        return $c;
    }

    /**
     * @param string $functionName
     * @return string
     */
    private function suffixFunctionName($functionName)
    {
        // Make sure the function name is not suffixed twice.
        $functionName = preg_replace('/(_pagination)$/', '', (string) $functionName);
        return $functionName . '_pagination';
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array_values($this->functions);
    }
}
