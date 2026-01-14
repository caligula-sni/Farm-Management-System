CREATE TABLE dbfarm2.tbuser (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    UserName VARCHAR(45) NOT NULL, 
    PassWord VARCHAR(255) NOT NULL, 
    FullName VARCHAR(45) NOT NULL, 
    role_id int (10) unsigned NOT NULL,
    province_id int (10) unsigned NOT NULL,
    cm_id int (10) unsigned NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (role_id) REFERENCES tbrole(role_id),
    FOREIGN KEY (province_id) REFERENCES tbprovince(province_id),
    FOREIGN KEY (cm_id) REFERENCES tbcitymuni(cm_id)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.tbrole (
    role_id int (10) unsigned NOT NULL AUTO_INCREMENT,
    role_name VARCHAR (45) NOT NULL,
    PRIMARY KEY (role_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.audit_trail (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    action VARCHAR(255) NOT NULL, 
    user VARCHAR(45) NOT NULL, 
    timestamp timestamp NOT NULL DEFAULT current_timestamp(), 
    PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.tbprovince (
    province_id int (10) unsigned NOT NULL AUTO_INCREMENT,
    province_name VARCHAR (45) NOT NULL,
    PRIMARY KEY (province_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.tbcitymuni (
    cm_id int (10) unsigned NOT NULL AUTO_INCREMENT,
    cm_name VARCHAR (45) NOT NULL,
    province_id int(10) unsigned NOT NULL,
    PRIMARY KEY (cm_id),
    FOREIGN KEY (province_id) REFERENCES tbprovince(province_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.tbfarm (
	farm_id int (10) unsigned NOT NULL AUTO_INCREMENT,
	id int (10) unsigned NOT NULL,
	province_id int (10) unsigned NOT NULL,
	cm_id int (10) unsigned NOT NULL,
	farm_name VARCHAR (45) NOT NULL,
	PRIMARY KEY (farm_id),
    FOREIGN KEY (id) REFERENCES tbuser(id),
	FOREIGN KEY (province_id) REFERENCES tbprovince(province_id),
	FOREIGN KEY (cm_id) REFERENCES tbcitymuni(cm_id)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.tbcrop (
    crop_id int (10) unsigned NOT NULL AUTO_INCREMENT,
    crop_name VARCHAR (45) NOT NULL,
    PRIMARY KEY (crop_id)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE dbfarm2.tbfarmsupply (
	fs_id int (10) unsigned NOT NULL AUTO_INCREMENT,
	farm_id int (10) unsigned NOT NULL,
	crop_id int (10) unsigned NOT NULL,
	fs_quantity VARCHAR (45) NOT NULL,
	PRIMARY KEY (fs_id),
    	FOREIGN KEY (farm_id) REFERENCES tbfarm(farm_id),
	FOREIGN KEY (crop_id) REFERENCES tbcrop(crop_id)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

