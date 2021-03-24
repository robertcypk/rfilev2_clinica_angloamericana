<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\Ubigeo;

class RecursosController extends Controller
{
    public function paises()
    {
        return JsonResponse::create(Ubigeo::paises());
    }
    public function departamento(Request $request)
    {
        $pais = $request->get("pais");
        return JsonResponse::create(Ubigeo::departamento($pais));
    }
    public function provincia(Request $request)
    {
        $pais =$request->get("pais");
        $departamento=$request->get("departamento");
        return JsonResponse::create(Ubigeo::provincia($pais, $departamento));
    }
    public function distrito(Request $request)
    {
        $pais = $request->get("pais");
        $provincia = $request->get("provincia");
        return JsonResponse::create(Ubigeo::distrito($pais, $provincia));
    }
}
