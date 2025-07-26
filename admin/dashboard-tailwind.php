<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");

// Get today's date
$today = date('Y-m-d');

// Fetch statistics
$patientrow = $database->query("SELECT * FROM patient");
$doctorrow = $database->query("SELECT * FROM doctor");
$appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today'");
$schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = '$today'");

// Fetch upcoming appointments
$nextweek = date("Y-m-d", strtotime("+1 week"));
$sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, patient.pname, schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate 
            FROM schedule 
            INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
            INNER JOIN patient ON patient.pid = appointment.pid 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' 
            ORDER BY schedule.scheduledate DESC";
$appointments = $database->query($sqlmain);

// Fetch upcoming schedules
$sqlschedule = "SELECT schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop 
               FROM schedule 
               INNER JOIN doctor ON schedule.docid = doctor.docid 
               WHERE schedule.scheduledate >= '$today' AND schedule.scheduledate <= '$nextweek' 
               ORDER BY schedule.scheduledate DESC";
$schedules = $database->query($sqlschedule);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - eDoc</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include our custom Tailwind styles -->
    <link rel="stylesheet" href="../tailwind/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Dark mode toggle
        function toggleDarkMode() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
        }
        
        // Initialize dark mode based on system preference or localStorage
        function initDarkMode() {
            const html = document.documentElement;
            const storedTheme = localStorage.theme;
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (storedTheme === 'dark' || (!storedTheme && systemPrefersDark)) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
        
        // Run on page load
        document.addEventListener('DOMContentLoaded', initDarkMode);
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Sidebar -->
    <div class="sidebar bg-white h-screen shadow-lg fixed left-0 top-0 w-64 z-10 dark:bg-gray-800">
        <div class="p-5 border-b dark:border-gray-700">
            <h1 class="text-xl font-bold text-blue-600 dark:text-blue-400">eDoc Admin</h1>
            <p class="text-gray-600 text-sm dark:text-gray-300">Administrator Panel</p>
        </div>
        <nav class="mt-5">
            <a href="index.php" class="sidebar-item active">
                <i class="fas fa-home sidebar-item-icon"></i>
                <span>Bosh sahifa</span>
            </a>
            <a href="doctors.php" class="sidebar-item">
                <i class="fas fa-user-md sidebar-item-icon"></i>
                <span>Shifokorlar</span>
            </a>
            <a href="schedule.php" class="sidebar-item">
                <i class="fas fa-calendar-alt sidebar-item-icon"></i>
                <span>Jadval</span>
            </a>
            <a href="appointment.php" class="sidebar-item">
                <i class="fas fa-calendar-check sidebar-item-icon"></i>
                <span>Uchrashuvlar</span>
            </a>
            <a href="patient.php" class="sidebar-item">
                <i class="fas fa-user sidebar-item-icon"></i>
                <span>Bemorlar</span>
            </a>
        </nav>
        <div class="absolute bottom-0 w-full p-4 border-t dark:border-gray-700">
            <div class="flex justify-between items-center mb-3">
                <button onclick="toggleDarkMode()" class="dark-mode-toggle" aria-label="Toggle dark mode">
                    <i class="fas fa-moon text-gray-600 dark:text-gray-300"></i>
                </button>
                <a href="../logout.php" class="btn-danger w-full text-center py-2 px-4 rounded">
                    <i class="fas fa-sign-out-alt mr-2"></i>Chiqish
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content ml-64">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bosh sahifa</h1>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bugungi sana</p>
                    <p class="font-semibold dark:text-gray-300"><?php echo $today; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center dark:bg-blue-900">
                    <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card">
                <div class="dashboard-card-title">Shifokorlar</div>
                <div class="dashboard-card-value"><?php echo $doctorrow->num_rows ?></div>
                <div class="mt-2 text-green-500 text-sm">
                    <i class="fas fa-arrow-up mr-1"></i>Active
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-title">Bemorlar</div>
                <div class="dashboard-card-value"><?php echo $patientrow->num_rows ?></div>
                <div class="mt-2 text-green-500 text-sm">
                    <i class="fas fa-arrow-up mr-1"></i>Registered
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-title">Yangi buyurtma</div>
                <div class="dashboard-card-value"><?php echo $appointmentrow->num_rows ?></div>
                <div class="mt-2 text-blue-500 text-sm">
                    <i class="fas fa-calendar-check mr-1"></i>Today
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="dashboard-card-title">Bugungi sessiyalar</div>
                <div class="dashboard-card-value"><?php echo $schedulerow->num_rows ?></div>
                <div class="mt-2 text-purple-500 text-sm">
                    <i class="fas fa-clock mr-1"></i>Scheduled
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments and Sessions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upcoming Appointments -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Yaqilashayotgan uchrashuvlar</h2>
                </div>
                <div class="card-body">
                    <?php if ($appointments->num_rows > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="table-header">Navbat raqami</th>
                                        <th class="table-header">Bemor ismi</th>
                                        <th class="table-header">Shifokor</th>
                                        <th class="table-header">Sessiya</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <?php while ($row = $appointments->fetch_assoc()): ?>
                                        <tr class="table-row">
                                            <td class="table-cell font-medium"><?php echo $row['apponum']; ?></td>
                                            <td class="table-cell"><?php echo substr($row['pname'], 0, 25); ?></td>
                                            <td class="table-cell"><?php echo substr($row['docname'], 0, 25); ?></td>
                                            <td class="table-cell"><?php echo substr($row['title'], 0, 15); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="appointment.php" class="btn-primary inline-block">Barcha uchrashuvlarni ko'rsatish</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4 dark:text-gray-500"></i>
                            <p class="text-gray-500 dark:text-gray-400">Hozircha uchrashuvlar mavjud emas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Yaqilashayotgan sessiyalar</h2>
                </div>
                <div class="card-body">
                    <?php if ($schedules->num_rows > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="table-header">Sessiya nomi</th>
                                        <th class="table-header">Shifokor</th>
                                        <th class="table-header">Sana va vaqt</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <?php while ($row = $schedules->fetch_assoc()): ?>
                                        <tr class="table-row">
                                            <td class="table-cell"><?php echo substr($row['title'], 0, 30); ?></td>
                                            <td class="table-cell"><?php echo substr($row['docname'], 0, 20); ?></td>
                                            <td class="table-cell">
                                                <?php echo substr($row['scheduledate'], 0, 10); ?> 
                                                <?php echo substr($row['scheduletime'], 0, 5); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="schedule.php" class="btn-primary inline-block">Barcha sessiyalarni ko'rsatish</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-clock text-gray-400 text-4xl mb-4 dark:text-gray-500"></i>
                            <p class="text-gray-500 dark:text-gray-400">Hozircha sessiyalar mavjud emas</p>
                        </div>
                    <?php endif; ?>
                </div>
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