<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblHorarios
 *
 * @ORM\Table(name="tbl_horarios")
 * @ORM\Entity
 */
class TblHorarios
{
    /**
     * @var int
     *
     * @ORM\Column(name="idhorario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idhorario;

    /**
     * @var string
     *
     * @ORM\Column(name="dias", type="string", length=250, nullable=false)
     */
    private $dias;

    /**
     * @var string
     *
     * @ORM\Column(name="inicio", type="string", length=250, nullable=false)
     */
    private $inicio;

    /**
     * @var string
     *
     * @ORM\Column(name="fin", type="string", length=250, nullable=false)
     */
    private $fin;

    public function getIdhorario(): ?int
    {
        return $this->idhorario;
    }

    public function getDias(): ?string
    {
        return $this->dias;
    }

    public function setDias(string $dias): self
    {
        $this->dias = $dias;

        return $this;
    }

    public function getInicio(): ?string
    {
        return $this->inicio;
    }

    public function setInicio(string $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFin(): ?string
    {
        return $this->fin;
    }

    public function setFin(string $fin): self
    {
        $this->fin = $fin;

        return $this;
    }


}
