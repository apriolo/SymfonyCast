<?php


use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../app/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$kernel->boot();

$container = $kernel->getContainer();
$container->enterScope('request');
$container->set('request', $request);

$templating = $container->get('templating');

use Yoda\EventBundle\Entity\Event;

$event = new Event();
$event->setName('Testting name');
$event->setLocation('Location test');
$event->setTime(new \DateTime('tomorrow noon'));
$event->setDetails('Ha! Details for test');

$entityManager = $container->get('doctrine')->getManager();
$entityManager->persist($event);
$entityManager->flush();
