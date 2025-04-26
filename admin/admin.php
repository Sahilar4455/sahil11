
<?php
session_start();

// Database connection - using PDO
try {
    require_once 'db/connection.php'; 
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Admin credentials
$admin_username = 'admin';
$admin_password = 'villa@123'; // Hashed in production

// Login handling
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit();
    } else {
        $login_error = "Invalid username or password";
    }
}

// Logout handling
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit();
}

// Check if logged in
$logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Date filtering
$where_clause = '';
$params = [];
if ($logged_in && isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
    $where_clause = " WHERE DATE(submission_date) = :filter_date";
    $params[':filter_date'] = $_GET['filter_date'];
}

// Get contact submissions data
$submissions = [];
if ($logged_in && isset($pdo)) {
    try {
        $sql = "SELECT * FROM contact_submissions" . $where_clause . " ORDER BY submission_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

// Export to Excel (XLS format - simple HTML table)
if ($logged_in && isset($_GET['export'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=contacts_".date('Y-m-d').".xls");
    
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>First Name</th>';
    echo '<th>Last Name</th>';
    echo '<th>Email</th>';
    echo '<th>Phone</th>';
    echo '<th>Country Code</th>';
    echo '<th>Full Phone</th>';
    echo '<th>UTM Source</th>';
    echo '<th>UTM Medium</th>';
    echo '<th>UTM Campaign</th>';
    echo '<th>UTM Term</th>';
    echo '<th>UTM Content</th>';
    echo '<th>GCLID</th>';
    echo '<th>FBCLID</th>';
    echo '<th>Referrer</th>';
    echo '<th>Landing Page</th>';
    echo '<th>User Agent</th>';
    echo '<th>IP Address</th>';
    echo '<th>Submission Date</th>';
    echo '</tr>';
    
    foreach ($submissions as $submission) {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($submission['id']).'</td>';
        echo '<td>'.htmlspecialchars($submission['f_name']).'</td>';
        echo '<td>'.htmlspecialchars($submission['l_name']).'</td>';
        echo '<td>'.htmlspecialchars($submission['email']).'</td>';
        echo '<td>'.htmlspecialchars($submission['phone']).'</td>';
        echo '<td>'.htmlspecialchars($submission['country_code']).'</td>';
        echo '<td>'.htmlspecialchars($submission['full_phone']).'</td>';
        echo '<td>'.htmlspecialchars($submission['utm_source']).'</td>';
        echo '<td>'.htmlspecialchars($submission['utm_medium']).'</td>';
        echo '<td>'.htmlspecialchars($submission['utm_campaign']).'</td>';
        echo '<td>'.htmlspecialchars($submission['utm_term']).'</td>';
        echo '<td>'.htmlspecialchars($submission['utm_content']).'</td>';
        echo '<td>'.htmlspecialchars($submission['gclid']).'</td>';
        echo '<td>'.htmlspecialchars($submission['fbclid']).'</td>';
        echo '<td>'.htmlspecialchars($submission['referrer']).'</td>';
        echo '<td>'.htmlspecialchars($submission['landing_page']).'</td>';
        echo '<td>'.htmlspecialchars($submission['user_agent']).'</td>';
        echo '<td>'.htmlspecialchars($submission['ip_address']).'</td>';
        echo '<td>'.htmlspecialchars($submission['submission_date']).'</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts Admin</title>
    <style>
        /* Your existing CSS remains exactly the same */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
    min-height: 100vh;
    position: relative;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    width: 100%;
}

/* Login Form */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

.login-form {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.login-form h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
    font-size: clamp(1.5rem, 2.5vw, 2rem);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: clamp(0.9rem, 1.2vw, 1rem);
}

.form-group input {
    width: 100%;
    padding: 12px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: clamp(0.9rem, 1.2vw, 1rem);
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #3498db;
    outline: none;
}

.btn {
    display: inline-block;
    background: #3498db;
    color: #fff;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    border-radius: 4px;
    font-size: clamp(0.9rem, 1.2vw, 1rem);
    transition: all 0.3s;
}

.btn:hover {
    background: #2980b9;
    transform: translateY(-1px);
}

.btn:active {
    transform: translateY(0);
}

.btn-block {
    display: block;
    width: 100%;
}

.error {
    color: #e74c3c;
    margin-bottom: 15px;
    text-align: center;
    font-size: clamp(0.8rem, 1vw, 0.9rem);
}

/* Dashboard */
.header {
    background: #2c3e50;
    color: #fff;
    padding: 15px 0;
    margin-bottom: 30px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

.logo {
    font-size: clamp(1.2rem, 2vw, 1.5rem);
    font-weight: bold;
}

.logout-btn {
    color: #fff;
    text-decoration: none;
    background: #e74c3c;
    padding: 8px 15px;
    border-radius: 4px;
    transition: all 0.3s;
    font-size: clamp(0.8rem, 1vw, 0.9rem);
}

.logout-btn:hover {
    background: #c0392b;
    transform: translateY(-1px);
}

.sidebar {
    width: 250px;
    background: #34495e;
    color: #fff;
    position: fixed;
    height: 100vh;
    padding: 20px 0;
    top: 0;
    left: 0;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar.active {
    transform: translateX(0);
    box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
}

.sidebar-menu {
    list-style: none;
}

.sidebar-menu li {
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background 0.3s;
}

.sidebar-menu li a {
    color: #fff;
    text-decoration: none;
    display: block;
    font-size: clamp(0.9rem, 1.1vw, 1rem);
}

.sidebar-menu li:hover {
    background: rgba(255, 255, 255, 0.1);
}

.main-content {
    margin-left: 0;
    transition: all 0.3s ease-in-out;
    padding: 20px;
    width: 100%;
    position: relative;
}

.main-content.active {
    transform: translateX(250px);
    width: calc(100% - 250px);
}

.menu-toggle {
    background: none;
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    margin-right: 15px;
    transition: transform 0.3s;
    z-index: 1001;
    padding: 5px;
}

.menu-toggle.active {
    transform: rotate(90deg);
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.dashboard-title {
    font-size: clamp(1.3rem, 2vw, 1.8rem);
    color: #2c3e50;
}

.filter-form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-form input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: clamp(0.8rem, 1vw, 0.9rem);
    min-width: 150px;
}

.leads-table-container {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.leads-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    min-width: 600px;
}

.leads-table th, 
.leads-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: clamp(0.8rem, 1vw, 0.9rem);
}

.leads-table th {
    background: #2c3e50;
    color: #fff;
    position: sticky;
    top: 0;
}

.leads-table tr:hover {
    background: #f5f5f5;
}

.no-leads {
    text-align: center;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    font-size: clamp(0.9rem, 1.2vw, 1.1rem);
}

/* Close button for sidebar */
.sidebar-close {
    display: none;
    position: absolute;
    right: 15px;
    top: 15px;
    background: none;
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    z-index: 1001;
}

/* Overlay for mobile */
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Enhanced Responsiveness */
@media (max-width: 480px) {
    .login-form {
        padding: 20px;
    }
    
    .form-group input {
        padding: 10px 8px;
    }
    
    .btn {
        padding: 10px 15px;
    }
    
    .filter-form {
        width: 100%;
    }
    
    .filter-form input {
        width: 100%;
        min-width: unset;
    }
    
    .leads-table th, 
    .leads-table td {
        padding: 8px 10px;
        font-size: 0.8rem;
    }
    
    .header-content {
        padding: 0 10px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        width: 280px;
        transform: translateX(-280px);
    }
    
    .main-content.active {
        transform: translateX(280px);
        width: 100%;
    }
    
    .sidebar-close {
        display: block;
    }
    
    .header-content {
        flex-direction: row;
        text-align: left;
    }
    
    .menu-toggle {
        margin: 0 15px 0 0;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .filter-form {
        flex-direction: row;
        width: 100%;
    }
}

@media (min-width: 769px) and (max-width: 992px) {
    .sidebar {
        width: 220px;
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 220px;
        transform: none;
        width: calc(100% - 220px);
    }
    
    .sidebar-close {
        display: none;
    }
    
    .overlay {
        display: none;
    }
}

@media (min-width: 769px) {
    .sidebar {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 250px;
        width: calc(100% - 250px);
        transform: none;
    }
    
    .menu-toggle {
        display: none;
    }
    
    .sidebar-close {
        display: none;
    }
}

@media (min-width: 993px) and (max-width: 1200px) {
    .container {
        padding: 20px 30px;
    }
}

@media (min-width: 1201px) {
    .container {
        padding: 20px 40px;
    }
}

/* Print styles */
@media print {
    .sidebar, .menu-toggle, .logout-btn, .filter-form, .sidebar-close, .overlay {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        padding: 10px !important;
        width: 100% !important;
        transform: none !important;
    }
    
    .leads-table {
        box-shadow: none;
    }
    
    .header {
        position: static;
    }
}
    </style>
</head>
<body>
    <?php if (!$logged_in): ?>
    <!-- Login Form -->
    <div class="login-container">
        <form class="login-form" method="POST">
            <h2>Admin Login</h2>
            <?php if (isset($login_error)): ?>
                <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-block">Login</button>
        </form>
    </div>
    <?php else: ?>
    <!-- Dashboard -->
    <div class="sidebar" id="sidebar">
        <button class="sidebar-close">&times;</button>
        <ul class="sidebar-menu">
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="admin.php?export=1">Export to Excel</a></li>
            <li><a href="admin.php?logout=1">Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content" id="main-content">
        <header class="header">
            <div class="container header-content">
                <button class="menu-toggle" id="menu-toggle">☰</button>
                <div class="logo">Contacts Admin</div>
                <a href="admin.php?logout=1" class="logout-btn">Logout</a>
            </div>
        </header>
        
        <div class="container">
            <div class="dashboard-header">
                <h1 class="dashboard-title">Contact Submissions</h1>
                <form class="filter-form" method="GET">
                    <input type="date" name="filter_date" value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                    <button type="submit" class="btn">Filter</button>
                    <?php if (isset($_GET['filter_date'])): ?>
                        <a href="admin.php" class="btn">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <?php if (!empty($submissions)): ?>
                <div class="leads-table-container">
                    <table class="leads-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Country Code</th>
                                <th>Full Phone</th>
                                <th>UTM Source</th>
                                <th>UTM Medium</th>
                                <th>UTM Campaign</th>
                                <th>UTM Term</th>
                                <th>UTM Content</th>
                                <th>GCLID</th>
                                <th>FBCLID</th>
                                <th>Referrer</th>
                                <th>Landing Page</th>
                                <th>User Agent</th>
                                <th>IP Address</th>
                                <th>Submission Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($submission['id']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['f_name']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['l_name']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['country_code']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['full_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['utm_source']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['utm_medium']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['utm_campaign']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['utm_term']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['utm_content']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['gclid']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['fbclid']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['referrer']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['landing_page']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['user_agent']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['ip_address']); ?></td>
                                    <td><?php echo date('M j, Y g:i a', strtotime($submission['submission_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-leads">
                    <p>No submissions found<?php echo isset($_GET['filter_date']) ? ' for the selected date' : ''; ?>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="overlay" id="overlay"></div>
    
    <script>
        // Your existing JavaScript remains exactly the same
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const overlay = document.getElementById('overlay');
            const closeButton = document.querySelector('.sidebar-close');
            
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('active');
                overlay.classList.toggle('active');
            });
            
            closeButton.addEventListener('click', function() {
                sidebar.classList.remove('active');
                mainContent.classList.remove('active');
                overlay.classList.remove('active');
            });
            
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                mainContent.classList.remove('active');
                overlay.classList.remove('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(event.target) && 
                    !menuToggle.contains(event.target) && 
                    sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
            
            // Adjust on resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>