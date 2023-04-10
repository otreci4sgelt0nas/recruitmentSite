<?php
include('config.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form has been submitted
if (isset($_POST['submit'])) {
    // Get form data
    $address = $_POST['address'];
    $from_date = $_POST['from_date'];

    // Insert data into database
    $sql = "INSERT INTO address_history (address, from_date) VALUES ('$address', '$from_date')";
    if ($conn->query($sql) === TRUE) {
        echo "Address history added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Address History Form</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Address History Form</h2>
        <form id="address-history-form" method="POST">
            <div class="form-group" id="address-fields">
                <label>Address:</label>
                <input type="text" class="form-control" name="address[]" required>
                <label>From Date:</label>
                <input type="date" class="form-control" name="from_date[]" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit" id="submit-btn" disabled>Submit</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Add new address field if less than 7 years
            $('#address-history-form').on('change', 'input[type="date"]', function() {
                var fromDate = new Date($(this).val());
                var currentDate = new Date();
                var diffYears = Math.abs(currentDate.getFullYear() - fromDate.getFullYear());
                var newField = '';
                if (diffYears < 7) {
                    newField += '<div class="form-group">';
                    newField += '<label>Address:</label>';
                    newField += '<input type="text" class="form-control" name="address[]" required>';
                    newField += '<label>From Date:</label>';
                    newField += '<input type="date" class="form-control" name="from_date[]" required>';
                    newField += '</div>';
                    $('#address-fields').append(newField);
                }

                // Enable submit button if more than 7 years
                if (diffYears >= 7) {
                    $('#submit-btn').prop('disabled', false);
                } else {
                    $('#submit-btn').prop('disabled', true);
                }
            });
        });
    </script>
</body>
</html>
