CREATE OR ALTER PROCEDURE sp_ApproveVehicle
(
    @License_Plate VARCHAR(10)
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        DECLARE @CurrentStatus VARCHAR(50);

        ------------------------------------------------------------
        -- 1) Βεβαιωνόμαστε ότι το όχημα υπάρχει
        ------------------------------------------------------------
        SELECT @CurrentStatus = Status
        FROM VEHICLE
        WHERE License_Plate = @License_Plate;

        IF @CurrentStatus IS NULL
        BEGIN
            ;THROW 95001, 'Vehicle does not exist.', 1;
        END

        ------------------------------------------------------------
        -- 2) Μόνο vehicles με status = 'pending' μπορούν να εγκριθούν
        ------------------------------------------------------------
        IF @CurrentStatus <> 'pending'
        BEGIN
            ;THROW 95002, 'Vehicle status must be pending to approve.', 1;
        END

        ------------------------------------------------------------
        -- 3) Έλεγχος ΟΛΩΝ των απαιτούμενων εγγράφων οχήματος
        --    Κάθε vehicle doc type πρέπει να έχει:
        --    - 1 document
        --    - Status = approved
        --    - Expiry >= today
        ------------------------------------------------------------
        IF EXISTS
        (
            SELECT 1
            FROM VEHICLE_DOC_TYPE AS VT
            WHERE NOT EXISTS
            (
                SELECT 1
                FROM VEHICLE_DOCUMENT AS VD
                WHERE VD.License_Plate = @License_Plate
                  AND VD.Doc_Type_Name = VT.Name
                  AND VD.Status = 'approved'
                  AND VD.Expiry_Date >= CONVERT(date, GETDATE())
            )
        )
        BEGIN
            ;THROW 95003, 'Vehicle does not have all required valid approved documents.', 1;
        END

        ------------------------------------------------------------
        -- 4) Εγκρίνουμε το όχημα
        ------------------------------------------------------------
        UPDATE VEHICLE
        SET Status = 'approved'
        WHERE License_Plate = @License_Plate;

        ------------------------------------------------------------
        -- 5) Επιτυχία
        ------------------------------------------------------------
        SELECT 'Vehicle approved successfully.' AS Result;

    END TRY
    BEGIN CATCH
        THROW;
    END CATCH;

END;
GO
