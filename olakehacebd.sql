-- Crear la base de datos 'olakehace' si no existe
CREATE DATABASE IF NOT EXISTS olakehace;

-- Cambiar a la base de datos 'olakehace'
USE olakehace;

-- Limpiar tablas existentes en dado caso se vuelva a correr el script y restablecer el AUTO_INCREMENT
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE reports;
TRUNCATE TABLE notifications;
TRUNCATE TABLE notification_types;
TRUNCATE TABLE attendances;
TRUNCATE TABLE events;
TRUNCATE TABLE posts;
TRUNCATE TABLE post_statuses;
TRUNCATE TABLE post_categories;
TRUNCATE TABLE app_users;
TRUNCATE TABLE user_statuses;
TRUNCATE TABLE roles;

SET FOREIGN_KEY_CHECKS = 1;

-- Tabla de Roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Estados de Usuarios
CREATE TABLE IF NOT EXISTS user_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Usuarios (renombrada a 'app_users')
CREATE TABLE IF NOT EXISTS app_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status_id INT NOT NULL,
    post_aprvd INT DEFAULT 0,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (status_id) REFERENCES user_statuses(id)
);

-- Tabla de Categorias de Publicaciones
CREATE TABLE IF NOT EXISTS post_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Tabla de Estados de Publicaciones
CREATE TABLE IF NOT EXISTS post_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Publicaciones
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status_id INT NOT NULL,
    reports_count INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES post_categories(id),
    FOREIGN KEY (user_id) REFERENCES app_users(id),
    FOREIGN KEY (status_id) REFERENCES post_statuses(id)
);

-- Tabla de Eventos con campos 'created_at' y 'deleted_at'
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    audience_type VARCHAR(100) NOT NULL,
    url VARCHAR(255),
    image_path VARCHAR(255),
    status_id INT NOT NULL DEFAULT 1,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (status_id) REFERENCES post_statuses(id)
);

-- Tabla de Asistencias
CREATE TABLE IF NOT EXISTS attendances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES app_users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- Tabla de Tipos de Notificaciones
CREATE TABLE IF NOT EXISTS notification_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) UNIQUE NOT NULL
);

-- Tabla de Notificaciones
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    type_id INT NOT NULL,
    notification_message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES app_users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (type_id) REFERENCES notification_types(id)
);

-- Tabla de Reportes
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    reason TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES app_users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

-- Insertar roles iniciales
INSERT INTO roles (name) VALUES
('Admin'),
('Publicador'),
('Registrado');

-- Insertar estados de usuarios
INSERT INTO user_statuses (status_name) VALUES
('Activo'),
('Suspendido'),
('Inactivo');

-- Insertar estados de publicaciones
INSERT INTO post_statuses (status_name) VALUES
('Publicado'),
('Oculto'),
('Baneado');

-- Insertar categorías de publicaciones
INSERT INTO post_categories (name) VALUES
('Concierto'),
('Conferencia'),
('Deporte'),
('Festival');

-- Trigger para actualizar el contador de reportes al insertar un nuevo reporte
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

-- Trigger para decrementar el contador de reportes al eliminar un reporte
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

-- Trigger para setear una publicacion como "Oculta" cuando tenga más de 3 reportes
DELIMITER //

DROP TRIGGER IF EXISTS ban_post_if_reported;
CREATE TRIGGER ban_post_if_reported
AFTER INSERT ON reports
FOR EACH ROW
BEGIN
    DECLARE report_count INT;

    -- Obtener el numero de reportes de la publicación
    SELECT reports_count INTO report_count
    FROM posts
    WHERE id = NEW.post_id;

    IF report_count > 3 THEN
        -- Actualizar el status_id de la publicacion a "Oculta" (status_id = 3)
        UPDATE posts
        SET status_id = 3
        WHERE id = NEW.post_id;

        -- Actualizar el status_id del evento relacionado
        UPDATE events
        SET status_id = 3
        WHERE post_id = NEW.post_id;
    END IF;
END //

DELIMITER ;

-- Vista para visualizar publicaciones reportadas
CREATE OR REPLACE VIEW reported_posts AS
SELECT
    p.id AS post_id,
    p.title,
    p.description,
    p.reports_count,
    u.name AS posted_by
FROM
    posts p
JOIN
    app_users u ON p.user_id = u.id
WHERE
    p.reports_count > 0;

-- Procedimiento almacenado para obtener eventos próximos
DELIMITER //

CREATE PROCEDURE get_upcoming_events()
BEGIN
    SELECT
        e.id,
        p.title,
        p.description,
        e.event_date,
        e.event_time,
        e.location,
        e.image_path,
        e.created_at,
        e.deleted_at
    FROM
        events e
    JOIN
        posts p ON e.post_id = p.id
    WHERE
        e.event_date >= CURDATE()
    ORDER BY
        e.event_date ASC;
END //

DELIMITER ;

-- Ejemplo de inserts para fines de pruebas unitarias
INSERT INTO app_users (name, email, password, role_id, status_id) VALUES
('Marco Munguia', 'marco.munguia@example.com', 'hashed_password', 2, 1),
('Pancho Pistolas', 'pancho.pistolas@example.com', 'hashed_password', 3, 1);

INSERT INTO posts (user_id, category_id, title, description, status_id) VALUES
(1, 1, 'Festival de Musica', 'Un gran festival con artistas internacionales.', 1),
(2, 2, 'Conferencia de Tecnologia', 'Aprende sobre las ultimas tendencias en tecnologia.', 1);

INSERT INTO events (post_id, event_date, event_time, location, capacity, audience_type, url, image_path) VALUES
(1, '2024-11-20', '18:00:00', 'Parque Central', 5000, 'Publico General', 'http://festivalmusica.com', 'event-images/eventoejemplouno.png'),
(2, '2024-12-05', '09:00:00', 'Centro de Convenciones', 300, 'Profesionales', 'http://techtalks.com', 'event-images/eventoejemplodos.png');
