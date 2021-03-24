<?php

namespace App\Controller;

use App\Entity\TblHorarios;
use App\Form\TblHorariosType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/horarios")
 */
class TblHorariosController extends Controller
{
    /**
     * @Route("/", name="tbl_horarios_index", methods="GET")
     */
    public function index(): Response
    {
        $tblHorarios = $this->getDoctrine()
            ->getRepository(TblHorarios::class)
            ->findAll();

        return $this->render('tbl_horarios/index.html.twig', ['tbl_horarios' => $tblHorarios]);
    }

    /**
     * @Route("/new", name="tbl_horarios_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $tblHorario = new TblHorarios();
        $form = $this->createForm(TblHorariosType::class, $tblHorario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tblHorario);
            $em->flush();

            return $this->redirectToRoute('tbl_horarios_index');
        }

        return $this->render('tbl_horarios/new.html.twig', [
            'tbl_horario' => $tblHorario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idhorario}", name="tbl_horarios_show", methods="GET")
     */
    public function show(TblHorarios $tblHorario): Response
    {
        return $this->render('tbl_horarios/show.html.twig', ['tbl_horario' => $tblHorario]);
    }

    /**
     * @Route("/{idhorario}/edit", name="tbl_horarios_edit", methods="GET|POST")
     */
    public function edit(Request $request, TblHorarios $tblHorario): Response
    {
        $form = $this->createForm(TblHorariosType::class, $tblHorario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tbl_horarios_edit', ['idhorario' => $tblHorario->getIdhorario()]);
        }

        return $this->render('tbl_horarios/edit.html.twig', [
            'tbl_horario' => $tblHorario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idhorario}", name="tbl_horarios_delete", methods="DELETE")
     */
    public function delete(Request $request, TblHorarios $tblHorario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tblHorario->getIdhorario(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tblHorario);
            $em->flush();
        }

        return $this->redirectToRoute('tbl_horarios_index');
    }
}
