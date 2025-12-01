CREATE OR ALTER PROCEDURE sp_CheckVehicleDocuments
AS
BEGIN
    SET NOCOUNT ON;

    ------------------------------------------------------------
    -- 1) Μαρκάρουμε ως expired όσα vehicle documents έχουν λήξει
    ------------------------------------------------------------
    UPDATE VD
    SET Status = 'expired'
    FROM VEHICLE_DOCUMENT AS VD
    WHERE VD.Expiry_Date < CONVERT(date, GETDATE())
      AND VD.Status IN ('approved', 'pending', 'pending_review');

    ------------------------------------------------------------
    -- 2) Βρίσκουμε approved vehicles που ΔΕΝ έχουν όλα τα
    --    απαραίτητα vehicle doc types σε approved + μη ληγμένα
    ------------------------------------------------------------
    ;WITH VehiclesMissingValidDocs AS
    (
        SELECT V.License_Plate
        FROM VEHICLE AS V
        WHERE V.Status = 'approved'
          AND EXISTS
          (
              SELECT 1
              FROM VEHICLE_DOC_TYPE AS VT
              WHERE NOT EXISTS
              (
                  SELECT 1
                  FROM VEHICLE_DOCUMENT AS VD
                  WHERE VD.License_Plate   = V.License_Plate
                    AND VD.Doc_Type_Name   = VT.Doc_Type_Name
                    AND VD.Status          = 'approved'
                    AND VD.Expiry_Date    >= CONVERT(date, GETDATE())
              )
          )
    )
    UPDATE V
    SET V.Status = 'pending'
    FROM VEHICLE AS V
    INNER JOIN VehiclesMissingValidDocs AS X
        ON X.License_Plate = V.License_Plate;

END;
GO
