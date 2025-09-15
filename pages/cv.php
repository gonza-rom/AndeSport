<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de CV - AndeSport</title>
    <link rel="stylesheet" href="../styles/cv.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <div class="container">
            <p class="logo">AndeSport</p>
            <nav>
                <ul class="menu">
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="productos.php">Productos</a></li>
                    <li><a href="sobrenosotros.php">Sobre Nosotros</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                    <li><a href="inicio-sesion.php">Iniciar Sesion</a></li>
                </ul>
            </nav>
        </div>
    </header><br><br><br>

    <h1>Deja tu CV en AndeSport</h1>
    <p>Rellena el siguiente formulario para enviarnos tus datos y considerar tu CV en futuras oportunidades.</p>

    <form action="/submit-cv" method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre Completo:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="telefono">Teléfono:</label>
        <input type="tel" id="telefono" name="telefono" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion">

        <label for="experiencia">Experiencia Laboral:</label>
        <textarea id="experiencia" name="experiencia" rows="4"
            placeholder="Describe brevemente tu experiencia laboral"></textarea>

        <label for="educacion">Educación:</label>
        <textarea id="educacion" name="educacion" rows="4"
            placeholder="Describe tu educación o estudios relevantes"></textarea>

        <label for="cv">Subir CV (PDF, máximo 5 MB):</label>
        <input type="file" id="cv" name="cv" accept=".pdf" required>

        <button type="submit">Enviar CV</button>
    </form>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; AndeSport</p>
            <p class="mb-0">Te aseguramos CALIDAD en cada producto, embalaje seguro, y una excelente experiencia de compra.</p>
            <ul class="list-inline mt-3">
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
            </ul>
        </div>
    </footer>
</body>

</html>