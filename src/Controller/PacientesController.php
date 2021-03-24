<?php

namespace App\Controller;

use App\Entity\TblPaciente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/pacientes")
 */
class PacientesController extends Controller
{
    public function index(Request $request)
    {
        $session = new Session();
        $buscar = $request->get('buscar');
        $maxrs = 200;
        $total = 0;
        $paginador = "";

        $pagina = ($request->get("pagina") != 0) ? $request->get("pagina") : 0;
        $offset = ($request->get("pagina") != 0) ? ($request->get("pagina") - 1) * $maxrs : 0;
        $limit = " GROUP BY pa.codpaciente ORDER by pa.codpaciente DESC LIMIT " . $offset . "," . $maxrs . "";//AGE: ADDED "GROUP BY pa.codpaciente ";
        $filtro = "  "; // hc.codpaciente = pa.codpaciente

        $rw = [];

        $em = $this->getDoctrine()->getManager();

        if (!empty($buscar)) {
            $session->set('buscar', $buscar);
        }
        $buscar = $session->get('buscar');
        $min = strlen($buscar);

        if (!empty($buscar) and $min > 3) {
            $filtro .= "  WHERE (pa.numdocumento = '" . $buscar . "' OR pa.codpaciente = '" . $buscar . "' OR pa.nompaciente LIKE '%" . $buscar . "%' OR pa.apellidopaterno LIKE '%" . $buscar . "%' OR pa.apellidomaterno LIKE '%" . $buscar . "%')";
            $sql = "SELECT
            pa.codpaciente,
            concat(pa.nompaciente,' ',pa.apellidopaterno,' ',pa.apellidomaterno) AS nombresapellidos,
            pa.codtipodocumento AS tipodocumento,
            pa.numdocumento,
            pa.registro,
            COALESCE(hc.codhistoria,'') as codhistoria
            FROM tbl_paciente pa
            LEFT JOIN tbl_hc hc ON hc.codpaciente = pa.codpaciente " . $filtro . $limit;

            $sqlc ="SELECT count(pa.codpaciente) as total FROM tbl_paciente pa " . $filtro;

            $totalRs = $em->getConnection()->prepare($sqlc);
            $totalRs->execute();
            $totalRs = $totalRs->fetch();
            $total = $totalRs["total"];
            //listado
            $q = $em->getConnection()->prepare($sql);
            $q->execute();
            $rw = $q->fetchAll();
        }
        
        $providers_pag = new \App\Controller\Genpaginator;
        $paginador = $providers_pag->generatePaging($total, $maxrs, $pagina, $request->getUri());
        
        return $this->render('pacientes/index.html.twig', [
            'controller_name' => 'PacientesController',
            'pacientes' => $rw,
            'total' => $total,
            'buscar' => $buscar,
            'paginador' => $paginador,
        ]);
    }
    /**
     * @Route("/nuevo", name="nuevopaciente")
     */
    public function nuevopaciente(Request $request)
    {
        return $this->render('pacientes/new.html.twig', [
            'controller_name' => 'PacientesController',
        ]);
    }
    /**
     * @Route("/editar/{id}/ver", name="editarpaciente", defaults={"id"=0}, requirements={"id" = "\d+"}, methods="POST|GET")
     */
    public function editarpaciente(Request $request)
    {
        $id = $request->get('id');
        $p = $this->getDoctrine()->getRepository(TblPaciente::class)->findOneBy(array('codpaciente' => $id));

        $hc = $this->getDoctrine()->getRepository(\App\Entity\TblHc::class)->findBy(array('codpaciente'=>$id));

        if (empty($p)) {
            return $this->redirectToRoute('pacientes');
        }
        return $this->render('pacientes/editar.html.twig', [
            'paciente' => $p,
            'hc' => $hc
        ]);
    }
    /**
     * @Route("/editar/guardar", name="guardaredicionpaciente", methods="POST|GET")
     */
    public function guardaredicionpaciente(Request $request)
    {
        //$form = $this->createForm(TblPaciente::class, $tblpaciente);
        $request = $request->request->all();
        if (!empty($request)) {
            try {
                $form = $request['tbl_paciente'];
                $tblpaciente = $this->getDoctrine()->getRepository(TblPaciente::class)
                                               ->findOneBy(array('idpaciente'=> $form['idpaciente']));
                $tblpaciente->setNompaciente($form['nombrepaciente']);
                $tblpaciente->setApellidopaterno($form['apellidopaterno']);
                $tblpaciente->setApellidomaterno($form['apellidomaterno']);
                $tblpaciente->setCodconyuge($form['codconyuge']);
                $tblpaciente->setBusqueda($form['nombrepaciente'].' '.$form['apellidopaterno'].' '.$form['apellidomaterno']);
                $tblpaciente->setCodtipodocumento($form['codtipodocumento']);
                $tblpaciente->setNumdocumento($form['numdocumento']);
                $tblpaciente->setEmail($form['email']);
                $tblpaciente->setPais($form['pais']);
                $tblpaciente->setDepartamento($form['departamento']);
                $tblpaciente->setLocalidad($form['localidad']);
                $tblpaciente->setCodubigeo($form['codubigeo']);
                $tblpaciente->setDireccion($form['direccion']);
                $tblpaciente->setNumdireccion($form['numdireccion']);
                $tblpaciente->setCodigopostal($form['codigopostal']);
                $tblpaciente->setTelefono($form['telefono']);
                $tblpaciente->setCelular($form['celular']);
                $tblpaciente->setCodpaciente($form['codpaciente']);
                $this->getDoctrine()->getManager()->flush();
                return JsonResponse::create(array("success" => 1));
            } catch (\Expcetion $e) {
                return JsonResponse::create(array("success" => 2, 'error'=>"Excepcion:" .date('d/m/Y H:m:s').'-'. $e->getMessage() . ":" . $e->getFile() . ":" . $e->getLine()));
            }
        } else {
            return JsonResponse::create(array("success" => 2));
        }
    }

    /**
     * @Route("/hc_editar", name="hceditar", methods="POST|GET")
     */
    public function hceditar(Request $request)
    {
        $request = $request->request->all();
        if (!empty($request)) {
            try {
                $tblhc = $this->getDoctrine()->getRepository(\App\Entity\TblHc::class)
                                         ->findOneBy(array('idhc'=>$request['idhc']));
                $tblhc->setSede($request['sede']);
                $tblhc->setCodhistoria($request['hc']);
                $tblhc->setCodpaciente($request['codpaciente']);
                $this->getDoctrine()->getManager()->flush();

                return JsonResponse::create(array("success" => 1));
            } catch (\Expcetion $e) {
                return JsonResponse::create(array("success" => 2, 'error'=>"Excepcion:" .date('d/m/Y H:m:s').'-'. $e->getMessage() . ":" . $e->getFile() . ":" . $e->getLine()));
            }
        } else {
            return JsonResponse::create(array("success" => 2));
        }
    }

    /**
     * @Route("/hc_guardar", name="hcguardar", methods="POST|GET")
     */
    public function hcguardar(Request $request){
        $request = $request->request->all();
        if(!empty($request)){
            try{
                $tblhc = new \App\Entity\TblHc();
                $tblhc->setSede($request['sede']);
                $tblhc->setCodhistoria($request['hc']);
                $tblhc->setCodpaciente($request['codpaciente']);
                $tblhc->setRegistro(\DateTime::createFromFormat('Y-m-d', date("Y-m-d")));
                $tblhc->setTipohc(1);
                $em = $this->getDoctrine()->getManager();
                $em->persist($tblhc);
                $em->flush();
                return JsonResponse::create(array("success" => 1));
            } catch(\Expcetion $e){
                return JsonResponse::create(array("success" => 2, 'error'=>"Excepcion:" .date('d/m/Y H:m:s').'-'. $e->getMessage() . ":" . $e->getFile() . ":" . $e->getLine()));
            }
        }else{
            return JsonResponse::create(array("success" => 2));
        }
    }

    /**
     * @Route("/hc_eliminar", name="hceliminar", methods="POST|GET")
     */
    public function hceliminar(Request $request){
        $request = $request->request->all();
        if(!empty($request)){
            try{
                $tblhc = $this->getDoctrine()->getRepository(\App\Entity\TblHc::class)->findOneBy(array('idhc'=>$request['idhc']));
                if(!empty($tblhc)){
                    $em = $this->getDoctrine()->getManager();
                    $em->remove($tblhc);
                    $em->flush();
                    return JsonResponse::create(array("success" => 1));
                }
                return JsonResponse::create(array("success" => 2));
            }catch(\Expcetion $e){
                return JsonResponse::create(array("success" => 2, 'error'=>"Excepcion:" .date('d/m/Y H:m:s').'-'. $e->getMessage() . ":" . $e->getFile() . ":" . $e->getLine()));
            }
        }else{
            return JsonResponse::create(array("success"=>2));
        }
    }
}
