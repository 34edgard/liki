

CREATE TABLE correo (
  id_correo INTEGER PRIMARY KEY AUTOINCREMENT,
  email VARCHAR(50) NOT NULL
);



CREATE TABLE roles (
  id_rol INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre_rol VARCHAR(30) NOT NULL
);

 
CREATE TABLE usuario (
  id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
  cedula INTEGER NOT NULL,
  nombres VARCHAR(30) NOT NULL,
  apellidos VARCHAR(30) NOT NULL,
  id_rol INTEGER NOT NULL,
  usuario VARCHAR(30) NOT NULL,
  id_correo INTEGER NOT NULL,
  contrasena VARCHAR(166) NOT NULL,
  estado TEXT CHECK( estado IN ('activo','inactivo') ) DEFAULT 'activo' NOT NULL,
  FOREIGN KEY (id_rol) REFERENCES roles (id_rol),
  FOREIGN KEY (id_correo) REFERENCES correo (id_correo)
);