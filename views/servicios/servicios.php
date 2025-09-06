
            <h1>Servicios</h1>
            <div class="date">
                <input type="date">
            </div>

            <div class="filters">   
            <label for="search-order">Buscar Servicio:</label>
            <input type="text" id="search-order" name="search-order" placeholder="Ingrese un apellido">
            
            <button type="button" onclick="searchOrders()">Buscar</button>
        </div>
    </div>

    <button type="button" onclick="showModal()">Nuevo Servicio</button>

    <div id="newServiceModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Nuevo Servicio</h2>
        <form id="service-form">
            <label for="service-description">Descripción del Servicio:</label>
            <input type="text" id="service-description" name="service-description" required>
            
            <label for="service-cost">Costo del Servicio:</label>
            <input type="text" id="service-cost" name="service-cost" required>
            
            <div>
            <label for="service-weighing">¿Incluye Pesaje?</label>
            <select id="service-weighing" name="service-weighing" required>
                <option value="si">Sí</option>
                <option value="no">No</option>
            </select>
            </div>
            <button type="submit">Guardar Servicio</button>
        </form>
    </div>
</div>


            <div class="recent-order">
                <h2>Servicios</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Costo</th>
                            <th>Pesaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Lavado normal</td>
                            <td>$0.50 por libra</td>
                            <td>si</td>
                            <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
                        </tr>
                        <tr>
                            <td>Lavado en seco</td>
                            <td>EStimado por tipo de prenda</td>
                            <td>No</td>
                            <td>
                    <button class="editar-btn" onclick="editClient(this)"><i class="material-icons-sharp">edit</i> </button>
                    <button class="eliminar-btn" onclick="deleteClient(this)"><i class="material-icons-sharp">delete</i> </button>
                </td>
                        </tr>
                        <tr>
                            <td>Lavado de alfombras</td>
                            <td>EStimado por tamaño</td>
                            <td>No</td>
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
        document.getElementById('newServiceModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('newServiceModal').style.display = "none";
    }
</script>