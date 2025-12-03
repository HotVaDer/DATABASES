CREATE OR ALTER PROCEDURE sp_Login
(
    @Username  VARCHAR(30),
    @Password  NVARCHAR(100)
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @UserID INT;
    DECLARE @StoredHash VARBINARY(64);
    DECLARE @InputHash VARBINARY(64);
    DECLARE @First_Name VARCHAR(30);
    DECLARE @Last_Name VARCHAR(30);
    DECLARE @Email VARCHAR(50);
    DECLARE @Type_Name VARCHAR(50);

    ------------------------------------------------------------
    -- HASH PASSWORD
    ------------------------------------------------------------
    SET @InputHash = HASHBYTES('SHA2_512', @Password);

    ------------------------------------------------------------
    -- FETCH STORED HASH + USER INFO
    ------------------------------------------------------------
    SELECT  
        @UserID     = U.User_ID,
        @StoredHash = A.Password_Hash,
        @First_Name = U.First_Name,
        @Last_Name  = U.Last_Name,
        @Email      = U.Email,
        @Type_Name  = U.Type_Name
    FROM AUTHENTICATION A
    JOIN [USER] U ON U.User_ID = A.User_ID
    WHERE A.Username = @Username;

    ------------------------------------------------------------
    -- CASE 1: USER NOT FOUND
    ------------------------------------------------------------
    IF @UserID IS NULL
    BEGIN
        SELECT  
            Success = 0,
            Message = 'Invalid username';
        RETURN;
    END

    ------------------------------------------------------------
    -- CASE 2: PASSWORD WRONG
    ------------------------------------------------------------
    IF @StoredHash <> @InputHash
    BEGIN
        SELECT  
            Success = 0,
            Message = 'Invalid password';
        RETURN;
    END

    ------------------------------------------------------------
    -- CASE 3: SUCCESSFUL LOGIN
    ------------------------------------------------------------
    SELECT
        Success = 1,
        Message = 'Login successful',
        User_ID = @UserID,
        First_Name = @First_Name,
        Last_Name = @Last_Name,
        Email = @Email,
        Type_Name = @Type_Name;
END;
GO
