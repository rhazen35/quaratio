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

CREATE PROCEDURE
  `proc_newModel`(
  IN `modelId` INT(11),
  IN `userId` INT(11),
  IN `modelHash` VARCHAR(256),
  IN `modelDate` DATE,
  IN `modelTime` TIME,
  OUT `InsertId` INT(11)
)
  BEGIN
    INSERT INTO xmi_models (id, user_id, hash, date, time)
    VALUES(modelId, userId, modelHash, modelDate, modelTime);
    SET InsertId = last_insert_id();
    SELECT InsertId;
  END$$

CREATE PROCEDURE
  `proc_getModel`(
  IN modelId INT(11)
)
  BEGIN
    SELECT user_id, hash, date, time FROM xmi_models WHERE id = modelId;
  END $$