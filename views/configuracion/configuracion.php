<?php
$pagina_actual = 'Configuración'; // Definir la página actual

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../html/layout.php') ?>
</head>
    <main class="main-content">
      
        <div class="recent-order">
            <h2>Servicios</h2>
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Costo</th>
                        <th>Pesaje</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Lavado normal</td>
                        <td>$0.50 por libra</td>
                        <td>si</td>
                    </tr>
                    <tr>
                        <td>Lavado en seco</td>
                        <td>EStimado por tipo de prenda</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td>Lavado de alfombras</td>
                        <td>EStimado por tamaño</td>
                        <td>No</td>
                    </tr>
                    <!-- se seguirán agregando filas aquí -->
                </tbody>
            </table>
            <a href="#">Mostrar todo</a>
        </div>
    </main>


    
</html>
