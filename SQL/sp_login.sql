CREATE   PROCEDURE sp_Login
(
    @Username  VARCHAR(30)
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @UserID INT;
    DECLARE @StoredHash NVARCHAR(255);
    DECLARE @First_Name VARCHAR(30);
    DECLARE @Last_Name VARCHAR(30);
    DECLARE @Email VARCHAR(50);
    DECLARE @Type_Name VARCHAR(50);

    ------------------------------------------------------------
    -- FETCH USER + HASH
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
    -- USER NOT FOUND
    ------------------------------------------------------------
    IF @UserID IS NULL
    BEGIN
        SELECT Success = 0, Message = 'Invalid username';
        RETURN;
    END

    ------------------------------------------------------------
    -- SUCCESS – RETURN USER & HASH
    ------------------------------------------------------------
    SELECT
        Success = 1,
        Message = 'User found',
        User_ID = @UserID,
        First_Name = @First_Name,
        Last_Name = @Last_Name,
        Email = @Email,
        Type_Name = @Type_Name,
        Password_Hash = @StoredHash;
END;

