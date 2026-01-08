

INSERT INTO correo (id_correo, email) VALUES ('1', 'correoDePrueva_1@gmail.com');
INSERT INTO correo (id_correo, email) VALUES ('2', 'correoDePrueva_2@gmail.com');

INSERT INTO roles (id_rol, nombre_rol) VALUES ('1', 'ADMINISTRADOR');
INSERT INTO roles (id_rol, nombre_rol) VALUES ('2', 'SECRETARIA');




INSERT INTO usuario (id_usuario, cedula, nombres, apellidos, id_rol, usuario, id_correo, contrasena, estado) VALUES ('1', '309309', 'Josel', 'Alvarez Lopez ', '1', 'Usuario_1', '1', '$2y$10$Iqpu.MxyGyUPnaLSyV7bAup6u//h7N1X8wRi0wneO2KH5yS8AIWpC', 'activo');
INSERT INTO usuario (id_usuario, cedula, nombres, apellidos, id_rol, usuario, id_correo, contrasena, estado) VALUES ('2', '390390', 'Carlosh', 'Marcano', '1', '390390', '2', '$2y$10$Fs0/0/M.4u3L.QZ8FRtXduHd1DgEKI4bBOAmZYhiacwTdGyNoyxlq', 'activo');


