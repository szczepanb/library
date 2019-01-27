<?php

namespace App\Utilities\Paginator\Interfaces;

use App\Utilities\Paginator\Paginator;
use App\Utilities\Paginator\Interfaces\PaginatorPageInterface;

interface PaginatorPagesInterface
{
    public function getPages():array;
    public function appendPage(PaginatorPageInterface $page);
    public function prependPage(PaginatorPageInterface $page);
}
