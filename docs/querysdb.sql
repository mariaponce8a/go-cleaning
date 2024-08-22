-- CONSULTAR USUARIO
select * from tb_usuarios_plataforma;

-- CREAR USUARIO
insert into tb_usuarios_plataforma 
(usuario, nombre, apellido, perfil, clave) 
values ('jojip_a','JOJI', 'PONCE', 'A', sha2('Admin1236*', 256));
select * from tb_usuarios_plataforma; 

-- EDITAR USUARIO 
update tb_usuarios_plataforma set
usuario = 'jojip_a', nombre ='JOJI', apellido='PONCE', perfil='A', clave=sha2('Admin1236', 256)
WHERE id_usuario= 7;
select * from tb_usuarios_plataforma;

-- ELIMINAR USUARIO
delete from tb_usuarios_plataforma where id_usuario = 29
select * from tb_usuarios_plataforma;