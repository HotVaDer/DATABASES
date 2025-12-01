CREATE OR ALTER PROCEDURE sp_ApproveVehicleDocument
(
    @Vehicle_Document_ID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        DECLARE @LicensePlate VARCHAR(10);
        DECLARE @Doc_Type_Name VARCHAR(50);
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Φέρνουμε το document
        ------------------------------------------------------------
        SELECT 
            @LicensePlate = License_Plate,
            @Doc_Type_Name = Doc_Type_Name,
            @CurrentStatus = Status
        FROM VEHICLE_DOCUMENT
        WHERE Vehicle_Document_ID = @Vehicle_Document_ID;

        IF @LicensePlate IS NULL
        BEGIN
            ;THROW 94001, 'Vehicle document does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Επιτρέπουμε approve ΜΟΝΟ αν το status είναι pending/pending_review
        ------------------------------------------------------------
        IF @CurrentStatus NOT IN ('pending', 'pending_review')
        BEGIN
            ;THROW 94002, 'Vehicle document cannot be approved in its current status.', 1;
        END

        ------------------------------------------------------------
        -- 3) Κάνουμε approved το document
        ------------------------------------------------------------
        UPDATE VEHICLE_DOCUMENT
        SET Status = 'approved'
        WHERE Vehicle_Document_ID = @Vehicle_Document_ID;

        ------------------------------------------------------------
        -- 4) Επιτυχία
        ------------------------------------------------------------
        SELECT 'Vehicle document approved successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
