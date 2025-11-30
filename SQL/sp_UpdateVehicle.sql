CREATE OR ALTER PROCEDURE sp_UpdateVehicle
(
   @License_Plate   VARCHAR(10),
   @Seat_Capacity   INT           = NULL,
   @Trunk_Space     FLOAT         = NULL,
   @Trunk_Weight    FLOAT         = NULL,
   @Vehicle_Type    VARCHAR(50)   = NULL,
   @Price_To_Ride   FLOAT         = NULL,
   @Region_ID       INT           = NULL,
   @actorID         INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ----------------------------------------------------------
        -- 1. Check actor exists
        ----------------------------------------------------------
        DECLARE @actorRole VARCHAR(50);

        SELECT @actorRole = [Role]
        FROM [USER]
        WHERE User_ID = @actorID;

        IF @actorRole IS NULL
            ;THROW 91001, 'Actor does not exist.', 1;

        IF @actorRole NOT IN ('driver','operator','admin')
            ;THROW 91002, 'Actor not authorized.', 1;


        ----------------------------------------------------------
        -- 2. Ensure vehicle exists + get owner
        ----------------------------------------------------------
        DECLARE @OwnerID INT;

        SELECT @OwnerID = User_ID
        FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        IF @OwnerID IS NULL
            ;THROW 91003, 'Vehicle does not exist.', 1;


        ----------------------------------------------------------
        -- 3. Driver must own the vehicle
        ----------------------------------------------------------
        IF (@actorRole = 'driver' AND @actorID <> @OwnerID)
            ;THROW 91004, 'Driver cannot update other drivers'' vehicles.', 1;


        ----------------------------------------------------------
        -- 4. Update vehicle
        ----------------------------------------------------------
        UPDATE VEHICLE
        SET 
            Seat_Capacity = COALESCE(@Seat_Capacity, Seat_Capacity),
            Trunk_Space   = COALESCE(@Trunk_Space, Trunk_Space),
            Trunk_Weight  = COALESCE(@Trunk_Weight, Trunk_Weight),
            Vehicle_Type  = COALESCE(@Vehicle_Type, Vehicle_Type),
            Price_To_Ride = COALESCE(@Price_To_Ride, Price_To_Ride),
            Region_ID     = COALESCE(@Region_ID, Region_ID)
        WHERE License_Plate = @License_Plate;


        ----------------------------------------------------------
        -- 5. If driver updates → reset status to pending
        ----------------------------------------------------------
        IF @actorRole = 'driver'
        BEGIN
            UPDATE VEHICLE
            SET Status = 'pending'
            WHERE License_Plate = @License_Plate;
        END


        ----------------------------------------------------------
        COMMIT TRANSACTION;
        SELECT 'Vehicle updated successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 
            ROLLBACK TRANSACTION;

        DECLARE @ErrorMessage NVARCHAR(4000) = ERROR_MESSAGE();
        THROW 50001, @ErrorMessage, 1;
    END CATCH
END;
GO
