<?php


namespace Yoda\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;
use Yoda\EventBundle\Entity\Event;

class Controller extends BaseController
{
    /**
     * @return SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }


    public function enforceOwnerSecurity(Event $event)
    {
        $user = $this->getUser();

        if ($user != $event->getOwner()) {
            throw new AccessDeniedException('Apenas owners podem editar eventos');
        }
    }

}