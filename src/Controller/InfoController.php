<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
     * @Route("/info")
     */
class InfoController extends Controller
{
    /**
     * @Route("/", name="info")
     */
    public function index()
    {
        return $this->render('info/index.html.twig', [
            'controller_name' => 'InfoController',
        ]);
    }

    /**
     * @Route("/ayuda", name="ayuda")
     */
    public function ayuda()
    {
        return $this->render('info/ayuda.html.twig', [
            'controller_name' => 'InfoController',
        ]);
    }

}
