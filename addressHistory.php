<?php
include('config.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form has been submitted
if (isset($_POST['submit'])) {
    // Get form data
    $addresses = $_POST['address'];
    $from_dates = $_POST['from_date'];

    // Insert data into database
    $sql_values = '';
    foreach ($addresses as $key => $address) {
        $from_date = $from_dates[$key];
        $sql_values .= "('email@mail.com', '$address', '$from_date'), ";
    }
    $sql_values = rtrim($sql_values, ', ');
    $sql = "INSERT INTO address_history (email, address, from_date) VALUES $sql_values";
    if ($conn->query($sql) === TRUE) {
        echo "Address history added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Get all previously saved addresses from database
$sql = "SELECT * FROM address_history";
$result = $conn->query($sql);
$addresses = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIx3cN-voKQtY3MqfIvYErJwtwf0wUVfE&libraries=places"></script>
</head>
<body>
    <div class="container">
        <h2>Address History Form</h2>
        <form id="address-history-form" method="POST">
            <?php
            $i = 0;
            while ($i < count($addresses)) {
                $address = $addresses[$i];
            ?>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" class="form-control address" name="address[]" value="<?php echo $address['address']; ?>" required>
                <label>From Date:</label>
                <input type="date" class="form-control" name="from_date[]" value="<?php echo $address['from_date']; ?>" required>
            </div>
            <?php
                $i++;
            }
            ?>
            <div class="form-group" id="new-address-fields">
                <label>Address:</label>
                <input type="text" class="form-control address" name="address[]" required>
                <label>From Date:</label>
                <input type="date" class="form-control" name="from_date[]" required>
            </div>
            <div class="form-group" id="submit-btn-group">
                <button type="submit" class="btn btn-primary" name="submit" id="submit-btn" disabled>Submit</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Add new address field if less than 7 years
            $('#address-history-form').on('change', 'input[name="from_date[]"]', function() {
                var current_date = new Date();
                var from_date = new Date($(this).val());
                var diff_years = (current_date - from_date) / (1000 * 3600 * 24 * 365);
                if (diff_years < 7) {
                    var new_field = $('#new-address-fields').clone();
                    new_field.find('input').val('');
                    new_field.find('.address').each(function() {
                        var autocomplete = new google.maps.places.Autocomplete(this);
                    });
                    $('#submit-btn-group').before(new_field);
                } else {
                    $('#submit-btn').prop('disabled', false);
                }
            });

            // Initialize autocomplete for existing address fields
            $('.address').each(function() {
                var autocomplete = new google.maps.places.Autocomplete(this);
            });
        });
    </script>
</body>
</html>
