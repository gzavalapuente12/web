<?php
include("../includes/header.php");
include("../conexion.php"); // Asegúrate de que este archivo define $conn = mysqli_connect(...);

// CREAR usuario
if (isset($_POST['registrar'])) {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $rol = mysqli_real_escape_string($conn, $_POST['rol']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Sentencia preparada para evitar inyección SQL
    $sql = "INSERT INTO USUARIOS_LOGIN (Nombre, Correo, Password, Rol, FechaRegistro)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $nombre, $correo, $password, $rol);

    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success text-center mt-3'>✅ Usuario registrado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>❌ Error al registrar usuario: " . mysqli_error($conn) . "</div>";
    }

    mysqli_stmt_close($stmt);
}

// ELIMINAR usuario
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $sql = "DELETE FROM USUARIOS_LOGIN WHERE ID_Usuario = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success text-center mt-3'>🗑 Usuario eliminado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>❌ Error al eliminar usuario: " . mysqli_error($conn) . "</div>";
    }

    mysqli_stmt_close($stmt);
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0 p-5" style="border-radius:20px;">
        <h2 class="text-success text-center mb-4">👥 Gestión de Usuarios y Roles</h2>

        <!-- FORMULARIO DE REGISTRO -->
        <form method="POST" class="row g-3 mb-5">
            <div class="col-md-3">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
            </div>
            <div class="col-md-3">
                <input type="email" name="correo" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="col-md-2">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <div class="col-md-2">
                <select name="rol" class="form-select" required>
                    <option value="">Rol...</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Editor">Editor</option>
                    <option value="Consultor">Consultor</option>
                </select>
            </div>
            <div class="col-md-2 text-center">
                <button type="submit" name="registrar" class="btn btn-success w-100">Registrar</button>
            </div>
        </form>

        <!-- LISTA DE USUARIOS -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT * FROM USUARIOS_LOGIN ORDER BY FechaRegistro DESC";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='text-center'>
                            <td>{$row['ID_Usuario']}</td>
                            <td>{$row['Nombre']}</td>
                            <td>{$row['Correo']}</td>
                            <td>{$row['Rol']}</td>
                            <td>{$row['FechaRegistro']}</td>
                            <td>
                                <a href='usuarios.php?eliminar={$row['ID_Usuario']}' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Eliminar este usuario?\");'>Eliminar</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center text-muted'>No hay usuarios registrados.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="panel_admin.php" class="btn btn-outline-success">⬅ Volver al Panel</a>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>