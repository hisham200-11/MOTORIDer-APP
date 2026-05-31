// =======================
// UPDATE RIDER
// =======================
function updateRider() {
    const name = document.getElementById("name").value.trim();
    const username = document.getElementById("username").value.trim();
    const contactNo = document.getElementById("contactNo").value.trim();
    const gcash = document.getElementById("gcash").value.trim();
    const status = document.getElementById("status");

    const params = new URLSearchParams();
    params.append("role", "rider");
    params.append("name", name);
    params.append("username", username);
    params.append("gcash", gcash);
    params.append("contactNo", contactNo);

    fetch("update.php", {
        method: "POST",
        body: params
    })
    .then(response => response.text())
    .then(data => {
        showStatus(status, data);
    })
    .catch(error => {
        showError(status, error);
    });
}


// =======================
// UPDATE DRIVER (UPDATED)
// =======================
function updateDriver() {
    const name = document.getElementById("name").value.trim();
    const username = document.getElementById("username").value.trim();
    const contactNo = document.getElementById("contactNo").value.trim();

    const vehicle_type = document.getElementById("vehicle_type")?.value.trim() || "";
    const vehicle_model = document.getElementById("vehicle_model")?.value.trim() || "";
    const vehicle_color = document.getElementById("vehicle_color")?.value.trim() || "";
    const vehicle_plate = document.getElementById("vehicle_plate")?.value.trim() || "";

    const driver_license = document.getElementById("driver_license")?.value.trim() || "";
    const license_expiry = document.getElementById("license_expiry")?.value || "";

    const gcash = document.getElementById("gcash")?.value.trim() || "";

    const status = document.getElementById("status");

    // Basic validation
    if (!name || !username || !contactNo) {
        status.style.display = "block";
        status.className = "status error";
        status.innerText = "❌ Please fill in required fields.";
        return;
    }

    const params = new URLSearchParams();
    params.append("role", "driver");
    params.append("name", name);
    params.append("username", username);
    params.append("contactNo", contactNo);

    params.append("vehicle_type", vehicle_type);
    params.append("vehicle_model", vehicle_model);
    params.append("vehicle_color", vehicle_color);
    params.append("vehicle_plate", vehicle_plate);

    params.append("driver_license", driver_license);
    params.append("license_expiry", license_expiry);

    params.append("gcash", gcash);

    fetch("update.php", {
        method: "POST",
        body: params
    })
    .then(response => response.text())
    .then(data => {
        showStatus(status, data);
    })
    .catch(error => {
        showError(status, error);
    });
}


// =======================
// STATUS HANDLERS
// =======================
function showStatus(status, data) {
    status.style.display = "block";

    if (data.toLowerCase().includes("success")) {
        status.className = "status success";
        status.innerText = "✅ Changes saved successfully!";
    } else {
        status.className = "status error";
        status.innerText = "❌ Failed to save changes.";
    }
}

function showError(status, error) {
    status.style.display = "block";
    status.className = "status error";
    status.innerText = "⚠️ Something went wrong.";
    console.error(error);
}


// =======================
// INPUT FILTER (NUMBERS ONLY)
// =======================
const contactInput = document.getElementById("contactNo");
if (contactInput) {
    contactInput.addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
}

// =======================
// GCASH TOP-UP
// =======================
function topUpGcash() {
    const amountInput = document.getElementById("topupAmount");
    const status = document.getElementById("topupStatus");
    const amount = parseFloat(amountInput.value);

    if (!amount || amount <= 0) {
        status.style.display = "block";
        status.className = "status error";
        status.innerText = "❌ Please enter a valid amount.";
        return;
    }

    const params = new URLSearchParams();
    params.append("amount", amount);

    fetch("topupGcash.php", {
        method: "POST",
        body: params
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            status.style.display = "block";
            status.className = "status success";
            status.innerText = "✅ Balance updated successfully!";

            // Update the displayed balance without reload
            document.getElementById("gcashBalanceDisplay").textContent =
                "₱" + parseFloat(data.new_balance).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            amountInput.value = "";
        } else {
            status.style.display = "block";
            status.className = "status error";
            status.innerText = "❌ " + (data.error || "Top-up failed.");
        }
    })
    .catch(error => {
        showError(status, error);
    });
}