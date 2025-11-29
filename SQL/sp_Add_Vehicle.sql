CREATE OR ALTER PROCEDURE sp_Add_Vehicle
(
   @License_Plate VARCHAR(10),
   @Seat_Capacity INT,
   @Trunk_Space FLOAT,
   @Trunk_Weight FLOAT,
   @Vehicle_Type VARCHAR(50),
   @Price_To_Ride FLOAT,
   @Driver_ID INT,
   @Region_ID INT,
   @actorID INT
)
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRY
        BEGIN TRANSACTION;

        ----------------------------------------------------------
        -- 1. Check actor exists and has valid role
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM SYSTEM_USER
            WHERE User_ID = @actorID
              AND Role IN ('Driver', 'Operator', 'Admin')
        )
        BEGIN
            RAISERROR('Unauthorized: actor does not have permissions.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END

        ----------------------------------------------------------
        -- 2. Ensure the Driver exists
        ----------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM DRIVER WHERE Driver_ID = @Driver_ID)
        BEGIN
            RAISERROR('Driver does not exist.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END

        ----------------------------------------------------------
        -- 3. If actor is a Driver, must match Driver_ID
        ----------------------------------------------------------
        IF EXISTS (SELECT 1 FROM SYSTEM_USER WHERE User_ID = @actorID AND Role = 'Driver')
           AND @actorID <> @Driver_ID
        BEGIN
            RAISERROR('Unauthorized: Drivers can only add their own vehicles.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END

        ----------------------------------------------------------
        -- 4. Unique plate check
        ----------------------------------------------------------
        IF EXISTS (SELECT 1 FROM VEHICLE WHERE License_Plate = @License_Plate)
        BEGIN
            RAISERROR('Plate number already exists.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END

        ----------------------------------------------------------
        -- 5. Insert with status forced to PENDING
        ----------------------------------------------------------
        INSERT INTO VEHICLE (
            License_Plate,
            Seat_Capacity,
            Trunk_Space,
            Trunk_Weight,
            Vehicle_Type,
            Price_To_Ride,
            Status,
            Driver_ID,
            Region_ID
        )
        VALUES (
            @License_Plate,
            @Seat_Capacity,
            @Trunk_Space,
            @Trunk_Weight,
            @Vehicle_Type,
            @Price_To_Ride,
            'PENDING',      
            @Driver_ID,
            @Region_ID
        );

        DECLARE @NewVehicleID INT = SCOPE_IDENTITY();

        COMMIT TRANSACTION;

        SELECT @NewVehicleID AS Vehicle_ID;
    END TRY

    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;
        DECLARE @ErrMsg NVARCHAR(4000) = ERROR_MESSAGE();
        RAISERROR(@ErrMsg, 16, 1);
    END CATCH
END;
