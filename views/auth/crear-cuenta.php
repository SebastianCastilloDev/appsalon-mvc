<h1 class="nombre-pagina">Crear Cuenta</h1>
<p class="descripcion-pagina">LLena el siguiente formulario para crear una cuenta</p>

<form method="POST" class="formulario" action="/crear-cuenta">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre"/>
    </div>
    <div class="campo">
        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" name="apellido" placeholder="Tu apellido"/>
    </div>
    <div class="campo">
        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Tu telefono" >
    </div>
    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Tu E-mail"/>
    </div>
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu password"/>
    </div>

    <input type="submit" value="Crear Cuenta" class="boton">
    
</form>

<div class="acciones">
    <a href="/">¿Ya tienes un cuenta? Inicia Sesión</a>
    <a href="/olvide-password">¿Olvidaste tu password?</a>
</div>