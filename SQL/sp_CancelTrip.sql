CREATE OR ALTER PROCEDURE sp_CancelTrip
(
    @Trip_ID INT,
    @Cancelled_By VARCHAR(20)   -- 'driver' or 'user'
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
            THROW 100201, 'Trip does not exist.', 1;

        ------------------------------------------------------------
        -- 2) Load status
        ------------------------------------------------------------
        DECLARE @Status VARCHAR(50);
        SELECT @Status = Status 
        FROM TRIP WHERE Trip_ID = @Trip_ID;

        ------------------------------------------------------------
        -- 3) Status validation
        ------------------------------------------------------------
        IF @Status IN ('completed')
            THROW 100202, 'Cannot cancel a completed trip.', 1;

        IF @Status = 'cancelled'
            THROW 100203, 'Trip already cancelled.', 1;

        ------------------------------------------------------------
        -- 4) Cancel trip
        ------------------------------------------------------------
        UPDATE TRIP
        SET Status = 'cancelled',
            Cancelled_At = GETDATE(),
            Cancelled_By = @Cancelled_By
        WHERE Trip_ID = @Trip_ID;

        ------------------------------------------------------------
        -- 5) Set all pending matches to expired
        ------------------------------------------------------------
        UPDATE TRIP_VEHICLE_MATCH
        SET Response_Status = 'expired'
        WHERE Trip_ID = @Trip_ID
          AND Response_Status = 'pending';

        COMMIT TRANSACTION;

        SELECT 'Trip cancelled successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK;
        THROW;
    END CATCH;
END;
GO
