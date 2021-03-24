<?php
namespace App\Controller;

use App\Entity\TblUsuarios;
use App\Form\TblUsuariosType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * @Route("/usuarios")
 */
class TblUsuariosController extends Controller
{
    /**
     * @Route("/", name="tbl_usuarios_index", methods="GET")
     */
    public function index(): Response
    {
        $tblUsuarios = $this->getDoctrine()
            ->getRepository(TblUsuarios::class)
            ->findAll();

        return $this->render('tbl_usuarios/index.html.twig', ['tbl_usuarios' => $tblUsuarios]);
    }

    /**
     * @Route("/new", name="tbl_usuarios_new", methods="GET|POST")
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $tblUsuario = new TblUsuarios();
        $form = $this->createForm(TblUsuariosType::class, $tblUsuario);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //if ($form->isSubmitted() && $form->isValid()) {
            $input = $request->get("tbl_usuarios");
            //post
            $tblUsuario->setCodigopais($input["codigopais"]);
            if ($input["codigopais"]=="PE") {
                $departamento = $input["departamento"];
                $provincia = $input["provincia"];
                $distritos= $input["distritos"];
                $tblUsuario->setDepartamento($departamento);
                $tblUsuario->setProvincia($provincia);
                $tblUsuario->setDistritos($distritos);
            } else {
                $tblUsuario->setDepartamento(0);
                $tblUsuario->setProvincia(0);
                $tblUsuario->setDistritos(0);
            }
            $tblUsuario->setIdhorario($input["idhorario"]);
            $tblUsuario->setIdarea($input["idarea"]);
            // crear contrasenia
            $password = $encoder->encodePassword($tblUsuario, $input["password"]);
            $tblUsuario->setPassword($password);
            //subir imagen
            $imagen = $form->get('imagen')->getData();
            if (!empty($imagen)) {
                $file = $tblUsuario->getEmail()."-avatar.".$imagen->guessExtension();
                $imagen->move(__DIR__. "/../../public/avatar/", $file);
            } else {
                $file = 'img2.jpg';
            }
            $tblUsuario->setImagen($file);
            // medicos
            $medicos = $input['medicos'];
            $medicos = empty($medicos) ? '0' : implode(',', $medicos);
            $tblUsuario->setMedicos($medicos);
            $em = $this->getDoctrine()->getManager();
            $em->persist($tblUsuario);
            $em->flush();

            return $this->redirectToRoute('tbl_usuarios_index');
        }

        return $this->render('tbl_usuarios/new.html.twig', [
            'tbl_usuario' => $tblUsuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idusuario}", name="tbl_usuarios_show", methods="GET")
     */
    public function show(TblUsuarios $tblUsuario): Response
    {
        return $this->render('tbl_usuarios/show.html.twig', ['tbl_usuario' => $tblUsuario]);
    }

    /**
     * @Route("/{idusuario}/edit", name="tbl_usuarios_edit", methods="GET|POST")
     */
    public function edit(Request $request, TblUsuarios $tblUsuario, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(TblUsuariosType::class, $tblUsuario);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // && $form->isValid()
            $input = $request->get("tbl_usuarios");
            //post
            $tblUsuario->setCodigopais($input["codigopais"]);
            if ($input["codigopais"]=="PE") {
                $departamento = $input["departamento"];
                $provincia = $input["provincia"];
                $distritos= $input["distritos"];
                $tblUsuario->setDepartamento($departamento);
                $tblUsuario->setProvincia($provincia);
                $tblUsuario->setDistritos($distritos);
            } else {
                $tblUsuario->setDepartamento(0);
                $tblUsuario->setProvincia(0);
                $tblUsuario->setDistritos(0);
            }
            $tblUsuario->setIdhorario($input["idhorario"]);
            $tblUsuario->setIdarea($input["idarea"]);
            // crear contrasenia
            if (!empty($input["password"])) {
                $password = $encoder->encodePassword($tblUsuario, $input["password"]);
                $tblUsuario->setPassword($password);
            }
            //subir imagen
            if (!empty($form->get('imagen')->getData())) {
                $imagen = $form->get('imagen')->getData();
                $file = $tblUsuario->getEmail()."-avatar.".$imagen->guessExtension();
                //$fichero =  __DIR__. '/../../public/cajas-usr-'.$codebar.'.zpl';
                //file_put_contents(__DIR__. "/../../public/avatar/test-imange.txt", $imagen);
                $imagen->move(__DIR__. "/../../public/avatar/", $file);
                $tblUsuario->setImagen($file);
            }
           // $medicos = $input['medicos'];
           // $medicos = empty($medicos) ? '0' : implode(',', $medicos);
           $medicos = '0';
            $tblUsuario->setMedicos($medicos);
            // update entity
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('tbl_usuarios_edit', ['idusuario' => $tblUsuario->getIdusuario()]);
            return $this->redirectToRoute('tbl_usuarios_index');
        }

        return $this->render('tbl_usuarios/edit.html.twig', [
            'tbl_usuario' => $tblUsuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idusuario}", name="tbl_usuarios_delete", methods="DELETE")
     */
    public function delete(Request $request, TblUsuarios $tblUsuario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tblUsuario->getIdusuario(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tblUsuario);
            $em->flush();
            return JsonResponse::create(array('success'=>1));
        }

        return JsonResponse::create(array('success'=>0));
    }
}
