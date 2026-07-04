<?php
session_start();

if (!isset($_SESSION["Student_ID"])) {
    header("Location: login.php");
    exit();
}

require_once "config/db.php";

$studentID = $_SESSION["Student_ID"];

/* ==========================
   GET STUDENT DETAILS
========================== */

$sql = "SELECT s.Student_ID,
               s.First_Name,
               s.Email,
               d.Department_Name,
               d.Faculty
        FROM student s
        INNER JOIN department d
        ON s.Department_ID = d.Department_ID
        WHERE s.Student_ID = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $studentID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

/* ==========================
   COUNT REGISTERED COURSES
========================== */

$countSQL = "SELECT COUNT(*) AS total
             FROM registration
             WHERE Student_ID = ?";

$countStmt = mysqli_prepare($conn, $countSQL);
mysqli_stmt_bind_param($countStmt, "i", $studentID);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$count = mysqli_fetch_assoc($countResult);

$totalCourses = $count['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

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

/* ================= HEADER ================= */

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
    box-shadow:0 4px 12px rgba(0,0,0,.15);
    z-index:1000;
}

.logo{
    display:flex;
    align-items:center;
    gap:15px;
}

.logo-icon{
    width:50px;
    height:50px;
    border-radius:50%;
    border:2px solid white;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:22px;
}

.logo-text h2{
    font-size:22px;
}

.logo-text p{
    font-size:13px;
    opacity:.9;
}

.header-right{
    display:flex;
    align-items:center;
    gap:25px;
}

.notification{
    font-size:22px;
    cursor:pointer;
}

.user{
    display:flex;
    align-items:center;
    gap:10px;
}

.avatar{
    width:42px;
    height:42px;
    border-radius:50%;
    background:white;
    color:#2563eb;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:bold;
}

/* ================= SIDEBAR ================= */

.sidebar{
    position:fixed;
    top:70px;
    left:0;
    width:250px;
    height:calc(100vh - 70px);
    background:#0f172a;
    overflow:auto;
    padding-top:20px;
}

.sidebar ul{
    list-style:none;
}

.sidebar li{
    margin:8px 15px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:15px;
    color:white;
    text-decoration:none;
    padding:15px;
    border-radius:12px;
    transition:.3s;
}

.sidebar a:hover{
    background:#2563eb;
    transform:translateX(5px);
}

.sidebar .active{
    background:#2563eb;
}

.sidebar i{
    width:20px;
    text-align:center;
}

/* ================= MAIN ================= */

.main{
    margin-left:250px;
    margin-top:70px;
    padding:35px;
}

/* ================= WELCOME ================= */

.welcome{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.welcome h1{
    color:#0f172a;
    font-size:34px;
}

.welcome p{
    margin-top:8px;
    color:#666;
}

.clock{
    background:white;
    padding:18px 25px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    text-align:center;
}

.clock h2{
    color:#2563eb;
}

.clock span{
    color:#666;
    font-size:14px;
}

/* ================= QUICK CARDS ================= */

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.card{
    background:white;
    border-radius:18px;
    padding:25px;
    text-decoration:none;
    color:#333;
    box-shadow:0 10px 20px rgba(0,0,0,.08);
    transition:.3s;
}

.card:hover{
    transform:translateY(-8px);
    box-shadow:0 18px 35px rgba(0,0,0,.15);
}

.card i{
    font-size:40px;
    color:#2563eb;
    margin-bottom:18px;
    transition:.3s;
}

.card:hover i{
    transform:scale(1.15);
}

.card h3{
    margin-bottom:10px;
}

.card p{
    color:#666;
}

/* ================= STATISTICS ================= */

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.stat{
    background:white;
    border-radius:18px;
    padding:25px;
    text-align:center;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    transition:.3s;
}

.stat:hover{
    transform:translateY(-5px);
}

.stat i{
    font-size:34px;
    color:#2563eb;
    margin-bottom:15px;
}

.stat h2{
    margin:10px 0;
    color:#2563eb;
}

.stat p{
    color:#666;
}

/* ======== PART 2 STARTS HERE ======== */
.bottom{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:25px;
}

.notice,
.profile{
    background:white;
    border-radius:18px;
    padding:25px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
}

.notice h2,
.profile h2{
    color:#2563eb;
    margin-bottom:20px;
}

.notice p{
    margin:15px 0;
    color:#555;
}

.notice i{
    color:#2563eb;
    margin-right:8px;
}

.profile .avatar-large{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#2563eb;
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:34px;
    margin:0 auto 20px;
}

.profile h3{
    text-align:center;
    margin-bottom:20px;
    color:#0f172a;
}

.profile p{
    margin:12px 0;
    color:#555;
}

.footer{
    text-align:center;
    margin-top:40px;
    color:#777;
}

@media(max-width:1000px){

.bottom{
    grid-template-columns:1fr;
}

}

@media(max-width:850px){

.sidebar{
    width:80px;
}

.sidebar a span{
    display:none;
}

.main{
    margin-left:80px;
}

.logo-text{
    display:none;
}

}

</style>

</head>

<body>

<header class="header">

<div class="logo">

<div class="logo-icon">
<i class="fa-solid fa-graduation-cap"></i>
</div>

<div class="logo-text">
<h2>Utalii University</h2>
<p>Student Course Registration System</p>
</div>

</div>

<div class="header-right">

<div class="notification">
<i class="fa-solid fa-bell"></i>
</div>

<div class="user">

<div class="avatar">
<?php echo strtoupper(substr($student["First_Name"],0,1)); ?>
</div>

<strong>
<?php echo htmlspecialchars($student["First_Name"]); ?>
</strong>

</div>

</div>

</header>

<aside class="sidebar">

<ul>

<li>
<a href="dashboard.php" class="active">
<i class="fa-solid fa-house"></i>
<span>Dashboard</span>
</a>
</li>

<li>
<a href="register_course.php">
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
<a href="#">
<i class="fa-solid fa-user"></i>
<span>My Profile</span>
</a>
</li>

<li>
<a href="#">
<i class="fa-solid fa-gear"></i>
<span>Settings</span>
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

<main class="main">

<div class="welcome">

<div>

<h1 id="greeting"></h1>

<p>
Welcome back,
<strong><?php echo htmlspecialchars($student["First_Name"]); ?></strong>
</p>

</div>

<div class="clock">

<h2 id="time"></h2>

<span id="date"></span>

</div>

</div>

<div class="cards">

<a href="register_course.php" class="card">
<i class="fa-solid fa-book-open"></i>
<h3>Register Course</h3>
<p>Register your semester courses.</p>
</a>

<a href="view_courses.php" class="card">
<i class="fa-solid fa-book"></i>
<h3>My Courses</h3>
<p>View your registered courses.</p>
</a>

<a href="drop_course.php" class="card">
<i class="fa-solid fa-trash"></i>
<h3>Drop Course</h3>
<p>Remove a course registration.</p>
</a>

<a href="#" class="card">
<i class="fa-solid fa-user"></i>
<h3>My Profile</h3>
<p>View your academic profile.</p>
</a>

</div>

<div class="stats">

<div class="stat">
<i class="fa-solid fa-book"></i>
<h2><?php echo $totalCourses; ?></h2>
<p>Registered Courses</p>
</div>

<div class="stat">
<i class="fa-solid fa-building-columns"></i>
<h2><?php echo htmlspecialchars($student["Department_Name"]); ?></h2>
<p>Department</p>
</div>

<div class="stat">
<i class="fa-solid fa-school"></i>
<h2><?php echo htmlspecialchars($student["Faculty"]); ?></h2>
<p>Faculty</p>
</div>

<div class="stat">
<i class="fa-solid fa-id-card"></i>
<h2><?php echo htmlspecialchars($student["Student_ID"]); ?></h2>
<p>Student ID</p>
</div>

</div>

<div class="bottom">

<div class="notice">

<h2>
<i class="fa-solid fa-bullhorn"></i>
Notice Board
</h2>

<p><i class="fa-solid fa-circle-check"></i>Course registration is currently open.</p>

<p><i class="fa-solid fa-circle-check"></i>Ensure fee clearance before registration.</p>

<p><i class="fa-solid fa-circle-check"></i>Registration closes on 31st August.</p>

<p><i class="fa-solid fa-circle-check"></i>Remember to confirm your units before submission.</p>

</div>

<div class="profile">

<div class="avatar-large">
<i class="fa-solid fa-user"></i>
</div>

<h3><?php echo htmlspecialchars($student["First_Name"]); ?></h3>

<p><strong>Student ID:</strong> <?php echo htmlspecialchars($student["Student_ID"]); ?></p>

<p><strong>Email:</strong> <?php echo htmlspecialchars($student["Email"]); ?></p>

<p><strong>Department:</strong> <?php echo htmlspecialchars($student["Department_Name"]); ?></p>

<p><strong>Faculty:</strong> <?php echo htmlspecialchars($student["Faculty"]); ?></p>

</div>

</div>

<div class="footer">

<p>&copy; 2026 Utalii University | Student Course Registration System</p>

</div>

</main>

<script>

function updateDashboard(){

const now=new Date();

const hour=now.getHours();

let greeting="Good Evening 🌙";

if(hour<12){
    greeting="Good Morning ☀";
}else if(hour<18){
    greeting="Good Afternoon 🌤";
}

document.getElementById("greeting").innerHTML=
greeting+", <?php echo htmlspecialchars($student['First_Name']); ?>";

document.getElementById("time").innerHTML=
now.toLocaleTimeString();

document.getElementById("date").innerHTML=
now.toDateString();

}

updateDashboard();

setInterval(updateDashboard,1000);

</script>

</body>
</html>