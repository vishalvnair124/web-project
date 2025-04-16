<?php
include '../common/connection.php';

$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM users LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>{$row['user_gender']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['blood_group']}</td>
            <td>";
        if ($row['user_status'] == 1) {
            echo '<span class="badge badge-active">Active</span>';
        } elseif ($row['user_status'] == 2) {
            echo '<span class="badge badge-temp">Temp Block</span>';
        } else {
            echo '<span class="badge badge-blocked">Blocked</span>';
        }
        echo "</td>
            <td class='action-buttons'>
                <form method='POST' action='update_user_status.php'>
                    <input type='hidden' name='user_id' value='{$row['user_id']}'><div class='actionButtonsCollection'>";
                    
                    echo ($row['user_status'] != 0)?"<button class='btn-block' name='block'>Block</button>":"";
                    echo ($row['user_status'] != 2)?"<button class='btn-temp' name='temp'>Temp Block</button>":"";
                    echo ($row['user_status'] != 1)?"<button class='btn-activate' name='activate'>Activate</button>":"";
                echo"</div></form>
            </td>
        </tr>";
    }
} else {
    echo "NO_MORE";
}
