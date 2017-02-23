CREATE TABLE newreports (
	user_id INT,
	weeklyreport_id INT,
	
	PRIMARY KEY (user_id, weeklyreport_id),
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
	FOREIGN KEY (weeklyreport_id) REFERENCES weeklyreports(id) ON DELETE CASCADE
);