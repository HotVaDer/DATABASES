EXEC sp_RequestTrip
  @User_ID = 123,
  @Service_Type_Name = 'Standard',
  @Start_Point = geography::Point(35.1676, 33.3736, 4326),
  @End_Point   = geography::Point(35.1856, 33.3823, 4326);