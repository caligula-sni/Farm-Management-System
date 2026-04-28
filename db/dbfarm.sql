DROP DATABASE IF EXISTS dbfarm2;
-- Create database
CREATE DATABASE IF NOT EXISTS dbfarm2;
USE dbfarm2;

-- =========================
-- BASE TABLES (NO DEPENDENCIES)
-- =========================

CREATE TABLE IF NOT EXISTS tbrole (
    role_id INT UNSIGNED AUTO_INCREMENT,
    role_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (role_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbprovince (
    province_id INT UNSIGNED AUTO_INCREMENT,
    province_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (province_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbcitymuni (
    cm_id INT UNSIGNED AUTO_INCREMENT,
    cm_name VARCHAR(45) NOT NULL,
    province_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (cm_id),
    FOREIGN KEY (province_id) REFERENCES tbprovince(province_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbcrop (
    crop_id INT UNSIGNED AUTO_INCREMENT,
    crop_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (crop_id)
) ENGINE=InnoDB;

-- =========================
-- DEPENDENT TABLES
-- =========================

CREATE TABLE IF NOT EXISTS tbuser (
    id INT UNSIGNED AUTO_INCREMENT,
    UserName VARCHAR(45) NOT NULL,
    PassWord VARCHAR(255) NOT NULL,
    FullName VARCHAR(45) NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    province_id INT UNSIGNED NOT NULL,
    cm_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (role_id) REFERENCES tbrole(role_id),
    FOREIGN KEY (province_id) REFERENCES tbprovince(province_id),
    FOREIGN KEY (cm_id) REFERENCES tbcitymuni(cm_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbfarm (
    farm_id INT UNSIGNED AUTO_INCREMENT,
    id INT UNSIGNED NOT NULL,
    province_id INT UNSIGNED NOT NULL,
    cm_id INT UNSIGNED NOT NULL,
    farm_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (farm_id),
    FOREIGN KEY (id) REFERENCES tbuser(id),
    FOREIGN KEY (province_id) REFERENCES tbprovince(province_id),
    FOREIGN KEY (cm_id) REFERENCES tbcitymuni(cm_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tbfarmsupply (
    fs_id INT UNSIGNED AUTO_INCREMENT,
    farm_id INT UNSIGNED NOT NULL,
    crop_id INT UNSIGNED NOT NULL,
    fs_quantity VARCHAR(45) NOT NULL,
    PRIMARY KEY (fs_id),
    FOREIGN KEY (farm_id) REFERENCES tbfarm(farm_id),
    FOREIGN KEY (crop_id) REFERENCES tbcrop(crop_id)
) ENGINE=InnoDB;

-- =========================
-- INDEPENDENT TABLE
-- =========================

CREATE TABLE IF NOT EXISTS audit_trail (
    id INT UNSIGNED AUTO_INCREMENT,
    action VARCHAR(255) NOT NULL,
    user VARCHAR(45) NOT NULL DEFAULT 'system',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- =========================
-- SEED DATA
-- =========================

-- Roles (required before any user can be inserted)
INSERT INTO tbrole (role_id, role_name) VALUES
(1, 'Farmer'),
(2, 'Admin'),
(3, 'Superadmin')
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- Default province & city for the default superadmin
INSERT INTO tbprovince (province_id, province_name) VALUES (1, 'Default Province')
ON DUPLICATE KEY UPDATE province_name = VALUES(province_name);

INSERT INTO tbcitymuni (cm_id, cm_name, province_id) VALUES (1, 'Default City', 1)
ON DUPLICATE KEY UPDATE cm_name = VALUES(cm_name);

-- Default superadmin account  (username: superadmin  |  password: password)
-- Hash generated with: password_hash('Admin@1234', PASSWORD_DEFAULT)
INSERT INTO tbuser (id, UserName, PassWord, FullName, role_id, province_id, cm_id) VALUES
(1, 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 3, 1, 1)
ON DUPLICATE KEY UPDATE UserName = VALUES(UserName);
