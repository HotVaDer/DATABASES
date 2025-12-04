CREATE   PROCEDURE sp_RegisterDriver
(
    @UserID INT,
    @Photo VARBINARY(MAX),
    @EU_Residence_Pass VARCHAR(50)
)
AS
BEGIN
    SET NOCOUNT ON;

    ------------------------------------------------------------
    -- 1) Check if user exists
    ------------------------------------------------------------
    IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @UserID)
    BEGIN
        SELECT 'USER_NOT_FOUND' AS Status;
        RETURN;
    END

    ------------------------------------------------------------
    -- 2) Check if user already applied as driver
    ------------------------------------------------------------
    IF EXISTS (SELECT 1 FROM DRIVER WHERE User_ID = @UserID)
    BEGIN
        SELECT 'ALREADY_DRIVER' AS Status;
        RETURN;
    END

    ------------------------------------------------------------
    -- 3) Insert into DRIVER table with status = pending
    ------------------------------------------------------------
    INSERT INTO DRIVER (User_ID, Photo, EU_Residence_Pass, Status)
    VALUES (@UserID, @Photo, @EU_Residence_Pass, 'pending');

    SELECT 'SUCCESS' AS Status;
END
