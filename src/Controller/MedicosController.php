<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\TblMedicos;

class MedicosController extends Controller
{
    /**
     * @Route("/medicos", name="medicos")
     */
    public function index(Request $request)
    {
        return $this->render('medicos/index.html.twig', [
            'lstAreas' => $this->lstAreas(),
            'lstMedicos' => $this->lstMedicos()
        ]);
    }
    /**
     * @Route("/medicos/guardar", name="medicosguardar")
     */
    public function medicosguardar(Request $request)
    {
        $medico = $request->get('medico');
        $codmedico = $request->get('codmedico');
        $areas = $request->get('areas');
        $estado = $request->get('estado');
        if (empty($medico)) {
            return JsonResponse::create(array('success'=>0));
        }
        if (empty($codmedico)) {
            return JsonResponse::create(array('success'=>0));
        }
        if (empty($areas)) {
            return JsonResponse::create(array('success'=>0));
        }
        $estado = empty($estado) ? 0 : $estado;
        $check = $this->getDoctrine()->getRepository(\App\Entity\TblMedicos::class)->findOneBy(array('codmedico'=>$codmedico));
        if (empty($check)) {
            $areas = implode(',', $areas);
            $m = new \App\Entity\TblMedicos();
            $m->setMedico($medico);
            $m->setCodmedico($codmedico);
            $m->setAreas($areas);
            $m->setFecha(date('Y-m-d H:i:s'));
            $m->setEstado($estado);
            $em = $this->getDoctrine()->getManager();
            $em->persist($m);
            $em->flush();
            return JsonResponse::create(array('success'=>1));
        } else {
            $areas = implode(',', $areas);
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $q = $qb->update('App\Entity\TblMedicos', 'm')
                ->set('m.medico', $qb->expr()->literal($medico))
                ->set('m.codmedico', $qb->expr()->literal($codmedico))
                ->set('m.areas', $qb->expr()->literal(''.$areas.''))
                ->set('m.estado', $qb->expr()->literal($estado))
                ->where('m.idmedico = ?1')
                ->setParameter(1, $check->getIdmedico())
                ->getQuery();
            $p = $q->execute();
            return JsonResponse::create(array('success'=>2));
        }
    }
    /**
     * @Route("/medicos/eliminar", name="eliminarmedico")
     */
    public function eliminarmedico(Request $request)
    {
        $temp = $request->request->all();
        if(!empty($temp['idmedico'])){
            $id = $temp['idmedico'];
            $em = $this->getDoctrine()->getEntityManager();
            $medico = $em->getRepository(TblMedicos::Class)->findOneBy(array('idmedico' => $id));

            $em->remove($medico);
            $em->flush();
            return JsonResponse::create(array('success'=>0, 'idmedico'=>$id));
        }else{
            return JsonResponse::create(array('success'=>0, 'msg'   =>  'Codigo de medico es obligatorio'));
        }
    }
    /**
     * @Route("/medicos_codigo_zona", name="medicos_codigo_zona", methods="POST|GET")
     */
    public function medicoscodigozona(Request $request)
    {
        if (!empty($request->get('codzona'))) {
            $em = $this->getDoctrine()->getManager()->getConnection();
            $sql = "SELECT idmedico,medico FROM tbl_medicos WHERE areas like '%".$request->get('codzona')."%' ";
            $rw = $em->prepare($sql);
            $rw->execute();
            $rw = $rw->fetchAll();
            return JsonResponse::create($rw);
        }
        return JsonResponse::create(array());
    }
    private function lstAreas()
    {
        $lstAreas = $this->getDoctrine()->getRepository(\App\Entity\TblAreas::class)->findAll();
        return $lstAreas;
    }
    private function lstMedicos()
    {
        $lstMedicos = $this->getDoctrine()->getRepository(\App\Entity\TblMedicos::class)->findAll();
        return $lstMedicos;
    }
}
