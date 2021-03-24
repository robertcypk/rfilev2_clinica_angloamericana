<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblAtencion
 *
 * @ORM\Table(name="tbl_atencion")
 * @ORM\Entity
 */
class TblAtencion
{
    /**
     * @var int
     *
     * @ORM\Column(name="idatencion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idatencion;

    /**
     * @var int
     *
     * @ORM\Column(name="idpaciente", type="integer", nullable=false)
     */
    private $idpaciente;

    /**
     * @var string
     *
     * @ORM\Column(name="prioridad", type="string", length=250, nullable=false)
     */
    private $prioridad;

    /**
     * @var string
     *
     * @ORM\Column(name="tipohc", type="string", length=250, nullable=false)
     */
    private $tipohc;

    /**
     * @var string
     *
     * @ORM\Column(name="numdoc", type="string", length=250, nullable=false)
     */
    private $numdoc;

    /**
     * @var string
     *
     * @ORM\Column(name="seriedoc", type="string", length=250, nullable=false)
     */
    private $seriedoc;

    /**
     * @var string
     *
     * @ORM\Column(name="valordoc", type="string", length=250, nullable=false)
     */
    private $valordoc;

    /**
     * @var string
     *
     * @ORM\Column(name="tipodoc", type="string", length=250, nullable=false)
     */
    private $tipodoc;

    /**
     * @var string
     *
     * @ORM\Column(name="sede", type="string", length=250, nullable=false)
     */
    private $sede;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $registro = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=250, nullable=false)
     */
    private $status;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idhc", type="integer", nullable=true)
     */
    private $idhc;

    public function getIdatencion(): ?int
    {
        return $this->idatencion;
    }

    public function getIdpaciente(): ?int
    {
        return $this->idpaciente;
    }

    public function setIdpaciente(int $idpaciente): self
    {
        $this->idpaciente = $idpaciente;

        return $this;
    }

    public function getPrioridad(): ?string
    {
        return $this->prioridad;
    }

    public function setPrioridad(string $prioridad): self
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    public function getTipohc(): ?string
    {
        return $this->tipohc;
    }

    public function setTipohc(string $tipohc): self
    {
        $this->tipohc = $tipohc;

        return $this;
    }

    public function getNumdoc(): ?string
    {
        return $this->numdoc;
    }

    public function setNumdoc(string $numdoc): self
    {
        $this->numdoc = $numdoc;

        return $this;
    }

    public function getSeriedoc(): ?string
    {
        return $this->seriedoc;
    }

    public function setSeriedoc(string $seriedoc): self
    {
        $this->seriedoc = $seriedoc;

        return $this;
    }

    public function getValordoc(): ?string
    {
        return $this->valordoc;
    }

    public function setValordoc(string $valordoc): self
    {
        $this->valordoc = $valordoc;

        return $this;
    }

    public function getTipodoc(): ?string
    {
        return $this->tipodoc;
    }

    public function setTipodoc(string $tipodoc): self
    {
        $this->tipodoc = $tipodoc;

        return $this;
    }

    public function getSede(): ?string
    {
        return $this->sede;
    }

    public function setSede(string $sede): self
    {
        $this->sede = $sede;

        return $this;
    }

    public function getRegistro(): ?\DateTimeInterface
    {
        return $this->registro;
    }

    public function setRegistro(\DateTimeInterface $registro): self
    {
        $this->registro = $registro;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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


}
