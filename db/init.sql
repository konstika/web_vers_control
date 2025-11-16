CREATE DATABASE IF NOT EXISTS vers_control_DB;
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT SELECT,UPDATE,INSERT,DELETE ON vers_control_DB.* TO 'user'@'%';
FLUSH PRIVILEGES;
SET time_zone = '+03:00';

USE vers_control_DB;
CREATE TABLE IF NOT EXISTS user (
    id_user INT NOT NULL AUTO_INCREMENT,
    login VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_user)
    );

CREATE TABLE IF NOT EXISTS project (
    id_project INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES user(id_user)
    );

CREATE TABLE IF NOT EXISTS version_project (
    id_version_project INT AUTO_INCREMENT PRIMARY KEY,
    id_project INT,
    name VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES user(id_user),
    FOREIGN KEY (id_project) REFERENCES project(id_project)
    );

CREATE TABLE IF NOT EXISTS history (
    id_history INT AUTO_INCREMENT PRIMARY KEY,
    id_project INT,
    id_version_project INT,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES user(id_user),
    FOREIGN KEY (id_project) REFERENCES project(id_project),
    FOREIGN KEY (id_version_project) REFERENCES version_project(id_version_project)
    );