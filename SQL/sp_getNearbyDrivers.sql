CREATE OR ALTER PROCEDURE sp_GetNearbyDrivers
(
    @Latitude FLOAT,
    @Longitude FLOAT,
    @Service_Type_ID INT,
    @MaxResults INT = 5
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @UserPoint GEOGRAPHY =
        geography::Point(@Latitude, @Longitude, 4326);

    SELECT TOP (@MaxResults)
           V.License_Plate,
           VL.Updated_At,
           VL.GeoPoint.STDistance(@UserPoint) AS DistanceMeters
    FROM Vehicle_Location VL
    JOIN VEHICLE V ON V.License_Plate = VL.License_Plate
    JOIN SERVICE_CATALOG SC ON SC.License_Plate = V.License_Plate
    WHERE SC.Service_Type_ID = @Service_Type_ID
    ORDER BY VL.GeoPoint.STDistance(@UserPoint);
END;
GO
