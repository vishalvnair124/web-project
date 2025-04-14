<?php
include '../common/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="report-container">
    <div class="report-header">
        <h1 class="recent-Articles">Recent Requests</h1>
    </div>
    <?php
    $requestsSQL = "SELECT blood_requests.*,users.name FROM `blood_requests` INNER JOIN users ON users.user_id=blood_requests.recipient_id WHERE blood_requests.request_status=1 OR blood_requests.request_status=3 ORDER BY request_id DESC ";
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
                            <td class="label-tag" style='background-color:#075E54'><a href="whatsapp://send?text=🩸*Blood%20Needed*🩸%0ABlood%20Group:%20<?= urlencode($request['blood_group']) ?>%0AHospital:%20<?= urlencode($request['hospital_name']) ?>%0APlace:%20<?= urlencode($request['place']) ?>" style='text-decoration:none; color:white;'>Share</whatsapp:>
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