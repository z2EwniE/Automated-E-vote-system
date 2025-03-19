<?php
session_start();
require_once '../config/database.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login/student_login.php");
    exit();
}

$page_title = "Student Dashboard";
$database = new Database();
$db = $database->getConnection();

// Get student details
$student_query = "SELECT s.*, c.course_name, d.department_name 
                 FROM students s
                 JOIN course c ON s.course = c.course_id
                 JOIN department d ON s.department = d.department_id
                 WHERE s.id = :student_id";
$stmt = $db->prepare($student_query);
$stmt->execute([':student_id' => $_SESSION['student_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Validate student data
if (!$student) {
    // Handle case where student data is not found
    session_destroy();
    header("Location: ../login/student_login.php?error=invalid_session");
    exit();
}

// Fetch all platforms with candidate details
$platforms_query = "SELECT p.*, c.candidate_name, c.candidate_image_path, 
                   pos.position_name, pl.partylist_name,
                   (SELECT COUNT(*) FROM platform_likes WHERE platform_id = p.platform_id) as like_count,
                   (SELECT COUNT(*) FROM platform_likes WHERE platform_id = p.platform_id AND student_id = :student_id) as user_liked
                   FROM platforms p 
                   JOIN candidates c ON p.candidate_id = c.candidate_id
                   JOIN positions pos ON c.candidate_position = pos.position_id
                   JOIN partylists pl ON c.partylist_id = pl.partylist_id
                   ORDER BY p.created_at DESC";
$platforms_stmt = $db->prepare($platforms_query);
$platforms_stmt->execute([':student_id' => $_SESSION['student_id']]);
$platforms = $platforms_stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize empty array if no platforms found
if (!$platforms) {
    $platforms = [];
}

require_once '../includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex">
        <!-- Sidebar Navigation -->
        <nav class="fixed left-0 top-0 h-screen w-20 bg-white shadow-lg flex flex-col items-center py-8 space-y-8">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <button onclick="confirmLogout()" class="w-12 h-12 bg-red-500/10 hover:bg-red-500/20 rounded-xl flex items-center justify-center transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </nav>

        <!-- Main Content -->
        <div class="ml-24 flex-1 max-w-6xl mx-auto">
            <!-- User Profile Card -->
            <div class="bg-white rounded-2xl p-6 mb-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                        <p class="text-gray-600"><?php echo htmlspecialchars($student['student_id']); ?></p>
                        <p class="text-gray-500 mt-1"><?php echo htmlspecialchars($student['course_name'] . ' - ' . $student['department_name']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Platform Posts -->
            <?php if (empty($platforms)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500">No platforms available at the moment.</p>
            </div>
            <?php else: ?>
            <?php foreach ($platforms as $platform): ?>
            <div class="bg-white rounded-2xl shadow-lg mb-8 border border-gray-200 overflow-hidden transition-transform duration-200 hover:scale-[1.02]">
                <div class="p-6">
                    <!-- Candidate Info -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="relative">
                            <img src="../<?php echo htmlspecialchars($platform['candidate_image_path']); ?>" 
                                 alt="Profile" class="w-14 h-14 rounded-xl object-cover ring-2 ring-purple-500/30">
                            <div class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-500 rounded-lg border-2 border-white"></div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($platform['candidate_name']); ?></h3>
                            <div class="flex gap-2 mt-1">
                                <span class="px-3 py-1 text-sm font-medium bg-green-500/10 text-green-600 rounded-lg"><?php echo htmlspecialchars($platform['position_name']); ?></span>
                                <span class="px-3 py-1 text-sm font-medium bg-blue-500/10 text-blue-600 rounded-lg"><?php echo htmlspecialchars($platform['partylist_name']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Platform Content -->
                    <p class="text-gray-600 leading-relaxed mb-6"><?php echo nl2br(htmlspecialchars($platform['content'])); ?></p>

                    <!-- Media Content -->
                    <?php if ($platform['image_path']): ?>
                    <div class="space-y-4 mb-6">
                        <?php 
                        $media_files = json_decode($platform['image_path'], true);
                        if ($media_files):
                            foreach ($media_files as $media): 
                                $ext = pathinfo($media, PATHINFO_EXTENSION);
                                if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                        <img src="../<?php echo htmlspecialchars($media); ?>" alt="Platform Media" 
                             class="rounded-xl shadow-lg max-h-96 w-auto mx-auto">
                        <?php 
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </div>
                    <?php endif; ?>

                    <!-- Interaction Section -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex space-x-6">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium text-gray-600"><?php echo $platform['like_count']; ?></span>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="handleLike(this, <?php echo $platform['platform_id']; ?>)"
                                    class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-colors duration-200 <?php echo $platform['user_liked'] ? 'bg-red-500/10 text-red-600' : 'bg-gray-100 text-gray-600 hover:bg-red-500/10 hover:text-red-600'; ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Like
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../logout.php';
    }
}

async function handleLike(button, platformId) {
    try {
        const response = await fetch('../ajax/handle_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ platform_id: platformId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update like count - Fixed selector to match the correct element
            const likeCountElement = button.closest('.p-6').querySelector('.text-sm.font-medium.text-gray-600');
            likeCountElement.textContent = data.like_count;
            
            // Update button appearance
            if (data.is_liked) {
                button.classList.remove('bg-gray-100', 'text-gray-600');
                button.classList.add('bg-red-500/10', 'text-red-600');
            } else {
                button.classList.remove('bg-red-500/10', 'text-red-600');
                button.classList.add('bg-gray-100', 'text-gray-600');
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>