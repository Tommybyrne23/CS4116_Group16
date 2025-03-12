USE SportzMarketplace;

DROP PROCEDURE IF EXISTS ban_user;

DELIMITER //

CREATE PROCEDURE ban_user (
	IN p_userID INT
)
BEGIN 
	DECLARE user_count INT;
		-- Check if the user exists
		SELECT COUNT(*) INTO user_count FROM Users WHERE userID = p_userID;
        
    IF user_count > 0 THEN 
        DELETE FROM Users WHERE userID = p_userID;
        SELECT 'User successfully banned.' AS message;
        
    ELSE
        SELECT 'User does not exist.' AS message;
        
    END IF;
    
END;