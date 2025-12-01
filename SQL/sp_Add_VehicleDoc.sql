CREATE OR ALTER PROCEDURE sp_Add_VehicleDoc
(
    @License_Plate  VARCHAR(10),
    @Doc_Type_Name  VARCHAR(50),
    @File_Data      VARBINARY(MAX),
    @Issue_Date     DATE,
    @Expiry_Date    DATE,
    @ActorID        INT
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        ----------------------------------------------------------
        -- 1) Actor must be a Driver
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM DRIVER WHERE User_ID = @ActorID
        )
        BEGIN
            ;THROW 94001, 'Unauthorized: Only drivers can upload vehicle documents.', 1;
        END

        ----------------------------------------------------------
        -- 2) Ensure vehicle exists
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM VEHICLE WHERE License_Plate = @License_Plate
        )
        BEGIN
            ;THROW 94002, 'Vehicle does not exist.', 1;
        END

        ----------------------------------------------------------
        -- 3) Ensure the driver owns this vehicle
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1
            FROM VEHICLE V
            WHERE V.License_Plate = @License_Plate
              AND V.User_ID = @ActorID
        )
        BEGIN
            ;THROW 94003, 'Unauthorized: Driver can only upload documents for their own vehicle.', 1;
        END

        ----------------------------------------------------------
        -- 4) Validate document type exists
        ----------------------------------------------------------
        IF NOT EXISTS (
            SELECT 1 FROM VEHICLE_DOC_TYPE WHERE Doc_Type_Name = @Doc_Type_Name
        )
        BEGIN
            ;THROW 94004, 'Invalid vehicle document type.', 1;
        END

        ----------------------------------------------------------
        -- 5) Insert new Vehicle Document as pending
        ----------------------------------------------------------
        INSERT INTO VEHICLE_DOCUMENT
        (
            Status,
            Expiry_Date,
            Issue_Date,
            Uploaded_At,
            File_Data,
            License_Plate,
            Doc_Type_Name
        )
        VALUES
        (
            'pending',
            @Expiry_Date,
            @Issue_Date,
            GETDATE(),
            @File_Data,
            @License_Plate,
            @Doc_Type_Name
        );

        SELECT 'Vehicle document submitted successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH
END;
GO