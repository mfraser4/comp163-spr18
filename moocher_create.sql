DROP VIEW IF EXISTS Item_popularity;
DROP VIEW IF EXISTS In_stock;
DROP TABLE IF EXISTS Archived_rent_log;
DROP TABLE IF EXISTS Rent_log;
DROP TABLE IF EXISTS Residents;
DROP TABLE IF EXISTS Customer_reviews;
DROP TABLE IF EXISTS Holds;
DROP TABLE IF EXISTS Musical_instruments;
DROP TABLE IF EXISTS Sports_equipment;
DROP TABLE IF EXISTS Equipment;
DROP TABLE IF EXISTS Media;
DROP TABLE IF EXISTS Items;
DROP TABLE IF EXISTS Houses;
DROP TABLE IF EXISTS Moochers;

CREATE TABLE Moochers (
	Member_id	INTEGER		PRIMARY KEY		NOT NULL DEFAULT '0',
	Name		CHAR(30)	NOT NULL DEFAULT '',
	Email		CHAR(30)	NOT NULL DEFAULT '',
	Phone		Char(12)	NOT NULL DEFAULT '',
	Address		CHAR(50) NOT NULL DEFAULT ''
	);


CREATE TABLE Houses (
	Name		CHAR(30)	PRIMARY KEY		NOT NULL DEFAULT '',
	Address		CHAR(50)	NOT NULL DEFAULT '',
	Email		CHAR(30) NOT NULL DEFAULT ''
	);


CREATE TABLE Items (
	Item_no		INTEGER			PRIMARY KEY		NOT NULL DEFAULT '0',
	Name 		CHAR(40)		NOT NULL DEFAULT '',
	Amazon_URL	CHAR(400)	NOT NULL DEFAULT '',
	Quality		CHAR(9)		NOT NULL DEFAULT '',
	Home		CHAR(30)		NOT NULL DEFAULT '',
	CanMooch	BOOLEAN			NOT NULL DEFAULT 'false',

	CONSTRAINT item_housing FOREIGN KEY (Home) REFERENCES Houses(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Media (
	Item_no				INTEGER			PRIMARY KEY		NOT NULL DEFAULT '0',
	Genre				CHAR(20)		NOT NULL DEFAULT '',
	Year				INTEGER			NOT NULL DEFAULT '0',
	Rated				CHAR(5)		NOT NULL DEFAULT '',
	Movie_TV_Game		CHAR(9)		NOT NULL DEFAULT '',
	Prod_Ch_Studio		CHAR(30)		NOT NULL DEFAULT '',
	Disc_type_Console	CHAR(8)		NOT NULL DEFAULT '',

	CONSTRAINT media_items FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Equipment (
	Item_no		INTEGER			PRIMARY KEY		NOT NULL DEFAULT '0',
	Brand		CHAR(20)		NOT NULL DEFAULT '',

	CONSTRAINT equipment_items FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Sports_equipment (
	Item_no		INTEGER			PRIMARY KEY		NOT NULL DEFAULT '0',
	Sport		CHAR(10)		NOT NULL DEFAULT '',
	Quantity	INTEGER			NOT NULL DEFAULT '0',

	CONSTRAINT sports_items FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Musical_instruments (
	Item_no				INTEGER			PRIMARY KEY		NOT NULL DEFAULT '0',
	Instrument_type		CHAR(10)		NOT NULL DEFAULT '',

	CONSTRAINT music_items FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Holds (
	Item_no		INTEGER		NOT NULL DEFAULT '0',
	Moocher_id	INTEGER		NOT NULL DEFAULT '0',
	Queue_num	INTEGER		NOT NULL DEFAULT '0',
	PRIMARY KEY (Item_no, Moocher_id),

	CONSTRAINT item_holds FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	CONSTRAINT hold_moochers FOREIGN KEY (Moocher_id) REFERENCES Moochers(Member_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Customer_reviews (
	House_name	CHAR(30)		NOT NULL DEFAULT '',
	Moocher_id	INTEGER			NOT NULL DEFAULT '0',
	Review 		CHAR(200)	NOT NULL DEFAULT '',
	Stars		INTEGER			NOT NULL DEFAULT '0',
	PRIMARY KEY (House_name, Moocher_id, Review),

	CONSTRAINT customer_houses FOREIGN KEY (House_name) REFERENCES Houses(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	CONSTRAINT customer_moochers FOREIGN KEY (Moocher_id) REFERENCES Moochers(Member_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Residents (
	Resident_name	CHAR(30)	NOT NULL DEFAULT '',
	House_name		CHAR(30)	NOT NULL DEFAULT '',
	PRIMARY KEY (Resident_name, House_name),

	CONSTRAINT residents_house FOREIGN KEY (House_name) REFERENCES Houses(Name)
		ON UPDATE CASCADE
		ON DELETE CASCADE
	);


CREATE TABLE Rent_log (
	House_name	CHAR(30)	NOT NULL DEFAULT '',
	Item_no		INTEGER		NOT NULL DEFAULT '0',
	Moocher_id	INTEGER		NOT NULL DEFAULT '0',
	Rent_date	DATE 		NOT NULL DEFAULT '0001-01-01',
	Return_date	DATE 		NOT NULL DEFAULT '0001-01-01',
	PRIMARY KEY (House_name, Item_no, Moocher_id, Rent_date),

	CONSTRAINT rent_house FOREIGN KEY (House_name) REFERENCES Houses(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	CONSTRAINT rent_moochers FOREIGN KEY (Moocher_id) REFERENCES Moochers(Member_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	CONSTRAINT rent_items FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);


CREATE TABLE Archived_rent_log (
	House_name	CHAR(30)	NOT NULL DEFAULT '',
	Item_no		INTEGER		NOT NULL DEFAULT '0',
	Moocher_id	INTEGER		NOT NULL DEFAULT '0',
	Rent_date	DATE 		NOT NULL DEFAULT '0001-01-01',
	Return_date	DATE 		NOT NULL DEFAULT '0001-01-01',
	PRIMARY KEY (House_name, Item_no, Moocher_id, Rent_date)
);

CREATE VIEW In_stock AS
SELECT DISTINCT I.Item_no, I.Name, I.Amazon_URL, I.Quality
FROM items I, rent_log R
WHERE I.CanMooch=true AND ((NOT EXISTS (SELECT *
                                    FROM rent_log R1
                                    WHERE I.Item_no=R1.Item_no)
                         )
                        OR 
                        (R.Item_no=I.Item_no AND CURRENT_DATE>=(SELECT MAX(Return_date)
                                                                FROM rent_log R2
                                                                WHERE I.Item_no=R2.Item_no
                                                                )
                        )
                       );

CREATE VIEW Item_popularity AS
SELECT items.Item_no, items.Name, COUNT(rent_log.Item_no) AS No_rented
FROM (items LEFT JOIN rent_log ON items.item_no=rent_log.item_no)
GROUP BY items.Item_no
ORDER BY COUNT(rent_log.Item_no) DESC;

-- -- ensures order of queue numbers for an item is sequential
-- -- NOTE:  meant to check every time a value is inserted and complements QueueDoubledUp
-- --		to ensure Holds conditions hold
-- CREATE ASSERTION QueueSequential
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Holds
-- 			GROUP BY Item_no
-- 			HAVING COUNT(Item_no)<>MAX(Queue_num)
-- 			)
-- 		AND
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Holds
-- 			WHERE Queue_num<=0
-- 			)
-- 		);

-- CREATE ASSERTION QueueDoubledUp
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Holds H1, Holds H2
-- 			WHERE H1.Item_no=H2.Item_no AND H1.Queue_num=H2.Queue_num AND H1.Moocher_id<>H2.Moocher_id
-- 			)
-- 		);

-- -- -- enforcing domains since MySQL does not support create domains
-- CREATE ASSERTION ValidQuality
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Items
-- 			WHERE Quality NOT IN ('Poor','Okay','Good','Very Good')
-- 			)
-- 		);

-- CREATE ASSERTION ValidInstrument
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Musical_instruments
-- 			WHERE Instrument_type NOT IN ('Wind','Percussion','Brass','String','Electronic', 'Keyboard')
-- 			)
-- 		);

-- CREATE ASSERTION ValidDiscType
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Media
-- 			WHERE Disc_type_Console NOT IN ('SD','Blu-ray','XboxOne','Xbox360','PS3','PS4','Wii','PC')
-- 			)
-- 		);

-- CREATE ASSERTION ValidMedia
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT *
-- 			FROM Media
-- 			WHERE Movie_TV_Game NOT IN ('Movie','TV','Game')
-- 			)
-- 		);

-- CREATE ASSERTION ValidReview
-- 	CHECK (
-- 		NOT EXISTS (
-- 			SELECT * FROM Customer_reviews WHERE Stars>5 OR Stars<1
-- 			)
-- 		);

-- -- decrement the other holds on the queue of that item when a moocher drops his hold on a particular item
-- CREATE TRIGGER QueueChange
-- 	AFTER DELETE ON Holds
-- 	REFERENCING OLD AS O
-- 	FOR EACH ROW 
-- 	BEGIN
-- 	UPDATE Holds
-- 	SET Queue_num = Queue_num-1
-- 	WHERE Holds.Item_no=O.Item_no AND O.Queue_num < Holds.Queue_num; END