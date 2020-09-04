<?php

namespace ADJGP\UserBundle\Controller;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use ADJGP\UserBundle\Entity\User;
use ADJGP\UserBundle\Form\UserType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('ADJGPUserBundle:User')->findAll();

       /* $res = 'Lista de usuarios: <br/>';

        foreach($users as $user){
       
        $res .='Usuario: '.$user->getName().' <br />';


        }

        return new Response($res);*/

        return $this->render('ADJGPUserBundle:User:index.html.twig', array('users' => $users));
    }

    public function addAction(){

        $user = new User();

        $form = $this->createCreateForm($user);

        return $this->render('ADJGPUserBundle:User:add.html.twig', array('form' => $form->createView()));

    }

    private function createCreateForm(User $entity){

         $form = $this->createForm(new UserType, $entity, array(
           
            'action' => $this->generateUrl('adjgp_user_create'),
            'method' => 'POST'

         ));

         return $form;

    }

    public function createAction(Request $request){

        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if($form->isValid()){

            $password = $form->get('password')->getData();

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);

            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('adjgp_user_index');

        }

        return $this->render('ADJGPUserBundle:User:add.html.twig', array('form' => $form->createView()));

    }
    public function editAction($id){
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ADJGPUserBundle:User')->find($id);

        if (!$user) {
            
            throw $this->createNotFoundException('Este usuario no existe.');
        }

        $form= $this->createEditForm($user);

        return $this->render('ADJGPUserBundle:User:edit.html.twig', array('user'=> $user, 'form' => $form->createView()));


    }
    
    private function createEditForm(User $entity){

        $form = $this->createForm(new UserType, $entity, array('action' => $this->generateUrl('adjgp_user_update', array('id' => $entity->getId())), 'method' => 'PUT'));

        return $form;

    }
    public function updateAction($id, Request $request){
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ADJGPUserBundle:User')->find($id);

        $old_pass = $user->getPassword();

        if (!$user) {
            
            throw $this->createNotFoundException('Este usuario no existe.');
        }

        $form= $this->createEditForm($user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            $password = $form->get('password')->getData();
            
            if (!empty($password)) {
                
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);
                $user->setPassword($encoded);

            }else{

                $user->setPassword($old_pass);
            }
            $em->flush();

            return $this->redirectToRoute('adjgp_user_index');
        }

        return $this->render('ADJGPUserBundle:User:edit.html.twig', array('user'=> $user, 'form' => $form->createView()));

    }

    public function viewAction($id){

      $repo = $this->getDoctrine()->getRepository('ADJGPUserBundle:User');

      $user = $repo->find($id);
     
      return $this->render('ADJGPUserBundle:User:view.html.twig',array('user' => $user));
    }

    public function deleteAction($id){

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ADJGPUserBundle:User')->find($id);

        $em->remove($user);
        $em->flush();

        return new Response('Eliminado');

    }
}
