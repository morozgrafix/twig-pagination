<?php

namespace DevotedCode\Twig\Pagination;

class MockPaginationBehaviour extends AbstractPaginationBehaviour
{
    public function getPaginationData($totalPages, $currentPage)
    {
        return [];
    }
}
