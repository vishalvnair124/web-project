<?php
// Include necessary scripts or start session if needed
session_start();
// Fetch data from the database or any source
$requestsCount = 500; // Example data
$testimonialsCount = 325; // Example data
$usersCount = 1500; // Example data
$completionRate = 85; // Percent value
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../adminstyles/dashboard.css"> <!-- External CSS file -->
</head>
<body>

    <div class="box-container">
        <div class="box box1" data-count="<?= $requestsCount ?>">
            <div class="text">
                <h2 class="topic-heading" id="requests-count">0</h2>
                <h2 class="topic">Requests</h2>
            </div>
            <img src="../media/request.png" alt="Requests">
        </div>

        <div class="box box2" data-count="<?= $testimonialsCount ?>">
            <div class="text">
                <h2 class="topic-heading" id="testimonials-count">0</h2>
                <h2 class="topic">Testimonials</h2>
            </div>
            <img src="../media/testimonial.png" alt="Testimonials">
        </div>

        <div class="box box3" data-count="<?= $usersCount ?>">
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
        </div>
    </div>

    <div class="report-container">
        <div class="report-header">
            <h1 class="recent-Articles">Recent Requests</h1>
            <button class="view">View All</button>
        </div>

        <div class="report-body">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Name</th>
                        <th>Blood Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>R001</td>
                        <td>John Doe</td>
                        <td>O+</td>
                        <td class="label-tag Pending">Pending</td>
                    </tr>
                    <tr>
                        <td>R002</td>
                        <td>Jane Smith</td>
                        <td>A-</td>
                        <td class="label-tag Completed">Completed</td>
                    </tr>
                    <tr>
                        <td>R003</td>
                        <td>Alice Johnson</td>
                        <td>B+</td>
                        <td class="label-tag Pending">Pending</td>
                    </tr>
                    <tr>
                        <td>R004</td>
                        <td>Mike Lee</td>
                        <td>AB-</td>
                        <td class="label-tag Completed">Completed</td>
                    </tr>
                    <tr>
                        <td>R005</td>
                        <td>Sophia Brown</td>
                        <td>O-</td>
                        <td class="label-tag Pending">Pending</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Optional JavaScript for count-up animation -->
    <script>
        const counters = document.querySelectorAll(".topic-heading");

        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.parentElement.parentElement.getAttribute('data-count').replace('%', '');
                const current = +counter.innerText.replace('%', '');
                const increment = Math.ceil(target / 100);

                if (current < target) {
                    counter.innerText = current + increment + (counter.id === "completion-rate" ? "%" : "");
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target + (counter.id === "completion-rate" ? "%" : "");
                }
            };
            updateCount();
        });
    </script>

</body>
</html>
