create table volejbal (
	jmeno int NOT NULL,
	datum int NOT NULL,

	primary key (jmeno, datum)
);

alter table volejbal add column stav tinyint default -1;
alter table volejbal add column lastzmena int default 0;
alter table volejbal add column stav_pivo tinyint default -1;
