DELIMITER $$

CREATE PROCEDURE
  `proc_newModel`(
  IN id INT(11),
  IN user_id INT(11),
  IN hash VARCHAR(256),
  IN upload_date DATE,
  IN upload_time TIME,
  IN date DATE,
  IN time TIME
)
  BEGIN
    INSERT INTO xmi_models VALUES(id, user_id, hash, upload_date, upload_time, date, time);
  END $$

CREATE PROCEDURE
  `proc_getMatchingModelHash`(
  IN fileHash VARCHAR(256)
)
  BEGIN
    SELECT hash FROM xmi_models WHERE hash = fileHash;
  END $$