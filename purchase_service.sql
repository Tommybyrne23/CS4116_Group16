USE SportzMarketplace;

DROP PROCEDURE IF EXISTS purchase_service;

DELIMITER //

CREATE PROCEDURE purchase_service (
	IN p_userid INT,
    IN p_busid INT,
    IN p_serviceid INT,
    IN p_subserviceid INT
)
BEGIN 
	INSERT INTO Transactions (userID, businessID, serviceID, subserviceID)
    VALUES (p_userID, p_businessID, p_serviceID, p_subserviceID);
END;
