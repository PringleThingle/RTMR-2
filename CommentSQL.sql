CREATE TABLE `commenttable` (
  `commentID` int(6) NOT NULL AUTO_INCREMENT,
  `blogID` int(5) NOT NULL,
  `commenttext` text,
  `commenttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `commentposter` int(5) DEFAULT NULL,
  PRIMARY KEY (`commentID`),
  KEY `blogID_idx` (`blogID`),
  KEY `commentposter_idx` (`commentposter`),
  CONSTRAINT `blogID` FOREIGN KEY (`commentposter`) REFERENCES `usertable` (`userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


