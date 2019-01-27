<?php

namespace App\Utilities\Paginator;

use App\Utilities\Paginator\Interfaces\PaginatorPageInterface;

class PaginatorPage implements PaginatorPageInterface
{
    private $value;
    private $href;
    private $active;

    public function __construct(string $value, string $href, bool $active)
    {
        $this->value = $value;
        $this->href = $href;
        $this->active = $active;
    }

    public function getValue():string
    {
        return $this->value;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function getHref():string
    {
        return $this->href;
    }

    public function setHref(string $href)
    {
        $this->href = $href;
    }

    public function getActive():bool
    {
        return $this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }
}
