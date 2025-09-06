<h1>Usuarios</h1>
<div class="date">
    <input type="date">
</div>

<div class="filters">   
            <label for="search-order">Buscar Usuario:</label>
            <input type="text" id="search-order" name="search-order" placeholder="Ingrese un apellido">
            
            <button type="button" onclick="searchOrders()">Buscar</button>
        </div>
    </div>

    <button type="button" onclick="showModal()">Nuevo Usuario</button>


    <div id="newUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Nuevo Usuario</h2>
        <form id="user-form">
            <label for="user-email">Usuario:</label>
            <input type="email" id="user-email" name="user-email" required>
            
            <label for="user-name">Nombre:</label>
            <input type="text" id="user-name" name="user-name" required>
            
            <label for="user-lastname">Apellido:</label>
            <input type="text" id="user-lastname" name="user-lastname" required>
            
            <div>
            <label for="user-profile">Perfil:</label>
            <select id="user-profile" name="user-profile" required>
                <option value="administrador">Administrador</option>
                <option value="usuario">Usuario</option>
            </select>
            </div>
            <button type="submit">Guardar Usuario</button>
        </form>
    </div>
</div>


<div class="recent-order">
    <h2>Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Perfil</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>john@hotmail.com</td>
                <td>John</td>
                <td>Macias</td>
                <td>Administrador</td>
                <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
            </tr>
            <tr>
                <td>alicia@hotmail.com</td>
                <td>Alicia</td>
                <td>Perez</td>
                <td>Usuario</td>
                <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
            </tr>
            <!-- se seguirán agregando filas aquí -->
        </tbody>
    </table>
    <a href="#">Mostrar todo</a>
</div>

<script>
    function showModal() {
        document.getElementById('newUserModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('newUserModal').style.display = "none";
    }
</script>