<?php

namespace App\Utilities\Paginator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginatorUrlGenerator
{
    private $router;
    private $name;
    private $parameters;
    private $refType;
    private $urlParam;

    public function __construct(UrlGeneratorInterface $router, $name, $parameters = [], $refType = UrlGeneratorInterface::RELATIVE_PATH, $urlParam = 'page')
    {
        $this->router = $router;
        $this->name = $name;
        $this->parameters = $parameters;
        $this->refType = $refType;
        $this->urlParam = $urlParam;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getParameters():array
    {
        return $this->parameters;
    }

    public function setParameters(string $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getRefType():int
    {
        return $this->refType;
    }

    public function setRefType(string $refType)
    {
        $this->refType = $refType;
    }

    public function getUrlParam():string
    {
        return $this->urlParam;
    }

    public function setUrlParam(string $urlParam)
    {
        $this->urlParam = $urlParam;
    }

    public function generateUrl($page):string
    {
        $this->parameters = array_merge($this->parameters, [$this->urlParam => $page]);
        return $this->router->generate($this->name, $this->parameters, $this->refType);
    }
}
