<?php
include("../includes/header.php");
include("../conexion.php");

// --- AGREGAR SERVICIO (MySQLi) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"]) && $_POST["accion"] == "agregar") {
    $nombre      = $_POST["nombre"] ?? '';
    $descripcion = $_POST["descripcion"] ?? '';
    $precio      = $_POST["precio"] ?? '';

    if (!empty($nombre) && !empty($descripcion) && !empty($precio)) {

        // Usamos consulta preparada
        $sql  = "INSERT INTO SERVICIO (Nombre, Descripcion, Precio) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // "s" = string, "d" = double/decimal
            mysqli_stmt_bind_param($stmt, "ssd", $nombre, $descripcion, $precio);
            $ok = mysqli_stmt_execute($stmt);

            if ($ok) {
                echo "<div class='alert alert-success text-center'>✅ Servicio agregado correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger text-center'>❌ Error al agregar servicio.</div>";
                // echo mysqli_error($conn); // descomenta para debug
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "<div class='alert alert-danger text-center'>❌ Error al preparar la consulta.</div>";
            // echo mysqli_error($conn);
        }
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Completa todos los campos.</div>";
    }
}

// --- ELIMINAR SERVICIO (MySQLi) ---
if (isset($_GET["eliminar"])) {
    $id = intval($_GET["eliminar"]); // Convertimos a int por seguridad

    $sql  = "DELETE FROM SERVICIO WHERE ID_Servicio = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $ok = mysqli_stmt_execute($stmt);

        if ($ok) {
            echo "<div class='alert alert-success text-center'>🗑️ Servicio eliminado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>❌ No se pudo eliminar el servicio.</div>";
            // echo mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='alert alert-danger text-center'>❌ Error al preparar la consulta de eliminación.</div>";
        // echo mysqli_error($conn);
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0 p-5" style="border-radius: 20px;">
        <h2 class="text-success text-center mb-4">💼 Gestión de Servicios</h2>
        <p class="text-center text-muted mb-4">Administra los servicios ofrecidos por la veterinaria.</p>

        <!-- Formulario para agregar servicios -->
        <form method="POST" class="row g-3 mb-4 justify-content-center">
            <input type="hidden" name="accion" value="agregar">
            <div class="col-md-3">
                <input type="text" class="form-control" name="nombre" placeholder="Nombre del servicio" required>
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="descripcion" placeholder="Descripción" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="precio" placeholder="Precio" step="0.01" required>
            </div>
            <div class="col-md-2 text-center">
                <button type="submit" class="btn btn-success w-100">➕ Agregar</button>
            </div>
        </form>

        <!-- Tabla de servicios -->
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Listar servicios (MySQLi)
                    $sql  = "SELECT * FROM SERVICIO ORDER BY ID_Servicio ASC";
                    $stmt = mysqli_query($conn, $sql);

                    if ($stmt && mysqli_num_rows($stmt) > 0) {
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            echo "
                                <tr class='text-center'>
                                    <td>{$row['ID_Servicio']}</td>
                                    <td>" . htmlspecialchars($row['Nombre']) . "</td>
                                    <td>" . htmlspecialchars($row['Descripcion']) . "</td>
                                    <td>$" . number_format($row['Precio'], 2) . "</td>
                                    <td>
                                        <a href='?eliminar={$row['ID_Servicio']}' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Eliminar este servicio?')\">🗑️ Eliminar</a>
                                    </td>
                                </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>⚠️ No hay servicios registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="panel_admin.php" class="btn btn-outline-success px-4">⬅️ Volver al Panel</a>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
