CREATE   PROCEDURE sp_ApproveDriverDocument
(
    @DocumentID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    UPDATE DRIVER_DOCUMENT
    SET Status = 'approved'
    WHERE Driver_Document_ID = @DocumentID;

    -- Check if ALL documents for that user are approved
    DECLARE @UserID INT;
    SELECT @UserID = User_ID FROM DRIVER_DOCUMENT WHERE Driver_Document_ID = @DocumentID;

    IF NOT EXISTS (
        SELECT 1 FROM DRIVER_DOCUMENT 
        WHERE User_ID = @UserID AND Status <> 'approved'
    )
    BEGIN
        UPDATE DRIVER SET Status = 'approved'
        WHERE User_ID = @UserID;
    END
END;
