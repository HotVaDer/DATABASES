CREATE OR ALTER PROCEDURE sp_ApproveDriverDocument
(
    @Driver_Document_ID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        DECLARE @UserID INT;
        DECLARE @Doc_Type_Name VARCHAR(50);
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Φέρνουμε το document
        ------------------------------------------------------------
        SELECT 
            @UserID = User_ID,
            @Doc_Type_Name = Doc_Type_Name,
            @CurrentStatus = Status
        FROM DRIVER_DOCUMENT
        WHERE Driver_Document_ID = @Driver_Document_ID;

        IF @UserID IS NULL
        BEGIN
            ;THROW 93001, 'Document does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Επιτρέπουμε approve ΜΟΝΟ αν το status είναι pending
        ------------------------------------------------------------
        IF @CurrentStatus NOT IN ('pending', 'pending_review')
        BEGIN
            ;THROW 93002, 'Document cannot be approved in its current status.', 1;
        END

                ------------------------------------------------------------
                -- 3) Κάνουμε approved το νέο document (κρατάμε τα παλιά ως έχουν)
                ------------------------------------------------------------
        UPDATE DRIVER_DOCUMENT
        SET Status = 'approved'
        WHERE Driver_Document_ID = @Driver_Document_ID;

        ------------------------------------------------------------
        -- 5) Επιτυχία
        ------------------------------------------------------------
        SELECT 'Document approved successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
