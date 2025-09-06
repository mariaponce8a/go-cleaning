<h1>Clientes</h1>

<!-- Modal para nuevo cliente -->
<div id="newClientModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeClientForm()">&times;</span>
        <h2>Nuevo Cliente</h2>
        <form id="client-form">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre"><br>
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido"><br>
            <label for="tipo-id">Tipo de Identificación:</label>
            <select id="tipo-id" name="tipo-id">
                <option value="ci">CI</option>
                <option value="ruc">RUC</option>
            </select><br>
            <label for="identificacion">Identificación:</label>
            <input type="text" id="identificacion" name="identificacion"><br>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono"><br>
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo"><br>
            <button type="button" onclick="closeClientForm()">Cerrar</button>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<div class="filters">   
            <label for="search-order">Buscar Cliente:</label>
            <input type="text" id="search-order" name="search-order" placeholder="Ingrese ID o nombre del cliente">
            
            <button type="button" onclick="searchOrders()">Buscar</button>
        </div>
    </div>

    <div>
<button type="button" onclick="showClientForm()">Nuevo Cliente</button>
</div>
<!-- Tabla de clientes -->
<div class="recent-order">
    <h2>Lista de clientes</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Tipo de Identificación</th>
                <th>Identificación</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Carlos</td>
                <td>Bustamante</td>
                <td>CI</td>
                <td>098976554</td>
                <td>0990089990</td>
                <td>cbustamante@hotmail.com</td>
                <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
            </tr>
            <tr>
                <td>Amelia</td>
                <td>Casas</td>
                <td>CI</td>
                <td>2356777651</td>
                <td>0996776667</td>
                <td>amelia@hotmail.com</td>
                <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
            </tr>
            <tr>
                <td>Francisco</td>
                <td>Ramirez</td>
                <td>RUC</td>
                <td>1788896789001</td>
                <td>0995656441</td>
                <td>francisco@hotmail.com</td>
                <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
            </tr>
            <tr>
                <td>Christian</td>
                <td>Fonseca</td>
                <td>CI</td>
                <td>1700098765</td>
                <td>0997800644</td>
                <td>christian@hotmail.com</td>
                <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
            </tr>
            <!-- Se seguirán agregando filas aquí -->
        </tbody>
    </table>
    <a href="#">Mostrar todo</a>
</div>

<script>
    function showClientForm() {
        document.getElementById('newClientModal').style.display = "block";
    }

    function closeClientForm() {
        document.getElementById('newClientModal').style.display = "none";
    }

    function editClient(button) {
        // Obtener la fila actual del cliente
        var row = button.parentNode.parentNode;
        var cells = row.getElementsByTagName("td");
        
        // Obtener valores actuales
        var nombre = cells[0].innerText;
        var apellido = cells[1].innerText;
        var tipoId = cells[2].innerText;
        var identificacion = cells[3].innerText;
        var telefono = cells[4].innerText;
        var correo = cells[5].innerText;
        
        // Llenar el formulario de edición
        document.getElementById('nombre').value = nombre;
        document.getElementById('apellido').value = apellido;
        document.getElementById('tipo-id').value = tipoId;
        document.getElementById('identificacion').value = identificacion;
        document.getElementById('telefono').value = telefono;
        document.getElementById('correo').value = correo;
        
        // Mostrar el formulario de edición
        document.getElementById('newClientModal').style.display = "block";
    }

    function deleteClient(button) {
        // Obtener la fila actual del cliente y eliminarla
        var row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    document.getElementById('client-form').addEventListener('submit', function(event) {
        event.preventDefault();
        // Implementar lógica para guardar el nuevo cliente o actualizar cliente existente
        closeClientForm(); // Cerrar el modal después de guardar o actualizar
    });

    // Cerrar el modal cuando el usuario hace clic fuera del contenido del modal
    window.onclick = function(event) {
        var modal = document.getElementById('newClientModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>