CREATE   PROCEDURE sp_RegisterUser
    @FirstName      VARCHAR(30),
    @LastName       VARCHAR(30),
    @BirthDate      DATE,
    @Email          VARCHAR(50),
    @Address        VARCHAR(30),
    @Gender         VARCHAR(10),
    @TypeName       VARCHAR(50),
    @PasswordHash   VARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    -- Check if email already exists
    IF EXISTS (SELECT 1 FROM [USER] WHERE Email = @Email)
    BEGIN
        SELECT 'EMAIL_EXISTS' AS Status;
        RETURN;
    END

    -- Insert user
    INSERT INTO [USER] (First_Name, Last_Name, Birth_Date, Email, Address, Gender, Type_Name)
    VALUES (@FirstName, @LastName, @BirthDate, @Email, @Address, @Gender, @TypeName);

    DECLARE @NewUserID INT = SCOPE_IDENTITY();

    -- Insert into AUTHENTICATION table
    INSERT INTO AUTHENTICATION (Username, Password_Hash, User_ID)
    VALUES (@Email, @PasswordHash, @NewUserID);

    SELECT 'SUCCESS' AS Status, @NewUserID AS User_ID;
END
