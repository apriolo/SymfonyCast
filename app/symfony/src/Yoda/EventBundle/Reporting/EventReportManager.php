<?php


namespace Yoda\EventBundle\Reporting;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Router;

// Uma classe de service, serve para gerar relattorios em csv
class EventReportManager
{
    // Receber o enetity manager por injeção de dependencias
    private $em;

    //Injetar dependencias para criar a url para ser exportada tmb
    private $router;

    public function __construct(EntityManager $em, Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    public function getRecentlyUpdatedReport()
    {
        $events = $this->em->getRepository('EventBundle:Event')
            ->getRecentlyUpdatedEvents();

        $rows = array();
        foreach ($events as $event) {
            $data = array($event->getId(),
                $event->getName(),
                $event->getTime()->format('Y-m-d H:i:s'),
                $this->router->generate('event_show',
                    ["slug" => $event->getSlug()],
                    true
                )
            );

            $rows[] = implode(',', $data);
        }
        return implode("\n", $rows);
    }
}