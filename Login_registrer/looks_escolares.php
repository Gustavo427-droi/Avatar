<?php
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

// Traer todas las prendas activas
$sql = $con->prepare("SELECT Id_Prenda, Nombre_Prenda, Descripcion, Genero, Categoria, Precio_base, Color, Talla, Stock, Precio_Color FROM prendas WHERE activo=1");
$sql->execute();
$productos = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Looks Escolares - Chic Fresh</title>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a2e0a1b6b2.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --lila-bebe: #e6d5f7;
      --lila-oscuro: #b58be3;
      --hover-lila: #d2b5f1;
      --card-color: #ffffff;
      --shadow: rgba(0, 0, 0, 0.1);
      --text-dark: #333;
      --text-light: #666;
      --accent: #6b3fa0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Quicksand', sans-serif;
    }

    body {
      background-color: var(--lila-bebe);
      color: var(--text-dark);
      padding: 20px;
    }

    header {
      background-color: var(--lila-oscuro);
      padding: 30px 20px;
      border-radius: 16px;
      text-align: center;
      margin-bottom: 40px;
      color: white;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    header h1 {
      font-size: 36px;
    }

    header p {
      font-size: 18px;
      margin-top: 8px;
      color: #f2f2f2;
    }

    .carrito {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 20px;
      background-color: white;
      padding: 10px 16px;
      border-radius: 30px;
      color: var(--accent);
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      box-shadow: 0 4px 8px var(--shadow);
    }

    .carrito i {
      font-size: 22px;
    }

    .contador {
      background-color: var(--accent);
      color: white;
      border-radius: 50%;
      padding: 4px 8px;
      font-size: 14px;
    }

    .busqueda {
      display: flex;
      justify-content: center;
      margin: 30px 0;
    }

    .busqueda input {
      padding: 10px 16px;
      border: none;
      border-radius: 50px 0 0 50px;
      font-size: 16px;
      width: 300px;
    }

    .busqueda button {
      background-color: var(--accent);
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 0 50px 50px 0;
      cursor: pointer;
    }

    .productos {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .producto {
  background-color: var(--card-color);
  border-radius: 16px;
  padding: 16px;
  text-align: center;
  box-shadow: 0 4px 8px var(--shadow);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  width: 260px; /* ajustado al tamaño de la imagen */
  margin: 0 auto; /* para centrar si es necesario */
}

    .producto:hover {
      transform: translateY(-10px);
      box-shadow: 0 0 12px 4px var(--hover-lila);
    }

    .producto img {
  width: auto;
  max-width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 12px;
  display: block;
  margin: 0 auto;
}


    .producto h3 {
      margin-top: 12px;
      font-size: 18px;
    }

    .producto p {
      color: var(--text-light);
      font-size: 16px;
      margin: 4px 0;
    }

    .volver {
      display: block;
      text-align: center;
      margin-top: 40px;
      font-weight: bold;
      color: var(--accent);
      text-decoration: none;
    }

    footer {
      margin-top: 60px;
      text-align: center;
      color: #666;
    }

    /* galería */
    .galeria {
      display: flex;
      gap: 6px;
      justify-content: center;
      margin-bottom: 12px;
    }

    .galeria img {
      width: 30%;
      border-radius: 8px;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <header>
    <h1>Looks Escolares</h1>
    <p>Explora nuestra selección de prendas escolares con estilo</p>
    <div class="carrito">
      <i class="fas fa-shopping-cart"></i>
      <span class="contador" id="contadorCarrito">0</span>
    </div>
  </header>

  <div class="busqueda">
    <input type="text" placeholder="Buscar prenda..." />
    <button><i class="fas fa-search"></i></button>
  </div>

  <section class="productos">
    <?php foreach ($productos as $producto) { 
      $id = $producto['Id_Prenda'];
      $nombre = $producto['Nombre_Prenda'];
      $precio = $producto['Precio_base'];

      // Intenta cargar imagen basada en el ID
      $imagenModelo = "assets/images/fn/fn1.jpeg";
      if (!file_exists($imagenModelo)) {
        $imagenModelo = "assets/images/fondo2.jpg"; // imagen por defecto
      }
    ?>
      <div class="producto">
        <a href="detalle_looks_escolares.php?nombre=<?php echo urlencode($nombre); ?>">
          <img src="<?php echo $imagenModelo; ?>" alt="<?php echo htmlspecialchars($nombre); ?>">
          <h3><?php echo htmlspecialchars($nombre); ?></h3>
          <p><?php echo 'Desde S/. ' . number_format($precio, 2); ?></p>
        </a>
      </div>
    <?php } ?>
  </section>

  <a href="chic_fresh.php" class="volver">Volver a la página de inicio</a>

  <footer>
    &copy; 2025 Chic Fresh - Looks Escolares
  </footer>

  <script>
    let contador = 0;
    function agregarAlCarrito() {
      contador++;
      document.getElementById("contadorCarrito").textContent = contador;
    }
  </script>
</body>
</html>
