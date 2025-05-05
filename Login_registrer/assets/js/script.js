document.getElementById("btn__iniciar-sesion").addEventListener("click", mostrarLogin);
document.getElementById("btn__registrarse").addEventListener("click", mostrarRegister);

function mostrarLogin() {
    document.querySelector(".formulario__login").style.display = "block";
    document.querySelector(".formulario__register").style.display = "none";
}

function mostrarRegister() {
    document.querySelector(".formulario__login").style.display = "none";
    document.querySelector(".formulario__register").style.display = "block";
}
