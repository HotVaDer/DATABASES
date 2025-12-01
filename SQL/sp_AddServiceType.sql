CREATE OR ALTER PROCEDURE sp_AddServiceType
(
    @Service_Type_Name  VARCHAR(50),
    @Description        VARCHAR(255)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY

        ------------------------------------------------------------
        -- 1) Βασικός έλεγχος τιμών
        ------------------------------------------------------------
        IF @Service_Type_Name IS NULL OR LTRIM(RTRIM(@Service_Type_Name)) = ''
        BEGIN
            ;THROW 98001, 'Service_Type_Name cannot be empty.', 1;
        END

        IF @Description IS NULL OR LTRIM(RTRIM(@Description)) = ''
        BEGIN
            ;THROW 98002, 'Description cannot be empty.', 1;
        END

        ------------------------------------------------------------
        -- 2) Έλεγχος ότι δεν υπάρχει ήδη υπηρεσία με αυτό το όνομα
        ------------------------------------------------------------
        IF EXISTS (
            SELECT 1
            FROM SERVICE_TYPE
            WHERE Service_Type_Name = @Service_Type_Name
        )
        BEGIN
            ;THROW 98003, 'Service type with this name already exists.', 1;
        END

        ------------------------------------------------------------
        -- 3) Εισαγωγή στο SERVICE_TYPE
        ------------------------------------------------------------
        INSERT INTO SERVICE_TYPE
        (
            Description,
            Service_Type_Name
        )
        VALUES
        (
            @Description,
            @Service_Type_Name
        );

        DECLARE @Service_Type_ID INT = SCOPE_IDENTITY();

        ------------------------------------------------------------
        -- 4) Επιστροφή Service_Type_ID
        ------------------------------------------------------------
        SELECT 
            @Service_Type_ID   AS Service_Type_ID,
            @Service_Type_Name AS Service_Type_Name,
            'Service type created successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;
END;
GO
