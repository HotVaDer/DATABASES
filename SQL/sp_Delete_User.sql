CREATE OR ALTER PROCEDURE sp_DeleteUser
(
    @UserID INT,      -- the user we want to delete
    @ActorID INT      -- the user performing the delete
)
AS
BEGIN
    SET NOCOUNT ON;

    BEGIN TRY
        BEGIN TRANSACTION;

        ------------------------------------------------------------
        -- 1. Check: User exists
        ------------------------------------------------------------
        IF NOT EXISTS (SELECT 1 FROM [USER] WHERE User_ID = @UserID)
            THROW 70001, 'User does not exist.', 1;

        ------------------------------------------------------------
        -- 2. Get ACTOR role
        ------------------------------------------------------------
        DECLARE @ActorRole VARCHAR(50);

        SELECT TOP 1 @ActorRole = UT.Type_Name
        FROM USER_TYPE_MAP UTM
        JOIN USER_TYPE UT 
            ON UTM.User_Type_ID = UT.User_Type_ID
        WHERE UTM.User_ID = @ActorID;

        IF @ActorRole IS NULL
            THROW 70002, 'Actor does not have a role.', 1;

        ------------------------------------------------------------
        -- 3. Get TARGET role
        ------------------------------------------------------------
        DECLARE @TargetRole VARCHAR(50);

        SELECT TOP 1 @TargetRole = UT.Type_Name
        FROM USER_TYPE_MAP UTM
        JOIN USER_TYPE UT 
            ON UTM.User_Type_ID = UT.User_Type_ID
        WHERE UTM.User_ID = @UserID;

        IF @TargetRole IS NULL
            THROW 70003, 'Target user does not have a role.', 1;

        ------------------------------------------------------------
        -- 4. Permission Rules
        ------------------------------------------------------------

        -- Rule A: passengers or drivers cannot delete anyone
        IF @ActorRole NOT IN ('system_admin', 'system_operator')
            THROW 70004, 'Access denied: insufficient privileges.', 1;

        -- Rule B: operator cannot delete admin
        IF @ActorRole = 'system_operator' AND @TargetRole = 'system_admin'
            THROW 70005, 'Operators cannot delete administrators.', 1;

        -- Rule C: operator cannot delete another operator
        IF @ActorRole = 'system_operator' AND @TargetRole = 'system_operator'
            THROW 70006, 'Operators cannot delete other operators.', 1;

        ------------------------------------------------------------
        -- 5. DELETE RELATED DATA (in correct FK order)
        ------------------------------------------------------------

        -- User Preferences
        DELETE FROM USER_PREFERENCES WHERE User_ID = @UserID;

        -- Payment Methods
        DELETE FROM USER_PAYMENT_METHOD WHERE User_ID = @UserID;

        -- Authentication
        DELETE FROM AUTHENTICATION WHERE User_ID = @UserID;

        -- GDPR log entries
        DELETE FROM GDPR_LOG WHERE User_ID = @UserID;

        -- Trip-related deletions
        DELETE FROM TRIP_VEHICLE_MATCH 
            WHERE Trip_ID IN (SELECT Trip_ID FROM TRIP WHERE User_ID = @UserID);

        DELETE FROM TRIP_SEGMENT 
            WHERE Trip_ID IN (SELECT Trip_ID FROM TRIP WHERE User_ID = @UserID);

        DELETE FROM PAYMENT_TRANSACTION WHERE User_ID = @UserID;

        DELETE FROM TRIP WHERE User_ID = @UserID;

        -- If the user is a driver → delete driver tables
        IF EXISTS (SELECT 1 FROM DRIVER WHERE User_ID = @UserID)
        BEGIN
            DECLARE @DriverID INT = (SELECT Driver_ID FROM DRIVER WHERE User_ID = @UserID);

            DELETE FROM DRIVER_DOCUMENT WHERE Driver_ID = @DriverID;
            DELETE FROM VEHICLE WHERE Driver_ID = @DriverID;
            DELETE FROM DRIVER WHERE Driver_ID = @DriverID;
            DELETE FROM SERVICE_CATALOG WHERE Driver_ID = @DriverID;
        END

        -- Remove roles
        DELETE FROM USER_TYPE_MAP WHERE User_ID = @UserID;

        ------------------------------------------------------------
        -- 6. Delete User
        ------------------------------------------------------------
        DELETE FROM [USER] WHERE User_ID = @UserID;

        ------------------------------------------------------------
        -- 7. Success
        ------------------------------------------------------------
        COMMIT TRANSACTION;

        SELECT 'User deleted successfully.' AS Result;

    END TRY
    BEGIN CATCH
        IF XACT_STATE() <> 0
            ROLLBACK TRANSACTION;
        THROW;
    END CATCH;
END;
GO
