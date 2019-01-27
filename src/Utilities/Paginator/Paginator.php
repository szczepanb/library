<?php
namespace App\Utilities\Paginator;

use App\Utilities\Paginator\PaginatorPages;
use App\Utilities\Paginator\Interfaces\PaginatorInterface;
use App\Utilities\Paginator\Interfaces\PaginatorPagesInterface;

class Paginator implements PaginatorInterface
{
    private $is_pager;
    private $offset;
    private $pages;

    public function getIsPager():bool
    {
        return $this->is_pager;
    }

    public function setIsPager(bool $is_pager)
    {
        $this->is_pager = $is_pager;
    }

    public function getOffset():int
    {
        return $this->offset;
    }

    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    public function getPages():?PaginatorPagesInterface
    {
        return $this->pages;
    }

    public function setPages(PaginatorPagesInterface $pages)
    {
        $this->pages = $pages;
    }
}
