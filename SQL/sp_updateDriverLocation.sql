CREATE OR ALTER PROCEDURE sp_UpdateDriverLocation
(
    @License_Plate VARCHAR(10),
    @Latitude FLOAT,
    @Longitude FLOAT
)
AS
BEGIN
    SET NOCOUNT ON;

    -- Validate vehicle exists
    IF NOT EXISTS (SELECT 1 FROM VEHICLE WHERE License_Plate = @License_Plate)
        THROW 97001, 'Vehicle not found.', 1;

    DECLARE @Geo GEOGRAPHY =
        geography::Point(@Latitude, @Longitude, 4326);

    -- Upsert logic: update if exists, else insert
    IF EXISTS (SELECT 1 FROM Vehicle_Location WHERE License_Plate = @License_Plate)
    BEGIN
        UPDATE Vehicle_Location
        SET GeoPoint = @Geo,
            Updated_At = GETDATE()
        WHERE License_Plate = @License_Plate;
    END
    ELSE
    BEGIN
        INSERT INTO Vehicle_Location (License_Plate, GeoPoint)
        VALUES (@License_Plate, @Geo);
    END

    SELECT 'Location updated.' AS Result;
END;
GO
