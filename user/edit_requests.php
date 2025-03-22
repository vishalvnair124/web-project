<?php
// Include database connection
include '../common/connection.php';

$request_id = $_GET['id'];
$query = "SELECT * FROM blood_requests WHERE request_id = $request_id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<script>alert('Request not found!'); window.location.href='my_requests.php';</script>";
    exit();
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Get updated values
        $blood_group = $_POST['blood_group'];
        $request_units = $_POST['request_units'];
        $when_need_blood = $_POST['when_need_blood'];
        $hospital_name = $_POST['hospital_name'];
        $doctor_name = $_POST['doctor_name'];
        $additional_notes = $_POST['additional_notes'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        $sql = "UPDATE blood_requests SET 
                    blood_group='$blood_group', 
                    request_units='$request_units', 
                    when_need_bllood='$when_need_blood', 
                    hospital_name='$hospital_name', 
                    doctor_name='$doctor_name', 
                    additional_notes='$additional_notes', 
                    latitude='$latitude', 
                    longitude='$longitude'
                WHERE request_id = $request_id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Request updated successfully!'); window.location.href='my_requests.php';</script>";
        } else {
            echo "Error updating request: " . $conn->error;
        }
    }

    if (isset($_POST['delete'])) {
        $sql = "UPDATE blood_requests SET request_status = 6 WHERE request_id = $request_id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Request closed successfully!'); window.location.href='my_requests.php';</script>";
        } else {
            echo "Error updating request status: " . $conn->error;
        }
    }

    if (isset($_POST['re_request'])) {
        // Condition-based logic (Now blank)
    }
}

$conn->close();
?>

<link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->

<style>
    body {
        font-family: 'Arial', sans-serif;
        background: #F5F5F5;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .form-container {
        max-width: 700px;
        margin: auto;
        background: #FFFFFF;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        margin-top: 50px;
    }

    .form-container h2 {
        text-align: center;
        color: #D32F2F;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .form-container label {
        display: block;
        font-weight: bold;
        margin: 10px 0 5px;
        color: #444;
    }

    .form-container input,
    .form-container select,
    .form-container textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #DDDDDD;
        border-radius: 8px;
        font-size: 16px;
        background: #FAFAFA;
        transition: 0.3s ease-in-out;
    }

    .form-container input:focus,
    .form-container select:focus,
    .form-container textarea:focus {
        border-color: #D32F2F;
        background: #FFFFFF;
        outline: none;
    }

    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn {
        width: 32%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        transition: 0.3s ease-in-out;
    }

    .re-request-btn {
        background: #00796B;
        color: white;
    }

    .re-request-btn:hover {
        background: #004D40;
    }

    .update-btn {
        background: #1976D2;
        color: white;
    }

    .update-btn:hover {
        background: #0D47A1;
    }

    .delete-btn {
        background: #D32F2F;
        color: white;
    }

    .delete-btn:hover {
        background: #B71C1C;
    }
</style>


<div class="form-container">
    <div class="header">
        <h2>Edit Blood Request</h2>
        <a href="http://localhost/dropforlife/user/?page=requests.php" class="back-btn">Back</a>
    </div>


    <form method="POST">
        <label>Blood Group:</label>
        <select name="blood_group">
            <option value="A+" <?= $row['blood_group'] == "A+" ? "selected" : "" ?>>A+</option>
            <option value="A-" <?= $row['blood_group'] == "A-" ? "selected" : "" ?>>A-</option>
            <option value="B+" <?= $row['blood_group'] == "B+" ? "selected" : "" ?>>B+</option>
            <option value="B-" <?= $row['blood_group'] == "B-" ? "selected" : "" ?>>B-</option>
            <option value="O+" <?= $row['blood_group'] == "O+" ? "selected" : "" ?>>O+</option>
            <option value="O-" <?= $row['blood_group'] == "O-" ? "selected" : "" ?>>O-</option>
            <option value="AB+" <?= $row['blood_group'] == "AB+" ? "selected" : "" ?>>AB+</option>
            <option value="AB-" <?= $row['blood_group'] == "AB-" ? "selected" : "" ?>>AB-</option>
        </select>

        <label>Units Required:</label>
        <input type="number" name="request_units" min="1" value="<?= $row['request_units'] ?>">

        <label>When Needed:</label>
        <input type="datetime-local" name="when_need_blood" value="<?= date('Y-m-d\TH:i', strtotime($row['when_need_bllood'])) ?>">

        <label>Hospital Name:</label>
        <input type="text" name="hospital_name" value="<?= $row['hospital_name'] ?>">

        <label>Doctor Name:</label>
        <input type="text" name="doctor_name" value="<?= $row['doctor_name'] ?>">

        <label>Additional Notes:</label>
        <textarea name="additional_notes"><?= $row['additional_notes'] ?></textarea>

        <label>Latitude:</label>
        <input type="text" name="latitude" value="<?= $row['latitude'] ?>" readonly>

        <label>Longitude:</label>
        <input type="text" name="longitude" value="<?= $row['longitude'] ?>" readonly>

        <div class="btn-container">
            <button type="submit" name="re_request" class="btn re-request-btn"> Re-Request</button>
            <button type="submit" name="update" class="btn update-btn"> Update</button>
            <button type="submit" name="delete" class="btn delete-btn"> Close Request</button>
        </div>
    </form>
</div>