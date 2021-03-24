<?php

namespace App\Controller;

use App\Controller\Printerpos;
use App\Entity\TblAlmacen;
use App\Entity\TblHc;
use App\Entity\TblPaciente;
use App\Entity\TblSolicitudes;
use App\Entity\TblUsuarios;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/{estado}", name="dashboard")
     */
    public function index(Request $request, UserInterface $user)
    {
        // lista de solicitudes en curso
        $maxrs = 500; //50
        $total = 0;
        $paginador = "";
        $estado = ($request->get("estado") != 0) ? $request->get("estado") : 1;
        $pagina = ($request->get("pagina") != 0) ? $request->get("pagina") : 0;
        $offset = ($request->get("pagina") != 0) ? ($request->get("pagina") - 1) * $maxrs : 0;
        //" . $offset . "," .
      
        $em = $this->getDoctrine()->getManager();

        $user = $user->getIdusuario();
        $up = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneBy(array('idusuario' => $user));
        if ($up->getRol() == 'ROLE_SOLICITANTE') {
            // $slt_area = "SELECT a.codzona,a.sede FROM tbl_usuarios u, tbl_areas a WHERE a.idarea=u.idarea AND u.idusuario='" . $up->getIdusuario() . "'";
            // $codzona_area_u = $em->getConnection()->prepare($slt_area);
            // $codzona_area_u->execute();
            // $codzona_area_u = $codzona_area_u->fetch();
            // $filtro = "s.estado in (1,2,3,4) AND s.codzona='" . $codzona_area_u['codzona'] . "' ";
            $filtro = "s.estado in (1,2,3,4) AND s.responsablec='" . $up->getEmail() . "' ";
        } else {
            $filtro = "s.estado = '".$estado."' ";
        }

        // $limit = " LIMIT " . $offset . "," . $maxrs . "";
        $limit = " ";
        // (SELECT u.rol FROM tbl_usuarios u WHERE u.idusuario=s.responsable) AS rol,
        // (SELECT u.imagen FROM tbl_usuarios u WHERE u.idusuario=s.responsable) AS imagen,
        $sql = "SELECT 
                s.idsolicitud,
                s.fechapedido,
                TIME(s.fechapedido) AS horapedido,
                s.registro,
                s.responsable,
                s.responsablec,                
                s.estado,
                s.codmedico,
                s.nommedico,
                s.codsede,
                s.codzona,
                s.nomconsultorio,
                s.dnuevo,
                s.codtipopedido,
                s.codpaciente,
                s.idhc,
                s.folio
                FROM tbl_solicitudes s
                WHERE ".$filtro."
                AND s.idhc != 0
                ORDER by DATE(s.fechapedido) ASC, TIME(s.fechapedido) ASC ";
        $totalRs = $em->getConnection()->prepare("SELECT count(s.idsolicitud) as total FROM tbl_solicitudes s WHERE s.estado  = '". $estado ."' ");
        $totalRs->execute();
        $totalRs = $totalRs->fetch();
        $total = $totalRs['total'];

        //
        $q = $em->getConnection()->prepare($sql);
        $q->execute();
        $rw = $q->fetchAll();

        $em->flush();

        $providers_pag = new \App\Controller\Genpaginator;
        $paginador = $providers_pag->generatePaging($total, $maxrs, $pagina, $request->getUri());
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'solicitudes' => $rw,
            'paginador' => $paginador,
            'responsables' => $this->responsables(),
            'estado'=> $estado,
        ]);
    }

    private function responsables()
    {
        $responsables = $this->getDoctrine()->getRepository(TblUsuarios::class)->findBy(array('rol' => 'ROLE_MENSAJERO'));
        return $responsables;
    }
    /**
     * @Route("/solicitudes_pendientes", name="solicitudes_pendientes")
     */
    public function solicitudes_pendientes(Request $request, UserInterface $user): Response
    {
        $em = $this->getDoctrine()->getManager();

        $user = $user->getIdusuario();
        $up = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneBy(array('idusuario' => $user));
        if ($up->getRol() == 'ROLE_SOLICITANTE') {
            $slt_area = "SELECT a.codzona,a.sede FROM tbl_usuarios u, tbl_areas a WHERE a.idarea=u.idarea AND u.idusuario='" . $up->getIdusuario() . "' ";
            $codzona_area_u = $em->getConnection()->prepare($slt_area);
            $codzona_area_u->execute();
            $codzona_area_u = $codzona_area_u->fetch();
            // $filtro = " AND s.codzona='" . $codzona_area_u['codzona'] . "' AND s.codsede='". $codzona_area_u['sede'] ."' ";
            $filtro = " AND s.codzona='" . $codzona_area_u['codzona'] . "' ";
        } else {
            $filtro = "  ";
        }
        return $this->render('dashboard/tabhome.html.twig', ['filtro' => $filtro]);
    }
    /**
     * @Route("/ajax_solicitudes_pendientes", name="ajax_solicitudes_pendientes")
     */
    public function ajax_solicitudes_pendientes(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
       
        $sql = "SELECT
        s.idsolicitud,        
        s.registro,
        s.fechapedido AS pedido,
        DATE(s.fechapedido) as fechapedido,
        TIME(s.fechapedido) as horapedido,
        s.estado,
        s.codsede,
        s.codzona,
        s.nomconsultorio,
        s.codpaciente,
        s.responsable,       
        s.responsablec,       
        s.dnuevo,
        s.codtipopedido,
        s.idhc,   
        s.folio       
        FROM tbl_solicitudes s
        WHERE s.estado in (0)
        AND s.idhc != 0
        AND DATE(s.fechapedido) = curdate()
        ORDER by TIME(s.fechapedido) ASC";
        $rw = $em->getConnection()->prepare($sql);
        $rw->execute();
        $rw = $rw->fetchAll();
        if (!empty($rw)) {
            // return JsonResponse::create($rw);
            return $this->render('dashboard/tabhome.html.twig', ['solicitudes' => $rw]);
        }
        return JsonResponse::create(array());
    }

    
    /**
     * @Route("/imprimir_solicitud", name="imprimir_solicitud")
     */
    public function imprimir_solicitud(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $idsolicitud = $request->get("idsolicitud");
        $iduser = $request->get("idusuario");

        if (empty($idsolicitud)) {
            return JsonResponse::create(array('success' => 0));
        }
        try {           
            $print = new Printerpos($em);
            $stprint = $print->boucher( $iduser, $idsolicitud, '0');    
        } catch (\Expcetion $e) {
            return JsonResponse::create(array("success" => 0, "err" => $e->getMessage()));
        }
        return JsonResponse::create(array('success' => 1));
    }

    /**
     * @Route("/aceptar_solicitud", name="aceptar_solicitud")
     */
    public function aceptar_solicitud(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->get("solicitud");
        $stprint = 0;

        if (empty($params)) {
            return JsonResponse::create(array('success' => 0));
        }
        try {
            // $fichero = __DIR__ . "/../../public/fn-aceptar_solicitud.txt";
            // file_put_contents($fichero, json_encode($params) . "\n", FILE_APPEND);
            $print = new Printerpos($em);
            foreach ($params as $key => $item) {
                if (!empty($item['idsolicitud'])) {
                    $qpdf = $em->createQuery("UPDATE \App\Entity\TblSolicitudes s SET  s.estado='1', s.responsable='" . $item['user'] . "' WHERE s.idsolicitud='" . $item['idsolicitud'] . "'");
                    $rs = $qpdf->execute();
                    /* agregar boucher */
                   // $stprint = $print->boucher($item['user'], $item['idsolicitud'], '0');
                    /* */
                }
            }
        } catch (\Expcetion $e) {
            return JsonResponse::create(array("success" => 0, "err" => $e->getMessage()));
        }
        return JsonResponse::create(array('success' => 1));
    }


    /**
     * @Route("/procesar_solicitud", name="procesar_solicitud")
     */
    public function procesar_solicitud(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $request->get("user");
        $estado = $request->get("estado");
        $confirm = empty($request->get("confirm")) ? "" : $request->get("confirm");
        $idsolicitud = $request->get("idsolicitud");

        $so = $request->get('so');
        if (!empty($so)) {
            try {
                foreach ($so as $k => $v) {
                    if (!empty($v['idsolicitud'])) {
                        $qpdf = $em->createQuery("UPDATE \App\Entity\TblSolicitudes s SET  s.estado='" . $v['estado'] . "', s.responsable='" . $v['user'] . "' where s.idsolicitud='" . $v['idsolicitud'] . "'");
                        $rs = $qpdf->execute();
                        $this->historial($v['idsolicitud'], $this->getemail($v['user']), $this->getcodh($v['idsolicitud']), $this->getidarea($v['user']), $confirm, $v['estado']);
                    }
                }
            } catch (\Expcetion $e) {
                return JsonResponse::create(array("success" => 0, "err" => $e->getMessage()));
            }
        } else {
            if (empty($user) and empty($estado) and empty($idsolicitud)) {
                return JsonResponse::create(array('success' => 0));
            }
            try {
                $qpdf = $em->createQuery("UPDATE \App\Entity\TblSolicitudes s SET  s.estado='" . $estado . "', s.responsable='" . $user . "', s.cumplimiento='" . $confirm . "' where s.idsolicitud='" . $idsolicitud . "'");
                $rs = $qpdf->execute();
                $this->historial($idsolicitud, $this->getemail($user), $this->getcodh($idsolicitud), $this->getidarea($user), $confirm, $estado);
            } catch (\Expcetion $e) {
                return JsonResponse::create(array("success" => 0, "err" => $e->getMessage()));
            }
        }
        return JsonResponse::create(array('success' => 1));
    }
    private function getemail($user)
    {
        $xres = $this->getDoctrine()->getRepository(\App\Entity\TblUsuarios::class)->findOneby(array('idusuario' => $user));
        return $xres->getEmail();
    }
    private function getidarea($user)
    {
        $xres = $this->getDoctrine()->getRepository(\App\Entity\TblUsuarios::class)->findOneby(array('idusuario' => $user));
        return $xres->getIdarea();
    }
    private function getcodh($idsolicitud)
    {
        $check = $this->getDoctrine()->getRepository(\App\Entity\TblSolicitudes::class)->findOneby(array('idsolicitud' => $idsolicitud));
        $chc = $this->getDoctrine()->getRepository(\App\Entity\TblHc::class)->findOneby(array('codpaciente' => $check->getCodpaciente()));
        return $chc->getCodhistoria();
    }
    private function historial($idsolicitud, $responsable, $codigohc, $ubicacion, $comentarios, $estado)
    {
        $historial = new \App\Entity\TblHistorial();
        $historial->setIdsolicitud($idsolicitud);
        $historial->setIdhc($codigohc);
        $historial->setUbicacion($ubicacion);
        $historial->setEstatus($estado);
        $historial->setUsuario($responsable);
        $historial->setFecha(date('Y-m-d H:i:s'));
        $historial->setComentarios($comentarios);
        $em = $this->getDoctrine()->getManager();
        $em->persist($historial);
        $em->flush();
    }
    /**
     * @Route("/excel_pacientes", name="excelpacientes")
     */
    public function excelpacientes(Request $request)
    {
        return $this->render('dashboard/excelpacientes.html.twig', [
        ]);
    }
    /**
     * @Route("/procesar_excel_pacientes", name="procesarexcelpacientes")
     */
    public function procesarexcelpacientes(Request $request)
    {
        /* subir excel */
        $xml = $request->files->get('xml');
        $path = str_replace(array('/src', '/Controller'), array('', ''), __DIR__) . '/public/xml';
        if (empty($xml)) {
            return jsonResponse::create(array('success' => 0));
        }
        $xml->move($path, $xml->getClientOriginalName());
        //$excel = \PHPExcel_IOFactory::load($path.'/'.$xml->getClientOriginalName());
        /* procesar */
        $inputFileName = $path . "/" . $xml->getClientOriginalName();
        //
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

        $columnas = array();
        for ($row = 1; $row <= 1; ++$row) {
            for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $columnas[$worksheet->getCellByColumnAndRow($col, $row)->getValue()] = "";
            }
        }
        $rs = array();
        $c = 0;
        for ($row = 2; $row <= $highestRow; ++$row) {
            $rs[] = [
                'setNompaciente' => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
                'setCodpaciente' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                'setApellidopaterno' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                'setApellidomaterno' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                'setCodtipodocumento' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                'setNumdocumento' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                'setFechanacimiento' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                'setEmail' => empty($worksheet->getCellByColumnAndRow(8, $row)->getValue()) ? '-' : $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                'setTelefono' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                'setCelular' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                'setPais' => empty($worksheet->getCellByColumnAndRow(11, $row)->getValue()) ? '1' : $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
                'setCodubigeo' => empty($worksheet->getCellByColumnAndRow(12, $row)->getValue()) ? '0' : $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                'setDepartamento' => empty($worksheet->getCellByColumnAndRow(13, $row)->getValue()) ? '0' : $worksheet->getCellByColumnAndRow(13, $row)->getValue(),
                'setLocalidad' => empty($worksheet->getCellByColumnAndRow(14, $row)->getValue()) ? '0' : $worksheet->getCellByColumnAndRow(14, $row)->getValue(),
                'setDireccion' => empty($worksheet->getCellByColumnAndRow(15, $row)->getValue()) ? '-' : $worksheet->getCellByColumnAndRow(15, $row)->getValue(),
                'setNumdireccion' => empty($worksheet->getCellByColumnAndRow(16, $row)->getValue()) ? '0' : $worksheet->getCellByColumnAndRow(16, $row)->getValue(),
                'setCodigopostal' => empty($worksheet->getCellByColumnAndRow(17, $row)->getValue()) ? '0' : $worksheet->getCellByColumnAndRow(17, $row)->getValue(),
                'setRegistro' => date('Y-m-d H:i:s'),
                'setEstado' => '0',
            ];
        }
        /* registro */
        //
        foreach ($rs as $key => $value) {
            /* verificar existentes */
            $pct = $this->getDoctrine()->getRepository(TblPaciente::class)->findOneBy(array('codpaciente' => $value['setCodpaciente']));
            if (empty($pct)) {
                /* insertar */
                $s = new TblPaciente();
                foreach ($value as $key => $v) {
                    $s->$key(mb_convert_encoding($v, 'UTF-8'));
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($s);
                $em->flush();
            }
        }
        unlink($path . '/' . $xml->getClientOriginalName());
        /* */
        return jsonResponse::create(array('success' => 1));
    }
    /**
     * @Route("/procesar_excel_historias", name="procesarexcelhistorias")
     */
    public function procesarexcelhistorias(Request $request)
    {
        /* subir excel */
        $xml = $request->files->get('xml');
        $path = str_replace(array('/src', '/Controller'), array('', ''), __DIR__) . '/public/xml';
        if (empty($xml)) {
            return jsonResponse::create(array('success' => 0));
        }
        $xml->move($path, $xml->getClientOriginalName());
        /* procesar */
        $inputFileName = $path . "/" . $xml->getClientOriginalName();
        //
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $columnas = array();
        for ($row = 1; $row <= 1; ++$row) {
            for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $columnas[$worksheet->getCellByColumnAndRow($col, $row)->getValue()] = "";
            }
        }
        $rs = array();
        $c = 0;
        for ($row = 2; $row <= $highestRow; ++$row) {
            $rs[] = [
                'setCodpaciente' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                'setSede' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                'setTipohc' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                'setCodhistoria' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
            ];
        }
        /* registro */
        //
        foreach ($rs as $key => $value) {
            /* verificar existentes */
            $check = $this->getDoctrine()->getRepository(TblHc::class)->findOneby(array('sede' => $value['setSede'], 'codhistoria' => $value['setCodhistoria']));
            if (empty($check)) {
                /* */
                $TblPaciente = $this->getDoctrine()->getRepository(TblPaciente::class)->findOneby(array('codpaciente' => $value['setCodpaciente']));
                if (!empty($TblPaciente)) {
                    /* insertar */
                    $s = new TblHc();
                    foreach ($value as $key => $v) {
                        if ($key != 'setCodpaciente') {
                            $s->$key(mb_convert_encoding($v, 'UTF-8'));
                        } else {
                            $s->setcodpaciente($TblPaciente);
                        }
                    }
                    $s->setRegistro(\DateTime::createFromFormat('Y-m-d', date("Y-m-d")));
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($TblPaciente);
                    $em->persist($s);
                    $em->flush();
                }
            }
        }
        unlink($path . '/' . $xml->getClientOriginalName());
        /* */
        return jsonResponse::create(array('success' => 1));
    }
    /**
     * @Route("/procesar_excel_pedidos", name="procesarexcelpedidos")
     */
    public function procesarexcelpedidos(Request $request)
    {
        /* subir excel */
        $xml = $request->files->get('xml');
        $path = str_replace(array('/src', '/Controller'), array('', ''), __DIR__) . '/public/xml';
        if (empty($xml)) {
            return jsonResponse::create(array('success' => 0));
        }
        $xml->move($path, $xml->getClientOriginalName());
        /* procesar */
        $inputFileName = $path . "/" . $xml->getClientOriginalName();
        //
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        /*
        $columnas = array();
        for ($row = 1; $row <= 1; ++$row) {
        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
        $columnas[$worksheet->getCellByColumnAndRow($col, $row)->getValue()] = "";
        }
        }
         */
        $rs = array();
        $c = 0;
        for ($row = 2; $row <= $highestRow; ++$row) {
            $rs[] = [
                'setFechapedido' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                'setHorapedido' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                'setCodtipopedido' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                'setCodmedico' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                'setNommedico' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                'setDnuevo' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                'setAnulado' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                'setIdcita' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                'setEstado' => 0,
                'setCodzona' => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
                'setIdhc' => $worksheet->getCellByColumnAndRow(13, $row)->getValue(),
                'setcodpaciente' => $worksheet->getCellByColumnAndRow(14, $row)->getValue(),
                'setCodconsultorio' => $worksheet->getCellByColumnAndRow(15, $row)->getValue(),
                'setNomconsultorio' => $worksheet->getCellByColumnAndRow(16, $row)->getValue(),
                'setReqplaca' => $worksheet->getCellByColumnAndRow(17, $row)->getValue(),
                'setCodsede' => $worksheet->getCellByColumnAndRow(18, $row)->getValue(),
                'setObservaciones' => '-',
                'setCumplimiento' => '-',
                'setResponsable' => '0',
                'setSolicitud' => date('Y-m-d H:i:s'),
            ];
        }
        /*
        echo '<pre>';
        print_r($rs);
        echo '</pre>';
        return jsonResponse::create(array('success'=>0));
         */
        /* registro */
        //
        foreach ($rs as $key => $value) {
            /* verificar existentes */
            $check = $this->getDoctrine()->getRepository(TblPaciente::class)->findOneby(array('codpaciente' => $value['setcodpaciente']));
            if (!empty($check)) {
                /* */
                $idhc = $this->getDoctrine()->getRepository(TblHc::class)->findOneBy(array('codhistoria' => $value['setIdhc']));
                if (!empty($idhc)) {
                    /* insertar */
                    $s = new TblSolicitudes();
                    foreach ($value as $key => $v) {
                        if ($key == 'setcodpaciente') {
                            $s->setcodpaciente($check->getcodpaciente());
                        } elseif ($key == 'setIdhc') {
                            $s->setIdhc($idhc);
                        } else {
                            $s->$key(mb_convert_encoding($v, 'UTF-8'));
                        }
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($idhc);
                    $em->persist($s);
                    $em->flush();
                }
            }
        }
        unlink($path . '/' . $xml->getClientOriginalName());
        /* */
        return jsonResponse::create(array('success' => 1));
    }
    /**
     * @Route("/procesar_excel_ubicaciones", name="procesarexcelubicaciones")
     */
    public function procesarexcelubicaciones(Request $request)
    {
        /* subir excel */
        $xml = $request->files->get('xml');
        $path = str_replace(array('/src', '/Controller'), array('', ''), __DIR__) . '/public/xml';
        if (empty($xml)) {
            return jsonResponse::create(array('success' => 0));
        }
        $xml->move($path, $xml->getClientOriginalName());
        /* procesar */
        $inputFileName = $path . "/" . $xml->getClientOriginalName();
        //
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($inputFileName);
        $worksheet = $spreadsheet->getActiveSheet();
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow(); // e.g. 10
        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $rs = array();
        $c = 0;
        // inicial : 2
        for ($row = 2; $row <= $highestRow; ++$row) {
            $rs[] = [
                'setSede' => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
                'setCodhistoria' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                'setFolio' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                'setTipohc' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                'setCaja' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                'setIdusuario' => 1,
                'setFecharegistrohc' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                'setFechaultimasolictud' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                'setTotalfolios' => 0,
            ];
        }
        /*
        echo '<pre>';
        print_r($rs);
        echo '</pre>';
         */
        foreach ($rs as $key => $value) {
            /* verificar existentes */
            //$check = $this->getDoctrine()->getRepository(TblAlmacen::class)->findOneby(array('codigo'=>$value['setCodigo']));
            //if (!empty($check)) {
            /* */
            /* insertar */
            $s = new TblAlmacen();
            foreach ($value as $key => $v) {
                if ($key == 'setSede') {
                    if ($value['setSede'] == 'SAN ISIDRO') {
                        $s->setSede('1');
                    } else {
                        $s->setSede('2');
                    }
                } else {
                    $s->$key(mb_convert_encoding($v, 'UTF-8'));
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($s);
            $em->flush();
            //}
        }
        unlink($path . '/' . $xml->getClientOriginalName());
        return jsonResponse::create(array('success' => 1));
    }
}
