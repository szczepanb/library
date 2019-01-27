<?php

namespace App\Utilities\Paginator;

use App\Utilities\Paginator\PaginatorPage;
use App\Utilities\Paginator\Interfaces\PaginatorPageInterface;
use App\Utilities\Paginator\Interfaces\PaginatorPagesInterface;

class PaginatorPages implements \ArrayAccess, PaginatorPagesInterface
{
    private $pages = [];

    public function offsetSet($offset, $page) {
        if (is_null($offset)) {
            $this->pages[] = $page;
        } else {
            $this->pages[$offset] = $page;
        }
    }

    public function offsetExists($offset) {
        return isset($this->pages[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->pages[$offset]);
    }

    public function offsetGet($offset):PaginatorPageInterface {
        return isset($this->pages[$offset]) ? $this->pages[$offset] : null;
    }

    public function getPages():array
    {
        return $this->pages;
    }

    public function appendPage(PaginatorPageInterface $page)
    {
        $this->pages[] = $page;
    }

    public function prependPage(PaginatorPageInterface $page)
    {
        array_unshift($this->pages, $page);
    }
}
