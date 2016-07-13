/**********************************************************/
/**********************************************************/
/********************** FUNCTIONS *************************/
/**********************************************************/
DELIMITER $$
/**********************************************************/
/* Function that checks if an email adress already exists */
/**********************************************/
CREATE FUNCTION
`f_checkEmailExists`(`input` VARCHAR(150))
RETURNS int(11)
BEGIN
    DECLARE output int(11);
    SELECT id INTO output FROM users WHERE email = input;
    RETURN output;
END$$
/**********************************************/
/* Function that checks if an email and password match */
/**********************************************/
CREATE FUNCTION
`f_checkEmailPass`(`input` VARCHAR(150))
RETURNS varchar(256)
BEGIN
    DECLARE output varchar(256);
    SELECT password INTO output FROM users WHERE email = input;
    RETURN output;
END$$
/**********************************************/
/* Function that returns the user id with a given email */
/**********************************************/
CREATE FUNCTION
`f_getUserId`(`input` varchar(150))
RETURNS int(11)
BEGIN
    DECLARE output int(11);
    SELECT id INTO output FROM users WHERE email = input;
    RETURN output;
END$$
/**********************************************/
/* Function that returns the user type i.e. the current status in the system */
/**********************************************/
CREATE FUNCTION
`f_getUserType`(`input` int(11))
RETURNS varchar(25)
BEGIN
    DECLARE output varchar(25);
    SELECT type INTO output FROM users WHERE id = input;
    RETURN output;
END$$