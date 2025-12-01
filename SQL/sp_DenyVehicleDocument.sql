CREATE OR ALTER PROCEDURE sp_DenyVehicleDocument
(
    @Vehicle_Document_ID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        DECLARE @LicensePlate VARCHAR(10);
        DECLARE @Document_Type_Name VARCHAR(50);
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Φέρνουμε το vehicle document
        ------------------------------------------------------------
        SELECT 
            @LicensePlate = License_Plate,
            @Document_Type_Name = Doc_Type_Name,
            @CurrentStatus = Status
        FROM VEHICLE_DOCUMENT
        WHERE Vehicle_Document_ID = @Vehicle_Document_ID;

        IF @LicensePlate IS NULL
        BEGIN
            ;THROW 94101, 'Vehicle document does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Επιτρέπουμε reject ΜΟΝΟ αν status = pending/pending_review
        ------------------------------------------------------------
        IF @CurrentStatus NOT IN ('pending', 'pending_review')
        BEGIN
            ;THROW 94102, 'Vehicle document cannot be rejected in its current status.', 1;
        END

        ------------------------------------------------------------
        -- 3) Κάνουμε το document denied
        ------------------------------------------------------------
        UPDATE VEHICLE_DOCUMENT
        SET Status = 'denied'
        WHERE Vehicle_Document_ID = @Vehicle_Document_ID;

        ------------------------------------------------------------
        -- 4) Επιτυχία
        ------------------------------------------------------------
        SELECT 'Vehicle document rejected successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
