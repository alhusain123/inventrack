<?php

// cleans forms after 
$student_id = htmlspecialchars($_POST['student_id'] ?? '');
$full_name  = htmlspecialchars($_POST['full_name'] ?? '');
$email      = htmlspecialchars($_POST['email'] ?? '');
$age        = htmlspecialchars($_POST['age'] ?? '');
$course     = htmlspecialchars($_POST['course'] ?? '');
$gender     = htmlspecialchars($_POST['gender'] ?? '');
$terms      = isset($_POST['terms']) ? htmlspecialchars($_POST['terms']) : '';

$errors = [];

// checks required field if inputs are correct
if (empty($student_id)) {
    $errors[] = "Student ID is required.";
}

if (empty($full_name)) {
    $errors[] = "Full Name is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (empty($age)) {
    $errors[] = "Age is required.";
}

if (empty($course)) {
    $errors[] = "Course is required.";
}

if (empty($gender)) {
    $errors[] = "Gender is required.";
}

if (empty($terms)) {
    $errors[] = "You must accept the terms and conditions.";
}

// checks for user being minor or not
if ($age < 18) {
    $age_status = "Minor";
} else {
    $age_status = "Adult";
}

// converts into full course code
switch ($course) {
    case "BSIT":
        $course_full = "Bachelor of Science in Information Technology";
        break;

    case "BSCS":
        $course_full = "Bachelor of Science in Computer Science";
        break;

    case "BSIS":
        $course_full = "Bachelor of Science in Information Systems";
        break;

    default:
        $course_full = "Unknown Course";
}

// Function about making a welcome message for name
function greetStudent($name) {
    return "Welcome, " . $name . "!";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow mx-auto" style="max-width: 700px;">

        <h2 class="text-center mb-4">Registration Result</h2>

        <?php if (!empty($errors)): ?>

            <div class="alert alert-danger">
                <h5>Please fix the following errors:</h5>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <a href="index.php" class="btn btn-secondary">Go Back</a>

        <?php else: ?>

            <div class="alert alert-success">
                <h4><?php echo greetStudent($full_name); ?></h4>
                <p>Your registration details were processed successfully.</p>
            </div>

            <table class="table table-bordered">
                <tr>
                    <th>Student ID</th>
                    <td><?php echo $student_id; ?></td>
                </tr>

                <tr>
                    <th>Full Name</th>
                    <td><?php echo $full_name; ?></td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td><?php echo $email; ?></td>
                </tr>

                <tr>
                    <th>Age</th>
                    <td><?php echo $age; ?></td>
                </tr>

                <tr>
                    <th>Age Status</th>
                    <td><?php echo $age_status; ?></td>
                </tr>

                <tr>
                    <th>Course</th>
                    <td><?php echo $course_full; ?></td>
                </tr>

                <tr>
                    <th>Gender</th>
                    <td><?php echo $gender; ?></td>
                </tr>

                <tr>
                    <th>Terms</th>
                    <td><?php echo $terms; ?></td>
                </tr>
            </table>

            <a href="index.php" class="btn btn-primary">Register Again</a>

        <?php endif; ?>

    </div>
</div>

</body>
</html>