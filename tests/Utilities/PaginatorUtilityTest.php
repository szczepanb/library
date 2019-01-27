<?php

namespace App\Utilities;

use App\Utilities\PaginatorUtility;
use App\Utilities\Exceptions\ActivePageNotExistException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;


class PaginatorUtilityTest extends TestCase
{
    public function testPaginatorMaxPages()
    {
        $route = new Route('/test/{page}');
        $routes = new RouteCollection();
        $routes->add('test', $route);
        $routerGenerateOptions = ['name' => 'test', 'parameters' => [], 'refType' => null];
        $requestContext = new RequestContext();
        $router = new UrlGenerator($routes, $requestContext);

        $paginator = new PaginatorUtility($router, $routerGenerateOptions, 123, 20);
        $paginator->getPaginator();
        
        $this->assertEquals(7, $paginator->getMaxPages());

        $paginator->setLimit(5);
        $paginator->getPaginator();
        $this->assertEquals(25, $paginator->getMaxPages());

        $paginator->setLimit(120);
        $paginator->getPaginator();
        $this->assertEquals(2, $paginator->getMaxPages());

        $paginator->setLimit(125);
        $paginator->getPaginator();
        $this->assertEquals(1, $paginator->getMaxPages());
        
        $paginator->setTotal(4);
        $paginator->setLimit(2);
        $paginator->getPaginator();
        $this->assertEquals(2, $paginator->getMaxPages());
    }

    public function testPaginatorPages()
    {
        $route = new Route('/test/{page}');
        $routes = new RouteCollection();
        $routes->add('test', $route);
        $routerGenerateOptions = ['name' => 'test', 'parameters' => [], 'refType' => null];
        $requestContext = new RequestContext();
        $router = new UrlGenerator($routes, $requestContext);

        $paginator = new PaginatorUtility($router, $routerGenerateOptions, 123, 20);
        $paginations = $paginator->getPaginator();

        $this->assertEquals(1, $paginations['pages'][0]['value']);
        $this->assertEquals(2, $paginations['pages'][1]['value']);
        $this->assertEquals(3, $paginations['pages'][2]['value']);
        $this->assertEquals(4, $paginations['pages'][3]['value']);
        $this->assertEquals(5, $paginations['pages'][4]['value']);

        $paginator->setActivePage(3);

        $paginations = $paginator->getPaginator();
        $this->assertEquals(1, $paginations['pages'][0]['value']);
        $this->assertEquals(2, $paginations['pages'][1]['value']);
        $this->assertEquals(3, $paginations['pages'][2]['value']);
        $this->assertEquals(4, $paginations['pages'][3]['value']);
        $this->assertEquals(5, $paginations['pages'][4]['value']);
        $this->assertEquals(true, $paginations['pages'][2]['active']);

        $paginator->setActivePage(7);

        $paginations = $paginator->getPaginator();
        $this->assertEquals(3, $paginations['pages'][0]['value']);
        $this->assertEquals(4, $paginations['pages'][1]['value']);
        $this->assertEquals(5, $paginations['pages'][2]['value']);
        $this->assertEquals(6, $paginations['pages'][3]['value']);
        $this->assertEquals(7, $paginations['pages'][4]['value']);
        $this->assertEquals(true, $paginations['pages'][4]['active']);

        $paginator->setLimit(60);
        $paginator->setActivePage(1);

        $paginations = $paginator->getPaginator();
        $this->assertEquals(1, $paginations['pages'][0]['value']);
        $this->assertEquals(2, $paginations['pages'][1]['value']);
        $this->assertEquals(3, $paginations['pages'][2]['value']);

        $paginator2 = new PaginatorUtility($router, $routerGenerateOptions, 4, 2, 1);
        $paginations = $paginator2->getPaginator();

        $this->assertEquals(1, $paginations['pages'][0]['value']);
        $this->assertEquals(2, $paginations['pages'][1]['value']);
        $this->assertEquals(2, count($paginations['pages']));
    }

    public function testPaginatorExecptionActivePage()
    {
        $route = new Route('/test/{page}');
        $routes = new RouteCollection();
        $routes->add('test', $route);
        $routerGenerateOptions = ['name' => 'test', 'parameters' => [], 'refType' => null];
        $requestContext = new RequestContext();
        $router = new UrlGenerator($routes, $requestContext);

        $paginator = new PaginatorUtility($router, $routerGenerateOptions, 123, 20);
        $paginator->setActivePage(10);
        $this->expectException(ActivePageNotExistException::class);
        $paginations = $paginator->getPaginator();
    }
}
