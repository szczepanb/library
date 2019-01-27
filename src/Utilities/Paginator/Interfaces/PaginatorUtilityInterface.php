<?php

namespace App\Utilities\Paginator\Interfaces;

use App\Utilities\Paginator\Paginator;

interface PaginatorUtilityInterface
{
    public function getPaginator():PaginatorInterface;
}
