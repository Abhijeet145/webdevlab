<?php
session_start();

$patientId = $_SESSION['uid'] ?? 'michael_scott';
$patientsXML = simplexml_load_file("data/patient_data.xml") or die("Unable to load patient data.");
$doctorsXML = simplexml_load_file("data/doctor_data.xml") or die("Unable to load doctor data.");
$appointmentsXMLFile = "data/appointments.xml";

// Find current patient
$patient = null;
foreach ($patientsXML->entry as $entry) {
    if ((string)$entry->id === $patientId) {
        $patient = $entry;
        break;
    }
}
if (!$patient) {
    die("Patient not found.");
}

// Handle booking
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['book'])) {
    $doctorId = $_POST['doctor_id'];
    $appointmentTime = $_POST['appointment_time'];
    $problem = trim($_POST['problem']);

    if (!file_exists($appointmentsXMLFile)) {
        $appointmentsXML = new SimpleXMLElement('<?xml version="1.0"?><appointments></appointments>');
    } else {
        $appointmentsXML = simplexml_load_file($appointmentsXMLFile);
    }

    $newEntry = $appointmentsXML->addChild("entry");
    $newEntry->addChild("patient_id", $patient->id);
    $newEntry->addChild("doctor_id", $doctorId);
    $newEntry->addChild("time", $appointmentTime);
    $newEntry->addChild("problem", htmlspecialchars($problem));
    $appointmentsXML->asXML($appointmentsXMLFile);

    header("Location: patient_home.php");
    exit;
}

// Load appointments
$appointmentsXML = file_exists($appointmentsXMLFile) ? simplexml_load_file($appointmentsXMLFile) : null;

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
    <title>Patient Dashboard</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .container { background: white; padding: 20px 30px; max-width: 1000px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; }
        .section { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f9f9f9; }
        .btn { padding: 6px 14px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #218838; }
        input[type="text"], textarea { padding: 6px; width: 90%; margin: 5px 0; }
        textarea { resize: vertical; height: 50px; }

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
        <h2>Welcome, <?= htmlspecialchars($patient->name) ?></h2>

        <div class="section">
            <h3>Your Medical Record</h3>
            <table>
                <tr><th>ID</th><td><?= htmlspecialchars($patient->id) ?></td></tr>
                <tr><th>Name</th><td><?= htmlspecialchars($patient->name) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($patient->email) ?></td></tr>
                <tr><th>Medical History</th><td><?= htmlspecialchars($patient->medical_history) ?></td></tr>
                <tr><th>Nurse Assigned</th><td><?= htmlspecialchars($patient->nurse_assigned) ?></td></tr>
                <tr><th>Treatments</th><td><?= htmlspecialchars($patient->treatements) ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h3>Your Upcoming Appointments</h3>
            <table>
                <tr>
                    <th>Doctor ID</th>
                    <th>Time</th>
                    <th>Problem</th>
                </tr>
                <?php if ($appointmentsXML): ?>
                    <?php foreach ($appointmentsXML->entry as $appt): ?>
                        <?php if ((string)$appt->patient_id === $patientId): ?>
                            <tr>
                                <td><?= htmlspecialchars($appt->doctor_id) ?></td>
                                <td><?= htmlspecialchars($appt->time) ?></td>
                                <td><?= htmlspecialchars($appt->problem) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No appointments found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- Live Consultation Chat -->
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

        <div class="section">
            <h3>Available Doctors</h3>
            <table>
                <tr>
                    <th>Doctor ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Availability</th>
                    <th>Availability Time</th>
                    <th>Book Appointment</th>
                </tr>
                <?php foreach ($doctorsXML->entry as $doc): ?>
                    <?php if ((string)$doc->available === "Available"): ?>
                        <tr>
                            <form method="POST">
                                <td><?= htmlspecialchars($doc->id) ?></td>
                                <td><?= htmlspecialchars($doc->name) ?></td>
                                <td><?= htmlspecialchars($doc->specialization) ?></td>
                                <td><?= htmlspecialchars($doc->available) ?></td>
                                <td><?= htmlspecialchars($doc->availability_time) ?></td>
                                <td>
                                    <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doc->id) ?>">
                                    <input type="text" name="appointment_time" placeholder="e.g., 11:00" required><br>
                                    <textarea name="problem" placeholder="Describe your problem" required></textarea><br>
                                    <button class="btn" type="submit" name="book">Book</button>
                                </td>
                            </form>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
