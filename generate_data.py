import datetime
from faker import Faker
import json
import random
import math
fake = Faker()

def create_staff():
    experiences = ['intern','junior','mid','senior','expert']
    roles=['technician', 'security', 'support']
    staff = []
    for _ in range(500):
        name = fake.name()
        age=random.randint(18,60)
        experience_level = random.choice(experiences)
        Roles = random.choice(roles)
        info = {
            'Staff_Name': name,
            'Staff_Age':age,
            'Staff_Role': Roles,
            'LevelOfExperience': experience_level
        }
        staff.append(info)
    # json.dump(staff, open('data/staff.json', 'w'), indent=4)
    return staff

def create_act(artists,bands) -> list[dict[str,str]] :
    act=[]
    for artist_id, artist in enumerate(artists):
        artist_id=artist_id+1
        info={
            'Act_ID':artist_id,
            'Act_Type':'artist',
        }
        act.append(info)
    for band_id, bands in enumerate(bands):
        band_id=band_id+len(artists)+1
        info={
            'Act_ID':band_id,
            'Act_Type':'band',
        }
        act.append(info)
    #json.dump(act, open('data/artist_genres.json', 'w'), indent=4)
    return act
def create_artists() -> list[dict[str,str]]:
    artists = []
    for i in range(1,70):
        name = fake.name()
        birthdate = fake.date_of_birth(minimum_age=18, maximum_age=60).strftime("%Y-%m-%d")
        nickname = fake.user_name()
        insta=fake.user_name()
        web=fake.url()
        info = {
            'Act_ID':i,
            'Artist_Name': name,
            'Artist_StageName': nickname,
            'DateOfBirth': birthdate,
            'Website' : web,
            'Instagram_Profile':insta
        }
        artists.append(info)
    #json.dump(artists, open('data/artists.json', 'w'), indent=4)
    return artists
    
def create_bands() -> list[dict[str,str]]:
    bands = []
    for i in range(1,19):
        band_name = fake.color_name() + ' ' + fake.word()
        foundation_date = fake.date_of_birth(minimum_age=1, maximum_age=20).strftime("%Y-%m-%d")
        website = fake.url()
        info = {
            'Act_ID':i+len(artists),
            'Band_Name': band_name,
            'Formation_Date': foundation_date,
            'Website': website,
            'Instagram_Profile':fake.user_name(),
        }
        bands.append(info)
    #json.dump(bands, open('data/bands.json', 'w'), indent=4)
    return bands
        
        
def create_band_members(bands, artists) -> list[dict[str,str]]:
    band_members = []
    for band_id, bands in enumerate(bands):
        band_id = band_id + 1
        band_members_count = random.randint(3,5)
        band_members_artists = random.sample(artists, band_members_count)
        for artist in band_members_artists:
            artist_id = artists.index(artist) + 1
            joindate=fake.date()
            info = {
                'Band_ID': band_id,
                'Artist_ID': artist_id,
                'JoinDate':joindate
            }
            band_members.append(info)
    # json.dump(band_members, open('data/band_members.json', 'w'), indent=4)
    return band_members

def create_act_genres(acts,genres) -> list[dict[str,str]] :
    act_genres = []
    for act_id, act in enumerate(acts):
        act_id = act_id + 1
        act_genres_count = random.randint(1,3)
        act_genres_list = random.sample(genres, act_genres_count)
        for genre in act_genres_list:
            info = {
                'Act_ID': act_id,
                'Genre_ID': random.randint(1, len(genres)),
            }
            act_genres.append(info)
    return act_genres
   
    
def create_locations() -> list[dict[str,str]]:
    locations = []
    for _ in range(10):
        address = fake.address()
        latitude = fake.latitude()
        longitude = fake.longitude()
        city = fake.city()
        country = fake.country()
        continent = random.choice(['europe','asia','north_america','south_america','africa','oceania'])
        info = {
            'Street_Address' : address,
            'City' : city,
            'Country' : country,
            'Continent' : continent,
            'Latitude' : str(latitude),
            'Longitude' : str(longitude)
        }
        locations.append(info)
    #json.dump(locations, open('data/locations.json', 'w'), indent=4)
    return locations
    
def create_Visitors() -> list[dict[str,str]]:
    users=[]
    for _ in range(500):
        user_first_name = fake.first_name()
        user_last_name = fake.last_name()
        tel=fake.phone_number()   
        info = {
            'FirstName': user_first_name,
            'LastName': user_last_name,
            'Age':random.randint(18,65),
            'Telephone_Number': tel
        }
        users.append(info)
    #json.dump(users, open('data/users.json', 'w'), indent=4)    
    return users
    
def create_stages() -> list[dict[str,str]]:
    locations = json.load(open('data/locations.json'))
    venues = []
    for location in locations:
        for _ in range(3):
            venue_name = fake.company()
            venue_description=fake.catch_phrase()
            venue_capacity = random.randint(100,300)
            venue_inforomation = fake.catch_phrase()
            info = {
                'Stage_Name': venue_name,
                'Stage_Description':venue_description,
                'MaxCapacity': venue_capacity,
                'Technical_Information': venue_description
            }
            venues.append(info)
    #json.dump(venues, open('data/venues.json', 'w'), indent=4)
    return venues

genres = ['rock','pop','jazz','blues','metal','country','rap','hip-hop','classical']
subgenres = {
    'rock': ['alternative','indie','punk','hardcore'],
    'pop': ['synthpop','dance-pop','electropop'],
    'jazz': ['smooth','fusion','free'],
    'blues': ['delta','chicago','electric'],
    'metal': ['heavy','thrash','death'],
    'country': ['bluegrass','country-rock'],
    'rap': ['gangsta','conscious','trap'],
    'hip-hop': ['old-school','new-school'],
    'classical': ['baroque','romantic','modern']
}

staff = create_staff()
genres = [
    {
        'Genre_Name': genre
    }  for genre in genres
]
new_subgenres = {}
for genre in subgenres:
    for subgenre in subgenres[genre]:
        new_subgenres[subgenre] = genre
subgenres = [
    {
        'Subgenre_Name': subgenre,
        'Genre_Name': new_subgenres[subgenre]
    } for subgenre in new_subgenres
]

artists = create_artists()
bands = create_bands()
act=create_act(artists,bands)
band_members=create_band_members(bands,artists)
actgenres=create_act_genres(act,genres)
locations=create_locations()
visitors=create_Visitors()
stages=create_stages()

# staff = json.load(open('data/staff.json'))
# artists = json.load(open('data/artists.json'))
# bands = json.load(open('data/bands.json'))
# band_members = json.load(open('data/band_members.json'))
# artist_genres = json.load(open('data/artist_genres.json'))
# locations = json.load(open('data/locations.json'))
# users = json.load(open('data/users.json'))
# venues = json.load(open('data/venues.json'))

f = open('festival-data.sql','w')

def insert_data(table_name:str, data:list[dict]):
    for item in data:
        query = f'INSERT INTO {table_name} ('
        query += ', '.join(item.keys()) + ') VALUES ('
        query += ', '.join([f"'{str(value)}'" for value in item.values()]) + ');\n'
        f.write(query)
        
        
insert_data('Act',act)
insert_data('Genre', genres)
insert_data('Subgenre', subgenres)
insert_data('Staff', staff)
insert_data('Artist', artists)
insert_data('Band', bands)
insert_data('ArtistBand', band_members)
insert_data('ActGenre',actgenres )
insert_data('Location', locations)
insert_data('Visitor', visitors) 
insert_data('Stage', stages)

def create_festivals() -> list[dict[str,str]]:
    festivals = []
    start_year = 2018
    festival_count = 10
    for i in range(festival_count):
        year = start_year + i
        start_date = fake.date_between(start_date=datetime.date(year, 1, 1), end_date=datetime.date(year, 12, 31))
        duration = random.randint(8,10)
        end_date = start_date + datetime.timedelta(days=duration)
        festival = {
            'Festival_Year': year,
            'Location_ID': i+1,
            'start_date': start_date.strftime("%Y-%m-%d"),
            'end_date' : end_date.strftime("%Y-%m-%d"),
        }
        festivals.append(festival)
    #json.dump(festivals, open('data/festivals.json', 'w'), indent=4)
    return festivals

def create_events(festivals) -> list[dict[str,str]]:
    events = []
    for festival in festivals:
        festival_start_date = festival['start_date']
        festival_end_date = festival['end_date']
        festival_start_date = datetime.datetime.strptime(festival_start_date, "%Y-%m-%d")
        festival_end_date = datetime.datetime.strptime(festival_end_date, "%Y-%m-%d")
        festival_duration = (festival_end_date - festival_start_date).days
        events_count = 8
        event_days = random.sample(range(festival_duration), events_count)
        event_days.sort()
        festival_location = festival['Location_ID']
        venues_in_festival = []
        for venue in stages:
                venues_in_festival.append(stages.index(venue)+1)
        for day in event_days:
            event_date = festival_start_date + datetime.timedelta(days=day)
            event_duration_hours = random.randint(9, 12)
            event_start_time = random.randint(0, 23-event_duration_hours)
            event_end_time = event_start_time + event_duration_hours
            event = {
                'Festival_Year': festival['Festival_Year'],
                'Stage_ID' : random.choice(venues_in_festival),
                'start_time': event_date.strftime("%Y-%m-%d") + ' ' + str(event_start_time) + ':00:00',
                'end_time': event_date.strftime("%Y-%m-%d") + ' ' + str(event_end_time) + ':00:00',
            }
            events.append(event)
    #json.dump(events, open('data/events.json', 'w'), indent=4)
    return events
    
def create_staff_assigment(events) -> list[dict[str,str]]:
    event_staff = []
    for event in events:
        event_id = events.index(event) + 1
        venue_id = event['Stage_ID']
        venue = stages[venue_id-1]
        capacity = venue['MaxCapacity']
        security_count = round(capacity * 0.06)
        helper_count = round(capacity * 0.03)
        technical_count = round(capacity * 0.05)
        securities = []
        while len(securities) < security_count:
            staff_member = random.choice(staff)
            if staff_member['Staff_Role'] in ['security'] and staff_member not in securities:
                securities.append(staff_member)
        helpers = []
        while len(helpers) < helper_count:
            staff_member = random.choice(staff)
            if staff_member['Staff_Role'] in ['support'] and staff_member not in helpers:
                helpers.append(staff_member)
        technicals = []
        while len(technicals) < technical_count:
            staff_member = random.choice(staff)
            if staff_member['Staff_Role'] in ['technician'] and staff_member not in technicals:
                technicals.append(staff_member)
        all_staff = securities + helpers + technicals
        for staff_member in all_staff:
            event_staff.append({
                'Staff_ID': staff.index(staff_member) + 1,
                'Event_ID': event_id
            })
    #json.dump(event_staff, open('data/event_staff.json', 'w'), indent=4)
    return event_staff
performance_types = ['warmup','headline','Special Guest']
artist_participations = {}
def create_performances(events, artists, bands) -> list[dict[str,str]]:
    performances:list[dict[str,str]] = []
    for event in events:
        act_id=random.randint(1,70)
        event_id = events.index(event) + 1
        performance_count = 3 
        performances_durations_minutes = [random.randint(60, 150) for _ in range(performance_count)]
        intervals = [random.randint(5, 25) for _ in range(performance_count)]
        event_start_time = datetime.datetime.strptime(event['start_time'], "%Y-%m-%d %H:%M:%S")
        for i in range(performance_count):
            performance_start_time = event_start_time + datetime.timedelta(minutes=sum(performances_durations_minutes[:i]) + sum(intervals[:i]))
            performance_end_time = performance_start_time + datetime.timedelta(minutes=performances_durations_minutes[i])
            performance_type = random.choice(performance_types)
            info = {
                'Event_ID': event_id,
                'Act_ID': act_id,
                'performance_type': performance_type,
                'StartTime': performance_start_time.strftime("%Y-%m-%d %H:%M:%S"),
                'EndTime': performance_end_time.strftime("%Y-%m-%d %H:%M:%S"),
            }
            performances.append(info)
    #json.dump(performances, open('data/performances.json', 'w'), indent=4)
    # print(artist_participations)
    return performances      
def create_tickets(events) -> list[list[str]]:
    tickets = []     
    for event in events:
        event_id = events.index(event) + 1
        venue_id = event['Stage_ID']
        venue = stages[venue_id-1]
        capacity = venue['MaxCapacity']
        ticket_count = random.randint(25, capacity)
        _users = random.sample(range(1,len(visitors)+1), ticket_count)
        ticket_types = ['regular'] * math.ceil(ticket_count * 0.85) + ['vip'] * math.ceil(ticket_count * 0.10) 
        ticket_types += ['backstage'] * (ticket_count - len(ticket_types))
        random.shuffle(ticket_types)
        for i in range(ticket_count):
            user = _users[i]
            ticket_type = ticket_types[i]
            sold_as = random.choice(['cash','credit_card','debit_card'])
            info = {
                'Visitor_ID':user,
                'Event_ID':event_id,
                'Category':ticket_type,
                'Method_Of_Payment':sold_as,
                'EANCode':fake.ean13(),
                'Price' : random.randint(50, 200),
                
            }
            tickets.append(info)
    #json.dump(buy_tickets, open('data/buy_tickets.json', 'w'), indent=4)
    return tickets

def buy_and_use(tickets) -> list[list[str]]:
    ticket_info = []
    for ticket in random.sample(tickets, int(len(tickets) * 0.8)):
        ticket_id = tickets.index(ticket) + 1
        used = int(random.random()<0.6)
        info = {
            'Ticket_ID': ticket_id,
            'Bought': 1,
            'Used': used
        }
        f.write(f'UPDATE Ticket SET Bought = 1, Used = {int(used)}, PurchaseDate = CURDATE() WHERE Ticket_ID = {ticket_id};\n')
        ticket_info.append(info)
        
    return ticket_info

festivals=create_festivals()
events=create_events(festivals)
event_staff=create_staff_assigment(events)
performances=create_performances(events, artists, bands)
tickets = create_tickets(events)

insert_data('Festival', festivals)

insert_data('Event', events)
insert_data('StaffAssignment', event_staff)
insert_data('Performance', performances)
insert_data('Ticket', tickets)
ticket_info = buy_and_use(tickets)

# lets do some ratings
def create_ratings(tickets):
    ratings = []
    for used_ticket in tickets:
        event_id = used_ticket['Event_ID']
        performances_in_that_event = [
            performance for performance in performances if performance['Event_ID'] == event_id
        ]
        for performance in performances_in_that_event:
            if random.random() < 0.5:
                continue
            performance_id = performances.index(performance) + 1
            info = {
                'Visitor_ID': used_ticket['Visitor_ID'],
                'Performance_ID': performance_id,
                'Criteria':random.choice(['artist_performance','sound_lighting ','appearance','organization','overall_experience']),
                'Score':random.randint(1,5)
            }
            ratings.append(info)
    return ratings

ratings = create_ratings(tickets)
insert_data('Rating', ratings)

def create_resaleQueue(tickets):
    requests = []
    for ticket in random.sample(tickets, int(len(tickets) * 0.2)):
        ticket_id = tickets.index(ticket) + 1
        info = {
                'Visitor_ID': ticket['Visitor_ID'],
                'Ticket_ID': ticket_id,
                'Event_ID': ticket['Event_ID'],
                'Category': ticket['Category'],
                'RequestType': random.choice(['buy','sell']),
                'RequestDate': fake.date(),
                'Request_Status': random.choice(['pending','completed','cancelled']) 
            }
        requests.append(info)
    return requests
request = create_resaleQueue(tickets)
insert_data('ResaleRequest', request)