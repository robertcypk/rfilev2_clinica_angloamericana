<?php
namespace App\Controller;

class Toolclass
{
    public function fhc($v)
    {
        $v = preg_replace("/[^0-9]/", "", $v);
        $v = str_pad($v, 7, '0', STR_PAD_LEFT);
        return $v;
    }
    public function ffolio($v)
    {
        $v = preg_replace("/[^0-9]/", "", $v);
        $v = str_pad($v, 3, '0', STR_PAD_LEFT);
        return $v;
    }
    public function psedes($v)
    {
        switch ($v) {
            case '01':
                return 'San Isidro';
                break;
            case '02':
                return 'La Molina';
                break;
            case '03':
                return 'San Isidro';
                break;
            case '04':
                return 'Torre Dr. Fleck';
                break;
        }
    }
    public function psestados($v)
    {
        switch ($v) {
            case 1:
                return 'En Busqueda';
                break;
            case 2:
                return 'Encontrado';
                break;
            case 3:
                return 'En Trayecto';
                break;
            case 4:
                return 'Entregado';
                break;
            case 5:
                return 'En Retorno';
                break;
            case 6:
                return 'En Acopio';
                break;
            case 7:
                return 'Archivado';
                break;
            case 8:
                return 'Anulado';
                break;
            default:
                return 'Pendiente';
                break;
        }
    }
}
