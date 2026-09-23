-- Ruční pořadí týmů ve výpisu docházky – domácí první, hosté poslední.
-- Při shodné pozici se řadí abecedně, takže nula u všech znamená původní chování.
ALTER TABLE `team`
	ADD `position` smallint(6) NOT NULL DEFAULT 0 AFTER `color`;

-- Výchozí pořadí podle současného stavu
UPDATE `team` SET `position` = 1 WHERE `name` = 'Zegoni';
UPDATE `team` SET `position` = 2 WHERE `name` = 'Já nevím';
UPDATE `team` SET `position` = 3 WHERE `name` = 'hosti';
