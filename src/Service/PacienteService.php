<?php
namespace App\Service;

use App\Entity\TblHc;
use App\Entity\TblPaciente;
use App\Entity\TblSolicitudes;
use Doctrine\ORM\EntityManager;

class PacienteService
{
    protected $em;
    protected $codhistoria;
    protected $sede;
    protected $fechapedido;
    protected $horapedido;
    protected $codtipopedido;
    protected $codmedico;
    protected $nommedico;
    protected $dnuevo;
    protected $anulado;
    protected $codzona;
    protected $codconsultorio;
    protected $nomconsultorio;
    protected $idcita;
    protected $reqplaca;
    protected $codpaciente;
    protected $idsolicitud;
    protected $responsable;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public function InsertarPaciente(
        $nombrepaciente, //paciente
        $apellidopaterno,
        $apellidomaterno,
        $codpaciente,
        $numdocumento,
        $fechanacimiento,
        $codtipodocumento,
        $email,
        $telefono,
        $celular,
        $codigopostal,
        $pais,
        $codubigeo,
        $departamento,
        $localidad,
        $direccion,
        $numdireccion
    ) {
        
        // paciente
        if (empty($codpaciente) or $codpaciente == '?') {
            // file_put_contents($fichero, "Codigo de paciente requerido" . "\n", FILE_APPEND);
            return 'Codigo de paciente requerido';
        }
        if (empty($nombrepaciente) or $nombrepaciente == '?') {
            // file_put_contents($fichero, "Nombre de paciente requerido" . "\n", FILE_APPEND);
            return 'Nombre de paciente requerido';
        }
        

        $apellidopaterno  = empty($apellidopaterno) ? '' : $apellidopaterno;
        $apellidomaterno = empty($apellidomaterno) ? '' : $apellidomaterno;
        $codtipodocumento = empty($codtipodocumento) ? 'S/D' : $codtipodocumento;            
        $busqueda = $apellidopaterno.' '.$apellidomaterno .' '. $nombrepaciente;
        $numdocumento = empty($numdocumento) ? '' : $numdocumento;
        $fechanacimiento = empty($fechanacimiento) ? '' : $fechanacimiento;
        $pais = empty($pais) ? '160' : $pais;
        $departamento = empty($departamento) ? '' : $departamento;
        $localidad = empty($localidad) ? '' : $localidad;
        $numdireccion = empty($numdireccion) ? '' : $numdireccion;
        $codigopostal = empty($codigopostal) ? '' : $codigopostal;
        $celular = empty($celular) ? '' : $celular;
        $telefono = empty($telefono) ? '' : $telefono;
        $email = empty($email) ? '' : $email;
        $codubigeo = empty($codubigeo) ? '' : $codubigeo;
        $codconyuge = '';
        $fecharegistro = date('Y-m-d H:i:s');
        //verificar codigo de paciente
        $check = $this->em->getRepository(TblPaciente::class)->findOneby(array('codpaciente' => $codpaciente));
        if (empty($check)) {           
            try {
                $TblPaciente = new TblPaciente();
                $TblPaciente->setNompaciente($nombrepaciente);
                $TblPaciente->setApellidopaterno($apellidopaterno);
                $TblPaciente->setApellidomaterno($apellidomaterno);
                $TblPaciente->setBusqueda($busqueda);
                $TblPaciente->setCodpaciente($codpaciente);
                $TblPaciente->setCodtipodocumento($codtipodocumento);
                $TblPaciente->setNumdocumento($numdocumento);
                $TblPaciente->setFechanacimiento($fechanacimiento);
                $TblPaciente->setEmail($email);
                $TblPaciente->setTelefono($telefono);
                $TblPaciente->setCelular($celular);
                $TblPaciente->setPais($pais);
                $TblPaciente->setCodubigeo($codubigeo);
                $TblPaciente->setDepartamento($departamento);
                $TblPaciente->setLocalidad($localidad);
                $TblPaciente->setDireccion($direccion);
                $TblPaciente->setNumdireccion($numdireccion);
                $TblPaciente->setCodigopostal($codigopostal);
                $TblPaciente->setCodconyuge($codconyuge);
                $TblPaciente->setRegistro($fecharegistro);

                $TblPaciente->setEstado('0');
                $nombrepaciente = preg_replace("/[']/", "\'", $nombrepaciente);
                $apellidopaterno = preg_replace("/[']/", "\'", $apellidopaterno);
                $apellidomaterno = preg_replace("/[']/", "\'", $apellidomaterno);
                $concat = $apellidopaterno.' '.$apellidomaterno.' '.$nombrepaciente;
                $TblPaciente->setBusqueda($concat);

                $this->em->persist($TblPaciente);
                $this->em->flush();
                return "Nuevo paciente registrado";
            } catch (\Exception $e) {
                file_put_contents($fichero, "Excepcion:" .date('d/m/Y H:m:s').'-'. $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine() . "\n", FILE_APPEND);
                return $e->getMessage();
            }
        } else {
            try {
                $sql = "UPDATE \App\Entity\TblPaciente s SET ";
                $sql .= " s.nompaciente='" . $nombrepaciente . "',";
                $sql .= " s.apellidopaterno='" . $apellidopaterno . "',";
                $sql .= " s.apellidomaterno='" . $apellidomaterno . "',";
                $sql .= " s.codconyuge='" . $codconyuge . "',";
                $sql .= " s.busqueda='" . $busqueda . "',";
                $sql .= " s.codtipodocumento='" . $codtipodocumento . "',";
                $sql .= " s.numdocumento='" . $numdocumento . "',";
                $sql .= " s.fechanacimiento='" . $fechanacimiento . "',";
                $sql .= " s.email='" . $email . "',";
                $sql .= " s.telefono='" . $telefono . "',";
                $sql .= " s.celular='" . $celular . "',";
                $sql .= " s.pais='" . $pais . "',";
                $sql .= " s.codubigeo='" . $codubigeo . "',";
                $sql .= " s.departamento='" . $departamento . "',";
                $sql .= " s.localidad='" . $localidad . "',";
                $sql .= " s.direccion='" . $direccion . "',";
                $sql .= " s.numdireccion='" . $numdireccion . "',";
                $sql .= " s.codigopostal='" . $codigopostal . "',";
                $sql .= " s.registro='" . $fecharegistro . "',";                
                $sql .= " s.estado='1'";
                $sql .= " WHERE s.codpaciente='" . $codpaciente . "'";
                $update = $this->em->createQuery($sql);
                $rs = $update->execute();
                return "Paciente actualizado";
            } catch (\Expcetion $e) {
               
                $fichero = __DIR__ . "/../../public/logs/InsertarPaciente-WS-Error.tx";
                file_put_contents($fichero, "Excepcion:" .date('d/m/Y H:m:s').'-'. $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine() . "\n", FILE_APPEND);
                return $e->getMessage();
            }
        }
    }
    public function InsertarHistoria(
        $codhistoria,
        $codpaciente,
        $sede,
        $tipohc
    ) {
        if (empty($codpaciente)) {
            return 'Codigo de paciente requerido';
        }
        if (empty($codhistoria)) {
            return "Codigo de historia es requerido";
        }
        if (empty($sede)) {
            return "Codigo Sede es requerido";
        }
        $tipohc = empty($tipohc) ? 1 : $tipohc;
        // historia      
       
        //seleccionar paciente
        $TblPaciente = $this->em->getRepository(TblPaciente::class)->findOneby(array('codpaciente' => $codpaciente));
        if (empty($TblPaciente)) {
            return "El paciente no se encuentra registrado";
        }

        // $check = $this->em->getRepository(TblHc::class)->findOneby(array('sede' => $sede, 'codhistoria' => $codhistoria, 'codpaciente' => $codpaciente));
        $check = $this->em->getRepository(TblHc::class)->findOneby(array('sede' => $sede, 'codpaciente' => $codpaciente));
        // si esta vacio se inserta
        if (empty($check)) {
            try {
                $TblHC = new Tblhc();
                $TblHC->setRegistro(\DateTime::createFromFormat('Y-m-d', date("Y-m-d")));
                $TblHC->setSede($sede);
                $TblHC->setTipohc($tipohc);
                $TblHC->setCodhistoria($codhistoria);
                //em
                $TblHC->setCodpaciente($TblPaciente->getCodpaciente());
                $this->em->persist($TblHC);
                $this->em->flush();
                return 'Nueva Historia Clinica registrada';
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            // si existe aqui actualiza
            try {
                $sql = "UPDATE \App\Entity\TblHc h SET ";
                $sql .= " h.tipohc='" . $tipohc . "',";
                $sql .= " h.sede='" . $sede . "',";
                $sql .= " h.codhistoria='" . $codhistoria . "'";
                // $sql .= " WHERE h.codpaciente='" . $codpaciente . "' AND h.codhistoria='" . $codhistoria . "' AND h.sede='" . $sede . "'";
                $sql .= " WHERE h.codpaciente='" . $codpaciente . "' AND h.sede='" . $sede . "'";
                $update = $this->em->createQuery($sql);
                $rs = $update->execute();
                return "Paciente actualizado";
            } catch (\Expcetion $e) {
                $fichero = __DIR__ . "/../../public/logs/InsertarHistoria-WS-Error.tx";
                file_put_contents($fichero, "Excepcion:" .date('d/m/Y H:m:s').'-'.$e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine() . "\n", FILE_APPEND);
                return $e->getMessage();
            }
        }
    }
    public function InsertarPedido(
        $codhistoria, //solicitud
        $codsede,
        $fechapedido,
        $horapedido,
        $codtipopedido,
        $codmedico,
        $nommedico,
        $dnuevo,
        $anulado,
        $codzona,
        $codconsultorio,
        $nomconsultorio,
        $idcita,
        $reqplaca,
        $codpaciente,
        $responsable
    ){
        //validaciones
        if (empty($codsede)) {
            return "Codigo Sede es requerido";
        }
        if (empty($fechapedido)) {
            return "Fecha de pedido requerido";
        }
        if (empty($horapedido)) {
            return "Hora de pedido requerido";
        }
        if (empty($codpaciente)) {
            return "Codigo de Paciente requerido";
        }
        if (empty($idcita)) {
            return "Codigo de Cita requerido";
        }
        //parse
        $fechapedido = str_replace('/', '-', $fechapedido);
        $fechapedido = date('Y-m-d', strtotime($fechapedido));
        $fechahorapedido = date('Y-m-d', strtotime($fechapedido))." ".$horapedido;
        $fecharegistro = date('Y-m-d H:i:s');
        $dia =  date(strtotime($fechapedido));
        if(date('Y-m-d', $dia) == date('Y-m-d')){
            $codtipopedido = "N";
        } else{
            $codtipopedido = "P";
        }
        $rslt = "";
        $idSol = "";
        $message = "";

        try{
            //validamos el estado del pedido:
            if($anulado==2){//ANULAREMOS LA SOLICITUD.
                $this->anularSol($idcita);
                //file_put_contents($fichero, (date('d/m/Y H:m:s')."> Solicitud anulada -> ".$this->codhistoria."/".$this->codpaciente."\n"), FILE_APPEND);
                $message =  'Solicitud anulada';
                // return  'Solicitud anulada';
            }else if($anulado==1){
                //VALIDAMOS QUE EXISTA DICHA SOLICITUD:
                /*$sql = "SELECT * FROM tbl_solicitudes WHERE idcita=:idcita";
                $q = $this->em->getConnection()->prepare($sql);
                $q->bindValue('idcita', $idcita);
                $q->execute();
                $solicitud = $q->fetch();*/
                $pedido = $this->em->getRepository(TblSolicitudes::class)->findOneBy(array('idcita' => $idcita));
                
                if(empty($pedido)){
                    $message =  'No existe la solicitud "'.$idcita.'" para actualizar';
                    // return 'No existe la solicitud "'.$idcita.'" para actualizar';
                }else{
                    
                    //Actualizamos la informacion:
                    $pedido->setCodsede($codsede);//
                    $pedido->setRegistro($fecharegistro);
                    $pedido->setFechapedido($fechahorapedido);//
                    $pedido->setCodtipopedido($codtipopedido);//
                    $pedido->setCodmedico($codmedico);//
                    $pedido->setNommedico($nommedico);//
                    $pedido->setDnuevo($dnuevo);//
                    $pedido->setAnulado($anulado);//
                    $pedido->setIdcita($idcita);//
                    $pedido->setCodzona($codzona);//
                    $pedido->setCodpaciente($codpaciente);//
                    $pedido->setCodconsultorio($codconsultorio);//
                    $pedido->setNomconsultorio($nomconsultorio);//
                    $pedido->setReqplaca($reqplaca);//
                    $pedido->setObservaciones("Actualizado por WS");
                    $pedido->setIdhc($codhistoria);//
                    $pedido->setResponsablec($responsable);//
                    $this->em->flush();   
                    $idSol =  $pedido->getIdsolicitud();                
                    $message =  'Solicitud "'.$idcita.'" actualizada correctamente';
                  
                    // return 'Solicitud "'.$idcita.'" actualizada correctamente';
                }
                //si( dia = HOY -> N) //impresion: N+A+I:hoy 
                
            }else{ //NUEVO REGISTRO
                //verificamos que no exista uno anterior                
                $pedido = $this->em->getRepository(TblSolicitudes::class)->findOneBy(array('codpaciente' => $codpaciente, 'idhc' => $codhistoria, 'estado'=>'4'));
                if(!empty($pedido)){
                    //ES UNA INTERCONSULTA, EL ESTADO VUELVE A 3
                    $codtipopedido = 'I';
                    $pedido->setRegistro($fecharegistro);
                    $pedido->setCodsede($codsede);//
                    $pedido->setFechapedido($fechahorapedido);//
                    $pedido->setCodtipopedido($codtipopedido);//
                    $pedido->setCodmedico($codmedico);//
                    $pedido->setNommedico($nommedico);//
                    $pedido->setDnuevo($dnuevo);//
                    $pedido->setAnulado($anulado);//
                    $pedido->setEstado('3');
                    $pedido->setCodzona($codzona);//
                    $pedido->setCodpaciente($codpaciente);//
                    $pedido->setCodconsultorio($codconsultorio);//
                    $pedido->setNomconsultorio($nomconsultorio);//
                    $pedido->setIdcita($idcita);//
                    $pedido->setReqplaca($reqplaca);//
                    $pedido->setObservaciones("Actualizado por WS");
                    $pedido->setCumplimiento("");
                    $pedido->setResponsable("0");
                    $pedido->setIdhc($codhistoria);//
                    $pedido->setFolio('0');
                    $pedido->setResponsableb("");
                    $pedido->setResponsablec($responsable);//
                    $this->em->flush();
                    $message =  'Actualizado a Interconsulta';
                    $idSol =  $pedido->getIdsolicitud();  
                    // return  'Actualizado a Interconsulta';
                }else{
                    
                    //validamos IDCITA
                    /*$sql = "SELECT * FROM tbl_solicitudes WHERE idcita=:idcita";
                    $q = $this->em->getConnection()->prepare($sql);
                    $q->bindValue('idcita', $idcita);
                    $q->execute();
                    $solicitud = $q->fetch();*/
                   
                    $solicitud = $this->em->getRepository(TblSolicitudes::class)->findOneBy(array('idcita' => $idcita));
                    if(!empty($solicitud)){
                       $message =  'Ya xiste otra solicitud con el mismo idcita';
                        // return "Ya xiste otra solicitud con el mismo idcita";
                    }else{
                        //validamos información del usuario
                        $sql = "SELECT * FROM tbl_paciente WHERE codpaciente=:codpaciente";
                        $q = $this->em->getConnection()->prepare($sql);
                        $q->bindValue('codpaciente', $codpaciente);
                        $q->execute();
                        $paciente = $q->fetch();
                        //$solicitud = $this->em->getRepository(TblPaciente::class)->findOneBy(array('codpaciente' => $codpaciente));
                       
                        //return $paciente['codpaciente'];
                        if(empty($paciente)){
                            $message =  'El código del paciente es incorrecto';
                            //   return 'El código del paciente es incorrecto';
                        }else{
                            // //VALIDAMOS LA FECHA:
                            // if($fechapedido == date('Y-m-d')){
                            //     $codtipopedido = 'N';
                            // }else{
                            //     $codtipopedido = 'P';
                            // }
                            //preparamos para insertar
                            //$em->$this->getDoctrine()->getManager();
                            $pedido = new TblSolicitudes();
                            $pedido->setRegistro($fecharegistro);
                            $pedido->setCodsede($codsede);//
                            $pedido->setFechapedido($fechahorapedido);//
                            $pedido->setCodtipopedido($codtipopedido);//
                            $pedido->setCodmedico($codmedico);//
                            $pedido->setNommedico($nommedico);//
                            $pedido->setDnuevo($dnuevo);//
                            $pedido->setAnulado($anulado);//
                            $pedido->setEstado('0');
                            $pedido->setCodzona($codzona);//
                            $pedido->setCodpaciente($codpaciente);//
                            $pedido->setCodconsultorio($codconsultorio);//
                            $pedido->setNomconsultorio($nomconsultorio);//
                            $pedido->setIdcita($idcita);//
                            $pedido->setReqplaca($reqplaca);//
                            $pedido->setObservaciones("Registrado por WS");
                            $pedido->setCumplimiento("");
                            $pedido->setResponsable("0");
                            $pedido->setIdhc($codhistoria);//
                            $pedido->setFolio('0');
                            $pedido->setResponsableb("");
                            $pedido->setResponsablec($responsable);//
                            $this->em->persist($pedido);
                            $this->em->flush();
                            $message = 'registrado correctamente';
                            $idSol =  $pedido->getIdsolicitud();  
                        }
                
                    }
                    
                }
            }
           //             
            if($anulado!=2 && !empty($idSol)){
                //impresion
                if(date('Y-m-d', $dia) == date('Y-m-d')){
                    try {    
                        $print = new \App\Controller\Printerpos($this->em);
                        $stprint = $print->boucher(27, $idSol, '0');                            
                        //$fichero = __DIR__ . "/../../public/logs/InsertarPedido-WS-PrintDAY.txt";           
                        //file_put_contents($fichero, PHP_EOL . $fecharegistro ." > ".$idSol . " -- DIA: " . date('Y-m-d', $dia). " \r\n", FILE_APPEND);  
                     } catch (\Expcetion $e) {
                        //  return JsonResponse::create(array("msg" => $e->getMessage()));
                         $message = "Solicitud registrado, Sin impresión en voucher: Error: " +  $e->getMessage();
                     }
                 }
            }
            


            return  $message;
        }catch (\Exception $e) {
            
            $fichero = __DIR__ . "/../../public/logs/InsertarPedido-WS-Error.tx";           
            file_put_contents($fichero, (date('d/m/Y H:m:s').">".$e->getMessage() .  "\n" . $e->getFile() . "\n" . $e->getLine()."\n"), FILE_APPEND);
            return $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }
    }

    // *********************************************
    // *********************************************
    public function InsertarPedido_ant(
        $codhistoria, //solicitud
        $sede,
        $fechapedido,
        $horapedido,
        $codtipopedido,
        $codmedico,
        $nommedico,
        $dnuevo,
        $anulado,
        $codzona,
        $codconsultorio,
        $nomconsultorio,
        $idcita,
        $reqplaca,
        $codpaciente,
        $responsable
    ) {
        if (empty($sede)) {
            return "Codigo Sede es requerido";
        }
        if (empty($fechapedido)) {
            return "Fecha de pedido requerido";
        }
        if (empty($horapedido)) {
            return "Hora de pedido requerido";
        }
        if (empty($codpaciente)) {
            return "Codigo de Paciente requerido";
        }
        $this->codtipopedido = empty($codtipopedido) ? "0" : $codtipopedido;
        $this->tipohc = empty($tipohc) ? 1 : 2;
        $this->idcita = empty($idcita) ? "0" : $idcita;
        $this->anulado = empty($anulado) ? 0 : $anulado;
        $this->codconsultorio = empty($codconsultorio) ? "0" : $codconsultorio;
        $this->dnuevo = empty($dnuevo) ? "0" : $dnuevo;
        $this->reqplaca = empty($reqplaca) ? "0" : $reqplaca;
        $this->codhistoria = empty($codhistoria) ? "" : $codhistoria;
        $this->sede = $sede;
        $this->fechapedido = $fechapedido;
        $this->horapedido = $horapedido;
        $this->codzona = empty($codzona) ? '000' : $codzona;
        $this->nomconsultorio = empty($nomconsultorio) ? '-' : $nomconsultorio;
        $this->nommedico = empty($nommedico) ? '-' : $nommedico;
        $this->codmedico = empty($codmedico) ? '000' : $codmedico;
        $this->resposable = empty($responsable) ? '' : $responsable;

        $this->codpaciente = $codpaciente;

       
        $rslt = "";
        try {
           
            $sql = "SELECT * FROM tbl_solicitudes WHERE idcita=:idcita";
            $q = $this->em->getConnection()->prepare($sql);
            $q->bindValue('idcita', $this->idcita);
            $q->execute();
            $csol = $q->fetch();
           
            if (empty($csol)) {
                $rslt = $this->actRegistro();
                if ($rslt == 'N') {
                    $rslt = 'Imprimiento Ticket';
                } else {
                    $rslt = 'Programada';
                }
                file_put_contents($fichero, (date('d/m/Y H:m:s').">Reg.tipo:".$rslt." -> ".$this->codhistoria."/".$this->codpaciente."\n"), FILE_APPEND);
                return 'Solicitud registrada correctamente / ' . $rslt;
            } else {
                if ($this->anulado == '0') {
                    $ret = $this->verificarFechaActualSede($csol['idsolicitud'], $this->sede);
                    if ($ret == 'I') {
                        $this->Interconsulta($csol['idsolicitud']);
                        file_put_contents($fichero, (date('d/m/Y H:m:s').">Act. con tipo: Interconsulta -> ".$this->codhistoria."/".$this->codpaciente."\n"), FILE_APPEND);
                        return 'Solicitud actualizada a Interconsulta';
                    } elseif ($ret == 'N') {
                        $this->Noprogramada($csol['idsolicitud']);
                        file_put_contents($fichero, (date('d/m/Y H:m:s').">Act. con tipo: No programada -> ".$this->codhistoria."/".$this->codpaciente."\n"), FILE_APPEND);
                        return 'Solicitud actualizada a No programada';
                    } else {
                        $this->actSolicitud($csol['idsolicitud']);
                        $this->actImprimirSolActualizado($csol['idsolicitud']);
                        file_put_contents($fichero, (date('d/m/Y H:m:s')."> Solicitud actualizada -> ".$this->codhistoria."/".$this->codpaciente."\n"), FILE_APPEND);
                        return 'Solicitud actualizada ';
                    }
                } elseif ($this->anulado == '2') {
                    $this->anularSol($csol['idsolicitud']);
                    file_put_contents($fichero, (date('d/m/Y H:m:s')."> Solicitud anulada -> ".$this->codhistoria."/".$this->codpaciente."\n"), FILE_APPEND);
                    return 'Solicitud anulada';
                } else {
                    return 'Sin actualizaciones';
                }
            }
        } catch (\Exception $e) {
            $fichero = __DIR__ . "/../../public/logs/InsertarPedido-WS-Error.txt";
            file_put_contents($fichero, (date('d/m/Y H:m:s').">".$e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine()."\n"), FILE_APPEND);
            return $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }
    }
    private function verificarFechaActualSede($idsolicitud, $sede)
    {
        try {
            $sql = "SELECT * FROM tbl_solicitudes
                    WHERE STR_TO_DATE( CONVERT(fechapedido,char) ,'%e/%c/%Y')=DATE_FORMAT(now(),'%Y-%c-%e')
                    AND anulado='0'
                    AND idsolicitud=:idsolicitud
                    AND codsede=:codsede ";
            $q = $this->em->getConnection()->prepare($sql);
            $q->bindValue('idsolicitud', $idsolicitud);
            $q->bindValue('codsede', $sede);
            $q->execute();
            $csol = $q->fetch();
            if (!empty($csol)) {
                if ($csol['estado'] == '4') {
                    return 'I';
                } elseif ($csol['estado'] == '1') {
                    return 'N';
                } else {
                    return 'P';
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }
    }
    private function anularSol($idsolicitud)
    {
        $sql = "UPDATE tbl_solicitudes SET  estado='8' WHERE  idsolicitud='" . $idsolicitud . "'";
        $q = $this->em->getConnection()->prepare($sql);
        $q->execute();
    }
    private function Interconsulta($idsolicitud)
    {
        $sql = "UPDATE tbl_solicitudes SET  codtipopedido='I', responsable='18', estado='4' WHERE  idsolicitud='" . $idsolicitud . "' AND anulado='0'";
        $q = $this->em->getConnection()->prepare($sql);
        $q->execute();

        $print = new \App\Controller\Printerpos($this->em);
        $stprint = $print->boucher('18', $idsolicitud, '0');
    }
    private function Noprogramada($idsolicitud)
    {
        $sql = "UPDATE tbl_solicitudes SET  codtipopedido='N', responsable='18' WHERE  idsolicitud='" . $idsolicitud . "' AND anulado='0'";
        $q = $this->em->getConnection()->prepare($sql);
        $q->execute();

        $fichero = __DIR__ . "/../../public/Noprogramada.txt";
        file_put_contents($fichero, 'idsolicitud:' . $idsolicitud, FILE_APPEND);

        $print = new \App\Controller\Printerpos($this->em);
        $stprint = $print->boucher('18', $idsolicitud, '0');
    }
    private function actRegistro()
    {
        try {
            $pedido = new TblSolicitudes();
            $pedido->setSolicitud(date('Y-m-d H:i:s'));
            $pedido->setCodsede($this->sede);
            $pedido->setFechapedido($this->fechapedido);
            $pedido->setHorapedido($this->horapedido);
            $pedido->setCodtipopedido($this->codtipopedido);
            $pedido->setCodmedico($this->codmedico);
            $pedido->setNommedico($this->nommedico);
            $pedido->setDnuevo($this->dnuevo);
            $pedido->setAnulado($this->anulado);
            $pedido->setEstado('0');
            $pedido->setCodzona($this->codzona);
            $pedido->setCodpaciente($this->codpaciente);
            $pedido->setCodconsultorio($this->codconsultorio);
            $pedido->setNomconsultorio($this->nomconsultorio);
            $pedido->setIdcita($this->idcita);
            $pedido->setReqplaca($this->reqplaca);
            $pedido->setObservaciones("ws");
            $pedido->setCumplimiento("-");
            $pedido->setResponsable("0");
            $pedido->setIdhc($this->codhistoria);
            $pedido->setResponsablec($this->responsable);
            $this->em->persist($pedido);
            $this->em->flush();
            $idsolicitud = $pedido->getIdsolicitud();
            return $this->actImprimirSolRegistrado($idsolicitud);
            //return 1; //"Solicitud fue registrada correctamente";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    private function actSolicitud($idsolicitud)
    {
        try {
            $sql = "UPDATE \App\Entity\TblSolicitudes p SET ";
            $sql .= " p.codsede='" . $this->sede . "',";
            $sql .= " p.fechapedido='" . $this->fechapedido . "',";
            $sql .= " p.horapedido='" . $this->horapedido . "',";
            $sql .= " p.codtipopedido='" . $this->codtipopedido . "',";
            $sql .= " p.codmedico='" . $this->codmedico . "',";
            $sql .= " p.nommedico='" . $this->nommedico . "',";
            $sql .= " p.dnuevo='" . $this->dnuevo . "',";
            $sql .= " p.anulado='" . $this->anulado . "',";
            $sql .= " p.codzona='" . $this->codzona . "',";
            $sql .= " p.codconsultorio='" . $this->codconsultorio . "',";
            $sql .= " p.nomconsultorio='" . $this->nomconsultorio . "',";
            $sql .= " p.responsablec='" . $this->responsable . "',";
            $sql .= " p.reqplaca='" . $this->reqplaca . "' ";
            $sql .= " p.observaciones='ws' ";
            //$sql .= " WHERE p.idcita='" . $this->idcita . "' AND p.codpaciente='" . $this->codpaciente . "'";
            $sql .= " WHERE p.idsolicitud='" . $idsolicitud . "'";
            // p.fechapedido=:fechapedido AND horapedido=:horapedido AND
            $update = $this->em->createQuery($sql);
            $rs = $update->execute();
            return 1; //"Solicitud actualizada";
        } catch (\Exception $e) {
            return "actSolicitud :" . $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }
    }
    private function actImprimirSolRegistrado($idsolicitud)
    {
        try {
            $fichero = __DIR__ . "/../../public/actImprimirSolRegistrado.txt";
            file_put_contents($fichero, "idsolicitud :" . $idsolicitud, FILE_APPEND);
            // actualizar solicitud a 1 en busqueda
            if ($this->codtipopedido == 'N') {
                $q = $this->em->getConnection()->prepare("UPDATE tbl_solicitudes
            SET  estado='1', responsable='18',  cumplimiento='no programada'
            WHERE STR_TO_DATE( CONVERT(fechapedido,char) ,'%e/%c/%Y')=DATE_FORMAT(now(),'%Y-%c-%e')
            AND hour(now())=hour(horapedido)
            AND anulado='0'
            AND idsolicitud='" . $idsolicitud . "'");
                $q->execute();
            } else {
                $q = $this->em->getConnection()->prepare("UPDATE tbl_solicitudes
            SET  estado='1', responsable='18', cumplimiento='programada'
            WHERE STR_TO_DATE( CONVERT(fechapedido,char) ,'%e/%c/%Y')=DATE_FORMAT(now(),'%Y-%c-%e')
            AND anulado='0'
            AND idsolicitud='" . $idsolicitud . "'");
                $q->execute();
            }

            $print = new \App\Controller\Printerpos($this->em);
            $stprint = $print->boucher('18', $idsolicitud, '0');
            if ($stprint == 1) {
                return 'N';
            } else {
                return 'P';
            }
        } catch (\Exception $e) {
            return $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }
    }
    private function actImprimirSolActualizado($idsolicitud)
    {
        try {

            // seleccionar actualizado
            $sql = "SELECT * FROM tbl_solicitudes
                    WHERE codtipopedido='P'
                    AND  STR_TO_DATE( CONVERT(fechapedido,char) ,'%e/%c/%Y')=DATE_FORMAT( NOW() ,'%Y-%c-%e')
                    AND hour(now())=hour(horapedido)
                    AND anulado='0'
                    AND idsolicitud='" . $idsolicitud . "'
                    AND codsede='" . $this->sede . "'";
            $q = $this->em->getConnection()->prepare($sql);
            $q->execute();
            $csol = $q->fetch();

            // actualizar solicitud a 1 en busqueda
            /*$qpdf = $this->em->getConnection()->prepare("UPDATE tbl_solicitudes  SET  estado='1', responsable='18'
            WHERE STR_TO_DATE( CONVERT(fechapedido,char) ,'%e/%c/%Y')='" . $this->fechapedido . "'
            AND horapedido='" . $this->horapedido . "'
            AND codpaciente='" . $codpaciente . "'
            AND codsede='" . $this->sede . "'");
            $rs = $qpdf->execute();*/

            $fichero = __DIR__ . "/../../public/actImprimirSolActualizado.txt";
            file_put_contents($fichero, ($idsolicitud."\n"), FILE_APPEND);

            // imprimir boucher
            $print = new \App\Controller\Printerpos($this->em);
            $stprint = $print->boucher('18', $csol['idsolicitud'], '0');
        } catch (\Exception $e) {
            return $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine();
        }
    }
}
