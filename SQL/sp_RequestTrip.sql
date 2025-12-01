CREATE OR ALTER PROCEDURE sp_RequestTrip
(
    @User_ID            INT,
    @Service_Type_Name  VARCHAR(50),
    @Start_Point        geography,
    @End_Point          geography
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;
        DECLARE @Service_Type_ID INT;
        DECLARE @Trip_ID INT;

        ------------------------------------------------------------
        -- 1) Validate user exists
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM [USER] WHERE User_ID = @User_ID
        )
        BEGIN
            ;THROW 96001, 'User does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Validate service type
        ------------------------------------------------------------
        SELECT @Service_Type_ID = ST.Service_Type_ID
        FROM SERVICE_TYPE AS ST
        WHERE ST.Service_Type_Name = @Service_Type_Name;

        IF @Service_Type_ID IS NULL
        BEGIN
            ;THROW 96002, 'Invalid service type.', 1;
        END

        ------------------------------------------------------------
        -- 3) Validate geography inputs
        ------------------------------------------------------------
        IF @Start_Point IS NULL OR @End_Point IS NULL
        BEGIN
            ;THROW 96003, 'Start and end points are required.', 1;
        END

        IF @Start_Point.STSrid <> 4326 OR @End_Point.STSrid <> 4326
        BEGIN
            ;THROW 96004, 'Geography SRID must be 4326 (WGS 84).', 1;
        END

        ------------------------------------------------------------
        -- 4) OPTIONAL: Ensure user has a payment method
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM USER_PAYMENT_METHOD WHERE User_ID = @User_ID
        )
        BEGIN
            ;THROW 96005, 'A valid payment method is required to request a trip.', 1;
        END

        ------------------------------------------------------------
        -- 5) Insert trip
        ------------------------------------------------------------
        INSERT INTO TRIP
        (
            Request_Time,
            [Status],
            Start_Point,
            End_Point,
            User_ID,
            Service_Type_ID
        )
        VALUES
        (
            GETDATE(),
            'requested',
            @Start_Point,
            @End_Point,
            @User_ID,
            @Service_Type_ID
        );

        SET @Trip_ID = SCOPE_IDENTITY();

        SELECT @Trip_ID AS Trip_ID, 'Trip requested successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH
END;
GO
