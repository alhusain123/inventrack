<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow">

        <h2 class="text-center mb-4">Student Registration Form</h2>

        <!-- FORM START -->
        <form method="POST" action="process.php">

            <!-- Student ID -->
            <div class="mb-3">
                <label>Student ID</label>
                <input type="text" name="student_id" class="form-control">
            </div>

            <!-- Full Name -->
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control">
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <!-- Age -->
            <div class="mb-3">
                <label>Age</label>
                <input type="number" name="age" class="form-control">
            </div>

            <!-- Course -->
            <div class="mb-3">
                <label>Course</label>
                <select name="course" class="form-control">
                    <option value="">Select Course</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                    <option value="BSIS">BSIS</option>
                </select>
            </div>

            <!-- Gender -->
            <div class="mb-3">
                <label>Gender</label><br>
                <input type="radio" name="gender" value="Male"> Male
                <input type="radio" name="gender" value="Female"> Female
            </div>

            <!-- Terms -->
            <div class="mb-3">
                <input type="checkbox" name="terms"> I agree to the Terms
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100">
                Register
            </button>

        </form>
        <!-- FORM END -->

    </div>
</div>

</body>
</html>