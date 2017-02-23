CREATE TABLE notifications
(
	comment_id INT,
	member_id INT,
	weeklyreport_id INT,
	
	PRIMARY KEY (comment_id, member_id),
	FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
	FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
	FOREIGN KEY (weeklyreport_id) REFERENCES weeklyreports(id) ON DELETE CASCADE
);