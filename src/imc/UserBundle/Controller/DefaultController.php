<?php

namespace imc\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('imcIdealBundle:Default:index.html.twig');
    }
}
