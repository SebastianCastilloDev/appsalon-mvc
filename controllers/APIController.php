<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index () {
        $servicios = Servicio::all();

        echo json_encode($servicios);
    }

    public static function guardar() {

        //almacena la cita y devuelve el id
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $id = $resultado['id'];

        //almacena la cita y el servicio

        $idServicios = explode(',',$_POST['servicios']); //explode es similar a split

        foreach($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }



        echo json_encode(['resultado' => $resultado]);
    }
}

