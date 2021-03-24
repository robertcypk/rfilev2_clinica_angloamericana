<?php
namespace App\Controller;

use App\Entity\TblHc;
use App\Entity\TblPaciente;
use App\Entity\TblUbicaciones;
use Doctrine\ORM\EntityManagerInterface;
use \Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use \Mike42\Escpos\Printer;

class Printerpos
{
    private $em;
    private $folio;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function boucher($user, $solicitud, $folio)
    {
        $this->folio = $folio;
        /*
        h.codhistoria,
        h.sede,
        and h.codpaciente=s.codpaciente
         */
        
        $slt = $this->em->getConnection()->prepare("SELECT
                s.idsolicitud,
                s.codsede,
                s.codpaciente,
                concat(p.nompaciente,' ',p.apellidopaterno,' ',p.apellidomaterno) as paciente,
                p.nompaciente,
                p.apellidopaterno,
                p.apellidomaterno,        
                s.idcita as cita,
                s.codzona as area,
                s.codmedico,
                s.nommedico as medico,
                s.codconsultorio,
                s.nomconsultorio,
                s.observaciones as observaciones,
                s.fechapedido,        
                TIME(s.fechapedido) as horapedido,
                s.estado,
                s.reqplaca,
                s.codtipopedido,
                s.idhc,
                s.folio,
                s.responsablec,
                u.caja,
                u.estado
                from tbl_paciente p, tbl_solicitudes s, tbl_ubicaciones u
                where p.codpaciente=s.codpaciente 
                and s.anulado='0'         
                and (s.idhc IS NOT NULL OR s.idhc != 0)
                and u.codhistoria = s.idhc
                and u.sede = if(s.codsede = '02', s.codsede, '01')
                and u.folio = s.folio
                and s.idsolicitud=? ");
        $slt->bindValue(1, $solicitud);
        $slt->execute();
        $slt = $slt->fetch();
       
        //$connector = new WindowsPrintConnector("smb://172.25.52.67/RICOH-Test");
        if (!empty($slt)) {
            
            
            try {
                $cn = "smb://" . $this->slcImpresora($slt['codsede']);
                // BARCODE_UPCA,BARCODE_UPCE,BARCODE_JAN13,BARCODE_JAN8,BARCODE_CODE39,BARCODE_ITF,BARCODE_CODABAR
                $connector = new WindowsPrintConnector($cn);
               // $connector = new WindowsPrintConnector("smb://192.168.0.11/RFILE-Printer1");
                $printer = new Printer($connector);

                $printer->setEmphasis(true);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Solicitud de Historia Clinica\n");
                $printer->setEmphasis(false);
                $printer->setUnderline(1);
                $printer->text("Hora de impresión:" . date("d/m/Y") . " " . date("h:i A") . "\n");
                $printer->setUnderline(0);

                /* Barcodes - see barcode.php for more detail */
                $printer->setBarcodeHeight(70);
                $printer->setBarcodeWidth(3);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
                $printer->barcode('{ARF2-' . $slt['idsolicitud'], Printer::BARCODE_CODE128);
                $printer->feed(1);

                // substr($slt['fechapedido'], 0, 30).'...')
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text(str_pad("F. Cita", 13) . ": ");
                $printer->setEmphasis(true);
                $printer->text(date("d/m/Y", strtotime($slt['horapedido'])). " - " . date("h:i A", strtotime($slt['horapedido'])) . "\n");
                $printer->setEmphasis(false);
                $printer->text(str_pad("Sede", 13) . ": ");
                $printer->setEmphasis(true);
                $printer->text($this->psede($slt['codsede']) . "\n");
                $printer->setEmphasis(false);
                $printer->text(str_pad("Tipo", 13) . ": ");
                $printer->setEmphasis(true);
                $printer->text($this->tipoSol($slt['codtipopedido']) . "\n");
                $printer->setEmphasis(false);
                $printer->text(str_pad("Solicitante", 13) . ": ");
                $printer->setEmphasis(true);
                $printer->text($slt['responsablec'] . "\n");
                $printer->setEmphasis(false);
                $printer->feed();
                if ($slt['codtipopedido'] == "I") {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text(str_pad(" UBICACIÓN ACTUAL EN CLINICA ", 45, "=", STR_PAD_BOTH) . "\n");
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->feed();
                    $printer->text(str_pad("HC", 13) . ": ");
                    $printer->setEmphasis(true);
                    $printer->text(str_pad($slt['idhc'], 7, '0', STR_PAD_LEFT). "\n");
                    $printer->setEmphasis(false);
                    $printer->text(str_pad("Referencia", 13) . ": ");
                    $printer->text($this->fichaUbicacionesHistoricas($slt['idhc']) . "\n");
                    $printer->feed();
                } else {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text(str_pad(" UBICACIÓN EN ARCHIVO ", 45, "=", STR_PAD_BOTH) . "\n");
                    $printer->setJustification(Printer::JUSTIFY_LEFT);
                    $printer->feed();
                    $printer->text(str_pad("HC", 13) . ": ");
                    $printer->setEmphasis(true);
                    $printer->text(str_pad($slt['idhc'], 7, '0', STR_PAD_LEFT). "\n");
                    $printer->setEmphasis(false);

                    $printer->text(str_pad("Tipo", 13) . ": ");                 
                    $printer->text($this->tipo_historia($slt['estado']). "\n");
                    $printer->text(str_pad("Caja", 13) . ": ");
                    $printer->text($slt['caja'] . "\n");
                    $printer->text(str_pad("Folio", 13) . ": ");
                   
                    $printer->text(str_pad($slt['folio'], 3, "0", STR_PAD_LEFT) . "\n");
                    $placa = ($slt['reqplaca'] == 1) ? 'SI' : 'NO';
                    $printer->text(str_pad("Placa", 13) . ": ");
                    $printer->text($placa . "\n");
                    $printer->feed();
                }
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text(str_pad(" ZONA DE ENTREGA ", 45, "=", STR_PAD_BOTH) . "\n");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->feed();

                $printer->text(str_pad("Zona", 13) . ": ");
                $printer->text($slt['area'] . "\n");
                $printer->text(str_pad("Referencia", 13) . ": ");
                //$printer->text($this->psede($this->tblareas($slt['area'], $slt['codsede'], "sede")) . " - " . $this->referencia($slt['area']) . "\n");
                $printer->text($this->psede($slt['codsede']) . " - " . $this->referencia($slt['area']) . "\n");
                // $printer->text(str_pad("Consultorio", 13) . ": ");
                // $printer->text($slt['codconsultorio'] . ' - ' . $slt['nomconsultorio'] . "\n");
                $printer->text(str_pad("Cod. Paciente", 13) . ": ");
                $printer->text($slt['codpaciente'] . "\n");
                $printer->text(str_pad("Nom. Paciente", 13) . ": ");                
                //$printer->text(substr($slt['paciente'], 0, 33) . "\n");
                $printer->text(substr($slt['apellidopaterno']." ".$slt['apellidomaterno'] ." ".$slt['nompaciente'] , 0, 33) . "\n");
                $printer->text(str_pad("Cod. Doctor", 13) . ": ");
                $printer->text($slt['codmedico'] . "\n");
                $printer->text(str_pad("Nom. Doctor", 13) . ": ");
                $printer->text($slt['medico'] . "\n");
                $printer->feed();
                /* Printer shutdown */
                $printer->cut();
                $printer->close();
                $fichero = __DIR__ . "/../../public/logs/InsertarPedido-WS-Ticket.txt";           
                file_put_contents($fichero, PHP_EOL . $fechapedido ." >> ".$idsolicitud . " \r\n", FILE_APPEND);                 

            } catch (\Exception $e) {
               // file_put_contents($fichero, (date('d/m/Y H:m:s').'-'.$e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine()) . "\n", FILE_APPEND);
                return 0;
            }
            return 1;
           
        }
        // $fichero = __DIR__ . "/../../public/Printerpos-NoImpresos.txt";
        // file_put_contents($fichero,date('d/m/Y H:m:s').'-'.$solicitud. "\n", FILE_APPEND);
        return 0;
    }
    /* */
    public function sltmdc($codmedico)
    {
        $sql = "SELECT * FROM tbl_medicos WHERE idmedico='".$codmedico."'";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->execute();
        $slt = $slt->fetch();
        return empty($slt) ? "n/a" : $slt['medico'];
    }
    public function slcImpresora($sede = '01')
    {
        $dir = __DIR__ . "/../../config/Printer/printer.json";
        if (!file_exists($dir)) {
            return "172.25.52.67/RFile-printer1";
        }
        $json = file_get_contents($dir, false);
        $filtro = json_decode($json, true);
        return $filtro[$sede];
    }
    
    private function tblpaciente($codpaciente, $col)
    {
        if (empty($col)) {
            return "";
        }
        $rs = $this->em->getRepository(TblPaciente::class)->findOneBy(array('codpaciente' => (int) $codpaciente));
        return empty($rs) ? "n/a" : $rs->$col();
    }
    private function tblareas($id, $codsede, $col)
    {
        if (empty($col)) {
            return "";
        }
        // $rs = $this->em->getRepository(\App\Entity\TblAreas::class)->findOneBy(array('codzona' => (int) $id, 'sede' => $codsede));
        $slt = $this->em->getConnection()->prepare("SELECT * FROM tbl_areas WHERE codzona='".$id."' and sede='".$codsede."'");
        $slt->bindValue(1, $id);
        $slt->bindValue(2, $codsede);
        $slt->execute();
        $slt = $slt->fetch();
        return empty($slt) ? "n/a" : $slt[$col];
    }
    private function referencia($zona)
    {
        if (empty($zona)) {
            return "";
        }
        // $rs = $this->em->getRepository(\App\Entity\TblAreas::class)->findOneBy(array('codzona' => (int) $id, 'sede' => $codsede));
        $slt = $this->em->getConnection()->prepare("SELECT * FROM tbl_areas WHERE codzona='".$zona."'");
       // $slt->bindValue(1, $id);
        //$slt->bindValue(2, $codsede);
        $slt->execute();
        $slt = $slt->fetch();
        return empty($slt) ? "n/a" : $slt['nombre'];
    }
    private function tblhcc($id, $codsede, $col)
    {
        if (empty($col)) {
            return "";
        }
        $hc = $this->em->getRepository(TblHc::class)->findOneBy(array('codpaciente' => (int) $id, 'sede' => $codsede));
        return empty($hc) ? "n/a" : $hc->$col();
    }
    private function outputtextc($v1, $v2, $sp, $t)
    {
        if ($t == 'c') {
            if ($sp > 20) {
                $sp2 = $sp * 2;
                if ($sp2 > 50) {
                    $sp = $sp2 - 50;
                } else {
                    $sp = $sp2;
                }
            } else {
                $sp = 50;
            }
        }
        return sprintf('%30.30s %-8s', $v1, $v2);
        //return str_pad($v1, $sp).$v2;
    }
    private function fichaUbicacionesHistoricas($cod)
    {
        //$rs = $this->em->getRepository(\App\Entity\TblHistorial::class)->findOneBy(array('idhc' => (int) $cod,'estatus'=>4));
        $sql = "SELECT distinct
        a.codzona,
        a.nombre,
        a.tipo,
        fecha
        from
        tbl_areas a,
        tbl_historial h
        where a.idarea=h.ubicacion and h.idhc=? and h.estatus=4 order by fecha desc limit 1";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->bindValue(1, $cod);
        $slt->execute();
        $slt = $slt->fetch();
        if (!empty($slt)) {
            return $slt['codzona'] . " - " . $slt['nombre'];
        } else {
            return 'n/a';
        }
    }
    private function fichaUbicaciones($hc, $sede,$folio, $colm)
    {
        //$rs = $this->em->getRepository(TblUbicaciones::class)->findOneBy(array('codhistoria' => $cod, 'sede' => $sede));
        $rt = "n/a";
        $sql ="SELECT * FROM tbl_ubicaciones WHERE codhistoria='".$hc."' AND sede='".$sede."' AND folio='".$folio."'";
        $slt = $this->em->getConnection()->prepare($sql);
       // $slt->bindValue(1, $hc);
        $slt->execute();
        $rsl = $slt->fetch();
        return $rsl['caja'];

        /*switch ($colm) {
            case 'tipo':
                if (!empty($slt_rs)) {
                    if ($slt_rs['estado'] == 2) {
                        $rt = 'Pasivo';
                    }
                }
                return 'Activo';
                break;
            case 'ubicacion':
                if (!empty($slt_rs)) {
                    $rt = 'Caja Nro. ' . $slt_rs['caja'];
                }
                break;
            case 'folio':
                if (!empty($slt_rs)) {
                    $rt = $slt_rs['folio'];
                }
                break;
        }
        // $fichero = __DIR__ . "/../../public/PrinterPos-fichaUbicaciones.txt";
        // file_put_contents($fichero, '>'.$cod. $sede.$rt . "\n", FILE_APPEND);
        return $rt;*/
    }

    // FUNCTION SERVICE GLOBAL 
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

    private function tipoSol($v)
    {        
        switch ($v) {
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
                return 'ANEXO DE ARCHIVOS /ADMINISTRATIVO';
                break;
        }
    }
    private function tipo_historia($st)
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
    private function psede($v)
    {
        switch ($v) {
            case '01':
                return 'SAN ISIDRO';
                break;
            case '02':
                return 'LA MOLINA';
                break;
            case '03':
                return 'SAN ISIDRO';
                break;
            case '04':
                return 'TORRE DR.FLECK';
                break;
        }
    }
    private function psestado($v)
    {
        switch ($v) {
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
}
