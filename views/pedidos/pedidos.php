<h1>Pedidos</h1>


        <!-- Filtros de búsqueda -->
        <div class="filters">
            <label for="search-date">Buscar por Fecha:</label>
            <input type="date" id="search-date" name="search-date">
            
            <label for="search-order">Buscar por Pedido:</label>
            <input type="text" id="search-order" name="search-order" placeholder="Ingrese ID o nombre del cliente">
            
            <button type="button" onclick="searchOrders()">Buscar</button>
        </div>
    </div>


<!-- Botón para mostrar el formulario de nuevo pedido -->
<button type="button" onclick="showModal()">Nuevo Pedido</button>

<!-- Modal para nuevo pedido -->
<div id="newOrderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Nuevo Pedido</h2>
        <form id="order-form">
            <label for="order-date">Fecha del Pedido:</label>
            <input type="date" id="order-date" name="order-date" required>
            
            <label for="order-user">Usuario:</label>
            <input type="text" id="order-user" name="order-user" required>
            
            <label for="client-phone">Teléfono del Cliente:</label>
            <input type="text" id="client-phone" name="client-phone" required maxlength="10">
            
            <label for="client-email">Correo del Cliente:</label>
            <input type="email" id="client-email" name="client-email">
            
            <label for="order-discount">Descuento:</label>
            <input type="text" id="order-discount" name="order-discount">
            
            <label for="order-subtotal">Subtotal del Pedido:</label>
            <input type="number" id="order-subtotal" name="order-subtotal" step="0.01" required>
            
            <label for="order-items">Cantidad de Artículos:</label>
            <input type="number" id="order-items" name="order-items" required>
            
            <label for="order-client">Cliente:</label>
            <input type="text" id="order-client" name="order-client" required>
            
            <button type="submit">Guardar Pedido</button>
        </form>
    </div>
</div>

<!-- Tabla de pedidos -->
<div class="recent-order">
    <h2>Pedidos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha del Pedido</th>
                <th>Usuario</th>
                <th>Teléfono del Cliente</th>
                <th>Correo del Cliente</th>
                <th>Descuento</th>
                <th>Subtotal del Pedido</th>
                <th>Cantidad de Artículos</th>
                <th>Cliente</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            <tr>
                <td>2024-07-14</td>
                <td>Juan Pérez</td>
                <td>0998765432</td>
                <td>juan.perez@example.com</td>
                <td>10%</td>
                <td>$50.00</td>
                <td>5</td>
                <td>María López</td>
                <td>
                <button class="editar-btn"><i class="material-icons-sharp">edit</i> </button>
<button class="eliminar-btn"><i class="material-icons-sharp">delete</i> </button>

    </td>
            </tr>
            <tr>
                <td>2024-07-13</td>
                <td>Ana García</td>
                <td>0987654321</td>
                <td>ana.garcia@example.com</td>
                <td>5%</td>
                <td>$30.00</td>
                <td>3</td>
                <td>Carlos Bustamante</td>
                <td>
                <button class="editar-btn"><i class="material-icons-sharp">edit</i> </button>
<button class="eliminar-btn"><i class="material-icons-sharp">delete</i> </button>

    </td>
            </tr>
            <!-- se seguirán agregando filas aquí -->
        </tbody>
    </table>
    <a href="#">Mostrar todo</a>
</div>

<script>
    function showModal() {
        document.getElementById('newOrderModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('newOrderModal').style.display = "none";
    }

    function searchOrders() {
    }

    function editOrder(button) {
    var row = button.parentNode.parentNode;
    var cells = row.getElementsByTagName("td");

    // Obtener los valores actuales de la fila
    var orderDate = cells.item(0).innerText.trim();
    var orderUser = cells.item(1).innerText.trim();
    var clientPhone = cells.item(2).innerText.trim();
    var clientEmail = cells.item(3).innerText.trim();   
    var orderDiscount = cells.item(4).innerText.trim();
    var orderSubtotal = cells.item(5).innerText.trim();
    var orderItems = cells.item(6).innerText.trim();
    var orderClient = cells.item(7).innerText.trim();

    // Llenar el formulario de pedido con los valores actuales
    document.getElementById('order-date').value = orderDate;
    document.getElementById('order-user').value = orderUser;
    document.getElementById('client-phone').value = clientPhone;
    document.getElementById('client-email').value = clientEmail;
    document.getElementById('order-discount').value = orderDiscount;
    document.getElementById('order-subtotal').value = orderSubtotal;
    document.getElementById('order-items').value = orderItems;
    document.getElementById('order-client').value = orderClient;

    // Mostrar el modal de editar pedido
    showModal();
}
    document.getElementById('order-form').addEventListener('submit', function(event) {
        event.preventDefault();
        // Implementar lógica para guardar el pedido editado aquí
        closeModal(); // Cerrar el modal después de guardar o actualizar
    });

    // Cerrar el modal cuando el usuario hace clic fuera del contenido del modal
    window.onclick = function(event) {
        var modal = document.getElementById('newOrderModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
</script>