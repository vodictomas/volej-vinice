-- tinytext má limit 255 bajtů, česká diakritika zabírá 2 bajty na znak
ALTER TABLE `message`
	MODIFY `text` varchar(250) NOT NULL;
