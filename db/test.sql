CREATE OR REPLACE TABLE YTRecord (
	`uid` INT UNSIGNED NOT NULL,
	`url` varchar(512) NOT NULL
);
CREATE INDEX YTRecord_index_uid
ON YTRecord(`uid`);

INSERT INTO YTRecord(`uid`,`url`)
VALUES (1,'456'),
(1,'756'),
 (2,'456'),
 (3,'756');
SELECT * FROM YTRecord;