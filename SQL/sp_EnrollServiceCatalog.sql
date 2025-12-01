CREATE OR ALTER PROCEDURE sp_EnrollServiceCatalog
(
    @UserID             INT,
    @License_Plate      VARCHAR(10),
    @Service_Type_Name  VARCHAR(50),
    @Available_From     DATETIME,
    @Available_To       DATETIME
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        ------------------------------------------------------------
        -- 0) Βασικός έλεγχος ημερομηνιών
        ------------------------------------------------------------
        IF @Available_From >= @Available_To
        BEGIN
            ;THROW 97000, 'Available_From must be earlier than Available_To.', 1;
        END

        ------------------------------------------------------------
        -- 1) Ο χρήστης πρέπει να είναι APPROVED driver
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM DRIVER
            WHERE User_ID = @UserID
              AND Status = 'approved'
        )
        BEGIN
            ;THROW 97001, 'User is not an approved driver.', 1;
        END

        ------------------------------------------------------------
        -- 2) Το όχημα πρέπει να υπάρχει, να ανήκει στον driver
        --    και να είναι approved
        ------------------------------------------------------------
        DECLARE 
            @VehicleUserID  INT,
            @VehicleStatus  VARCHAR(50),
            @Seat_Capacity  INT,
            @Trunk_Space    FLOAT,
            @Trunk_Weight   FLOAT,
            @Vehicle_Type   VARCHAR(50);

        SELECT 
            @VehicleUserID = User_ID,
            @VehicleStatus = Status,
            @Seat_Capacity = Seat_Capacity,
            @Trunk_Space   = Trunk_Space,
            @Trunk_Weight  = Trunk_Weight,
            @Vehicle_Type  = Vehicle_Type
        FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        IF @VehicleUserID IS NULL
        BEGIN
            ;THROW 97002, 'Vehicle does not exist.', 1;
        END

        IF @VehicleUserID <> @UserID
        BEGIN
            ;THROW 97003, 'Vehicle does not belong to this driver.', 1;
        END

        IF @VehicleStatus <> 'approved'
        BEGIN
            ;THROW 97004, 'Vehicle is not approved.', 1;
        END

        ------------------------------------------------------------
        -- 3) Βρίσκουμε το Service_Type_ID από το Service_Type_Name
        ------------------------------------------------------------
        DECLARE @Service_Type_ID INT;

        SELECT @Service_Type_ID = Service_Type_ID
        FROM SERVICE_TYPE
        WHERE Service_Type_Name = @Service_Type_Name;

        IF @Service_Type_ID IS NULL
        BEGIN
            ;THROW 97005, 'Service type does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 4) Ελέγχουμε αν υπάρχει ΕΣΤΩ ΕΝΑ requirement set
        --    που να καλύπτεται από το όχημα
        --
        --    ΣΗΜΕΙΩΣΗ: Στο schema του VEHICLE δεν έχουμε πεδία
        --    για πόρτες, πίσω καθίσματα, age, οπότε ελέγχουμε
        --    αυτά που μπορούμε: Min_Seats, Min_Trunk_Space,
        --    Min_Trunk_Weight, Required_Vehicle_Type.
        ------------------------------------------------------------
        IF NOT EXISTS
        (
            SELECT 1
            FROM VEHICLE_REQUIREMENTS AS R
            WHERE R.Service_Type_ID = @Service_Type_ID
              AND (@Seat_Capacity >= R.Min_Seats)
              AND (@Trunk_Space   >= R.Min_Trunk_Space)
              AND (@Trunk_Weight  >= R.Min_Trunk_Weight)
              AND (
                    R.Required_Vehicle_Type IS NULL
                 OR R.Required_Vehicle_Type = ''
                 OR R.Required_Vehicle_Type = @Vehicle_Type
              )
        )
        BEGIN
            ;THROW 97006, 'Vehicle does not satisfy any requirement set for this service type.', 1;
        END

        ------------------------------------------------------------
        -- 5) (Προαιρετικό) Έλεγχος για διπλή εγγραφή στο ίδιο slot
        ------------------------------------------------------------
        IF EXISTS (
            SELECT 1
            FROM SERVICE_CATALOG AS SC
            WHERE SC.User_ID         = @UserID
              AND SC.License_Plate   = @License_Plate
              AND SC.Service_Type_ID = @Service_Type_ID
              AND SC.Available_From  = @Available_From
              AND SC.Available_To    = @Available_To
        )
        BEGIN
            ;THROW 97007, 'This vehicle is already enrolled for this service in the given time slot.', 1;
        END

        ------------------------------------------------------------
        -- 6) Εγγραφή στο SERVICE_CATALOG
        ------------------------------------------------------------
        INSERT INTO SERVICE_CATALOG
        (
            Available_From,
            Available_To,
            License_Plate,
            User_ID,
            Service_Type_ID
        )
        VALUES
        (
            @Available_From,
            @Available_To,
            @License_Plate,
            @UserID,
            @Service_Type_ID
        );

        SELECT SCOPE_IDENTITY() AS Catalog_ID, 'Service enrollment successful.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;
END;
GO
