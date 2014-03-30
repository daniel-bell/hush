CREATE TABLE users(
	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(id),
	username VARCHAR(256) NOT NULL,
	email VARCHAR(256),
	password_hash VARCHAR(256) NOT NULL,
	salt VARCHAR(256) NOT NULL,
	avatar_file_path VARCHAR(256),
	locked DATETIME,
	last_activity DATETIME
);

CREATE TABLE user_relationship(
	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(id),
	relationship_key VARCHAR(256) NOT NULL,
	creator_user_id INT NOT NULL,
	FOREIGN KEY(creator_user_id) REFERENCES users(id),
	creator_user_key VARCHAR(256) NOT NULL,
	target_user_id INT NOT NULL,
	FOREIGN KEY(target_user_id) REFERENCES users(id),
	target_user_key VARCHAR(256) NOT NULL,
	relationship_type ENUM('blocked', 'friend', 'requested') NOT NULL
);

CREATE TABLE ip_history(
	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(id),
	user_id INT NOT NULL,
	FOREIGN KEY(user_id) REFERENCES users(id),
	ip INT UNSIGNED NOT NULL,
	access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages(
	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(id),
	message_key VARCHAR(256) NOT NULL,
	sent_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	send_user_id INT NOT NULL,
	FOREIGN KEY(send_user_id) REFERENCES users(id),
	target_user_id INT NOT NULL,
	FOREIGN KEY(target_user_id) REFERENCES users(id),
	message_content MEDIUMTEXT NOT NULL
);