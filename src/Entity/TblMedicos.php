<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblMedicos
 *
 * @ORM\Table(name="tbl_medicos")
 * @ORM\Entity
 */
class TblMedicos
{
    /**
     * @var int
     *
     * @ORM\Column(name="idmedico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmedico;

    /**
     * @var string
     *
     * @ORM\Column(name="medico", type="text", length=65535, nullable=false)
     */
    private $medico;

    /**
     * @var string
     *
     * @ORM\Column(name="codmedico", type="string", length=250, nullable=false)
     */
    private $codmedico;

    /**
     * @var string
     *
     * @ORM\Column(name="areas", type="text", length=65535, nullable=false)
     */
    private $areas;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="string", length=250, nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=250, nullable=false)
     */
    private $estado;

    public function getIdmedico(): ?int
    {
        return $this->idmedico;
    }

    public function getMedico(): ?string
    {
        return $this->medico;
    }

    public function setMedico(string $medico): self
    {
        $this->medico = $medico;

        return $this;
    }

    public function getCodmedico(): ?string
    {
        return $this->codmedico;
    }

    public function setCodmedico(string $codmedico): self
    {
        $this->codmedico = $codmedico;

        return $this;
    }

    public function getAreas(): ?string
    {
        return $this->areas;
    }

    public function setAreas(string $areas): self
    {
        $this->areas = $areas;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }


}
