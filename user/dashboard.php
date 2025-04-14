<?php
include '../common/connection.php';
// Include necessary scripts or start session if needed
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch data from the database or any source
$requestsCount = $conn->query("SELECT * FROM `blood_requests` WHERE recipient_id=$user_id")->num_rows;
$testimonialsCount = $conn->query("SELECT * FROM `donor_notifications` WHERE donor_id=$user_id AND donor_notifications_status=5")->num_rows;
$usersCount = $conn->query("SELECT * FROM `users`")->num_rows; // Example data
$completionRate = 85; // Percent value

// Content of the dashboard
?>

<div class="box-container">
    <div class="box box1" data-count="<?= $requestsCount ?>">
        <div class="text">
            <h2 class="topic-heading" id="requests-count">0</h2>
            <h2 class="topic">My Requests</h2>
        </div>
        <img src="../media/request.png" alt="Requests">
    </div>

    <div class="box box2" data-count="<?= $testimonialsCount ?>">
        <div class="text">
            <h2 class="topic-heading" id="testimonials-count">0</h2>
            <h2 class="topic">My Donations</h2>
        </div>
        <img src="../media/testimonial.png" alt="Testimonials">
    </div>

    <!-- <div class="box box3" data-count="<?= $usersCount ?>">
        <div class="text">
            <h2 class="topic-heading" id="users-count">0</h2>
            <h2 class="topic">Users</h2>
        </div>
        <img src="../media/users.png" alt="Users">
    </div>

    <div class="box box4" data-count="<?= $completionRate ?>%">
        <div class="text">
            <h2 class="topic-heading" id="completion-rate">0%</h2>
            <h2 class="topic">Completion</h2>
        </div>
        <img src="../media/complete.png" alt="Completion">
    </div> -->
</div>

<div class="report-container">
    <div class="report-header">
        <h1 class="recent-Articles">Recent Requests</h1>
        <a href="?page=RecentRequests.php"><button class="view">View All</button></a>
    </div>

    <?php
    $requestsSQL = "SELECT blood_requests.*,users.name FROM `blood_requests` INNER JOIN users ON users.user_id=blood_requests.recipient_id WHERE blood_requests.request_status=1 OR blood_requests.request_status=3 ORDER BY request_id DESC LIMIT 5";
    $requests = $conn->query($requestsSQL);
    ?>

    <div class="report-body">
        <table>
            <thead>
                <tr>
                    <th>SI No</th>
                    <th>Name</th>
                    <th>Blood Type</th>
                    <th>Location</th>
                    <th>Hospital</th>
                    <th>Status</th>
                    <th>Share</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                if ($requests->num_rows > 0) {

                    while ($request = $requests->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $request['name'] ?></td>
                            <td><?= $request['blood_group'] ?></td>
                            <td><?= $request['place'] ?></td>
                            <td><?= $request['hospital_name'] ?></td>
                            <td class="label-tag">Pending</td>
                            <td class="label-tag" style='background-color:#075E54'><a href="whatsapp://send?text=🩸%20*Blood%20Needed*%20🩸%0ABlood%20Group:%20<?= urlencode($request['blood_group']) ?>%0AHospital:%20<?= urlencode($request['hospital_name']) ?>%0APlace:%20<?= urlencode($request['place']) ?>" style='text-decoration:none; color:white;'>Share</whatsapp:>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>