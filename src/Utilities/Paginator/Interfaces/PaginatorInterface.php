<?php

namespace App\Utilities\Paginator\Interfaces;

use App\Utilities\Paginator\Paginator;

interface PaginatorInterface
{
    public function getIsPager():bool;
    public function getOffset():int;
    public function getPages():?PaginatorPagesInterface;
}
