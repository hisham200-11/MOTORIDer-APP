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
        }
    }

    // Read initial state from PHP instead of fetching getstatus.php
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

// Auto-refresh interval
setInterval(refreshDashboard, 3000);
refreshDashboard();

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