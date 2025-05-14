<?php
function listar_archivos($dir) {
    $archivos = scandir($dir);
    foreach ($archivos as $archivo) {
        if ($archivo != "." && $archivo != "..") {
            $ruta = $dir . DIRECTORY_SEPARATOR . $archivo;
            if (is_dir($ruta)) {
                echo "<strong>📁 Carpeta: <a href='http://localhost/$archivo/'>$archivo/</a></strong><br>";
                listar_archivos($ruta);
            } else {
                if (pathinfo($archivo, PATHINFO_EXTENSION) == "php") {
                    echo "📄 <a href='http://localhost/$archivo'>$archivo</a><br>";
                }
            }
        }
    }
}
listar_archivos(__DIR__);
?>