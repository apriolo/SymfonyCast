<?php

namespace Yoda\UserBundle\Controller;

// usando o controller criado
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yoda\EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Yoda\UserBundle\Entity\User;
use Yoda\UserBundle\Form\RegisterFormType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class RegisterController extends Controller
{
    /**
     * @Route ("/register", name="user_register")
     * @Template ("/Register/register.html.twig")
     */
    public function registerAction(Request $request)
    {

        $user = new User();
        // Criando os atributos para registrar usuarios
        $form = $this->createForm(new RegisterFormType(), $user);


        // Verifico a condição do formulario
        // se for subimited eu insiro o user
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // pega os dados do form
//            $data = $form->getData();
            // Crio uma entity usuario
//            $user = new User();
            // Seto os valores manual
//            $user->setUsername($data['username']);
//            $user->setEmail($data['email']);

            // utomatico pelo createform
            $user = $form->getData();
            $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));

            // Abro o entity manager e salvo
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //Adding a flash message of success register
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Welcome to the Death Star, have a magical day!');

            // Fazendo o login do usuario cadastrado
            $this->authenticateUser($user);

            $url = $this->generateUrl('event_index');
            return $this->redirect($url);
        }
        return ['form' => $form->createView()];
    }

    // Função para usar o btcrypt de salvr senhas criptografas
    private function encodePassword(User $user, $plaiPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

        return $encoder->encodePassword($plaiPassword, $user->getSalt());
    }

    // Function para autenticar usuarios por chamada de funcao
    private function authenticateUser(User $user)
    {
        $providerKey = 'secured_area'; // your firewall name
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        $this->getSecurityContext()->setToken($token);
    }
}