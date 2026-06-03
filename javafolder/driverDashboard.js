// ============================================
// LEAFLET MAP INITIALIZATION
// ============================================
let map = null;
let pickupMarker = null;
let dropoffMarker = null;

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Leaflet map
    map = L.map('map').setView([14.5995, 120.9842], 13);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);
});

// ============================================
// STATUS TOGGLE
// ============================================
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("statusToggle");
    const statusText = document.getElementById("statusText");
    const onlineContent = document.getElementById("onlineContent");
    const offlineContent = document.getElementById("offlineContent");

    function updateUI(isOnline) {
        if (isOnline) {
            statusText.innerText = "Online";
            toggle.checked = true;
            onlineContent.style.display = "block";
            offlineContent.style.display = "none";
        } else {
            statusText.innerText = "Offline";
            toggle.checked = false;
            onlineContent.style.display = "none";
            offlineContent.style.display = "block";
            clearMapRoute();
        }
    }

    updateUI(toggle.checked);

    toggle.addEventListener("change", function() {
        fetch("togglestatus.php", { method: "POST" })
        .then(res => res.text())
        .then(data => updateUI(data.trim() === "ONLINE"));
    });
});

/**
 * REFRESH LOGIC
 * Runs every 3 seconds to keep the UI in sync with the database
 */
function refreshDashboard() {
    loadActiveRide();
    loadPendingRequests();
    updateDriverStats(); // Updates the Earnings and Ride Count cards
}

// Function to update the Stats Cards at the top
function updateDriverStats() {
    fetch("getDriverStats.php") // You will need to create this simple PHP file
    .then(res => res.json())
    .then(data => {
        // Ensure these IDs match your HTML
        const rideDisplay = document.getElementById("totalRidesDisplay");
        const earningsDisplay = document.getElementById("totalEarningsDisplay");

        if (rideDisplay) rideDisplay.innerText = data.total_rides;
        if (earningsDisplay) earningsDisplay.innerText = "₱" + data.total_earnings;
    })
    .catch(err => console.error("Error updating stats:", err));
}

function loadActiveRide() {
    fetch("getActiveRides.php")
    .then(res => res.text())
    .then(html => {
        document.getElementById("activeRide").innerHTML = html;
        attachRideButtons(); 
        togglePendingSection();
        
        // Update map with ride coordinates from hidden inputs
        const activeRideElement = document.querySelector('.active-ride');
        if (activeRideElement) {
            const pickupLatInput = activeRideElement.querySelector('.ride-pickup-lat');
            const pickupLngInput = activeRideElement.querySelector('.ride-pickup-lng');
            const dropoffLatInput = activeRideElement.querySelector('.ride-dropoff-lat');
            const dropoffLngInput = activeRideElement.querySelector('.ride-dropoff-lng');
            
            if (pickupLatInput && pickupLngInput && dropoffLatInput && dropoffLngInput) {
                const pickupLat = parseFloat(pickupLatInput.value);
                const pickupLng = parseFloat(pickupLngInput.value);
                const dropoffLat = parseFloat(dropoffLatInput.value);
                const dropoffLng = parseFloat(dropoffLngInput.value);
                
                // Only display if coordinates are valid
                if (pickupLat && pickupLng && dropoffLat && dropoffLng) {
                    displayRideOnMap(pickupLat, pickupLng, dropoffLat, dropoffLng);
                } else {
                    clearMapRoute();
                }
            } else {
                clearMapRoute();
            }
        } else {
            clearMapRoute();
        }
    });
}

function loadPendingRequests() {
    fetch("getPendingRides.php")
    .then(res => res.text())
    .then(html => {
        document.getElementById("rideRequests").innerHTML = html;
        attachAcceptDeclineEvents();
    });
}

function togglePendingSection() {
    const activeRide = document.querySelector("#activeRide .active-ride");
    const pendingSection = document.getElementById("pendingRequestsSection");
    
    if (activeRide) {
        pendingSection.style.display = "none";
    } else {
        pendingSection.style.display = "block";
    }
}

// ATTACH BUTTON LISTENERS
function attachRideButtons() {
    // 🚀 Start ride button
    document.querySelectorAll(".start-btn").forEach(btn => {
        btn.onclick = function() {
            const rideId = this.dataset.id;
            fetch("startRide.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: `id=${rideId}`
            }).then(() => refreshDashboard());
        };
    });

    // ✅ Complete/End ride button
        document.querySelectorAll(".end-btn").forEach(btn => {
        btn.onclick = function() {
            const rideId = this.dataset.id;
            if(confirm("Are you sure you want to complete this ride?")) {
                fetch("endRide.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                    body: `id=${rideId}`
                })
                .then(res => res.json()) // 👈 changed from res.text() to res.json()
                .then(data => {
                    if(data.status === "success") {
                        clearMapRoute(); // Clear route when ride ends
                        showReceipt(data); // 👈 show receipt instead of refreshDashboard
                    } else {
                        alert("Error completing ride.");
                    }
                });
            }
        };
    });
}

function attachAcceptDeclineEvents() {
    document.querySelectorAll(".accept-btn").forEach(btn => {
        btn.onclick = function() {
            const rideId = this.dataset.id;
            fetch("updateRideStatus.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: `id=${rideId}&status=accepted`
            }).then(() => refreshDashboard());
        };
    });

    document.querySelectorAll(".decline-btn").forEach(btn => {
        btn.onclick = function() {
            const rideId = this.dataset.id;
            fetch("updateRideStatus.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: `id=${rideId}&status=declined`
            }).then(() => refreshDashboard());
        };
    });
}

// ============================================
// MAP DISPLAY WITH COORDINATES
// ============================================
let routeLine = null;

function displayRideOnMap(pickupLat, pickupLng, dropoffLat, dropoffLng) {
    // Clear old markers and route
    if (pickupMarker) map.removeLayer(pickupMarker);
    if (dropoffMarker) map.removeLayer(dropoffMarker);
    if (routeLine) map.removeLayer(routeLine);
    
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
    }).addTo(map).bindPopup('Pickup Location');
    
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
    }).addTo(map).bindPopup('Dropoff Location');
    
    // Fetch route from OSRM
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${pickupLng},${pickupLat};${dropoffLng},${dropoffLat}?overview=full&geometries=geojson`;
    
    fetch(osrmUrl)
        .then(res => res.json())
        .then(data => {
            if (data.routes && data.routes[0]) {
                const coordinates = data.routes[0].geometry.coordinates;
                // Draw route line (blue polyline)
                routeLine = L.polyline(
                    coordinates.map(coord => [coord[1], coord[0]]),
                    { color: '#3b82f6', weight: 5, opacity: 0.7 }
                ).addTo(map);
            }
        })
        .catch(err => console.error('OSRM routing error:', err));
    
    // Fit map to both markers
    const bounds = L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]);
    map.fitBounds(bounds, { padding: [50, 50] });
}

function clearMapRoute() {
    if (pickupMarker) {
        map.removeLayer(pickupMarker);
        pickupMarker = null;
    }
    if (dropoffMarker) {
        map.removeLayer(dropoffMarker);
        dropoffMarker = null;
    }
    if (routeLine) {
        map.removeLayer(routeLine);
        routeLine = null;
    }
}

// ============================================

// RECEIPT MODAL
function showReceipt(data) {
    const existing = document.getElementById("receiptModal");
    if (existing) existing.remove();

    const modal = document.createElement("div");
    modal.id = "receiptModal";
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex;
        align-items: center; justify-content: center; z-index: 9999;
    `;

    modal.innerHTML = `
        <div style="background: white; border-radius: 16px; padding: 30px; width: 90%; max-width: 380px;">
            <h2 style="text-align:center; margin-bottom: 5px;">🧾 Ride Receipt</h2>
            <p style="text-align:center; color:#888; margin-bottom: 20px;">Ride Completed</p>

            <div style="border-top: 1px solid #eee; padding-top: 15px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#666;">Passenger</span>
                    <strong>${data.rider_name}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#666;">Pickup Location</span>
                    <strong>${data.pickup}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#666;">Dropoff Location</span>
                    <strong>${data.dropoff}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#666;">Distance</span>
                    <strong>${data.distance} km</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#666;">Payment Method</span>
                    <strong>${data.payment_method}</strong>
                </div>
            </div>

            <!-- FARE BREAKDOWN -->
            <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#666;">Gross Fare</span>
                    <strong>₱${parseFloat(data.fare).toFixed(2)}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                    <span style="color:#e74c3c;">Platform Fee (5%)</span>
                    <strong style="color:#e74c3c;">- ₱${parseFloat(data.tax).toFixed(2)}</strong>
                </div>
            </div>

            <!-- TOTAL DRIVER EARNINGS -->
            <div style="border-top: 2px solid #9333ea; margin-top: 15px; padding-top: 15px;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="font-size: 18px; font-weight: bold;">Your Earnings</span>
                    <span style="font-size: 22px; font-weight: bold; color: #9333ea;">₱${parseFloat(data.driver_earnings).toFixed(2)}</span>
                </div>
            </div>

            <button onclick="document.getElementById('receiptModal').remove(); refreshDashboard();"
                style="width:100%; margin-top: 20px; padding: 12px;
                       background: #9333ea; color: white; border: none;
                       border-radius: 10px; font-size: 16px; cursor: pointer;">
                Done
            </button>
        </div>
    `;

    document.body.appendChild(modal);
}

// ============================================
// AUTO-REFRESH EVERY 3 SECONDS
// ============================================
setInterval(refreshDashboard, 3000);
refreshDashboard();