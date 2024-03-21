<?php

function loademployees()
{
    $xml = simplexml_load_file('employees.xml');
    return $xml;
}

function saveemployees($xml)
{
    $xml->asXML('employees.xml');
}

function displayemployee($employee)
{
    echo "<div class='card'>";
    echo "<div class='card-body'>";
    echo "<h2>employee Details</h2>";
    echo "Name: {$employee->name}<br>";
    echo "Phone: {$employee->phone}<br>";
    echo "Address: {$employee->address}<br>";
    echo "Email: {$employee->email}<br>";
    echo "</div>";
    echo "</div>";
    echo "<br>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $employees = loademployees();
    if (isset($_POST['index']))
        $index = (int)$_POST['index'];

    switch ($action) {
        case 'insert':
            $newemployee = $employees->addChild('employee');
            $newemployee->addChild('name', $_POST['name']);
            $newemployee->addChild('phone', $_POST['phone']);
            $newemployee->addChild('address', $_POST['address']);
            $newemployee->addChild('email', $_POST['email']);
            break;
        case 'update':
            if (isset($employees->employee[$index])) {
                $employees->employee[$index]->name = $_POST['name'];
                $employees->employee[$index]->phone = $_POST['phone'];
                $employees->employee[$index]->address = $_POST['address'];
                $employees->employee[$index]->email = $_POST['email'];
            }
            break;
        case 'delete':
            unset($employees->employee[$index]);
            if (count($employees->employee) > 1)
                $index = ($index + 1) % count($employees->employee);
            break;
        case 'search':
            $searchTerm = $_POST['search_term'];
            $foundemployees = array();
            foreach ($employees->employee as $employee) {
                if (stripos($employee->name, $searchTerm) !== false) {
                    $foundemployees[] = $employee;
                }
            }
            echo "<h2>Search Results</h2>";
            if (!empty($foundemployees)) {
                foreach ($foundemployees as $foundemployee) {
                    displayemployee($foundemployee);
                    echo "<br>";
                }
            } else {
                echo "<p>No employee found With Name '{$searchTerm}'.</p>";
            }
            exit;
            break;
        case 'next':
            $index = $index + 1;
            break;
        case 'prev':
            $index = $index - 1;
            break;
    }

    saveemployees($employees);

    header("Location: {$_SERVER['PHP_SELF']}?index=$index");
    exit;
}

$employees = loademployees();
$totalemployees = count($employees->employee);

if (isset($_GET['index'])) {
    $index = (int)$_GET['index'];
} else {
    $index = 0;
}

$employee = isset($employees->employee[$index]) ? $employees->employee[$index] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Management</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    .content {
        margin-top: 50px;
    }
</style>
</head>
<body>

<div class="container">
    <div class="card content">

    <div class="card-header">
    <h1 class="mt-4 mb-4">Employee Management</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="employeeForm" method='post' class="mb-4">
                <input type='hidden' name='index' value='<?php echo $index; ?>'>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type='text' class="form-control" name='name' placeholder='Name' value='<?php echo $employee ? $employee->name : ''; ?>'>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type='text' class="form-control" name='phone' placeholder='Phone' value='<?php echo $employee ? $employee->phone : ''; ?>'>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type='text' class="form-control" name='address' placeholder='Address' value='<?php echo $employee ? $employee->address : ''; ?>'>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type='text' class="form-control" name='email' placeholder='Email' value='<?php echo $employee ? $employee->email : ''; ?>'>
                </div>
                <button type='submit' class="btn btn-success" name='action' value='insert'>Insert</button>
                <button type='submit' class="btn btn-info" name='action' value='update'>Update</button>
                <button type='submit' class="btn btn-danger" name='action' value='delete'>Delete</button>
                <button type="submit" class="btn btn-secondary" name="action" value="prev" <?php echo $index == 0 || $totalemployees == 0 ? 'disabled' : ''; ?>>Prev</button>
                <button type="submit" class="btn btn-secondary" name="action" value="next" <?php echo $index == $totalemployees - 1 || $totalemployees == 0 ? 'disabled' : ''; ?>>Next</button>
            </form>
        </div>
    </div>
    </div>
    <br>
    <form id="searchForm" method='post' class="mb-4">
        <div class="form-group">
            <input type='text' class="form-control" name='search_term' placeholder='Search by name'>
        </div>
        <button type='submit' class="btn btn-primary" name='action' value='search'>Search</button>
    </form>

    <?php
    if ($employee) {
        displayemployee($employee);
    } else {
        echo "<p>No employee found.</p>";
    }
    ?>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
