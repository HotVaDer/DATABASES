CREATE OR ALTER PROCEDURE sp_RegisterUser
(
    @First_Name      VARCHAR(30),
    @Last_Name       VARCHAR(30),
    @Birth_Date      DATE,
    @Email           VARCHAR(50),
    @Address         VARCHAR(30),
    @Gender          VARCHAR(10),
    @Username        VARCHAR(30),
    @Password        NVARCHAR(100)      -- PLAIN TEXT κωδικός από την εφαρμογή
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Έλεγχος αν υπάρχει ήδη αυτό το Username
        ------------------------------------------------------------
        IF EXISTS (SELECT 1 FROM AUTHENTICATION WHERE Username = @Username)
            THROW 90001, 'Username already exists.', 1;

        ------------------------------------------------------------
        -- 2) Έλεγχος αν υπάρχει ήδη αυτό το Email
        ------------------------------------------------------------
        IF EXISTS (SELECT 1 FROM [USER] WHERE Email = @Email)
            THROW 90002, 'Email already exists.', 1;

        ------------------------------------------------------------
        -- 3) Hash του password με SHA2_512
        ------------------------------------------------------------
        DECLARE @Password_Hash VARBINARY(64);
        SET @Password_Hash = HASHBYTES('SHA2_512', @Password);

        ------------------------------------------------------------
        -- 4) Εισαγωγή στον πίνακα [USER] ως passenger
        ------------------------------------------------------------
        INSERT INTO [USER]
        (
            First_Name, Last_Name, Birth_Date, Email,
            Address, Gender, Type_Name
        )
        VALUES
        (
            @First_Name, @Last_Name, @Birth_Date, @Email,
            @Address, @Gender, 'passenger'
        );

        DECLARE @NewUserID INT = SCOPE_IDENTITY();

        ------------------------------------------------------------
        -- 5) Εισαγωγή credentials στον πίνακα AUTHENTICATION
        ------------------------------------------------------------
        INSERT INTO AUTHENTICATION
        (
            Username, Password_Hash, User_ID
        )
        VALUES
        (
            @Username, @Password_Hash, @NewUserID
        );

        ------------------------------------------------------------
        -- 6) Επιτυχία – επιστροφή User_ID
        ------------------------------------------------------------
        COMMIT TRANSACTION;
        SELECT @NewUserID AS User_ID;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;

        THROW;
    END CATCH;
END;
GO
