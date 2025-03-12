CREATE DATABASE SportzMarketplace;
USE SportzMarketplace;

CREATE TABLE Businesses (businessID INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(30), description VARCHAR(100), contactInfo VARCHAR(50));
CREATE TABLE Users(userID INT AUTO_INCREMENT PRIMARY KEY, firstName VARCHAR(30), lastName VARCHAR(30), username VARCHAR(10), password VARCHAR(10));
CREATE TABLE Services(serviceID INT AUTO_INCREMENT PRIMARY KEY, serviceName VARCHAR(30), description VARCHAR(100), businessID INT, FOREIGN KEY (businessID) REFERENCES Businesses(businessID));
CREATE TABLE SubServices(subserviceID INT AUTO_INCREMENT PRIMARY KEY, subserviceName VARCHAR(30), description VARCHAR(100), cost DECIMAL(10,2), serviceID INT, FOREIGN KEY (serviceID) REFERENCES Services(serviceID));
CREATE TABLE Transactions(transactionID INT AUTO_INCREMENT PRIMARY KEY, userID INT, businessID INT, serviceID INT, subserviceID INT, FOREIGN KEY (userID) REFERENCES Users(userID), FOREIGN KEY (businessID) REFERENCES Businesses(businessID), FOREIGN KEY (serviceID) REFERENCES Services(serviceID), FOREIGN KEY (subserviceID) REFERENCES Subservices(subserviceID));
CREATE TABLE Reviews(reviewID INT AUTO_INCREMENT PRIMARY KEY, userID INT, businessID INT, serviceID INT, subserviceID INT, rating ENUM('1', '2', '3', '4', '5'), comment VARCHAR(100), FOREIGN KEY (userID) REFERENCES Users(userID), FOREIGN KEY (businessID) REFERENCES Businesses(businessID), FOREIGN KEY (serviceID) REFERENCES Services(serviceID), FOREIGN KEY (subserviceID) REFERENCES Subservices(subserviceID));
CREATE TABLE Competitions(compID INT AUTO_INCREMENT PRIMARY KEY, compName VARCHAR(50), sport VARCHAR(30), type ENUM('team', 'individual'), date DATE);
CREATE TABLE Participants(userID INT PRIMARY KEY, compID INT, FOREIGN KEY (userID) REFERENCES Users(userID), FOREIGN KEY (compID) REFERENCES Competitions(compID));
CREATE TABLE Messages(messageID INT AUTO_INCREMENT PRIMARY KEY, userID INT, businessID INT, message VARCHAR(100), sender ENUM('user', 'business'), FOREIGN KEY (userID) REFERENCES Users(userID), FOREIGN KEY (businessID) REFERENCES Businesses(businessID));