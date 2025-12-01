CREATE OR ALTER PROCEDURE sp_AddVehicleRequirement
(
    @Service_Type_Name     VARCHAR(50),
    @Min_Seats             INT,
    @Max_Vehicle_Age       INT,
    @Min_Trunk_Space       FLOAT,
    @Min_Trunk_Weight      FLOAT,
    @Must_Be_4_Door        BIT,
    @Must_Have_Rear_Seats  BIT,
    @Required_Vehicle_Type VARCHAR(50) = NULL   -- μπορεί να είναι optional
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        DECLARE @Service_Type_ID INT;

        ------------------------------------------------------------
        -- 1) Βρίσκουμε Service_Type_ID από το όνομα
        ------------------------------------------------------------
        SELECT @Service_Type_ID = Service_Type_ID
        FROM SERVICE_TYPE
        WHERE Service_Type_Name = @Service_Type_Name;

        IF @Service_Type_ID IS NULL
        BEGIN
            ;THROW 98101, 'Service type does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Βασικά validations στα νούμερα
        ------------------------------------------------------------
        IF @Min_Seats <= 0
        BEGIN
            ;THROW 98102, 'Min_Seats must be greater than 0.', 1;
        END

        IF @Max_Vehicle_Age < 0
        BEGIN
            ;THROW 98103, 'Max_Vehicle_Age cannot be negative.', 1;
        END

        IF @Min_Trunk_Space < 0
        BEGIN
            ;THROW 98104, 'Min_Trunk_Space cannot be negative.', 1;
        END

        IF @Min_Trunk_Weight < 0
        BEGIN
            ;THROW 98105, 'Min_Trunk_Weight cannot be negative.', 1;
        END

        ------------------------------------------------------------
        -- 3) Αν δεν δοθεί Required_Vehicle_Type, βάλτο '' (όχι NULL)
        --    για να μην σπάει το NOT NULL του πεδίου.
        ------------------------------------------------------------
        IF @Required_Vehicle_Type IS NULL
        BEGIN
            SET @Required_Vehicle_Type = '';
        END

        ------------------------------------------------------------
        -- 4) Εισαγωγή στο VEHICLE_REQUIREMENTS
        ------------------------------------------------------------
        INSERT INTO VEHICLE_REQUIREMENTS
        (
            Min_Seats,
            Max_Vehicle_Age,
            Min_Trunk_Space,
            Min_Trunk_Weight,
            Must_Be_4_Door,
            Must_Have_Rear_Seats,
            Required_Vehicle_Type,
            Service_Type_ID
        )
        VALUES
        (
            @Min_Seats,
            @Max_Vehicle_Age,
            @Min_Trunk_Space,
            @Min_Trunk_Weight,
            CONVERT(BINARY(1), @Must_Be_4_Door),
            CONVERT(BINARY(1), @Must_Have_Rear_Seats),
            @Required_Vehicle_Type,
            @Service_Type_ID
        );

        DECLARE @Requirement_ID INT = SCOPE_IDENTITY();

        ------------------------------------------------------------
        -- 5) Επιστρέφουμε info
        ------------------------------------------------------------
        SELECT 
            @Requirement_ID      AS Requirement_ID,
            @Service_Type_ID     AS Service_Type_ID,
            @Service_Type_Name   AS Service_Type_Name,
            'Vehicle requirement added successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;
END;
GO
