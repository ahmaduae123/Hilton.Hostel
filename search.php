<?php
session_start();
require 'db.php';

$filters = [];
$query = "SELECT * FROM trips WHERE 1=1";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    if (!empty($_POST['destination'])) {
        $query .= " AND destination LIKE ?";
        $filters[] = "%" . $_POST['destination'] . "%";
    }
    if (!empty($_POST['max_price'])) {
        $query .= " AND price <= ?";
        $filters[] = $_POST['max_price'];
    }
    if (!empty($_POST['travel_type'])) {
        $query .= " AND travel_type = ?";
        $filters[] = $_POST['travel_type'];
    }
}
$stmt = $pdo->prepare($query);
$stmt->execute($filters);
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>window.location.href='login.php';</script>";
    } else {
        $trip_id = $_POST['trip_id'];
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, trip_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $trip_id]);
        $to = $_SESSION['email'];
        $subject = "Booking Confirmation";
        $message = "Your booking for trip ID $trip_id has been confirmed.";
        mail($to, $subject, $message);
        echo "<script>alert('Booking confirmed! Check your email.'); window.location.href='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Trips - Hilton Hostel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(to right, #f4f7f9, #e0e7ff);
        }
        header {
            background: #1a3c34;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .search-section {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .search-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .search-form input, .search-form select {
            padding: 10px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(33% - 20px);
        }
        .btn {
            background: #ff6f61;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #e55a50;
        }
        .trip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .trip-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .trip-card:hover {
            transform: translateY(-5px);
        }
        .trip-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .trip-card h3 {
            padding: 15px;
            font-size: 24px;
            color: #1a3c34;
        }
        .trip-card p {
            padding: 0 15px 15px;
            color: #555;
        }
        @media (max-width: 768px) {
            .search-form input, .search-form select {
                width: 100%;
                margin: 10px 0;
            }
            .trip-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="javascript:window.location.href='index.php'">Home</a>
            <a href="javascript:window.location.href='search.php'">Search Trips</a>
            <a href="javascript:window.location.href='guide.php'">Travel Guide</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="javascript:window.location.href='dashboard.php'">Dashboard</a>
                <a href="javascript:window.location.href='logout.php'">Logout</a>
            <?php else: ?>
                <a href="javascript:window.location.href='login.php'">Login</a>
                <a href="javascript:window.location.href='signup.php'">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
    <section class="search-section">
        <form class="search-form" method="POST">
            <input type="text" name="destination" placeholder="Destination">
            <input type="number" name="max_price" placeholder="Max Price">
            <select name="travel_type">
                <option value="">Travel Type</option>
                <option value="solo">Solo</option>
                <option value="group">Group</option>
                <option value="family">Family</option>
                <option value="adventure">Adventure</option>
            </select>
            <button type="submit" name="search" class="btn">Search</button>
        </form>
        <div class="trip-grid">
            <?php foreach ($trips as $trip): ?>
                <div class="trip-card">
                    <img src="<?php echo htmlspecialchars($trip['image']); ?>" alt="<?php echo htmlspecialchars($trip['destination']); ?>">
                    <h3><?php echo htmlspecialchars($trip['destination']); ?></h3>
                    <p><?php echo htmlspecialchars($trip['description']); ?></p>
                    <p><strong>$<?php echo number_format($trip['price'], 2); ?></strong> - <?php echo $trip['duration']; ?> days</p>
                    <form method="POST">
                        <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
                        <button type="submit" name="book" class="btn">Book Now</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>
