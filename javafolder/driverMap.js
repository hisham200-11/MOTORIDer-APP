// ============================================
// DRIVER MAP & LOCATION TRACKING
// ============================================
// Initialize map only if we're on the driver home page

let driverMap = null;
let driverMarker = null;
let routeLine = null;
let pickupMarker = null;
let dropoffMarker = null;
let driverLocation = null;
let currentRideData = null;
let locationUpdateInterval = null;
let watchPositionId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Guard all map code - only run if map div exists
    if (!document.getElementById('driverMap')) {
        return; // Exit early if not on Driverhome.php
    }

    // ============================================
    // MAP INITIALIZATION
    // ============================================
    driverMap = L.map('driverMap').setView([14.58426405648777, 120.98132780256736], 15);
    
    // ============================================
    // LEAFLET TILE LAYER - Jawg Lagoon
    // ============================================
    L.tileLayer('https://tile.jawg.io/jawg-lagoon/{z}/{x}/{y}{r}.png?access-token={accessToken}', {
        attribution: '<a href="https://jawg.io" title="Tiles Courtesy of Jawg Maps" target="_blank">&copy; <b>Jawg</b>Maps</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        minZoom: 0,
        maxZoom: 22,
        accessToken: 'BIsIvEiFSGceqy5PcX2nMm4fYD41sxDbLzRYrJVN7Uzl5A4JohiAMb53ffZlArFm'
    }).addTo(driverMap);

    // ============================================
    // GET DRIVER CURRENT LOCATION & START TRACKING
    // ============================================
    initializeDriverLocation();
});

function initializeDriverLocation() {
    if (navigator.geolocation) {
        // Get initial position
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                driverLocation = { lat, lng };
                
                driverMap.setView([lat, lng], 15);
                
                // Place driver marker
                if (driverMarker) {
                    driverMarker.setLatLng([lat, lng]);
                } else {
                    driverMarker = L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(driverMap).bindPopup('Your Current Location');
                }
                
                // Update location in database
                updateLocationInDatabase(lat, lng);
                
                // Watch position continuously
                watchPositionId = navigator.geolocation.watchPosition(
                    function(position) {
                        const newLat = position.coords.latitude;
                        const newLng = position.coords.longitude;
                        driverLocation = { lat: newLat, lng: newLng };
                        
                        // Update marker
                        if (driverMarker) {
                            driverMarker.setLatLng([newLat, newLng]);
                        }
                        
                        // Update location in database
                        updateLocationInDatabase(newLat, newLng);
                    },
                    function(error) {
                        console.warn('Error watching position:', error);
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 10000
                    }
                );
            },
            function(error) {
                console.warn('Geolocation denied. Using Philippines default view.', error);
            }
        );
    }
}

function updateLocationInDatabase(lat, lng) {
    fetch('updateDriverLocation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `lat=${lat}&lng=${lng}`
    }).catch(err => console.error('Error updating driver location:', err));
}

// ============================================
// ACCEPT RIDE - SHOW PIN-TO-PIN ROUTE
// ============================================
function acceptRideWithMap(rideId) {
    // Fetch ride details (pickup & dropoff locations)
    fetch('getRideDetails.php?id=' + rideId)
        .then(res => res.json())
        .then(data => {
            currentRideData = data;
            
            const pickupLat = parseFloat(data.pickup_lat);
            const pickupLng = parseFloat(data.pickup_lng);
            const dropoffLat = parseFloat(data.dropoff_lat);
            const dropoffLng = parseFloat(data.dropoff_lng);
            
            // Clear existing route
            if (routeLine) {
                driverMap.removeLayer(routeLine);
            }
            if (pickupMarker) {
                driverMap.removeLayer(pickupMarker);
            }
            if (dropoffMarker) {
                driverMap.removeLayer(dropoffMarker);
            }
            
            // Add pickup marker (green)
            pickupMarker = L.marker([pickupLat, pickupLng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(driverMap).bindPopup(`<strong>Pickup</strong><br>${data.pickup_address}`);
            
            // Add dropoff marker (red)
            dropoffMarker = L.marker([dropoffLat, dropoffLng], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(driverMap).bindPopup(`<strong>Dropoff</strong><br>${data.dropoff_address}`);
            
            // Draw route line from driver -> pickup -> dropoff
            if (driverLocation) {
                const routePoints = [
                    [driverLocation.lat, driverLocation.lng],
                    [pickupLat, pickupLng],
                    [dropoffLat, dropoffLng]
                ];
                
                routeLine = L.polyline(routePoints, {
                    color: '#9333ea',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '5, 5'
                }).addTo(driverMap);
            }
            
            // Fit map to show all markers
            const group = new L.featureGroup([driverMarker, pickupMarker, dropoffMarker]);
            driverMap.fitBounds(group.getBounds().pad(0.1));
            
            // Actually accept the ride
            fetch('updateRideStatus.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${rideId}&status=accepted`
            })
            .then(res => res.text())
            .then(() => {
                console.log('Ride accepted');
                // Refresh dashboard
                refreshDashboard();
            })
            .catch(err => console.error('Error accepting ride:', err));
        })
        .catch(err => console.error('Error fetching ride details:', err));
}

// ============================================
// CLEAR MAP ROUTE
// ============================================
function clearMapRoute() {
    if (routeLine) {
        driverMap.removeLayer(routeLine);
        routeLine = null;
    }
    if (pickupMarker) {
        driverMap.removeLayer(pickupMarker);
        pickupMarker = null;
    }
    if (dropoffMarker) {
        driverMap.removeLayer(dropoffMarker);
        dropoffMarker = null;
    }
    currentRideData = null;
}

// ============================================
// STOP LOCATION TRACKING (when driver goes offline)
// ============================================
function stopLocationTracking() {
    if (watchPositionId !== null) {
        navigator.geolocation.clearWatch(watchPositionId);
        watchPositionId = null;
    }
}

// ============================================
// UPDATE ROUTE WHILE DRIVING TO PICKUP
// ============================================
function updateRouteToNextLocation() {
    if (!currentRideData || !driverLocation) return;
    
    const pickupLat = parseFloat(currentRideData.pickup_lat);
    const pickupLng = parseFloat(currentRideData.pickup_lng);
    const dropoffLat = parseFloat(currentRideData.dropoff_lat);
    const dropoffLng = parseFloat(currentRideData.dropoff_lng);
    
    // Redraw route from current driver location
    if (routeLine) {
        driverMap.removeLayer(routeLine);
    }
    
    const routePoints = [
        [driverLocation.lat, driverLocation.lng],
        [pickupLat, pickupLng],
        [dropoffLat, dropoffLng]
    ];
    
    routeLine = L.polyline(routePoints, {
        color: '#9333ea',
        weight: 4,
        opacity: 0.7,
        dashArray: '5, 5'
    }).addTo(driverMap);
}

// Keep route updated every 5 seconds while active ride
setInterval(function() {
    if (currentRideData) {
        updateRouteToNextLocation();
    }
}, 5000);
