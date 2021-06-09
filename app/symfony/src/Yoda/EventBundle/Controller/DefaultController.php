<?php

namespace Yoda\EventBundle\Controller;


class DefaultController extends Controller
{
    public function indexAction(String $name): Response
    {
        // Get entity manager
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EventBundle:Event');

        $event = $repo->findOneBy([
            'name' => 'Testting name'
        ]);

        // return a json response
        // return new Response("teste", 200, ["Content-Type" => 'application/json']);
        return $this->render('EventBundle:Default:index.html.twig',[
            'event' => $event
        ]);
    }
}
