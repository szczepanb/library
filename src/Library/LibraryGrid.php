<?php

namespace App\Library;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait LibraryGrid
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
        $return = ['q' => '', 'author' => '', 'translation' => ''];

        if(strlen($this->session->get('q')))
            $return['q'] = $this->session->get('q');

        if(strlen($this->session->get('author')))
            $return['author'] = $this->session->get('author');

        if(strlen($this->session->get('translation')))
            $return['translation'] = $this->session->get('translation');

        if($request->get('q') !== null)
            $return['q'] = $request->get('q');
        
        if($request->get('author') !== null)
            $return['author'] = $request->get('author');
        
        if($request->get('translation') !== null)
            $return['translation'] = $request->get('translation');

        $this->session->set('q', $return['q']);
        $this->session->set('author', $return['author']);
        $this->session->set('translation', $return['translation']);

        return $return;
    }

    private function getParameters($filter):array
    {
        $parameters = ['page' => 1];

        if(strlen($filter['q']))
            $parameters['q'] = $filter['q']; 

        if(strlen($filter['author']))
            $parameters['author'] = $filter['author'];

        if(strlen($filter['translation']))
            $parameters['translation'] = $filter['translation'];

        return $parameters;
    }
}
