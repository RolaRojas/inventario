<?php
// Archivo de depuración independiente
// Coloca este archivo en la raíz de tu proyecto (donde está index.php)

// Configuración de la base de datos (ajusta según tu configuración)
$db_host = 'localhost';
$db_user = 'root'; // Cambia esto si usas un usuario diferente
$db_pass = ''; // Cambia esto si usas una contraseña
$db_name = 'inventario'; // Cambia esto al nombre de tu base de datos

// Conectar a la base de datos
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "<h1>Depuración de la Base de Datos</h1>";

// Verificar tablas existentes
echo "<h2>Tablas existentes:</h2>";
echo "<ul>";
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo "<li>{$row[0]}</li>";
}
echo "</ul>";

// Verificar estructura de la tabla articulo
echo "<h2>Estructura de la tabla 'articulo':</h2>";
$result = $conn->query("DESCRIBE articulo");
echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

// Mostrar algunos registros de la tabla articulo
echo "<h2>Registros de la tabla 'articulo':</h2>";
$result = $conn->query("SELECT * FROM articulo LIMIT 5");
echo "<table border='1'>";
// Encabezados
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<tr>";
    foreach ($row as $key => $value) {
        echo "<th>{$key}</th>";
    }
    echo "</tr>";
    
    // Reiniciar el puntero
    $result->data_seek(0);
    
    // Datos
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value === null ? "NULL" : $value) . "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";

// Verificar estructura de la tabla ubicacion
echo "<h2>Estructura de la tabla 'ubicacion':</h2>";
$result = $conn->query("DESCRIBE ubicacion");
echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

// Mostrar algunos registros de la tabla ubicacion
echo "<h2>Registros de la tabla 'ubicacion':</h2>";
$result = $conn->query("SELECT * FROM ubicacion LIMIT 5");
echo "<table border='1'>";
// Encabezados
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<tr>";
    foreach ($row as $key => $value) {
        echo "<th>{$key}</th>";
    }
    echo "</tr>";
    
    // Reiniciar el puntero
    $result->data_seek(0);
    
    // Datos
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value === null ? "NULL" : $value) . "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";

// Probar una consulta JOIN
echo "<h2>Prueba de JOIN con ubicacion:</h2>";
$query = "
    SELECT a.id, a.inventario_interno, a.nroserie, 
           m.nombre as marca_nombre, 
           f.nombre as familia_nombre, 
           u.nombre as ubicacion_nombre
    FROM articulo a
    LEFT JOIN marca m ON a.id_marca = m.id
    LEFT JOIN familia f ON a.id_familia = f.id
    LEFT JOIN ubicacion u ON a.id_ubicacion = u.id
    LIMIT 5
";

try {
    $result = $conn->query($query);
    
    if ($result) {
        echo "<table border='1'>";
        // Encabezados
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<th>{$key}</th>";
            }
            echo "</tr>";
            
            // Reiniciar el puntero
            $result->data_seek(0);
            
            // Datos
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . ($value === null ? "NULL" : $value) . "</td>";
                }
                echo "</tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>Error en la consulta: " . $conn->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

// Verificar si existe la columna id_ubicacion y arreglarla si es necesario
echo "<h2>Verificación y arreglo de la columna id_ubicacion:</h2>";

// Verificar si existe la columna
$result = $conn->query("SHOW COLUMNS FROM articulo LIKE 'id_ubicacion'");
if ($result->num_rows == 0) {
    echo "<p>La columna 'id_ubicacion' no existe en la tabla 'articulo'. Intentando agregarla...</p>";
    
    try {
        $conn->query("ALTER TABLE articulo ADD COLUMN id_ubicacion INT NULL");
        echo "<p>Columna 'id_ubicacion' agregada correctamente.</p>";
        
        // Actualizar los registros existentes con un valor predeterminado
        $result = $conn->query("SELECT id FROM ubicacion WHERE nombre = 'informatica' LIMIT 1");
        if ($result->num_rows > 0) {
            $ubicacion_id = $result->fetch_assoc()['id'];
            $conn->query("UPDATE articulo SET id_ubicacion = {$ubicacion_id}");
            echo "<p>Registros actualizados con ubicación predeterminada (informatica).</p>";
        } else {
            echo "<p>No se encontró la ubicación 'informatica'. Los registros no se han actualizado.</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error al agregar la columna: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>La columna 'id_ubicacion' ya existe en la tabla 'articulo'.</p>";
    
    // Verificar si hay registros con id_ubicacion NULL
    $result = $conn->query("SELECT COUNT(*) as count FROM articulo WHERE id_ubicacion IS NULL");
    $null_count = $result->fetch_assoc()['count'];
    
    if ($null_count > 0) {
        echo "<p>Hay {$null_count} registros con id_ubicacion NULL. Intentando actualizarlos...</p>";
        
        $result = $conn->query("SELECT id FROM ubicacion WHERE nombre = 'informatica' LIMIT 1");
        if ($result->num_rows > 0) {
            $ubicacion_id = $result->fetch_assoc()['id'];
            $conn->query("UPDATE articulo SET id_ubicacion = {$ubicacion_id} WHERE id_ubicacion IS NULL");
            echo "<p>Registros actualizados con ubicación predeterminada (informatica).</p>";
        } else {
            echo "<p>No se encontró la ubicación 'informatica'. Los registros no se han actualizado.</p>";
        }
    } else {
        echo "<p>Todos los registros tienen un valor para id_ubicacion.</p>";
    }
}

// Cerrar conexión
$conn->close();
?>