<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
}

$user_id = $_SESSION['user_id'];
$bookings = $pdo->prepare("SELECT bookings.*, trips.destination FROM bookings JOIN trips ON bookings.trip_id = trips.id WHERE bookings.user_id = ?");
$bookings->execute([$user_id]);
$bookings = $bookings->fetchAll(PDO::FETCH_ASSOC);

$saved = $pdo->prepare("SELECT saved_destinations.*, trips.destination FROM saved_destinations JOIN trips ON saved_destinations.trip_id = trips.id WHERE saved_destinations.user_id = ?");
$saved->execute([$user_id]);
$saved_destinations = $saved->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $trip_id = $_POST['trip_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, trip_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $trip_id, $rating, $comment]);
    echo "<script>alert('Review submitted!'); window.location.href='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hilton Hostel</title>
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
        .dashboard-section {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .dashboard-section h2 {
            color: #1a3c34;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        .review-form select, .review-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            .dashboard-section {
                padding: 20px;
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
            <a href="javascript:window.location.href='dashboard.php'">Dashboard</a>
            <a href="javascript:window.location.href='logout.php'">Logout</a>
        </nav>
    </header>
    <section class="dashboard-section">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div class="card">
            <h3>Your Bookings</h3>
            <?php foreach ($bookings as $booking): ?>
                <p><?php echo htmlspecialchars($booking['destination']); ?> - <?php echo $booking['booking_date']; ?> (<?php echo $booking['status']; ?>)</p>
            <?php endforeach; ?>
        </div>
        <div class="card">
            <h3>Saved Destinations</h3>
            <?php foreach ($saved_destinations as $saved): ?>
                <p><?php echo htmlspecialchars($saved['destination']); ?> - Saved on <?php echo $saved['saved_at']; ?></p>
            <?php endforeach; ?>
        </div>
        <div class="card">
            <h3>Submit a Review</h3>
            <form class="review-form" method="POST">
                <select name="trip_id" required>
                    <option value="">Select Trip</option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?php echo $booking['trip_id']; ?>"><?php echo htmlspecialchars($booking['destination']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="rating" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
                <textarea name="comment" placeholder="Your review" rows="4"></textarea>
                <button type="submit" name="submit_review" class="btn">Submit Review</button>
            </form>
        </div>
    </section>
</body>
</html>
