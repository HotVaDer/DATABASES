CREATE OR ALTER PROCEDURE sp_Login
(
    @Username  VARCHAR(30),
    @Password  NVARCHAR(100)      -- PLAIN TEXT κωδικός που έγραψε ο user
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @UserID INT;
        DECLARE @Password_Hash VARBINARY(64);

        ------------------------------------------------------------
        -- 1) Hash the password provided at login
        ------------------------------------------------------------
        SET @Password_Hash = HASHBYTES('SHA2_512', @Password);

        ------------------------------------------------------------
        -- 2) Find the User_ID if the credentials are correct
        ------------------------------------------------------------
        SELECT @UserID = User_ID
        FROM AUTHENTICATION
        WHERE Username = @Username
          AND Password_Hash = @Password_Hash;

        IF @UserID IS NULL
        BEGIN
            ;THROW 91001, 'Invalid username or password.', 1;
        END

        ------------------------------------------------------------
        -- 3) Return user information (including Type_Name)
        ------------------------------------------------------------
        SELECT 
            U.User_ID,
            U.First_Name,
            U.Last_Name,
            U.Email,
            U.Type_Name
        FROM [USER] AS U
        WHERE U.User_ID = @UserID;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;
END;
GO
