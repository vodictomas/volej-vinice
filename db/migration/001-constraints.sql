-- Unikátní termín pro každý den
ALTER TABLE `term`
	ADD UNIQUE KEY `date` (`date`),
	MODIFY `available` tinyint(1) NOT NULL DEFAULT 1;

-- Jeden záznam docházky na hráče a termín, reason obsahuje jen kód z AttendanceReasonDial
UPDATE `attendance` SET `reason` = NULL WHERE `reason` = '';

ALTER TABLE `attendance`
	ADD UNIQUE KEY `player_term` (`player_id`, `term_id`),
	DROP KEY `player_id`,
	MODIFY `reason` varchar(3) DEFAULT NULL;

-- Datum zprávy se doplní automaticky
ALTER TABLE `message`
	MODIFY `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `team`
	MODIFY `active` tinyint(1) NOT NULL DEFAULT 1;

ALTER TABLE `player`
	MODIFY `active` tinyint(1) NOT NULL DEFAULT 1,
	MODIFY `prefill` tinyint(1) NOT NULL DEFAULT 0;
