<?php
require 'config/database.php';
$db = new Database();
$con = $db->conectar();

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
if ($nombre === '') {
    echo "Prenda no encontrada.";
    exit;
}

$sql = $con->prepare("SELECT * FROM prendas WHERE Nombre_Prenda = ? AND activo = 0");
$sql->execute([$nombre]);
$variantes = $sql->fetchAll(PDO::FETCH_ASSOC);

if (empty($variantes)) {
    echo "No hay variantes disponibles.";
    exit;
}

$colores = [];
$tallasPorColor = [];
foreach ($variantes as $variante) {
    $color = $variante['Color'];
    $talla = $variante['Talla'];
    $colores[$color] = $variante;
    $tallasPorColor[$color][] = $talla;
}

$imagenesPorColor = [];
foreach ($colores as $color => $variante) {
    $id = $variante['Id_Prenda'];
    $color_nombre = strtolower(str_replace(' ', '_', $color));
    $imagenes = [];
    for ($i = 1; $i <= 3; $i++) {
        $ruta = "assets/images/{$id}_{$color_nombre}_{$i}.jpeg";
        if (file_exists($ruta)) {
            $imagenes[] = $ruta;
        }
    }
    $imagenesPorColor[$color] = !empty($imagenes) ? $imagenes : ['assets/images/default.jpeg'];
}

$primerColor = array_key_first($colores);
$primeraVariante = $colores[$primerColor];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($nombre); ?> - Detalle</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap');
    body {
      font-family: 'Quicksand', sans-serif;
      background: #ffeef4;
      padding: 0;
      margin: 0;
      color: #4d3263;
    }

    .banner-rosa {
      width: 100%;
      background: linear-gradient(90deg,rgb(252, 138, 185), #ffcce2, #ffb6d5);
      color: #ffffff;
      text-align: center;
      padding: 15px 0;
      font-size: 18px;
      font-weight: bold;
      animation: frases 20s linear infinite;
    }

    @keyframes frases {
      0%   { content: "\2728 Nueva colección disponible ahora \2728"; }
      25%  { content: "\1F381 ¡30% de descuento en tu primera compra!"; }
      50%  { content: "\1F457 Estilo juvenil, fresco y auténtico"; }
      75%  { content: "\2728 Viste como quieres ser recordado"; }
      100% { content: "\2728 Nueva colección disponible ahora \2728"; }
    }

    .detalle-container {
      max-width: 1000px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
      display: flex;
      gap: 30px;
      align-items: center;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .imagen-principal {
      position: relative;
    }

    .imagen-principal img {
      width: 330px;
      height: auto;
      border-radius: 16px;
      border: 2px solid #ffcce2;
    }

    .imagen-principal button {
      position: absolute;
      top: 45%;
      background: rgba(255,182,213,0.8);
      border: none;
      font-size: 22px;
      cursor: pointer;
      padding: 8px 12px;
      border-radius: 50%;
      color: #fff;
    }

    #anterior { left: -30px; }
    #siguiente { right: -30px; }

    .info {
      flex: 1;
    }

    .info h2 {
      margin-bottom: 10px;
      font-size: 26px;
    }

    .precio {
      font-size: 22px;
      color: #e91e63;
      margin-bottom: 20px;
    }

    select {
      padding: 10px;
      margin: 10px 0;
      font-size: 16px;
      border-radius: 10px;
      border: 1px solid #e0b2c4;
    }

    button {
      margin-top: 20px;
      padding: 12px 24px;
      background: #e91e63;
      color: white;
      border: none;
      border-radius: 14px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #c2185b;
    }

    .volver {
      display: block;
      text-align: center;
      margin: 30px auto;
      color: #e91e63;
      font-weight: bold;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="banner-rosa">
  ✨ Nueva colección disponible ahora — Explora prendas versátiles y con estilo para tu día a día ✨
</div>

<div class="detalle-container">
  <div class="imagen-principal">
    <button id="anterior">←</button>
    <img id="imagenPrenda" src="" alt="Imagen principal">
    <button id="siguiente">→</button>
  </div>

  <div class="info">
    <h2><?php echo htmlspecialchars($nombre); ?></h2>
    <p id="descripcionPrenda"><?php echo $primeraVariante['Descripcion']; ?></p>

    <div class="precio">
      Precio: S/. <span id="precioPrenda"><?php echo number_format($primeraVariante['Precio_Color'], 2); ?></span>
    </div>

    <label for="color">Color:</label>
    <select id="color" onchange="actualizarDetalle()">
      <?php foreach ($colores as $color => $variante): ?>
        <option value="<?php echo $color; ?>"><?php echo $color; ?></option>
      <?php endforeach; ?>
    </select>

    <label for="talla">Talla:</label>
    <select id="talla"></select>

    <button onclick="agregarAlCarrito()">Agregar al carrito</button>
  </div>
</div>

<a href="looks_escolares.php" class="volver">← Volver al catálogo</a>

<script>
  const variantes = <?php echo json_encode($variantes); ?>;
  const tallasPorColor = <?php echo json_encode($tallasPorColor); ?>;
  const colores = <?php echo json_encode($colores); ?>;
  const imagenesPorColor = <?php echo json_encode($imagenesPorColor); ?>;

  let index = 0;
  let imagenes = [];

  function mostrarImagenCarrusel() {
    const img = document.getElementById('imagenPrenda');
    img.src = imagenes[index];
  }

  document.getElementById('anterior').onclick = () => {
    if (imagenes.length > 0) {
      index = (index - 1 + imagenes.length) % imagenes.length;
      mostrarImagenCarrusel();
    }
  };

  document.getElementById('siguiente').onclick = () => {
    if (imagenes.length > 0) {
      index = (index + 1) % imagenes.length;
      mostrarImagenCarrusel();
    }
  };

  function actualizarDetalle() {
    const color = document.getElementById('color').value;
    const variante = colores[color];
    if (!variante) return;
    imagenes = imagenesPorColor[color] || ['assets/images/default.jpeg'];
    index = 0;
    mostrarImagenCarrusel();
    document.getElementById('precioPrenda').textContent = parseFloat(variante.Precio_Color).toFixed(2);
    document.getElementById('descripcionPrenda').textContent = variante.Descripcion;

    const selectTalla = document.getElementById('talla');
    selectTalla.innerHTML = '';
    if (tallasPorColor[color]) {
      tallasPorColor[color].forEach(t => {
        const option = document.createElement('option');
        option.value = t;
        option.textContent = t;
        selectTalla.appendChild(option);
      });
    }
  }

  function agregarAlCarrito() {
    alert("Producto agregado al carrito (demo). ");
  }

  actualizarDetalle();
</script>

</body>
</html>
