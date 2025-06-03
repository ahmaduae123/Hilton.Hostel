<?php
session_start();
require 'db.php';

$stmt = $pdo->query("SELECT * FROM trips LIMIT 3");
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hilton Hostel - Discover Your Next Adventure</title>
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
        .hero {
            text-align: center;
            padding: 50px 20px;
            background: url('https://source.unsplash.com/random/1600x900/?travel') no-repeat center/cover;
            color: white;
        }
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .hero p {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .btn {
            background: #ff6f61;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
        }
        .btn:hover {
            background: #e55a50;
        }
        .trending {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .trending h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 36px;
            color: #1a3c34;
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
            .hero h1 {
                font-size: 32px;
            }
            .hero p {
                font-size: 18px;
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
            <a href="index.php">Home</a>
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
    <section class="hero">
        <h1>Discover Your Next Adventure</h1>
        <p>Explore breathtaking destinations with Hilton Hostel</p>
        <a href="javascript:window.location.href='search.php'" class="btn">Find Trips</a>
    </section>
    <section class="trending">
        <h2>Trending Destinations</h2>
        <div class="trip-grid">
            <?php foreach ($trips as $trip): ?>
                <div class="trip-card">
                    <img src="<?php echo htmlspecialchars($trip['image']); ?>" alt="<?php echo htmlspecialchars($trip['destination']); ?>">
                    <h3><?php echo htmlspecialchars($trip['destination']); ?></h3>
                    <p><?php echo htmlspecialchars($trip['description']); ?></p>
                    <p><strong>$<?php echo number_format($trip['price'], 2); ?></strong> - <?php echo $trip['duration']; ?> days</p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>
