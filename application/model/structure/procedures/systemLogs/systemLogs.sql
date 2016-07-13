DELIMITER $$

CREATE PROCEDURE
`proc_newSystemLogger`(
 IN id INT(11),
 IN userId INT(11),
 IN logGroup INT(11),
 IN logAcion INT(11),
 IN logMessage INT(11),
 IN logDate DATE,
 IN logTime TIME
)
BEGIN
  INSERT INTO logs_system VALUES(id, userId, logGroup, logAcion, logMessage, logDate, logTime);
END $$

CREATE PROCEDURE
`proc_countSystemLogs`()
  BEGIN
    SELECT COUNT(id) FROM logs_system;
  END $$

CREATE PROCEDURE
`proc_getSystemLogsUserIds`()
 BEGIN
  SELECT user_id FROM logs_system GROUP BY user_id;
 END $$

CREATE PROCEDURE
`proc_getSystemLogsGroups`()
  BEGIN
    SELECT log_group FROM logs_system GROUP BY log_group;
  END $$

CREATE PROCEDURE
  `proc_getSystemLogsActions`()
  BEGIN
    SELECT log_action FROM logs_system GROUP BY log_action;
  END $$

CREATE PROCEDURE
  `proc_getSystemLogsMessages`()
  BEGIN
    SELECT log_message FROM logs_system GROUP BY log_message;
  END $$

CREATE PROCEDURE
`proc_getSystemLogsDates`()
  BEGIN
    SELECT log_date FROM logs_system GROUP BY log_date;
  END $$

CREATE PROCEDURE
  `proc_getSystemLogsTimes`()
  BEGIN
    SELECT log_time FROM logs_system GROUP BY log_time;
  END $$
