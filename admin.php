<?php
session_start();
include "connection.php";

// Simple admin auth - you can add a proper login later
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: adminlogin.php");
    exit();
}

// ── STATS ──────────────────────────────────────────────
$total_drivers    = $conn->query("SELECT COUNT(*) AS c FROM driver_tbl")->fetch_assoc()['c'];
$total_passengers = $conn->query("SELECT COUNT(*) AS c FROM customer_tbl")->fetch_assoc()['c'];
$total_rides      = $conn->query("SELECT COUNT(*) AS c FROM ride_requests WHERE status='completed'")->fetch_assoc()['c'];
$total_tax        = $conn->query("SELECT SUM(tax) AS c FROM ride_requests WHERE status='completed'")->fetch_assoc()['c'] ?? 0;
$total_revenue    = $conn->query("SELECT SUM(price) AS c FROM ride_requests WHERE status='completed'")->fetch_assoc()['c'] ?? 0;
$pending_rides    = $conn->query("SELECT COUNT(*) AS c FROM ride_requests WHERE status='pending'")->fetch_assoc()['c'];

// ── DATA ───────────────────────────────────────────────
$drivers    = $conn->query("SELECT * FROM driver_tbl ORDER BY driver_id DESC")->fetch_all(MYSQLI_ASSOC);
$passengers = $conn->query("SELECT * FROM customer_tbl ORDER BY customer_id DESC")->fetch_all(MYSQLI_ASSOC);
$rides      = $conn->query("
    SELECT rr.*, d.name AS driver_name 
    FROM ride_requests rr 
    LEFT JOIN driver_tbl d ON rr.driver_id = d.driver_id 
    ORDER BY rr.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// ── HANDLE ACTIONS ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_driver') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM driver_tbl WHERE driver_id=$id");
    } elseif ($action === 'delete_passenger') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM customer_tbl WHERE customer_id=$id");
    } elseif ($action === 'toggle_driver_status') {
        $id = (int)$_POST['id'];
        $row = $conn->query("SELECT status FROM driver_tbl WHERE driver_id=$id")->fetch_assoc();
        $new = $row['status'] === 'on' ? 'off' : 'on';
        $conn->query("UPDATE driver_tbl SET status='$new' WHERE driver_id=$id");
    }
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoRide Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- DARK MODE ADDITION START -->
    <link rel="stylesheet" href="dark-mode.css">
    <script src="dark-mode.js"></script>
    <!-- DARK MODE ADDITION END -->
    <link rel="stylesheet" href="admin.css">

</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <h1><i class="fa-solid fa-motorcycle"></i> MotoRide</h1>
        <p>Admin Panel</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Overview</div>
        <button class="nav-link active" onclick="showTab('dashboard', this)">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </button>

        <div class="nav-label">Manage</div>
        <button class="nav-link" onclick="showTab('drivers', this)">
            <i class="fa-solid fa-motorcycle"></i> Drivers
        </button>
        <button class="nav-link" onclick="showTab('passengers', this)">
            <i class="fa-solid fa-users"></i> Passengers
        </button>
        <button class="nav-link" onclick="showTab('rides', this)">
            <i class="fa-solid fa-route"></i> Ride Transactions
        </button>

        <div class="nav-label">Finance</div>
        <button class="nav-link" onclick="showTab('earnings', this)">
            <i class="fa-solid fa-peso-sign"></i> Platform Earnings
        </button>
    </nav>

    <div class="sidebar-footer">
        <!-- DARK MODE ADDITION START -->
        <a href="#" onclick="toggleDarkMode(); return false;" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <i class="fa-solid fa-moon"></i> <span>Dark Mode</span>
        </a>
        <hr style="border: none; border-top: 1px solid var(--border); margin: 10px 0;">
        <!-- DARK MODE ADDITION END -->
        <a href="adminlogout.php">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main">

    <!-- ══ DASHBOARD ══ -->
    <section class="tab-section active" id="tab-dashboard">
        <div class="page-header">
            <h2>Dashboard</h2>
            <p>Welcome back, Admin. Here's what's happening today.</p>
        </div>

        <!-- STATS -->
        <div class="stat-grid">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-motorcycle"></i></div>
                <div class="stat-value"><?php echo $total_drivers; ?></div>
                <div class="stat-label">Total Drivers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-value"><?php echo $total_passengers; ?></div>
                <div class="stat-label">Passengers</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-route"></i></div>
                <div class="stat-value"><?php echo $total_rides; ?></div>
                <div class="stat-label">Completed Rides</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-value"><?php echo $pending_rides; ?></div>
                <div class="stat-label">Pending Rides</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-peso-sign"></i></div>
                <div class="stat-value">₱<?php echo number_format($total_revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div>
                <div class="stat-value">₱<?php echo number_format($total_tax, 0); ?></div>
                <div class="stat-label">Platform Fees</div>
            </div>
        </div>

        <!-- RECENT RIDES -->
        <div class="table-card">
            <div class="table-header">
                <h3>Recent Transactions</h3>
                <span style="font-size:12px; color:var(--text2);">Last 10 rides</span>
            </div>
            <div class="activity-list">
                <?php
                $recent = array_slice($rides, 0, 10);
                if (empty($recent)): ?>
                    <div style="text-align:center; padding:40px; color:var(--text2); font-size:13px;">No transactions yet</div>
                <?php else: foreach ($recent as $r):
                    $statusColor = match($r['status']) {
                        'completed' => '#22c55e',
                        'pending'   => '#f59e0b',
                        'accepted'  => '#60a5fa',
                        'started'   => '#a855f7',
                        default     => '#8888aa'
                    };
                ?>
                <div class="activity-item">
                    <div class="activity-dot" style="background:<?php echo $statusColor; ?>"></div>
                    <div class="activity-info">
                        <strong><?php echo htmlspecialchars($r['rider_name']); ?> → <?php echo htmlspecialchars($r['driver_name'] ?? 'Unassigned'); ?></strong>
                        <p><?php echo htmlspecialchars($r['pickup']); ?> → <?php echo htmlspecialchars($r['dropoff']); ?> • <?php echo $r['distance']; ?>km • <?php echo htmlspecialchars($r['payment_method']); ?></p>
                    </div>
                    <div>
                        <div class="activity-fare">₱<?php echo number_format($r['price'], 2); ?></div>
                        <div class="activity-time"><?php echo date('M d, h:i A', strtotime($r['created_at'])); ?></div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- ══ DRIVERS ══ -->
    <section class="tab-section" id="tab-drivers">
        <div class="page-header">
            <h2>Drivers</h2>
            <p>Manage all registered drivers</p>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>All Drivers (<?php echo $total_drivers; ?>)</h3>
                <input type="text" placeholder="Search drivers..." oninput="filterTable(this, 'driversTable')">
            </div>
            <div class="table-wrap">
                <table id="driversTable">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Contact</th>
                            <th>Username</th>
                            <th>Plate No.</th>
                            <th>Brand / Model</th>
                            <th>Color</th>
                            <th>License</th>
                            <th>Expiry</th>
                            <th>GCash</th>
                            <th>Status</th>
                            <th>Rides</th>
                            <th>Earnings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($drivers)): ?>
                            <tr class="empty-row"><td colspan="13">No drivers found</td></tr>
                        <?php else: foreach ($drivers as $d): ?>
                        <tr>
                            <td>
                                <div class="name-cell">
                                    <div class="avatar"><?php echo strtoupper(substr($d['name'], 0, 1)); ?></div>
                                    <?php echo htmlspecialchars($d['name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($d['contact_no']); ?></td>
                            <td style="color:var(--text2);"><?php echo htmlspecialchars($d['username']); ?></td>
                            <td><span style="font-family:var(--font-mono); font-size:12px;"><?php echo htmlspecialchars($d['plate_no']); ?></span></td>
                            <td><?php echo htmlspecialchars($d['brand'] . ' ' . $d['model']); ?></td>
                            <td><?php echo htmlspecialchars($d['color']); ?></td>
                            <td style="font-family:var(--font-mono); font-size:11px;"><?php echo htmlspecialchars($d['driver_license'] ?? 'N/A'); ?></td>
                            <td><?php echo $d['license_expiry'] ? date('M d, Y', strtotime($d['license_expiry'])) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($d['gcash'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge <?php echo $d['status'] === 'on' ? 'badge-green' : 'badge-gray'; ?>">
                                    <i class="fa-solid fa-circle" style="font-size:7px;"></i>
                                    <?php echo $d['status'] === 'on' ? 'Online' : 'Offline'; ?>
                                </span>
                            </td>
                            <td style="font-family:var(--font-mono);"><?php echo $d['total_rides']; ?></td>
                            <td style="font-family:var(--font-mono); color:var(--green);">₱<?php echo number_format($d['total_earnings'], 2); ?></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_driver_status">
                                        <input type="hidden" name="id" value="<?php echo $d['driver_id']; ?>">
                                        <button type="submit" class="btn-sm btn-toggle">
                                            <?php echo $d['status'] === 'on' ? 'Set Offline' : 'Set Online'; ?>
                                        </button>
                                    </form>
                                    <button class="btn-sm btn-danger" onclick="confirmDelete('driver', <?php echo $d['driver_id']; ?>, '<?php echo htmlspecialchars($d['name']); ?>')">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ══ PASSENGERS ══ -->
    <section class="tab-section" id="tab-passengers">
        <div class="page-header">
            <h2>Passengers</h2>
            <p>Manage all registered passengers</p>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>All Passengers (<?php echo $total_passengers; ?>)</h3>
                <input type="text" placeholder="Search passengers..." oninput="filterTable(this, 'passengersTable')">
            </div>
            <div class="table-wrap">
                <table id="passengersTable">
                    <thead>
                        <tr>
                            <th>Passenger</th>
                            <th>Contact</th>
                            <th>Username</th>
                            <th>GCash</th>
                            <th>Total Rides</th>
                            <th>Total Spent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($passengers)): ?>
                            <tr class="empty-row"><td colspan="7">No passengers found</td></tr>
                        <?php else: foreach ($passengers as $p):
                            // Get per-passenger stats
                            $pname = $conn->real_escape_string($p['name']);
                            $pstats = $conn->query("SELECT COUNT(*) AS rides, SUM(price) AS spent FROM ride_requests WHERE rider_name='$pname' AND status='completed'")->fetch_assoc();
                        ?>
                        <tr>
                            <td>
                                <div class="name-cell">
                                    <div class="avatar" style="background:linear-gradient(135deg,#22c55e,#16a34a);"><?php echo strtoupper(substr($p['name'], 0, 1)); ?></div>
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($p['contact_no']); ?></td>
                            <td style="color:var(--text2);"><?php echo htmlspecialchars($p['username']); ?></td>
                            <td><?php echo htmlspecialchars($p['gcash'] ?? 'N/A'); ?></td>
                            <td style="font-family:var(--font-mono);"><?php echo $pstats['rides'] ?? 0; ?></td>
                            <td style="font-family:var(--font-mono); color:var(--green);">₱<?php echo number_format($pstats['spent'] ?? 0, 2); ?></td>
                            <td>
                                <button class="btn-sm btn-danger" onclick="confirmDelete('passenger', <?php echo $p['customer_id']; ?>, '<?php echo htmlspecialchars($p['name']); ?>')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ══ RIDES ══ -->
    <section class="tab-section" id="tab-rides">
        <div class="page-header">
            <h2>Ride Transactions</h2>
            <p>All ride requests and their statuses</p>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h3>All Rides (<?php echo count($rides); ?>)</h3>
                <input type="text" placeholder="Search rides..." oninput="filterTable(this, 'ridesTable')">
            </div>
            <div class="table-wrap">
                <table id="ridesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Passenger</th>
                            <th>Driver</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Distance</th>
                            <th>Fare</th>
                            <th>Tax</th>
                            <th>Driver Earnings</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rides)): ?>
                            <tr class="empty-row"><td colspan="12">No rides found</td></tr>
                        <?php else: foreach ($rides as $r):
                            $badgeClass = match($r['status']) {
                                'completed' => 'badge-green',
                                'pending'   => 'badge-yellow',
                                'accepted'  => 'badge-blue',
                                'started'   => 'badge-purple',
                                'cancelled' => 'badge-red',
                                default     => 'badge-gray'
                            };
                        ?>
                        <tr>
                            <td style="font-family:var(--font-mono); color:var(--text2);">#<?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['rider_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['driver_name'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($r['pickup']); ?></td>
                            <td><?php echo htmlspecialchars($r['dropoff']); ?></td>
                            <td style="font-family:var(--font-mono);"><?php echo $r['distance']; ?> km</td>
                            <td style="font-family:var(--font-mono); color:var(--text);">₱<?php echo number_format($r['price'], 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--red);">₱<?php echo number_format($r['tax'], 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--green);">₱<?php echo number_format($r['driver_earnings'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo $r['payment_method'] === 'GCash' ? 'badge-purple' : 'badge-gray'; ?>">
                                    <?php echo htmlspecialchars($r['payment_method']); ?>
                                </span>
                            </td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                            <td style="font-size:12px; color:var(--text2);"><?php echo date('M d, Y h:i A', strtotime($r['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ══ EARNINGS ══ -->
    <section class="tab-section" id="tab-earnings">
        <div class="page-header">
            <h2>Platform Earnings</h2>
            <p>Overview of all platform fee collections</p>
        </div>

        <div class="stat-grid">
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-peso-sign"></i></div>
                <div class="stat-value">₱<?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">Gross Revenue</div>
            </div>
            <div class="stat-card red">
                <div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div>
                <div class="stat-value">₱<?php echo number_format($total_tax, 2); ?></div>
                <div class="stat-label">Platform Fees (5%)</div>
            </div>
            <div class="stat-card purple">
                <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-value">₱<?php echo number_format($total_revenue - $total_tax, 2); ?></div>
                <div class="stat-label">Driver Payouts</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon"><i class="fa-solid fa-route"></i></div>
                <div class="stat-value"><?php echo $total_rides; ?></div>
                <div class="stat-label">Completed Rides</div>
            </div>
        </div>

        <!-- PAYMENT METHOD BREAKDOWN -->
        <?php
        $cash_total  = $conn->query("SELECT SUM(price) AS t, COUNT(*) AS c FROM ride_requests WHERE status='completed' AND payment_method='Cash'")->fetch_assoc();
        $gcash_total = $conn->query("SELECT SUM(price) AS t, COUNT(*) AS c FROM ride_requests WHERE status='completed' AND payment_method='GCash'")->fetch_assoc();
        $cash_tax    = $conn->query("SELECT SUM(tax) AS t FROM ride_requests WHERE status='completed' AND payment_method='Cash'")->fetch_assoc();
        $gcash_tax   = $conn->query("SELECT SUM(tax) AS t FROM ride_requests WHERE status='completed' AND payment_method='GCash'")->fetch_assoc();
        ?>

        <div class="table-card">
            <div class="table-header"><h3>Payment Method Breakdown</h3></div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Rides</th>
                            <th>Gross Revenue</th>
                            <th>Platform Fee (5%)</th>
                            <th>Driver Payouts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-gray"><i class="fa-solid fa-money-bill"></i> Cash</span></td>
                            <td style="font-family:var(--font-mono);"><?php echo $cash_total['c']; ?></td>
                            <td style="font-family:var(--font-mono);">₱<?php echo number_format($cash_total['t'] ?? 0, 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--red);">₱<?php echo number_format($cash_tax['t'] ?? 0, 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--green);">₱<?php echo number_format(($cash_total['t'] ?? 0) - ($cash_tax['t'] ?? 0), 2); ?></td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-purple"><i class="fa-solid fa-mobile-screen"></i> GCash</span></td>
                            <td style="font-family:var(--font-mono);"><?php echo $gcash_total['c']; ?></td>
                            <td style="font-family:var(--font-mono);">₱<?php echo number_format($gcash_total['t'] ?? 0, 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--red);">₱<?php echo number_format($gcash_tax['t'] ?? 0, 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--green);">₱<?php echo number_format(($gcash_total['t'] ?? 0) - ($gcash_tax['t'] ?? 0), 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PER TRANSACTION -->
        <div class="table-card">
            <div class="table-header">
                <h3>Fee Collection per Ride</h3>
                <input type="text" placeholder="Search..." oninput="filterTable(this, 'earningsTable')">
            </div>
            <div class="table-wrap">
                <table id="earningsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Passenger</th>
                            <th>Driver</th>
                            <th>Route</th>
                            <th>Gross Fare</th>
                            <th>Platform Fee</th>
                            <th>Driver Payout</th>
                            <th>Payment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $completed = array_filter($rides, fn($r) => $r['status'] === 'completed');
                        if (empty($completed)): ?>
                            <tr class="empty-row"><td colspan="9">No completed rides yet</td></tr>
                        <?php else: foreach ($completed as $r): ?>
                        <tr>
                            <td style="font-family:var(--font-mono); color:var(--text2);">#<?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['rider_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['driver_name'] ?? '—'); ?></td>
                            <td style="font-size:12px;"><?php echo htmlspecialchars($r['pickup']); ?> → <?php echo htmlspecialchars($r['dropoff']); ?></td>
                            <td style="font-family:var(--font-mono);">₱<?php echo number_format($r['price'], 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--red);">₱<?php echo number_format($r['tax'], 2); ?></td>
                            <td style="font-family:var(--font-mono); color:var(--green);">₱<?php echo number_format($r['driver_earnings'], 2); ?></td>
                            <td><span class="badge <?php echo $r['payment_method'] === 'GCash' ? 'badge-purple' : 'badge-gray'; ?>"><?php echo htmlspecialchars($r['payment_method']); ?></span></td>
                            <td style="font-size:12px; color:var(--text2);"><?php echo date('M d, Y', strtotime($r['end_time'])); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</main>

<!-- DELETE CONFIRM MODAL -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>⚠️ Confirm Delete</h3>
        <p id="deleteMsg">Are you sure you want to delete this account? This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <form method="POST" id="deleteForm" style="display:inline;">
                <input type="hidden" name="action" id="deleteAction">
                <input type="hidden" name="id" id="deleteId">
                <button type="submit" class="btn-confirm-delete">Delete</button>
            </form>
        </div>
    </div>
</div>

<script src="javafolder/admin.js"></script>

</body>
</html>
