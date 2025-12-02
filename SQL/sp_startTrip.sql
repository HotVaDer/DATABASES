CREATE OR ALTER PROCEDURE sp_StartTrip
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
            THROW 100001, 'Trip does not exist.', 1;

        ------------------------------------------------------------
        -- 2) Load trip info
        ------------------------------------------------------------
        DECLARE @Status VARCHAR(50);
        DECLARE @AssignedPlate VARCHAR(10);

        SELECT @Status = Status,
               @AssignedPlate = Assigned_License_Plate
        FROM TRIP
        WHERE Trip_ID = @Trip_ID;

        ------------------------------------------------------------
        -- 3) Validate trip status
        ------------------------------------------------------------
        IF @Status <> 'accepted'
            THROW 100002, 'Trip cannot be started unless it is accepted.', 1;

        ------------------------------------------------------------
        -- 4) Validate assigned driver
        ------------------------------------------------------------
        IF @AssignedPlate IS NULL OR @AssignedPlate <> @License_Plate
            THROW 100003, 'This driver is not assigned to the trip.', 1;

        ------------------------------------------------------------
        -- 5) Update status → in_progress
        ------------------------------------------------------------
        UPDATE TRIP
        SET Status = 'in_progress',
            Start_Time = GETDATE()
        WHERE Trip_ID = @Trip_ID;

        COMMIT TRANSACTION;

        SELECT 'Trip started successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK;
        THROW;
    END CATCH;
END;
GO
