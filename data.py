import datetime
from faker import Faker
import json
import random
import math
fake = Faker()

def create_User():
    experiences = ['intern','junior','mid','senior','expert']
    roles=['technician', 'security', 'support']
    user = []
    for _ in range(500):
        Firstname = fake.name()
        LastName = fake.last_name()
        Birthdate = fake.date_of_birth(minimum_age=18, maximum_age=70).strftime("%Y-%m-%d")
        Email= fake.email()
        Address=fake.address()
        Gender=random.choice(['male','female'])
        info = {
            'FirstName': Firstname,
            'LastName': LastName,
            'Birthdate': Birthdate,
            'Email': Email,
            'Address': Address,
            'Gender': Gender
        }
        
        user.append(info)
    # json.dump(staff, open('data/staff.json', 'w'), indent=4)
    return user
def create_Auth():
    Auth=[]
    for _ in range(500):
        username = fake.user_name()
        password = fake.password()
        info = {
            'Username': username,
            'Password': password,
        }
        Auth.append(info)
    return Auth

def User_Preferences():
    preferences = []
    Setting_Names = ['notifications', 'dark_mode', 'auto_update', 'location_services', 'data_saver','save_payment_methods']
    for _ in range(500):
        Setting_Name=random.choice(Setting_Names)
        Setting_Value=random.choice([True, False])
        info = {
            'Setting_Name': Setting_Name,
            'Setting_Value': Setting_Value,
        }
        preferences.append(info)
    return preferences

def GDPR_LOG():
    GDPR_LOG = []
    for _ in range(500):
        Status = random.choice(['requested', 'processed', 'completed', 'denied'])
        Comment = fake.sentence(nb_words=6)
        Request_Date = fake.date_between(start_date='-2y', end_date='today').strftime("%Y-%m-%d")
        Approval_Date = fake.date_between(start_date=Request_Date, end_date='today').strftime("%Y-%m-%d")
        info = {
            'Status': Status,
            'Comment': Comment,
            'Request_Date': Request_Date,
            'Approval_Date': Approval_Date,
        }
        GDPR_LOG.append(info)
    return GDPR_LOG

def Driver():
    Driver = []
    statuses = ['active', 'inactive', 'suspended', 'pending']
    for _ in range(500):
        status=random.choice(statuses)
        Eu_Residences_Pass=fake.url()
        info = {
            'Status': status,
            'Eu_Residences_Pass': Eu_Residences_Pass,
        }
        Driver.append(info)
    return Driver

def User_Payment_Methods():
    User_Payment_Methods = []
    Card_Types = ['Visa', 'MasterCard', 'American Express', 'Discover']
    for _ in range(500):
        Card_Type=random.choice(Card_Types)
        full_number = fake.credit_card_number(card_type=Card_Type)
        Last4_Digits = ''.join(filter(str.isdigit, full_number))[-4:]
        Expiration_Date=fake.credit_card_expire()
        Card_Holder_Name=fake.name()
        info = {
            'Card_Type': Card_Type,
            'Card_Number': Last4_Digits,
            'Expiration_Date': Expiration_Date,
            'Card_Holder_Name': Card_Holder_Name,
        }
        User_Payment_Methods.append(info)
    return User_Payment_Methods

def Payment_Transactions():
    Payment_Transactions = []
    Currency = ['USD', 'EUR', 'GBP', 'JPY', 'AUD']
    for _ in range(500):
        Gross_Amount=round(random.uniform(10.0, 1000.0), 2)
        Transaction_Date = fake.date_between(start_date='-2y', end_date='today').strftime("%Y-%m-%d")
        Status=random.choice(['completed', 'pending', 'failed', 'refunded'])
        info = {
            'Gross_Amount': Gross_Amount,
            'Transaction_Date': Transaction_Date,
            'Status': Status,
            'Currency': random.choice(Currency),
        }
        Payment_Transactions.append(info)
    return Payment_Transactions

def Driver_Document():
    Driver_Document = []
    Document_Types = ['ID','license', 'insurance', 'registration', 'inspection','Certificate of Clean Criminal Record','Medical Report','Psychological Evaluation Report']
    for _ in range(500):
        Document_Type=random.choice(Document_Types)
        Document_URL=fake.url()
        upload_date = fake.date_between(start_date='-2y', end_date='today')
        Issue_Date = fake.date_between(start_date=upload_date, end_date='today')
        Expiration_Date = fake.date_between(start_date='today', end_date='+5y').strftime("%Y-%m-%d")
        info = {
            'Document_Type': Document_Type,
            'File_URL': Document_URL,
            'Expiry_Date': Expiration_Date,
            'Upload_Date': upload_date.strftime("%Y-%m-%d"),
            'Issue_Date': Issue_Date.strftime("%Y-%m-%d"),
        }
        Driver_Document.append(info)
    return Driver_Document

def Veihilce_Documents():
    Veihilce_Documents = []
    Document_Types = ['registration', 'inspection','emission test']
    for _ in range(500):
        Document_Type=random.choice(Document_Types)
        File_URL=fake.url()
        upload_date = fake.date_between(start_date='-2y', end_date='today')
        Issue_Date = fake.date_between(start_date=upload_date, end_date='today')
        Expiration_Date = fake.date_between(start_date='today', end_date='+5y').strftime("%Y-%m-%d")
        info = {
            'Document_Type': Document_Type,
            'File_URL': File_URL,
            'Expiry_Date': Expiration_Date,
            'Uploaded_At': upload_date.strftime("%Y-%m-%d"),
            'Issue_Date': Issue_Date.strftime("%Y-%m-%d"),
        }
        Veihilce_Documents.append(info)
    return Veihilce_Documents

def Document_Verification():
    Document_Verification = []
    Statuses = ['pending', 'approved', 'rejected']
    for _ in range(500):
        Status=random.choice(Statuses)
        Verified_At = fake.date_between(start_date='-2y', end_date='today').strftime("%Y-%m-%d")
        Comment = fake.sentence(nb_words=6)
        info = {
            'Status': Status,
            'Verified_At': Verified_At,
            'Comments': Comment,
        }
        Document_Verification.append(info)
    return Document_Verification

def Vehicle():
    Vehicle = []
    Vehicle_Types = ['HatchBack', 'truck', 'motorcycle', 'van', 'suv', 'sedan', 'coupe', 'convertible', 'wagon', 'minivan', 'pickup']
    for _ in range(500):
        Vehicle_Type=random.choice(Vehicle_Types)
        Seat_Capacity=random.randint(2, 8)
        Trunk_Space=random.randint(100, 1000)  # in liters
        Trunk_Weight=random.randint(50, 300)  # in kg
        Price_To_Ride=round(random.uniform(5.0, 100.0), 2)
        Status=random.choice(['Approved', 'Denied', 'pending'])
        License_Plate=fake.license_plate()
        info = {
            'Vehicle_Type': Vehicle_Type,
            'Seat_Capacity': Seat_Capacity,
            'Trunk_Space': Trunk_Space,
            'Trunk_Weight': Trunk_Weight,
            'Price_To_Ride': Price_To_Ride,
            'Status': Status,
            'License_Plate': License_Plate,
        }
        Vehicle.append(info)
    return Vehicle

def Vehicle_Requirements():
    Vehicle_Requirements = []
    for _ in range(500):
        Min_Seats = random.randint(2, 8)
        Max_vehicles_Age = random.randint(1, 15)
        Min_Trunk_Space = random.randint(100, 500)
        Min_Trunk_Weight = random.randint(50, 200)
        Must_Be_4Door = random.choice([True, False])
        Must_Have_Rear_Seats = random.choice([True, False])
        Required_Vehicle_Types = ['HatchBack', 'truck', 'motorcycle', 'van', 'suv', 'sedan', 'coupe', 'convertible', 'wagon', 'minivan', 'pickup']
        info = {
            'Min_Seats': Min_Seats,
            'Max_vehicles_Age': Max_vehicles_Age,
            'Min_Trunk_Space': Min_Trunk_Space,
            'Min_Trunk_Weight': Min_Trunk_Weight,
            'Must_Be_4Door': Must_Be_4Door,
            'Must_Have_Rear_Seats': Must_Have_Rear_Seats,
            'Required_Vehicle_Types': random.sample(Required_Vehicle_Types, k=random.randint(1, len(Required_Vehicle_Types))),
        }
        Vehicle_Requirements.append(info)
    return Vehicle_Requirements
def Bridge_Points():
    Bridge_Points = []
    for _ in range(500):
        Latitude = fake.latitude()
        Longitude = fake.longitude()
        info = {
            'Bridge_Lat': Latitude,
            'Bridge_Long': Longitude,
        }
        Bridge_Points.append(info)
    return Bridge_Points

def Geofence_Regions():
    Geofence_Regions = []
    for _ in range(500):
        Max_Lat = fake.latitude()
        Min_Lat = fake.latitude()
        Max_Long = fake.longitude()
        Min_Long = fake.longitude()
        Name=fake.region()
        info = {
            'Max_Lat': max(Max_Lat, Min_Lat),
            'Min_Lat': min(Max_Lat, Min_Lat),
            'Max_Long': max(Max_Long, Min_Long),
            'Min_Long': min(Max_Long, Min_Long),
            'Name': Name,
        }
        Geofence_Regions.append(info)
    return Geofence_Regions

def Service_Types():
    Service_Types = []
    Types = ['Simple_Ride','Luxury_Ride','Delivery_Service_Light','Delivery_Service_Heavy','MultiStop_Ride']
    for _ in range(500):
        Type=random.choice(Types)
        Description = fake.sentence(nb_words=6)
        info = {
            'Type': Type,
            'Description': Description,
        }
        Service_Types.append(info)
    return Service_Types

def Trip():
    Trip = []
    for _ in range(500):
        Start_Latitude = fake.latitude()
        Start_Longitude = fake.longitude()
        End_Latitude = fake.latitude()
        End_Longitude = fake.longitude()
        Requested_Time = fake.date_time_between(start_date='-2y', end_date='now')
        Duration=random.randint(5, 180)  # in minutes
        Status=random.choice(['completed', 'in_progress'])
        Price_Final=round(random.uniform(10.0, 500.0), 2)
        info = {
            'Start_Latitude': Start_Latitude,
            'Start_Longitude': Start_Longitude,
            'End_Latitude': End_Latitude,
            'End_Longitude': End_Longitude,
            'Requested_Time': Requested_Time.strftime("%Y-%m-%d %H:%M:%S"),
            'Duration': Duration,
            'Status': Status,
            'Price_Final': Price_Final,
        }
        Trip.append(info)
    return Trip
def Trip_Segments():
    Trip_Segments = []
    for _ in range(500):
        Sequencer=random.randint(1, 10)
        Start_Latitude = fake.latitude()
        Start_Longitude = fake.longitude()
        End_Latitude = fake.latitude()
        End_Longitude = fake.longitude()
        info = {
            'Sequence_no': Sequencer,
            'Start_Latitude': Start_Latitude,
            'Start_Longitude': Start_Longitude,
            'End_Latitude': End_Latitude,
            'End_Longitude': End_Longitude,
        }
        Trip_Segments.append(info)
    return Trip_Segments

def Trip_Vehicle_Match():
    Trip_Vehicle_Match = []
    for _ in range(500):
        Offer_Time = fake.date_time_between(start_date='-2y', end_date='now').strftime("%Y-%m-%d %H:%M:%S")
        Response_Time = fake.date_time_between(start_date=Offer_Time, end_date='now').strftime("%Y-%m-%d %H:%M:%S")
        Response_Status=random.choice(['accepted', 'declined', 'no_response'])
        info = {
            'Offer_Time': Offer_Time,
            'Response_Time': Response_Time,
            'Response_Status': Response_Status,
        }
        Trip_Vehicle_Match.append(info)
    return Trip_Vehicle_Match