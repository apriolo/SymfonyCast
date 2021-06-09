<?php

namespace Yoda\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yoda\EventBundle\Entity\Event;

// Bbiblioteca para setar a ordem de execucao
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Repository\UserRepository;

class LoadEvents implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository('UserBundle:User')
            ->findOneByUsernameOrEmail('user');
        $event1 = new Event();
        $event1->setName('Load Event 1');
        $event1->setLocation('Load Location 1');
        $event1->setTime(new \DateTimeImmutable('now'));
        $event1->setDetails('Events with details by loadevents');
        $event1->setOwner($user);
        $manager->persist($event1);

        // ADM
        $admin = $manager->getRepository('UserBundle:User')
            ->findOneByUsernameOrEmail('admin');
        $event2 = new Event();
        $event2->setName('Load Event 2');
        $event2->setLocation('Load Location 2');
        $event2->setTime(new \DateTimeImmutable('tomorrow'));
        $event2->setDetails('Events with details by loadevents 2');
        $event2->setOwner($admin);
        $manager->persist($event2);


        $manager->flush();
    }

    // Definindo a ordem de execucao das fixtures
    public function getOrder()
    {
        return 20;
    }
}