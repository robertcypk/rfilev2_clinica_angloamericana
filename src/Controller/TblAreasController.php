<?php

namespace App\Controller;

use App\Entity\TblAreas;
use App\Form\TblAreasType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * @Route("/zonas")
 */
class TblAreasController extends Controller
{
    /**
     * @Route("/", name="tbl_areas_index", methods="GET")
     */
    public function index(): Response
    {
        $tblAreas = $this->getDoctrine()
            ->getRepository(TblAreas::class)
            ->findAll();
        return $this->render('tbl_areas/index.html.twig', ['tbl_areas' => $tblAreas]);
    }

    /**
     * @Route("/new", name="tbl_areas_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $tblArea = new TblAreas();
        $form = $this->createForm(TblAreasType::class, $tblArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $codsede =$request->get("tbl_areas");
            /*if ($codsede["sede"]=='SAN ISIDRO') {
                $codsede = 1;
            } else {
                $codsede = 2;
            }*/
            //$tblArea->setCodsede($codsede);
            $em = $this->getDoctrine()->getManager();
            $em->persist($tblArea);
            $em->flush();

            return $this->redirectToRoute('tbl_areas_index');
        }

        return $this->render('tbl_areas/new.html.twig', [
            'tbl_area' => $tblArea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idarea}", name="tbl_areas_show", methods="GET")
     */
    public function show(TblAreas $tblArea): Response
    {
        return $this->render('tbl_areas/show.html.twig', ['tbl_area' => $tblArea]);
    }

    /**
     * @Route("/{idarea}/edit", name="tbl_areas_edit", methods="GET|POST")
     */
    public function edit(Request $request, TblAreas $tblArea): Response
    {
        $form = $this->createForm(TblAreasType::class, $tblArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('tbl_areas_index');

            //return $this->redirectToRoute('tbl_areas_edit', ['idarea' => $tblArea->getIdarea()]);
        }

        return $this->render('tbl_areas/edit.html.twig', [
            'tbl_area' => $tblArea,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idarea}", name="tbl_areas_delete", methods="DELETE")
     */
    public function delete(Request $request, TblAreas $tblArea): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tblArea->getIdarea(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tblArea);
            $em->flush();
            return JsonResponse::create(array('success'=>1));
        }

        return JsonResponse::create(array('success'=>0));
    }
}
