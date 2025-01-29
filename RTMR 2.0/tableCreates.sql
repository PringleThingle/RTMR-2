#DROP TABLE movieReviews;
#DROP TABLE movieInfo;
#DROP TABLE directorInfo;
#DROP TABLE userInfo;


CREATE TABLE userInfo(
userID INT NOT NULL AUTO_INCREMENT,
username VARCHAR(50) DEFAULT NULL,
hashedPass VARCHAR(255) DEFAULT NULL,
email VARCHAR(100) DEFAULT NULL,
userLevel INT(1) DEFAULT 2,
lastSession varchar(64) DEFAULT NULL,
PRIMARY KEY (userID),
UNIQUE KEY username_UNIQUE (username),
UNIQUE KEY email_UNIQUE (email)
);

CREATE TABLE directorInfo(
directorID INT(7),
directorName VARCHAR(50),
directorDOB DATE,
directorBirthplace VARCHAR(300),
PRIMARY KEY (directorID)
);

CREATE TABLE movieInfo(
movieID INT(9) NOT NULL,
title VARCHAR(100),
rating DECIMAL(3,1),
movieDescription VARCHAR(1000),
posterLink VARCHAR(300),
directorID INT(7),
PRIMARY KEY (movieID),
FOREIGN KEY (directorID) REFERENCES directorInfo(directorID)
);

CREATE TABLE movieReviews(
reviewID INT(9) NOT NULL AUTO_INCREMENT,
reviewText VARCHAR(300),
userRating DECIMAL(3,1),
movieID INT(9) NOT NULL,
userID INT NOT NULL,
PRIMARY KEY (reviewID),
FOREIGN KEY (movieID) REFERENCES movieInfo(movieID),
FOREIGN KEY (userID) REFERENCES userInfo(userID)
);