<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use \Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use \Mike42\Escpos\Printer;
use App\Controller\Toolclass;

class Printstickerpos
{
    private $em;
    private $tool;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->tool = new Toolclass();
    }
    public function slcImpresora($sede = '01')
    {
        $dir = __DIR__ . "/../../config/Printer/printer.json";
        if (!file_exists($dir)) {
            return "172.25.52.67/RFile-printer2";
        }
        $json = file_get_contents($dir, false);
        $filtro = json_decode($json, true);
        return $filtro[$sede];
    }
    public function imprimirfolder($codhistoria, $codpaciente, $sede, $folio, $caja, $print)
    {
        
        $sql = "SELECT
        distinct
        p.nompaciente AS nompaciente,        
		ifnull(p.apellidopaterno,' ') as appaterno, 
		ifnull(p.apellidomaterno,' ') as apmaterno, 
        p.fechanacimiento,
        p.numdocumento AS documento,
        p.codpaciente
        FROM
        tbl_paciente p
        WHERE
        p.codpaciente=? ";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->bindValue(1, $codpaciente);
        $slt->execute();
        $slt = $slt->fetch();
        $codhistoria = $this->tool->fhc($codhistoria);
        if (empty($codhistoria)) {
            return "No se encontro Historia Clinica : " . $codpaciente;
        }
        if (!empty($slt)) {
            try {
                $ffolio = $this->tool->ffolio($folio);                
                $codebar_suba = substr($codhistoria, 0, -1);
                $codebar_subb = substr($codhistoria, -1);
                $codebar_subc = $codebar_suba.'>6'.$codebar_subb .'-'. $ffolio;
				$ZPL="^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR6,6~SD15^JUS^LRN^CI0^XZ";
				$ZPL.="^XA";
				$ZPL.="^MMT";
				$ZPL.="^PW607";
				$ZPL.="^LL0304";
				$ZPL.="^LS0";
				$ZPL.="^BY3,3,67^FT11,241^BCN,,N,N";
				$ZPL.="^FD>:HC>5".$codebar_subc."^FS";
				$ZPL.="^FT11,161^A0N,34,33^FH\^FDN: ".$slt['fechanacimiento'] . "^FS";
				$ZPL.="^FT227,161^A0N,34,33^FH\^FDDoc: ".$slt['documento'] . "^FS";
				$ZPL.="^FT592,299^A0B,51,50^FH\^FDC: ".$caja."^FS";
				$ZPL.="^FT11,113^A0N,39,38^FH\^FD".$this->SpecialText($slt['nompaciente'])."^FS";
				$ZPL.="^FT11,57^A0N,39,38^FH\^FD".$this->SpecialText($slt['appaterno']).' '.$this->SpecialText($slt['apmaterno'])."^FS";
				$ZPL.="^FT202,288^A0N,45,45^FH\^FDHC".$codhistoria."-". $ffolio."^FS";
				$ZPL.="^PQ1,0,1,Y^XZ";
				
                $fichero =  __DIR__. "/../../public/zplfolder/".date('d-m-Y')."-HC".$codhistoria."-".$ffolio."_".$caja.".zpl";
                file_put_contents($fichero, $ZPL);
                $cmd = \exec(" lp -d ".$print." -o raw ".$fichero);
                return "Imprimiendo : " . $codhistoria;
            } catch (\Exception $e) {
                return $e->getMessage()."\n".$e->getFile()."\n".$e->getLine();
            }
        } else {
            return "No se encontro Historia Clinica : " . $codpaciente;
        }
    }
    public function imprimirDocumentos($codhistoria, $codpaciente, $sede, $folio, $caja, $tipoarch, $print)
    {
        
        date_default_timezone_set("America/Lima");
        $sql = "SELECT
        distinct
        p.nompaciente AS nompaciente,
        ifnull(p.apellidopaterno,' ') as appaterno, 
		ifnull(p.apellidomaterno,' ') as apmaterno, 
        p.fechanacimiento,
        p.numdocumento AS documento,
        p.codpaciente
        FROM
        tbl_paciente p
        WHERE
        p.codpaciente=? ";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->bindValue(1, $codpaciente);
        $slt->execute();
        $slt = $slt->fetch();
        $codhistoria = $this->tool->fhc($codhistoria);
        if (empty($codhistoria)) {
            return "No se encontro Historia Clinica : " . $codpaciente;
        }
        if (!empty($slt)) {
            try {
                // $folio = $this->tool->ffolio($this->historialactividades($codhistoria, 'folio'));
                $ffolio = $this->tool->ffolio($folio);  
                $codebar_suba = substr($codhistoria, 0, -1);
                $codebar_subb = substr($codhistoria, -1);
                $codebar_subc = $codebar_suba.'>6'.$codebar_subb .'-'. $ffolio;
                $ZPL ="^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR6,6~SD15^JUS^LRN^CI0^XZ";
				$ZPL.="^XA";
				$ZPL.="^MMT";
				$ZPL.="^PW408";
				$ZPL.="^LL0208";
				$ZPL.="^LS0";
				$ZPL.="^FO0,96^GFA,00384,00384,00004,:Z64:";
				$ZPL.="eJxjYCATyP9DxQwHMDGGGhhoYGBg/sDAwP6DgYH/DwMDnw0DgwwfA4MEGxoGisnJQdSAMEg94wOIERIsWNRDMcgsORmoHjR9IMCPy11MQLVA/fx9QPF5QAzUKwHUKwF0qwRQv4QDEGPzO/kAAOKaKWg=:256B";
				$ZPL.="^BY2,3,37^FT39,177^BCN,,N,N";
				$ZPL.="^FD>:HC>5".$codebar_subc."^FS";
				$ZPL.="^FT39,131^A0N,23,24^FH\^FD".$this->SpecialText($slt['nompaciente'])."^FS";
				$ZPL.="^FT39,102^A0N,23,24^FH\^FD".$this->SpecialText($slt['appaterno']).' '.$this->SpecialText($slt['apmaterno'])."^FS";
                $ZPL.="^FT402,195^A0B,23,24^FH\^FDC: ".$caja."^FS";
				$ZPL.="^FT11,73^A0N,28,28^FH\^FDU: ".$this->psede($sede)."-".$this->historialactividades($codhistoria, 'ubih')."^FS";
				$ZPL.="^FT11,38^A0N,28,28^FH\^FDT: ".$this->SpecialText($this->tipoDocumentos($tipoarch))."^FS";
				$ZPL.="^FT121,200^A0N,23,24^FB170,1,0,C^FH\^FDHC".$codhistoria."-". $ffolio."^FS";
				$ZPL.="^PQ1,0,1,Y^XZ";	
                $fichero =  __DIR__. "/../../public/zplfolder/".date('d-m-Y')."-tipo".$codhistoria."-".$ffolio."-".$tipoarch.".zpl";
                file_put_contents($fichero, $ZPL);
                $cmd = \exec(" lp -d ".$print." -o raw ".$fichero);
                return "Imprimiendo : " . $codhistoria;
            } catch (\Exception $e) {
                return $e->getMessage()."\n".$e->getFile()."\n".$e->getLine();
            }
        } else {
            return "No se encontro Historia Clinica : " . $codpaciente;
        }
    }
    private function tipoDocumentos($tipoarch)
    {
        $docs = array(  0=>'Sin documentos',
                        1=>'Hojas de urgencias',
                        2=>'Resultados de laboratorio',
                        3=>'Rayos x',
                        4=>'Tomografía',
                        5=>'Ecografía',
                        6=>'Mamografía',
                        7=>'Resonancia magnética',
                        8=>'Densitometría',
                        9=>'Medicina nuclear',
                        10=>'Endoscopias',
                        11=>'Oftalmológicas',
                        12=>'Electrocardiograma',
                        13=>'Prueba ergo métrica graduada',
                        14=>'Electrocardiografía ambulatoria',
                        15=>'Ecocardiografía doppler a color',
                        16=>'Mamografía',
                        17=>'Holter'
                    );
        return $docs[$tipoarch];
    }
    private function gethc($codpaciente)
    {
        $sql = "SELECT distinct idhc FROM tbl_solicitudes where codpaciente='".$codpaciente."' AND idhc is not null";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->execute();
        $slt = $slt->fetch();
        if (!empty($slt)) {
            return $slt['idhc'];
        } else {
            return '';
        }
    }
    
    private function tblubicaciones($codhistoria, $sede, $folio, $col)
    {
        //$ubicaciones = $this->em->getRepository(\App\Entity\TblUbicaciones::class)->findOneBy(array('codhistoria'=>$codhistoria));
        $sql = "SELECT * FROM tbl_ubicaciones where codhistoria='".$codhistoria."' AND sede='".$sede."' AND folio='".$folio."'";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->execute();
        $slt = $slt->fetch();
        return empty($slt) ? '' : $slt[$col];        
    }
    private function getUser($user)
    {
        $sql = "SELECT distinct email FROM tbl_usuarios where idusuario='".$user."'";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->execute();
        $slt = $slt->fetch();
        if (!empty($slt)) {
            return $slt['email'];
        } else {
            return '';
        }
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
                return 'ANEXO / ADMINISTRATIVO';
                break;
        }
    }
    private function psede($v)
    {
        switch ($v) {
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
        }
    }
    private function historialactividades($codhistoria, $col)
    {
        $sql="SELECT 
        s.idsolicitud,
        his.usuario,
        his.ubicacion,
        ar.nombre as ubih,
        s.folio,
        ar.codsede,
        ar.codzona,
        his.estatus,
        his.fecha
        FROM tbl_solicitudes s, tbl_historial his, tbl_areas ar
        WHERE ar.idarea=his.ubicacion 
        AND  s.idsolicitud=his.idsolicitud
        AND his.idhc='".$codhistoria."'
        ORDER BY his.fecha asc
        limit 1";
        $slt = $this->em->getConnection()->prepare($sql);
        $slt->execute();
        $slt = $slt->fetch();
        switch ($col) {
            case 'ubih':
                if (!empty($slt)) {
                    if ($slt['estatus'] == 4) {
                        return $slt['ubih'];
                    } else {
                        return $this->tool->psestados($slt['estatus']);
                    }
                } else {
                    return 'Archivado';
                }
                break;
            case 'usuario':
                return empty($slt) ? 'n/a' : $slt['usuario'];
                break;
            case 'fecha':
                return empty($slt) ? 'n/a' : date('d/m/Y h:i:s A', $slt['fecha']);
                break;
            case 'folio':
                return empty($slt) ? 0 : $slt['folio'];
                break;
            default:
                return empty($slt) ? 'n/a' : $slt[$col];
                break;
        }
    }
    private function setZero($v)
    {
        return empty($v) ? '000' : $v;
    }
    private function nomusr($v)
    {
        $usr = $this->em->getRepository(\App\Entity\TblUsuarios::class)->findOneBy(array('email'=>$v));
        return empty($usr) ? "Desconocido" : $usr->getNombre()." ".$usr->getApellidos();
    }
    public function stickerIndividual($codebar = '0', $print)
    {
        if (empty($codebar) && $codebar != 0){
            return "Ingrese el codigo de usuario para impresíón";
        }
        $email = $this->getUser($codebar);
        
        try {
            $codebar = str_pad($codebar, 7, "0", STR_PAD_LEFT);
            $codebar_suba = substr($codebar, 0, -1);
            $codebar_subb = substr($codebar, -1);
            $codebar_subc = $codebar_suba.'>6'.$codebar_subb;
            $ZPL="^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR6,6~SD15^JUS^LRN^CI0^XZ";
			$ZPL.="^XA";
			$ZPL.="^MMT";
			$ZPL.="^PW408";
			$ZPL.="^LL0208";
			$ZPL.="^LS0";
			$ZPL.="^FO0,96^GFA,00384,00384,00004,:Z64:";
			$ZPL.="eJxjYCATyP9DxQwHMDGGGhhoYGBg/sDAwP6DgYH/DwMDnw0DgwwfA4MEGxoGisnJQdSAMEg94wOIERIsWNRDMcgsORmoHjR9IMCPy11MQLVA/fx9QPF5QAzUKwHUKwF0qwRQv4QDEGPzO/kAAOKaKWg=:256B";
			$ZPL.="^BY2,3,67^FT66,147^BCN,,N,N";
			$ZPL.="^FD>:RF3->5".$codebar_subc."^FS";
            $ZPL.="^FT4,53^A0N,28,28^FH\^FD". $email."^FS";
			$ZPL.="^FT113,179^A0N,34,33^FB196,1,0,C^FH\^FDRF3-".$codebar."^FS";
			$ZPL.="^PQ1,0,1,Y^XZ";
			
            $fichero =  __DIR__. '/../../public/zplfolder/'.date('d-m-Y').'-user'.$codebar.'.zpl';
            file_put_contents($fichero, $ZPL);
            $cmd = \exec(" lp -d ".$print." -o raw ".$fichero);
            return 1;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function stickerMultiple($request)
    {
        $tipo = $request->get("tipo");
        $sticker = $request->get("stickers");
        $tipoarch= $request->get("tipoarch");
        $print = $request->get("printer");
        if (!empty($sticker) and !empty($tipo)) {
            if ($tipo == 1) {
                // imprimir folder o carpetas
                $rs = "";
                foreach ($sticker as $key => $v) {
                    $rs .= $this->imprimirfolder($v['id'], $v['pa'], $v['sede'], $v['folio'], $v['caja'], $print) . "\n";
                }
                return $rs;
            } elseif ($tipo == 2) {
                // imprimir documentos entregados
                $rs = "";
                foreach ($sticker as $key => $v) {
                    $rs .= $this->imprimirDocumentos($v['id'], $v['pa'], $v['sede'], $v['folio'], $v['caja'], $tipoarch, $print) . "\n";
                }
                return $rs;
            } else {
                return "Sin tareas";
            }
        } else {
            return "Seleccione Tipo de archivo e ingrese los datos de los pacientes a imprimir";
        }
    }
    public function SpecialText($texto)
    {
        $vectorUi = array('Á','É','Í','Ó','Ú','Ñ');
        $vectorUo = array('\B5','\90','\D6','\E3','\E9','\A5');
        $vectorLi = array('á','é','í','ó','ú','ñ');
        $vectorLo = array('\A0','\82','\A1','\A2','\A3','\A4');
        for ($x = 0; $x < 6; $x++) {
            $texto = str_replace($vectorUi[$x], $vectorUo[$x], $texto);
        }
        for ($x = 0; $x < 6; $x++) {
            $texto = str_replace($vectorLi[$x], $vectorLo[$x], $texto);
        }
        return $texto;
    }
}
