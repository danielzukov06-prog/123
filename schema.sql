-- Andmebaasi skeem (MySQL 8+)
CREATE TABLE roles (
id TINYINT UNSIGNED PRIMARY KEY,
name VARCHAR(20) NOT NULL UNIQUE
);
INSERT INTO roles (id, name) VALUES (1, 'user'), (2, 'admin');


CREATE TABLE users (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
first_name VARCHAR(60) NOT NULL,
last_name VARCHAR(60) NOT NULL,
personal_id CHAR(11) NOT NULL UNIQUE,
email VARCHAR(191) NOT NULL UNIQUE,
password_hash VARCHAR(255) NOT NULL,
role_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
);


CREATE TABLE trainings (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(120) NOT NULL,
date DATE NOT NULL,
starts_at TIME NOT NULL,
duration_minutes SMALLINT UNSIGNED NOT NULL,
max_participants SMALLINT UNSIGNED NOT NULL,
created_by INT UNSIGNED,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
INDEX idx_datetime (date, starts_at),
CONSTRAINT fk_trainings_created_by FOREIGN KEY (created_by) REFERENCES users(id)
);


CREATE TABLE registrations (
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_id INT UNSIGNED NOT NULL,
training_id INT UNSIGNED NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
UNIQUE KEY uq_user_training (user_id, training_id),
CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
CONSTRAINT fk_reg_training FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
);