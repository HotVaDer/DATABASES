CREATE   PROCEDURE sp_SubmitDriverDocument
(
    @UserID        INT,
    @Doc_Type_Name VARCHAR(50),
    @Issue_Date    DATE,
    @Expiry_Date   DATE,
    @File_Data     VARBINARY(MAX)
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @CurrentStatus VARCHAR(50);

    ------------------------------------------------------------
    -- 1) Check if driver exists
    ------------------------------------------------------------
    SELECT @CurrentStatus = Status
    FROM DRIVER
    WHERE User_ID = @UserID;

    IF @CurrentStatus IS NULL
    BEGIN
        SELECT 'NOT_DRIVER' AS Status;
        RETURN;
    END

    ------------------------------------------------------------
    -- 2) If denied → allow resubmit and reset to pending
    ------------------------------------------------------------
    IF @CurrentStatus = 'denied'
    BEGIN
        UPDATE DRIVER
        SET Status = 'pending'
        WHERE User_ID = @UserID;

        SET @CurrentStatus = 'pending';
    END

    ------------------------------------------------------------
    -- 3) Allow only pending or approved drivers to submit docs
    ------------------------------------------------------------
    IF @CurrentStatus NOT IN ('pending', 'approved')
    BEGIN
        SELECT 'BAD_STATUS' AS Status;
        RETURN;
    END

    ------------------------------------------------------------
    -- 4) Validate document type exists
    ------------------------------------------------------------
    IF NOT EXISTS (SELECT 1 FROM DRIVER_DOC_TYPE WHERE Doc_Type_Name = @Doc_Type_Name)
    BEGIN
        SELECT 'INVALID_DOC_TYPE' AS Status;
        RETURN;
    END

    ------------------------------------------------------------
    -- 5) Insert driver document (pending review by admin)
    ------------------------------------------------------------
    INSERT INTO DRIVER_DOCUMENT
    (
        Status,
        Expiry_Date,
        Issue_Date,
        Uploaded_At,
        Doc_Type_Name,
        File_Data,
        User_ID
    )
    VALUES
    (
        'pending',
        @Expiry_Date,
        @Issue_Date,
        GETDATE(),
        @Doc_Type_Name,
        @File_Data,
        @UserID
    );

    SELECT 'SUCCESS' AS Status;
END;
