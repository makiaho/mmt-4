CREATE TABLE notes (
	id INT AUTO_INCREMENT,
	content VARCHAR(1000),
	created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        project_role VARCHAR(20) NOT NULL,
        email varchar(40),
        contact_user tinyint(1),
        note_read tinyint(1),
	PRIMARY KEY (id)
);