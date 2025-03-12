USE SportzMarketplace;

DROP PROCEDURE IF EXISTS leave_review;

DELIMITER //

CREATE PROCEDURE leave_review (
    IN p_userID INT,
    IN p_businessID INT,
    IN p_serviceID INT,
    IN p_subserviceID INT,
    IN p_rating ENUM('1', '2', '3', '4', '5'),
    IN p_comment VARCHAR(100)
)
BEGIN
    INSERT INTO Reviews (userID, businessID, serviceID, subserviceID, rating, comment)
    VALUES (p_userID, p_businessID, p_serviceID, p_subserviceID, p_rating, p_comment);
END;
