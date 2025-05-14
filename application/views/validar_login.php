<?php
session_start();
require 'conexion.php'; // Tu archivo de conexión a la BD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = trim($_POST['rut']);
    $clave = $_POST['clave'];

    // Buscar el usuario por rut
    $sql = "SELECT * FROM usuarios WHERE rut = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($clave, $usuario['clave'])) {
            $_SESSION['rut'] = $usuario['rut'];
            $_SESSION['id'] = $usuario['id'];
            header("Location: panel.php"); // Redirigir al panel
            exit();
        }
    }

    // Si llega aquí es porque falló
    header("Location: login.php?ERROR=1");
    exit();
}
?>

