<?php

namespace imc\IdealBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('imcIdealBundle:Default:index.html.twig');
    }


    public function moiAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('imcIdealBundle:Default:layout.html.twig');
    }


}
