USE SportzMarketplace;

DROP PROCEDURE IF EXISTS add_subservice;

DELIMITER //

CREATE PROCEDURE add_subservice (
	IN p_subserviceName VARCHAR(30),
    IN p_description VARCHAR(100),
    IN p_cost INT,
    IN p_serviceID INT
)
BEGIN 
	SELECT COUNT(*) INTO subservice_count
    FROM Subservices
    WHERE subserviceName = p_subserviceName AND serviceID = p_serviceID;

    IF subservice_count = 0 THEN
		INSERT INTO Subservices(subserviceName, description, cost, serviceID)
		VALUES (p_subserviceName, p_description, p_cost, p_serviceID);
		SELECT 'Subservice successfully added.' AS message; -- Success message
	ELSE
		SELECT 'Subservice already exists.' AS message; -- Service already exists.
	END IF;
END;