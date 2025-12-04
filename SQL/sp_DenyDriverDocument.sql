CREATE   PROCEDURE sp_DenyDriverDocument
(
    @DocumentID INT
)
AS
BEGIN
    SET NOCOUNT ON;

    UPDATE DRIVER_DOCUMENT
    SET Status = 'denied'
    WHERE Driver_Document_ID = @DocumentID;

    -- When ANY document is denied → driver becomes denied
    DECLARE @UserID INT;
    SELECT @UserID = User_ID FROM DRIVER_DOCUMENT WHERE Driver_Document_ID = @DocumentID;

    UPDATE DRIVER
    SET Status = 'denied'
    WHERE User_ID = @UserID;
END;
