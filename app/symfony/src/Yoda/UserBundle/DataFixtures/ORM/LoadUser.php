<?php

namespace Yoda\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Yoda\UserBundle\Entity\User;

// Para usar o bycrypt para senhas precisa desses componentes
// gera um hash + salt (um outro hash gerado no inicio da string senha)
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

// para definir a sequencia em que as fixtures vao ser carregadas
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUser implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@teste.com');
        $user->setPassword($this->encodePassword($user, '123'));
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername("admin");
        $admin->setEmail('admin@teste.com');
        $admin->setPassword($this->encodePassword($admin, '123'));
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $manager->flush();
    }

    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    // Function para retornar o valor da ordem em que as fixtures vao executar
    public function getOrder()
    {
        return 10;
    }
}