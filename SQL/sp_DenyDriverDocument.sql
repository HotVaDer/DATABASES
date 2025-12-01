CREATE OR ALTER PROCEDURE sp_DenyDriverDocument
(
    @Driver_Document_ID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        DECLARE @UserID INT;
        DECLARE @Document_Type_Name VARCHAR(50);
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Φέρνουμε το document
        ------------------------------------------------------------
        SELECT 
            @UserID = User_ID,
            @Document_Type_Name = Document_Type_Name,
            @CurrentStatus = Status
        FROM DRIVER_DOCUMENT
        WHERE Driver_Document_ID = @Driver_Document_ID;

        IF @UserID IS NULL
        BEGIN
            ;THROW 93101, 'Document does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Επιτρέπουμε reject ΜΟΝΟ αν είναι pending / pending_review
        ------------------------------------------------------------
        IF @CurrentStatus NOT IN ('pending', 'pending_review')
        BEGIN
            ;THROW 93102, 'Document cannot be rejected in its current status.', 1;
        END

        ------------------------------------------------------------
        -- 3) Κάνουμε το document denied
        ------------------------------------------------------------
        UPDATE DRIVER_DOCUMENT
        SET Status = 'denied'
        WHERE Driver_Document_ID = @Driver_Document_ID;

        ------------------------------------------------------------
        -- 4) Επιτυχία
        ------------------------------------------------------------
        SELECT 'Document rejected successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
