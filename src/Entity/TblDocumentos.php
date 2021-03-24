<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblDocumentos
 *
 * @ORM\Table(name="tbl_documentos")
 * @ORM\Entity
 */
class TblDocumentos
{
    /**
     * @var int
     *
     * @ORM\Column(name="iddoc", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iddoc;

    /**
     * @var int
     *
     * @ORM\Column(name="idsolicitud", type="integer", nullable=false)
     */
    private $idsolicitud;

    /**
     * @var string|null
     *
     * @ORM\Column(name="hospitalizacion", type="text", length=65535, nullable=true)
     */
    private $hospitalizacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="documento", type="text", length=65535, nullable=true)
     */
    private $documento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tipo", type="text", length=65535, nullable=true)
     */
    private $tipo;

    public function getIddoc(): ?int
    {
        return $this->iddoc;
    }

    public function getIdsolicitud(): ?int
    {
        return $this->idsolicitud;
    }

    public function setIdsolicitud(int $idsolicitud): self
    {
        $this->idsolicitud = $idsolicitud;

        return $this;
    }

    public function getHospitalizacion(): ?string
    {
        return $this->hospitalizacion;
    }

    public function setHospitalizacion(?string $hospitalizacion): self
    {
        $this->hospitalizacion = $hospitalizacion;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(?string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }


}
