USE SportzMarketplace;

DROP PROCEDURE IF EXISTS signup_comp;

DELIMITER //

CREATE PROCEDURE signup_comp (
	IN p_userid INT,
    IN p_compid INT
)
BEGIN 
	IF NOT EXISTS (SELECT 1 FROM Participants WHERE userID = p_userid AND compID = p_compid) 
    THEN INSERT INTO Participants (userID, compID) VALUES (p_userid, p_compid);
	ELSE SELECT 'User already signed up for this competition.' AS message; 
END IF;
END;