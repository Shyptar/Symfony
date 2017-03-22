<?php

namespace Test\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TestCrudBundle:Default:index.html.twig');
    }
}
