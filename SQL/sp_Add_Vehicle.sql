CREATE OR ALTER PROCEDURE sp_AddVehicle
(
   @License_Plate VARCHAR(10),
   @Seat_Capacity INT,
   @Trunk_Space FLOAT,
   @Trunk_Weight FLOAT,
   @Vehicle_Type VARCHAR(50),
   @Price_To_Ride FLOAT,
   @DriverUserID INT,
   @Region_ID INT,
   @actorID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ----------------------------------------------------------
        -- 1. Check actor exists and has permissions
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM [USER]
            WHERE User_ID = @actorID
              AND Type_Name IN ('driver', 'operator', 'admin')
        )
        BEGIN
            ;THROW 91001, 'Unauthorized: actor has no permissions.', 1;
        END

        ----------------------------------------------------------
        -- 2. Check that the driver is approved
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM DRIVER
            WHERE User_ID = @DriverUserID
              AND Status = 'approved'
        )
        BEGIN
            ;THROW 91002, 'Driver is not approved.', 1;
        END

        ----------------------------------------------------------
        -- 3. Unique plate check
        ----------------------------------------------------------
        IF EXISTS (SELECT 1 FROM VEHICLE WHERE License_Plate = @License_Plate)
        BEGIN
            ;THROW 91003, 'License plate already exists.', 1;
        END

        ----------------------------------------------------------
        -- 4. Insert vehicle with pending status
        ----------------------------------------------------------
        INSERT INTO VEHICLE
        (
            License_Plate,
            Seat_Capacity,
            Trunk_Space,
            Trunk_Weight,
            Vehicle_Type,
            Price_To_Ride,
            Status,
            User_ID,
            Region_ID
        )
        VALUES
        (
            @License_Plate,
            @Seat_Capacity,
            @Trunk_Space,
            @Trunk_Weight,
            @Vehicle_Type,
            @Price_To_Ride,
            'pending',
            @DriverUserID,
            @Region_ID
        );

        COMMIT TRANSACTION;

        -- Since License Plate is PK, return it directly
        SELECT @License_Plate AS License_Plate;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0 ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;
GO
