USE SportzMarketplace;

DROP PROCEDURE IF EXISTS delete_review;

DELIMITER //

CREATE PROCEDURE delete_review (
	IN p_reviewID INT
)
BEGIN 
	DECLARE review_count INT;
		-- Check if the user exists
		SELECT COUNT(*) INTO review_count FROM Reviews WHERE reviewID = p_reviewID;
        
    IF review_count > 0 THEN 
        DELETE FROM Reviews WHERE reviewID = p_reviewID;
        SELECT 'Review successfully deleted.' AS message;
        
    ELSE
        SELECT 'Review does not exist.' AS message;
        
    END IF;
    
END;