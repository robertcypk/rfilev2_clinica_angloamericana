<?php
namespace App\Controller;

use App\Entity\TblPaciente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/reportes")
 */
class ReportesController extends Controller
{
    /**
     * @Route("/{pagina}", name="reportes", defaults={"pagina"="0"})
     */
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $maxrs = 50;
        $total = 0;
        $paginador = "";
        $em = $this->getDoctrine()->getManager();
        $pagina = ($request->get("pagina") != 0) ? $request->get("pagina") : 0;
        $offset = ($request->get("pagina") != 0) ? ($request->get("pagina") - 1) * $maxrs : 0;

        //" . $offset . "," .
        $limit = " LIMIT " . $maxrs . "";
        $filtro = "  ";

        $rw = [];

        if (!empty($buscar)) {
            $imp = "";
            $codhistoria = preg_replace("/[^0-9]/", "", $buscar);
            // $codhistoria = str_pad($codhistoria, 7, '0', STR_PAD_LEFT);
            $hcp = $em->getConnection()->prepare("SELECT hc.codpaciente FROM tbl_solicitudes hc where  hc.idhc='" . $codhistoria . "'");
            $hcp->execute();
            $imp = $hcp->fetchAll();
            if (!empty($imp)) {
                $arr = [];
                foreach ($imp as $k => $v) {
                    $arr[] = $v['codpaciente'];
                }
                $sql = "SELECT
            pa.codpaciente,
            so.registro,
            concat(pa.nompaciente,' ',pa.apellidopaterno,' ',pa.apellidomaterno) AS nombresapellidos,
            pa.codtipodocumento AS tipodocumento,
            pa.numdocumento AS documento,
            pa.registro,
            so.idsolicitud,
            so.codsede,
            so.codmedico,
            so.nommedico,
            so.codzona,
            so.codconsultorio,
            so.nomconsultorio,           
            (select group_concat(ubic.estado) from tbl_ubicaciones ubic where ubic.codhistoria=so.idhc) as estadoubi,
            so.fechapedido,           
            so.estado,
            (select GROUP_CONCAT(ar.nombre) FROM tbl_areas ar WHERE ar.codzona=so.codzona AND ar.sede=so.codsede) AS referencia
            FROM tbl_paciente pa, tbl_solicitudes so WHERE so.codpaciente=pa.codpaciente and  pa.codpaciente in ( " . implode(",", $arr) . ") order by idsolicitud desc limit 50 ";
                //listado
                $q = $em->getConnection()->prepare($sql);
                $q->execute();
                $rw = $q->fetchAll();
            }
        }

        return $this->render('reportes/index.html.twig', [
            'solicitudes' => $rw,
            'codhistoria' => $buscar,
            'nombrepaciente' => $this->nombrepaciente($buscar),
            'responsables' => $this->responsables(),
        ]);
    }

    private function responsables()
    {
        $responsables = $this->getDoctrine()->getRepository(\App\Entity\TblUsuarios::class)->findBy(array('rol' => 'ROLE_MENSAJERO'));
        return $responsables;
    }

    private function nombrepaciente($codhistoria)
    {
        $em = $this->getDoctrine()->getManager();
        $codhistoria = preg_replace("/[^0-9]/", "", $codhistoria);
        $codhistoria = str_pad($codhistoria, 7, '0', STR_PAD_LEFT);

        $sqla = "SELECT distinct  so.codpaciente
        FROM tbl_solicitudes so
        WHERE so.idhc='".$codhistoria."' limit 1";

        $q = $em->getConnection()->prepare($sqla);
        $q->execute();
        $rwa = $q->fetch();

        if (empty($rwa)) {
            return "";
        }

        $sql = "SELECT  concat(p.nompaciente,' ',p.apellidopaterno,' ',p.apellidomaterno) AS nombre 
                FROM  tbl_paciente p
                WHERE p.codpaciente='" . $rwa['codpaciente'] . "'";
        $q = $em->getConnection()->prepare($sql);
        $q->execute();
        $rw = $q->fetch();
        return empty($rw) ? '' : $rw['nombre'];
    }

    /**
     * @Route("/usuarios/{pagina}", name="reportesusuarios")
     */
    public function reportesusuarios(Request $request)
    {
        $session = new Session();
        $buscar = $request->get('buscar');
        $maxrs = 100;
        $total = 0;
        $paginador = "";
        $em = $this->getDoctrine()->getManager();
        $pagina = ($request->get("pagina") != 0) ? $request->get("pagina") : 0;
        $offset = ($request->get("pagina") != 0) ? ($request->get("pagina") - 1) * $maxrs : 0;
        $limit = " LIMIT " . $offset . "," . $maxrs . "";
        $filtro = "  ";
        $rw_rs = [];
        if (!empty($buscar)) {
            if (!empty($session->get('buscar'))) {
                $buscar = $session->get('buscar');
            } else {
                $session->set('buscar', $buscar);
            }

            $filtro .= " WHERE usuario ='" . $buscar . "' ";
            $sql = "SELECT *  FROM tbl_historial ";
            $totalRs = $em->getConnection()->prepare($sql . $filtro);
            $totalRs->execute();
            $totalRs = $totalRs->fetchAll();
            $total = count($totalRs);
            //listado
            $q = $em->getConnection()->prepare($sql . $filtro . $limit);
            $q->execute();
            $rw = $q->fetchAll();
            if (!empty($rw)) {
                foreach ($rw as $k => $v) {
                    $rw_rs[] = [
                        'codsede' => $this->detallesolicitud($v['idsolicitud'], 'codsede'),
                        'idsolicitud' => $v['idsolicitud'],
                        'ubicacion' => $v['ubicacion'],
                        'fechapedido' => $this->detallesolicitud($v['idsolicitud'], 'fechapedido'),                        
                        'estado' => $this->detallesolicitud($v['idsolicitud'], 'estado')
                    ];
                }
            }
        }
        

        $providers_pag = new \App\Controller\Genpaginator;
        $paginador = $providers_pag->generatePaging($total, $maxrs, $pagina, $request->getUri());
        return $this->render('reportes/usuarios.html.twig', [
            'solicitudes' => $rw_rs,
            'total' => $total,
            'paginador' => $paginador,
            'buscar' => $buscar,
        ]);
    }
    public function detallesolicitud($id, $col="")
    {
        $em = $this->getDoctrine()->getManager();
        $sql = "SELECT codsede,
                nomconsultorio,
                fechapedido
                estado FROM tbl_solicitudes WHERE idsolicitud=" . (int) $id;
        $s = $em->getConnection()->prepare($sql);
        $s->execute();
        $rs = $s->fetch();
        
        switch ($col) {
            case 'codsede':
                return empty($rs)? '00' : $rs['codsede'];
                break;
            case 'nomconsultorio':
                return empty($rs)? '' : $rs['nomconsultorio'];
            break;
            case 'fechapedido':
                return empty($rs)? date("d/m/Y") : $rs['fechapedido'];
                break;           
            case 'estado':
                return empty($rs)? '' : $rs['estado'];
                break;
            default:
                return '0';
            break;
        }
    }
    /**
     * @Route("/solicitud/{id}", name="solicitud_detalle", defaults={"id"="0"})
     */
    public function detalle(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $idsolicitud = $request->get("id");        
        $sql = "SELECT
                s.idsolicitud,
                s.registro,
                s.fechapedido as fechacita,
                s.codtipopedido,
                IFNULL(STR_TO_DATE( CONVERT(s.fechapedido,char) ,'%e/%c/%Y'), STR_TO_DATE( CONVERT(s.fechapedido,char) ,'%c/%e/%Y')) as fechapedido,       
                TIME(s.fechapedido) as horapedido,
                s.codtipopedido,
                ifnull(s.codmedico,'') as codmedico,
                ifnull(s.nommedico,'') as nommedico,      
                s.dnuevo,
                s.anulado,
                s.idcita,
                s.estado,
                s.codzona,
                s.codpaciente,
                s.folio,
                ifnull(s.codconsultorio,'') as codconsultorio,
                ifnull(s.nomconsultorio,'') as nomconsultorio,        
                s.reqplaca,
                s.codsede,
                s.codsede,
                CASE WHEN s.codsede IN ('02') THEN '02' ELSE '01' END AS sedearchivo,
                s.responsablec,
                (select concat(nompaciente,' ',apellidopaterno,' ',apellidomaterno) from tbl_paciente p where p.codpaciente=s.codpaciente ) as nompaciente,
                s.idhc as codhistoria, 
                (select u.estado from tbl_ubicaciones u where  u.codhistoria = s.idhc and u.sede=sedearchivo and u.folio=s.folio limit 1 ) as tipohc,
                (select caja from tbl_ubicaciones  where  codhistoria = s.idhc and sede=sedearchivo and folio=s.folio limit 1 ) as caja        
                FROM tbl_solicitudes s       
                where s.idsolicitud=?";

        $rs = [];
        $hc = $em->getConnection()->prepare($sql);
        $hc->bindValue(1, $idsolicitud);
        $hc->execute();
        $rs = $hc->fetch();

        if (empty($rs)) {
            return $this->redirectToRoute('dashboard');
        }

        $paciente = $em->getRepository(TblPaciente::class)->findOneBy(array('codpaciente' => $rs['codpaciente']));
        if (empty($paciente)) {
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('reportes/solicitud_detalle.html.twig', [
            'hc' => $rs,
        ]);
    }

    /**
     * @Route("/documentos", name="reporte_documentos")
     */
    public function documentos()
    {
        return $this->render('reportes/documentos.html.twig', [
            'controller_name' => 'ReportesController',
        ]);
    }
}
