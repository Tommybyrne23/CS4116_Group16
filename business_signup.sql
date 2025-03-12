USE SportzMarketplace;

DROP PROCEDURE IF EXISTS business_signup;

DELIMITER //

CREATE PROCEDURE business_signup (
	IN p_name VARCHAR(30),
    IN p_description VARCHAR(100),
    IN p_contactInfo VARCHAR(50)
)
BEGIN 
	DECLARE business_count INT;

    -- Check if a business with the same name already exists
    SELECT COUNT(*) INTO business_count
    FROM Businesses
    WHERE name = p_name;

    IF business_count = 0 THEN
        -- Business does not exist, insert new business
        INSERT INTO Businesses (name, description, contactInfo)
        VALUES (p_name, p_description, p_contactInfo);
        SELECT 'Business successfully registered.' AS message; -- Success message
    ELSE
        -- Business already exists
        SELECT 'Business name already in use.' AS message; -- Error message
    END IF;
END;