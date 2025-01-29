
CREATE TABLE `MenuPage` (
`pageid` int(5) NOT NULL AUTO_INCREMENT,
`pagename` varchar(50),
`url` varchar(200),
`menuorder` int(2),
PRIMARY KEY (`pageid`)
);

CREATE TABLE `MenuLevel` (
`upid` int(5) NOT NULL AUTO_INCREMENT,
`minul` int(1),
`maxul` int(1),
`pageid` int(2),
PRIMARY KEY (`upid`),
FOREIGN KEY (`pageid`) REFERENCES MenuPage(`pageid`)
);
