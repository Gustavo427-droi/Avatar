<?php
session_start();
include 'conexion_be.php';

$correo = $_POST['correo'];
$contraseña = $_POST['contraseña'];

$validar_login = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo='$correo' and contraseña='$contraseña'");

if(mysqli_num_rows($validar_login) > 0){
    $_SESSION['usuario'] = $correo;

    echo '
        <html>
        <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: "¡Bienvenido a Chic Fresh! 💖",
                    text: "¡Ya eres parte del mundo más fresco de la web! 🧊",
                    icon: "success",
                    confirmButtonText: "Entrar"
                }).then(() => {
                    window.location = "../chic_fresh.php";
                });
            </script>
        </body>
        </html>
    ';
    exit;
} else {
    echo '
        <script>
            alert("Usuario no existe, por favor verifique los datos introducidos");
            window.location = "../index.php";
        </script>
    ';
    exit;
}
?>
