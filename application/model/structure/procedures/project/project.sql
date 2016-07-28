CREATE PROCEDURE
`proc_newProject`(
  IN `projectId` INT(11),
  IN `userId` INT(11),
  IN `projectName` VARCHAR(150),
  IN `projectDescription` VARCHAR(200),
  IN `projectDate` DATE,
  IN `projectTime` TIME,
  OUT `InsertId` INT(11)
)
BEGIN
  INSERT INTO projects (id, user_id, name, description, date, time)
  VALUES(projectId, userId, projectName, projectDescription, projectDate, projectTime);
  SET InsertId = last_insert_id();
  SELECT InsertId;
END $$

CREATE PROCEDURE
`proc_getProjectById`(
  IN projectId INT(11)
)
  BEGIN
    SELECT name, description, date, time FROM projects WHERE id = projectID;
  END;

CREATE PROCEDURE
`proc_newProjectModel`(
  IN `id` INT(11),
  IN `userId` INT(11),
  IN `projectId` INT(11),
  IN `modelId` INT(11)
)
  BEGIN
    INSERT INTO projects_models VALUES(id, userId, projectId, modelId);
  END $$

CREATE PROCEDURE
`proc_getAllProjectsByUser`(
  IN userID INT(11)
)
  BEGIN
    SELECT id, name, description, date, time FROM projects WHERE user_id = userID;
END $$