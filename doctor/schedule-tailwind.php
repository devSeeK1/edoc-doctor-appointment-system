<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'd') {
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// Import database
include("../connection.php");
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

// Search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Get today's date
$today = date('Y-m-d');

// Fetch schedules with search filter
$sqlmain = "SELECT schedule.scheduleid, schedule.title, schedule.scheduledate, schedule.scheduletime, schedule.nop 
            FROM schedule 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            WHERE doctor.docid = $userid";

if (!empty($search)) {
    $sqlmain .= " AND schedule.title LIKE '%$search%'";
}

$sqlmain .= " ORDER BY schedule.scheduledate ASC";
$result = $database->query($sqlmain);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessiyalar - eDoc</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include our custom Tailwind styles -->
    <link rel="stylesheet" href="../tailwind/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar bg-white h-screen shadow-lg fixed left-0 top-0 w-64 z-10">
        <div class="p-5 border-b">
            <h1 class="text-xl font-bold text-blue-600">eDoc Doctor</h1>
            <p class="text-gray-600 text-sm truncate"><?php echo substr($useremail, 0, 22); ?></p>
        </div>
        <nav class="mt-5">
            <a href="index.php" class="sidebar-item">
                <i class="fas fa-home sidebar-item-icon"></i>
                <span>Bosh sahifa</span>
            </a>
            <a href="appointment.php" class="sidebar-item">
                <i class="fas fa-calendar-check sidebar-item-icon"></i>
                <span>Mening uchrashuvlarim</span>
            </a>
            <a href="schedule.php" class="sidebar-item active">
                <i class="fas fa-calendar-alt sidebar-item-icon"></i>
                <span>Sessiyalar</span>
            </a>
            <a href="patient.php" class="sidebar-item">
                <i class="fas fa-user sidebar-item-icon"></i>
                <span>Bemorlar</span>
            </a>
            <a href="settings.php" class="sidebar-item">
                <i class="fas fa-cog sidebar-item-icon"></i>
                <span>Profil sozlamalari</span>
            </a>
        </nav>
        <div class="absolute bottom-0 w-full p-4 border-t">
            <a href="../logout.php" class="btn-danger w-full text-center py-2 px-4 rounded">
                <i class="fas fa-sign-out-alt mr-2"></i>Chiqish
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content ml-64">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Sessiyalar</h1>
            <div class="flex items-center space-x-4">
                <a href="add-session.php" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Yangi sessiya
                </a>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card mb-6">
            <div class="card-body">
                <form action="" method="POST" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-grow">
                        <input 
                            type="search" 
                            name="search" 
                            class="form-input" 
                            placeholder="Sessiya nomi bo'yicha qidirish"
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>Qidirish
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="schedule.php" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Tozalash
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Schedule Cards -->
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <h2 class="card-title">Mening sessiyalarim</h2>
                <span class="text-gray-500 text-sm">
                    <?php echo $result->num_rows; ?> ta natija
                </span>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="dashboard-card">
                                <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="text-gray-600 mb-1">
                                    <i class="far fa-calendar-alt mr-2"></i>
                                    <?php echo $row['scheduledate']; ?>
                                </p>
                                <p class="text-gray-600 mb-3">
                                    <i class="far fa-clock mr-2"></i>
                                    <?php echo substr($row['scheduletime'], 0, 5); ?>
                                </p>
                                <p class="text-gray-600 mb-4">
                                    <i class="fas fa-user-friends mr-2"></i>
                                    <?php 
                                        // Check how many appointments already exist for this schedule
                                        $scheduleid = $row['scheduleid'];
                                        $appt_result = $database->query("SELECT COUNT(*) as count FROM appointment WHERE scheduleid = $scheduleid");
                                        $appt_count = $appt_result->fetch_assoc()['count'];
                                        echo $appt_count . " / " . $row['nop'] . " bemor";
                                    ?>
                                </p>
                                
                                <div class="flex space-x-2 mt-4">
                                    <a href="delete-session.php?id=<?php echo $row['scheduleid']; ?>" 
                                       class="btn-danger flex-1 text-center"
                                       onclick="return confirm('Sessiyani o'chirmoqchimisiz?')">
                                        <i class="fas fa-trash-alt mr-2"></i>O'chirish
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Sessiyalar topilmadi</h3>
                        <p class="text-gray-500 mb-6">
                            <?php if (!empty($search)): ?>
                                "<?php echo htmlspecialchars($search); ?>" bo'yicha hech qanday sessiya topilmadi.
                            <?php else: ?>
                                Hozircha sessiyalaringiz mavjud emas.
                            <?php endif; ?>
                        </p>
                        <a href="add-session.php" class="btn-primary inline-block">
                            <i class="fas fa-plus mr-2"></i>Yangi sessiya yaratish
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Simple JavaScript for active menu item
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarItems = document.querySelectorAll('.sidebar-item');
            sidebarItems.forEach(item => {
                item.addEventListener('click', function() {
                    sidebarItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>