<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(String $name): Response
    {
        // return a json response
//        return new Response("teste", 200, ["Content-Type" => 'application/json']);
        return $this->render('EventBundle:Default:index.html.twig',[
            'name' => $name
        ]);
    }
}
