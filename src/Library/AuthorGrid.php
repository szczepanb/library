<?php

namespace App\Library;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait AuthorGrid
{
    private $session;

    /**
     * @required
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    private function setFilter(Request $request)
    {
        $return = ['q' => '', 'country' => ''];

        if(strlen($this->session->get('q')))
            $return['q'] = $this->session->get('q');

        if(strlen($this->session->get('country')))
            $return['country'] = $this->session->get('country');

        if($request->get('q') !== null)
            $return['q'] = $request->get('q');
        
        if($request->get('country') !== null)
            $return['country'] = $request->get('country');

        $this->session->set('q', $return['q']);
        $this->session->set('country', $return['country']);

        return $return;
    }

    private function getParameters($filter):array
    {
        $parameters = ['page' => 1];

        if(strlen($filter['q']))
            $parameters['q'] = $filter['q']; 

        if(strlen($filter['country']))
            $parameters['country'] = $filter['country']; 

        return $parameters;
    }
}
