-- E-mail pro obnovu hesla, token obnovy (sha256 hash) s platností
ALTER TABLE `user`
	MODIFY `password` varchar(255) NOT NULL,
	ADD `email` varchar(100) DEFAULT NULL AFTER `login`,
	ADD `reset_token` char(64) DEFAULT NULL,
	ADD `reset_expire` datetime DEFAULT NULL,
	ADD UNIQUE KEY `login` (`login`),
	ADD UNIQUE KEY `email` (`email`),
	ADD UNIQUE KEY `reset_token` (`reset_token`);
