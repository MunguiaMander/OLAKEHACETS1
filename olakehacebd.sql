-- Crear la base de datos 'olakehace' si no existe
CREATE DATABASE IF NOT EXISTS olakehace;

-- Cambiar a la base de datos 'olakehace'
USE olakehace;

-- Instrucciones de creación de tablas y relaciones

-- Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE,
    created_at TIMESTAMP
);

-- Estados de usuarios
CREATE TABLE user_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE
);

-- Usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role_id INT,
    status_id INT,
    post_aprvd INT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (status_id) REFERENCES user_statuses(id)
);

-- Categorías de publicaciones
CREATE TABLE post_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    created_at TIMESTAMP
);

-- Estados de publicaciones
CREATE TABLE post_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE
);

-- Publicaciones
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category_id INT,
    title VARCHAR(255),
    description TEXT,
    status_id INT,
    reports_count INT DEFAULT 0,
    created_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES post_categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (status_id) REFERENCES post_statuses(id)
);

-- Eventos
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT,
    event_date DATE,
    event_time TIME,
    location VARCHAR(255),
    capacity INT,
    audience_type VARCHAR(100),
    url VARCHAR(255),
    created_at TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

-- Asistencias
CREATE TABLE attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Notificaciones
CREATE TABLE notification_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) UNIQUE
);

-- Notificaciones
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    type_id INT,
    notification_message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (type_id) REFERENCES notification_types(id)
);


-- Reportes
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    post_id INT,
    reason TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

-- Ejemplo de inserts (opcional)

-- Insertar roles iniciales
INSERT INTO roles (name, created_at) VALUES ('Admin', NOW()), ('Publicador', NOW()), ('Registrado', NOW());

-- Insertar estados de usuarios
INSERT INTO user_statuses (status_name) VALUES ('Activo'), ('Suspendido'), ('Inactivo');


-- Trigger para actualizar el contador de reportes
DELIMITER //

CREATE TRIGGER update_reports_count
AFTER INSERT ON reports
FOR EACH ROW
BEGIN
    UPDATE posts
    SET reports_count = reports_count + 1
    WHERE id = NEW.post_id;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER decrement_reports_count
AFTER DELETE ON reports
FOR EACH ROW
BEGIN
    UPDATE posts
    SET reports_count = reports_count - 1
    WHERE id = OLD.post_id;
END //

DELIMITER ;

-- Vista para visualizar publicaciones reportadas
CREATE VIEW reported_posts AS
SELECT p.id AS post_id, p.title, p.description, p.reports_count, u.name AS posted_by
FROM posts p
JOIN users u ON p.user_id = u.id
WHERE p.reports_count > 0;
