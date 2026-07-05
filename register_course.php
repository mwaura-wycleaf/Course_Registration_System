<?php
session_start();

if (!isset($_SESSION["Student_ID"])) {
    header("Location: login.php");
    exit();
}

require_once "config/db.php";

$studentID = $_SESSION["Student_ID"];
$success = "";
$error = "";
$registeredCount = 0;

/* ===========================
   GET LOGGED IN STUDENT
=========================== */

$studentSQL = "SELECT s.Student_ID,
                      s.First_Name,
                      s.Email,
                      s.Department_ID,
                      d.Department_Name,
                      d.Faculty
               FROM student s
               INNER JOIN department d
               ON s.Department_ID = d.Department_ID
               WHERE s.Student_ID = ?";

$stmt = mysqli_prepare($conn,$studentSQL);
mysqli_stmt_bind_param($stmt,"s",$studentID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

/* ===========================
   FETCH COURSES
=========================== */

$courseSQL = "SELECT *
              FROM course
              WHERE Department_ID=?
              ORDER BY Course_Code";

$stmt = mysqli_prepare($conn,$courseSQL);
mysqli_stmt_bind_param($stmt,"s",$student["Department_ID"]);
mysqli_stmt_execute($stmt);

$courses = mysqli_stmt_get_result($stmt);

/* ===========================
   STORE REGISTERED COURSES
=========================== */

$registeredCourses = [];

$registeredSQL = "SELECT Course_ID
                  FROM registration
                  WHERE Student_ID=?";

$stmt = mysqli_prepare($conn,$registeredSQL);
mysqli_stmt_bind_param($stmt,"s",$studentID);
mysqli_stmt_execute($stmt);

$registeredResult = mysqli_stmt_get_result($stmt);

while($row=mysqli_fetch_assoc($registeredResult)){
    $registeredCourses[] = $row["Course_ID"];
}

/* ===========================
   REGISTER COURSES
=========================== */

if(isset($_POST["register"])){

    if(empty($_POST["courses"])){

        $error="Please select at least one course.";

    }else{

        $semester = trim($_POST["semester"]);
        $academicYear = trim($_POST["academic_year"]);
        $registrationDate = date("Y-m-d");

        foreach($_POST["courses"] as $courseID){

            if(in_array($courseID,$registeredCourses)){
                continue;
            }

            /* Generate Registration ID */

            $idSQL = "SELECT Registration_ID
                      FROM registration
                      ORDER BY CAST(SUBSTRING(Registration_ID,3) AS UNSIGNED) DESC
                      LIMIT 1";

            $idResult = mysqli_query($conn,$idSQL);
            $lastID = mysqli_fetch_assoc($idResult);

            if($lastID){

                $number = intval(substr($lastID["Registration_ID"],2)) + 1;

            }else{

                $number = 1;

            }

            $registrationID = "RD".$number;

            /* Insert Registration */

            $insertSQL = "INSERT INTO registration
            (
                Registration_ID,
                Student_ID,
                Course_ID,
                Semester,
                Academic_Year,
                Registration_Date
            )
            VALUES
            (
                ?,?,?,?,?,?
            )";

            $insertStmt = mysqli_prepare($conn,$insertSQL);

            mysqli_stmt_bind_param(
                $insertStmt,
                "ssssss",
                $registrationID,
                $studentID,
                $courseID,
                $semester,
                $academicYear,
                $registrationDate
            );

            if(mysqli_stmt_execute($insertStmt)){

                $registeredCount++;

            }

        }

        if($registeredCount>0){

            $success = $registeredCount." course(s) registered successfully.";

        }else{

            $error = "The selected course(s) have already been registered.";

        }

        /* Refresh Registered Courses */

        $registeredCourses=[];

        $stmt=mysqli_prepare($conn,$registeredSQL);
        mysqli_stmt_bind_param($stmt,"s",$studentID);
        mysqli_stmt_execute($stmt);

        $registeredResult=mysqli_stmt_get_result($stmt);

        while($row=mysqli_fetch_assoc($registeredResult)){
            $registeredCourses[]=$row["Course_ID"];
        }

        /* Refresh Courses */

        $stmt=mysqli_prepare($conn,$courseSQL);
        mysqli_stmt_bind_param($stmt,"s",$student["Department_ID"]);
        mysqli_stmt_execute($stmt);

        $courses=mysqli_stmt_get_result($stmt);

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Course</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,Helvetica,sans-serif;
}

body{
background:#eef2f7;
color:#333;
}

.header{
position:fixed;
top:0;
left:0;
width:100%;
height:70px;
background:#2563eb;
display:flex;
justify-content:space-between;
align-items:center;
padding:0 30px;
color:#fff;
box-shadow:0 3px 10px rgba(0,0,0,.15);
z-index:1000;
}

.header h2{
display:flex;
align-items:center;
gap:10px;
font-size:24px;
}

.header .student-name{
font-weight:bold;
font-size:16px;
}

.sidebar{
position:fixed;
top:70px;
left:0;
width:250px;
height:calc(100vh - 70px);
background:#0f172a;
overflow-y:auto;
}

.sidebar ul{
list-style:none;
padding:20px 0;
}

.sidebar li{
margin:8px 15px;
}

.sidebar a{
display:flex;
align-items:center;
gap:15px;
padding:15px;
text-decoration:none;
color:#fff;
border-radius:12px;
transition:.3s;
}

.sidebar a:hover{
background:#2563eb;
}

.sidebar .active{
background:#2563eb;
}

.main{
margin-left:250px;
margin-top:70px;
padding:35px;
}

.page-title{
margin-bottom:25px;
}

.page-title h1{
font-size:30px;
color:#0f172a;
margin-bottom:8px;
}

.page-title p{
color:#666;
font-size:15px;
}

.success{
background:#d4edda;
color:#155724;
padding:15px;
border-radius:10px;
margin-bottom:20px;
}

.error{
background:#f8d7da;
color:#721c24;
padding:15px;
border-radius:10px;
margin-bottom:20px;
}

.card{
background:#fff;
padding:30px;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.card-info{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.badge{
background:#2563eb;
color:#fff;
padding:8px 18px;
border-radius:30px;
font-size:14px;
font-weight:bold;
}

.form-row{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
margin-bottom:25px;
}

label{
display:block;
margin-bottom:8px;
font-weight:bold;
}

select{
width:100%;
padding:12px;
border:1px solid #ccc;
border-radius:8px;
outline:none;
transition:.3s;
}

select:focus{
border-color:#2563eb;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th{
background:#2563eb;
color:#fff;
padding:15px;
text-align:left;
}

td{
padding:15px;
border-bottom:1px solid #ddd;
}

tbody tr:nth-child(even){
background:#f8fafc;
}

tbody tr:hover{
background:#edf4ff;
}

.status{
color:#16a34a;
font-weight:bold;
}

.registered{
color:#dc2626;
font-weight:bold;
}

button{
margin-top:25px;
padding:14px 25px;
background:#2563eb;
color:#fff;
border:none;
border-radius:10px;
cursor:pointer;
font-size:16px;
transition:.3s;
}

button:hover{
background:#1d4ed8;
}

button:disabled{
background:#9ca3af;
cursor:not-allowed;
}

.footer{
margin-top:30px;
text-align:center;
color:#666;
}

@media(max-width:900px){

.sidebar{
width:80px;
}

.sidebar a span{
display:none;
}

.main{
margin-left:80px;
}

.form-row{
grid-template-columns:1fr;
}

table{
font-size:14px;
}

}

</style>

</head>

<body>

<header class="header">

<h2>
<i class="fa-solid fa-graduation-cap"></i>
Utalii University
</h2>

<div class="student-name">
<?php echo htmlspecialchars($student["First_Name"]); ?>
</div>

</header>

<aside class="sidebar">

<ul>

<li>
<a href="dashboard.php">
<i class="fa-solid fa-house"></i>
<span>Dashboard</span>
</a>
</li>

<li>
<a href="register_course.php" class="active">
<i class="fa-solid fa-book-open"></i>
<span>Register Course</span>
</a>
</li>

<li>
<a href="view_courses.php">
<i class="fa-solid fa-book"></i>
<span>My Courses</span>
</a>
</li>

<li>
<a href="drop_course.php">
<i class="fa-solid fa-trash"></i>
<span>Drop Course</span>
</a>
</li>

<li>
<a href="logout.php">
<i class="fa-solid fa-right-from-bracket"></i>
<span>Logout</span>
</a>
</li>

</ul>

</aside>

<div class="main">

<div class="page-title">

<h1>Register Courses</h1>

<p>
Department:
<strong><?php echo htmlspecialchars($student["Department_Name"]); ?></strong>
</p>

</div>

<?php if($success!=""){ ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<?php if($error!=""){ ?>
<div class="error"><?php echo $error; ?></div>
<?php } ?>

<div class="card">

<div class="card-info">

<h2>Available Courses</h2>

<span class="badge">
<?php echo mysqli_num_rows($courses); ?> Courses
</span>

</div>

<form method="POST">

<div class="form-row">

<div>

<label>Semester</label>

<select name="semester" required>

<option value="">Select Semester</option>
<option>Semester 1</option>
<option>Semester 2</option>

</select>

</div>

<div>

<label>Academic Year</label>

<select name="academic_year" required>

<option value="">Select Academic Year</option>
<option>2025/2026</option>
<option>2026/2027</option>
<option>2027/2028</option>

</select>

</div>

</div>

<table>

<thead>

<tr>
<th>Select</th>
<th>Course Code</th>
<th>Course Name</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($courses)>0){

while($course=mysqli_fetch_assoc($courses)){

$isRegistered=in_array($course["Course_ID"],$registeredCourses);

?>

<tr>

<td>

<?php if($isRegistered){ ?>

<input type="checkbox" disabled>

<?php }else{ ?>

<input
type="checkbox"
name="courses[]"
value="<?php echo $course["Course_ID"]; ?>">

<?php } ?>

</td>

<td>

<?php echo htmlspecialchars($course["Course_Code"]); ?>

</td>

<td>

<?php echo htmlspecialchars($course["Course_Name"]); ?>

</td>

<td>

<?php

if($isRegistered){

?>

<span class="registered">
<i class="fa-solid fa-circle-check"></i>
Already Registered
</span>

<?php

}else{

?>

<span class="status">
<i class="fa-solid fa-circle"></i>
Available
</span>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="4" style="text-align:center;padding:30px;color:#666;">

No courses available for your department.

</td>

</tr>

<?php } ?>

</tbody>

</table>

<button
type="submit"
name="register"
id="registerBtn"
disabled>

<i class="fa-solid fa-floppy-disk"></i>

Register Selected Courses

</button>

</form>

</div>

<div class="footer">

<p>

© <?php echo date("Y"); ?> Utalii University | Student Course Registration System

</p>

</div>

</div>

<script>

const checkboxes=document.querySelectorAll('input[name="courses[]"]');

const registerBtn=document.getElementById("registerBtn");

checkboxes.forEach(box=>{

box.addEventListener("change",()=>{

const selected=document.querySelectorAll('input[name="courses[]"]:checked').length;

registerBtn.disabled=(selected===0);

document.title=selected+" Course(s) Selected";

});

});

document.querySelector("form").addEventListener("submit",function(e){

const selected=document.querySelectorAll('input[name="courses[]"]:checked').length;

if(selected===0){

alert("Please select at least one course.");

e.preventDefault();

return;

}

if(!confirm("Are you sure you want to register the selected course(s)?")){

e.preventDefault();

}

});

</script>

</body>

</html>