function showReceipt(ride) {
            document.getElementById("modal-date").textContent      = formatDate(ride.end_time);
            document.getElementById("modal-rider").textContent     = ride.rider_name;
            document.getElementById("modal-pickup").textContent    = ride.pickup;
            document.getElementById("modal-dropoff").textContent   = ride.dropoff;
            document.getElementById("modal-distance").textContent  = ride.distance + " km";
            document.getElementById("modal-payment").textContent   = ride.payment_method;
            document.getElementById("modal-fare").textContent      = "₱" + parseFloat(ride.price).toFixed(2);
            document.getElementById("modal-tax").textContent       = "- ₱" + parseFloat(ride.tax).toFixed(2);
            document.getElementById("modal-earnings").textContent  = "₱" + parseFloat(ride.driver_earnings).toFixed(2);
            document.getElementById("receiptModal").classList.add("active");
        }

        function closeReceipt() {
            document.getElementById("receiptModal").classList.remove("active");
        }

        function formatDate(datetime) {
            const d = new Date(datetime);
            return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) +
                   ' • ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }

        // Close modal when clicking outside
        document.getElementById("receiptModal").addEventListener("click", function(e) {
            if (e.target === this) closeReceipt();
});