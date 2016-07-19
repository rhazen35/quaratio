CREATE TABLE `xmi_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hash` varchar(256) NOT NULL,
  `upload_date` date NOT NULL,
  `upload_time` time NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;