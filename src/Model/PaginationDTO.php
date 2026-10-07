<?php
namespace App\Model;

class PaginationDTO
{
    public int $page;
    public int $limit;

    public function __construct(int $page = 1, int $limit = 4)
    {
        $this->page = $page;
        $this->limit = $limit;
    }
}