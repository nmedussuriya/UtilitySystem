USE UtilityManagementDB;
GO

-- 1 Create User table
CREATE TABLE [User] (
    user_id INT PRIMARY KEY IDENTITY(1,1),
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contact_no VARCHAR(15),
    role VARCHAR(50) NOT NULL
);
GO

-- 2 Create role-specific tables
CREATE TABLE Admin (
    admin_id INT PRIMARY KEY IDENTITY(1,1),
    staff_role VARCHAR(50),
    joined_date DATE,
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES [User](user_id)
);
GO

CREATE TABLE Customer (
    customer_id INT PRIMARY KEY IDENTITY(1,1),
    address VARCHAR(200),
    nic_no VARCHAR(20) UNIQUE,
    customer_type VARCHAR(50),
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES [User](user_id)
);
GO

CREATE TABLE Field_Officer (
    officer_id INT PRIMARY KEY IDENTITY(1,1),
    area_assigned VARCHAR(100),
    designation VARCHAR(50),
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES [User](user_id)
);
GO

CREATE TABLE Cashier (
    cashier_id INT PRIMARY KEY IDENTITY(1,1),
    branch_name VARCHAR(100),
    shift_name VARCHAR(50),
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES [User](user_id)
);
GO

CREATE TABLE Manager (
    manager_id INT PRIMARY KEY IDENTITY(1,1),
    department VARCHAR(100),
    report_access_level VARCHAR(50),
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES [User](user_id)
);
GO


CREATE TABLE Tariff (
    tariff_id INT PRIMARY KEY IDENTITY(1,1),
    utility_type VARCHAR(20) NOT NULL,     
    connection_type VARCHAR(50) NOT NULL, 
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);
GO

CREATE TABLE Electricity_Tariff_Slab (
    slab_id INT PRIMARY KEY IDENTITY(1,1),
    tariff_id INT NOT NULL,
    min_unit INT NOT NULL,
    max_unit INT NULL,                   
    rate_per_unit DECIMAL(10,2) NOT NULL,
    fixed_charge DECIMAL(10,2) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,

    FOREIGN KEY (tariff_id) REFERENCES Tariff(tariff_id)
);
GO

CREATE TABLE Water_Tariff_Slab (
    slab_id INT PRIMARY KEY IDENTITY(1,1),
    tariff_id INT NOT NULL,
    min_unit INT NOT NULL,
    max_unit INT NULL,                    
    rate_per_m3 DECIMAL(10,2) NOT NULL,
    fixed_charge DECIMAL(10,2) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,

    FOREIGN KEY (tariff_id) REFERENCES Tariff(tariff_id)
);
GO


CREATE TABLE [Connection] (
    connection_id INT PRIMARY KEY IDENTITY(1,1),
    connection_date DATE,
    status VARCHAR(50) DEFAULT 'Active',
    customer_id INT, 
    tariff_id INT,
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id),
    FOREIGN KEY (tariff_id) REFERENCES Tariff(tariff_id)
);
GO


CREATE TABLE Electricity (
    connection_id INT PRIMARY KEY,
    install_date DATE,
    meter_type VARCHAR(50),
    voltage VARCHAR(20),
    FOREIGN KEY (connection_id) REFERENCES [Connection](connection_id)

);
GO


CREATE TABLE Water (
    connection_id INT PRIMARY KEY,
    install_date DATE,
    pipe_size VARCHAR(20),
    pressure VARCHAR(20),
    FOREIGN KEY (connection_id) REFERENCES [Connection](connection_id)
);
GO


CREATE TABLE Meter_Reading (
    reading_id INT PRIMARY KEY IDENTITY(1,1),
    reading_date DATE,
    previous_reading DECIMAL(10,2),
    current_reading DECIMAL(10,2),
    consumption DECIMAL(10,2), 
    connection_id INT,
    officer_id INT,
    FOREIGN KEY (connection_id) REFERENCES [Connection](connection_id),
    FOREIGN KEY (officer_id) REFERENCES Field_Officer(officer_id)
);
GO


CREATE TABLE Bill (
    bill_id INT PRIMARY KEY IDENTITY(1,1),
    issue_date DATE,
    due_date DATE,
    total_amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'Unpaid', 
    connection_id INT,
    FOREIGN KEY (connection_id) REFERENCES [Connection](connection_id)
);
GO


CREATE TABLE Payment (
    payment_id INT PRIMARY KEY IDENTITY(1,1),
    payment_date DATE DEFAULT GETDATE(),
    amount_paid DECIMAL(10,2),
    bill_id INT,
    cashier_id INT NULL, 
   FOREIGN KEY (bill_id) REFERENCES Bill(bill_id),
    FOREIGN KEY (cashier_id) REFERENCES Cashier(cashier_id)
);
GO

 
CREATE TABLE Cash (
    receipt_no VARCHAR(50) PRIMARY KEY,
    counter_no VARCHAR(20),
    payment_id INT UNIQUE,  
    FOREIGN KEY (payment_id) REFERENCES Payment(payment_id)
);
GO

 
CREATE TABLE Card (
    card_payment_id INT PRIMARY KEY IDENTITY(1,1),
    card_type VARCHAR(50),
    card_number VARCHAR(20),  
    payment_id INT UNIQUE, 
    FOREIGN KEY (payment_id) REFERENCES Payment(payment_id)
);
GO

 
CREATE TABLE Online_Payment (
    online_txn_id INT PRIMARY KEY IDENTITY(1,1),
    platform_name VARCHAR(50),
    transaction_ref VARCHAR(100),
    payment_id INT UNIQUE,  
    FOREIGN KEY (payment_id) REFERENCES Payment(payment_id)
);
GO

CREATE TABLE Issue (
    issue_id INT PRIMARY KEY IDENTITY(1,1),
    service_type VARCHAR(50) NOT NULL,       
    customer_id INT NOT NULL,                 
    connection_id INT NULL,                  
    location VARCHAR(200),                    
    description VARCHAR(MAX) NOT NULL,
    priority VARCHAR(20) DEFAULT 'Medium',    
    status VARCHAR(20) DEFAULT 'Open',        
    reported_date DATETIME DEFAULT GETDATE(),
    resolved_date DATETIME NULL,
    assigned_officer INT NULL,               
    resolution_notes VARCHAR(MAX) NULL,

    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id),
    FOREIGN KEY (connection_id) REFERENCES [Connection](connection_id),
    FOREIGN KEY (assigned_officer) REFERENCES Field_Officer(officer_id)
);
GO

 
/*   FUNCTION 1: SLAB-BASED BILL CALCULATION*/

CREATE FUNCTION dbo.fn_CalculateSlabAmount
(
    @consumption INT,
    @tariff_id INT,
    @utility_type VARCHAR(20)
)
RETURNS DECIMAL(10,2)
AS
BEGIN
    DECLARE @total DECIMAL(10,2) = 0;

    -- ELECTRICITY SLAB CALCULATION
    IF (@utility_type = 'Electricity')
    BEGIN
        SELECT @total = SUM(
            CASE
                WHEN @consumption >= min_unit
                THEN
                    (CASE
                        WHEN max_unit IS NULL OR @consumption <= max_unit
                        THEN @consumption - min_unit + 1
                        ELSE max_unit - min_unit + 1
                    END) * rate_per_unit
                ELSE 0
            END
        )
        FROM Electricity_Tariff_Slab
        WHERE tariff_id = @tariff_id;
    END

    -- WATER SLAB CALCULATION
    IF (@utility_type = 'Water')
    BEGIN
        SELECT @total = SUM(
            CASE
                WHEN @consumption >= min_unit
                THEN
                    (CASE
                        WHEN max_unit IS NULL OR @consumption <= max_unit
                        THEN @consumption - min_unit + 1
                        ELSE max_unit - min_unit + 1
                    END) * rate_per_m3
                ELSE 0
            END
        )
        FROM Water_Tariff_Slab
        WHERE tariff_id = @tariff_id;
    END

    RETURN @total;
END;
GO

/*  FUNCTION 2: GET FIXED CHARGE*/

CREATE FUNCTION dbo.fn_GetFixedCharge
(
    @tariff_id INT,
    @utility_type VARCHAR(20)
)
RETURNS DECIMAL(10,2)
AS
BEGIN
    DECLARE @fixed DECIMAL(10,2);

    IF (@utility_type = 'Electricity')
        SELECT @fixed = MAX(fixed_charge)
        FROM Electricity_Tariff_Slab WHERE tariff_id = @tariff_id;

    IF (@utility_type = 'Water')
        SELECT @fixed = MAX(fixed_charge)
        FROM Water_Tariff_Slab WHERE tariff_id = @tariff_id;

    RETURN ISNULL(@fixed,0);
END;
GO

/* TRIGGER 1: AUTO CALCULATE CONSUMPTION */

CREATE TRIGGER trg_CalculateConsumption
ON Meter_Reading
AFTER INSERT
AS
BEGIN
    UPDATE m
    SET consumption = i.current_reading - i.previous_reading
    FROM Meter_Reading m
    JOIN inserted i ON m.reading_id = i.reading_id;
END;
GO

/*TRIGGER 2: AUTO UPDATE BILL STATUS*/

CREATE TRIGGER trg_UpdateBillStatus
ON Payment
AFTER INSERT
AS
BEGIN
    UPDATE b
    SET status = 'Paid'
    FROM Bill b
    JOIN inserted i ON b.bill_id = i.bill_id
    WHERE b.total_amount <= (
        SELECT SUM(amount_paid) FROM Payment WHERE bill_id = b.bill_id
    );
END;
GO

--TRIGGER 3:  Update Tariff effective_from when a new Electricity slab is added

CREATE TRIGGER trg_UpdateElectricityTariffEffectiveDate
ON Electricity_Tariff_Slab
AFTER INSERT
AS
BEGIN
    UPDATE t
    SET t.effective_from = 
        (SELECT MIN(effective_from) 
         FROM Electricity_Tariff_Slab 
         WHERE tariff_id = t.tariff_id)
    FROM Tariff t
    JOIN inserted i ON t.tariff_id = i.tariff_id;
END;
GO

-- TRIGGER 4:  Update Tariff effective_from when a new Water slab is added

CREATE TRIGGER trg_UpdateWaterTariffEffectiveDate
ON Water_Tariff_Slab
AFTER INSERT
AS
BEGIN
    UPDATE t
    SET t.effective_from = 
        (SELECT MIN(effective_from) 
         FROM Water_Tariff_Slab 
         WHERE tariff_id = t.tariff_id)
    FROM Tariff t
    JOIN inserted i ON t.tariff_id = i.tariff_id;
END;
GO





/*VIEW 1: UNPAID BILLS */

CREATE VIEW View_UnpaidBills
AS
SELECT
    b.bill_id,
    u.name AS CustomerName,
    b.total_amount,
    b.status,
    b.due_date
FROM Bill b
JOIN Connection c ON b.connection_id = c.connection_id
JOIN Customer cu ON c.customer_id = cu.customer_id
JOIN [User] u ON cu.user_id = u.user_id
WHERE b.status <> 'Paid';
GO

/* VIEW 2: MONTHLY REVENUE*/

CREATE VIEW View_MonthlyRevenue
AS
SELECT
    YEAR(payment_date) AS Year,
    MONTH(payment_date) AS Month,
    SUM(amount_paid) AS Revenue
FROM Payment
GROUP BY YEAR(payment_date), MONTH(payment_date);
GO

/* PROCEDURE 1: GENERATE BILL (SLAB-BASED) */

CREATE PROCEDURE sp_GenerateBill_Slab
@reading_id INT
AS
BEGIN
    DECLARE @consumption INT;
    DECLARE @connection_id INT;
    DECLARE @tariff_id INT;
    DECLARE @utility_type VARCHAR(20);
    DECLARE @total DECIMAL(10,2);
    DECLARE @fixed DECIMAL(10,2);

    SELECT
        @consumption = mr.consumption,
        @connection_id = mr.connection_id,
        @tariff_id = c.tariff_id,
        @utility_type = t.utility_type
    FROM Meter_Reading mr
    JOIN Connection c ON mr.connection_id = c.connection_id
    JOIN Tariff t ON c.tariff_id = t.tariff_id
    WHERE mr.reading_id = @reading_id;

    SET @total = dbo.fn_CalculateSlabAmount(@consumption, @tariff_id, @utility_type);
    SET @fixed = dbo.fn_GetFixedCharge(@tariff_id, @utility_type);

    INSERT INTO Bill (issue_date, due_date, total_amount, status, connection_id)
    VALUES (GETDATE(), DATEADD(DAY,30,GETDATE()), @total + @fixed, 'Unpaid', @connection_id);
END;
GO

/*  PROCEDURE 2: REGISTER CUSTOMER*/

CREATE PROCEDURE sp_RegisterCustomer
    @name VARCHAR(100),
    @username VARCHAR(50),
    @email VARCHAR(100),
    @contact VARCHAR(15),
    @address VARCHAR(200),
    @nic VARCHAR(20),
    @type VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;

    -- Check for duplicate username
    IF EXISTS (SELECT 1 FROM [User] WHERE username = @username)
    BEGIN
        RAISERROR('Username already exists!', 16, 1);
        RETURN;
    END

    -- Check for duplicate email
    IF EXISTS (SELECT 1 FROM [User] WHERE email = @email)
    BEGIN
        RAISERROR('Email already exists!', 16, 1);
        RETURN;
    END

    -- Check for duplicate NIC
    IF EXISTS (SELECT 1 FROM Customer WHERE nic_no = @nic)
    BEGIN
        RAISERROR('NIC already exists!', 16, 1);
        RETURN;
    END

    DECLARE @uid INT;
    DECLARE @genPass VARCHAR(50);

    -- Generate random password
    SET @genPass = LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'A1!';

    -- Insert into User table
    INSERT INTO [User] (name, username, password, email, contact_no, role)
    VALUES (@name, @username, @genPass, @email, @contact, 'Customer');

    SET @uid = SCOPE_IDENTITY();

    -- Insert into Customer table
    INSERT INTO Customer (address, nic_no, customer_type, user_id)
    VALUES (@address, @nic, @type, @uid);

    -- Return generated password
    SELECT @genPass AS created_password;
    SELECT @uid AS NewUserID;
END
GO



INSERT INTO [User] (name, username, password, email, contact_no, role)
VALUES

-- Existing 12 users
('Alice Brown', 'aliceb', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'A1!', 'alice@example.com', '0771111111', 'Customer'),
('Bob Green', 'bobg', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'B2@', 'bob@example.com', '0772222222', 'Customer'),
('Charlie White', 'charliew', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'C3#', 'charlie@example.com', '0773333333', 'Admin'),
('Diana Black', 'dianab', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'D4$', 'diana@example.com', '0774444444', 'Admin'),
('Ethan Grey', 'ethang', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'E5%', 'ethan@example.com', '0775555555', 'Field Officer'),
('Fiona Blue', 'fionab', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'F6&', 'fiona@example.com', '0776666666', 'Field Officer'),
('George Red', 'georger', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'G7*', 'george@example.com', '0777777777', 'Cashier'),
('Hannah Gold', 'hannahg', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'H8!', 'hannah@example.com', '0778888888', 'Cashier'),
('Ian Silver', 'ians', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'I9@', 'ian@example.com', '0779999999', 'Manager'),
('Julia Violet', 'juliav', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'J0#', 'julia@example.com', '0770000000', 'Manager'),
('Kevin Orange', 'kevino', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'K1$', 'kevin@example.com', '0771234567', 'Customer'),
('Laura Pink', 'laurap', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'L2%', 'laura@example.com', '0777654321', 'Admin'),
('Liam Black', 'liamb', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'L1!', 'liamb@example.com', '0771111112', 'Customer'),
('Mia White', 'miaw', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'M2@', 'miaw@example.com', '0771111113', 'Customer'),
('Noah Green', 'noahg', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'N3#', 'noahg@example.com', '0771111114', 'Customer'),
('Olivia Brown', 'oliviab', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'O4$', 'oliviab@example.com', '0771111115', 'Customer'),
('Paul Grey', 'paulg', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'P5%', 'paulg@example.com', '0771111116', 'Customer'),
('Quinn Blue', 'quinnb', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'Q6&', 'quinnb@example.com', '0771111117', 'Customer'),
('Ruby Red', 'rubyr', LEFT(CONVERT(VARCHAR(50), NEWID()), 8) + 'R7*', 'rubyr@example.com', '0771111118', 'Customer')
;

GO

DECLARE @UserID INT;

-- Customers
SELECT @UserID = user_id FROM [User] WHERE username = 'aliceb';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('1 Main St, Colombo', '901111111V', 'Residential', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'bobg';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('2 Lake Rd, Kandy', '901222222V', 'Residential', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'kevino';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('3 River Rd, Galle', '901333333V', 'Commercial', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'liamb';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('12 Flower Road, Colombo 03', '901234567V', 'Residential', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'miaw';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('45 Palm Avenue, Kandy', '902345678V', 'Commercial', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'noahg';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('78 Ocean Drive, Galle', '903456789V', 'Residential', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'oliviab';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('23 Hill Street, Negombo', '904567890V', 'Commercial', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'paulg';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('56 Lake Road, Colombo 07', '905678901V', 'Residential', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'quinnb';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('89 River Street, Matara', '906789012V', 'Commercial', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'rubyr';
INSERT INTO Customer (address, nic_no, customer_type, user_id)
VALUES ('34 Garden Avenue, Jaffna', '907890123V', 'Residential', @UserID);

-- Admins
SELECT @UserID = user_id FROM [User] WHERE username = 'charliew';
INSERT INTO Admin (staff_role, joined_date, user_id)
VALUES ('Super Admin', '2023-01-01', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'dianab';
INSERT INTO Admin (staff_role, joined_date, user_id)
VALUES ('HR Admin', '2023-02-15', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'laurap';
INSERT INTO Admin (staff_role, joined_date, user_id)
VALUES ('Manager Admin', '2023-03-10', @UserID);

-- Field Officers
SELECT @UserID = user_id FROM [User] WHERE username = 'ethang';
INSERT INTO Field_Officer (area_assigned, designation, user_id)
VALUES ('Colombo North', 'Senior Officer', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'fionab';
INSERT INTO Field_Officer (area_assigned, designation, user_id)
VALUES ('Kandy Central', 'Junior Officer', @UserID);

-- Cashiers
SELECT @UserID = user_id FROM [User] WHERE username = 'georger';
INSERT INTO Cashier (branch_name, shift_name, user_id)
VALUES ('Head Office', 'Morning', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'hannahg';
INSERT INTO Cashier (branch_name, shift_name, user_id)
VALUES ('Colombo Branch', 'Evening', @UserID);

-- Managers
SELECT @UserID = user_id FROM [User] WHERE username = 'ians';
INSERT INTO Manager (department, report_access_level, user_id)
VALUES ('Electricity', 'Full', @UserID);

SELECT @UserID = user_id FROM [User] WHERE username = 'juliav';
INSERT INTO Manager (department, report_access_level, user_id)
VALUES ('Water', 'Full', @UserID);
GO


-- Electricity Tariff
INSERT INTO Tariff (utility_type, connection_type, effective_from, admin_id)
VALUES ('Electricity', 'Domestic', '2025-01-01', 1);

-- Water Tariff
INSERT INTO Tariff (utility_type, connection_type, effective_from, admin_id)
VALUES ('Water', 'Domestic', '2025-01-01', 1);
GO

INSERT INTO Electricity_Tariff_Slab (tariff_id, min_unit, max_unit, rate_per_unit, fixed_charge,effective_from, effective_to)
VALUES
(1, 0, 30, 5.00, 150.00, '2025-01-01', '2025-12-31'),
(1, 31, 60, 10.00, 200.00,'2025-01-01', '2025-12-31'),
(1, 61, 90, 20.00, 300.00,'2025-01-01', '2025-12-31'),
(1, 91, 120, 30.00, 400.00,'2025-01-01', '2025-12-31'),
(1, 121, NULL, 45.00, 500.00,'2025-01-01', '2025-12-31');
GO

INSERT INTO Water_Tariff_Slab (tariff_id, min_unit, max_unit, rate_per_m3, fixed_charge,effective_from, effective_to)
VALUES
(2, 0, 5, 10.00, 100.00,'2025-01-01', '2025-12-31'),
(2, 6, 10, 20.00, 150.00,'2025-01-01', '2025-12-31'),
(2, 11, 20, 30.00, 200.00,'2025-01-01', '2025-12-31'),
(2, 21, 30, 40.00, 250.00,'2025-01-01', '2025-12-31'),
(2, 31, NULL, 60.00, 300.00,'2025-01-01', '2025-12-31');
GO

 
INSERT INTO [Connection] (connection_date, status, customer_id, tariff_id) VALUES 
('2024-02-10', 'Active', 1, 1),  
('2024-02-15', 'Active', 2, 2),
('2024-03-12', 'Active', 3, 1),
('2024-03-25', 'Active', 4, 1),
('2024-04-10', 'Active', 5, 2),
('2024-04-15', 'Active', 6, 1),
('2024-04-28', 'Active', 7, 2),
('2024-05-15', 'Active', 8, 2),
('2024-05-26', 'Active', 9, 1),
('2024-06-15', 'Active', 10, 2)
;

 
INSERT INTO Electricity (connection_id, meter_type, voltage) VALUES 
(1, 'Smart Meter', '210V'),
(3, 'Smart Meter', '230V'),
(4, 'Smart Meter', '240V'),
(6, 'Smart Meter', '250V'),
(9, 'Smart Meter', '260V');

INSERT INTO Water (connection_id, pipe_size, pressure) VALUES 
(2, '1/2 Inch', 'High'),
(5, '1 Inch', 'High'),
(7, '1/2 Inch', 'low'),
(8, '1 Inch', 'High'),
(10, '2 Inch', 'High')
;
 
INSERT INTO Meter_Reading (reading_date, previous_reading, current_reading, connection_id, officer_id)
VALUES
-- Electricity Connections
('2025-11-01', 1000, 1150, 1, 1),
('2025-11-01', 1200, 1360, 3, 1),
('2025-11-02', 500, 520, 4, 1),
('2025-11-03', 480, 500, 6, 1),
('2025-11-04', 1000, 1150, 9, 1),

-- Water Connections
('2025-11-01', 200, 220, 2, 2),
('2025-11-02', 1000, 1150, 5, 2),
('2025-11-03', 950, 1100, 7, 2),
('2025-11-04', 500, 520, 8, 2),
('2025-11-05', 300, 320, 10, 2);


INSERT INTO Bill (issue_date, due_date, total_amount, status, connection_id)
VALUES
-- Electricity Connections
(GETDATE(), DATEADD(DAY,30,GETDATE()), 3800, 'Unpaid', 1),  -- consumption 150
(GETDATE(), DATEADD(DAY,30,GETDATE()), 3950, 'Unpaid', 3),  -- consumption 160
(GETDATE(), DATEADD(DAY,30,GETDATE()), 650,  'Unpaid', 4),  -- consumption 20
(GETDATE(), DATEADD(DAY,30,GETDATE()), 600,  'Unpaid', 6),  -- consumption 20
(GETDATE(), DATEADD(DAY,30,GETDATE()), 3800, 'Unpaid', 9),  -- consumption 150

-- Water Connections
(GETDATE(), DATEADD(DAY,30,GETDATE()), 700,  'Unpaid', 2),  -- consumption 20
(GETDATE(), DATEADD(DAY,30,GETDATE()), 9050, 'Unpaid', 5),  -- consumption 150
(GETDATE(), DATEADD(DAY,30,GETDATE()), 8850, 'Unpaid', 7),  -- consumption 150
(GETDATE(), DATEADD(DAY,30,GETDATE()), 800,  'Unpaid', 8),  -- consumption 20
(GETDATE(), DATEADD(DAY,30,GETDATE()), 700,  'Unpaid', 10); -- consumption 20

-- 1️⃣ Full payment (Bill 1 fully settled)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (6825.00, 1, 1);

-- 2️⃣ Partial payment (Bill 2 – still pending)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (1000.00, 2, 1);

-- 3️⃣ Second partial payment for Bill 2 (still pending)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (1500.00, 2, 1);

-- 4️⃣ Final payment for Bill 2 (now becomes PAID)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (2000.00, 2, 1);

-- 5️⃣ Full payment (Bill 3)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (4750.00, 3, 2);

-- 6️⃣ Partial payment (Bill 4)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (3000.00, 4, 2);

-- 7️⃣ Another partial payment (Bill 4 – still unpaid)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (1500.00, 4, 2);

-- 8️⃣ Full payment (Bill 5)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (5200.00, 5, 1);

-- 9️⃣ Partial payment (Bill 6)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (2500.00, 6, 2);

-- 🔟 Final payment for Bill 6 (bill becomes PAID)
INSERT INTO Payment (amount_paid, bill_id, cashier_id)
VALUES (2700.00, 6, 2);


INSERT INTO Cash (receipt_no, counter_no, payment_id) VALUES
('RCPT-1001', 'C01', 1),
('RCPT-1002', 'C01', 2),
('RCPT-1003', 'C02', 3),
('RCPT-1004', 'C02', 4),
('RCPT-1005', 'C03', 5);

INSERT INTO Card (card_type, card_number, payment_id) VALUES
('Visa', '4111111111111111', 6),
('MasterCard', '5500000000000004', 7),
('Amex', '340000000000009', 8);


INSERT INTO Online_Payment (platform_name, transaction_ref, payment_id) VALUES
('PayHere', 'PH-TXN-9001', 9),
('Dialog eZ Cash', 'EZCASH-TXN-7788', 10);


INSERT INTO Issue (service_type, customer_id, connection_id, location, description, priority, assigned_officer)
VALUES
('Electricity', 1, 1, '1 Main St, Colombo', 'No power since morning', 'High', 1),
('Water', 2, 2, '2 Lake Rd, Kandy', 'Low water pressure', 'Medium', 2),
('Electricity', 3, 3, '3 River Rd, Galle', 'Meter malfunctioning', 'High', 1),
('Water', 5, 5, '45 Palm Avenue, Kandy', 'Leak in pipe', 'Critical', 2),
('Electricity', 6, 6, '78 Ocean Drive, Galle', 'Fuse keeps blowing', 'Medium', 1);
GO


EXEC sp_GenerateBill_Slab @reading_id = 1;
 
SELECT '--- USERS ---';
SELECT * FROM [User];
SELECT '--- BILLS (Check Status) ---';
SELECT * FROM Bill;
SELECT '--- PAYMENTS ---';
SELECT * FROM Payment;
SELECT '--- UNPAID BILLS VIEW (Should show only Bill 2, partially paid) ---';
SELECT * FROM View_UnpaidBills;

 
SELECT '--- METER READING (Check Consumption) ---';
SELECT * FROM Meter_Reading;
SELECT * FROM Issue;