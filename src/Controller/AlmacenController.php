<?php

namespace App\Controller;

use App\Controller\Printstickerpos;
use App\Entity\TblDocumentos;
use App\Entity\TblUbicaciones;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Controller\Toolclass;

/**
 * @Route("/almacen")App\
 */
class AlmacenController extends Controller
{
    /**
     * @Route("/", name="almacen")
     */
    public function index(Request $request)
    {
        return $this->render('almacen/index.html.twig', [
        ]);
    }

    /**
     * @Route("/impresion", name="impresionstickers")
     */
    public function stickers(Request $request)
    {
        return $this->render('almacen/stickers.html.twig', [
        ]);
    }

    /**
     * @Route("/impresion/sticker/individual", name="enviar_imp_sticker_individual")
     */
    public function enviarimpsticker(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tipoimp = $request->get("tipoimp");
        $codigo = $request->get("codigo");
        $print = $request->get("printer");
        $printer = new Printstickerpos($em);
        $rs = $printer->stickerIndividual($codigo,$print);
        return JsonResponse::create(array("success" => $rs));
    }

    /**
     * @Route("/impresion/sticker/multiple", name="enviar_imp_sticker_multiple")
     */
    public function enviarimpstickermultiple(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $printer = new Printstickerpos($em);
        $rs = $printer->stickerMultiple($request);
        return JsonResponse::create(array("success" => 1, 'msg' => $rs));
    }

    /**
     * @Route("/gestionar/{codigo}", name="gestionarubhc", defaults={"codigo"="0"})
     */
    public function gestionar(Request $request)
    {
        $codigo = $request->get("codigo");
        $sede = $request->get("sede");
        $em = $this->getDoctrine()->getManager();
       // $tool = new Toolclass();
       // $codigo = $tool->fhc($codigo);
       // $extn = $this-> AppExtensionTwig()->getFunctions();
        $sql = "SELECT * FROM tbl_ubicaciones u WHERE u.codhistoria=".$codigo;
        $hc = $em->getConnection()->prepare($sql);
        $hc->execute();
        $hc = $hc->fetchAll();
        $arr = [];
        if (!empty($hc)) {
            foreach ($hc as $k => $v) {
                $arr[] = [
                    // 'sede' => $tool->psedes($v['sede']),
                    'sede' => $v['sede'],
                    'codsede' => $v['sede'],
                    'codhistoria' => $codigo,
                    'caja' => $v['caja'],
                    'folio' => $v['folio'],
                    'idubicacion' => $v['idubicaciones'],
                    'estado' => $v['estado']
                ];
            }
        }
        return $this->render('almacen/gestionar.html.twig', [
            'hc' => $arr,
            'codigo' => $codigo
        ]);
    }

    /**
     * @Route("/gestionardocumentos", name="gestionardocumentos", methods="GET")
     */
    public function gestionardocumentos(Request $request)
    {
        $ini = $request->get('inicio');
        // $fin = $request->get('fin');
        $sede = $request->get('sede');
        $maxrs = 20;
        $total = 0;
        $paginador = "";
        $em = $this->getDoctrine()->getManager();
        $pagina = ($request->get("pagina") != 0) ? $request->get("pagina") : 0;
        $offset = ($request->get("pagina") != 0) ? ($request->get("pagina") - 1) * $maxrs : 0;
        $limit = " LIMIT " . $offset . "," . $maxrs . "";
        $filtro = "  ";
        $rw = [];
        //and STR_TO_DATE( '" . $fin . "' ,'%e-%c-%Y')
        //and !empty($fin)
        if (!empty($sede) and !empty($ini)) {
            $filtro = " WHERE u.codpaciente=so.codpaciente AND so.estado='7' AND so.codsede='" . $sede . "'";
            $filtro .= " AND STR_TO_DATE( CONVERT(so.fechapedido,char) ,'%e/%c/%Y') = STR_TO_DATE( '" . $ini . "' ,'%e-%c-%Y') ";
            $sql = "SELECT
        so.idsolicitud,
        so.codpaciente,
        so.codsede,
        so.nommedico,
        so.codzona,
        so.nomconsultorio,
        so.solicitud,
        so.fechapedido,
        so.horapedido,
        concat(u.nompaciente,' ',u.apellidopaterno,' ',u.apellidomaterno) as paciente
        FROM tbl_solicitudes so, tbl_paciente u " . $filtro . $limit;
            $totalRs = $em->getConnection()->prepare("SELECT count(*) as total FROM tbl_solicitudes so, tbl_paciente u " . $filtro);
            $totalRs->execute();
            $totalrs = $totalRs->fetch();
            $total = empty($totalrs['total']) ? 0 : $totalrs['total'];
            //listado
            $q = $em->getConnection()->prepare($sql);
            $q->execute();
            $rw = $q->fetchAll();
        }
        return $this->render('almacen/documentos.html.twig', [
            'solicitudes' => $rw,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/guardardocumento", name="guardardocumento", methods="POST")
     */
    public function guardardocumento(Request $request)
    {
        $documento = $request->get("documento");
        $st = 0;
        if (is_array($documento)) {
            if (!empty($documento)) {
                foreach ($documento as $k => $v) {
                    $d = $this->getDoctrine()->getRepository(TblDocumentos::class)->findOneBy(array('idsolicitud' => $v['idsolicitud']));
                    if (empty($d)) {
                        $doc = new TblDocumentos();
                        $doc->setIdsolicitud($v['idsolicitud']);
                        $doc->setHospitalizacion($v['hospitalizacion']);
                        $doc->setDocumento($v['documento']);
                        $doc->setTipo($v['tipo']);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($doc);
                        $em->flush();
                        $st = 1;
                    }
                }
            }
        }
        return JsonResponse::create(array('success' => $st));
    }

    /**
     * @Route("/guardar", name="guardarubicacion", methods="POST")
     */
    public function guardarubicacion(Request $request, UserInterface $user)
    {
        $tbl_ubicaciones = $request->get("tbl_ubicaciones");
        $responsable = $user->getIdusuario();
        $fecharegistro = date('Y-m-d H:i:s');
        if (empty($tbl_ubicaciones['codigo'])) {
            return JsonResponse::create(array('success' => 0));
        }
       
        // 

if (!empty($tbl_ubicaciones['idubicaciones']) ) {      
           
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $q = $qb->update('App\Entity\TblUbicaciones', 'u')
                ->set('u.codhistoria', $qb->expr()->literal($tbl_ubicaciones['codigo']))
                ->set('u.sede', $qb->expr()->literal($tbl_ubicaciones['sede']))
                ->set('u.caja', $qb->expr()->literal($tbl_ubicaciones['caja']))
                ->set('u.folio', $qb->expr()->literal($tbl_ubicaciones['folio']))
                ->set('u.estado', $qb->expr()->literal($tbl_ubicaciones['estado']))
                ->set('u.responsable', $qb->expr()->literal($responsable))
                ->set('u.fechaactualizado', $qb->expr()->literal($fecharegistro))                
                ->where('u.idubicaciones = ?1')
                ->setParameter(1, $tbl_ubicaciones['idubicaciones'])
                // ->setParameter(1, $idUbication)
                ->getQuery();
            $p = $q->execute();
            return JsonResponse::create(array('success' => 2));
        } else {
            $em = $this->getDoctrine()->getManager();
            $totalRs = $em->getConnection()->prepare("SELECT idubicaciones FROM tbl_ubicaciones WHERE codhistoria=".$tbl_ubicaciones['codigo']." AND sede='".$tbl_ubicaciones['sede']."' AND folio=".$tbl_ubicaciones['folio']." LIMIT 1");
            $totalRs->execute();
            $totalrs = $totalRs->fetch();
            $idUbication = $totalrs['idubicaciones'];
            
            if (!empty($idUbication)){                
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $q = $qb->update('App\Entity\TblUbicaciones', 'u')
                ->set('u.codhistoria', $qb->expr()->literal($tbl_ubicaciones['codigo']))
                ->set('u.sede', $qb->expr()->literal($tbl_ubicaciones['sede']))
                ->set('u.caja', $qb->expr()->literal($tbl_ubicaciones['caja']))
                ->set('u.folio', $qb->expr()->literal($tbl_ubicaciones['folio']))
                ->set('u.estado', $qb->expr()->literal($tbl_ubicaciones['estado']))
                ->set('u.responsable', $qb->expr()->literal($responsable))
                ->set('u.fechaactualizado', $qb->expr()->literal($fecharegistro))               
                ->where('u.idubicaciones = ?1')
                ->setParameter(1, $idUbication)
                // ->setParameter(1, $idUbication)
                ->getQuery();
                $p = $q->execute();
                return JsonResponse::create(array('success' => 2));


            }else {
                $tblubicaciones = new TblUbicaciones();
                $tblubicaciones->setCodhistoria($tbl_ubicaciones['codigo']);
                $tblubicaciones->setSede($tbl_ubicaciones['sede']);
                $tblubicaciones->setCaja($tbl_ubicaciones['caja']);
                $tblubicaciones->setFolio($tbl_ubicaciones['folio']);
                $tblubicaciones->setEstado($tbl_ubicaciones['estado']);
                $tblubicaciones->setResponsable($responsable);
                $tblubicaciones->setFecharegistro($fecharegistro);
                $tblubicaciones->setFechaactualizado($fecharegistro);               
                $em = $this->getDoctrine()->getManager();
                $em->persist($tblubicaciones);
                $em->flush();
            }
        }
        return JsonResponse::create(array('success' => 1));
    }
    /**
     * @Route("/eliminarubicacion", name="eliminarubicacion", methods="POST")
     */
    public function eliminarubicacion(Request $request, UserInterface $user)
    {
        $id = $request->get("id");
        $d = $this->getDoctrine()->getRepository(TblUbicaciones::class)->findOneBy(array('idubicaciones' => $id));
        if (!empty($d)) {
            $em = $this->getDoctrine()->getManager();
            $sql= "DELETE FROM tbl_ubicaciones WHERE idubicaciones='".$id."' ";
            $hc = $em->getConnection()->prepare($sql);
            $hc->execute();
            return JsonResponse::create(array('success' => 1));
        }
        return JsonResponse::create(array('success' => 0));
    }
}
