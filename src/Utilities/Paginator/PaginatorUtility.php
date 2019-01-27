<?php

namespace App\Utilities\Paginator;

use App\Utilities\Paginator\Exceptions\ActivePageNotExistException;
use App\Utilities\Paginator\Interfaces\PaginatorUtilityInterface;
use App\Utilities\Paginator\PaginatorUrlGenerator;
use App\Utilities\Paginator\Interfaces\PaginatorInterface;
use App\Utilities\Paginator\Paginator;
use App\Utilities\Paginator\PaginatorPages;
use App\Utilities\Paginator\PaginatorPage;

class PaginatorUtility implements PaginatorUtilityInterface
{
    private $paginator;
    private $paginatorUrlGenerator;
    private $total;
    private $activePage;
    private $limit;
    private $delta;
    private $maxPages;

    public function __construct(PaginatorUrlGenerator $paginatorUrlGenerator, $total, $limit = 20, $activePage = 1, $delta = 2)
    {
        $this->paginator = new Paginator();
        $this->paginatorUrlGenerator = $paginatorUrlGenerator;
        $this->total = $total;
        $this->limit = $limit;
        $this->activePage = $activePage;
        $this->delta = $delta;
    }

    public function getActivePage()
    {
        return $this->activePage;
    }

    public function setActivePage($active)
    {
        $this->activePage = $active;
    }
    
    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    public function getDelta()
    {
        return $this->delta;
    }

    public function setDelta($delta)
    {
        $this->delta = $delta;
    }
    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }
    
    public function getMaxPages()
    {
        return $this->maxPages;
    }

    public function setMaxPages($maxPages)
    {
        $this->maxPages = $maxPages;
    }

    public function getPaginator():PaginatorInterface
    {
        $this->generatePaginator();
        
        return $this->paginator;
    }
    
    private function generatePaginator()
    {
        $this->calculateMaxPages();
        $this->checkActivePage();

        if($this->isPager())
        {
            $this->paginator->setIsPager(true);
            $this->paginator->setOffset($this->limit*($this->activePage-1));

            $this->generatePages();
        }
        else
        {
            $this->paginator->setIsPager(false);
            $this->paginator->setOffset(0);
        }
    }

    private function generatePages()
    {
        $pagerPages = new PaginatorPages();
        $pagerPages->appendPage(new PaginatorPage($this->activePage, $this->paginatorUrlGenerator->generateUrl($this->activePage), true));

        $prependSpace = $this->activePage-1;
        $appendSpace = $this->maxPages-$this->activePage;
        
        if($prependSpace > $this->delta)
            $prependSpace = $this->delta;

        if($appendSpace > $this->delta)
            $appendSpace = $this->delta;

        if($prependSpace > 0)
        {
            if($this->delta-$appendSpace > 0)
            {
                $left = $this->activePage-$prependSpace-1;
                $prependSpace += ($left > $this->delta?$this->delta-$appendSpace:$left);
            }

            for ($i=1; $i <= $prependSpace; $i++)
            {
                $pagerPages->prependPage(new PaginatorPage($this->activePage-$i, $this->paginatorUrlGenerator->generateUrl($this->activePage-$i), false));
            }
        }

        if($appendSpace > 0)
        {
            if($this->delta-$prependSpace > 0)
            {
                $left = $this->maxPages-$this->activePage-$appendSpace;
                $appendSpace += ($left > $this->delta?$this->delta-$prependSpace:$left);
            }

            for ($i=1; $i <= $appendSpace; $i++)
            {
                $pagerPages->appendPage(new PaginatorPage($this->activePage+$i, $this->paginatorUrlGenerator->generateUrl($this->activePage+$i), false));
            }
        }

        $pagerPages = $this->addPreviousBtn($pagerPages);
        $pagerPages = $this->addNextBtn($pagerPages);

        $this->paginator->setPages($pagerPages);
    }

    private function calculateMaxPages()
    {
        $this->maxPages = (empty($this->total)?1:ceil($this->total/$this->limit));
    }

    private function isActiveBiggerDelta()
    {
        return $this->activePage > $this->delta;
    }

    private function isPager()
    {
        return $this->maxPages > 1;
    }

    private function checkActivePage()
    {
        if($this->activePage > $this->maxPages)
        {
            throw new ActivePageNotExistException();
        }
    }

    private function addPreviousBtn(PaginatorPages $pagerPages, $name = '<'):PaginatorPages
    {
        if($this->activePage > 1)
            $pagerPages->prependPage(new PaginatorPage($name, $this->paginatorUrlGenerator->generateUrl($this->activePage-1), false));
        
        return $pagerPages;
    }
    
    private function addNextBtn(PaginatorPages $pagerPages, $name = '>'):PaginatorPages
    {
        if($this->activePage < $this->maxPages)
            $pagerPages->appendPage(new PaginatorPage($name, $this->paginatorUrlGenerator->generateUrl($this->activePage+1), false));

        return $pagerPages;
    }
}
