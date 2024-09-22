-- CONSULTAR USUARIO
select *
from tb_usuarios_plataforma;

-- CREAR USUARIO
insert into tb_usuarios_plataforma
    (usuario, nombre, apellido, perfil, clave)
values
    ('jojip_a', 'JOJI', 'PONCE', 'A', sha2('Admin1236*', 256));
select *
from tb_usuarios_plataforma;

-- EDITAR USUARIO 
update tb_usuarios_plataforma set
usuario = 'jojip_a', nombre ='JOJI', apellido='PONCE', perfil='A', clave=sha2('Admin1236', 256)
WHERE id_usuario= 7;
select *
from tb_usuarios_plataforma;

-- ELIMINAR USUARIO
delete from tb_usuarios_plataforma where id_usuario = 29
select *
from tb_usuarios_plataforma;


-- CONSULTAR PEDIDO
select
    p.id_pedido_cabecera, p.fecha_pedido, p.fk_id_usuario,
    u.usuario , p.cantidad_articulos,
    p.fk_id_cliente, c.identificacion_cliente, c.correo_cliente , c.nombre_cliente, c.apellido_cliente,
    p.fk_id_descuentos, d.tipo_descuento_desc , d.cantidad_descuento , p.pedido_subtotal, p.estado_pago, p.valor_pago,
    p.fecha_hora_recoleccion_estimada, p.direccion_recoleccion, p.fecha_hora_entrega_estimada,
    p.direccion_entrega, p.tipo_entrega
from tb_pedido p
    inner join tb_usuarios_plataforma u on u.id_usuario = p.fk_id_usuario
    inner join tb_clientes_registrados c on c.id_cliente = p.fk_id_cliente
    inner join tb_tipo_descuentos d on d.id_tipo_descuento = p.fk_id_descuentos
;


-- CREAR PEDIDO
INSERT INTO tb_pedido
    (
    fecha_pedido,
    fk_id_usuario,
    cantidad_articulos,
    fk_id_cliente,
    fk_id_descuentos,
    pedido_subtotal,
    estado_pago,
    valor_pago,
    fecha_hora_recoleccion_estimada,
    direccion_recoleccion,
    fecha_hora_entrega_estimada,
    direccion_entrega,
    tipo_entrega
    )
VALUES
    (
        CURRENT_TIMESTAMP, -- Para la fecha actual del pedido
        4, -- ID del usuario (valor ficticio)
        10, -- Cantidad de artículos (valor ficticio)
        2, -- ID del cliente (valor ficticio)
        2, -- ID del descuento (valor ficticio)
        150.75, -- Subtotal del pedido
        'P', -- Estado del pago F , P o C (PAGO AL FINALIZAR, PAGO PARCIAL y PAGO COMPLETO)
        150.75, -- Valor del pago
        '2024-09-02 10:00:00', -- Fecha y hora de recolección estimada
        'Av. Siempre Viva 123', -- Dirección de recolección
        '2024-09-04 15:00:00', -- Fecha y hora de entrega estimada
        'Calle Falsa 456', -- Dirección de entrega
        'D'         -- Tipo de entrega  D o L (DOMICILIO o LOCAL)
);


// creacion de pedido sp 

CREATE DEFINER=`usg8hrdab84mdg8a`@`%` PROCEDURE `InsertarPedidoConDetalle`(
    IN p_fecha_pedido DATETIME,
    IN p_fk_id_usuario INT,
    IN p_cantidad_articulos INT,
    IN p_fk_id_cliente INT,
    IN p_fk_id_descuentos INT,
    IN p_pedido_subtotal DECIMAL(10, 2),
    IN p_estado_pago VARCHAR(50),
    IN p_valor_pago DECIMAL(10, 2),
    IN p_fecha_recoleccion_estimada DATE,
    IN p_hora_recoleccion_estimada TIME,
    IN p_direccion_recoleccion VARCHAR(255),
    IN p_fecha_entrega_estimada DATE,
    IN p_hora_entrega_estimada TIME,
    IN p_direccion_entrega VARCHAR(255),
    IN p_tipo_entrega VARCHAR(50),
    IN p_total DECIMAL(10, 2),
    IN p_detalles JSON
)
BEGIN
    DECLARE v_id_pedido INT;
    DECLARE exit handler FOR SQLEXCEPTION
    BEGIN
        -- Si ocurre un error, se revierte la transacción
        ROLLBACK;
        SELECT 0 AS respuesta;
    END;

    -- Iniciar transacción
    START TRANSACTION;
    
    -- Insertar en tb_pedido
    INSERT INTO tb_pedido (
        fecha_pedido,
        fk_id_usuario,
        cantidad_articulos,
        fk_id_cliente,
        fk_id_descuentos,
        pedido_subtotal,
        estado_pago,
        valor_pago,
        fecha_recoleccion_estimada,
        hora_recoleccion_estimada,
        direccion_recoleccion,
        fecha_entrega_estimada,
        hora_entrega_estimada,
        direccion_entrega,
        tipo_entrega,
        total
    )
    VALUES (
        p_fecha_pedido,
        p_fk_id_usuario,
        p_cantidad_articulos,
        p_fk_id_cliente,
        p_fk_id_descuentos,
        p_pedido_subtotal,
        p_estado_pago,
        p_valor_pago,
        p_fecha_recoleccion_estimada,
        p_hora_recoleccion_estimada,
        p_direccion_recoleccion,
        p_fecha_entrega_estimada,
        p_hora_entrega_estimada,
        p_direccion_entrega,
        p_tipo_entrega,
        p_total
    );

    -- Obtener el ID del nuevo pedido
    SET v_id_pedido = LAST_INSERT_ID();
    
    -- Insertar en tb_pedido_detalle usando JSON
    INSERT INTO tb_pedido_detalle (fk_id_servicio, libras, precio_servicio, fk_id_pedido, descripcion_articulo, cantidad)
    SELECT
        JSON_UNQUOTE(JSON_EXTRACT(detail, '$.fk_id_servicio')),
        JSON_UNQUOTE(JSON_EXTRACT(detail, '$.libras')),
        JSON_UNQUOTE(JSON_EXTRACT(detail, '$.precio_servicio')),
        v_id_pedido,
        JSON_UNQUOTE(JSON_EXTRACT(detail, '$.descripcion_articulo')),
        JSON_UNQUOTE(JSON_EXTRACT(detail, '$.cantidad'))
    FROM JSON_TABLE(
        p_detalles,
        '$[*]' COLUMNS (
            detail JSON PATH '$'
        )
    ) AS t;

    -- Confirmar la transacción si todo es exitoso
    COMMIT;
    
    -- Mensaje de éxito
    SELECT 1 AS mensaje, v_id_pedido as idPedido;
    
    
END