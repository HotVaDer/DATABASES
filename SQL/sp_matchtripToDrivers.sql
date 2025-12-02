CREATE OR ALTER PROCEDURE sp_MatchTripDrivers
(
    @Trip_ID INT,
    @MaxDrivers INT = 5
)
AS
BEGIN
    SET NOCOUNT ON;

    -- Load trip info
    DECLARE @StartPoint GEOGRAPHY;
    DECLARE @Service_Type_ID INT;

    SELECT @StartPoint = Start_Point,
           @Service_Type_ID = Service_Type_ID
    FROM TRIP
    WHERE Trip_ID = @Trip_ID;

    IF @StartPoint IS NULL
        THROW 98001, 'Trip not found.', 1;

    -- Find nearby drivers
    ;WITH NearbyDrivers AS
    (
        SELECT TOP (@MaxDrivers)
               VL.License_Plate,
               VL.GeoPoint.STDistance(@StartPoint) AS DistanceMeters
        FROM Vehicle_Location VL
        JOIN VEHICLE V ON V.License_Plate = VL.License_Plate
        JOIN SERVICE_CATALOG SC ON SC.License_Plate = VL.License_Plate
        WHERE SC.Service_Type_ID = @Service_Type_ID
        ORDER BY VL.GeoPoint.STDistance(@StartPoint)
    )
    INSERT INTO TRIP_VEHICLE_MATCH
    (
        Offer_Time,
        Response_Time,
        Response_Status,
        Trip_ID,
        License_Plate
    )
    SELECT GETDATE(), NULL, 'pending', @Trip_ID, License_Plate
    FROM NearbyDrivers;

    SELECT 'Matching completed.' AS Result;
END;
GO
