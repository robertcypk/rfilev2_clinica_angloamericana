<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use \App\Entity\TblHc;
use \App\Entity\TblHistorial;
use \App\Entity\TblSolicitudes;
use \App\Entity\TblUbicaciones;
use \App\Entity\TblUsuarios;
use \App\Controller\Printstickerpos;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/", name="api", methods="POST|GET")
     */
    public function api(Request $request): Response
    {
        $session = new Session();
        $email = $request->get('email');
        $password = $request->get('password');
        if (empty($email)) {
            return Response::create("0");
        }
        if (empty($password)) {
            return Response::create("0");
        }
        $user = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneBy(array('email' => $email));
        if (empty($user)) {
            return Response::create("0");
        }
        //file_put_contents(__DIR__."/../../public/email_login_api.txt", $email."-".$user->getRol());
        //file_put_contents(__DIR__."/../../public/api.txt", json_encode($user));
        if (($user->getRol() == 'ROLE_ADMIN') || ($user->getRol() == 'ROLE_ALMACEN') || ($user->getRol() == 'ROLE_MENSAJERO')) {
            if ($this->get('security.password_encoder')->isPasswordValid($user, $password)) {
                $session->set('nombre', $user->getNombre());
                $session->set('apellidos', $user->getApellidos());
                $session->set('idusuario', $user->getIdusuario());
                $session->set('rol', $user->getRol());
                return Response::create($session->get('idusuario') . ':' . $session->get('nombre') . ' ' . $session->get('apellidos') . ':' . $session->get('rol'));
            }
        }
        return Response::create('0');
    }

    /**
     * @Route("/asignarsolicitudes", name="asignarsolicitudes", methods="POST|GET")
     */
    public function asignarsolicitudes(Request $request) : Response
    {
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif (empty($temp['solicitudes'])) {
                $flag = false;
                $msg = "Campo SOLICITUDES es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $solicitudes = $temp['solicitudes'];
                foreach ($solicitudes as $solicitud) {
                    $data[] = $this->verificar_actualizar($solicitud['idsolicitud'], $idusuario);
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    private function verificar_actualizar($idsolicitud, $idusuario)
    {
       
        $em = $this->getDoctrine()->getEntityManager();
        $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud' => $idsolicitud));
        $flag = true;
        $json = [];
        if (empty($solicitud)) {
            $flag = false;
            $msg = "IDSOLICITUD inválido";
        } else {
            if ($solicitud->getEstado()!=0) {
                $flag = false;
                $msg = "No se puede cambiar a un estado no continuo";
            //$data = $solicitud->getEstado();
            } else {
                $solicitud->setEstado(1);
                $solicitud->setResponsable($idusuario);
                $em->flush();

                $tbl_solicitudes = $em->getConnection()->prepare("SELECT * FROM tbl_solicitudes 
                    WHERE idsolicitud='".$idsolicitud."'");
                    $tbl_solicitudes->execute();
                    $json = $tbl_solicitudes->fetch();                    
                    // $valorTwig =  $this->container->get('twig')->tipo_pedido();
                    $valorTwig = $this->ubi_archivo($json['idhc'], $json['codsede'], $json['folio'], 'caja');
                     $json +=  [ "caja" => $valorTwig ];                                                       
                    $msg =  'Solicitud "'.$idsolicitud.'" cambiada a estado 1';
            }
        }
        $data = [
            'error' => false,
            'flag'   => $flag,
            'msg'   =>  $msg,
            'data'   =>  $json
        ];
        return $data;
    }

    /**
     * @Route("/buscarsolicitudes", name="buscarsolicitudes", methods="POST|GET")
     */
    public function buscarsolicitudes(Request $request)
    {
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif (empty($temp['solicitudes'])) {
                $flag = false;
                $msg = "Campo SOLICITUDES es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $solicitudes = $temp['solicitudes'];
                foreach ($solicitudes as $solicitud) {
                    $data[] = $this->verificar_actualizar_encontrado($solicitud['idsolicitud'], $idusuario);
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    
    private function verificar_actualizar_encontrado($idsolicitud, $idusuario)
    {
        $em = $this->getDoctrine()->getEntityManager();       
        $solicitud = $em->createQuery("SELECT s FROM \App\Entity\TblSolicitudes s WHERE  s.idsolicitud=". $idsolicitud );
        $solrw = $solicitud->execute();
        $flag = true;
        $json = [];
       
        if (empty($solrw)) {
            $flag = false;
            $msg = "IDSOLICITUD inválido";
        } else {
            if ($solrw[0]->getEstado()==1) {
                if ($solrw[0]->getCodsede() == '02') {
                    $sede = '02';
                } else {
                    $sede = '01';
                }
               // $folio = intval(substr($solrw[0]->getIdhc(), -3));
                $folio = $solrw[0]->getFolio();
                $codigo = $solrw[0]->getIdhc();//intval(substr(, 2, 7));                
                $sql = "SELECT * FROM tbl_ubicaciones 
                WHERE codhistoria='".$codigo."' 
                AND sede='".$sede."' 
                AND folio=".$folio." LIMIT 1";
               // return $sql;
                $ubrw = $em->getConnection()->prepare($sql);
                $ubrw->execute();
                $ubrw = $ubrw->fetch();

                if (empty($ubrw)) {
                    $flag = false;
                    $msg =  'NO se encuentra la ubicación en archivo '.$codigo;
                } else {
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud' => $idsolicitud));
                    $solicitud->setEstado(2);
                    $solicitud->setResponsable($idusuario);
                    $em->flush();

                    $tbl_solicitudes = $em->getConnection()->prepare("SELECT * FROM tbl_solicitudes 
                    WHERE idsolicitud='".$idsolicitud."'");
                    $tbl_solicitudes->execute();
                    $json = $tbl_solicitudes->fetch();
                    $valorTwig = $this->ubi_archivo($json['idhc'], $json['codsede'], $json['folio'], 'caja');
                    $json +=  [ "caja" => $valorTwig ];      
                    $msg =  'Solicitud "'.$idsolicitud.'" cambiada a estado ENCONTRADO';
                }
            } else {
                $msg =  'Solicitud "'.$idsolicitud.'" no puede cambiadar a no continuo';
            }
        }
        $data = [
            'error' => false,
            'flag'   => $flag,
            'msg'   =>  $msg,
            'data' => $json
            //     ]
            // 'json' => $json[
            //     'solicitud' => $json,
            //     'ubicacion' =>  $ubrw
            //     ]
        ];
        return $data;
    }

    /**
     * @Route("/trayectohistorias", name="trayectohistorias", methods="POST|GET")
     */
    public function trayectohistorias(Request $request)
    {
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif (empty($temp['historias'])) {
                $flag = false;
                $msg = "Campo HISTORIAS es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $solicitudes = $temp['historias'];
                foreach ($solicitudes as $solicitud) {
                    if (!empty($solicitud['idhc'])) {
                        $data[] = $this->verificar_trayectoria_hc($solicitud['idhc'], $solicitud['folio'], $idusuario, 2, 3);
                    } else {
                        $data[] = "Codigo de HC es obligatorio ";
                    }
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }

    /**
     * @Route("/entregarhistorias", name="entregarhistorias", methods="POST|GET")
     */
    public function entregarhistorias(Request $request)
    {
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif (empty($temp['historias'])) {
                $flag = false;
                $msg = "Campo HISTORIAS es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $solicitudes = $temp['historias'];
                $responsable = $temp['responsable'];
                foreach ($solicitudes as $solicitud) {
                    if (!empty($solicitud['idhc'])) {
                        $data[] = $this->varificar_trayectoria_hc_responsable($solicitud['idhc'], $solicitud['folio'], $idusuario, $responsable, 3, 4);
                    } else {
                        $data[] = "err";
                    }
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }

    private function verificar_trayectoria_hc($idhc, $folio, $idusuario, $stct, $stcm)
    {
        $em = $this->getDoctrine()->getEntityManager();       
        // $solicitud = $em->createQuery("SELECT s FROM \App\Entity\TblSolicitudes s WHERE  s.idhc='" . $idhc . "' AND s.folio='".$folio."' AND s.estado='".$stct."'");
        $solicitud = $em->createQuery("SELECT s FROM \App\Entity\TblSolicitudes s WHERE  s.idhc='" . $idhc . "' AND s.folio='".$folio."' ");
        $solrw = $solicitud->execute();
      // print_r($solrw);
        $flag = true;
        $json = [];
        if (empty($solrw)) {
            $flag = false;
            $msg = 'HISTORIA "'.$idhc.'" inválida';
        } else {
            $em = $this->getDoctrine()->getEntityManager();   
            $solicitud = $em->createQuery("SELECT s FROM \App\Entity\TblSolicitudes s WHERE  s.idhc='" . $idhc . "' AND s.folio='".$folio."' AND s.estado='".$stct."' ");
            $soli = $solicitud->execute(); 
           // echo print_r($soli); 
           // $rstd = $soli->fetch();
            // if ($soli[0]->getEstado()==$stct) {
            if (!empty($soli)) {
                $sql = "SELECT 
                a.idarea,
                a.nombre,
                a.tipo,
                a.codzona,
                a.codsede,
                u.email
                FROM tbl_usuarios u , tbl_areas a
                WHERE
                 a.idarea=u.idarea
                AND u.idusuario = '".$idusuario."' ";
                $rw = $em->getConnection()->prepare($sql);
                $rw->execute();
                $rw = $rw->fetch();
                $msg = '';
                if (!empty($rw)) {
                    /*if ($solrw[0]->getCodsede() == '02') {
                        $sede = '02';
                    } else {
                        $sede = '01';
                    }*/                    
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud'=>$soli[0]->getIdsolicitud()));
                    $solicitud->setEstado($stcm);
                    $solicitud->setResponsableb($idusuario);
                    $em->flush(); 
                    
                    $idsolicitud = $solicitud->getIdsolicitud();
                   $tbl_solicitudes = $em->getConnection()->prepare("SELECT * FROM tbl_solicitudes 
                    WHERE idsolicitud='".$idsolicitud."'");
                    $tbl_solicitudes->execute();
                    $json = $tbl_solicitudes->fetch();                   
                   $valorTwig = $this->ubi_archivo($json['idhc'], $json['codsede'], $json['folio'], 'caja');
                  /* $json = $solicitud->findAll();
                   $valorTwig = $this->ubi_archivo($solicitud->getIdhc(), $solicitud->getCodsede(), $solicitud->getFolio(), 'caja');*/
                    $json +=  [ "caja" => $valorTwig ];  

                    $this->historial($soli[0]->getIdsolicitud(), $rw['email'], $idhc, $rw['idarea'], 'sw-trayectoria verificada', 3);
                    $msg =  'HISTORIA "'.$idhc.'" cambiada a estado EN TRAYECTO';
                }else {
                    $msg =  'El USUARIO: "' . $idusuario. '" no existe ';

                }
            } else {
                $msg =  'HISTORIA "'.$idhc.'" no puede cambiadar a no continuo';
            }
        }
        $data = [
            'error' => false,
            'flag'   => $flag,
            'msg'   =>  $msg,
            'data' => $json
        ];
        return $data;
    }

    private function varificar_trayectoria_hc_responsable($idhc, $folio, $idusuario, $responsable, $stct, $stcm)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $solicitud = $em->createQuery("SELECT s FROM \App\Entity\TblSolicitudes s WHERE  s.idhc='" . $idhc . "' AND s.folio='".$folio."' AND s.estado='".$stct."' ");
        $solrw = $solicitud->execute();
        $flag = true;
        $json = [];
        if (empty($solrw)) {
            $flag = false;
            $msg = "CODIGO DE HISTORIA inválido";
        } else {
            if ($solrw[0]->getEstado()==$stct) {
                // historial emisor
                $rw = $this->gusuario($idusuario);
                if (!empty($rw)) {
                    /* if ($solrw[0]->getCodsede() != '01') {
                         $sede = '02';
                     } else {
                         $sede = '01';
                     }*/
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud'=>$solrw[0]->getIdsolicitud()));
                    $solicitud->setEstado($stcm);
                    $solicitud->setResponsableb($idusuario);
                    $em->flush();

                    $idsolicitud = $solicitud->getIdsolicitud();
                    $tbl_solicitudes = $em->getConnection()->prepare("SELECT * FROM tbl_solicitudes 
                    WHERE idsolicitud='".$idsolicitud."'");
                    $tbl_solicitudes->execute();
                    $json = $tbl_solicitudes->fetch();                   
                   /* $valorTwig = $this->ubi_archivo($json['idhc'], $json['codsede'], $json['folio'], 'caja');
                    $json +=  [ "caja" => $valorTwig ];  */


                    $this->historial($solrw[0]->getIdsolicitud(), $rw['email'], $idhc, $rw['idarea'], 'sw- Trayectoria Verificada', 4);
                    // historial receptor
                    $rw_responsable = $this->gusuario($responsable);
                    if (!empty($rw_responsable)) {
                        $this->historial($solrw[0]->getIdsolicitud(), $rw_responsable['email'], $idhc, $rw_responsable['idarea'], 'sw- EN consultorio', 4);
                    }
                    $msg =  'HISTORIA "'.$idhc.'" cambiada a estado EN CONSULTORIO';
                }
            } else {
                $msg =  'HISTORIA "'.$idhc.'" no puede cambiadar a no continuo';
            }
        }
        $data = [
            'error' => false,
            'flag'   => $flag,
            'msg'   =>  $msg,
            'data' => $json
        ];
        return $data;
    }

    private function gusuario($idusuario)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sql = "SELECT 
                a.idarea,
                a.nombre,
                a.tipo,
                a.codzona,
                a.codsede,
                u.email
                FROM tbl_usuarios u , tbl_areas a
                WHERE
                 a.idarea=u.idarea
                AND u.idusuario = '".$idusuario."' ";
        $rw = $em->getConnection()->prepare($sql);
        $rw->execute();
        $rw = $rw->fetch();
        return $rw;
    }

    /**
     * @Route("/verificarresponsable", name="verificarresponsable", methods="POST|GET")
     */
    public function verificarresponsable(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $u = $em->createQuery("SELECT u FROM \App\Entity\TblUsuarios u WHERE  u.idusuario='" . $idusuario . "' AND u.status='activo'");
                $rs= $u->getScalarResult();
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $rs
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }

    /**
     * @Route("/recogerhistorias", name="recogerhistorias", methods="POST|GET")
     */
    /***
     * 10.	BUSCAR SOLICUTUD (HC, FOLIO, ), Filtrado por Solicitud que este en estado = 4
     * 11.	Actualizar columna ESTADO = 5
     * 12.	BUSCAR solicitudes de mismo dia, con hora de entrega siguiente
     * 13.	SI encuentra resultado de solicitud siguiente
     *      a.	Actualizar solicitud actual a estado = 7
     *      b.	Agregar registro en TBL_historial
     *      c.	Actualizar nueva solicitud a estado = 3
     *      d.	Agregar registro en historial de nueva solicitud
     * 14.	Agregar registro en TBL_historial
     */
    public function recogerhistorias(Request $request)
    {
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif (empty($temp['historias'])) {
                $flag = false;
                $msg = "Campo HISTORIAS es obligatorio";
            } elseif (empty($temp['responsable'])) {
                    $flag = false;
                    $msg = "Campo RESPONSABLE es obligatorio";
            } else {
               $idusuario = $temp['idusuario'];
               $msg = $idusuario;
                $solicitudes = $temp['historias'];
                foreach ($solicitudes as $solicitud) {
                    if (!empty($solicitud['idhc'])) {
                        $data[] = $this->recoger_historias($solicitud['idhc'], $solicitud['folio'], $idusuario, 4, 5);
                    }
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
           // return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    private function recoger_historias($idhc, $folio, $idusuario, $stct, $stcm)
    {
        $em = $this->getDoctrine()->getEntityManager();       
        $solicitud = $em->createQuery("SELECT s FROM \App\Entity\TblSolicitudes s WHERE  s.idhc='" . $idhc . "' AND s.folio='".$folio."' AND s.estado='".$stct."' ");

        $solrw = $solicitud->execute();
        // $hcrec = $solrw->fetchAll();
       // echo print_r ($solrw);
        $flag = true;
        $json = [];
        if (empty($solrw)) {
            $flag = false;
            $msg = "CODIGO DE HISTORIA inválido";
        } else {
            if ($solrw[0]->getEstado()==$stct) {
                // $solicitud_rs = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idusuario' =>$solrw[0]->getIdsolicitud()));
                $solicitud_rs = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud' =>$solrw[0]->getIdsolicitud()));
                $solicitud_rs->setEstado($stcm);
                $solicitud_rs->setResponsableb($idusuario);
                $em->flush();
                $msg =  'HISTORIA "'.$idhc.'" cambiada a estado EN RETORNO';
            } else {
                $msg =  'HISTORIA "'.$idhc.'" no puede cambiar un estado NO continuo';
            }
            $sql = "SELECT 
                a.idarea,
                a.nombre,
                a.tipo,
                a.codzona,
                a.codsede,
                u.email
                FROM tbl_usuarios u , tbl_areas a
                WHERE
                 a.idarea=u.idarea
                AND u.idusuario = '".$idusuario."' ";
            $rw = $em->getConnection()->prepare($sql);
            $rw->execute();
            $rw = $rw->fetch();
            $this->buscar_solicitudes_fecha_actual($solrw[0]->getIdsolicitud(), $rw['email'], $idusuario, $idhc, $rw['idarea'], 7);
            $this->buscar_solicitudes_en_cola($solrw[0]->getIdsolicitud(), $rw['email'], $idusuario, $idhc, $rw['idarea']);
        }
        $data = [
            'error' => false,
            'flag'   => $flag,
            'msg'   =>  $msg
        ];
        return $data;
    }
    private function buscar_solicitudes_fecha_actual($idsolicitud, $email, $idusuario, $idhc, $idarea, $stcm)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $sqlc = "SELECT 
        *
        FROM
        tbl_solicitudes
        WHERE
        idhc='".$idhc."'
        AND day(fechapedido)=day(NOW())
        AND month(fechapedido)=month(NOW())
        AND year(fechapedido)=year(NOW()) 
        ORDER BY fechapedido DESC limit 1,1";
        $rw = $em->getConnection()->prepare($sqlc);
        $rw->execute();
        $rw = $rw->fetch();
        if (!empty($rw)) {
            $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array(
            'idsolicitud'=>$rw['idsolicitud']
        ));
            if (!empty($solicitud)) {
                $solicitud->setEstado($stcm);
                $solicitud->setResponsableb($idusuario);
                $em->flush();
                $this->historial($idsolicitud, $email, $idhc, $idarea, 'Solicitud HC en ARCHIVO', $stcm);
            }
        }
    }
    private function buscar_solicitudes_en_cola($idsolicitud, $email, $idusuario, $idhc, $idarea)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $sql = "SELECT * FROM 
        tbl_solicitudes 
        WHERE 
        idhc='".$idhc."'
        AND estado='0'
        AND day(fechapedido) = day(NOW())
        AND month(fechapedido)=month(NOW())
        AND year(fechapedido)=year(NOW())
        ORDER BY fechapedido DESC
        LIMIT 0,1";
        $rw = $em->getConnection()->prepare($sql);
        $rw->execute();
        $rw = $rw->fetch();

        if (!empty($rw)) {
            $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array(
            'idsolicitud'=>$rw['idsolicitud']
        ));
            if (!empty($solicitud)) {
                $solicitud->setEstado(3);
                $solicitud->setResponsableb($idusuario);
                $em->flush();
                $this->historial($idsolicitud, $email, $idhc, $idarea, 'Solicitud HC - En Trayecto', 3);
            }
        }
    }

    /**
     * @Route("/close", name="apiclose", methods="POST|GET")
     */
    public function apiclose(Request $request)
    {
        return Response::create('denegado');
    }
    private function tblhc($id, $col)
    {
        if (empty($col)) {
            return "";
        }
        $hc = $this->getDoctrine()->getRepository(TblHc::class)->findOneBy(array('codpaciente' => $id));
        return empty($hc) ? "" : $hc->$col();
    }
    /**
     * @Route("/validacion", name="validacion", methods="POST")
     */
    public function validacion(Request $request): Response
    {
        try {
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif (empty($temp['solicitudes'])) {
                $flag = false;
                $msg = "Campo SOLICITUDES es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $solicitudes = $temp['solicitudes'];
                foreach ($solicitudes as $solicitud) {
                    $data[] = $this->valida($solicitud, $idusuario);
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    
    private function valida($request, $idusuario)
    {
        try {
            //return $request;
            $em = $this->getDoctrine()->getEntityManager();
            $temp = $request;
            $temp['idusuario'] = $idusuario;

            $flag = true;
            $msg = "";
            if (empty($temp['estado'])) {
                $flag = false;
                $msg = "Campo ESTADO es obligatorio";
            } else {
                $estado = $temp['estado'];
            }
            if ($flag && ($estado > 7 || $estado < 1)) {
                //estados invalidos
                $flag = false;
                $msg = "estados invalidos";
            } elseif ($flag && $estado == 1) {
                if (empty($temp['idsolicitud'])) {
                    $flag = false;
                    $msg = "Campo IDSOLICITUD es obligatorio";
                } else {
                    $idsolicitud = $temp['idsolicitud'];
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud' => $idsolicitud));
                    if (empty($solicitud)) {
                        $flag = false;
                        $msg = "IDSOLICITUD inválido";
                    } else {
                        if ($solicitud->getEstado()!=0) {
                            $flag = false;
                            $msg = "No se puede cambiar a un estado no continuo";
                        //$data = $solicitud->getEstado();
                        } else {
                            $solicitud->setEstado(1);
                            $em->flush();
                            $msg =  'Solicitud "'.$idsolicitud.'" cambiada a estado 1';
                        }
                    }
                }
            } else {
                if (empty($temp['codhistoria'])) {
                    $flag = false;
                    $msg = "Campo CODHISTORIA es obligatorio";
                } else {
                    $codhistoria = $temp['codhistoria'];
                }
                if ($flag && ($estado==2 || $estado==3 || $estado == 6)) {
                    //los demas estados(2-3-6)
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idhc' => $codhistoria, 'estado' => intval($estado) -1 ));
                    if (empty($solicitud)) {
                        $flag = false;
                        $msg = "No existen solicitudes en el estado ".(intval($estado) -1)." para el codigo de historia ".$codhistoria;
                    } else {
                        $solicitud->setEstado($estado);
                        $em->flush();
    
                        $msg =  'Solicitud "'.$solicitud->getIdSolicitud().'" cambiada a estado '.$estado;
                    }
                } else {
                    if ($flag&& ($estado== 4 || $estado == 5)) {
                        if ($flag&&empty($temp['responsable'])) {
                            $flag = false;
                            $msg = "campo RESPONSABLE es obligatorio";
                        } else {
                            $responsable = $temp['responsable'];
                            //buscamos
                            $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idhc' => $codhistoria, 'estado' => intval($estado) -1 ));
                            if (empty($solicitud)) {
                                $flag = false;
                                $msg = "No existen solicitudes en el estado ".(intval($estado) -1)." para el codigo de historia ".$codhistoria;
                            } else {
                                $solicitud->setEstado($estado);
                                $solicitud->setResponsable($responsable);
                                $em->flush();
                                $msg =  'Solicitud "'.$solicitud->getIdSolicitud().'" cambiada a estado '.$estado;
                            }
                        }
                    } elseif ($flag && $estado == 7) {
                        if (empty($temp['caja'])) {
                            $flag = false;
                            $msg = "campo CAJA es obligatorio";
                        } else {
                            $caja = $temp['caja'];
                        }
                        if ($flag&&empty($temp['sede'])) {
                            $flag = false;
                            $msg = "Campo SEDE es obligatorio";
                        } else {
                            $sede = $temp['sede'];
                        }
                        if ($flag) {
                            //obtenemos el folio
                            $folio = intval(substr($codhistoria, -3));
                            $codigo = intval(substr($codhistoria, 2, 7));
                            //verificamos que exista una solicitud en el estado 6
                            $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idhc' => $codigo, 'estado' => intval($estado) -1 ));
                            if (empty($solicitud)) {
                                $flag = false;
                                $msg = "No existen solicitudes en el estado ".(intval($estado) -1)." para el codigo de historia ".$codigo;
                            } else {
                                //verificamos la caja
                                $ubicacion = $em->getRepository(TblUbicaciones::class)->findOneBy(array('caja' => $caja, 'sede' => $sede, 'folio' => $folio, 'codhistoria' => $codigo ));
                                if (empty($ubicacion)) {
                                    //no es valido
                                    $flag = false;
                                    $msg = "Caja de ubicación incorrecta";
                                } else {
                                    //es valido
                                    $solicitud->setEstado($estado);
                                    $em->flush();
                                    $msg =  'Solicitud "'.$solicitud->getIdSolicitud().'" cambiada a estado '.$estado;
                                }
                            }
                        }
                    }
                }
            }
            if ($flag) {//no hubo error
                $historial = new TblHistorial();
                $historial->setIdsolicitud($solicitud->getIdSolicitud());
                $historial->setIdhc($solicitud->getIdHc());
                $historial->setUbicacion($solicitud->getCodZona());//codigo de zona
                $historial->setEstatus($estado);
                $historial->setUsuario($idusuario);
                $historial->setFecha(date('Y-m-d H.i:s'));
                $historial->setComentarios('');
                $em->persist($historial);
                $em->flush();
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg
            ];


            return $data;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    /**
     * @Route("/validacion_test", name="validacion_test", methods="POST")
     */
    public function validacion_test(Request $request): Response
    {
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $temp = $request->request->all();

            $flag = true;
            $msg = "";
            if (empty($temp['estado'])) {
                $flag = false;
                $msg = "Campo ESTADO es obligatorio";
            } else {
                $estado = $temp['estado'];
            }
            if ($flag && ($estado > 7 || $estado < 1)) {
                //estados invalidos
                $flag = false;
                $msg = "estados invalidos";
            } elseif ($flag && $estado == 1) {
                if (empty($temp['idsolicitud'])) {
                    $flag = false;
                    $msg = "Campo IDSOLICITUD es obligatorio";
                } else {
                    $idsolicitud = $temp['idsolicitud'];
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud' => $idsolicitud));
                    if (empty($solicitud)) {
                        $flag = false;
                        $msg = "IDSOLICITUD inválido";
                    } else {
                        if ($solicitud->getEstado()!=0) {
                            $flag = false;
                            $msg = "No se puede cambiar a un estado no continuo";
                        //$data = $solicitud->getEstado();
                        } else {
                            $solicitud->setEstado(1);
                            $em->flush();
                            $msg =  'Solicitud "'.$idsolicitud.'" cambiada a estado 1';
                        }
                    }
                }
            } else {
                if (empty($temp['codhistoria'])) {
                    $flag = false;
                    $msg = "Campo CODHISTORIA es obligatorio";
                } else {
                    $codhistoria = $temp['codhistoria'];
                }
                if ($flag && ($estado==2 || $estado==5 || $estado == 6)) {
                    //los demas estados(2-5-6)
                    $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idhc' => $codhistoria, 'estado' => intval($estado) -1 ));
                    if (empty($solicitud)) {
                        $flag = false;
                        $msg = "No existen solicitudes en el estado ".(intval($estado) -1)." para el codigo de historia ".$codhistoria;
                    } else {
                        $solicitud->setEstado($estado);
                        $em->flush();
    
                        $msg =  'Solicitud "'.$solicitud->getIdSolicitud().'" cambiada a estado '.$estado;
                    }
                } else {
                    if ($flag&&empty($temp['idusuario'])) {
                        $flag = false;
                        $msg = "campo IDUSUARIO es obligatorio";
                    } else {
                        $responsable = $temp['idusuario'];
                        if ($flag&& ($estado== 3 || $estado == 4)) {
                            //buscamos
                            $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idhc' => $codhistoria, 'estado' => intval($estado) -1 ));
                            if (empty($solicitud)) {
                                $flag = false;
                                $msg = "No existen solicitudes en el estado ".(intval($estado) -1)." para el codigo de historia ".$codhistoria;
                            } else {
                                $solicitud->setEstado($estado);
                                $solicitud->setResponsable($responsable);
                                $em->flush();
                                $msg =  'Solicitud "'.$solicitud->getIdSolicitud().'" cambiada a estado '.$estado;
                            }
                        } elseif ($flag && $estado == 7) {
                            if (empty($temp['caja'])) {
                                $flag = false;
                                $msg = "campo CAJA es obligatorio";
                            } else {
                                $caja = $temp['caja'];
                            }
                            if ($flag&&empty($temp['sede'])) {
                                $flag = false;
                                $msg = "Campo SEDE es obligatorio";
                            } else {
                                $sede = $temp['sede'];
                            }
                            if ($flag) {
                                //obtenemos el folio
                                $folio = intval(substr($codhistoria, -3));
                                $codigo = intval(substr($codhistoria, 2, 7));
                                //verificamos que exista una solicitud en el estado 6
                                $solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idhc' => $codigo, 'estado' => intval($estado) -1 ));
                                if (empty($solicitud)) {
                                    $flag = false;
                                    $msg = "No existen solicitudes en el estado ".(intval($estado) -1)." para el codigo de historia ".$codigo;
                                } else {
                                    //verificamos la caja
                                    $ubicacion = $em->getRepository(TblUbicaciones::class)->findOneBy(array('caja' => $caja, 'sede' => $sede, 'folio' => $folio, 'codhistoria' => $codigo ));
                                    if (empty($ubicacion)) {
                                        //no es valido
                                        $flag = false;
                                        $msg = "Caja de ubicación incorrecta";
                                    } else {
                                        //es valido
                                        $solicitud->setEstado($estado);
                                        $em->flush();
                                        $msg =  'Solicitud "'.$solicitud->getIdSolicitud().'" cambiada a estado '.$estado;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($flag) {//no hubo error
                $historial = new Tblhistorial();
                $historial->setIdSolicitud($solicitud->getIdSolicitud());
                $historial->setIdHc($solicitud->getIdHc());
                $historial->setUbicacion($solicitud->getZona());//codigo de zona
                $historial->setEstatus($estado);
                $historial->setUsuario($idusuario);
                $historial->setFecha(date('Y-m-d'));
                $em->persist($historial);
                $em->flush();
            } else {
                $historial = [];
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'historial'  =>  $historial
            ];

            $response = new Response(
               json_encode($data, true),
               Response::HTTP_OK,
               ['Content-type' => 'application/json']
            );

            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    /**
     * @Route("/validacion2", name="validacion2", methods="POST|GET")
     */
    public function validacion2(Request $request): Response
    {
        $responsable = $request->get('idusuario');
        $idsolicitud = $request->get('idsolicitud');
        $comentarios = empty($request->get('comentarios')) ? '-' : $request->get('comentarios');
        $estado = $request->get('estado');
        $recepcion = $request->get('recepcion');

        if (empty($responsable) and empty($idsolicitud)) {
            return Response::create('Parametros incorrectos');
        }
        if (empty($estado)) {
            return Response::create('Seleccione un estado de solicitud');
        }

        $idsolicitud = trim(preg_replace('/[\t|\s{"\\n}]/', '', $idsolicitud));
        $idsolicitud = trim(preg_replace('/(,")/', '', $idsolicitud));
        $idsolicitud = trim(preg_replace('/(")/', '', $idsolicitud));
        $codeValid = substr($recepcion, 4);
        if (empty($codeValid)) {
            $recepcion = 18;
        } else {
            $recepcion = $codeValid;
        }

        try {
            $idsolicitud = explode(',', $idsolicitud);
            $idsolicitud = array_unique($idsolicitud);
            $idsolicitud = array_filter($idsolicitud, 'strlen');
            //file_put_contents(__DIR__."/../../public/scanner_reformat.txt", json_encode($idsolicitud));
            $msg = "";
            $c = count($idsolicitud);
            $estado = preg_replace('/.(-)*.([aA-zZ])+\w/', '', $estado);
            foreach ($idsolicitud as $key => $value) {
                // $value = "HC123456799-485";
                // verificar si es barcode Actual
                $codeValid = substr($value, 0, 2);
                if ($codeValid == "HC") {
                    $id = substr($value, 2, 7);
                    $tipoCod = "RF1";
                } else {
                    $value_ex = explode('-', $value);
                    $id = $value_ex[1];
                    $tipoCod = $value_ex[0];
                }

                //file_put_contents(__DIR__."/../../public/scanner_reformat-rf123.txt", $tipoCod."-".$id, FILE_APPEND);
                if ($tipoCod == 'RF2') {
                    $msg .= $this->actualizarEstadosSolTicket($responsable, (int) $id, $comentarios, $estado, $recepcion);
                } elseif ($tipoCod == 'RF1' or $tipoCod == 'RF3') {
                    $msg .= $this->actualizarEstadosSolArch($responsable, $id, $comentarios, $estado, $recepcion);
                } else {
                    $msg .= "Codigo Invalido" . $codeValid;
                }
                if (($key + 1) <= $c) {
                    $msg .= "<br>";
                }
            }
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
        return Response::create($msg);
    }
    public function actualizarEstadosSolArch($responsable, $codhistoria, $comentarios, $estado, $recepcion)
    {
        $idsolicitud  = "--";
        $codigopaciente= "--";
        $codigohc= $codhistoria;
        $ubicacion = "--";
        try {
            $xres = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => $recepcion));
            if (empty($xres)) {
                $fichero = __DIR__ . "/../../public/registroHC-NoRegistrado.txt";
                $data = "Fecha:" . date("Y-m-d H:i:s"). ",El usuario no existe" . ",idusuario:".$responsable.",HC:".$codhistoria.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
                //file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);
                //file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',El Usuario no existe,'. date("Y-m-d H:i:s"), FILE_APPEND);
                return 'El usuario no existe: ' . $recepcion;
            }
            $em = $this->getDoctrine()->getManager();
            $q_idsol = $em->getConnection()->prepare("SELECT fechapedido, horapedido,idsolicitud,estado,idhc
                                                      FROM tbl_solicitudes
                                                      WHERE idhc='" . $codhistoria . "' AND estado in ('0','1','2','3','4','5','6') order by fechapedido,horapedido asc limit 1");
            $q_idsol->execute();
            $rs_idsol = $q_idsol->fetch();

            if (!empty($rs_idsol)) {
                if ($rs_idsol['estado'] == $estado) {
                    $fichero = __DIR__ . "/../../public/registroHCValidas.txt";
                    $data = "Fecha:" . date("Y-m-d H:i:s"). ",Se actualizo la solicitud a" . ",idusuario:".$responsable.",HC:".$codhistoria.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
                   // file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);
                    return 'Se actualizo la solicitud a ' . $this->stpedido($estado);

                   // file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',Solicitud ya actualizada,'. date("Y-m-d H:i:s"), FILE_APPEND);
                }

                $up = $em->getConnection()->prepare("UPDATE tbl_solicitudes
                                                SET estado=" . (int) $estado . ", responsableb=" . (int) $recepcion . "
                                                WHERE idsolicitud='" . $rs_idsol['idsolicitud'] . "'");
                $up->execute();
                if ($estado == 5) {
                    $recepcion_historial_recojo = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $recepcion));
                    if (!empty($recepcion_historial_recojo)) {
                        $this->historial(
                            $rs_idsol['idsolicitud'],
                            $recepcion_historial_recojo->getEmail(),
                            $rs_idsol['idhc'],
                            $recepcion_historial_recojo->getIdarea(),
                            'Entregó',
                            $estado
                        );
                    }
                    $recepcion_historial_entrega = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $responsable));
                    if (!empty($recepcion_historial_entrega)) {
                        $this->historial(
                            $rs_idsol['idsolicitud'],
                            $recepcion_historial_entrega->getEmail(),
                            $rs_idsol['idhc'],
                            $recepcion_historial_entrega->getIdarea(),
                            'Recibió',
                            $estado
                        );
                    }
                } else {
                    $recepcion_historial_recojo = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $recepcion));
                    if (!empty($recepcion_historial_recojo)) {
                        $this->historial(
                            $rs_idsol['idsolicitud'],
                            $recepcion_historial_recojo->getEmail(),
                            $rs_idsol['idhc'],
                            $recepcion_historial_recojo->getIdarea(),
                            'Recibió',
                            $estado
                        );
                    }
                    $recepcion_historial_entrega = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $responsable));
                    if (!empty($recepcion_historial_entrega)) {
                        $this->historial(
                            $rs_idsol['idsolicitud'],
                            $recepcion_historial_entrega->getEmail(),
                            $rs_idsol['idhc'],
                            $recepcion_historial_entrega->getIdarea(),
                            'Entregó',
                            $estado
                        );
                    }
                }
                $fichero = __DIR__ . "/../../public/registroHCValidas.txt";
                $data = "Fecha:" . date("Y-m-d H:i:s"). ",Se actualizo la solicitud a" . ",idusuario:".$responsable.",HC:".$codhistoria.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
               // file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);
                //file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',Solicitud actualizada,'. date("Y-m-d H:i:s"), FILE_APPEND);
                return 'Sol-CodHistoria: ' . $codhistoria . ' en ' . $data . $this->stpedido($estado);
            } else {
                $fichero = __DIR__ . "/../../public/registroHC-NoRegistrado.txt";
                $data = "Fecha:" . date("Y-m-d H:i:s"). ",Mensaje:Solicitud no existe " . ",idusuario:".$responsable.",HC:".$codhistoria.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
               // file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);
               // file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',No existe solicitud,'. date("Y-m-d H:i:s"), FILE_APPEND);
                return 'Solicitud no existe : ' . $codhistoria;
            }
        } catch (\Exception $e) {
            $fichero = __DIR__ . "/../../public/actualizarEstadosSolArch.txt";
           // file_put_contents($fichero, $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
            $fichero = __DIR__ . "/../../public/registroHC-NoRegistrado.txt";
            $data = "Fecha:" . date("Y-m-d H:i:s"). ",Mensaje:Código de HC no registrado" . ",idusuario:".$responsable.",HC:".$codhistoria.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
            //file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);

            //file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',Código de HC no registrado,'. date("Y-m-d H:i:s"), FILE_APPEND);


            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    public function actualizarEstadosSolTicket($responsable, $idsolicitud, $comentarios, $estado, $recepcion)
    {
        $ubicacion= "--";
        $codigohc= "--";
        $codigopaciente = "--";
        try {
            $xres = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => $responsable));
            if (empty($xres)) {
                $fichero = __DIR__ . "/../../public/registroHC-Ticket-NoRegistrado.txt";
                $data = "Fecha:" . date("Y-m-d H:i:s"). ",Mensaje:El usuario responsable no existe " . ",idusuario:".$responsable.",SOL:".$idsolicitud.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
               // file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);

               // file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',No Existe usuario Responsable,'. date("Y-m-d H:i:s"), FILE_APPEND);
                return 'El usuario no existe : ' . $responsable;
            }
            $check = $this->getDoctrine()->getRepository(TblSolicitudes::class)->findOneby(array('idsolicitud' => $idsolicitud));
            if (!empty($check)) {
                if ($check->getEstado() == $estado) {
                    return 'El estado actual de esta solicitud esta en ' . $this->stpedido($estado);
                }
                $chc = $this->getDoctrine()->getRepository(TblHc::class)->findOneby(array('codpaciente' => $check->getCodpaciente()));
                if (empty($chc)) {
                    $fichero = __DIR__ . "/../../public/registroHC-Ticket-NoRegistrado.txt";
                    $data = "Fecha:" . date("Y-m-d H:i:s"). ",Mensaje:No exite HC de paciente" . ",idusuario:".$responsable.",SOL:".$idsolicitud.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
                    //file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);

                    //file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',No Existe HC,'. date("Y-m-d H:i:s"), FILE_APPEND);

                    return 'No exite HC de paciente Nro.: ' . $check->getCodpaciente();
                }
                $em = $this->getDoctrine()->getManager();
                if ($estado != 4) {
                    $up = $em->getConnection()->prepare("UPDATE tbl_solicitudes
                                                SET estado=" . (int) $estado . ", responsable=" . $xres->getIdusuario() . "
                                                WHERE idsolicitud=" . $idsolicitud . " and estado not in(8)");
                    $up->execute();

                    $this->historial($idsolicitud, $xres->getEmail(), $chc->getCodhistoria(), $xres->getIdarea(), $comentarios, $estado);
                } else {
                    $up = $em->getConnection()->prepare("UPDATE tbl_solicitudes
                                                SET estado=" . (int) $estado . ", responsableb=" . (int) $recepcion . "
                                                WHERE idsolicitud=" . $idsolicitud . " and estado not in(8)");
                    $up->execute();

                    if ($estado == 5) {
                        $recepcion_historial_recojo = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $recepcion));
                        if (!empty($recepcion_historial_recojo)) {
                            $this->historial($idsolicitud, $recepcion_historial_recojo->getEmail(), $chc->getCodhistoria(), $recepcion_historial_recojo->getIdarea(), 'Entregó', $estado);
                        }
                        $recepcion_historial_entrega = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $responsable));
                        if (!empty($recepcion_historial_entrega)) {
                            $this->historial($idsolicitud, $recepcion_historial_entrega->getEmail(), $chc->getCodhistoria(), $recepcion_historial_entrega->getIdarea(), 'Recibió', $estado);
                        }
                    } else {
                        $recepcion_historial_recojo = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $recepcion));
                        if (!empty($recepcion_historial_recojo)) {
                            $this->historial($idsolicitud, $recepcion_historial_recojo->getEmail(), $chc->getCodhistoria(), $recepcion_historial_recojo->getIdarea(), 'Recibió', $estado);
                        }
                        $recepcion_historial_entrega = $this->getDoctrine()->getRepository(TblUsuarios::class)->findOneby(array('idusuario' => (int) $responsable));
                        if (!empty($recepcion_historial_entrega)) {
                            $this->historial($idsolicitud, $recepcion_historial_entrega->getEmail(), $chc->getCodhistoria(), $recepcion_historial_entrega->getIdarea(), 'Entregó', $estado);
                        }
                    }
                }

               // file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',Solicitud actualizada,'. date("Y-m-d H:i:s"), FILE_APPEND);

                return 'Solicitud ' . $idsolicitud . ' en ' . $this->stpedido($estado);
            } else {
                $fichero = __DIR__ . "/../../public/registroHC-Ticket-NoRegistrado.txt";
                $data = "Fecha:" . date("Y-m-d H:i:s"). ",Mensaje:Solicitud no existe" . ",idusuario:".$responsable.",SOL:".$idsolicitud.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
              //  file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);

              //  file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',Solicitud no existe,'. date("Y-m-d H:i:s"), FILE_APPEND);

                return 'Solicitud no existe : ' . $idsolicitud;
            }
        } catch (\Exception $e) {
            $fichero = __DIR__ . "/../../public/actualizarEstadosSolTicket.txt";
           // file_put_contents($fichero, $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());

            $fichero = __DIR__ . "/../../public/registroHC-Ticket-NoRegistrado.txt";
            $data = "Fecha:" . date("Y-m-d H:i:s"). ",Mensaje:Solicitud no existe" . ",idusuario:".$responsable.",SOL:".$idsolicitud.",Estado:".$estado.",idusuarioreceptor:".$recepcion;
           // file_put_contents($fichero, PHP_EOL . $data, FILE_APPEND);

           // file_put_contents(__DIR__ . "/../../public/PDA_scanner.csv", PHP_EOL . $idsolicitud.','. $responsable .','.$codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .',Solicitud no existe,'. date("Y-m-d H:i:s"), FILE_APPEND);

            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }
    public function stpedido($st)
    {
        switch ($st) {
            case 1:
                return 'En Busqueda';
                break;
            case 2:
                return 'Encontrado';
                break;
            case 3:
                return 'En Trayecto';
                break;
            case 4:
                return 'Entregado';
                break;
            case 5:
                return 'En Retorno';
                break;
            case 6:
                return 'En Acopio';
                break;
            case 7:
                return 'Archivado';
                break;
            case 8:
                return 'Anulado';
                break;
            default:
                return 'Pendiente';
                break;
        }
    }
    private function historial($idsolicitud, $responsable, $codigohc, $ubicacion, $comentarios, $estado)
    {
        $historial = new TblHistorial();
        $historial->setIdsolicitud($idsolicitud);
        $historial->setIdhc($codigohc);
        $historial->setUbicacion($ubicacion);
        $historial->setEstatus((int) $estado);
        $historial->setUsuario($responsable);
        //$historial->setFecha(new \DateTime("now"));
        $historial->setFecha(date('Y-m-d H.i:s'));
        $historial->setComentarios($comentarios);
        $em = $this->getDoctrine()->getManager();
        $em->persist($historial);
        $em->flush();
        //file_put_contents(__DIR__ . "/../../public/scanner-TblHistorial.txt", $idsolicitud . "-" . $responsable . "-" . $estado . "-" . $comentarios . "\n", FILE_APPEND);
    }



    /*  private function reportePDA ($idsolicitud, $responsable, $codigohc,$codigopaciente,$ubicacion,$estado,$comentarios ){
          $csv = $responsable .','. $idsolicitud.','. $responsable .','. $codigohc.','.$estado.','.$codigopaciente.','. $ubicacion .','. $estado .','. $comentarios.','. date("Y-m-d H:i:s");
          $csv_handler = fopen (__DIR__ .'/../../public/reportes/PDA_scanner.csv','w');
          fwrite ($csv_handler,$csv);
          fclose ($csv_handler);*/

    //AGE


    /**
     * @Route("/consultarcaja", name="consultarcaja", methods="POST")
     */
    public function consultar_caja(Request $request):Response
    {
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['codcaja'])) {
                $flag = false;
                $msg = "Campo CODCAJA es obligatorio";
            } else {
                $codcaja = $temp['codcaja'];
                //$ubicaciones = $em->getRepositore(TblUbicaciones::class)->findBy(array('caja'=> $codcaja));
                $sql = "SELECT
                u.idubicaciones,
                u.fecharegistro,
                u.sede,
                u.codhistoria,
                u.folio,
                u.caja,
                u.estado,
                u.responsable,
                coalesce(s.idsolicitud,'') as SOL_idsolicitud,
                coalesce(s.estado,'') as SOL_estado,
                coalesce(s.codtipopedido,'') as SOL_codtitpopedido,
                coalesce(s.dnuevo,'') as SOL_dnuevo,
                coalesce(s.codzona,'') as SOL_codzona,
                coalesce(s.codsede,'') as SOL_codsede,
                coalesce(s.responsable,'') as SOL_responsable,
                coalesce(s.responsablec,'') as SOL_responsablec
                FROM tbl_ubicaciones u
                LEFT JOIN tbl_solicitudes s 
                    ON s.idhc  = u.codhistoria 
                    AND s.folio = u.folio 
                WHERE u.caja = '".$codcaja."'";
                $em = $this->getDoctrine()->getManager();
                $q = $em->getConnection()->prepare($sql);
                $q->execute();
                $ubicaciones = $q->fetchall();
            }
            if (empty($ubicaciones)) {
                $flag = false;
                $msg = "Caja vacía o no registrada";
            } else {
                $data = $ubicaciones;
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }

    /**
     * @Route("/actualizaubicacion", name="actualizaubicacion", methods="POST")
     */
    public function actualiza_ubicacion(Request $request):Response
    {
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['idusuario'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } else {
                $idusuario = $temp['idusuario'];
                $usuario = $em->getRepository(TblUsuarios::class)->findOneBy(array('idusuario'=> $idusuario));
                if (empty($usuario)) {
                    $flag = false;
                    $msg = "idusuario inválido";
                }
            }
            
            
            if ($flag&&empty($temp['ubicaciones'])) {
                $flag = false;
                $msg = "Campo IDUSUARIO es obligatorio";
            } elseif ($flag) {
                $ubicaciones = $temp['ubicaciones'];
                
                foreach ($ubicaciones as $ubic) {
                    $flag = true;
                    $msg = "";
                    $solicitud = [];
                    
                    if (empty($ubic['idubicaciones'])) { //nuevo registro
                        $ubicacion = new TblUbicaciones();
                        $ubicacion->setSede($ubic['sede']);
                        $ubicacion->setCodhistoria($ubic['codhistoria']);
                        $ubicacion->setCaja($ubic['caja']);
                        $ubicacion->setFolio($ubic['folio']);
                        $ubicacion->setEstado($ubic['estado']);
                        $ubicacion->setFecharegistro(date('Y-m-d H:i:s'));
                        $ubicacion->setFechaactualizado(date('Y-m-d H:i:s'));
                        $ubicacion->setResponsable($usuario->getApellidos()." ".$usuario->getNombre());

                        $em->persist($ubicacion);
                        $em->flush();
                        $msg = "Ubicación nueva registrada";
                    } else { //update
                        $ubicacion = $em->getRepository(TblUbicaciones::class)->findOneBy(array('idubicaciones'=> $ubic['idubicaciones']));
                        if (empty($ubicacion)) {
                            $flag = true;
                            $msg = "IDUBICACIONES incorrecto";
                        } else {
                            $ubicacion->setSede($ubic['sede']);
                            $ubicacion->setCodhistoria($ubic['codhistoria']);
                            $ubicacion->setCaja($ubic['caja']);
                            $ubicacion->setFolio($ubic['folio']);
                            $ubicacion->setEstado($ubic['estado']);
                            $ubicacion->setFecharegistro(date('Y-m-d H:i:s'));
                            $ubicacion->setFechaactualizado(date('Y-m-d H:i:s'));
                            $ubicacion->setResponsable($usuario->getApellidos()." ".$usuario->getNombre());
                            
                            $em->flush();
                            $msg = "actualizado correctamente";
                            //buscamos la solicitud si es que es del dia siguiente.
                            $tomorrow = date("Y-m-d", strtotime("+1 day"));
                            //$solicitud = $em->getRepository(TblSolicitudes::class)->findOneBy(array('idubicaciones'=> $ubic->idubicaciones));
                            $sql = "SELECT
                            s.idsolicitud,
                            s.fechapedido,
                            s.codtipopedido,
                            s.codmedico,
                            s.nommedico,
                            s.dnuevo,
                            s.anulado,
                            s.idcita,
                            s.estado,
                            s.codzona,
                            s.idhc,
                            s.codpaciente,
                            s.codconsultorio,
                            s.nomconsultorio,
                            s.reqplaca,
                            s.codsede,
                            s.observaciones,
                            s.cumplimiento,
                            s.responsable,
                            s.responsablec,
                            s.folio

                            FROM tbl_solicitudes s
                            WHERE s.folio = '".$ubicacion->getFolio() ."'
                            AND s.idhc = '" . $ubicacion->getCodhistoria() ."'
                            AND DATE(s.fechapedido) = '".$tomorrow . "' 
                            ";

                            $em = $this->getDoctrine()->getManager();
                            $q = $em->getConnection()->prepare($sql);
                            $q->execute();
                            $solicitud = $q->fetchall();
                        }
                    }
                    $data[] = [
                        'error'     => false,
                        'flag'      => $flag,
                        'msg'       =>  $msg,
                        'solicitud'  =>  $solicitud
                    ];
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'data'  =>  $data
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }

    /**
     * @Route("/stickerfolder", name="stickerfolder", methods="POST")
     */
    public function stickerfolder(Request $request):Response
    {
        try {
            //$codhistoria;
            //sede
            //folio
            //caja
            //print
            //consultar tabla HC (idhc, sede)
            //imprimir_folder($codhistoria,codpaciente,sede, folio, caja, print)
            $em = $this->getDoctrine()->getEntityManager();
            $temp = $request->request->all();
            $flag = true;
            $msg = "";
            $data = [];
            if (empty($temp['codhistoria'])) {
                $flag = false;
                $msg = "Campo CODHISTORIA es obligatorio";
            }
            if ($flag&&empty($temp['sede'])) {
                $flag = false;
                $msg = "Campo SEDE es obligatorio";
            }
            if ($flag&&empty($temp['folio'])) {
                $temp['folio'] = 0;
            }
            if ($flag&&empty($temp['caja'])) {
                $flag = false;
                $msg = "Campo CAJA es obligatorio";
            }
            if ($flag&&empty($temp['print'])) {
                $flag = false;
                $msg = "Campo PRINT es obligatorio";
            }
            if ($flag) {
                $codhistoria    = $temp['codhistoria'];
                $sede           = $temp['sede'];
                $folio          = $temp['folio'];
                $caja           = $temp['caja'];
                $print          = $temp['print'];
                $hc = $em->getRepository(TblHc::class)->findOneBy(array('codhistoria'=> $codhistoria, 'sede'=>$sede));
                if (empty($hc)) {
                    $flag = false;
                    $msg = "No se encontro un paciente para esta sede/codhistoria";
                } else {
                    $codpaciente = $hc->getCodpaciente();
                    $printer = new Printstickerpos($em);
                    //$msg = "imprimiendo";
                    $msg = $printer->imprimirfolder($codhistoria, $codpaciente, $sede, $folio, $caja, $print);
                }
            }
            $data = [
                'error' => false,
                'flag'   => $flag,
                'msg'   =>  $msg
            ];
            $response = new Response(
                json_encode($data, true),
                Response::HTTP_OK,
                ['Content-type' => 'application/json']
            );
            return $response;
        } catch (\Exception $e) {
            return Response::create($e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine());
        }
    }


    private function ubi_archivo($codhistoria, $codsede, $folio, $col)
    {

       

        // $conn = $this->em->getConnection();
        if (empty($codhistoria)) {
            return 'n/a';
        }
        if ($codsede != '02') {
            $codsede = '01';
        }
        $em = $this->getDoctrine()->getEntityManager();
        $ubicacion = $em->getRepository(TblUbicaciones::class)->findOneBy(array('codhistoria' => $codhistoria,'sede' =>$codsede, 'folio' => $folio ));
        if($col == 'caja'){
            return $ubicacion->getCaja();
        }
        

        // $sql = "SELECT ".$col." FROM tbl_ubicaciones WHERE codhistoria=".$codhistoria." and sede='".$codsede."' and folio=".$folio." limit 1";
        // $q = $conn->prepare($sql);
        // $q->execute();
        // $rws = $q->fetch();
        // return empty($rws) ? '' : $rws[$col];
    }

}
