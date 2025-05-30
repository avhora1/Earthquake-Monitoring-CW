-- SQL Server Migration Script

-- Drop existing tables if they exist
DROP TABLE IF EXISTS stock_list;
DROP TABLE IF EXISTS artefacts;
DROP TABLE IF EXISTS pallets;
DROP TABLE IF EXISTS earthquakes;
DROP TABLE IF EXISTS observatories;

-- Table structure for 'observatories'
CREATE TABLE observatories (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(50) NOT NULL,
    est_date DATE NOT NULL,
    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL
);

-- Table structure for 'earthquakes'
CREATE TABLE earthquakes(
    id VARCHAR(50) PRIMARY KEY, 
    country VARCHAR(20),
    country_id INT,
    magnitude FLOAT,
    [type] VARCHAR(8),
    [date] DATE,
    [time] TIME,
    latitude FLOAT, 
    longitude FLOAT,
    logged_date DATETIME DEFAULT GETDATE(),
    user_id INT,
    observatory_id INT
);

-- Table structure for 'pallets'
CREATE TABLE pallets (
    id INT IDENTITY(1,1) PRIMARY KEY,
    pallet_size NVARCHAR(4) NOT NULL,
    arrival_date DATETIME NOT NULL
);

-- Table structure for 'artefacts'
CREATE TABLE artefacts (
    id INT IDENTITY(1,1) PRIMARY KEY,
    earthquake_id VARCHAR(50) NOT NULL,
    type NVARCHAR(50) NOT NULL,
    description NVARCHAR(MAX) NOT NULL,
    time_stamp DATETIME NOT NULL,
    shelving_loc CHAR(1) NOT NULL,
    pallet_id INT NULL,
    required NVARCHAR(3) NOT NULL DEFAULT 'Yes'
);

-- Table structure for 'stock_list'
CREATE TABLE stock_list (
    id INT IDENTITY(1,1) PRIMARY KEY,
    artifact_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    availability NVARCHAR(3) NOT NULL DEFAULT 'Yes'
);

-- Add foreign key constraints after all tables are created
-- Add foreign key constraint to earthquakes table
ALTER TABLE earthquakes
ADD CONSTRAINT FK_Earthquakes_Observatories FOREIGN KEY (observatory_id) REFERENCES observatories (id) ON UPDATE CASCADE;
ALTER TABLE earthquakes
ADD CONSTRAINT FK_Earthquakes_Registered_accounts FOREIGN KEY (user_id) REFERENCES registered_accounts (id) ON UPDATE CASCADE;
    --FOREIGN KEY (user_id) REFERENCES registered_accounts(id),
    --FOREIGN KEY (observatory_id) REFERENCES observatories(id)

-- Add foreign key constraint to artefacts table for earthquakes
ALTER TABLE artefacts
ADD CONSTRAINT FK_Artefacts_Earthquakes FOREIGN KEY (earthquake_id) REFERENCES earthquakes (id) ON UPDATE CASCADE;

-- Add foreign key constraint to artefacts table for pallets
ALTER TABLE artefacts
ADD CONSTRAINT FK_Artefacts_Pallets FOREIGN KEY (pallet_id) REFERENCES pallets (id) ON UPDATE CASCADE;

-- Add foreign key constraint to stock_list table
ALTER TABLE stock_list
ADD CONSTRAINT FK_StockList_Artefacts FOREIGN KEY (artifact_id) REFERENCES artefacts (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Adding the first name and surname to the registered users database
-- ALTER TABLE registered_accounts
-- ADD firstName VARCHAR(20)  NULL,
--    lastName VARCHAR(30) NULl;