<?php

namespace App\Utilities\Paginator\Interfaces;

use App\Utilities\Paginator\Paginator;

interface PaginatorPageInterface
{
    public function getValue():string;
    public function getHref():string;
    public function getActive():bool;
}
