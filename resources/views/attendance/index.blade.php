<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Student Attendance Records</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Class ID</th>
                    <th>Section ID</th>
                    <th>Student ID</th>
                    <th>Student BioID</th>
                    <th>Status</th>
                    <th>Session ID</th>
                    <th>School ID</th>
                    <th>Device ID</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($studentAttendances as $attendance): ?>
                <tr>
                    <td><?= htmlspecialchars($attendance->id) ?></td>
                    <td><?= htmlspecialchars($attendance->class_id) ?></td>
                    <td><?= htmlspecialchars($attendance->section_id) ?></td>
                    <td><?= htmlspecialchars($attendance->student_id) ?></td>
                    <td><?= htmlspecialchars($attendance->stu_bioid) ?></td>
                    <td><?= htmlspecialchars($attendance->status) ?></td>
                    <td><?= htmlspecialchars($attendance->session_id) ?></td>
                    <td><?= htmlspecialchars($attendance->school_id) ?></td>
                    <td><?= htmlspecialchars($attendance->device_id) ?></td>
                    <td><?= htmlspecialchars($attendance->stu_intime) ?></td>
                    <td><?= htmlspecialchars($attendance->stu_outtime) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 class="text-center mt-5">Employee Attendance Records</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                   <!-- <th>Employee ID</th>-->
                    <th>Employee Role</th>
                    <th>Employee BioID</th>
                    <th>School ID</th>
                    <th>Device ID</th>
                    <th>Session</th>
                    <th>Status</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                 <!--   <th>Punch Status</th>-->
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach($employeeAttendances as $attendance): ?>
                <tr>
                    <td><?= htmlspecialchars($attendance->id) ?></td>
                   <!--   <td><?= htmlspecialchars($attendance->user_id) ?></td>-->
                    <td><?= htmlspecialchars($attendance->role_id) ?></td>
                    <td><?= htmlspecialchars($attendance->emp_bioid) ?></td>
                    <td><?= htmlspecialchars($attendance->school_id) ?></td>
                    <td><?= htmlspecialchars($attendance->device_id) ?></td>
                    <td><?= htmlspecialchars($attendance->session_id) ?></td>
                    <td><?= htmlspecialchars($attendance->status) ?></td>
                    <td><?= htmlspecialchars($attendance->emp_intime) ?></td>
                    <td><?= htmlspecialchars($attendance->emp_outtime) ?></td>
                 <!--   <td><?= htmlspecialchars($attendance->punchstatus) ?></td>  -->
                   
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
