<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TblHistorial
 *
 * @ORM\Table(name="tbl_historial")
 * @ORM\Entity
 */
class TblHistorial
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idsolicitud", type="integer", nullable=false)
     */
    private $idsolicitud = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="idhc", type="integer", nullable=false)
     */
    private $idhc = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="ubicacion", type="string", length=250, nullable=true)
     */
    private $ubicacion;//codigo de zona

    /**
     * @var int
     *
     * @ORM\Column(name="estatus", type="integer", nullable=false)
     */
    private $estatus;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=250, nullable=false)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="string", length=250, nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="comentarios", type="text", length=65535, nullable=false)
     */
    private $comentarios;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdhc(): ?int
    {
        return $this->idhc;
    }

    public function setIdhc(int $idhc): self
    {
        $this->idhc = $idhc;

        return $this;
    }

    public function getUbicacion(): ?string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?string $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }

    public function getEstatus(): ?int
    {
        return $this->estatus;
    }

    public function setEstatus(int $estatus): self
    {
        $this->estatus = $estatus;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

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

    public function getComentarios(): ?string
    {
        return $this->comentarios;
    }

    public function setComentarios(string $comentarios): self
    {
        $this->comentarios = $comentarios;

        return $this;
    }


}
