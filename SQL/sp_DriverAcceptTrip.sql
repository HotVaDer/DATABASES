CREATE   PROCEDURE sp_DriverAcceptTrip
(
    @TripID INT,
    @DriverID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    -- prevent already taken
    IF EXISTS (SELECT 1 FROM TRIP WHERE Trip_ID=@TripID AND Status <> 'waiting')
        THROW 82001, 'Trip already taken.', 1;

    DECLARE @Plate VARCHAR(10);

    SELECT @Plate = License_Plate
    FROM VEHICLE
    WHERE User_ID = @DriverID;

    INSERT INTO TRIP_VEHICLE_MATCH (Offer_Time, Response_Time, Response_Status, Trip_ID, License_Plate)
    VALUES (GETDATE(), GETDATE(), 'accepted', @TripID, @Plate);

    UPDATE TRIP SET Status='accepted' WHERE Trip_ID=@TripID;

    SELECT 'Trip Accepted' AS Result;
END;
