BEGIN TRANSACTION;
CREATE TABLE Cities (Id INTEGER PRIMARY KEY, Name TEXT);
CREATE TABLE Contacts (Identifier TEXT, Email TEXT, Id INTEGER PRIMARY KEY, Name TEXT, Phone TEXT);
CREATE TABLE Ride (Comment TEXT, Status NUMERIC, TimeEvening NUMERIC, TimeMorning NUMERIC, ContactId NUMERIC, DestCityId NUMERIC, DestLocation TEXT, SrcCityId NUMERIC, SrcLocation TEXT, Id INTEGER PRIMARY KEY);
CREATE INDEX RideDest ON Ride(DestCityId ASC);
CREATE INDEX RideFrom ON Ride(SrcCityId ASC);
CREATE UNIQUE INDEX Unique_City ON Cities(Name ASC);
COMMIT;
