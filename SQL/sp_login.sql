CREATE OR ALTER PROCEDURE sp_Login
(
    @Username VARCHAR(30)
)
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        U.User_ID,
        U.First_Name,
        U.Last_Name,
        U.Email,
        U.Type_Name,
        A.Password_Hash
    FROM AUTHENTICATION A
    JOIN [USER] U ON U.User_ID = A.User_ID
    WHERE A.Username = @Username;
END;
GO
