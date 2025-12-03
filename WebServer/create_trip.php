<?php
session_start();

// SECURITY CHECK
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$serviceID = $_GET['service'] ?? null;
if (!$serviceID) {
    die("Invalid service selected.");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Select Pickup & Destination</title>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    #map {
        width: 100%;
        height: 100vh;
    }

    /* Pickup search */
    .search-box {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 600px;
        z-index: 10;
    }

    .search-box input {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        font-size: 16px;
    }

    /* Destination search */
    .destination-box {
        position: absolute;
        top: 70px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 600px;
        z-index: 10;
    }

    .destination-box input {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        font-size: 16px;
    }

    /* Confirm button */
    .confirm-btn {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        padding: 14px 32px;
        background: black;
        color: white;
        font-size: 18px;
        border-radius: 10px;
        text-decoration: none;
        cursor: pointer;
        z-index: 10;
        border: none;
    }
</style>

<!-- GOOGLE MAPS API (replace YOUR_API_KEY) -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC7C5IT2UQ2JF_Lt8SknzI57pNShL86Sdk&libraries=places&callback=initMap" async defer></script>

<script>
let map, pickupMarker, destMarker;

function initMap() {
    const cyprusCenter = { lat: 35.1264, lng: 33.4299 }; // Cyprus center

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 11,
        center: cyprusCenter,
    });

    // Create two markers
    pickupMarker = new google.maps.Marker({
        map: map,
        draggable: true,
        label: "A"
    });

    destMarker = new google.maps.Marker({
        map: map,
        draggable: true,
        label: "B"
    });

    // AUTOCOMPLETE PICKUP
    const pickupInput = document.getElementById("pickup");
    const pickupAuto = new google.maps.places.Autocomplete(pickupInput, {
        componentRestrictions: { country: "cy" }
    });
    pickupAuto.bindTo("bounds", map);

    pickupAuto.addListener("place_changed", () => {
        const place = pickupAuto.getPlace();
        if (!place.geometry) return;

        map.panTo(place.geometry.location);
        pickupMarker.setPosition(place.geometry.location);

        document.getElementById("start_lat").value = place.geometry.location.lat();
        document.getElementById("start_lon").value = place.geometry.location.lng();
    });

    // AUTOCOMPLETE DESTINATION
    const destInput = document.getElementById("destination");
    const destAuto = new google.maps.places.Autocomplete(destInput, {
        componentRestrictions: { country: "cy" }
    });
    destAuto.bindTo("bounds", map);

    destAuto.addListener("place_changed", () => {
        const place = destAuto.getPlace();
        if (!place.geometry) return;

        map.panTo(place.geometry.location);
        destMarker.setPosition(place.geometry.location);

        document.getElementById("end_lat").value = place.geometry.location.lat();
        document.getElementById("end_lon").value = place.geometry.location.lng();
    });

    // DRAG EVENTS (Marker → coords)
    pickupMarker.addListener("dragend", () => {
        const pos = pickupMarker.getPosition();
        document.getElementById("start_lat").value = pos.lat();
        document.getElementById("start_lon").value = pos.lng();
    });

    destMarker.addListener("dragend", () => {
        const pos = destMarker.getPosition();
        document.getElementById("end_lat").value = pos.lat();
        document.getElementById("end_lon").value = pos.lng();
    });
}
</script>
</head>

<body onload="initMap()">

<!-- PICKUP SEARCH -->
<div class="search-box">
    <input id="pickup" type="text" placeholder="Enter pickup location">
</div>

<!-- DESTINATION SEARCH -->
<div class="destination-box">
    <input id="destination" type="text" placeholder="Enter destination">
</div>

<!-- MAP -->
<div id="map"></div>

<!-- FORM -->
<form action="create_trip_process.php" method="POST">

    <input type="hidden" name="service_id" value="<?= htmlspecialchars($serviceID) ?>">

    <input type="hidden" id="start_lat" name="start_lat">
    <input type="hidden" id="start_lon" name="start_lon">

    <input type="hidden" id="end_lat" name="end_lat">
    <input type="hidden" id="end_lon" name="end_lon">

    <button type="submit" class="confirm-btn">Confirm Trip</button>
</form>

</body>
</html>
