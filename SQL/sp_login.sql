CREATE OR ALTER PROCEDURE sp_Login
(
    @Username  VARCHAR(30),
    @Password  NVARCHAR(100)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @User_ID INT;
        DECLARE @Password_Hash VARBINARY(64);

        -- Hash the password provided at login
        SET @Password_Hash = HASHBYTES('SHA2_512', @Password);

        -- Find matching user
        SELECT @User_ID = User_ID
        FROM AUTHENTICATION
        WHERE Username = @Username
          AND Password_Hash = @Password_Hash;

        -- If no user found, return error
        IF @User_ID IS NULL
        BEGIN
            RAISERROR('Invalid username or password.', 16, 1);
            RETURN;
        END

        SELECT @User_ID AS User_ID;
    END TRY

    BEGIN CATCH
        DECLARE @ErrMsg NVARCHAR(4000) = ERROR_MESSAGE();
        RAISERROR(@ErrMsg, 16, 1);
    END CATCH;
END;
GO
