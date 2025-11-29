CREATE OR ALTER PROCEDURE sp_Update_User
CREATE OR ALTER PROCEDURE sp_UpdateUser
(
    @UserID        INT,
    @First_Name    NVARCHAR(50) = NULL,
    @Last_Name     NVARCHAR(50) = NULL,
    @Birth_Date    DATE = NULL,
    @Email         NVARCHAR(100) = NULL,
    @Address       NVARCHAR(200) = NULL,
    @Gender        NVARCHAR(10) = NULL
)
AS 
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 0) Check if user exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @UserID)
            THROW 50004, 'User not found.', 1;

        ------------------------------------------------------------
        -- 1) Uniqueness check for Email if being updated
        ------------------------------------------------------------
        IF @Email IS NOT NULL AND EXISTS (
            SELECT 1 FROM [USER]
            WHERE Email = @Email AND User_ID <> @UserID
        )
            THROW 50001, 'Email already in use.', 1;

        ------------------------------------------------------------
        -- 2) Update USER table with provided fields
        ------------------------------------------------------------
        UPDATE [USER]
        SET 
            First_Name = COALESCE(@First_Name, First_Name),
            Last_Name  = COALESCE(@Last_Name, Last_Name),
            Birth_Date = COALESCE(@Birth_Date, Birth_Date),
            Email      = COALESCE(@Email, Email),
            Address    = COALESCE(@Address, Address),
        WHERE User_ID = @UserID;
        COMMIT TRANSACTION;
    
        SELECT 
            User_ID,
            First_Name,
            Last_Name,
            Birth_Date,
            Email,
            [Address],
            Gender
        FROM [USER]
        WHERE User_ID = @UserID;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;
        THROW;
    END CATCH;
END;
GO
