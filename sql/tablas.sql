

CREATE TABLE configuracion (
  id_configuracion INTEGER PRIMARY KEY AUTOINCREMENT,
  clave VARCHAR(50) NOT NULL,
  valor VARCHAR(255) NOT NULL
);

CREATE TABLE correo (
  id_correo INTEGER PRIMARY KEY AUTOINCREMENT,
  email VARCHAR(50) NOT NULL
);


CREATE TABLE eventos (
  id_evento INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre_evento VARCHAR(100) NOT NULL,
  fecha_evento DATE NOT NULL,
  descripcion TEXT
);

CREATE TABLE horarios (
  id_horario INTEGER PRIMARY KEY AUTOINCREMENT,
  id_aula INTEGER NOT NULL,
  dia_semana TEXT CHECK( dia_semana IN ('lunes','martes','miércoles','jueves','viernes','sábado','domingo') ) NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  actividad VARCHAR(100) NOT NULL,
  FOREIGN KEY (id_aula) REFERENCES aulas (id_aula)
);

CREATE TABLE notificaciones (
  id_notificacion INTEGER PRIMARY KEY AUTOINCREMENT,
  id_representante INTEGER NOT NULL,
  mensaje TEXT NOT NULL,
  fecha_envio DATETIME NOT NULL,
  estado TEXT CHECK( estado IN ('enviada','leida') ) DEFAULT 'enviada' NOT NULL,
  FOREIGN KEY (id_representante) REFERENCES representantes (id_representante)
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


