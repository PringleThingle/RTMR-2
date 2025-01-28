CREATE TABLE `usertable` (
`userid` int(5) NOT NULL AUTO_INCREMENT,
`username` varchar(20) DEFAULT NULL,
`firstname` varchar(30) DEFAULT NULL,
`surname` varchar(30) DEFAULT NULL,
`email` varchar(45) DEFAULT NULL,
`dob` date DEFAULT NULL,
`userpass` varchar(255) DEFAULT NULL,
`lastsession` varchar(64) DEFAULT NULL,
`usertype` int(3) DEFAULT 2,
PRIMARY KEY (`userid`),
UNIQUE KEY `username_UNIQUE` (`username`),
UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into usertable (username, firstname, surname, email, dob,
userpass, lastsession, usertype) values ('bob', 'Robert',
'MacDougall', 'bob@bob.com', '1980-11-06',
'$2y$10$5G/H6B60OMhFvkvUWnYet.cE9MARbxQH/d.krsmHx00OHPpDQJEEi',
NULL, 1);
insert into usertable (username, firstname, surname, email, dob,
userpass, lastsession, usertype) values ('Jane', 'Jane', 'McJane',
'jane@jane.com', '1967-10-23', '$2y$10$863IrkZTkZ5gCFmi88MOhec0QojJDgAbp1qtxBGsvVkbmfJRBVB1u',
NULL, 2);
insert into usertable (username, firstname, surname, email, dob,
userpass, lastsession, usertype) values ('alice', 'alice', 'stuffs',
'alice@alice.co.uk', '1995-06-19',
'$2y$10$Op7Ncez4PfzX9zT5nESnSeC8detcoRh6MViqRTl3YhC6spQ4yo2QC',
NULL, 3);