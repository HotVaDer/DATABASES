CREATE OR ALTER PROCEDURE sp_Add_VehicleDoc
(
    @License_Plate VARCHAR(10),
    @Document_Type VARCHAR(50),
    @Document_File VARBINARY(MAX),
    @Issue_Date DATE,
    @Expiry_Date DATE,
    @actorID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ----------------------------------------------------------
        -- 1. Actor must be a Driver
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM DRIVER
            WHERE User_ID = @actorID
        )
        BEGIN
            RAISERROR('Unauthorized: Only drivers can upload vehicle documents.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END


        ----------------------------------------------------------
        -- 2. Ensure vehicle exists
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM VEHICLE
            WHERE License_Plate = @License_Plate
        )
        BEGIN
            RAISERROR('Vehicle does not exist.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END


        ----------------------------------------------------------
        -- 3. Ensure the driver owns this vehicle
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM VEHICLE V
            WHERE V.License_Plate = @License_Plate
              AND V.Driver_ID = @actorID
        )
        BEGIN
            RAISERROR('Unauthorized: Driver can only upload documents for their own vehicle.', 16, 1);
            ROLLBACK TRANSACTION;
            RETURN;
        END


        ----------------------------------------------------------
        -- 4. Insert new Vehicle Document
        ----------------------------------------------------------
        INSERT INTO VEHICLE_DOCUMENT
        (
            Status,
            Expiry_Date,
            Issue_Date,
            Uploaded_At,
            Doc_Type,
            File_URL,
            License_Plate
        )
        VALUES
        (
            'PENDING',
            @Expiry_Date,
            @Issue_Date,
            GETDATE(),
            @Document_Type,
            @Document_File,
            @License_Plate
        );


        COMMIT TRANSACTION;
    END TRY

    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;

        DECLARE @ErrorMessage NVARCHAR(4000) = ERROR_MESSAGE();
        RAISERROR(@ErrorMessage, 16, 1);
    END CATCH
END;
GO