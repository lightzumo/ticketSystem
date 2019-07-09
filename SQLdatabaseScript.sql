
CREATE DATABASE IF NOT EXISTS dbTySi;
USE dbTySi;


CREATE TABLE IF NOT EXISTS tblLanguage(
  idLanguage  CHAR(2)  PRIMARY KEY,
  dtName      VARCHAR(50) NOT NULL,
    CONSTRAINT ui_Name
    UNIQUE INDEX idx_Name(dtName)
);



CREATE TABLE IF NOT EXISTS tblPerson(
    idPerson    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	dtName      VARCHAR(255) NOT NULL,
	dtAddress   VARCHAR(255) 	 NULL,
	dtPassword	VARCHAR(255) NOT NULL,
	dtUsername	VARCHAR(40)	 NOT NULL,

  CONSTRAINT ui_Username
    UNIQUE INDEX idx_Username(dtUsername)
);


CREATE TABLE IF NOT EXISTS tblTechnician(
  fiPerson   INT UNSIGNED PRIMARY KEY,
  CONSTRAINT fk_PersonTechnician
    FOREIGN KEY (fiPerson)	REFERENCES tblPerson(idPerson)
      ON UPDATE CASCADE
      ON DELETE RESTRICT
);



CREATE TABLE IF NOT EXISTS tblCustomer(
  idCustomer 	INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fiPerson  	INT UNSIGNED,
  fiLanguage    CHAR(2)   NOT NULL,

  CONSTRAINT fk_PersonLanguage
      FOREIGN KEY (fiLanguage) REFERENCES tblLanguage(idLanguage)
      ON UPDATE CASCADE
      ON DELETE RESTRICT,

  CONSTRAINT fk_PersonCustomer
    FOREIGN KEY (fiPerson)	REFERENCES tblPerson(idPerson)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tblQuestion(
	idQuestionNr MEDIUMINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dtQuestion 	 VARCHAR(255) NOT NULL
);



CREATE TABLE IF NOT EXISTS tblAnwser(
	fiQuestion MEDIUMINT UNSIGNED,
	fiCustomer  INT UNSIGNED,
	dtAnswer   VARCHAR(255) NOT NULL,

      CONSTRAINT fk_QuestionAnwser
      FOREIGN KEY (fiQuestion) REFERENCES tblQuestion(idQuestionNr)
      ON UPDATE CASCADE
      ON DELETE RESTRICT,

	  CONSTRAINT fk_ClientAnswer
      FOREIGN KEY (fiCustomer) REFERENCES tblCustomer(idCustomer)
      ON UPDATE CASCADE
      ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS tblTicket(
  idTicket MEDIUMINT AUTO_INCREMENT PRIMARY KEY,
  fiPerson INT UNSIGNED NOT NULL,
  dtStatus  ENUM('Open', 'Ongoing', 'Closed') 	DEFAULT 'Open' NOT NULL,
  dtCreationTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_PersonTicket
    FOREIGN KEY (fiPerson) REFERENCES tblPerson(idPerson)
      ON UPDATE CASCADE
      ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS tblTranslateTicket(
  fiTicket          MEDIUMINT,
  fiLanguage       CHAR(2),
  dtTitle          VARCHAR(255) NOT NULL,
  dtDescription	   VARCHAR(1000) NOT NULL,

  CONSTRAINT fk_Ticket
    FOREIGN KEY (fiTicket) REFERENCES tblTicket(idTicket)
      ON UPDATE CASCADE
      ON DELETE CASCADE,

  CONSTRAINT fk_Language
    FOREIGN KEY (fiLanguage) REFERENCES tblLanguage(idLanguage)
      ON UPDATE CASCADE
      ON DELETE CASCADE,

  PRIMARY KEY(fiTicket, fiLanguage)
);


CREATE TABLE IF NOT EXISTS tblPost(
  idPost	  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fiTicket     MEDIUMINT   NOT NULL,
  fiPerson    INT UNSIGNED NOT NULL,
  dtReplyTime DATETIME DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_TicketPost
    FOREIGN KEY (fiTicket) REFERENCES tblTicket(idTicket)
      ON UPDATE CASCADE
      ON DELETE CASCADE,

  CONSTRAINT fk_PersonPost
    FOREIGN KEY (fiPerson) REFERENCES tblPerson(idPerson)
      ON UPDATE CASCADE
      ON DELETE CASCADE


);



CREATE TABLE IF NOT EXISTS tblTranslatePost (
  fiLanguage CHAR(2),
  fiPost INT UNSIGNED,
  dtText VARCHAR(255),
  CONSTRAINT fk_LanguageTranslatePost
    FOREIGN KEY (fiLanguage) REFERENCES tblLanguage(idLanguage)
      ON UPDATE CASCADE
      ON DELETE CASCADE,
  CONSTRAINT fk_PostTranslatePost
  FOREIGN KEY (fiPost) REFERENCES tblPost(idPost)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  PRIMARY KEY (fiPost, fiLanguage)
);



CREATE TABLE IF NOT EXISTS tblAttachment(
  idAttachment INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  dtURL VARCHAR(255) 		  NOT NULL,
  fiTicket MEDIUMINT 	   NULL,
  fiPost   INT UNSIGNED    NULL,


	CONSTRAINT ui_URL
    UNIQUE INDEX idx_URL(dtURL),

    CONSTRAINT fk_TicketAttachment
    FOREIGN KEY (fiTicket) REFERENCES tblTicket(idTicket)
      ON UPDATE CASCADE
      ON DELETE CASCADE,

	      CONSTRAINT fk_PostAttachment
    FOREIGN KEY (fiPost) REFERENCES tblPost(idPost)
      ON UPDATE CASCADE
      ON DELETE CASCADE

);



CREATE TABLE IF NOT EXISTS tblSubscription(

  idSubscription INT UNSIGNED AUTO_INCREMENT,
  fiTicket    MEDIUMINT    NOT NULL,
  fiCustomer  INT UNSIGNED NOT NULL,

  CONSTRAINT fk_TicketSubscription
    FOREIGN KEY (fiTicket) REFERENCES tblTicket(idTicket)
      ON UPDATE CASCADE
      ON DELETE CASCADE,

  CONSTRAINT fk_CustomerSubscription
    FOREIGN KEY (fiCustomer) REFERENCES tblCustomer(idCustomer) 
      ON UPDATE CASCADE
      ON DELETE CASCADE,
      PRIMARY KEY(idSubscription, fiTicket,fiCustomer)
);




CREATE TABLE IF NOT EXISTS tblMail(
	fiSubscription INT UNSIGNED PRIMARY KEY,
	dtAddress VARCHAR(50) NOT NULL,
  CONSTRAINT fk_SubscriptionMail
    FOREIGN KEY (fiSubscription) REFERENCES tblSubscription(idSubscription)
      ON UPDATE CASCADE
      ON DELETE CASCADE 
);



CREATE TABLE IF NOT EXISTS tblTweet(
  fiSubscription INT UNSIGNED PRIMARY KEY,
  dtTwitterID	 VARCHAR(40),
  CONSTRAINT fk_SubscriptionTweet
    FOREIGN KEY (fiSubscription) REFERENCES tblSubscription(idSubscription)
      ON UPDATE CASCADE
      ON DELETE CASCADE
);

INSERT INTO `tblLanguage` (`idLanguage`, `dtName`)
	   VALUES ('en', 'english'),
			  ('fr', 'french'),
			  ('de', 'german');

INSERT INTO `tblQuestion` (`idQuestionNr`, `dtQuestion`)
	   VALUES (NULL, 'What is your favorite book?'),
			  (NULL, 'What is your favorite food?'),
			  (NULL, 'What city you were born in?'),
			  (NULL, 'Where did you go to high school?'),
			  (NULL, 'Where did you meet your spouse?'),
			  (NULL, 'What was the first company you worked on?'),
			  (NULL, 'What is your favorite color?'),
			  (NULL, 'What is your favorite movie?');


/* Default technician, username: root , password: root*/

 INSERT INTO tblPerson (dtName, dtAddress,  dtPassword, dtUsername) VALUES ("root", "", "$2y$10$BrSFgrWjD8KIw65Qkv4k0uDcEo4yr9njvDuAyvUeMMm7xqStkOdTa", "root");
 		INSERT INTO tblTechnician (fiPerson) VALUES ((SELECT LAST_INSERT_ID()));












DELIMITER $$
--
-- Procedures
--
CREATE  PROCEDURE `sp_addAttachment` (IN `i_Ticket` MEDIUMINT, 
									  IN `i_Post` INT UNSIGNED,
									  IN `i_dtURL` VARCHAR(255),
									  OUT `o_message` VARCHAR(255)
									  )
BEGIN
	  DECLARE l_error_trapped INT DEFAULT 0;
    DECLARE my_dupkey_error CONDITION FOR 1062;

    DECLARE CONTINUE HANDLER FOR my_dupkey_error SET l_error_trapped = 1;
    INSERT INTO tblAttachment  (fiTicket, dtURL, fiPost) VALUES (i_Ticket, i_dtURL, i_Post);

    IF l_error_trapped THEN
		SELECT "Error: File already exists." AS "ERROR" INTO o_message;
	END IF;
END$$

CREATE  PROCEDURE `sp_changePasswordCustomer` (IN `i_Username` VARCHAR(40),
											   IN `i_answer` VARCHAR(255),
											   IN `i_question` MEDIUMINT UNSIGNED,
											   IN `i_newPassword` VARCHAR(255),
											   OUT `o_message` VARCHAR(255)
											   )
BEGIN
			 DECLARE selectedAnswer VARCHAR(255);
			 SET  selectedAnswer = "Empty";

			SELECT dtAnswer INTO  selectedAnswer FROM  tblAnwser WHERE fiCustomer =(SELECT idCustomer
			FROM tblCustomer WHERE fiPerson =(SELECT idPerson FROM tblPerson
			WHERE dtUsername= i_Username)) AND fiQuestion = i_question;


			IF( selectedAnswer = i_answer)THEN
				UPDATE `tblPerson` SET `dtPassword` = i_newPassword WHERE `tblPerson`.`dtUsername` = i_Username;
				SELECT "Password Updated" INTO o_message;

			ELSE
				SELECT "Wrong information" INTO o_message;
			END IF;
END$$

CREATE  PROCEDURE `sp_changePasswordTechnician` (IN `i_Username` VARCHAR(40),
												 IN `i_newPassword` VARCHAR(255),
												 OUT `o_message` VARCHAR(255)
												 )
BEGIN
	DECLARE usernameFound VARCHAR(40);
	SET  usernameFound = "Empty";
	SELECT dtUsername INTO usernameFound FROM tblPerson, tblTechnician WHERE dtUsername = i_Username and idPerson = fiPerson;


	IF( usernameFound = i_Username)THEN
		UPDATE `tblPerson` SET `dtPassword` = i_newPassword WHERE `tblPerson`.`dtUsername` = i_Username;
		SELECT "Password Updated" INTO o_message;
	ELSE
		SELECT "Wrong information" INTO o_message;
	END IF;

END$$


CREATE PROCEDURE sp_ChangeTicketStatus (
								IN i_dtStatus VARCHAR(10),
								IN i_idTicket MEDIUMINT,
								OUT o_message VARCHAR(50))
BEGIN

	IF(exists(SELECT idTicket FROM tblTicket WHERE idTicket = i_idTicket))THEN
	UPDATE tblTicket SET dtStatus = i_dtStatus WHERE idTicket = i_idTicket;
		SELECT "Status updated" INTO o_message;
	ELSE
		SELECT "Error: Ticket Doesn't exists!"INTO o_message;
	END IF;


END$$

CREATE  PROCEDURE `sp_CheckSubscription` (IN `i_idTicket` MEDIUMINT,
											OUT `o_message` VARCHAR(255)
										  )
BEGIN

 IF(exists(SELECT idSubscription FROM tblSubscription WHERE fiTicket = i_idTicket))THEN

	IF(exists(SELECT dtAddress FROM tblMail WHERE fiSubscription = (SELECT idSubscription FROM tblSubscription WHERE fiTicket = i_idTicket))) THEN
		SELECT dtAddress AS "address"  INTO o_message FROM tblMail WHERE fiSubscription = (SELECT idSubscription FROM tblSubscription WHERE fiTicket = i_idTicket);
	END IF;


	IF(exists(SELECT dtTwitterID FROM tblTweet WHERE fiSubscription = (SELECT idSubscription FROM tblSubscription WHERE fiTicket = i_idTicket))) THEN

		SELECT dtTwitterID FROM tblTweet WHERE fiSubscription = (SELECT idSubscription FROM tblSubscription WHERE fiTicket = i_idTicket) INTO o_message;
	END IF;

 ELSE
	SELECT "No Subscription" INTO o_message;
 END IF;
 END$$

CREATE  PROCEDURE `sp_createPost` (IN `i_idTicket` MEDIUMINT, 
								   IN `i_idPerson` INT UNSIGNED,
								   IN `i_Language` CHAR(2),
								   IN `i_answer` VARCHAR(255),
								   OUT `o_error` VARCHAR(20)
								   )
BEGIN
	 DECLARE checkStatus VARCHAR(10);
      SET checkStatus = "Empty";
      SELECT dtStatus INTO checkStatus FROM tblTicket WHERE idTicket = i_idTicket;

	IF( checkStatus != "Closed")THEN
		INSERT INTO tblPost (fiTicket, fiPerson) VALUES (i_idTicket, i_idPerson);
		INSERT INTO tblTranslatePost (fiPost, fiLanguage, dtText) VALUES ((SELECT LAST_INSERT_ID()),i_Language, i_Answer);
	ELSE
		SELECT "Error: Ticket Closed" INTO o_error;
	END IF;
END$$

CREATE  PROCEDURE `sp_createSubscription` (IN `i_idCustomer` INT UNSIGNED,
										   IN `i_idTicket` MEDIUMINT,
										   IN `i_TypeSub` TINYINT,
										   IN `i_ValueOfSub` VARCHAR(50)
										   )
BEGIN

/* IF i_TypeSub = 1 its Email
   else its Twitter */


	INSERT INTO tblSubscription (fiTicket, fiCustomer) VALUES (i_idTicket, i_idCustomer);

	IF i_TypeSub = 1 THEN

		INSERT INTO tblMail (fiSubscription, dtAddress) VALUES ((SELECT LAST_INSERT_ID()),i_ValueOfSub);

	ELSE

		INSERT INTO tblTweet (fiSubscription, dtTwitterID) VALUES ((SELECT LAST_INSERT_ID()),i_ValueOfSub);

	END IF;

END$$

CREATE  PROCEDURE `sp_createTicket` (IN `i_idPerson` INT UNSIGNED, IN `i_dtTitle` VARCHAR(255), IN `i_dtDescription` VARCHAR(1000), IN `i_Language` CHAR(2), IN `i_TypeSub` TINYINT, IN `i_ValueOfSub` VARCHAR(50), OUT `o_message` VARCHAR(255))  BEGIN
	DECLARE l_error_trapped INT DEFAULT 0;
	DECLARE my_notNull_error CONDITION FOR 1048;

    DECLARE CONTINUE HANDLER FOR my_notNull_error SET l_error_trapped = 1;
	INSERT INTO tblTicket (fiPerson) VALUES (i_idPerson);


	IF l_error_trapped THEN
		SELECT "Ticket has no title!" INTO o_message;
    ELSE
	SET @lastInsertedTicket = (SELECT LAST_INSERT_ID());
		INSERT INTO tblTranslateTicket (fiTicket, fiLanguage, dtDescription, dtTitle) VALUES (@lastInsertedTicket, i_Language, i_dtDescription, i_dtTitle);

		IF l_error_trapped THEN
			SELECT "Description of the Ticket is empty!" INTO o_message;
		ELSE
			IF i_TypeSub > 0 THEN

				CALL sp_createSubscription((SELECT idCustomer FROM tblCustomer WHERE fiPerson = i_idPerson),@lastInsertedTicket, i_TypeSub, i_ValueOfSub);

			END IF;

			SELECT "Ticket Created" INTO o_message;
		END IF;
	END IF;


END$$

CREATE  PROCEDURE `sp_createUser` (IN `i_Name` VARCHAR(255), IN `i_Address` VARCHAR(255), IN `i_Password` VARCHAR(255), IN `i_Username` VARCHAR(40), IN `i_Language` CHAR(2), IN `i_Question` MEDIUMINT UNSIGNED, IN `i_Answer` VARCHAR(255), OUT `o_message` VARCHAR(255))  BEGIN

    DECLARE l_error_trapped INT DEFAULT 0;
    DECLARE my_dupkey_error CONDITION FOR 1062;

    DECLARE CONTINUE HANDLER FOR my_dupkey_error SET l_error_trapped = 1;
    INSERT INTO tblPerson (dtName, dtAddress,  dtPassword, dtUsername) VALUES (i_Name, i_Address, i_Password, i_Username);
	INSERT INTO tblCustomer (fiPerson, fiLanguage) VALUES ((SELECT LAST_INSERT_ID()),i_Language);
	INSERT INTO tblAnwser (fiQuestion, fiCustomer, dtAnswer) VALUES (i_Question,(SELECT LAST_INSERT_ID()),i_Answer);

    IF l_error_trapped THEN
		SELECT "Error: Username, already exists." AS "ERROR" INTO o_message;
    ELSE
		SELECT "User created!" AS "MESSAGE" INTO o_message;
	END IF;
END$$

CREATE  PROCEDURE `sp_deleteUser` (IN `i_idPerson` INT UNSIGNED, OUT `o_message` CHAR(13))  BEGIN
	DELETE FROM tblPerson
	WHERE idPerson = i_idPerson;


	SELECT "User Deleted." INTO o_message;
END$$

CREATE  PROCEDURE `sp_getLanguages` ()  BEGIN
	SELECT * FROM tblLanguage;
END$$

CREATE  PROCEDURE `sp_getQuestions` ()  BEGIN
SELECT * FROM tblQuestion;
END$$

CREATE  PROCEDURE `sp_getStatusOfTicket` (IN `i_idTicket` MEDIUMINT)  BEGIN
	SELECT dtStatus FROM tblTicket WHERE idTicket = i_idTicket;
END$$

CREATE  PROCEDURE `sp_getTicketOfUser` (IN `i_idPerson` INT UNSIGNED, IN `i_Language` CHAR(2))  BEGIN
	SELECT idTicket, dtTitle, dtStatus, dtCreationTime, fiLanguage
	FROM tblTicket, tblTranslateTicket
	WHERE tblTicket.fiPerson = i_idPerson AND tblTicket.idTicket = tblTranslateTicket.fiTicket AND tblTranslateTicket.fiLanguage= i_Language
	ORDER BY idTicket DESC;
END$$

CREATE  PROCEDURE `sp_searchPost` (IN `i_idTicket` MEDIUMINT, IN `i_Language` CHAR(2))  BEGIN
	SELECT idPost, fiTicket, dtUsername, dtReplyTime, fiLanguage, fiPost, dtText
	FROM tblPost, tblTranslatePost, tblPerson
	WHERE fiPerson = idPerson AND idPost = fiPost AND fiTicket =i_idTicket AND tblTranslatePost.fiLanguage= i_Language ;
END$$

CREATE  PROCEDURE `sp_searchTicketNoAttachments` (IN `i_idTicket` MEDIUMINT, IN `i_Language` CHAR(2))  BEGIN
	SELECT idTicket,dtCreationTime, dtTitle, dtDescription ,dtUsername ,fiPerson
	FROM tblTicket,  tblTranslateTicket, tblPerson
	WHERE idTicket = i_idTicket AND idTicket = fiTicket AND tblTicket.fiPerson = tblPerson.idPerson AND tblTranslateTicket.fiLanguage= i_Language ;
END$$

CREATE  PROCEDURE `sp_searchTicketWithAttachments` (IN `i_idTicket` MEDIUMINT, IN `i_Language` CHAR(2))  BEGIN
	SELECT idTicket, dtCreationTime, dtTitle, dtDescription ,dtUsername ,fiPerson, dtURL
	FROM tblTicket, tblTranslateTicket, tblPerson, tblAttachment
	WHERE idTicket = i_idTicket AND idTicket = tblTranslateTicket.fiTicket AND tblTicket.fiPerson = tblPerson.idPerson AND idTicket = tblAttachment.fiTicket AND tblTranslateTicket.fiLanguage= i_Language;
END$$

CREATE  PROCEDURE `sp_selectPassword` (IN `i_Username` VARCHAR(40), OUT `o_message` VARCHAR(255))  BEGIN
		DECLARE password1 VARCHAR(255);
		DECLARE idPerson1 INT UNSIGNED;
		SELECT  password1= dtPassword, idPerson1 = idPerson FROM tblPerson WHERE dtUsername = i_Username;
		IF(password1 = NULL) THEN
		SELECT 'ERROR: User doesnt exist'INTO o_message;
		ELSE
		SELECT 'HEHE' into o_message ;
		END IF;
END$$

CREATE  PROCEDURE `sp_showAllTickets` (IN `i_Language` CHAR(2))  BEGIN
	SELECT idTicket, dtTitle, dtStatus, dtCreationTime, fiLanguage
	FROM tblTicket, tblTranslateTicket
  WHERE  tblTicket.idTicket = tblTranslateTicket.fiTicket AND tblTranslateTicket.fiLanguage= i_Language
	GROUP BY idTicket
	ORDER BY dtStatus, dtCreationTime DESC;
END$$

CREATE  PROCEDURE `sp_TranslatePost` (IN `i_idPost` MEDIUMINT, IN `i_Language` CHAR(2), IN `i_text` VARCHAR(255))  BEGIN
	INSERT INTO tblTranslatePost (fiPost, fiLanguage, dtText) VALUES ( i_idPost,i_Language, i_text);
END$$

CREATE  PROCEDURE `sp_TranslateTicket` (IN `i_idTicket` MEDIUMINT, IN `i_Language` CHAR(2), IN `i_description` VARCHAR(1000), `i_dtTitle` VARCHAR(255))  BEGIN
	INSERT INTO tblTranslateTicket (fiTicket, fiLanguage, dtDescription, dtTitle) VALUES (i_idTicket, i_Language, i_description, i_dtTitle);
END$$

CREATE PROCEDURE sp_createTechnician (
								IN i_Name VARCHAR(255),
								IN i_Address VARCHAR(255),
								IN i_Password VARCHAR(255),
								IN i_Username VARCHAR(40),
								OUT o_message VARCHAR(255))
BEGIN
    
    DECLARE l_error_trapped INT DEFAULT 0;
    DECLARE my_dupkey_error CONDITION FOR 1062;
    
    DECLARE CONTINUE HANDLER FOR my_dupkey_error SET l_error_trapped = 1;
    INSERT INTO tblPerson (dtName, dtAddress,  dtPassword, dtUsername) VALUES (i_Name, i_Address, i_Password, i_Username);

	
    IF l_error_trapped THEN
		SELECT "Error: Username, already exists." AS "ERROR" INTO o_message;	
    ELSE
		INSERT INTO tblTechnician (fiPerson) VALUES ((SELECT LAST_INSERT_ID()));
		SELECT "Technician created!" AS "MESSAGE" INTO o_message;
	END IF;
END$$

DELIMITER ;
