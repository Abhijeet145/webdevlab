<?php
session_start();

$nurseId = $_SESSION['uid'] ?? 'pam_beesly'; // Fallback for testing
$nurseXMLFile = 'data/nurse_data.xml';
$patientsXMLFile = 'data/patient_data.xml';

$nursesXML = simplexml_load_file($nurseXMLFile) or die("Failed to load nurse XML.");
$patientsXML = simplexml_load_file($patientsXMLFile) or die("Failed to load patient XML.");

// Get logged-in nurse
$nurse = null;
foreach ($nursesXML->entry as $entry) {
    if ((string)$entry->id === $nurseId) {
        $nurse = $entry;
        break;
    }
}
if (!$nurse) {
    die("Nurse not found.");
}

// Handle treatment update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_treatement'])) {
    $pid = $_POST['patient_id'];
    foreach ($patientsXML->entry as $patient) {
        if ((string)$patient->id === $pid && (string)$patient->nurse_assigned === $nurse->name) {
            $patient->treatements = $_POST['treatements'];
            break;
        }
    }
    $patientsXML->asXML($patientsXMLFile);
    header("Location: nurse_home.php");
    exit;
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php"); // Redirect to the login page after logout
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nurse Dashboard</title>
    <style>
        body { font-family: Arial; background: #f8f8f8; padding: 30px; }
        .container { background: white; padding: 25px; border-radius: 10px; max-width: 1100px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; }
        input[type="text"] { width: 90%; padding: 5px; }
        .btn { padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #218838; }
        .readonly { color: gray; }

        /* Style for logout button */
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <!-- Logout Button -->
    <button class="logout-btn" onclick="window.location.href='?logout=true'">Logout</button>

    <div class="container">
        <h2>Welcome, Nurse <?= htmlspecialchars($nurse->name) ?></h2>

        <h3>All Patient Records</h3>
        <table>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Medical History</th>
                <th>Nurse Assigned</th>
                <th>Treatments</th>
                <th>Action</th>
            </tr>
            <?php foreach ($patientsXML->entry as $patient): ?>
                <tr>
                    <form method="POST">
                        <td><?= htmlspecialchars($patient->id) ?></td>
                        <td><?= htmlspecialchars($patient->name) ?></td>
                        <td><?= htmlspecialchars($patient->email) ?></td>
                        <td><?= htmlspecialchars($patient->medical_history) ?></td>
                        <td><?= htmlspecialchars($patient->nurse_assigned) ?></td>
                        <td>
                            <?php if ((string)$patient->nurse_assigned === (string)$nurse->name): ?>
                                <input type="text" name="treatements" value="<?= htmlspecialchars($patient->treatements) ?>">
                            <?php else: ?>
                                <span class="readonly"><?= htmlspecialchars($patient->treatements) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((string)$patient->nurse_assigned === (string)$nurse->name): ?>
                                <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient->id) ?>">
                                <button class="btn" type="submit" name="update_treatement">Update</button>
                            <?php else: ?>
                                <span class="readonly">Not Assigned</span>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
