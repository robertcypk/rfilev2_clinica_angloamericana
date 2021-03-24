<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\TblUsuarios;
use App\Entity\TblSolicitudes;
use App\Entity\TblPaciente;
use App\Entity\TblHc;
use App\Entity\TblAlmacen;

/**
 * @Route("/validacion")
*/
class ValidacionController extends Controller
{
    /**
     * @Route("/", name="validacion", methods="POST|GET")
     */
    public function index(Request $request)
    {
        $responsable = $request->get('idUsuario');
        $idsolicitud = $request->get('idSolicitud');
        if (empty($responsable) and empty($idsolicitud)) {
            return JsonResponse::create(array('success'=>'Parametros incorrectos'));
        }
        try {
            $xres =  $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario'=>$responsable));
            if (empty($xres)) {
                return JsonResponse::create(array('success'=>'Usuario no existe'));
            }
            $check = $this->getDoctrine()->getRepository(TblSolicitudes::class)->findOneby(array('idsolicitud'=>$idsolicitud));
            if (!empty($check)) {
                $em = $this->getDoctrine()->getManager();
                $qpdf = $em->createQuery("UPDATE \App\Entity\TblSolicitudes s SET s.estado=?1, s.responsable=?2 where s.idsolicitud=?3");
                switch ($check->getEstado()) {
                case 1:
                $qpdf->setParameter(1, '2');
                $qpdf->setParameter(2, $xres->getIdusuario());
                $qpdf->setParameter(3, $idsolicitud);
                $rs = $qpdf->execute();
                return JsonResponse::create(array('success'=>'Solicitud actualizada','estado'=>stpedido(2)));
                break;
                case 2:
                $qpdf->setParameter(1, '3');
                $qpdf->setParameter(2, $xres->getIdusuario());
                $qpdf->setParameter(3, $idsolicitud);
                $rs = $qpdf->execute();
                return JsonResponse::create(array('success'=>'Solicitud actualizada','estado'=>stpedido(3)));
                break;
                case 3:
                $qpdf->setParameter(1, '4');
                $qpdf->setParameter(2, $xres->getIdusuario());
                $qpdf->setParameter(3, $idsolicitud);
                $rs = $qpdf->execute();
                return JsonResponse::create(array('success'=>'Solicitud actualizada','estado'=>stpedido(4)));
                break;
                case 4:
                $qpdf->setParameter(1, '5');
                $qpdf->setParameter(2, $xres->getIdusuario());
                $qpdf->setParameter(3, $idsolicitud);
                $rs = $qpdf->execute();
                return JsonResponse::create(array('success'=>'Solicitud actualizada','estado'=>stpedido(5)));
                break;
                case 5:
                $qpdf->setParameter(1, '6');
                $qpdf->setParameter(2, $xres->getIdusuario());
                $qpdf->setParameter(3, $idsolicitud);
                $rs = $qpdf->execute();
                return JsonResponse::create(array('success'=>'Solicitud actualizada','estado'=>stpedido(6)));
                break;
                case 6:
                $qpdf->setParameter(1, '7');
                $qpdf->setParameter(2, $xres->getIdusuario());
                $qpdf->setParameter(3, $idsolicitud);
                $rs = $qpdf->execute();
                return JsonResponse::create(array('success'=>'Solicitud actualizada','estado'=>stpedido(7)));
                break;
                }
                return JsonResponse::create(array('success'=>'No se puede actualizar solicitud'));
            } else {
                return JsonResponse::create(array('success'=>'La solicitud no existe'));
            }
        } catch (\Exception $e) {
            return JsonResponse::craete(array('success'=>$e->getMessage()));
        }
    }
}
