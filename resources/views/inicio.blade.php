<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Morelos</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/inicio.css')
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <div class="logo">
                <img src="{{ asset('images/logoprincipal.png') }}" alt="Grupo Morelos"/>
                <span>GRUPO MORELOS</span>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="#">Sobre nosotros</a></li>
                    <li><a href="#">Oferta</a></li>
                    <li><a href="#">Docentes</a></li>
                    <li><a href="#">Alumnos</a></li>
                    <li><a href="#">Aspirantes</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
            <a href="{{ route('adminlogin') }}" class="user-icon" aria-label="Cuenta">
                <img src="{{ asset('images/user.png') }}" alt="Cuenta">
            </a>
        </div>
        
    </header>

    <div class="hero" style="background-image: url('{{ asset('images/nosotros.png') }}');">
        <div class="hero-content">
        </div>
    </div>

    <section class="sobre-nosotros">
        <div class="contenido">
            <h2>SOBRE NOSOTROS</h2>
            <p>
            Diplomado en la Formación de Paramédicos, orientado al desarrollo de competencias teóricas y prácticas en atención prehospitalaria, primeros auxilios y manejo de emergencias médicas.
            </p>
            <div class="estadisticas">
                <div class="dato">
                    <h3>+15</h3>
                    <p>Años de experiencia</p>
                </div>
                <div class="dato">
                    <h3>+70</h3>
                    <p>Generaciones</p>
                </div>
                <div class="dato">
                    <h3>+5</h3>
                    <p>Cedes</p>
                </div>
            </div>
        </div>
    </section>

    <section class="historia">
        <div class="container">
            <h2>NUESTRA HISTORIA</h2>
            <p>En Grupo Morelos, hemos dedicado más de 15 años a la formación de profesionales en emergencias, construyendo un legado de excelencia y servicio.</p>
            <div class="galeria">
                <img src="{{ asset('images/historia.png') }}" alt="Nuestra Historia">
            </div>
        </div>
    </section>

    <section class="mision-vision-valores">
        <div class="container">
            <div class="mision item">
                <h3>MISIÓN</h3>
                <p>Formamos profesionales en atención prehospitalaria, protección civil y bomberos, con excelencia académica y ética.</p>
            </div>
            <div class="vision item">
                <h3>VISIÓN</h3>
                <p>Ser una institución líder en la formación de profesionales de la salud y emergencias, reconocida a nivel nacional.</p>
            </div>
            <div class="valores item">
                <h3>VALORES</h3>
                <p>Compromiso, responsabilidad, ética y excelencia en el aprendizaje continuo.</p>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2025 Grupo Morelos. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>