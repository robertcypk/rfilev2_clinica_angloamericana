<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelpController extends Controller
{
    /**
     * @Route("/help", name="help")
     */
    public function index()
    {
        return $this->render('help/index.html.twig', [
            'controller_name' => 'HelpController',
        ]);
    }
}
