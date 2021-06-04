<?php

namespace Yoda\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yoda\EventBundle\Entity\Event;

class LoadEvents implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $event1 = new Event();
        $event1->setName('Load Event 1');
        $event1->setLocation('Load Location 1');
        $event1->setTime(new \DateTimeImmutable('now'));
        $event1->setDetails('Events with details by loadevents');
        $manager->persist($event1);

        $event2 = new Event();
        $event2->setName('Load Event 2');
        $event2->setLocation('Load Location 2');
        $event2->setTime(new \DateTimeImmutable('tomorrow'));
        $event2->setDetails('Events with details by loadevents 2');

        $manager->persist($event2);
        $manager->flush();
    }
}