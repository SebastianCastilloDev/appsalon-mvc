<?php

namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index( Router $router) {

        if(!isset($_SESSION['nombre'])){
            header('Location: /');
        }
        session_start();
    
        

        $router->render('cita/index',[
            'nombre' => $_SESSION['nombre']
        ]);
    }
}