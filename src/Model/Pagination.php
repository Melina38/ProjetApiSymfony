<?php

namespace App\Model;

class Pagination
{
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $limit,
    ) {
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->total / $this->limit);
    }
}