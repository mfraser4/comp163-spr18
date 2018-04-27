DROP DATABASE IF EXISTS Moochers_db;
CREATE DATABASE IF NOT EXISTS Moochers_db;
USE Moochers_db;

DROP TABLE IF EXISTS Moochers;
CREATE TABLE Moochers (
	Member_id	INTEGER		PRIMARY KEY		NOT NULL,
	Name		VARCHAR(20)	NOT NULL,
	Email		VARCHAR(20)	NOT NULL,
	Phone		Char(11)	NOT NULL,
	Address		VARCHAR(50) NOT NULL
	);

DROP TABLE IF EXISTS Houses;
CREATE TABLE Houses (
	Name		VARCHAR(20)	PRIMARY KEY		NOT NULL,
	Address		VARCHAR(50)	NOT NULL,
	Email		VARCHAR(20) NOT NULL
	);

DROP TABLE IF EXISTS Items;
CREATE TABLE Items (
	Item_no		INTEGER			PRIMARY KEY		NOT NULL,
	Name 		VARCHAR(40)		NOT NULL,
	Amazon_URL	VARCHAR(200)	NOT NULL,
	Quality		VARCHAR(9)		NOT NULL,
	Home		VARCHAR(20)		NOT NULL,
	CanMooch	BOOLEAN			NOT NULL,

	FOREIGN KEY (Home) REFERENCES Houses(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Media;
CREATE TABLE Media (
	Item_no				INTEGER			PRIMARY KEY		NOT NULL,
	Genre				VARCHAR(15)		NOT NULL,
	Year				INTEGER			NOT NULL,
	Rated				VARCHAR(5)		NOT NULL,
	Movie_TV_Game		VARCHAR(9)		NOT NULL,
	Prod_Ch_Studio		VARCHAR(20)		NOT NULL,
	Disc_type_Console	VARCHAR(8)		NOT NULL,

	FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Equipment;
CREATE TABLE Equipment (
	Item_no		INTEGER			PRIMARY KEY		NOT NULL,
	Brand		VARCHAR(15)		NOT NULL,

	FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Sports_equipment;
CREATE TABLE Sports_equipment (
	Item_no		INTEGER			PRIMARY KEY		NOT NULL,
	Sport		VARCHAR(10)		NOT NULL,
	Quantity	INTEGER			NOT NULL,

	FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Musical_instruments;
CREATE TABLE Musical_instruments (
	Item_no				INTEGER			PRIMARY KEY		NOT NULL,
	Instrument_type		VARCHAR(10)		NOT NULL,

	FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Holds;
CREATE TABLE Holds (
	Item_no		INTEGER		NOT NULL,
	Moocher_id	INTEGER		NOT NULL,
	Queue_num	INTEGER		NOT NULL,
	PRIMARY KEY (Item_no, Moocher_id),

	FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	FOREIGN KEY (Moocher_id) REFERENCES Moochers(Member_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Customer_reviews;
CREATE TABLE Customer_reviews (
	House_name	VARCHAR(20)		NOT NULL,
	Moocher_id	INTEGER			NOT NULL,
	Review 		VARCHAR(200)	NOT NULL,
	Stars		INTEGER			NOT NULL,
	PRIMARY KEY (House_name, Moocher_id, Review),

	FOREIGN KEY (House_name) REFERENCES Houses(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	FOREIGN KEY (Moocher_id) REFERENCES Moochers(Member_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Residents;
CREATE TABLE Residents (
	Resident_name	VARCHAR(20)	NOT NULL,
	House_name		VARCHAR(20)	NOT NULL,
	PRIMARY KEY (Resident_name, House_name),

	FOREIGN KEY (House_name) REFERENCES Houses(Name)
		ON UPDATE CASCADE
		ON DELETE CASCADE
	);

DROP TABLE IF EXISTS Rent_log;
CREATE TABLE Rent_log (
	House_name	VARCHAR(20)	NOT NULL,
	Item_no		INTEGER		NOT NULL,
	Moocher_id	INTEGER		NOT NULL,
	Rent_date	DATE 		NOT NULL,
	Return_date	DATE 		NOT NULL,
	PRIMARY KEY (House_name, Item_no, Moocher_id, Rent_date),

	FOREIGN KEY (House_name) REFERENCES Houses(Name)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	FOREIGN KEY (Moocher_id) REFERENCES Moochers(Member_id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,

	FOREIGN KEY (Item_no) REFERENCES Items(Item_no)
		ON DELETE CASCADE
		ON UPDATE CASCADE
	);

DROP TABLE IF EXISTS Archived_rent_log;
CREATE TABLE Archived_rent_log (
	House_name	VARCHAR(20)	NOT NULL,
	Item_no		INTEGER		NOT NULL,
	Moocher_id	INTEGER		NOT NULL,
	Rent_date	DATE 		NOT NULL,
	Return_date	DATE 		NOT NULL,
	PRIMARY KEY (House_name, Item_no, Moocher_id, Rent_date)
);

CREATE VIEW In_stock AS
SELECT DISTINCT I.Item_no, I.Name, I.Amazon_URL, I.Quality
FROM items I, rent_log R
WHERE I.CanMooch=1 AND ((NOT EXISTS (SELECT *
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