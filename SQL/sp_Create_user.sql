CREATE OR ALTER PROCEDURE sp_CreateUser
(
    @First_Name     NVARCHAR(50),
    @Last_Name      NVARCHAR(50),
    @Birth_Date     DATE,
    @Email          NVARCHAR(100),
    @Address        NVARCHAR(200),
    @Gender         NVARCHAR(10),

    @Username       NVARCHAR(50),
    @Password       NVARCHAR(200),

    @User_Type_Name NVARCHAR(50) = N'passenger'  -- default role
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 0) Uniqueness checks
        ------------------------------------------------------------
        IF EXISTS (SELECT 1 FROM [USER] WHERE Email = @Email)
            THROW 50001, 'Email already in use.', 1;

        IF EXISTS (SELECT 1 FROM AUTHENTICATION WHERE Username = @Username)
            THROW 50002, 'Username already in use.', 1;

        ------------------------------------------------------------
        -- 1) Insert into USER
        ------------------------------------------------------------
        INSERT INTO [USER] (First_Name, Last_Name, Birth_Date, Email, Address, Gender)
        VALUES (@First_Name, @Last_Name, @Birth_Date, @Email, @Address, @Gender);

        DECLARE @UserID INT = SCOPE_IDENTITY();

        ------------------------------------------------------------
        -- 2) Insert into AUTHENTICATION with hashed password
        ------------------------------------------------------------
        INSERT INTO AUTHENTICATION (User_ID, Username, Password_Hash)
        VALUES (
            @UserID,
            @Username,
            HASHBYTES('SHA2_256', @Password)
        );

        ------------------------------------------------------------
        -- 3) Resolve role by name (look up User_Type_ID)
        ------------------------------------------------------------
        DECLARE @User_Type_ID INT;

        SELECT @User_Type_ID = User_Type_ID
        FROM USER_TYPE
        WHERE Type_Name = @User_Type_Name;

        IF @User_Type_ID IS NULL
            THROW 50003, 'Invalid user type name.', 1;

        ------------------------------------------------------------
        -- 4) Assign role using USER_TYPE_MAP
        ------------------------------------------------------------
        INSERT INTO USER_TYPE_MAP (User_ID, User_Type_ID)
        VALUES (@UserID, @User_Type_ID);

        ------------------------------------------------------------
        -- 5) Return newly created user ID
        ------------------------------------------------------------
        COMMIT TRANSACTION;
        SELECT @UserID AS NewUserID;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;
        THROW;
    END CATCH;
END;
GO
