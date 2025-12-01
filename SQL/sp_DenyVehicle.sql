CREATE OR ALTER PROCEDURE sp_DenyVehicle
(
    @License_Plate VARCHAR(10)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @IsVehicle INT;

        ------------------------------------------------------------
        -- 1) Check that the vehicle EXISTS
        ------------------------------------------------------------
        SELECT @IsVehicle = COUNT(*)
        FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        IF @IsVehicle = 0
        BEGIN
            ;THROW 96001, 'Vehicle does not exist.', 1;
        END


        ------------------------------------------------------------
        -- 2) Check if vehicle has ANY denied document
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM VEHICLE_DOCUMENT
            WHERE License_Plate = @License_Plate
              AND Status = 'denied'
        )
        BEGIN
            UPDATE VEHICLE
            SET Status = 'denied'
            WHERE License_Plate = @License_Plate;

            SELECT 'Vehicle denied due to rejected documents.' AS Result;
            RETURN;
        END


        ------------------------------------------------------------
        -- 3) Check if vehicle is missing ANY required vehicle doc type
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM VEHICLE_DOC_TYPE AS VT
            WHERE NOT EXISTS
            (
                SELECT 1
                FROM VEHICLE_DOCUMENT AS VD
                WHERE VD.License_Plate = @License_Plate
                  AND VD.Doc_Type_Name = VT.Doc_Type_Name
            )
        )
        BEGIN
            UPDATE VEHICLE
            SET Status = 'denied'
            WHERE License_Plate = @License_Plate;

            SELECT 'Vehicle denied due to missing required documents.' AS Result;
            RETURN;
        END


        ------------------------------------------------------------
        -- 4) OPTIONAL: deny if expired approved vehicle docs exist
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM VEHICLE_DOCUMENT
            WHERE License_Plate = @License_Plate
              AND Status = 'approved'
              AND Expiry_Date < CONVERT(date, GETDATE())
        )
        BEGIN
            UPDATE VEHICLE
            SET Status = 'denied'
            WHERE License_Plate = @License_Plate;

            SELECT 'Vehicle denied due to expired documents.' AS Result;
            RETURN;
        END

        ------------------------------------------------------------
        -- 5) If none of the above reasons triggered:
        ------------------------------------------------------------
        ;THROW 96003, 'Vehicle cannot be denied because all documents are valid.', 1;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
