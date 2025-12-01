CREATE OR ALTER PROCEDURE sp_DriverDenyTripRequest
(
    @UserID        INT,
    @Trip_ID       INT,
    @License_Plate VARCHAR(10)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        DECLARE 
            @DriverStatus        VARCHAR(50),
            @VehicleUserID       INT,
            @VehicleStatus       VARCHAR(50),
            @TripStatus          VARCHAR(50),
            @TripServiceTypeID   INT,
            @TripRequestTime     DATETIME;

        ------------------------------------------------------------
        -- 1) Ο χρήστης πρέπει να είναι APPROVED driver
        ------------------------------------------------------------
        SELECT @DriverStatus = Status
        FROM DRIVER
        WHERE User_ID = @UserID;

        IF @DriverStatus IS NULL
        BEGIN
            ;THROW 99101, 'User is not registered as a driver.', 1;
        END

        IF @DriverStatus <> 'approved'
        BEGIN
            ;THROW 99102, 'Driver is not approved.', 1;
        END

        ------------------------------------------------------------
        -- 2) Το όχημα πρέπει να υπάρχει, να ανήκει στον driver
        --    και να είναι approved
        ------------------------------------------------------------
        SELECT 
            @VehicleUserID = User_ID,
            @VehicleStatus = Status
        FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        IF @VehicleUserID IS NULL
        BEGIN
            ;THROW 99103, 'Vehicle does not exist.', 1;
        END

        IF @VehicleUserID <> @UserID
        BEGIN
            ;THROW 99104, 'Vehicle does not belong to this driver.', 1;
        END

        IF @VehicleStatus <> 'approved'
        BEGIN
            ;THROW 99105, 'Vehicle is not approved.', 1;
        END

        ------------------------------------------------------------
        -- 3) Έλεγχος Trip: υπάρχει & είναι requested
        ------------------------------------------------------------
        SELECT 
            @TripStatus        = [Status],
            @TripServiceTypeID = Service_Type_ID,
            @TripRequestTime   = Request_Time
        FROM TRIP
        WHERE Trip_ID = @Trip_ID;

        IF @TripStatus IS NULL
        BEGIN
            ;THROW 99106, 'Trip does not exist.', 1;
        END

        IF @TripStatus <> 'requested'
        BEGIN
            ;THROW 99107, 'Trip is not in requested status.', 1;
        END

        ------------------------------------------------------------
        -- 4) Έλεγχος ότι ο driver/vehicle είναι enrolled
        --    στην απαιτούμενη υπηρεσία και διαθέσιμος εκείνη την ώρα
        ------------------------------------------------------------
        IF NOT EXISTS
        (
            SELECT 1
            FROM SERVICE_CATALOG AS SC
            WHERE SC.User_ID         = @UserID
              AND SC.License_Plate   = @License_Plate
              AND SC.Service_Type_ID = @TripServiceTypeID
              AND @TripRequestTime BETWEEN SC.Available_From AND SC.Available_To
        )
        BEGIN
            ;THROW 99108, 'Driver/vehicle is not enrolled or available for the required service type at that time.', 1;
        END

        ------------------------------------------------------------
        -- 5) Προαιρετικά: Μη διπλή απάντηση από τον ίδιο driver/vehicle
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM TRIP_VEHICLE_MATCH
            WHERE Trip_ID = @Trip_ID
              AND License_Plate = @License_Plate
        )
        BEGIN
            ;THROW 99109, 'This driver/vehicle has already responded to this trip.', 1;
        END

        ------------------------------------------------------------
        -- 6) Εισαγωγή match ως denied
        ------------------------------------------------------------
        INSERT INTO TRIP_VEHICLE_MATCH
        (
            Offer_Time,
            Response_Time,
            Response_Status,
            Trip_ID,
            License_Plate
        )
        VALUES
        (
            GETDATE(),             -- Offer_Time
            GETDATE(),             -- Response_Time
            'denied',
            @Trip_ID,
            @License_Plate
        );

        DECLARE @Match_ID INT = SCOPE_IDENTITY();

        COMMIT TRANSACTION;

        SELECT 
            @Match_ID AS Match_ID,
            'Trip denied successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;

        THROW;
    END CATCH;
END;
GO
