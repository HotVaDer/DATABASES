CREATE   PROCEDURE sp_AddPaymentMethod
(
    @User_ID            INT,
    @Card_Holder_Name   VARCHAR(100),
    @Last_4_Digits      CHAR(4),
    @Card_Type          VARCHAR(20),
    @Expiry_Date        DATE
)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @Payment_Method_ID INT;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Check if user exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @User_ID)
        BEGIN
            ROLLBACK TRANSACTION;
            SELECT 
                Success = 0,
                Message = 'User does not exist.',
                Payment_Method_ID = NULL;
            RETURN;
        END

        ------------------------------------------------------------
        -- 2) Validate card data
        ------------------------------------------------------------
        IF LEN(@Last_4_Digits) <> 4 OR @Last_4_Digits LIKE '%[^0-9]%'
        BEGIN
            ROLLBACK TRANSACTION;
            SELECT 
                Success = 0,
                Message = 'Last 4 digits must be numeric.',
                Payment_Method_ID = NULL;
            RETURN;
        END

        IF @Expiry_Date <= CONVERT(date, GETDATE())
        BEGIN
            ROLLBACK TRANSACTION;
            SELECT 
                Success = 0,
                Message = 'Card expiry date must be in the future.',
                Payment_Method_ID = NULL;
            RETURN;
        END

        ------------------------------------------------------------
        -- 3) Insert payment method
        ------------------------------------------------------------
        INSERT INTO USER_PAYMENT_METHOD
        (
            Card_Holder_Name,
            Last_4_Digits,
            Card_Type,
            Expiry_Date,
            User_ID
        )
        VALUES
        (
            @Card_Holder_Name,
            @Last_4_Digits,
            @Card_Type,
            @Expiry_Date,
            @User_ID
        );

        SET @Payment_Method_ID = SCOPE_IDENTITY();

        COMMIT TRANSACTION;

        ------------------------------------------------------------
        -- 4) Return success
        ------------------------------------------------------------
        SELECT 
            Success = 1,
            Message = 'Payment method added.',
            Payment_Method_ID = @Payment_Method_ID;

    END TRY
    BEGIN CATCH

        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;

        SELECT 
            Success = 0,
            Message = ERROR_MESSAGE(),
            Payment_Method_ID = NULL;
    END CATCH
END;

