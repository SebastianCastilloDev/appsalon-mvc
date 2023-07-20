<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD']==='POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();
            if(empty($alertas)){
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario) {
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)){
                        //autenticar el usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionamiento

                        if ($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }                        
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD']==="POST"){

            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                //verificar que el email de usuario existe
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado==="1"){

                    // Generar token de un solo uso
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
                $alertas = Usuario::getAlertas();
            }
        }
        $router->render("auth/olvide",[
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router){

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        //buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === "POST"){
            //leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password',[
            'alertas' => $alertas,
            'error' => $error
        ]);


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
                    $usuario->hashPassword();

                    //generar un token unico
                    $usuario->crearToken();

                    //enviar el email

                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

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