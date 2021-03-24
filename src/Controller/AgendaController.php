<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use \App\Entity\TblAreas;
use \App\Entity\TblSolicitudes;
use \App\Entity\TblUsuarios;

/**
 * @Route("/agenda")
 */
class AgendaController extends Controller
{
    public function index(Request $request, UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $user->getIdusuario();
        $up = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneBy(array('idusuario' => $user));

        $ini = $request->get('inicio');
        // $fin = $request->get('fin');
        $sede = $request->get('sede');
        //$maxrs = 100;
        $total = 0;
        $paginador = "";

        //$pagina = ($request->get("pagina") != 0) ? $request->get("pagina") : 0;
        //$offset = ($request->get("pagina") != 0) ? ($request->get("pagina") - 1) * $maxrs : 0;
        $limit = ""; // " LIMIT " . $offset . "," . $maxrs . "";
        if (!empty($sede) && strcmp($sede,"all")!=0) {
            $filtro = " WHERE so.codsede='" . $sede . "'";
        } else {
            if ($up->getRol() == 'ROLE_ADMIN') {
                $filtro = " WHERE so.codsede in ('01','02','03','04') ";
            } else {
                // $slt_area = "SELECT a.sede FROM tbl_usuarios u, tbl_areas a WHERE a.idarea=u.idarea AND u.idusuario='" . $up->getIdusuario() . "'";
                // $codzona_area_u = $em->getConnection()->prepare($slt_area);
                // $codzona_area_u->execute();
                // $codzona_area_u = $codzona_area_u->fetch();
                //$filtro = " WHERE so.codsede='" . $codzona_area_u['sede'] . "' ";
                $filtro = " WHERE so.responsablec='". $up->getEmail()."' ";
            }
        }
        $rw = [];
        $sql = "SELECT distinct
                so.idsolicitud,
                so.codpaciente,
                so.codsede,
                so.nommedico,
                so.codzona,
                so.nomconsultorio,
                so.registro as solicitud,
                date (fechapedido) as fechapedido,
                TIME(so.fechapedido) as horapedido,
                (select concat(u.nompaciente,' ',u.apellidopaterno,' ',u.apellidomaterno) from tbl_paciente u where u.codpaciente=so.codpaciente limit 1) as paciente,
                so.estado,
                so.idhc as codhistoria,
                so.folio,
                (select u.estado from tbl_ubicaciones u where  u.codhistoria=so.idhc and u.sede=so.codsede limit 1 ) as tipohc
                FROM
                tbl_solicitudes so " ;
        // and !empty($fin)
        if ($up->getRol() != 'ROLE_ADMIN') {
            if (!empty($ini)) {
                // $filtro .= " AND (so.idhc IS NOT NULL OR so.idhc != 0) AND so.estado not in (8) AND STR_TO_DATE( CONVERT(so.fechapedido,char) ,'%e/%c/%Y') = STR_TO_DATE( '" . $ini . "' ,'%e-%c-%Y')";
                $filtro .= " AND date (so.fechapedido) = STR_TO_DATE( '" . $ini . "' ,'%e-%c-%Y') ORDER BY TIME (so.fechapedido) ASC ";
                $totalRs = $em->getConnection()->prepare("SELECT count(*) as total FROM tbl_solicitudes so " . $filtro);
                $totalRs->execute();
                $totalRs = $totalRs->fetch();
                $total = $totalRs["total"];
                $sql = $sql . $filtro . $limit;
                //listado
                $q = $em->getConnection()->prepare($sql);
                $q->execute();
                $rw = $q->fetchAll();
            }
        } else {
            if (!empty($sede) and !empty($ini)) {
                // $filtro .= " AND (so.idhc IS NOT NULL OR so.idhc != 0) AND STR_TO_DATE( CONVERT(so.fechapedido,char) ,'%e/%c/%Y') = STR_TO_DATE( '" . $ini . "' ,'%e-%c-%Y')";
                $filtro .= " AND date (so.fechapedido) = STR_TO_DATE( '" . $ini . "' ,'%d-%m-%Y') ORDER BY TIME (so.fechapedido) ASC ";
                $totalRs = $em->getConnection()->prepare("SELECT count(*) as total FROM tbl_solicitudes so " . $filtro);
                $totalRs->execute();
                $totalRs = $totalRs->fetch();
                $total = $totalRs["total"];

                $sql = $sql . $filtro . $limit;
                //listado
                $q = $em->getConnection()->prepare($sql);
                $q->execute();
                $rw = $q->fetchAll();
            }
        }

        return $this->render('agenda/index.html.twig', [
            'solicitudes' => $rw,
            'total' => $total,
        ]);
    }
    /**
     * @Route("/nueva-solicitud", name="nuevasolicitud", methods="POST|GET")
     */
    public function nuevasolicitud(Request $request): Response
    {
        // $doctores = $this->getDoctrine()->getRepository(TblUsuarios::class)->findBy(array('rol' => 'ROLE_DOCTOR'));
        $doctores = $this->getDoctrine()->getRepository(TblMedicos::class)->findAll();
        $consultorios = $this->getDoctrine()->getRepository(TblUsuarios::class)->findBy(array('rol' => 'ROLE_CONSULTORIO'));
        $areas = $this->getDoctrine()->getRepository(TblAreas::class)->findAll();
        return $this->render('agenda/new.html.twig', [
            'doctores' => $doctores,
            'consultorios' => $consultorios,
            'areas' => $areas,
        ]);
    }

    /**
     * @Route("/nueva-solicitud-simple", name="nuevasolicitudsimple", methods="POST|GET")
     */
    public function nuevasolicitudsimple(Request $request, UserInterface $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $user->getIdusuario();
        $up = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneBy(array('idusuario' => $user));
        //
        /*if (!empty($up->getMedicos())) {
            $medicos = explode(",", $up->getMedicos());
            $doctores = $this->getDoctrine()->getRepository(\App\Entity\TblMedicos::class)->findBy(array('idmedico' => $medicos));

        } else {
            $doctores = [];
        }*/
        $doctores = $this->getDoctrine()->getRepository(\App\Entity\TblMedicos::class)->findAll();
        $consultorios = $this->getDoctrine()->getRepository(TblUsuarios::class)->findBy(array('rol' => 'ROLE_CONSULTORIO'));
        $areas = $this->getDoctrine()->getRepository(TblAreas::class)->findOneBy(array('idarea' => $up->getIdarea()));
        //
     
           return $this->render('agenda/new_simple.html.twig', [ 
            'doctores' => $doctores,
            'consultorios' => $consultorios,
            'areas' => $areas,
            'user' => $user,
            'lstAreas' => $this->lstAreas(),
        ]);
    }

    /**
     * @Route("/buscar-hc", name="buscarhc", methods="POST")
     */
    public function buscarhc(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $codhistoria = preg_replace("/[^0-9]/", "", $request->get('codhistoria'));
        $codhistoria = str_pad($codhistoria, 7, '0', STR_PAD_LEFT);
        $codsede = $request->get('codsede');
        if (empty($codhistoria)) {
            return JsonResponse::create(array());
        }
        if (!empty($codesede)) {
            $sql = "SELECT concat(p.nompaciente,' ',p.apellidopaterno,' ',p.apellidomaterno) as nombrepaciente,
        p.codpaciente,
        h.codhistoria
         FROM tbl_hc h, tbl_paciente p
         WHERE p.codpaciente=h.codpaciente
         AND h.codhistoria='" . $codhistoria . "' AND h.sede='" . $codsede . "'";
        } else {
            $sql = "SELECT concat(ifnull(p.nompaciente,''),' ',ifnull(p.apellidopaterno,''),' ',ifnull(p.apellidomaterno,'')) as nombrepaciente,
        p.codpaciente,
        h.codhistoria
         FROM tbl_hc h, tbl_paciente p
         WHERE p.codpaciente=h.codpaciente
         AND h.codhistoria='" . $codhistoria . "' ";
        }
        $hc = $em->getConnection()->prepare($sql);
        $hc->execute();
        $rs = $hc->fetch();
        return JsonResponse::create($rs);
    }

    /**
     * @Route("/buscadorhc", name="buscadorhc", methods="POST")
     */
    public function buscadorhc(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $texto = $request->get('q');
        $codsede = $request->get('sede');
        if (empty($texto)) {
            return JsonResponse::create(array());
        }
        $maxrs = 10; //50
        $offset = ($request->get("page") != 0) ? ($request->get("page") - 1) * $maxrs : 0;
         $limit = " LIMIT " . $offset . "," . $maxrs . "";
        //$limit = " LIMIT " . $maxrs . "";

        $texto = preg_replace("/[']/", "\'", $texto);

        $sql = "SELECT concat(ifnull(p.nompaciente,''),' ',ifnull(p.apellidopaterno,''),' ',ifnull(p.apellidomaterno,'')) as nombrepaciente,
		ifnull(p.nompaciente,'') as nombre,
		ifnull(p.apellidomaterno,'') as materno,
		ifnull(p.apellidopaterno,'') as paterno,
		p.codpaciente,
		ifnull(h.codhistoria,'0000000') as codhistoria,
		ifnull(u.sede,0) as sede,
        ifnull(u.caja,0) as caja, 
        ifnull(u.folio,0) as folio,
        ifnull(u.estado, 0) as tipohistoria
		FROM tbl_paciente p
        left join tbl_hc h ON h.codpaciente = p.codpaciente
        left join tbl_ubicaciones u ON u.codhistoria = h.codhistoria 
        AND u.sede = if (h.sede = '02', h.sede, '01')
		WHERE p.busqueda like '" . $texto . "%'
        ORDER BY h.sede asc " . $limit;
       // AND p.busqueda like '%" . $texto . "%'
       // AND h.sede='" . $codsede . "' ORDER BY h.sede asc   " . $limit;
        $hc = $em->getConnection()->prepare($sql);
        $hc->execute();
        $rs = $hc->fetchAll();
        return JsonResponse::create(array('items' => $rs, 'total_count' => count($rs), 'limit' => $maxrs, 'incomplete_results' => false));
    }


    /**
     * @Route("/listhc", name="listhc", methods="POST")
     */
    public function listarhc(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $codhistoria = preg_replace("/[^0-9]/", "", $request->get('codhistoria'));
        $codhistoria = str_pad($codhistoria, 7, '0', STR_PAD_LEFT);
        $codfolio =  empty($request->get('codfolio'))? 0: $request->get('codfolio');       
        if (empty($codhistoria)) {
            return JsonResponse::create(array());
        }      
        
            $sql = "SELECT concat(ifnull(p.nompaciente,''),' ',ifnull(p.apellidopaterno,''),' ',ifnull(p.apellidomaterno,'')) as nombrepaciente,
            ifnull(p.nompaciente,'') as nombre, 
            ifnull(p.apellidomaterno,'') as materno, 
            ifnull(p.apellidopaterno,'') as paterno, 
            p.codpaciente,
            ifnull(h.codhistoria,'0000000') as codhistoria,
            ifnull(u.sede,0) as sede,
            ifnull(u.caja,0) as caja,
            ifnull(u.folio,0) as folio,
            ifnull(u.estado,0) as tipohistoria
            FROM tbl_paciente p
            LEFT JOIN tbl_hc h ON p.codpaciente = h.codpaciente
            LEFT JOIN tbl_ubicaciones u ON u.codhistoria = h.codhistoria   
            AND u.sede = if (h.sede = '02', h.sede, '01')
            WHERE h.codhistoria = '" . $codhistoria . "'           
            AND ifnull(u.folio,0) = '" . $codfolio . "'
            ORDER BY h.sede asc ";           
    
        $hc = $em->getConnection()->prepare($sql);
        $hc->execute();
        //$rs = $hc->fetch();
        $rs = $hc->fetchAll();      
        return JsonResponse::create($rs);
    }
    /**
     * @Route("/buscadorhcdni", name="buscadorhcdni", methods="POST")
     */
    public function buscadorhcdni(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dni = $request->get('dni');
        $codsede = $request->get('sede');
        if (empty($dni)) {
            return JsonResponse::create(array());
        }
        $maxrs = 10; //50
        $offset = ($request->get("page") != 0) ? ($request->get("page") - 1) * $maxrs : 0;
         $limit = " LIMIT " . $offset . "," . $maxrs . "";
        //$limit = " LIMIT " . $maxrs . "";

        $dni = preg_replace("/[']/", "\'", $dni);

        $sql = "SELECT concat(ifnull(p.nompaciente,''),' ',ifnull(p.apellidopaterno,''),' ',ifnull(p.apellidomaterno,'')) as nombrepaciente,
		ifnull(p.nompaciente,'') as nombre,
		ifnull(p.apellidomaterno,'') as materno,
		ifnull(p.apellidopaterno,'') as paterno,
		p.codpaciente,
		ifnull(h.codhistoria,'0000000') as codhistoria,
		ifnull(u.sede,0) as sede,
        ifnull(u.caja,0) as caja,
        ifnull(u.folio,0) as folio,
        ifnull(u.estado,0) as tipohistoria
        FROM tbl_paciente p         
        left join tbl_hc h ON h.codpaciente = p.codpaciente
        left join tbl_ubicaciones u ON u.codhistoria = h.codhistoria 
        AND u.sede = if (h.sede = '02', h.sede, '01')
        where p.idpaciente = (SELECT idpaciente from tbl_paciente where numdocumento = '".$dni."' limit 1 )
        
        ORDER BY h.sede asc " . $limit;
        // AND u.sede = if (h.sede = '02', h.sede, '01')
       // AND p.busqueda like '%" . $texto . "%'
       // AND h.sede='" . $codsede . "' ORDER BY h.sede asc   " . $limit;

        $hc = $em->getConnection()->prepare($sql);
        $hc->execute();
        $rs = $hc->fetchAll();
        return JsonResponse::create(array('items' => $rs, 'total_count' => count($rs), 'limit' => $maxrs, 'incomplete_results' => false));
    }

    /**
     * @Route("/realizar-nueva-solicitud", name="realizarnuevasolicitud", methods="POST" )
     */
    public function realizarnuevasolicitud(Request $request, UserInterface $user)
    {
        $msg = '';
        $codhistoria = $request->get('codhistoria');
        $sede = $request->get('sede');
        $sedehc = $request->get('sedehc');
        $tipohc = $request->get('tipohc');
        $fechapedido = $request->get('fechapedido');        
        $fechapedidoSys = str_replace ("/","-",$fechapedido);
        $horapedido = $request->get('horapedido');
        $fechahorapedido = date("Y-m-d", strtotime($fechapedidoSys))." ". $horapedido;
        $fecharegistro = date('Y-m-d H:i:s');
        $dia =  date(strtotime($fechapedidoSys));
        $codtipopedido = $request->get('codtipopedido');
        
        // $dnuevo = $request->get('dnuevo');
        // $anulado = $request->get('anulado');
        $codzona = $request->get('codzona');
        // $idcita = $request->get('idcita');
        $reqplaca = $request->get('placa');
        $codpaciente = $request->get('codpaciente');
        $folio = $request->get('folio');
        $responsable = $request->get('responsable');
        $observaciones = $request->get('msg');
        // $nomconsultorio = '';
        // $codconsultorio = '';
        // $codmedico = '';
        // $nommedico = '';
        
        $medico = $request->get('codmedico');
        if (empty($medico)) {
            return JsonResponse::create(array('msg' => "Médico es requerido",'success' => '2'));
        }
        if (!empty($medico)) {
            $medico = explode('-', $medico);
            $codmedico = $medico[0];
            $nommedico = $medico[1];
        }
        $consultorio = $request->get('codconsultorio');
        if (!empty($consultorio)) {
            $consultorio = explode('-', $consultorio);
            $codconsultorio = $consultorio[0];
            $nomconsultorio = $consultorio[1];
        }
        /* registro  */
        $codhistoria = empty($codhistoria) ? null : (int) $codhistoria;
        if (empty($sedehc)) {
            return JsonResponse::create(array('msg' => "No registra archivo físico",'success' => '2'));
        }
        if (empty($sede)) {
            return JsonResponse::create(array('msg' => "Zona de entrega es requerido",'success' => '2'));
        }
        if (empty($fechapedido)) {
            return JsonResponse::create(array('msg' => "Fecha de pedido requerido",'success' => '2'));
        }
        if (empty($horapedido)) {
            return JsonResponse::create(array('msg' => "Hora de pedido requerido",'success' => '2'));
        }
        if (empty($codzona)) {
            return JsonResponse::create(array('msg' => "Zona de entrega requerido",'success' => '2'));
        }
        $codmedico = empty($codmedico) ? "" : $codmedico;
        $nommedico = empty($nommedico) ? "" : $nommedico;
        $codconsultorio = empty($codconsultorio) ? "" : $codconsultorio;
        $nomconsultorio = empty($nomconsultorio) ? "" : $nomconsultorio;  
        if ( $codtipopedido != 'A'){
            if(date('Y-m-d', $dia) == date('Y-m-d')){
                $codtipopedido = "N";
            } else{
                $codtipopedido = "P";
            }
        }
        $reqplaca = empty($reqplaca) ? "0" : $reqplaca;
        // solicitud
        if (empty($codpaciente)) {
            return JsonResponse::create(array('msg' => "Codigo de paciente requerido",'success' =>'2'));
        }
        $user = $request->get("user");
        $em = $this->getDoctrine()->getManager();

        // ya registrado        
        $sql_time ="SELECT * FROM tbl_solicitudes 
                    WHERE 
                    DATE (fechapedido) = STR_TO_DATE('".$fechapedido."','%d/%m/%Y')
                    AND TIME (fechapedido) =  TIME_FORMAT('". $horapedido."','%H:%i:%s')
                    AND idhc ='". $codhistoria."' AND folio ='". $folio."'";
        $qtime = $em->getConnection()->prepare($sql_time);
        $qtime->execute();
        $qtime_rs = $qtime->fetch();
        if (!empty($qtime_rs)) {
            return JsonResponse::create(array('msg' => "La solicitud ya se encuentra registrada",'success' => '3'));
        }
        //si es interconsulta
        //$sql = "SELECT s.idsolicitud FROM tbl_solicitudes s WHERE s.codpaciente=" . $codpaciente . " AND DATE (fechapedido)= DATE_FORMAT(now(),'%Y-%c-%e') AND s.estado=4 limit 1";
        $sql = "SELECT idsolicitud FROM tbl_solicitudes 
                WHERE 
                DATE (fechapedido) = curdate()
                and idhc = '". $codhistoria."'
                and estado = 4
                limit 1";
        $q = $em->getConnection()->prepare($sql);
        $q->execute();
        $csol = $q->fetch();
       
        if (!empty($csol)) {
            $interc = $this->interconsulta($user, $csol['idsolicitud'], $codtipopedido, $fechahorapedido, $codmedico, $nommedico, $reqplaca, $folio, $codzona);         
            if ($interc == 1 ){
                    return JsonResponse::create(array('msg' => "Registrada como INTERCONSULTA",'success' => '1'));
            }else {
                return JsonResponse::create(array('msg' => "Error de registro, intentelo nuevamente",'success' => '2', 'detalle'=>$interc ));
            }   
            die();         
        }

        $user = empty($user) ? 0 : $user;
        $up = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneBy(array('idusuario' => $user));
        $respnom = empty($up) ? "" : $up->getEmail();
        $pedido = new TblSolicitudes();
        $pedido->setRegistro($fecharegistro);
        $pedido->setIdhc($codhistoria);
        $pedido->setCodsede($sedehc);
        $pedido->setFechapedido($fechahorapedido);
        $pedido->setCodtipopedido($codtipopedido);
        $pedido->setCodmedico($codmedico);
        $pedido->setNommedico($nommedico);
        $pedido->setDnuevo('1');
        $pedido->setAnulado('0');
        $pedido->setEstado('0');
        $pedido->setCodzona($codzona);
        $pedido->setCodpaciente($codpaciente);
        $pedido->setCodconsultorio($codconsultorio);
        $pedido->setNomconsultorio($nomconsultorio);
        $pedido->setIdcita('0');
        $pedido->setReqplaca($reqplaca);
        $pedido->setObservaciones($observaciones);
        $pedido->setCumplimiento("");
        $pedido->setResponsable($user);
        $pedido->setResponsableb("");
        $pedido->setResponsablec($respnom);
        $pedido->setFolio($folio);
        $em->persist($pedido);
        $em->flush();

        //Imprimir
        if(date('Y-m-d', $dia) == date('Y-m-d')){
            try {                   
                $idsolicitud = $pedido->getIdsolicitud();
                 $print = new Printerpos($em);
                 $stprint = $print->boucher($user, $idsolicitud, $folio);
             } catch (\Expcetion $e) {
                 return JsonResponse::create(array("msg" => $e->getMessage()));
             }
         }
        return JsonResponse::create(array('msg' => "Solicitud registrada correctamente",'success' => '1'));
    }
    private function interconsulta($user, $idsolicitud, $codtipopedido, $horapedido, $codmedico, $nommedico, $reqplaca, $folio, $codzona)
    {
        try {
            $sql = "UPDATE tbl_solicitudes
                    SET  estado='3',
                    folio='" . $folio . "',
                    responsable='" . $user . "',
                    codtipopedido='I',
                    codmedico='" . $codmedico . "',
                    nommedico='" . $nommedico . "',
                    fechapedido='" . $horapedido. "',                    
                    reqplaca = '" . $reqplaca . "',
                    codzona='". $codzona . "'
                    WHERE idsolicitud='" . $idsolicitud . "' ";                    
                    $em = $this->getDoctrine()->getManager();
                    $q = $em->getConnection()->prepare($sql);
                    $q->execute(); 
                    
                   $print = new Printerpos($em);
                   $stprint = $print->boucher($user, $idsolicitud, $folio);
                     // return "Solicitud fue actualizada a Interconsulta";              
                    return 1; 
       } catch (\Exception $e) {
            return $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }       
      
    }
    private function lstAreas()
    {
        $lstAreas = $this->getDoctrine()->getRepository(\App\Entity\TblAreas::class)->findAll();
        return $lstAreas;
    }
}
