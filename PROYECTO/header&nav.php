<h1><a href="inicio.php">Telemedicina</a></h1>
<nav>
    <ul>
        <?php
        if (!isset($_SESSION['usuario']['medico'])) {
            echo "<li><a href=\"inicio.php\" class=\"nav\">Inicio</a></li>";
        }
        ?>
        <li><a href="bandeja.php" class="nav">Bandeja</a></li>
        <?php
        if ($_SESSION['usuario']['rol'] != 0) {
            echo "<li><a href='zonaadmin.php' class='nav'>Zona Admin</a></li>";
        }
        ?>
        <li><a href="lista_medicos.php" class="nav">Medicos</a></li>
        <li><a href="perfil.php" class="nav">Perfil</a></li>
        <li><a href="logout.php" class="nav">Salir</a></li>
    </ul>
</nav>