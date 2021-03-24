<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

ini_set("soap.wsdl_cache_enabled", 0);
ini_set("session.auto_start", 0);
/**
 * @Route("/historiasclinicas")
 */
class WsdlsoapserviceController extends Controller
{
    /**
     * @Route("/", name="historiasclinicas", methods="GET|POST")
     */
    public function index(Request $request)
    {
        //echo phpinfo();
        ini_set("soap.wsdl_cache_enabled", "0");
        $options = array(
            'uri' => 'http://172.25.52.66/historiasclinicas/?wsdl',//'http://192.168.0.11/rfile/historiasclinicas/?wsdl',
            'cache_wsdl' => WSDL_CACHE_NONE,
            'exceptions' => true,
            'soap_version' => SOAP_1_2,
            'classmap' => array(Service\PacienteService::class)
            );
        $server = new \SoapServer(dirname(__FILE__).'/wsdl/Paciente.wsdl', $options);
        $server->setObject($this->get('app.paciente_service'));
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        ob_start();
        $server->handle();
        $response->setContent(dirname(__FILE__).'/wsdl/Paciente.wsdl');
        $response->setContent(ob_get_clean());
        return $response;
    }
}
