- Create database
CREATE DATABASE IF NOT EXISTS dbfarm2;
USE dbfarm2;

-- =========================
-- BASE TABLES (NO DEPENDENCIES)
-- =========================

CREATE TABLE tbrole (
    role_id INT UNSIGNED AUTO_INCREMENT,
    role_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (role_id)
) ENGINE=InnoDB;

CREATE TABLE tbprovince (
    province_id INT UNSIGNED AUTO_INCREMENT,
    province_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (province_id)
) ENGINE=InnoDB;

CREATE TABLE tbcitymuni (
    cm_id INT UNSIGNED AUTO_INCREMENT,
    cm_name VARCHAR(45) NOT NULL,
    province_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (cm_id),
    FOREIGN KEY (province_id) REFERENCES tbprovince(province_id)
) ENGINE=InnoDB;

CREATE TABLE tbcrop (
    crop_id INT UNSIGNED AUTO_INCREMENT,
    crop_name VARCHAR(45) NOT NULL,
    PRIMARY KEY (crop_id)
) ENGINE=InnoDB;

-- =========================
-- DEPENDENT TABLES
-- =========================

CREATE TABLE tbuser (
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

CREATE TABLE tbfarm (
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

CREATE TABLE tbfarmsupply (
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

CREATE TABLE audit_trail (
    id INT UNSIGNED AUTO_INCREMENT,
    action VARCHAR(255) NOT NULL,
    user VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

