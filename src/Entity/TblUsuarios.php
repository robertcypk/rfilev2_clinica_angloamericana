<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * TblUsuarios
 *
 * @ORM\Table(name="tbl_usuarios", indexes={@ORM\Index(name="idarea", columns={"idarea"}), @ORM\Index(name="idhorario", columns={"idhorario"})})
 * @ORM\Entity
 */
class TblUsuarios implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="idusuario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idusuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=250, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=250, nullable=false)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=250, nullable=false)
     */
    private $dni;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=250, nullable=false)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=250, nullable=false)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=250, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=250, nullable=false)
     */
    private $imagen;

    /**
     * @var string
     *
     * @ORM\Column(name="codigopais", type="string", length=250, nullable=false)
     */
    private $codigopais;

    /**
     * @var string
     *
     * @ORM\Column(name="departamento", type="string", length=250, nullable=false)
     */
    private $departamento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", length=250, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="registro", type="string", length=250, nullable=false)
     */
    private $registro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=250, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="rol", type="string", length=250, nullable=false)
     */
    private $rol;

    /**
     * @var int
     *
     * @ORM\Column(name="idhorario", type="integer", nullable=false)
     */
    private $idhorario;

    /**
     * @var int
     *
     * @ORM\Column(name="idarea", type="integer", nullable=false)
     */
    private $idarea;

    /**
     * @var string|null
     *
     * @ORM\Column(name="provincia", type="string", length=250, nullable=true)
     */
    private $provincia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="distritos", type="string", length=250, nullable=true)
     */
    private $distritos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="medicos", type="string", length=250, nullable=true)
     */
    private $medicos;

    public function getIdusuario(): ?int
    {
        return $this->idusuario;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): self
    {
        $this->dni = $dni;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): self
    {
        $this->celular = $celular;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getCodigopais(): ?string
    {
        return $this->codigopais;
    }

    public function setCodigopais(string $codigopais): self
    {
        $this->codigopais = $codigopais;

        return $this;
    }

    public function getDepartamento(): ?string
    {
        return $this->departamento;
    }

    public function setDepartamento(string $departamento): self
    {
        $this->departamento = $departamento;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getRegistro(): ?string
    {
        return $this->registro;
    }

    public function setRegistro(string $registro): self
    {
        $this->registro = $registro;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    public function getIdhorario(): ?int
    {
        return $this->idhorario;
    }

    public function setIdhorario(int $idhorario): self
    {
        $this->idhorario = $idhorario;

        return $this;
    }

    public function getIdarea(): ?int
    {
        return $this->idarea;
    }

    public function setIdarea(int $idarea): self
    {
        $this->idarea = $idarea;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(?string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getDistritos(): ?string
    {
        return $this->distritos;
    }

    public function setDistritos(?string $distritos): self
    {
        $this->distritos = $distritos;

        return $this;
    }

    public function getMedicos(): ?string
    {
        return $this->medicos;
    }

    public function setMedicos(?string $medicos): self
    {
        $this->medicos = $medicos;

        return $this;
    }
    public function eraseCredentials()
    {
        return null;
    }
    public function getRoles()
    {
        return [$this->getRol()];
    }
    public function getUsername()
    {
        return $this->email;
    }
    public function getSalt()
    {
        return null;
    }
}
