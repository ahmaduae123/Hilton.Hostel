<?php
session_start();
require 'db.php';

$stmt = $pdo->query("SELECT blog_posts.*, users.username FROM blog_posts JOIN users ON blog_posts.author_id = users.id");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_trip'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>window.location.href='login.php';</script>";
    } else {
        $trip_id = $_POST['trip_id'];
        $stmt = $pdo->prepare("INSERT INTO saved_destinations (user_id, trip_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $trip_id]);
        echo "<script>alert('Trip saved!'); window.location.href='guide.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Guide - Hilton Hostel</title>
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
        .guide-section {
            padding: 50px 20px;
            max-width: 1200px;
            margin: auto;
        }
        .post {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .post h3 {
            color: #1a3c34;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .post p {
            color: #555;
            margin-bottom: 10px;
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
        @media (max-width: 768px) {
            .guide-section {
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="javascript:window.location.href='dashboard.php'">Dashboard</a>
                <a href="javascript:window.location.href='logout.php'">Logout</a>
            <?php else: ?>
                <a href="javascript:window.location.href='login.php'">Login</a>
                <a href="javascript:window.location.href='signup.php'">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
    <section class="guide-section">
        <h2>Travel Guides & Tips</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <p><em>By <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></em></p>
                <form method="POST">
                    <input type="hidden" name="trip_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" name="save_trip" class="btn">Save Trip</button>
                </form>
            </div>
        <?php endforeach; ?>
    </section>
</body>
</html>
