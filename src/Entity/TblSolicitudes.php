<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblSolicitudes
 *
 * @ORM\Table(name="tbl_solicitudes", indexes={@ORM\Index(name="codpaciente", columns={"codpaciente"})})
 * @ORM\Entity
 */
class TblSolicitudes
{
    /**
     * @var int
     *
     * @ORM\Column(name="idsolicitud", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idsolicitud;

    /**
     * @var string|null
     *
     * @ORM\Column(name="registro", type="string", length=250, nullable=true)
     */
    private $registro;

    /**
     * @var string
     *
     * @ORM\Column(name="fechapedido", type="string", length =20, nullable=false)
     */
    private $fechapedido;


    /**
     * @var string|null
     *
     * @ORM\Column(name="codtipopedido", type="string", length=250, nullable=true)
     */
    private $codtipopedido;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codmedico", type="string", length=250, nullable=true)
     */
    private $codmedico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nommedico", type="string", length=250, nullable=true)
     */
    private $nommedico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dnuevo", type="string", length=250, nullable=true)
     */
    private $dnuevo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="anulado", type="string", length=250, nullable=true)
     */
    private $anulado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idcita", type="string", length=250, nullable=true)
     */
    private $idcita;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado", type="string", length=250, nullable=true)
     */
    private $estado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codzona", type="string", length=250, nullable=true)
     */
    private $codzona;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idhc", type="integer", nullable=true)
     */
    private $idhc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codpaciente", type="integer", nullable=true)
     */
    private $codpaciente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codconsultorio", type="string", length=250, nullable=true)
     */
    private $codconsultorio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomconsultorio", type="string", length=250, nullable=true)
     */
    private $nomconsultorio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reqplaca", type="string", length=250, nullable=true)
     */
    private $reqplaca;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codsede", type="string", length=250, nullable=true)
     */
    private $codsede;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observaciones", type="string", length=250, nullable=true)
     */
    private $observaciones;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cumplimiento", type="string", length=250, nullable=true)
     */
    private $cumplimiento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="responsable", type="string", length=250, nullable=true)
     */
    private $responsable;

    /**
     * @var string|null
     *
     * @ORM\Column(name="responsableb", type="string", length=250, nullable=true)
     */
    private $responsableb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="responsablec", type="string", length=250, nullable=true)
     */
    private $responsablec;

    /**
     * @var string|null
     *
     * @ORM\Column(name="folio", type="string", length=20, nullable=true)
     */
    private $folio;

    public function getIdsolicitud(): ?int
    {
        return $this->idsolicitud;
    }

    public function getRegistro(): ?string
    {
        return $this->registro;
    }

    public function setRegistro(?string $registro): self
    {
        $this->registro = $registro;

        return $this;
    }

    public function getFechapedido(): ?string
    {
        return $this->fechapedido;
    }

    public function setFechapedido(string $fechapedido): self
    {
        $this->fechapedido = $fechapedido;

        return $this;
    }

    public function getHorapedido(): ?string
    {
        return $this->horapedido;
    }

    public function setHorapedido(string $horapedido): self
    {
        $this->horapedido = $horapedido;

        return $this;
    }

    public function getCodtipopedido(): ?string
    {
        return $this->codtipopedido;
    }

    public function setCodtipopedido(?string $codtipopedido): self
    {
        $this->codtipopedido = $codtipopedido;

        return $this;
    }

    public function getCodmedico(): ?string
    {
        return $this->codmedico;
    }

    public function setCodmedico(?string $codmedico): self
    {
        $this->codmedico = $codmedico;

        return $this;
    }

    public function getNommedico(): ?string
    {
        return $this->nommedico;
    }

    public function setNommedico(?string $nommedico): self
    {
        $this->nommedico = $nommedico;

        return $this;
    }

    public function getDnuevo(): ?string
    {
        return $this->dnuevo;
    }

    public function setDnuevo(?string $dnuevo): self
    {
        $this->dnuevo = $dnuevo;

        return $this;
    }

    public function getAnulado(): ?string
    {
        return $this->anulado;
    }

    public function setAnulado(?string $anulado): self
    {
        $this->anulado = $anulado;

        return $this;
    }

    public function getIdcita(): ?string
    {
        return $this->idcita;
    }

    public function setIdcita(?string $idcita): self
    {
        $this->idcita = $idcita;

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

    public function getCodzona(): ?string
    {
        return $this->codzona;
    }

    public function setCodzona(?string $codzona): self
    {
        $this->codzona = $codzona;

        return $this;
    }

    public function getIdhc(): ?int
    {
        return $this->idhc;
    }

    public function setIdhc(?int $idhc): self
    {
        $this->idhc = $idhc;

        return $this;
    }

    public function getCodpaciente(): ?int
    {
        return $this->codpaciente;
    }

    public function setCodpaciente(?int $codpaciente): self
    {
        $this->codpaciente = $codpaciente;

        return $this;
    }

    public function getCodconsultorio(): ?string
    {
        return $this->codconsultorio;
    }

    public function setCodconsultorio(?string $codconsultorio): self
    {
        $this->codconsultorio = $codconsultorio;

        return $this;
    }

    public function getNomconsultorio(): ?string
    {
        return $this->nomconsultorio;
    }

    public function setNomconsultorio(?string $nomconsultorio): self
    {
        $this->nomconsultorio = $nomconsultorio;

        return $this;
    }

    public function getReqplaca(): ?string
    {
        return $this->reqplaca;
    }

    public function setReqplaca(?string $reqplaca): self
    {
        $this->reqplaca = $reqplaca;

        return $this;
    }

    public function getCodsede(): ?string
    {
        return $this->codsede;
    }

    public function setCodsede(?string $codsede): self
    {
        $this->codsede = $codsede;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getCumplimiento(): ?string
    {
        return $this->cumplimiento;
    }

    public function setCumplimiento(?string $cumplimiento): self
    {
        $this->cumplimiento = $cumplimiento;

        return $this;
    }

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(?string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getResponsableb(): ?string
    {
        return $this->responsableb;
    }

    public function setResponsableb(?string $responsableb): self
    {
        $this->responsableb = $responsableb;

        return $this;
    }

    public function getResponsablec(): ?string
    {
        return $this->responsablec;
    }

    public function setResponsablec(?string $responsablec): self
    {
        $this->responsablec = $responsablec;

        return $this;
    }

    public function getFolio(): ?string
    {
        return $this->folio;
    }

    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;

        return $this;
    }


}
