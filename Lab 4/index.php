<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Complaint Portal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2>Maintenance Complaint Form</h2>
    <form id="complaintForm" method="POST" action="submit_complaint.php" >
        
        <label for="empId">Employee ID or Roll No:</label>
        <input type="text" id="empId" name="empId" required>

        <label for="fullName">Full Name (First and Last):</label>
        <input type="text" id="fullName" name="fullName" required>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" pattern="\d{10}" required>

        <label for="email">Email ID:</label>
        <input type="email" id="email" name="email" required>

        <label for="complaintType">Complaint Type:</label>
        <select id="complaintType" name="complaintType" required>
            <option value="Electrical">Electrical</option>
            <option value="Plumbing">Plumbing</option>
            <option value="Air Conditioning">Air Conditioning</option>
            <option value="General">General</option>
        </select>

        <label for="description">Brief Description of Complaint:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <label for="token">Generated Token:</label>
        <input type="text" id="token" name="token" readonly>
        <!-- <input type='submit' name='submit' value='submit'> -->
        <button type="submit" name = "submit" value="submit" class="btn">Submit Complaint</button>
    </form>
    

    <!-- <button class="btn" onclick="checkStatus()">Check Complaint Status</button> -->
</div>

<script>
    function validateForm() {
        let empId = document.getElementById('empId').value;
        let fullName = document.getElementById('fullName').value;
        let phone = document.getElementById('phone').value;
        let email = document.getElementById('email').value;
        let complaintType = document.getElementById('complaintType').value;
        let description = document.getElementById('description').value;

        if (empId === "") {
            alert("Employee ID or Roll No. is required.");
            return false;
        }

        let namePattern = /^[a-zA-Z]+ [a-zA-Z]+$/;
        if (!namePattern.test(fullName)) {
            alert("Please enter a valid Full Name (First and Last).");
            return false;
        }

        let phonePattern = /^\d{10}$/;
        if (!phonePattern.test(phone)) {
            alert("Please enter a valid 10-digit phone number.");
            return false;
        }

        let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        if (description === "") {
            alert("Brief Description of the complaint is required.");
            return false;
        }

        document.getElementById('token').value = "TKN" + Math.floor(Math.random() * 100000);

        alert("Complaint submitted successfully!");

        return false;
    }

    function checkStatus() {
        alert("This feature will check the complaint status (functionality pending).");
    }
</script>

</body>
</html>
