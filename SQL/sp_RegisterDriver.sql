CREATE OR ALTER PROCEDURE sp_RegisterDriver
(
    @UserID INT,
    @Photo VARBINARY(255),
    @EU_Residence_Pass VARCHAR(50)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Check if user exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @UserID)
            THROW 80001, 'User does not exist.', 1;

        ------------------------------------------------------------
        -- 2) Check if user already applied as driver
        ------------------------------------------------------------
        IF EXISTS (SELECT 1 FROM DRIVER WHERE User_ID = @UserID)
            THROW 80002, 'User has already submitted a driver application.', 1;

        ------------------------------------------------------------
        -- 3) Insert driver application with status = pending
        ------------------------------------------------------------
        INSERT INTO DRIVER (User_ID, Photo, EU_Residence_Pass, Status)
        VALUES (@UserID, @Photo, @EU_Residence_Pass, 'pending');

        COMMIT TRANSACTION;

        SELECT 'Application submitted successfully' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 
            ROLLBACK TRANSACTION;

        THROW;
    END CATCH;
END;
GO
