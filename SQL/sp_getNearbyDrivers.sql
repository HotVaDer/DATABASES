CREATE   PROCEDURE sp_GetNearbyDrivers
(
    @Latitude  FLOAT,
    @Longitude FLOAT,
    @MaxDistance FLOAT = 5000,  -- 5 km radius
    @MaxResults  INT = 10
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @UserPoint GEOGRAPHY =
        geography::Point(@Latitude, @Longitude, 4326);

    SELECT TOP (@MaxResults)
        D.User_ID,
        V.License_Plate,
        VL.GeoPoint,
        VL.Updated_At,
        VL.GeoPoint.STDistance(@UserPoint) AS DistanceMeters
    FROM Vehicle_Location VL
    JOIN VEHICLE V ON V.License_Plate = VL.License_Plate
    JOIN DRIVER D ON D.User_ID = V.User_ID
    WHERE 
        D.Status = 'approved'
        AND V.Status = 'active'
        AND VL.GeoPoint.STDistance(@UserPoint) <= @MaxDistance
    ORDER BY VL.GeoPoint.STDistance(@UserPoint);
END;
