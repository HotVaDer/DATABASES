CREATE OR ALTER PROCEDURE sp_SubmitDriverDocument
(
    @UserID             INT,
    @Document_Type_Name VARCHAR(50),
    @Issue_Date         DATE,
    @Expiry_Date        DATE,
    @File_Data          VARBINARY(255)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Βρίσκουμε το status του driver
        ------------------------------------------------------------
        SELECT @CurrentStatus = Status
        FROM DRIVER
        WHERE User_ID = @UserID;

        IF @CurrentStatus IS NULL
        BEGIN
            ;THROW 92010, 'User is not registered as a driver.', 1;
        END

        ------------------------------------------------------------
        -- 2) Αν είναι denied → resubmit μέσα εδώ (denied → pending)
        ------------------------------------------------------------
        IF @CurrentStatus = 'denied'
        BEGIN
            UPDATE DRIVER
            SET Status = 'pending'
            WHERE User_ID = @UserID;

            SET @CurrentStatus = 'pending';
        END

        ------------------------------------------------------------
        -- 3) Επιτρέπουμε submit ΜΟΝΟ αν είναι pending ή approved
        ------------------------------------------------------------
        IF @CurrentStatus NOT IN ('pending', 'approved')
        BEGIN
            ;THROW 92011, 'Driver status does not allow document submission.', 1;
        END

        ------------------------------------------------------------
        -- 4) Έλεγχος ότι το document type υπάρχει
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM DRIVER_DOCUMENT_TYPE
            WHERE Name = @Document_Type_Name
        )
        BEGIN
            ;THROW 92002, 'Invalid document type.', 1;
        END

        ------------------------------------------------------------
        -- 5) Εισαγωγή εγγράφου ως pending (για έλεγχο από admin)
        ------------------------------------------------------------
        INSERT INTO DRIVER_DOCUMENT
        (
            Status,
            Expiry_Date,
            Issue_Date,
            Uploaded_At,
            Document_Type_Name,
            File_Data,
            User_ID
        )
        VALUES
        (
            'pending',
            @Expiry_Date,
            @Issue_Date,
            GETDATE(),
            @Document_Type_Name,
            @File_Data,
            @UserID
        );

        SELECT 'Document submitted successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;
END;
GO
