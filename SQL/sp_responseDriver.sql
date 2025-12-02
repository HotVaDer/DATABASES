CREATE OR ALTER PROCEDURE sp_DriverRespond
(
    @Match_ID INT,
    @Response_Status VARCHAR(30)  -- 'accepted' OR 'declined'
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Validate Match exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM TRIP_VEHICLE_MATCH WHERE Match_ID = @Match_ID)
            THROW 99001, 'Match does not exist.', 1;

        ------------------------------------------------------------
        -- 2) Load match info
        ------------------------------------------------------------
        DECLARE @Trip_ID INT;
        DECLARE @License_Plate VARCHAR(10);

        SELECT  @Trip_ID = Trip_ID,
                @License_Plate = License_Plate
        FROM TRIP_VEHICLE_MATCH
        WHERE Match_ID = @Match_ID;

        ------------------------------------------------------------
        -- 3) Validate status input
        ------------------------------------------------------------
        IF @Response_Status NOT IN ('accepted', 'declined')
            THROW 99002, 'Invalid response status.', 1;

        ------------------------------------------------------------
        -- 4) Update the selected match response
        ------------------------------------------------------------
        UPDATE TRIP_VEHICLE_MATCH
        SET Response_Status = @Response_Status,
            Response_Time = GETDATE()
        WHERE Match_ID = @Match_ID;

        ------------------------------------------------------------
        -- 5) If driver DECLINED → done
        ------------------------------------------------------------
        IF @Response_Status = 'declined'
        BEGIN
            COMMIT TRANSACTION;
            SELECT 'Driver declined this trip.' AS Result;
            RETURN;
        END

        ------------------------------------------------------------
        -- 6) If driver ACCEPTED:
        --     - Mark trip as accepted
        --     - Assign vehicle to the trip
        --     - Expire all other pending matches
        ------------------------------------------------------------

        -- 6a) Update Trip
        UPDATE TRIP
        SET Status = 'accepted'
        WHERE Trip_ID = @Trip_ID;

        -- 6b) Assign the chosen vehicle
        -- (Add column Assigned_License_Plate to TRIP if needed)
        IF COL_LENGTH('TRIP', 'Assigned_License_Plate') IS NOT NULL
        BEGIN
            UPDATE TRIP
            SET Assigned_License_Plate = @License_Plate
            WHERE Trip_ID = @Trip_ID;
        END

        -- 6c) Expire all other match attempts
        UPDATE TRIP_VEHICLE_MATCH
        SET Response_Status = 'expired'
        WHERE Trip_ID = @Trip_ID
          AND Match_ID <> @Match_ID
          AND Response_Status = 'pending';

        ------------------------------------------------------------
        -- Done
        ------------------------------------------------------------
        COMMIT TRANSACTION;
        SELECT 'Driver accepted. Trip assigned successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;
        THROW;
    END CATCH;
END;
GO
