<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"] == "" || $_SESSION['usertype'] != 'a') {
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");

// Search functionality
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Fetch doctors with search filter
$sqlmain = "SELECT doctor.docid, doctor.docname, doctor.docemail, doctor.doctel, specialties.sname 
            FROM doctor 
            INNER JOIN specialties ON specialties.id = doctor.specialties";

if (!empty($search)) {
    $sqlmain .= " WHERE doctor.docname LIKE '%$search%' OR doctor.docemail LIKE '%$search%'";
}

$sqlmain .= " ORDER BY doctor.docname ASC";
$result = $database->query($sqlmain);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shifokorlar - eDoc</title>
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
            <h1 class="text-xl font-bold text-blue-600">eDoc Admin</h1>
            <p class="text-gray-600 text-sm">Administrator Panel</p>
        </div>
        <nav class="mt-5">
            <a href="index.php" class="sidebar-item">
                <i class="fas fa-home sidebar-item-icon"></i>
                <span>Bosh sahifa</span>
            </a>
            <a href="doctors.php" class="sidebar-item active">
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
            <h1 class="text-2xl font-bold text-gray-800">Shifokorlar</h1>
            <div class="flex items-center space-x-4">
                <a href="add-new.php" class="btn-primary">
                    <i class="fas fa-user-md mr-2"></i>Yangi shifokor qo'shish
                </a>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user-md text-blue-600"></i>
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
                            placeholder="Shifokor ismi yoki email bo'yicha qidirish"
                            value="<?php echo htmlspecialchars($search); ?>"
                        >
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>Qidirish
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="doctors.php" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Tozalash
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Doctors Table -->
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <h2 class="card-title">Barcha shifokorlar</h2>
                <span class="text-gray-500 text-sm">
                    <?php echo $result->num_rows; ?> ta shifokor
                </span>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="table-header">ID</th>
                                    <th class="table-header">Ism</th>
                                    <th class="table-header">Email</th>
                                    <th class="table-header">Telefon</th>
                                    <th class="table-header">Mutaxassislik</th>
                                    <th class="table-header">Amallar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="table-row">
                                        <td class="table-cell font-bold"><?php echo $row['docid']; ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($row['docname']); ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($row['docemail']); ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($row['doctel']); ?></td>
                                        <td class="table-cell"><?php echo htmlspecialchars($row['sname']); ?></td>
                                        <td class="table-cell">
                                            <div class="flex space-x-2">
                                                <a href="edit-doc.php?id=<?php echo $row['docid']; ?>" 
                                                   class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete-doctor.php?id=<?php echo $row['docid']; ?>" 
                                                   class="text-red-600 hover:text-red-800"
                                                   onclick="return confirm('Shifokorni o'chirmoqchimisiz?')">
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
                        <i class="fas fa-user-md text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Shifokorlar topilmadi</h3>
                        <p class="text-gray-500 mb-6">
                            <?php if (!empty($search)): ?>
                                "<?php echo htmlspecialchars($search); ?>" bo'yicha hech qanday shifokor topilmadi.
                            <?php else: ?>
                                Hozircha shifokorlar mavjud emas.
                            <?php endif; ?>
                        </p>
                        <a href="add-new.php" class="btn-primary inline-block">
                            <i class="fas fa-user-md mr-2"></i>Yangi shifokor qo'shish
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