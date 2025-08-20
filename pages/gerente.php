<!DOCTYPE html>
<html lang="en">

<head>
    <title>AndeSport</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/administrador.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <!-- Encabezado -->
    <header class="p-3 mb-3 border-bottom">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo">AndeSport Gerencia</h1>
            <button class="btn btn-primary" onclick="window.location.href='../index.php'">Cerrar Sesión</button>
        </div>
    </header>

    <div class="container">
        <!-- Sección de navegación -->
        <ul class="nav nav-pills mb-3" id="adminTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="ventas-tab" data-bs-toggle="pill" href="#ventas" role="tab">Ventas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="estadisticas-tab" data-bs-toggle="pill" href="#estadisticas"
                    role="tab">Estadísticas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="clientes-tab" data-bs-toggle="pill" href="#clientes" role="tab">Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="proveedores-tab" data-bs-toggle="pill" href="#proveedores"
                    role="tab">Proveedores</a>
            </li>
        </ul>

        <!-- Contenido de las secciones -->
        <div class="tab-content" id="adminTabsContent">

            <!-- Sección de Ventas -->
            <div class="tab-pane fade" id="ventas" role="tabpanel">
                <h2 class="mb-3">Ventas</h2>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de datos -->
                        <tr>
                            <td>201</td>
                            <td>Mochila de Montaña</td>
                            <td>3</td>
                            <td>$450</td>
                            <td>2024-10-28</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Sección de Estadísticas -->
            <div class="tab-pane fade" id="estadisticas" role="tabpanel">
                <h2 class="mb-3">Estadísticas</h2>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Ventas Totales</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de datos -->
                        <tr>
                            <td>Carpa Trekking</td>
                            <td>120</td>
                            <td>$300,000</td>
                        </tr>
                        <tr>
                            <td>Mochila Montaña</td>
                            <td>80</td>
                            <td>$120,000</td>
                        </tr>
                        <tr>
                            <td>Saco de Dormir</td>
                            <td>200</td>
                            <td>$200,000</td>
                        </tr>
                    </tbody>
                </table>            
                <canvas id="ventasChart" width="600" height="400"></canvas>    
            </div>

            <!-- Nueva Sección de Clientes -->
            <div class="tab-pane fade" id="clientes" role="tabpanel">
                <h2 class="mb-3">Clientes</h2>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Cliente</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de clientes -->
                        <tr>
                            <td>101</td>
                            <td>Lucía López</td>
                            <td>lucia.lopez@email.com</td>
                            <td>+54 9 1234 5678</td>
                        </tr>
                        <tr>
                            <td>102</td>
                            <td>Gonzalo Romero</td>
                            <td>l1231@email.com</td>
                            <td>+54 9 1234 5678</td>
                        </tr>
                        <tr>
                            <td>103</td>
                            <td>Juan Lopez</td>
                            <td>l23432z@email.com</td>
                            <td>+54 9 1234 5678</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Nueva Sección de Proveedores -->
            <div class="tab-pane fade" id="proveedores" role="tabpanel">
                <h2 class="mb-3">Proveedores</h2>
                <a href="agregar-proveedor-gerente.php"><button class="btn btn-success mb-2">Agregar Proveedor</button></a>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Proveedor</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Outdoor Supplies</td>
                            <td>Juan Castro</td>
                            <td>+54 9 8765 4321</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Nike</td>
                            <td>Gonzalo Romero</td>
                            <td>+54 9 8765 6676</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Columbia</td>
                            <td>Tania Quiroga</td>
                            <td>+54 9 8765 4214</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <!-- Ejemplos de proveedores -->
                        <tr>
                            <td>501</td>
                            <td>Salomon</td>
                            <td>Anahi Barrionuevo</td>
                            <td>+54 9 8765 1231</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>501</td>
                            <td>THE NORTH FACE</td>
                            <td>Lucas Barrojo</td>
                            <td>+54 9 8765 1324</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Nueva Sección de Usuarios -->
            <div class="tab-pane fade" id="usuarios" role="tabpanel">
                <h2 class="mb-3">Usuarios</h2>
                <a href="nuevousuario.php"><button class="btn btn-success mb-2">Agregar Usuario</button></a>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Usuario</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ejemplos de usuarios -->
                        <tr>
                            <td>1</td>
                            <td>Ana Gómez</td>
                            <td>ana.gomez@andesport.com</td>
                            <td>Administrador</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Lucas Martínez</td>
                            <td>lucas.martinez@andesport.com</td>
                            <td>Encargado de stock</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Martina Ruiz</td>
                            <td>martina.ruiz@andesport.com</td>
                            <td>Repartidor</td>
                            <td>
                                <button class="btn btn-primary btn-sm">Editar</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const ctx = document.getElementById('ventasChart').getContext('2d');
        const ventasChart = new Chart(ctx, {
            type: 'pie', // Tipo de gráfico: bar, line, pie, etc.
            data: {
                labels: ['Carpa Trekking', 'Mochila Montaña', 'Saco de Dormir'], // Productos
                datasets: [{
                    label: 'Porcentaje de Ventas',
                    data: [120, 80, 200], // Cantidades de ventas
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'], // Colores
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Porcentaje de Ventas por Producto'
                    }
                }
            }
        });
    </script>

</body>

</html>