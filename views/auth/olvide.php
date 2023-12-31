<h1 class="nombre-pagina">Olvidé Password</h1>
<p class="descripción-pagina">Reestablece tu password escribiendo tu email a continuación</p>
<?php 
    include_once __DIR__ ."/../templates/alertas.php";
?>
<form method="POST" class="formulario" action="/olvide">
    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Tu E-mail"/>
    </div>

    <input type="submit" value="Enviar instrucciones" class="boton">
    
</form>

<div class="acciones">
    <a href="/">¿Ya tienes un cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>