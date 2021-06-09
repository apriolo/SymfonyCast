<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Contracts\Service;
use Yoda\EventBundle\Entity\Event;
// Usando o controller base criado
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Nao precisa de use pois a classe controller se encontra no mesmo namespace
use Yoda\EventBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//Para usar annotations para espeficicar template e rotas
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Yoda\EventBundle\Form\EventType;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all event entities.
     * @Template ("event/index.html.twig")
     * @Route ("/", name="event_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Find all events
//        $events = $em->getRepository('EventBundle:Event')->findAll();
        // Criar query para apresentar apenas os eventos que vao acontecer
        $events = $em->getRepository('EventBundle:Event')->getUpComingEvents();

//        return $this->render('event/index.html.twig', array(
//            'events' => $events,
//        ));

        // return usando a annotation de render template
        return array('events' => $events);
    }

    /**
     * Creates a new event entity.
     * @Route("/new", name="event_new")
     */
    public function newAction(Request $request)
    {
        // Verificação de permissoes de acesso dentro da rota
        // Vimos tambem dentro do security.yml
        // Criamos uma funcao para realizar essa verificação
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form = $this->createCreateForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get nos dados do usuario para fazer a relação
            $user = $this->getUser();
            $event->setOwner($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/new.html.twig', array(
            'entity' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Event entity.
     *
     * @param Event $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('event_new'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Finds and displays a event entity.
     * @Route("/{slug}/show", name="event_show")
     */
    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);

        return $this->render('event/show.html.twig', array(
            'entity' => $event,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     * @Route("/{slug}/edit", name="event_edit")
     */
    public function editAction(Request $request, Event $event)
    {
        // Verificação de permissoes de acesso dentro da rota
        $this->enforceUserSecurity();
        // Verificando se tem permissao de owner
        $this->enforceOwnerSecurity($event);

        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('Yoda\EventBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'entity' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     * @Route ("/{id}/delete", name="event_delete")
     */
    public function deleteAction(Request $request, Event $event)
    {
        // Verificação de permissoes de acesso dentro da rota
        $this->enforceUserSecurity();
        // Verificando se tem permissao de owner
        $this->enforceOwnerSecurity($event);

        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Event $event)
    {
        // Verificação de permissoes de acesso dentro da rota
        $this->enforceUserSecurity();

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function enforceUserSecurity(string $role = "ROLE_USER")
    {
        if (!$this->getSecurityContext()->isGranted($role)) {
            throw $this->createAccessDeniedException('need' . $role);
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/{id}/attend", name="event_attend")
     */
    // Funcao para participar do evento
    public function attendAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Event $event
         */
        $event = $em->getRepository("EventBundle:Event")->find($id);
        if (!$event) {
            throw $this->createNotFoundException("Unable to find event entity");
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());
        }
        
        $em->persist($event);
        $em->flush();

        $url = $this->generateUrl('event_show', [
            "slug" => $event->getSlug()
        ]);

        return $this->redirect($url);
    }

    /**
     * @Route("/{id}/unattend", name="event_unattend")
     */
    public function unattendAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Event $event
         */
        $event = $em->getRepository("EventBundle:Event")->find($id);
        if (!$event) {
            throw $this->createNotFoundException("Unable to find event entity");
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        $url = $this->generateUrl('event_show', [
            "slug" => $event->getSlug()
        ]);

        return $this->redirect($url);
    }
}
