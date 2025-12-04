CREATE   PROCEDURE sp_RequestTrip
(
    @User_ID            INT,
    @Service_Type_ID    INT,

    @Start_Lat          FLOAT,
    @Start_Lon          FLOAT,
    @End_Lat            FLOAT,
    @End_Lon            FLOAT
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @Trip_ID INT;
    DECLARE @ErrMsg VARCHAR(300);
    DECLARE @Start_Point GEOGRAPHY;
    DECLARE @End_Point GEOGRAPHY;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Validate user exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @User_ID)
        BEGIN
            ROLLBACK TRAN;
            SELECT 0 AS Success, 'User does not exist.' AS Message;
            RETURN;
        END

        ------------------------------------------------------------
        -- 2) Validate service type ID
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM SERVICE_TYPE WHERE Service_Type_ID = @Service_Type_ID
        )
        BEGIN
            ROLLBACK TRAN;
            SELECT 0 AS Success, 'Invalid service type ID.' AS Message;
            RETURN;
        END

        ------------------------------------------------------------
        -- 3) Validate coordinates
        ------------------------------------------------------------
        IF @Start_Lat IS NULL OR @Start_Lon IS NULL
            OR @End_Lat IS NULL OR @End_Lon IS NULL
        BEGIN
            ROLLBACK TRAN;
            SELECT 0 AS Success, 'Start and end coordinates are required.' AS Message;
            RETURN;
        END

        -- Build GEOGRAPHY
        SET @Start_Point = geography::Point(@Start_Lat, @Start_Lon, 4326);
        SET @End_Point   = geography::Point(@End_Lat,  @End_Lon,  4326);

        ------------------------------------------------------------
        -- 4) Require payment method
        ------------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM USER_PAYMENT_METHOD WHERE User_ID = @User_ID
        )
        BEGIN
            ROLLBACK TRAN;
            SELECT 0 AS Success, 'A valid payment method is required.' AS Message;
            RETURN;
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

        COMMIT TRAN;

        SELECT 
            1 AS Success,
            'Trip requested successfully.' AS Message,
            @Trip_ID AS Trip_ID;

    END TRY
    BEGIN CATCH

        IF @@TRANCOUNT > 0
            ROLLBACK TRAN;

        SET @ErrMsg = ERROR_MESSAGE();

        SELECT 
            0 AS Success,
            @ErrMsg AS Message;

    END CATCH;
END;
