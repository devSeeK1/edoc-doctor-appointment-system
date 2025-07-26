<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'p') {
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// Import database
include("../connection.php");

$sqlmain = "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();

$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Get today's date
$today = date('Y-m-d');

// Fetch statistics
$patientrow = $database->query("SELECT * FROM patient");
$doctorrow = $database->query("SELECT * FROM doctor");
$appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= '$today'");
$schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = '$today'");

// Fetch upcoming appointments for this patient
$sqlmain = "SELECT * FROM schedule 
            INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
            INNER JOIN patient ON patient.pid = appointment.pid 
            INNER JOIN doctor ON schedule.docid = doctor.docid  
            WHERE patient.pid = $userid AND schedule.scheduledate >= '$today' 
            ORDER BY schedule.scheduledate ASC";
$appointments = $database->query($sqlmain);

// Fetch doctors for search
$doctors = $database->query("SELECT docname FROM doctor");
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - eDoc</title>
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
            <h1 class="text-xl font-bold text-blue-600 dark:text-blue-400">eDoc Patient</h1>
            <p class="text-gray-600 text-sm truncate dark:text-gray-300"><?php echo substr($useremail, 0, 22); ?></p>
        </div>
        <nav class="mt-5">
            <a href="index.php" class="sidebar-item active">
                <i class="fas fa-home sidebar-item-icon"></i>
                <span>Bosh sahifa</span>
            </a>
            <a href="doctors.php" class="sidebar-item">
                <i class="fas fa-user-md sidebar-item-icon"></i>
                <span>Barcha shifokorlar</span>
            </a>
            <a href="schedule.php" class="sidebar-item">
                <i class="fas fa-calendar-alt sidebar-item-icon"></i>
                <span>Faol seanslar/Joy olish</span>
            </a>
            <a href="appointment.php" class="sidebar-item">
                <i class="fas fa-calendar-check sidebar-item-icon"></i>
                <span>Mening bronlarim</span>
            </a>
            <a href="settings.php" class="sidebar-item">
                <i class="fas fa-cog sidebar-item-icon"></i>
                <span>Sozlamalar</span>
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

        <!-- Welcome Section -->
        <div class="card mb-8">
            <div class="card-body bg-blue-50 dark:bg-blue-900/30">
                <h3 class="text-xl font-semibold text-gray-800 mb-2 dark:text-white">Assalomu alaykum</h3>
                <h1 class="text-2xl font-bold text-blue-600 mb-4 dark:text-blue-400"><?php echo $username; ?>.</h1>
                <p class="text-gray-600 mb-4 dark:text-gray-300">
                    Shifokorlar haqida ma'lumot yo'qmi? 
                    <a href="doctors.php" class="text-blue-600 hover:underline font-medium dark:text-blue-400">"Barcha shifokorlar"</a> 
                    yoki 
                    <a href="schedule.php" class="text-blue-600 hover:underline font-medium dark:text-blue-400">"Faol seanslar"</a> 
                    bo'limiga o'ting.<br>
                    O'tgan va kelajakdagi tayinlashlaringizni ko'rib chiqing va shifokoringizning kelish vaqti haqida ma'lumot oling.
                </p>
                
                <h3 class="text-lg font-semibold text-gray-800 mt-6 mb-3 dark:text-white">Shifokor bilan bog'laning.</h3>
                
                <form action="schedule.php" method="post" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-grow">
                        <input 
                            type="search" 
                            name="search" 
                            class="form-input focus-ring" 
                            placeholder="Shifokorni qidiring, biz esa mavjud sessiyani topamiz"
                            list="doctors"
                            aria-label="Search for doctors"
                        >
                        <datalist id="doctors">
                            <?php
                                while ($row = $doctors->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['docname']) . "'>";
                                }
                            ?>
                        </datalist>
                    </div>
                    <button type="submit" class="btn-primary focus-ring">
                        <i class="fas fa-search mr-2"></i>Qidirish
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats and Appointments -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stats Cards -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 dark:text-white">Holat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-title">Barcha shifokorlar</div>
                        <div class="dashboard-card-value"><?php echo $doctorrow->num_rows ?></div>
                        <div class="mt-2 text-blue-500 text-sm">
                            <i class="fas fa-user-md mr-1"></i>Available
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="dashboard-card-title">Barcha bemorlar</div>
                        <div class="dashboard-card-value"><?php echo $patientrow->num_rows ?></div>
                        <div class="mt-2 text-green-500 text-sm">
                            <i class="fas fa-users mr-1"></i>Registered
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="dashboard-card-title">Yangi Tayinlash</div>
                        <div class="dashboard-card-value"><?php echo $appointmentrow->num_rows ?></div>
                        <div class="mt-2 text-purple-500 text-sm">
                            <i class="fas fa-calendar-plus mr-1"></i>Today
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="dashboard-card-title">Bugungi Sessiyalar</div>
                        <div class="dashboard-card-value"><?php echo $schedulerow->num_rows ?></div>
                        <div class="mt-2 text-orange-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>Scheduled
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Yangi seanslaringiz</h2>
                    <a href="schedule.php" class="text-blue-600 hover:underline text-sm dark:text-blue-400">
                        <i class="fas fa-plus mr-1"></i>Yangi bron qilish
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <?php if ($appointments->num_rows > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="table-header">Tayinlash raqami</th>
                                            <th class="table-header">Sessiya nomi</th>
                                            <th class="table-header">Shifokor</th>
                                            <th class="table-header">Sana va vaqt</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                        <?php while ($row = $appointments->fetch_assoc()): ?>
                                            <tr class="table-row">
                                                <td class="table-cell font-bold text-lg"><?php echo $row['apponum']; ?></td>
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
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4 dark:text-gray-500"></i>
                                <p class="text-gray-500 mb-4 dark:text-gray-400">Hozircha uchrashuvlaringiz mavjud emas</p>
                                <a href="schedule.php" class="btn-primary inline-block">
                                    <i class="fas fa-user-md mr-2"></i>Shifokorni tanlang
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
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