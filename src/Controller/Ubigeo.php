<?php

namespace App\Controller;

class Ubigeo
{
    /**
     * paises
     * @return void
     */
    public function paises()
    {
        $dir = __DIR__."/json-PE/paises.json";
        $json = file_get_contents($dir);
        $filtro = json_decode($json, true);
        $arr = array();
        foreach ($filtro as $k => $v) {
            $arr[$v] = $k;
        }
        ksort($arr);
        return $arr;
    }
    /**
     * departamento
     * @param string $pais
     * @return void
     */
    public function departamento($pais="pe")
    {
        if ($pais=="default") {
            return array();
        }
        $dir = __DIR__."/json-".strtoUpper($pais)."/departamentos.json";
        if (!file_exists($dir)) {
            return array();
        }
        $json = file_get_contents($dir, false);
        $filtro = json_decode($json, true);
        ksort($filtro);
        return $filtro;
    }
    /**
     * provincia
     * @param string $pais
     * @param integer $departamento
     * @return void
     */
    public function provincia($pais="default", $departamento=0)
    {
        if ($pais=="default" and $departamento==0) {
            return array();
        }
        $dir = __DIR__."/json-".strtoUpper($pais)."/provincias.json";
        if (!file_exists($dir)) {
            return array();
        }
        $json = file_get_contents($dir, false);
        $filtro = json_decode($json, true);
        $array = array();
        foreach ($filtro[$departamento] as $k => $v) {
            $array[] = array(
                        'id_ubigeo' => $v['id_ubigeo'],
                        'nombre_ubigeo' => $v['nombre_ubigeo']
                    );
        }
        return ($array);
    }
    /**
     * distrito
     * @param string $pais
     * @param integer $provincia
     * @return void
     */
    public function distrito($pais="default", $provincia=0)
    {
        if ($pais=="default" and $provincia==0) {
            return array();
        }
        $dir = __DIR__."/json-".strtoUpper($pais)."/distritos.json";
        if (!file_exists($dir)) {
            return array();
        }
        $json = file_get_contents($dir, false);
        $filtro = json_decode($json, true);
        $array = array();
        foreach ($filtro[$provincia] as $k => $v) {
            $array[] = array(
                        'id_ubigeo' => $v['id_ubigeo'],
                        'nombre_ubigeo' => $v['nombre_ubigeo']
                    );
        }
        return ($array);
    }
}
