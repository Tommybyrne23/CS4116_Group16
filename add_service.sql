USE SportzMarketplace;

DROP PROCEDURE IF EXISTS add_service;

DELIMITER //

CREATE PROCEDURE add_service (
	IN p_serviceName VARCHAR(30),
    IN p_description VARCHAR(100),
    IN p_businessID INT
)
BEGIN 
	SELECT COUNT(*) INTO service_count
    FROM Services
    WHERE serviceName = p_serviceName;

    IF service_count = 0 THEN
		INSERT INTO Services(serviceName, description, businessID)
		VALUES (p_serviceName, p_description, p_businessID);
		SELECT 'Service successfully added.' AS message; -- Success message
	ELSE
		SELECT 'Service already exists.' AS message; -- Service already exists.
	END IF;
END;