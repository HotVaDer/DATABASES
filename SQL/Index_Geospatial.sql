CREATE SPATIAL INDEX SInd_VehicleLocation_GeoPoint
ON Vehicle_Location(GeoPoint)
USING GEOGRAPHY_AUTO_GRID;
