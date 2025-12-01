CREATE OR ALTER PROCEDURE sp_EnrollServiceCatalog
(
    @UserID         INT,
    @License_Plate  VARCHAR(10),
    @Service_Type_ID INT,
    @Available_From DATETIME,
    @Available_To   DATETIME
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
        -- 3) Το service type πρέπει να υπάρχει
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM SERVICE_TYPE
            WHERE Service_Type_ID = @Service_Type_ID
        )
        BEGIN
            ;THROW 97005, 'Service type does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 4) Παίρνουμε τα vehicle requirements για το service
        --    (υποθέτουμε 1 row ανά Service_Type_ID)
        ------------------------------------------------------------
        DECLARE
            @Min_Seats           INT,
            @Max_Vehicle_Age     INT,
            @Min_Trunk_Space     FLOAT,
            @Min_Trunk_Weight    FLOAT,
            @Must_Be_4_Door      BINARY(1),
            @Must_Have_Rear_Seats BINARY(1),
            @Required_Vehicle_Type VARCHAR(50);

        SELECT 
            @Min_Seats            = Min_Seats,
            @Max_Vehicle_Age      = Max_Vehicle_Age,
            @Min_Trunk_Space      = Min_Trunk_Space,
            @Min_Trunk_Weight     = Min_Trunk_Weight,
            @Must_Be_4_Door       = Must_Be_4_Door,
            @Must_Have_Rear_Seats = Must_Have_Rear_Seats,
            @Required_Vehicle_Type = Required_Vehicle_Type
        FROM VEHICLE_REQUIREMENTS
        WHERE Service_Type_ID = @Service_Type_ID;

        IF @Min_Seats IS NULL
        BEGIN
            ;THROW 97006, 'No vehicle requirements defined for this service type.', 1;
        END

        ------------------------------------------------------------
        -- 5) Έλεγχος που ΜΠΟΡΟΥΜΕ να κάνουμε με βάση το schema:
        --    seat capacity, trunk space, trunk weight, vehicle type
        --    (Max_Vehicle_Age, doors, rear seats χρειάζονται extra πεδία στο VEHICLE)
        ------------------------------------------------------------
        IF @Seat_Capacity < @Min_Seats
        BEGIN
            ;THROW 97007, 'Vehicle does not meet minimum seat capacity requirement.', 1;
        END

        IF @Trunk_Space < @Min_Trunk_Space
        BEGIN
            ;THROW 97008, 'Vehicle does not meet minimum trunk space requirement.', 1;
        END

        IF @Trunk_Weight < @Min_Trunk_Weight
        BEGIN
            ;THROW 97009, 'Vehicle does not meet minimum trunk weight requirement.', 1;
        END

        IF @Required_Vehicle_Type IS NOT NULL 
           AND @Required_Vehicle_Type <> ''
           AND @Vehicle_Type <> @Required_Vehicle_Type
        BEGIN
            ;THROW 97010, 'Vehicle type does not match required vehicle type for this service.', 1;
        END

        ------------------------------------------------------------
        -- 6) (Προαιρετικό) Έλεγχος για διπλή εγγραφή στο ίδιο slot
        --    Π.χ. να μην έχει ήδη καταχώρηση ο driver με το ίδιο όχημα
        --    και ίδιο service στο ίδιο χρονικό διάστημα
        ------------------------------------------------------------
        IF EXISTS (
            SELECT 1
            FROM SERVICE_CATALOG AS SC
            WHERE SC.User_ID        = @UserID
              AND SC.License_Plate  = @License_Plate
              AND SC.Service_Type_ID = @Service_Type_ID
              AND SC.Available_From = @Available_From
              AND SC.Available_To   = @Available_To
        )
        BEGIN
            ;THROW 97011, 'This vehicle is already enrolled for this service in the given time slot.', 1;
        END

        ------------------------------------------------------------
        -- 7) Εγγραφή στο SERVICE_CATALOG
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
