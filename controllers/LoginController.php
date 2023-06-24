<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){
        $router->render('auth/login');
    }

    public static function logout(){
        echo 'Desde Logout';
    }

    public static function olvide(Router $router){
        $router->render("auth/olvide-password");
    }

    public static function recuperar(){
        echo 'Desde Recuperar';
    }

    public static function crear(Router $router){
        
        $usuario = new Usuario;
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisas que alertas este vacio
            if (empty($alertas)) {
                // Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // no esta registrado
                    // hashear el password
                    $usuario->hassPassword();

                    //generar un token unico
                    $usuario->crearToken();

                    //enviar el email

                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    //debuguear($usuario);

                    //Crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta',
            [
                'usuario' => $usuario,
                'alertas' => $alertas
            ]
        );
    }
    
    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }
    
    public static function confirmar(Router $router){

        $alertas = [];
        
        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            //mostrar mensaje de error
            Usuario::setAlerta('error','Token no válido');
        } else {
            // modificar a usuario confirmado
            $usuario->confirmado="1";
            $usuario->token = "";
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
    

}