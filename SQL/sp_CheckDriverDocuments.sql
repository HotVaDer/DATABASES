CREATE OR ALTER PROCEDURE sp_CheckDriverDocuments
AS
BEGIN
    SET NOCOUNT ON;

    ------------------------------------------------------------
    -- 1) Μαρκάρουμε ως expired όσα έγγραφα έχουν λήξει
    ------------------------------------------------------------
    UPDATE DD
    SET Status = 'expired'
    FROM DRIVER_DOCUMENT AS DD
    WHERE DD.Expiry_Date < CONVERT(date, GETDATE())
      AND DD.Status IN ('approved', 'pending', 'pending_review');

    ------------------------------------------------------------
    -- 2) Βρίσκουμε approved drivers που ΔΕΝ έχουν όλα τα
    --    απαραίτητα document types σε approved + μη ληγμένα
    ------------------------------------------------------------
    ;WITH DriversMissingValidDocs AS
    (
        SELECT D.User_ID
        FROM DRIVER AS D
        WHERE D.Status = 'approved'
          AND EXISTS
          (
              SELECT 1
                FROM DRIVER_DOC_TYPE AS DT
              WHERE NOT EXISTS
              (
                  SELECT 1
                  FROM DRIVER_DOCUMENT AS DD
                  WHERE DD.User_ID            = D.User_ID
                  AND DD.Doc_Type_Name      = DT.Doc_Type_Name
                    AND DD.Status             = 'approved'
                    AND DD.Expiry_Date >= CONVERT(date, GETDATE())
              )
          )
    )
    UPDATE D
    SET D.Status = 'pending'
    FROM DRIVER AS D
    INNER JOIN DriversMissingValidDocs AS X
        ON X.User_ID = D.User_ID;

END;
GO
