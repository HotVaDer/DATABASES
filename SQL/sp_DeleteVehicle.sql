CREATE OR ALTER PROCEDURE sp_DeleteOrDeactivateVehicle
(
    @License_Plate VARCHAR(10),
    @actorID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ----------------------------------------------------------
        -- 1. Actor validation
        ----------------------------------------------------------
        DECLARE @actorRole VARCHAR(50);

        SELECT @actorRole = Type_Name 
        FROM [USER]
        WHERE User_ID = @actorID;

        IF @actorRole IS NULL
            ;THROW 94001, 'Actor does not exist.', 1;

        IF @actorRole NOT IN ('driver','operator','admin')
            ;THROW 94002, 'Actor not authorized.', 1;


        ----------------------------------------------------------
        -- 2. Vehicle exists + get owner
        ----------------------------------------------------------
        DECLARE @OwnerID INT;

        SELECT @OwnerID = User_ID
        FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        IF @OwnerID IS NULL
            ;THROW 94003, 'Vehicle does not exist.', 1;


        ----------------------------------------------------------
        -- 3. Driver can delete ONLY own vehicle
        ----------------------------------------------------------
        IF (@actorRole = 'driver' AND @actorID <> @OwnerID)
            ;THROW 94004, 'Driver cannot modify other user vehicles.', 1;


        ----------------------------------------------------------
        -- 4. Check if the vehicle participates in history tables
        ----------------------------------------------------------
        DECLARE @HasHistory BIT = 0;

        IF EXISTS (SELECT 1 FROM TRIP_VEHICLE_MATCH WHERE License_Plate = @License_Plate)
            SET @HasHistory = 1;

        IF EXISTS (SELECT 1 FROM TRIP_SEGMENT WHERE License_Plate = @License_Plate)
            SET @HasHistory = 1;

        IF EXISTS (SELECT 1 FROM SERVICE_CATALOG WHERE License_Plate = @License_Plate)
            SET @HasHistory = 1;


        ----------------------------------------------------------
        -- 5A. If vehicle has HISTORY → only deactivate
        ----------------------------------------------------------
        IF @HasHistory = 1
        BEGIN
            UPDATE VEHICLE
            SET Status = 'inactive'
            WHERE License_Plate = @License_Plate;

            COMMIT TRANSACTION;

            SELECT 'Vehicle has history → status changed to INACTIVE (not deleted).' AS Result;
            RETURN;
        END


        ----------------------------------------------------------
        -- 5B. If NO history → allow physical DELETE
        ----------------------------------------------------------
        DELETE FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        COMMIT TRANSACTION;

        SELECT 'Vehicle deleted successfully (no history found).' AS Result;

    END TRY

    BEGIN CATCH
        IF XACT_STATE() <> 0 
            ROLLBACK TRANSACTION;

        DECLARE @ErrMsg NVARCHAR(4000) = ERROR_MESSAGE();
        THROW 50003, @ErrMsg, 1;
    END CATCH
END;
GO
