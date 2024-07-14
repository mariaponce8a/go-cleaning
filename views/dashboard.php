
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
</head>

<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo" id="logo">
                    <img src="../public/img/logo.png" alt="Logo">
                    <h2><span>BURBUJA</span> DE <span>SEDA</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar">
                <div style="gap: 20px; display: flex; justify-content: space-between; flex-flow: column; gap: 15px;">
                    <button id="menu-btn">
                        <span class="material-icons-sharp">menu</span>
                    </button>
                    <a href="dashboard.php" class="<?php echo ($pagina_actual == 'Dashboard') ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">grid_view</span>
                        <h3>Dashboard</h3>
                    </a>
                    <a href="./usuarios/usuarios.php" class="<?php echo ($pagina_actual == 'Usuarios') ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">person_outline</span>
                        <h3>Usuarios</h3>
                    </a>
                    <a href="././pedidos/pedidos.php" class="<?php echo ($pagina_actual == 'Pedidos') ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">local_laundry_service</span>
                        <h3>Pedidos</h3>
                    </a>
                    <a href="././servicios/servicios.php" class="<?php echo ($pagina_actual == 'Servicios') ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">workspaces</span>
                        <h3>Servicios</h3>
                    </a>
                    <a href="././clientes/clientes.php" class="<?php echo ($pagina_actual == 'Clientes') ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">person_search</span>
                        <h3>Clientes</h3>
                    </a>
                    <a href="././configuracion/configuracion.php" class="<?php echo ($pagina_actual == 'Configuración') ? 'active' : ''; ?>">
                        <span class="material-icons-sharp">settings</span>
                        <h3>Configuración</h3>
                    </a>
                </div>
                <a style="position: absolute; bottom:0; margin-left: 8px; padding: 15px;" href="">
                    <span class="material-icons-sharp">logout</span>
                    <h3>Cerrar sesión</h3>
                </a>
            </div>
        </aside>
    
        <main class="main-content">
            <h1>Dashboard</h1>
            <div class="date">
                <input type="date">
            </div>
            <div class="recent-order">
                <h2>Pedidos Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Pérez</td>
                            <td>2024-07-10</td>
                            <td>Limpieza en seco</td>
                            <td>Completado</td>
                        </tr>
                        <tr>
                            <td>María Fonseca</td>
                            <td>2024-07-10</td>
                            <td>Limpieza en seco</td>
                            <td>Pendiente</td>
                        </tr>
                        <!-- se seguirán agregando filas aquí -->
                </table>
                <a href="#">Mostrar todo</a>
            </div>
        </main>
        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-icons-sharp">menu</span>
                </button>
                <div class="theme-toggler">
                    <span class="material-icons-sharp active">light_mode</span>
                    <span class="material-icons-sharp">dark_mode</span>
                </div>
                <div class="profile-photo">
                    <img src="" alt="">
                </div>
            </div>

            <div class="recent-updates">
                <h2>Actualizaciones Recientes</h2>
                <div class="update">
                    <div class="profile-photo">
                        <img src="" alt="">
                    </div>
                    <div class="message">
                        <p><b>María Ponce</b> entregó el pedido de la alfombra para Sebas</p>
                        <small class="text-muted">Hace dos minutos</small>
                    </div>
                </div>
                <!-- Agrega más actualizaciones aquí -->
            </div>

            <div class="sales-analytics">
                <h2>Entregas realizadas</h2>
                <div class="item">
                    <div class="icon">
                        <span class="material-icons-sharp">shopping_cart</span>
                    </div>
                    <div class="right">
                        <div class="info">
                            <h3>ORDEN</h3>
                            <small class="text-muted">Últimas 24 horas</small>
                        </div>
                        <h5 class="success">+39%</h5>
                        <h3>3849</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
            
    <script src="dashboard.js"></script>

</html>