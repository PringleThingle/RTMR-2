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
title text,
rating DECIMAL(3,1),
movieDescription text,
movieDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
posterLink text,
directorID INT(7) DEFAULT "NO DIRECTOR",
PRIMARY KEY (movieID),
FOREIGN KEY (directorID) REFERENCES directorInfo(directorID)
);

CREATE TABLE movieReviews(
reviewID INT(9) NOT NULL AUTO_INCREMENT,
reviewText text,
userRating DECIMAL(3,1),
movieID INT(9) NOT NULL,
reviewPoster int(5) DEFAULT NULL,
reviewTime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (reviewID),
KEY reviewPoster (reviewPoster),
FOREIGN KEY (movieID) REFERENCES movieInfo(movieID),
CONSTRAINT reviewPoster FOREIGN KEY (reviewPoster) REFERENCES userInfo (userID) ON DELETE SET NULL ON UPDATE CASCADE
);