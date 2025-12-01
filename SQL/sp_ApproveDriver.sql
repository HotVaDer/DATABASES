CREATE OR ALTER PROCEDURE sp_ApproveDriver
(
    @UserID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Βεβαιωνόμαστε ότι ο χρήστης είναι driver
        ------------------------------------------------------------
        SELECT @CurrentStatus = Status
        FROM DRIVER
        WHERE User_ID = @UserID;

        IF @CurrentStatus IS NULL
        BEGIN
            ;THROW 94001, 'User is not registered as a driver.', 1;
        END

        ------------------------------------------------------------
        -- 2) Μόνο drivers με status = 'pending' μπορούν να εγκριθούν
        ------------------------------------------------------------
        IF @CurrentStatus <> 'pending'
        BEGIN
            ;THROW 94002, 'Driver status must be pending to approve.', 1;
        END

        ------------------------------------------------------------
        -- 3) Έλεγχος ΟΛΩΝ των απαιτούμενων εγγράφων
        --    Κάθε document type πρέπει να έχει:
        --    - 1 document
        --    - Status = approved
        --    - Expiry >= today
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM DRIVER_DOC_TYPE AS DT
            WHERE NOT EXISTS
            (
                SELECT 1
                FROM DRIVER_DOCUMENT AS DD
                WHERE DD.User_ID = @UserID
                  AND DD.Doc_Type_Name = DT.Name
                  AND DD.Status = 'approved'
                  AND DD.Expiry_Date >= CONVERT(date, GETDATE())
            )
        )
        BEGIN
            ;THROW 94003, 'Driver does not have all required valid approved documents.', 1;
        END

        ------------------------------------------------------------
        -- 4) Εγκρίνουμε τον driver
        ------------------------------------------------------------
        UPDATE DRIVER
        SET Status = 'approved'
        WHERE User_ID = @UserID;

        ------------------------------------------------------------
        -- 5) Επιτυχία
        ------------------------------------------------------------
        SELECT 'Driver approved successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
