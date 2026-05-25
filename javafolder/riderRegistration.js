// ============================================
// RIDER REGISTRATION & DRIVER REGISTRATION
// ============================================
// (These functions are used on registration pages and run independently)

function registerRider() {
    console.log("Registering rider...");

    let name = document.getElementById("reg_name").value;
    let contact = document.getElementById("reg_contact").value;
    let username = document.getElementById("reg_username").value;
    let password = document.getElementById("reg_password").value;
    let truepassword = document.getElementById("reg_truepassword").value;
    let gcash = document.getElementById("reg_gcash").value;

    if (!name || !contact || !username || !password || !truepassword || !gcash) {
        alert("Please fill all fields");
        return;
    }

    if (password !== truepassword) {
        alert("Passwords do not match!");
        return;
    }

    fetch("register.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: 
            "role=rider" +
            "&name=" + encodeURIComponent(name) +
            "&contact=" + encodeURIComponent(contact) +
            "&username=" + encodeURIComponent(username) +
            "&password=" + encodeURIComponent(password) + 
            "&gcash=" + encodeURIComponent(gcash)
    })
    .then(response => response.text())
    .then(data => {
        console.log("Response:", data);
        const responseEl = document.getElementById("response");
        if (responseEl) responseEl.innerHTML = data;

        let cleanData = data.trim();

        console.log("1. What JS is looking for : 'Registered successfully!'");
        console.log("2. What PHP actually sent : '" + cleanData + "'");
        console.log("3. Do they match exactly? : ", cleanData === "Registered successfully!");

        if (cleanData === "Registered successfully!" || cleanData.includes("Registered successfully!")) {
            console.log("4. SUCCESS! Clearing the form now...");
            document.getElementById("reg_name").value = "";
            document.getElementById("reg_contact").value = "";
            document.getElementById("reg_username").value = "";
            document.getElementById("reg_password").value = "";
            document.getElementById("reg_truepassword").value = "";
            document.getElementById("reg_gcash").value = "";
        } else {
            console.log("4. FAILED: The text did not match.");
        }
    })
    .catch(error => {
        console.error("Fetch error:", error);
    });
}

function registerDriver() {
    console.log("Registering driver...");

    let name = document.getElementById("driver_name").value;
    let contact = document.getElementById("driver_contact").value;
    let username = document.getElementById("driver_username").value;
    let password = document.getElementById("driver_password").value;
    let truepassword = document.getElementById("driver_truepassword").value;
    let model = document.getElementById("driver_model").value;
    let color = document.getElementById("driver_color").value;
    let plate_no = document.getElementById("driver_plate").value;
    let driver_license = document.getElementById("driver_license").value;
    let license_expiry = document.getElementById("license_expiry").value;
    let gcash = document.getElementById("gcash").value;
    let brand = document.getElementById("driver_brand").value;

    if (!name || !contact || !username || !password || !truepassword ||
        !model || !color || !plate_no || !driver_license || !license_expiry || !gcash || !brand) {
        alert("Please fill all fields!");
        return;
    }

    if (password !== truepassword) {
        alert("Passwords do not match!");
        return;
    }

    fetch("register.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "role=driver" +
            "&name=" + encodeURIComponent(name) +
            "&contact=" + encodeURIComponent(contact) +
            "&username=" + encodeURIComponent(username) +
            "&password=" + encodeURIComponent(password) +
            "&plate_no=" + encodeURIComponent(plate_no) +
            "&model=" + encodeURIComponent(model) +
            "&color=" + encodeURIComponent(color) +
            "&driver_license=" + encodeURIComponent(driver_license) +
            "&license_expiry=" + encodeURIComponent(license_expiry) +
            "&gcash=" + encodeURIComponent(gcash) +
            "&driver_brand=" + encodeURIComponent(brand)
    })
    .then(response => response.text())
    .then(data => {
        console.log("Response:", data);

        const responseEl = document.getElementById("response");
        if (responseEl) responseEl.innerHTML = data;

        let cleanData = data.trim();

        if (cleanData === "Registered successfully!" || cleanData.includes("Registered successfully!")) {
            console.log("Success! Clearing driver form...");
            document.getElementById("driver_name").value = "";
            document.getElementById("driver_contact").value = "";
            document.getElementById("driver_username").value = "";
            document.getElementById("driver_password").value = "";
            document.getElementById("driver_truepassword").value = "";
            document.getElementById("driver_plate").value = "";
            document.getElementById("driver_brand").value = "";
            document.getElementById("driver_model").value = "";
            document.getElementById("driver_color").value = "";
            document.getElementById("driver_license").value = "";
            document.getElementById("license_expiry").value = "";
            document.getElementById("gcash").value = "";
        }
    })
    .catch(error => {
        console.error("Fetch error:", error);
    });
}

// ============================================
// MAP & RIDE BOOKING FOR RIDERHOME.PHP
// ============================================
// Initialize map only if we're on the rider home page

document.addEventListener('DOMContentLoaded', function() {
    // Guard all map code - only run if Riderhome.php map div exists
    if (!document.getElementById('map')) {
        return; // Exit early if not on Riderhome.php
    }

    // ============================================
    // MAP VARIABLES & STATE (Declared once - NO DUPLICATES)
    // ============================================
    map = L.map('map').setView([14.58426405648777, 120.98132780256736], 15); // Default to Manila view until geolocation runs
    let userLocation = null;
    let pickupMarker = null;
    let dropoffMarker = null;
    let routeLine = null;
    let clickCounter = 0;
    let currentRouteMeta = null;
    let pollInterval = null;
    let isRideActive = false;

    // ============================================
    // LEAFLET TILE LAYER - OpenStreetMap
    // ============================================
    // OR clean minimal light theme
    L.tileLayer('https://tile.jawg.io/jawg-lagoon/{z}/{x}/{y}{r}.png?access-token={accessToken}', {
    attribution: '<a href="https://jawg.io" title="Tiles Courtesy of Jawg Maps" target="_blank">&copy; <b>Jawg</b>Maps</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    minZoom: 0,
    maxZoom: 22,
    accessToken: 'BIsIvEiFSGceqy5PcX2nMm4fYD41sxDbLzRYrJVN7Uzl5A4JohiAMb53ffZlArFm'
}).addTo(map);
    // ============================================
    // SESSION PERSISTENCE - CHECK FOR ACTIVE RIDE ON PAGE LOAD
    // ============================================
    window.addEventListener('load', function() {
      checkForActiveRide();
    });

    function checkForActiveRide() {
      fetch('checkRideStatus.php')
        .then(res => res.text())
        .then(html => {
          if (html.includes('No current ride')) {
            initializeMapForBooking();
            return;
          }
          
          isRideActive = true;
          hideBookingForm();
          displayRideStatus(html);
          startRidePolling();
        })
        .catch(err => {
          console.error('Error checking ride status:', err);
          initializeMapForBooking();
        });
    }

    function hideBookingForm() {
      document.querySelector('.booking-card').style.display = 'none';
      document.getElementById('driversPanel').classList.add('hidden');
      document.getElementById('pickup').disabled = true;
      document.getElementById('dropoff').disabled = true;
      document.getElementById('paymentMethod').disabled = true;
    }

    function displayRideStatus(html) {
      const rideStatus = document.getElementById('rideStatus');
      rideStatus.innerHTML = `<div style="border: 3px solid #9333ea; border-radius: 8px; padding: 15px; background: #f9f5ff;">${html}</div>`;
    }

    // ============================================
    // GEOLOCATION & MAP INITIALIZATION FOR BOOKING
    // ============================================
    function initializeMapForBooking() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            userLocation = { lat, lng };
            
            map.setView([lat, lng], 15);
            
            pickupMarker = L.marker([lat, lng], {
              icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] })
            }).addTo(map).bindPopup('Your Pickup Location');
            
            reverseGeocodeNominatim(lat, lng).then(address => {
              document.getElementById('pickup').value = address;
            });
          },
          function(error) {
            console.warn('Geolocation denied. Using Philippines default view.', error);
          }
        );
      }
    }

    // ============================================
    // NOMINATIM REVERSE GEOCODING
    // ============================================
    function reverseGeocodeNominatim(lat, lng) {
      return fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => data.address?.city || data.address?.town || data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`)
        .catch(err => {
          console.error('Nominatim reverse geocode error:', err);
          return `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        });
    }

    // ============================================
    // NOMINATIM ADDRESS AUTOCOMPLETE
    // ============================================
    function setupAutocomplete(inputId) {
      const inputElement = document.getElementById(inputId);
      let debounceTimer = null;
      let autocompleteList = null;

      inputElement.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        if (autocompleteList) autocompleteList.remove();
        
        if (query.length < 3) return;
        
        debounceTimer = setTimeout(() => {
          fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=5&countrycodes=ph`)
            .then(res => res.json())
            .then(data => {
              if (data.length === 0) return;
              
              autocompleteList = document.createElement('div');
              autocompleteList.className = 'autocomplete-list';
              inputElement.parentElement.appendChild(autocompleteList);
              
              data.forEach(result => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.textContent = result.display_name;
                item.addEventListener('click', function() {
                  const lat = parseFloat(result.lat);
                  const lng = parseFloat(result.lon);
                  inputElement.value = result.display_name;
                  placeMarker(inputId, lat, lng, result.display_name);
                  autocompleteList.remove();
                  autocompleteList = null;
                });
                autocompleteList.appendChild(item);
              });
            })
            .catch(err => console.error('Nominatim search error:', err));
        }, 300);
      });
      
      document.addEventListener('click', function(event) {
        if (!inputElement.contains(event.target) && autocompleteList && !autocompleteList.contains(event.target)) {
          autocompleteList.remove();
          autocompleteList = null;
        }
      });
    }

    setupAutocomplete('pickup');
    setupAutocomplete('dropoff');

    // ============================================
    // CLICK-TO-PIN FUNCTIONALITY
    // ============================================
    map.on('click', function(event) {
      if (isRideActive) return;
      
      const lat = event.latlng.lat;
      const lng = event.latlng.lng;
      
      if (clickCounter === 0) {
        if (pickupMarker) map.removeLayer(pickupMarker);
        pickupMarker = L.marker([lat, lng], {
          icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] })
        }).addTo(map).bindPopup('Pickup Location');
        
        reverseGeocodeNominatim(lat, lng).then(address => {
          document.getElementById('pickup').value = address;
        });
        clickCounter++;
      } else if (clickCounter === 1) {
        if (dropoffMarker) map.removeLayer(dropoffMarker);
        dropoffMarker = L.marker([lat, lng], {
          icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] })
        }).addTo(map).bindPopup('Dropoff Location');
        
        reverseGeocodeNominatim(lat, lng).then(address => {
          document.getElementById('dropoff').value = address;
          calculateOSRMRoute();
        });
        clickCounter++;
      } else {
        if (pickupMarker) map.removeLayer(pickupMarker);
        if (dropoffMarker) map.removeLayer(dropoffMarker);
        if (routeLine) map.removeLayer(routeLine);
        pickupMarker = null;
        dropoffMarker = null;
        routeLine = null;
        document.getElementById('pickup').value = '';
        document.getElementById('dropoff').value = '';
        document.getElementById('rideStatus').innerHTML = '';
        clickCounter = 0;
      }
    });

    function placeMarker(inputId, lat, lng, address) {
      if (inputId === 'pickup') {
        if (pickupMarker) map.removeLayer(pickupMarker);
        pickupMarker = L.marker([lat, lng], {
          icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] })
        }).addTo(map).bindPopup('Pickup Location');
        clickCounter = Math.max(clickCounter, 1);
      } else {
        if (dropoffMarker) map.removeLayer(dropoffMarker);
        dropoffMarker = L.marker([lat, lng], {
          icon: L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] })
        }).addTo(map).bindPopup('Dropoff Location');
        clickCounter = 2;
        if (pickupMarker && dropoffMarker) {
          calculateOSRMRoute();
        }
      }
    }

    // ============================================
    // OSRM ROUTING & ROUTE DRAWING
    // ============================================
    function calculateOSRMRoute() {
      if (!pickupMarker || !dropoffMarker) return;
      
      const pickupLatLng = pickupMarker.getLatLng();
      const dropoffLatLng = dropoffMarker.getLatLng();
      
      const lng1 = pickupLatLng.lng;
      const lat1 = pickupLatLng.lat;
      const lng2 = dropoffLatLng.lng;
      const lat2 = dropoffLatLng.lat;
      
      document.getElementById('rideStatus').innerHTML = '<p style="text-align:center; color:#666;">Calculating route...</p>';
      
      if (routeLine) {
        map.removeLayer(routeLine);
        routeLine = null;
      }
      
      fetch(`https://router.project-osrm.org/route/v1/driving/${lng1},${lat1};${lng2},${lat2}?overview=full&geometries=geojson`)
        .then(res => res.json())
        .then(data => {
          if (data.routes && data.routes[0]) {
            const route = data.routes[0];
            const coordinates = route.geometry.coordinates;
            
            const distanceMeters = route.distance;
            const distanceKm = (distanceMeters / 1000).toFixed(2);
            
            routeLine = L.polyline(
              coordinates.map(coord => [coord[1], coord[0]]),
              { color: '#3b82f6', weight: 5, opacity: 0.7 }
            ).addTo(map);
            
            currentRouteMeta = {
              distance: distanceKm,
              distanceMeters: distanceMeters
            };
            
            const fareAmount = calculateFareAmount(distanceKm);
            
            document.getElementById('rideStatus').innerHTML = `
              <div style="border: 3px solid #9333ea; border-radius: 8px; padding: 15px; background: #f9f5ff;">
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #666;"><strong>Distance:</strong> ${distanceKm} km</p>
                <p style="margin: 0; font-size: 18px; color: #9333ea; font-weight: bold;"><strong>Estimated Fare:</strong> ₱${fareAmount.toFixed(2)}</p>
              </div>
            `;
            
            const bounds = L.latLngBounds([
              [lat1, lng1],
              [lat2, lng2]
            ]);
            map.fitBounds(bounds, { padding: [50, 50] });
          } else {
            document.getElementById('rideStatus').innerHTML = '<p style="color: #ef4444;">Error calculating route. Please try again.</p>';
          }
        })
        .catch(err => {
          console.error('OSRM routing error:', err);
          document.getElementById('rideStatus').innerHTML = '<p style="color: #ef4444;">Route calculation failed. Please check your internet connection.</p>';
        });
    }

    // ============================================
    // FARE CALCULATION
    // ============================================
    function calculateFareAmount(distanceKm) {
      const baseFare = 40;
      const baseFareDistance = 2;
      const additionalFarePerKm = 10;
      
      if (distanceKm <= baseFareDistance) {
        return baseFare;
      } else {
        const additionalDistance = distanceKm - baseFareDistance;
        return baseFare + (additionalDistance * additionalFarePerKm);
      }
    }

    // ============================================
    // WINDOW-EXPOSED FUNCTIONS (For onclick handlers in HTML)
    // ============================================
    window.clearAllAndRecenter = function() {
      if (pickupMarker) map.removeLayer(pickupMarker);
      if (dropoffMarker) map.removeLayer(dropoffMarker);
      if (routeLine) map.removeLayer(routeLine);
      
      pickupMarker = null;
      dropoffMarker = null;
      routeLine = null;
      currentRouteMeta = null;
      clickCounter = 0;
      
      document.getElementById('pickup').value = '';
      document.getElementById('dropoff').value = '';
      document.getElementById('rideStatus').innerHTML = '';
      
      const driversPanel = document.getElementById('driversPanel');
      if (driversPanel) {
        driversPanel.classList.remove('show');
        driversPanel.classList.add('hidden');
      }
      
      if (userLocation) {
        map.setView([userLocation.lat, userLocation.lng], 15);
      } else {
        map.setView([12.8797, 121.7740], 6);
      }
    };

    // ============================================
    // RESTORE BOOKING FORM FUNCTION
    // ============================================
    window.restoreBookingForm = function() {
      isRideActive = false;
      
      document.querySelector('.booking-card').style.display = 'block';
      
      document.getElementById('pickup').value = '';
      document.getElementById('pickup').disabled = false;
      document.getElementById('dropoff').value = '';
      document.getElementById('dropoff').disabled = false;
      document.getElementById('paymentMethod').value = '';
      document.getElementById('paymentMethod').disabled = false;
      
      if (pickupMarker) map.removeLayer(pickupMarker);
      if (dropoffMarker) map.removeLayer(dropoffMarker);
      if (routeLine) map.removeLayer(routeLine);
      
      pickupMarker = null;
      dropoffMarker = null;
      routeLine = null;
      currentRouteMeta = null;
      clickCounter = 0;
      
      document.getElementById('rideStatus').innerHTML = '';
      
      document.getElementById('driversPanel').classList.add('hidden');
      document.getElementById('driversPanel').classList.remove('show');
      
      if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
      }
      
      if (userLocation) {
        map.setView([userLocation.lat, userLocation.lng], 15);
      } else {
        map.setView([12.8797, 121.7740], 6);
      }
    };

    // ============================================
    // FIND AVAILABLE DRIVERS
    // ============================================
    window.calculateFare = function() {
      const pickup = document.getElementById('pickup').value.trim();
      const dropoff = document.getElementById('dropoff').value.trim();
      const paymentMethod = document.getElementById('paymentMethod').value;
      
      if (!pickup || !dropoff) {
        alert('Please set both pickup and dropoff locations on the map.');
        return;
      }
      
      if (!paymentMethod) {
        alert('Please select a payment method.');
        return;
      }
      
      if (!pickupMarker || !dropoffMarker || !currentRouteMeta) {
        alert('Please complete the route calculation first.');
        return;
      }
      
      const driverList = document.getElementById('driverList');
      driverList.innerHTML = '<p style="text-align:center; color:#666;">Loading available drivers...</p>';
      
      fetch('getavailabledrivers.php')
        .then(res => res.json())
        .then(drivers => {
          if (drivers.length === 0) {
            driverList.innerHTML = '<p style="color:#ef4444; text-align:center;">No drivers available right now. Please try again later.</p>';
            return;
          }
          
          driverList.innerHTML = '';
          drivers.forEach(driver => {
            const driverCard = document.createElement('div');
            driverCard.className = 'driver-card';
            driverCard.innerHTML = `
              <div class="driver-info">
                <h4>${driver.name}</h4>
                <p>${driver.brand} ${driver.model}</p>
                <p style="color: #9333ea; font-size: 12px;">${driver.color}</p>
              </div>
              <button class="accept-btn" onclick="selectDriver('${driver.driver_id}', '${driver.name}')">Accept</button>
            `;
            driverList.appendChild(driverCard);
          });
          
          const driversPanel = document.getElementById('driversPanel');
          driversPanel.classList.remove('hidden');
          driversPanel.classList.add('show');
        })
        .catch(err => {
          console.error('Error fetching drivers:', err);
          driverList.innerHTML = '<p style="color:#ef4444;">Failed to load drivers. Please try again.</p>';
        });
    };

    // ============================================
    // SELECT DRIVER & SUBMIT RIDE
    // ============================================
    window.selectDriver = function(driverId, driverName) {
      const pickup = document.getElementById('pickup').value.trim();
      const dropoff = document.getElementById('dropoff').value.trim();
      const paymentMethod = document.getElementById('paymentMethod').value;
      const distance = currentRouteMeta.distance;
      const fare = calculateFareAmount(distance);
      
      const requestBody = {
        pickup: pickup,
        dropoff: dropoff,
        distance: distance,
        fare: fare,
        payment_method: paymentMethod
      };
      
      fetch('submitRide.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestBody)
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById('driversPanel').classList.add('hidden');
            document.getElementById('driversPanel').classList.remove('show');
            
            const rideStatus = document.getElementById('rideStatus');
            rideStatus.innerHTML = `
              <div style="border: 3px solid #10b981; border-radius: 8px; padding: 15px; background: #f0fdf4;">
                <h3 style="margin: 0 0 10px 0; color: #10b981;">Booking Confirmed!</h3>
                <p style="margin: 5px 0; font-size: 13px; color: #555;"><strong>Driver:</strong> ${driverName}</p>
                <p style="margin: 5px 0; font-size: 13px; color: #555;"><strong>Pickup:</strong> ${pickup}</p>
                <p style="margin: 5px 0; font-size: 13px; color: #555;"><strong>Dropoff:</strong> ${dropoff}</p>
                <p style="margin: 5px 0; font-size: 13px; color: #555;"><strong>Fare:</strong> ₱${fare.toFixed(2)}</p>
                <p style="margin: 5px 0; font-size: 13px; color: #555;"><strong>Payment:</strong> ${paymentMethod}</p>
                <p style="margin: 10px 0 0 0; font-size: 13px; color: #9333ea; font-weight: bold;">Waiting for driver to accept...</p>
              </div>
            `;
            
            isRideActive = true;
            hideBookingForm();
            startRidePolling();
          } else {
            document.getElementById('rideStatus').innerHTML = `<p style="color: #ef4444;">${data.error || 'Failed to book ride. Please try again.'}</p>`;
          }
        })
        .catch(err => {
          console.error('Error submitting ride:', err);
          document.getElementById('rideStatus').innerHTML = '<p style="color: #ef4444;">Error processing your booking. Please try again.</p>';
        });
    };

    // ============================================
    // RIDE STATUS POLLING WITH SESSION PERSISTENCE
    // ============================================
    function startRidePolling() {
      if (pollInterval) return;
      
      pollInterval = setInterval(() => {
        fetch('checkRideStatus.php')
          .then(res => res.text())
          .then(html => {
            if (html.includes('No current ride')) {
              clearInterval(pollInterval);
              pollInterval = null;
              
              setTimeout(() => {
                window.restoreBookingForm();
              }, 10000);
              return;
            }
            
            displayRideStatus(html);
            
            if (html.includes('completed') || html.includes('Completed')) {
              clearInterval(pollInterval);
              pollInterval = null;
              
              setTimeout(() => {
                window.restoreBookingForm();
              }, 10000);
            }
          })
          .catch(err => console.error('Poll error:', err));
      }, 5000);
    }

    // ============================================
    // DISMISS RECEIPT FUNCTION (Called from checkRideStatus.php button)
    // ============================================
    window.dismissReceipt = function() {
      if (typeof pollInterval !== 'undefined') clearInterval(pollInterval);
      pollInterval = null;
      document.getElementById('rideStatus').innerHTML = '';
      document.getElementById('rideStatus').style.border = 'none';
      document.getElementById('rideStatus').style.padding = '0';
      document.getElementById('driversPanel').classList.add('hidden');
      document.getElementById('driversPanel').classList.remove('show');
      const bookingCard = document.querySelector('.booking-card');
      if (bookingCard) bookingCard.style.display = 'block';
      const inputs = document.querySelectorAll('#pickup, #dropoff, #paymentMethod');
      inputs.forEach(el => el.disabled = false);
      if (typeof window.restoreBookingForm === 'function') window.restoreBookingForm();
    };

    window.addEventListener('beforeunload', () => {
      if (pollInterval) clearInterval(pollInterval);
    });

}); // End DOMContentLoaded