CREATE OR ALTER PROCEDURE sp_DenyDriver
(
    @UserID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @IsDriver INT;

        ------------------------------------------------------------
        -- 1) Check that the user IS a driver
        ------------------------------------------------------------
        SELECT @IsDriver = COUNT(*)
        FROM DRIVER
        WHERE User_ID = @UserID;

        IF @IsDriver = 0
        BEGIN
            ;THROW 95001, 'User is not a driver.', 1;
        END


        ------------------------------------------------------------
        -- 2) Check if driver has ANY denied document
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM DRIVER_DOCUMENT
            WHERE User_ID = @UserID
              AND Status = 'denied'
        )
        BEGIN
            UPDATE DRIVER
            SET Status = 'denied'
            WHERE User_ID = @UserID;

            SELECT 'Driver denied due to rejected documents.' AS Result;
            RETURN;
        END


        ------------------------------------------------------------
        -- 3) Check if driver is missing ANY required document type
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM DRIVER_DOCUMENT_TYPE AS DT
            WHERE NOT EXISTS
            (
                SELECT 1
                FROM DRIVER_DOCUMENT AS DD
                WHERE DD.User_ID = @UserID
                  AND DD.Document_Type_Name = DT.Name
            )
        )
        BEGIN
            UPDATE DRIVER
            SET Status = 'denied'
            WHERE User_ID = @UserID;

            SELECT 'Driver denied due to missing required documents.' AS Result;
            RETURN;
        END


        ------------------------------------------------------------
        -- 4) OPTIONAL: Can also deny if expired approved docs exist
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM DRIVER_DOCUMENT
            WHERE User_ID = @UserID
              AND Status = 'approved'
              AND Expiry_Date < CONVERT(date, GETDATE())
        )
        BEGIN
            UPDATE DRIVER
            SET Status = 'denied'
            WHERE User_ID = @UserID;

            SELECT 'Driver denied due to expired documents.' AS Result;
            RETURN;
        END

        ------------------------------------------------------------
        -- 5) If none of the above reasons triggered:
        ------------------------------------------------------------
        ;THROW 95003, 'Driver cannot be denied because all documents are valid.', 1;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
