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

// Search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Fetch appointments with search filter
$sqlmain = "SELECT appointment.appoid, appointment.apponum, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime 
            FROM appointment 
            INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
            INNER JOIN patient ON appointment.pid = patient.pid 
            INNER JOIN doctor ON schedule.docid = doctor.docid 
            WHERE patient.pid = $userid";

if (!empty($search)) {
    $sqlmain .= " AND (doctor.docname LIKE '%$search%' OR schedule.title LIKE '%$search%')";
}

$sqlmain .= " ORDER BY schedule.scheduledate DESC";
$result = $database->query($sqlmain);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mening bronlarim - eDoc</title>
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
        
        // Show loading spinner on form submit
        function showLoading(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="loading-spinner mr-2"></span>Qidirilmoqda...';
                submitBtn.disabled = true;
            }
            return true;
        }
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
            <a href="index.php" class="sidebar-item">
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
            <a href="appointment.php" class="sidebar-item active">
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
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Mening bronlarim</h1>
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center dark:bg-blue-900">
                    <i class="fas fa-calendar-check text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card mb-6">
            <div class="card-body">
                <form action="" method="POST" class="flex flex-col md:flex-row gap-3" onsubmit="return showLoading(this)">
                    <div class="flex-grow">
                        <input 
                            type="search" 
                            name="search" 
                            class="form-input focus-ring" 
                            placeholder="Shifokor yoki sessiya nomi bo'yicha qidirish"
                            value="<?php echo htmlspecialchars($search); ?>"
                            aria-label="Search appointments"
                        >
                    </div>
                    <button type="submit" class="btn-primary focus-ring">
                        <i class="fas fa-search mr-2"></i>Qidirish
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="appointment.php" class="btn-secondary focus-ring">
                            <i class="fas fa-times mr-2"></i>Tozalash
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <h2 class="card-title">Mening bronlarim ro'yxati</h2>
                <span class="text-gray-500 text-sm dark:text-gray-400">
                    <?php echo $result->num_rows; ?> ta natija
                </span>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="table-header">Tayinlash raqami</th>
                                    <th class="table-header">Sessiya nomi</th>
                                    <th class="table-header">Shifokor</th>
                                    <th class="table-header">Sana</th>
                                    <th class="table-header">Vaqt</th>
                                    <th class="table-header">Amallar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="table-row">
                                        <td class="table-cell font-bold">#<?php echo $row['apponum']; ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($row['docname']); ?></td>
                                        <td class="table-cell"><?php echo $row['scheduledate']; ?></td>
                                        <td class="table-cell"><?php echo substr($row['scheduletime'], 0, 5); ?></td>
                                        <td class="table-cell">
                                            <div class="flex space-x-2">
                                                <a href="delete-appointment.php?id=<?php echo $row['appoid']; ?>" 
                                                   class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                   onclick="return confirm('Uchrashuvni bekor qilmoqchimisiz?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4 dark:text-gray-500"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2 dark:text-white">Hech qanday bron topilmadi</h3>
                        <p class="text-gray-500 mb-6 dark:text-gray-400">
                            <?php if (!empty($search)): ?>
                                "<?php echo htmlspecialchars($search); ?>" bo'yicha hech qanday bron topilmadi.
                            <?php else: ?>
                                Hozircha bronlaringiz mavjud emas.
                            <?php endif; ?>
                        </p>
                        <a href="schedule.php" class="btn-primary inline-block">
                            <i class="fas fa-calendar-plus mr-2"></i>Yangi bron qilish
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