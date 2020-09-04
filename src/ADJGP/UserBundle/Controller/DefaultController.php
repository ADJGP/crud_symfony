<?php

namespace ADJGP\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ADJGPUserBundle:Default:index.html.twig');
    }
}
