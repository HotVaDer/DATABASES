CREATE OR ALTER PROCEDURE sp_CompleteTrip
(
    @Trip_ID INT,
    @License_Plate VARCHAR(10)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Validate trip exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM TRIP WHERE Trip_ID = @Trip_ID)
            THROW 100101, 'Trip does not exist.', 1;

        ------------------------------------------------------------
        -- 2) Load current data
        ------------------------------------------------------------
        DECLARE @Status VARCHAR(50);
        DECLARE @AssignedPlate VARCHAR(10);

        SELECT @Status = Status,
               @AssignedPlate = Assigned_License_Plate
        FROM TRIP
        WHERE Trip_ID = @Trip_ID;

        ------------------------------------------------------------
        -- 3) Validate status
        ------------------------------------------------------------
        IF @Status <> 'in_progress'
            THROW 100102, 'Trip cannot be completed unless in_progress.', 1;

        ------------------------------------------------------------
        -- 4) Validate correct driver
        ------------------------------------------------------------
        IF @AssignedPlate <> @License_Plate
            THROW 100103, 'This driver is not assigned to the trip.', 1;

        ------------------------------------------------------------
        -- 5) Finish trip
        ------------------------------------------------------------
        UPDATE TRIP
        SET Status = 'completed',
            End_Time = GETDATE()
        WHERE Trip_ID = @Trip_ID;

        COMMIT TRANSACTION;

        SELECT 'Trip completed successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK;
        THROW;
    END CATCH;
END;
GO
