CREATE OR ALTER PROCEDURE sp_AddPaymentMethod
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

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1) Ensure user exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @User_ID)
            THROW 98001, 'User does not exist.', 1;

        ------------------------------------------------------------
        -- 2) Validate card data
        ------------------------------------------------------------
        IF LEN(@Last_4_Digits) <> 4 OR @Last_4_Digits LIKE '%[^0-9]%'
            THROW 98002, 'Last 4 digits must be numeric.', 1;

        IF @Expiry_Date <= CONVERT(date, GETDATE())
            THROW 98003, 'Card expiry date must be in the future.', 1;

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

        DECLARE @Payment_Method_ID INT = SCOPE_IDENTITY();

        ------------------------------------------------------------
        -- 4) Return inserted record
        ------------------------------------------------------------
        SELECT 
            Payment_Method_ID,
            Card_Holder_Name,
            Last_4_Digits,
            Card_Type,
            Expiry_Date,
            User_ID
        FROM USER_PAYMENT_METHOD
        WHERE Payment_Method_ID = @Payment_Method_ID;

        COMMIT TRANSACTION;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;
        THROW;
    END CATCH;
END;
GO
