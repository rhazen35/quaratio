CREATE TABLE
`logs_system`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11),
  `log_group` INT(11) NOT NULL,
  `log_action` INT(11) NOT NULL,
  `log_message` INT(11) NOT NULL,
  `date` DATE,
  `time` TIME,
    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;