<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblPaciente
 *
 * @ORM\Table(name="tbl_paciente", indexes={@ORM\Index(name="codpaciente", columns={"codpaciente"})})
 * @ORM\Entity
 */
class TblPaciente
{
    /**
     * @var int
     *
     * @ORM\Column(name="idpaciente", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idpaciente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nompaciente", type="string", length=250, nullable=true)
     */
    private $nompaciente;

    /**
     * @var int
     *
     * @ORM\Column(name="codpaciente", type="integer", nullable=false)
     */
    private $codpaciente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellidopaterno", type="string", length=250, nullable=true)
     */
    private $apellidopaterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellidomaterno", type="string", length=250, nullable=true)
     */
    private $apellidomaterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codtipodocumento", type="string", length=250, nullable=true)
     */
    private $codtipodocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numdocumento", type="string", length=250, nullable=true)
     */
    private $numdocumento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fechanacimiento", type="string", length=250, nullable=true)
     */
    private $fechanacimiento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefono", type="string", length=250, nullable=true)
     */
    private $telefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="celular", type="string", length=250, nullable=true)
     */
    private $celular;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigopostal", type="string", length=250, nullable=true)
     */
    private $codigopostal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pais", type="string", length=250, nullable=true)
     */
    private $pais;

    /**
     * @var string|null
     *
     * @ORM\Column(name="departamento", type="string", length=250, nullable=true)
     */
    private $departamento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="localidad", type="string", length=250, nullable=true)
     */
    private $localidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", length=250, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numdireccion", type="string", length=250, nullable=true)
     */
    private $numdireccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codubigeo", type="string", length=250, nullable=true)
     */
    private $codubigeo;

    /**
     * @var string
     *
     * @ORM\Column(name="registro", type="string", length=250, nullable=false)
     */
    private $registro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado", type="string", length=250, nullable=true)
     */
    private $estado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codconyuge", type="string", length=250, nullable=true)
     */
    private $codconyuge;

     /**
     * @var string|null
     *
     * @ORM\Column(name="busqueda", type="string", length=250, nullable=true)
     */
    private $busqueda;

    public function getIdpaciente(): ?int
    {
        return $this->idpaciente;
    }

    public function getNompaciente(): ?string
    {
        return $this->nompaciente;
    }

    public function setNompaciente(?string $nompaciente): self
    {
        $this->nompaciente = $nompaciente;

        return $this;
    }

    public function getCodpaciente(): ?int
    {
        return $this->codpaciente;
    }

    public function setCodpaciente(int $codpaciente): self
    {
        $this->codpaciente = $codpaciente;

        return $this;
    }

    public function getApellidopaterno(): ?string
    {
        return $this->apellidopaterno;
    }

    public function setApellidopaterno(?string $apellidopaterno): self
    {
        $this->apellidopaterno = $apellidopaterno;

        return $this;
    }

    public function getApellidomaterno(): ?string
    {
        return $this->apellidomaterno;
    }

    public function setApellidomaterno(?string $apellidomaterno): self
    {
        $this->apellidomaterno = $apellidomaterno;

        return $this;
    }

    public function getCodtipodocumento(): ?string
    {
        return $this->codtipodocumento;
    }

    public function setCodtipodocumento(?string $codtipodocumento): self
    {
        $this->codtipodocumento = $codtipodocumento;

        return $this;
    }

    public function getNumdocumento(): ?string
    {
        return $this->numdocumento;
    }

    public function setNumdocumento(?string $numdocumento): self
    {
        $this->numdocumento = $numdocumento;

        return $this;
    }

    public function getFechanacimiento(): ?string
    {
        return $this->fechanacimiento;
    }

    public function setFechanacimiento(?string $fechanacimiento): self
    {
        $this->fechanacimiento = $fechanacimiento;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(?string $celular): self
    {
        $this->celular = $celular;

        return $this;
    }

    public function getCodigopostal(): ?string
    {
        return $this->codigopostal;
    }

    public function setCodigopostal(?string $codigopostal): self
    {
        $this->codigopostal = $codigopostal;

        return $this;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(?string $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

    public function getDepartamento(): ?string
    {
        return $this->departamento;
    }

    public function setDepartamento(?string $departamento): self
    {
        $this->departamento = $departamento;

        return $this;
    }

    public function getLocalidad(): ?string
    {
        return $this->localidad;
    }

    public function setLocalidad(?string $localidad): self
    {
        $this->localidad = $localidad;

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

    public function getNumdireccion(): ?string
    {
        return $this->numdireccion;
    }

    public function setNumdireccion(?string $numdireccion): self
    {
        $this->numdireccion = $numdireccion;

        return $this;
    }

    public function getCodubigeo(): ?string
    {
        return $this->codubigeo;
    }

    public function setCodubigeo(?string $codubigeo): self
    {
        $this->codubigeo = $codubigeo;

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

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCodconyuge(): ?string
    {
        return $this->codconyuge;
    }

    public function setCodconyuge(?string $codconyuge): self
    {
        $this->codconyuge = $codconyuge;

        return $this;
    }
    
    public function getBusqueda(): ?string
    {
        return $this->busqueda;
    }

    public function setBusqueda(?string $busqueda): self
    {
        $this->busqueda = $busqueda;

        return $this;
    }


}
