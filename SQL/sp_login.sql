CREATE OR ALTER PROCEDURE sp_Login
(
    @Username NVARCHAR(50),
    @Password NVARCHAR(200)
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @UserID INT;

    ------------------------------------------------------------
    -- 1) Find user by username
    ------------------------------------------------------------
    SELECT @UserID = User_ID
    FROM AUTHENTICATION
    WHERE Username = @Username;

    IF @UserID IS NULL
    BEGIN
        RAISERROR('Invalid username.', 16, 1);
        RETURN;
    END

    ------------------------------------------------------------
    -- 2) Validate password hash
    ------------------------------------------------------------
    IF NOT EXISTS (
        SELECT 1
        FROM AUTHENTICATION
        WHERE User_ID = @UserID
          AND Password_Hash = HASHBYTES('SHA2_256', @Password)
    )
    BEGIN
        RAISERROR('Invalid password.', 16, 1);
        RETURN;
    END

    ------------------------------------------------------------
    -- 3) Successful login → return user data + roles
    ------------------------------------------------------------
    SELECT 
        U.User_ID,
        U.First_Name,
        U.Last_Name,
        U.Email,
        UT.Type_Name AS User_Role
    FROM [USER] U
    JOIN USER_TYPE_MAP UTM ON U.User_ID = UTM.User_ID
    JOIN USER_TYPE UT ON UTM.User_Type_ID = UT.User_Type_ID
    WHERE U.User_ID = @UserID;
END;
GO
