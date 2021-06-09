<?php

namespace Yoda\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use  Symfony\Component\Security\Core\SecurityContextInterface ;
use  Symfony\Component\HttpFoundation\Request;
use Yoda\EventBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login_form")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // Obtem erro de login se existir
        // Primeira forma de verificação mostrada, porem existe a forma mais facil de fazer
//        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
//            $error = $request->attributes->get(
//                SecurityContextInterface::AUTHENTICATION_ERROR
//            );
//        } else if (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
//            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
//            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
//        } else {
//            $error = "";
//        }

        // Forma mais simples de realizar o login
        $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);

        $lastUsername = $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render("/Security/login.html.twig",[
            // last username entered by the user
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route ("/logout", name="logout")
     */
    public function logoutAction()
    {
        
    }
}