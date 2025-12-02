CREATE OR ALTER PROCEDURE sp_FindAvailableVehicles
(
    @Trip_ID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    ------------------------------------------------------------
    -- 1) Get trip info
    ------------------------------------------------------------
    DECLARE @User_Location GEOGRAPHY;
    DECLARE @Service_Type_ID INT;

    SELECT 
        @User_Location = Start_Point,
        @Service_Type_ID = Service_Type_ID
    FROM TRIP
    WHERE Trip_ID = @Trip_ID;

    IF @User_Location IS NULL
        THROW 97001, 'Invalid Trip ID.', 1;

    ------------------------------------------------------------
    -- 2) Get service requirements
    ------------------------------------------------------------
    DECLARE 
        @MinSeats INT, @MinTrunkSpace FLOAT, @MinTrunkWeight FLOAT,
        @MustBe4Door BINARY(1), @MustHaveRearSeats BINARY(1),
        @RequiredType VARCHAR(50);

    SELECT
        @MinSeats = Min_Seats,
        @MinTrunkSpace = Min_Trunk_Space,
        @MinTrunkWeight = Min_Trunk_Weight,
        @MustBe4Door = Must_Be_4_Door,
        @MustHaveRearSeats = Must_Have_Rear_Seats,
        @RequiredType = Required_Vehicle_Type
    FROM VEHICLE_REQUIREMENTS
    WHERE Service_Type_ID = @Service_Type_ID;

    ------------------------------------------------------------
    -- 3) Return available vehicles sorted by distance
    ------------------------------------------------------------
    SELECT 
        V.License_Plate,
        D.User_ID AS Driver_ID,
        V.Vehicle_Type,
        V.Seat_Capacity,
        V.Trunk_Space,
        V.Price_To_Ride,
        V.Status AS Vehicle_Status,
        D.Status AS Driver_Status,
        @User_Location.STDistance(V.Current_Location) AS DistanceMeters
    FROM VEHICLE V
    JOIN DRIVER D ON D.User_ID = V.User_ID
    WHERE
        V.Status = 'available'
        AND D.Status = 'approved'
        AND V.Seat_Capacity >= @MinSeats
        AND V.Trunk_Space >= @MinTrunkSpace
        AND V.Trunk_Weight >= @MinTrunkWeight
        AND V.Vehicle_Type = @RequiredType
    ORDER BY @User_Location.STDistance(V.Current_Location);
END;
GO
