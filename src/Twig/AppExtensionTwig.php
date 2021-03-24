<?php

namespace App\Twig;

use App\Entity\TblAreas;
use App\Entity\TblHc;
use App\Entity\TblUbicaciones;
use App\Entity\TblUsuarios;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use \App\Entity\TblHistorial;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\RedirectResponse;

setlocale(LC_ALL, 'es_ES', 'es_ES.UTF-8');
date_default_timezone_set('America/Lima');

class AppExtensionTwig extends AbstractExtension
{
    protected $em;
    protected $container;

    public function __construct(RegistryInterface $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    public function getFilters(): array
    {
        return [
            //new \Twig_SimpleFilter('estado', [$this,"estadoFilter"], ['is_safe' => ['html']])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('stpedido', [$this, 'stpedido']),
            new TwigFunction('convertirfecha', [$this, 'convertirfecha']),
            
            new TwigFunction('nombre_de_dias', [$this, 'nombre_de_dias']),
            new TwigFunction('nombre_de_meses', [$this, 'nombre_de_meses']),
            new TwigFunction('nombre_sede', [$this, 'nombre_sede']),
            new TwigFunction('nombre_paciente', [$this, 'nombre_paciente']),
            new TwigFunction('tipo_historia', [$this, 'tipo_historia']),
            new TwigFunction('tipo_pedido', [$this, 'tipo_pedido']),
            new TwigFunction('ubi_archivo', [$this,'ubi_archivo']),
            new TwigFunction('formato_hc', [$this,'formato_hc']),
            new TwigFunction('formato_folio', [$this, 'formato_folio']),
            new TwigFunction('referencia', [$this, 'referencia']),
            new TwigFunction('nombre_usuario', [$this, 'nombre_usuario']),
            new TwigFunction('dato_usuario', [$this, 'dato_usuario']),
            
            new TwigFunction('usuarioh', [$this, 'usuarioh']),
            new TwigFunction('npaciente', [$this, 'npaciente']),
            new TwigFunction('detallesolicitud', [$this, 'detallesolicitud']),
            new TwigFunction('getareaporid', [$this, 'getareaporid']),
            new TwigFunction('gethcporsolicitud', [$this, 'gethcporsolicitud']),
            new TwigFunction('contadorderegistro', [$this, 'contadorderegistro']),
            //  no se usa
            new TwigFunction('solicitudes', [$this, 'solicitudes']),
            new TwigFunction('origen_pedido', [$this, 'origen_pedido']),
            new TwigFunction('ubicacion_actual', [$this, 'ubicacion_actual']),
            new TwigFunction('detallepaciente', [$this, 'detallepaciente']),
            new TwigFunction('detalleubi', [$this, 'detalleubi']),
            new TwigFunction('detallearea', [$this, 'detallearea']),
            new TwigFunction('historialSolicitud', [$this, 'historialSolicitud']),
            new TwigFunction('tblubicaciones', [$this, 'tblubicaciones']),
            new TwigFunction('tblhc', [$this, 'tblhc']),
            new TwigFunction('tagstats', [$this, 'tagstats']),
            new TwigFunction('tUbi', [$this,'tUbi']),
            new TwigFunction('caja', [$this,'caja']),
            new TwigFunction('autorizar', [$this,'autorizar'])


        ];
    }
    public function autorizar()
    {
        $session  = $this->container->get("security.token_storage")->getToken()->getUser();
        if ($session == 'anon.') {
            return new RedirectResponse("/logout");
        }
        if ($session->getStatus() == 'inactivo') {
            return new RedirectResponse("/logout");
        } else {
            return null;
        }
    }
    public function tblhc($id, $col)
    {
        if (empty($col)) {
            return "";
        }
        //$hc = $this->em->getRepository(TblHc::class)->findOneBy(array('codpaciente' => (int) $id));
        //return empty($hc) ? "" : $hc->$col();
        $conn = $this->em->getConnection();
        $rw = $conn->prepare("SELECT " . $col . " FROM tbl_hc WHERE codpaciente=" . (int) $id . "");
        $rw->execute();
        // $hc = $rw->fetch();
        $hc = $rw->fetchAll();
        return empty($hc) ? "n/a" : $hc[$col];
    }
    public function tblubicaciones($c, $s, $o)
    {
        $ub = $this->em->getRepository(TblUbicaciones::class)->findOneBy(array('codhistoria' => $c, 'sede' => $s));
        if (empty($ub)) {
            return '';
        }
        if ($o == 'caja') {
            return $ub->getCaja() . ' - ' . $ub->getFolio();
        } else {
            return $ub->getEstado();
        }
    }
    
    public function gethcporsolicitud($id)
    {
        $hc = $this->em->getRepository(TblHc::class)->findOneBy(array('idhc' => (int) $id));
        return $hc;
    }
    public function getareaporid($id)
    {
        $area = $this->em->getRepository(TblAreas::class)->findOneBy(array('idarea' => (int) $id));
        return $area;
    }
    public function detallesolicitud($id, $col="")
    {
        //$s = $this->em->getRepository(TblSolicitudes::class)->findOneBy(array('idsolicitud' => (int)$id));
        $sql = "SELECT codsede,
                nomconsultorio,
                STR_TO_DATE( CONVERT(fechapedido,char) ,'%e/%c/%Y') as fechapedido,
                horapedido,
                estado FROM tbl_solicitudes WHERE idsolicitud=" . (int) $id;
        $s = $this->em->getConnection()->prepare($sql);
        $s->execute();
        $rs = $s->fetch();
        
        switch ($col) {
            case 'codsede':
                return empty($rs)? 0 : $rs['codsede'];
                break;
            case 'nomconsultorio':
                return empty($rs)? 0 : $rs['nomconsultorio'];
            break;
            default:
                return '0';
            break;
        }
    }
    public function npaciente($codhistoria)
    {
        if (!empty($codhistoria)) {
            $codhistoria = preg_replace("/[^0-9]/", "", $codhistoria);
            // $codhistoria = str_pad($codhistoria, 7, '0', STR_PAD_LEFT);
            $sql = "SELECT p.nompaciente,
                    p.apellidopaterno,
                    p.apellidomaterno 
                    from tbl_hc hc, tbl_paciente p 
                    where p.codpaciente=hc.codpaciente 
                    and hc.codhistoria=".$codhistoria;
            $p = $this->em->getConnection()->prepare($sql);
            $p->execute();
            $rs = $p->fetch();
            $nombres = $rs['nompaciente'].' '.$rs['apellidopaterno'].' '.$rs['apellidomaterno'];
            return  $nombres;
        } else {
            // return ['nombresapellidos'=>""];
            return "n/a";
        }
    }
   
    public function usuarioh($v)
    {
        $u = $this->em->getRepository(TblUsuarios::class)->findOneBy(array('email' => $v));
        return empty($u) ? $v : $u->getNombre() . ' ' . $u->getApellidos();
    }
    public function historialSolicitud($id)
    {
        $h = $this->em->getRepository(TblHistorial::class)->findBy(array('idsolicitud' => (int) $id));
        return $h;
    }
    
   
    public function detalleubi($codigo, $col)
    {
        if (empty($col)) {
            return "";
        }
        $ubi = $this->em->getRepository(TblUbicaciones::class)->findOneBy(array('codhistoria' => (int) $codigo));
        $getcol = "get" . ucfirst($col);
        return empty($ubi) ? "n/a" : $ubi->$getcol();
    }
//  ***************************************
    //validar si de usa?
    public function detallearea($codsede, $codzona, $col)
    {
        if (empty($col)) {
            return "";
        }
        $area = $this->em->getRepository(TblAreas::class)->findOneBy(array('codzona' => $codzona));
        $getcol = "get" . ucfirst($col);
        return empty($area) ? "n/a" : $area->$getcol();
    }
     //validar si de usa?
     public function detallepaciente($id, $col)
     {
         if (empty($col)) {
             return "";
         }
         $conn = $this->em->getConnection();
         $rw = $conn->prepare("SELECT " . $col . " FROM tbl_paciente WHERE codpaciente='" . $id . "'");
         $rw->execute();
         $paciente = $rw->fetch();
         return empty($paciente) ? "n/a" : $paciente[$col];
     }
    //  ***************************************
    public function tUbi($codhistoria, $codsede, $col)
    {
        $conn = $this->em->getConnection();
        if (empty($codhistoria)) {
            return 'n/a';
        }
        if ($codsede != '02') {
            $codsede = '01';
        }
        $sql = "SELECT group_concat(ubi.caja) AS caja, 
        group_concat(ubi.folio) AS folio,
        group_concat(ubi.estado) AS estado
        FROM tbl_ubicaciones ubi 
        WHERE ubi.codhistoria='".$codhistoria."' and ubi.sede='".$codsede."'";

        $q = $conn->prepare($sql);
        $q->execute();
        $rw = $q->fetch();
        return empty($rw) ? 'n/a' : $rw[$col];
    }
   
  
    public function caja($codhistoria, $codsede, $folio)
    {
        $conn = $this->em->getConnection();
        if (empty($codhistoria)) {
            return 'n/a';
        }
        if ($codsede != '02') {
            $codsede = '01';
        }
        $sql = "SELECT caja FROM tbl_ubicaciones WHERE codhistoria='".$codhistoria."' and sede='".$codsede."' and folio='".$folio."' limit 1";
        $q = $conn->prepare($sql);
        $q->execute();
        $rw = $q->fetch();
        return empty($rw) ? '--' : $rw['caja'];
    }
    public function solicitudes($filtro)
    {
        $res = '';
        $conn = $this->em->getConnection();
        $sql = "SELECT distinct
        s.idsolicitud,
        date(s.fechapedido) AS pedido,
        s.registro,
        s.estado,
        s.codsede,
        s.codzona,
        s.nomconsultorio,
        s.fechapedido,        
        time(s.fechapedido) AS horapedido,
        s.codpaciente,
        (select concat(p.nompaciente,' ',p.apellidopaterno,' ',p.apellidomaterno) from tbl_paciente p where p.codpaciente=s.codpaciente ) as nompaciente,
        s.idhc,
        (select hb.tipohc from tbl_hc hb where  hb.codpaciente=s.codpaciente and hb.sede=s.codsede limit 1 ) as tipohc
        FROM tbl_solicitudes s
        WHERE s.estado in (0)
        AND (s.idhc IS NOT NULL OR s.idhc != 0)
        AND STR_TO_DATE( CONVERT(s.fechapedido,char) ,'%e/%c/%Y') = curdate() ".$filtro."
        ORDER BY STR_TO_DATE( CONVERT(s.fechapedido,char) ,'%e/%c/%Y') DESC limit 100";
        $rw = $conn->prepare($sql);
        $rw->execute();
        $rw = $rw->fetchAll();

        $rs = array();
        if (!empty($rw)) {
            foreach ($rw as $key => $v) {
                $rs[] = [
                    'codpaciente' => $v['codpaciente'],
                    'nompaciente' => $v['nompaciente'],
                    'codhistoria' => $v['idhc'],
                    'tipohc' => $v['tipohc'],
                    'caja' => $this->tblubicaciones_ajax($v['idhc'], $v['codsede'], 'caja'),
                    'folio' => $this->tblubicaciones_ajax($v['idhc'], $v['codsede'], ''),
                    'solicitud' => $v['solicitud'],
                    'fechapedido' => $v['fechapedido'],
                    'horapedido' => $v['horapedido'],
                    'referencia' => $this->tblareas_ajax($v['codzona']),
                    'idsolicitud' => $v['idsolicitud'],
                    'estado' => $v['estado'],
                    'codsede' => $v['codsede'],
                    'codzona' => $v['codzona'],
                    'nomconsultorio' => $v['nomconsultorio'],

                ];
            }
            return $rs;
        }
        return $rs;
    }
    private function tblareas_ajax($codzona)
    {
        $conn = $this->em->getConnection();
        $sql = "select GROUP_CONCAT(ar.nombre) as nombre FROM tbl_areas ar WHERE ar.codzona='" . $codzona . "' ";
        $rw = $conn->prepare($sql);
        $rw->execute();
        $rw = $rw->fetch();
        return empty($rw) ? "" : $rw['nombre'];
    }
    private function tblubicaciones_ajax($c, $s, $o)
    {
        $ub = $this->em->getRepository(TblUbicaciones::class)->findOneBy(array('codhistoria' => $c, 'sede' => $s));
        if (empty($ub)) {
            return '';
        }
        if ($o == 'caja') {
            return $ub->getCaja();
        } else {
            return $ub->getFolio();
        }
    }
    public function datedifference($diff)
    {
        $diff = (int) abs(time() - strtotime($diff));

        $MINUTE_IN_SECONDS = 60;
        $HOUR_IN_SECONDS = 60 * $MINUTE_IN_SECONDS; //3600
        $DAY_IN_SECONDS = 24 * $HOUR_IN_SECONDS; //86400
        $WEEK_IN_SECONDS = 7 * $DAY_IN_SECONDS; //604800
        $MONTH_IN_SECONDS = 30 * $DAY_IN_SECONDS; //18144000
        $YEAR_IN_SECONDS = 365 * $DAY_IN_SECONDS;

        $diffval = '';
        if ($diff <= $MINUTE_IN_SECONDS or $diff <= $HOUR_IN_SECONDS) {
            $mins = round($diff / $MINUTE_IN_SECONDS);
            if ($mins <= 1) {
                $mins = '1 Minuto';
            } else {
                $mins = $mins . ' Minutos';
            }
            $diffval = $mins;
        } elseif ($diff <= $DAY_IN_SECONDS && $diff >= $HOUR_IN_SECONDS) {
            $hours = round($diff / $HOUR_IN_SECONDS);
            if ($hours <= 1) {
                $hours = '1 hora';
            } else {
                $hours = $hours . ' horas';
            }
            $diffval = $hours;
        } elseif ($diff <= $WEEK_IN_SECONDS && $diff >= $DAY_IN_SECONDS) {
            $days = round($diff / $DAY_IN_SECONDS);
            if ($days <= 1) {
                $days = '1 Días';
            } else {
                $days = $days . ' Días';
            }
            $diffval = $days;
        } elseif ($diff <= $MONTH_IN_SECONDS && $diff >= $WEEK_IN_SECONDS) {
            $weeks = round($diff / $WEEK_IN_SECONDS);
            if ($weeks <= 1) {
                $weeks = '1 Semana';
            } else {
                $weeks = $weeks . ' Semanas';
            }
            $diffval = $weeks;
        } elseif ($diff <= $YEAR_IN_SECONDS && $diff >= $MONTH_IN_SECONDS) {
            $months = round($diff / $MONTH_IN_SECONDS);
            if ($months <= 1) {
                $months = '1 Meses';
            } else {
                $months = $months . ' Meses';
            }
            $diffval = $months;
        } elseif ($diff >= $YEAR_IN_SECONDS) {
            $years = round($diff / $YEAR_IN_SECONDS);
            if ($years <= 1) {
                $years = '1 Años';
            } else {
                $years = $years . ' Años';
            }
            $diffval = $years;
        }
        return $diffval;
    }

     ///////////////////////////////////////////

     public function referencia($codzona)
     {
         $conn = $this->em->getConnection();
         $sql = "select ar.nombre FROM tbl_areas ar WHERE ar.codzona='" . $codzona . "' limit 1";
         $rw = $conn->prepare($sql);
         $rw->execute();
         $rw = $rw->fetch();
         return empty($rw) ? "" : $rw['nombre'];
     }
 
     public function ubi_archivo($codhistoria, $codsede, $folio, $col)
     {
         $conn = $this->em->getConnection();
         if (empty($codhistoria)) {
             return 'n/a';
         }
         if ($codsede != '02') {
             $codsede = '01';
         }
         $sql = "SELECT ".$col." FROM tbl_ubicaciones WHERE codhistoria=".$codhistoria." and sede='".$codsede."' and folio=".$folio." limit 1";
         $q = $conn->prepare($sql);
         $q->execute();
         $rws = $q->fetch();
         return empty($rws) ? '' : $rws[$col];
     }
 
     public function nombre_paciente($codpaciente)
     {
         if (!empty($codpaciente)) {            
             $sql = "SELECT concat(nompaciente,' ',apellidopaterno,' ',apellidomaterno) as paciente from tbl_paciente p where p.codpaciente=".$codpaciente." limit 1";
             $p = $this->em->getConnection()->prepare($sql);
             $p->execute();
             $r = $p->fetch();
             return empty($r) ? "--" : $r['paciente'];
         } else {
             return "n/a";
         }
     }
     public function nombre_usuario($email)
     {
        $u = $this->em->getRepository(TblUsuarios::class)->findOneBy(array('email' => $email));
        return empty($u) ? 'n/a' : $u->getNombre() . ' ' . $u->getApellidos();
     }
     public function dato_usuario($id,$col)
     {
         $ubi = $this->em->getRepository(TblUsuarios::class)->findOneBy(array('idusuario' => (int) $id));
         $column = "get" . ucfirst($col);
         return empty($ubi) ? "n/a" : $ubi->$column();      
     }

    public function convertirfecha($st)
    {
        return date('d/m/Y  H:i A', $st); //strftime("%d de %B de %Y %H:%M", time());
    }
    public function tagstats($st)
    {
        switch ($st) {
            
            case 1:
                return '-1';
                break;
            case 2:
                return '-2';
                break;
            case 3:
                return '-3';
                break;
            case 4:
                return '-4';
                break;
            case 5:
                return '-5';
                break;
            case 6:
                return '-6';
                break;
            case 7:
                return '-7';
                break;
            case 8:
                return '-8';
                break;
            default:
                return '-0';
                break;
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
    public function ubicacion_actual($st)
    {
        switch ($st) {
            // case 1:
            //     return 'Archivo';
            //     break;
            // case 2:
            //     return 'Archivo';
            //     break;
            case 3:
                return 'Hacia consultorio';
                break;
            case 4:
                return 'En consultorio';
                break;
            case 5:
                return 'Hacia archivo';
                break;
            // case 6:
            //     return 'Archivo';
            //     break;
            case 7:
                return 'En caja';
                break;
            case 8:
                return 'Anulado';
                break;
            default:
                return 'Archivo';
                break;
        }
    }
    public function nombre_sede($st)
    {
        switch ($st) {
            case '01':
                return 'San Isidro';
                break;
            case '02':
                return 'La Molina';
                break;
            case '03':
                return 'San Isidro';
                break;
            case '04':
                return 'Torre Dr. Fleck';
                break;
            default:
                return 'Sin Sede';
                break;
        }
    }
    public function tipo_historia($st)
    {
        switch ($st) {
            case '1':
                return 'ACTIVA';
                break;
            case '2':
                return 'PASIVA';
                break;
            case '3':
                return 'FALLECIDO';
                break;
            case '4':
                return 'ANULADO';
                break;
            default:
                return 'SIN HISTORIA';
                break;
        }
    }
    public function tipo_pedido($st)
    {
        switch ($st) {
            case 'P':
                return 'PROGRAMADA';
                break;
            case 'N':
                return 'EXTRA PROGRAMADA';
                break;
            case 'I':
                return 'INTERCONSULTA';
                break;
            case 'A':
                return 'ANEXO / ADMINISTRATIVO';
                break;
            default:
                return 'SIN HISTORIA';
                break;
        }
    }
    public function origen_pedido($st)
    {
        switch ($st) {
            case '0':
                return 'DHS';
                break;
            default:
                return 'R-File';
                break;
        }
    }
    public function formato_folio($v)
    {
        if ($v=='') {
            return "SIN FOLIO";
        }
        $v = preg_replace("/[^0-9]/", "", $v);
        $v = str_pad($v, 3, '0', STR_PAD_LEFT);
        return $v;
    }
    public function nombre_de_dias($dia)
    {
        switch ($dia) {
            case 'Mon':return 'Lunes';
                break;
            case 'Tue':return 'Martes';
                break;
            case 'Wed':return 'Miercoles';
                break;
            case 'Thur':return 'Jueves';
                break;
            case 'Fri':return 'Viernes';
                break;
            case 'Sat':return 'Sabado';
                break;
            case 'Sun':return 'Domingo';
                break;
            default:return 'Lunes';
                break;
        }
    }
    

    public function nombre_de_meses($mes)
    {
        switch ($mes) {
            case '01':
                return 'Enero';
                break;
            case '02':
                return 'Febrero';
                break;
            case '03':
                return 'Marzo';
                break;
            case '04':
                return 'Abril';
                break;
            case '05':
                return 'Mayo';
                break;
            case '06':
                return 'Junio';
                break;
            case '07':
                return 'Julio';
                break;
            case '08':
                return 'Agosto';
                break;
            case '09':
                return 'Septiembre';
                break;
            case '10':
                return 'Octubre';
                break;
            case '11':
                return 'Noviembre';
                break;
            case '12':
                return 'Diciembre';
                break;
            default:
                return '';
                break;
        }
    }   
    public function formato_hc($number)
    {
        return str_pad($number, 7, '0', STR_PAD_LEFT);
    }
    public function contadorderegistro($op)
    {
        switch ($op) {
            case 'agendadehoy':
                //$sql = "SELECT count(*) AS total FROM tbl_solicitudes WHERE STR_TO_DATE( CONVERT(fechapedido,char) ,'%c/%e/%Y') = curdate()";
                $sql = "SELECT count(*) AS total FROM tbl_solicitudes WHERE date (fechapedido) = curdate() AND idhc != 0"; //estado IN (0) AND
                $p = $this->em->getConnection()->prepare($sql);
                $p->execute();
                $total = $p->fetch();
                return $total['total'];
                break;
            case 'agendahoyadelante':
                // $sql = "SELECT count(*) AS total FROM tbl_solicitudes WHERE STR_TO_DATE( CONVERT(fechapedido,char) ,'%c/%e/%Y') > curdate()";
                $sql = "SELECT count(*) AS total FROM tbl_solicitudes WHERE date (fechapedido) > curdate()";
                $p = $this->em->getConnection()->prepare($sql);
                $p->execute();
                $total = $p->fetch();
                return $total['total'];
                break;
            case 'pacientes':
                $sql = "SELECT count(*) AS total FROM tbl_paciente";
                $p = $this->em->getConnection()->prepare($sql);
                $p->execute();
                $total = $p->fetch();
                return $total['total'];
                break;
            case 'total':
                $sql = "SELECT count(*) AS total FROM tbl_solicitudes";
                $p = $this->em->getConnection()->prepare($sql);
                $p->execute();
                $total = $p->fetch();
                return $total['total'];
                break;
            case 'pendientes':
                $sql = "SELECT count(*) AS total FROM tbl_solicitudes WHERE estado in (0) AND DATE(fechapedido) = curdate() AND idhc != 0";
                $p = $this->em->getConnection()->prepare($sql);
                $p->execute();
                $total = $p->fetch();
                return $total['total'];
                break;
            case 'activos':
                $sql = "SELECT count(*) AS total FROM tbl_solicitudes WHERE estado in (1,2,3) AND idhc != 0";
                $p = $this->em->getConnection()->prepare($sql);
                $p->execute();
                $total = $p->fetch();
                return $total['total'];
                break;
            default:
                break;
        }
    }
}
