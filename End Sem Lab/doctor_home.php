<?php
session_start();

$doctorId = $_SESSION['uid'] ?? 'abhi'; // Fallback for testing
$doctorXMLFile = 'data/doctor_data.xml';
$patientsXMLFile = 'data/patient_data.xml';

$doctorsXML = simplexml_load_file($doctorXMLFile) or die("Failed to load doctor XML.");
$patientsXML = simplexml_load_file($patientsXMLFile) or die("Failed to load patient XML.");

// Get logged-in doctor
$doctor = null;
foreach ($doctorsXML->entry as $entry) {
    if ((string)$entry->id === $doctorId) {
        $doctor = $entry;
        break;
    }
}
if (!$doctor) {
    die("Doctor not found.");
}

// Update doctor availability
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['availability_time'])) {
        $doctor->available = ($_POST['available'] == 'yes') ? "Available" : "Not Available";
        $doctor->availability_time = ($_POST['availability_time']) ?: "N/A";
        $doctorsXML->asXML($doctorXMLFile);
        header("Location: doctor_home.php");
        exit;
    }

    // Update specific patient
    if (isset($_POST['update_patient'])) {
        $pid = $_POST['patient_id'];
        foreach ($patientsXML->entry as $patient) {
            if ((string)$patient->id === $pid) {
                $patient->nurse_assigned = $_POST['nurse_assigned'];
                $patient->treatements = $_POST['treatements'];
                break;
            }
        }
        $patientsXML->asXML($patientsXMLFile);
        header("Location: doctor_home.php");
        exit;
    }
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
    <title>Doctor Dashboard</title>
    <style>
        body { font-family: Arial; background: #f8f8f8; padding: 30px; }
        .container { background: white; padding: 25px; border-radius: 10px; max-width: 1100px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; }
        label { font-weight: bold; }
        .btn { padding: 8px 16px; background: #007bff; border: none; color: white; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; }
        input[type="text"] { width: 95%; padding: 5px; }

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
    <button class="logout-btn" onclick="window.location.href='?logout=true'">Logout</button>

    <div class="container">
        <h2>Welcome, Dr. <?= htmlspecialchars($doctor->name) ?></h2>

        <form method="POST">
            <p><strong>Current Availability:</strong> <?= htmlspecialchars($doctor->available) ?></p>
            <p><strong>Availability Time:</strong> <?= htmlspecialchars($doctor->availability_time) ?></p>

            <label>Change Availability:</label><br>
            <input type="radio" name="available" value="yes" <?= ($doctor->available == "Available") ? "checked" : "" ?>> Available
            <input type="radio" name="available" value="no" <?= ($doctor->available == "Not Available") ? "checked" : "" ?>> Not Available
            <br><br>

            <label>Edit Availability Time (e.g., 10:00 - 15:00):</label><br>
            <input type="text" name="availability_time" value="<?= htmlspecialchars($doctor->availability_time) ?>"><br><br>

            <button class="btn" type="submit">Save Changes</button>
        </form>

        <div id="chat">
            <h3>Live Consultation Chat</h3>
            <div id="chat-box" style="height:200px;overflow:auto;border:1px solid #ccc;padding:10px;"></div>
            <input type="text" id="msg" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>

        <script>
            const socket = new WebSocket("ws://localhost:8080"); // Adjust port if needed
            const chatBox = document.getElementById('chat-box');

            socket.onmessage = function(event) {
                // Check if the message is a Blob (binary data)
                if (event.data instanceof Blob) {
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        const textMessage = reader.result; // This will be a string
                        const message = document.createElement('div');
                        message.textContent = textMessage;
                        chatBox.appendChild(message);
                        chatBox.scrollTop = chatBox.scrollHeight;
                    };
                    reader.readAsText(event.data);
                } else {
                    // Handle text messages
                    const message = document.createElement('div');
                    message.textContent = event.data;
                    chatBox.appendChild(message);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            };

            function sendMessage() {
                const msg = document.getElementById('msg').value;
                socket.send("<?= $_SESSION['uid'] ?>: " + msg);
                document.getElementById('msg').value = '';
            }
        </script>

        <h3>Patient Records (Editable)</h3>
        <table>
            <tr>
                <th>ID</th>
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
                        <td>
                            <input type="text" name="nurse_assigned" value="<?= htmlspecialchars($patient->nurse_assigned) ?>">
                        </td>
                        <td>
                            <input type="text" name="treatements" value="<?= htmlspecialchars($patient->treatements) ?>">
                        </td>
                        <td>
                            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient->id) ?>">
                            <button class="btn" type="submit" name="update_patient">Update</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
