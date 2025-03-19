<?php
session_start();
require_once '../config/database.php';

// Check if candidate is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: ../login/candidate_login.php");
    exit();
}

$page_title = "Candidate Dashboard";
$database = new Database();
$db = $database->getConnection();

// Get candidate details
$candidate_query = "SELECT c.*, p.position_name, d.department_name, pl.partylist_name 
                   FROM candidates c
                   JOIN positions p ON c.candidate_position = p.position_id
                   JOIN department d ON c.department = d.department_id
                   JOIN partylists pl ON c.partylist_id = pl.partylist_id
                   WHERE c.candidate_id = :candidate_id";
$stmt = $db->prepare($candidate_query);
$stmt->execute([':candidate_id' => $_SESSION['candidate_id']]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle new platform post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        $media_paths = [];
        
        // Handle multiple file uploads
        if (!empty($_FILES['media']['name'][0])) {
            foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['media']['error'][$key] == 0) {
                    $upload_dir = '../uploads/platforms/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['media']['name'][$key], PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '_' . $_FILES['media']['name'][$key];
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $media_paths[] = 'uploads/platforms/' . $new_filename;
                    }
                }
            }
        }
        
        // Insert platform post
        $platform_query = "INSERT INTO platforms (candidate_id, content, image_path, created_at) 
                          VALUES (:candidate_id, :content, :image_path, NOW())";
        $platform_stmt = $db->prepare($platform_query);
        $platform_stmt->execute([
            ':candidate_id' => $_SESSION['candidate_id'],
            ':content' => $_POST['content'],
            ':image_path' => !empty($media_paths) ? json_encode($media_paths) : null
        ]);
        
        $db->commit();
        $_SESSION['success_message'] = "Platform posted successfully!";
        
        // Redirect after successful post to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error_message'] = "Failed to post platform: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Display messages and clear them
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Fetch candidate's platforms
$platforms_query = "SELECT p.*, 
                   (SELECT COUNT(*) FROM platform_likes WHERE platform_id = p.platform_id) as like_count,
                   (SELECT COUNT(*) FROM platform_comments WHERE platform_id = p.platform_id) as comment_count
                   FROM platforms p 
                   WHERE p.candidate_id = :candidate_id 
                   ORDER BY p.created_at DESC";
$platforms_stmt = $db->prepare($platforms_query);
$platforms_stmt->execute([':candidate_id' => $_SESSION['candidate_id']]);
$platforms = $platforms_stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <?php if ($success_message || $error_message): ?>
    <div class="fixed top-4 right-4 left-24 z-50 flex flex-col space-y-4">
        <?php if ($success_message): ?>
        <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-600 shadow-lg animate-fade-in-down">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-600 shadow-lg animate-fade-in-down">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <div class="flex">
        <!-- Sidebar Navigation -->
        <nav class="fixed left-0 top-0 h-screen w-20 bg-white shadow-lg flex flex-col items-center py-8 space-y-8">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                <?php if ($candidate['candidate_image_path']): ?>
                    <img src="../<?php echo htmlspecialchars($candidate['candidate_image_path']); ?>" 
                         alt="Profile" class="w-12 h-12 rounded-xl object-cover">
                <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                <?php endif; ?>
            </div>
            <a href="../logout.php" class="w-12 h-12 bg-red-500/10 hover:bg-red-500/20 rounded-xl flex items-center justify-center transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </nav>

        <!-- Main Content -->
        <div class="ml-24 flex-1 max-w-6xl mx-auto">
            <!-- Candidate Profile Card -->
            <div class="bg-white rounded-2xl p-6 mb-8 shadow-lg border border-gray-200">
                <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <?php if ($candidate['candidate_image_path']): ?>
                            <img src="../<?php echo htmlspecialchars($candidate['candidate_image_path']); ?>" 
                                 alt="Profile" class="w-16 h-16 rounded-xl object-cover">
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($candidate['candidate_name']); ?></h2>
                        <div class="flex gap-2 mt-2">
                            <span class="px-3 py-1 text-sm font-medium bg-green-500/10 text-green-400 rounded-lg"><?php echo htmlspecialchars($candidate['position_name']); ?></span>
                            <span class="px-3 py-1 text-sm font-medium bg-blue-500/10 text-blue-400 rounded-lg"><?php echo htmlspecialchars($candidate['partylist_name']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Platform Form -->
            <div class="bg-white rounded-2xl p-6 mb-8 shadow-lg border border-gray-200">
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <textarea name="content" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-purple-500 p-4" placeholder="Share your platform..."></textarea>
                    <div id="image-preview" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4"></div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Add Media
                                <input type="file" name="media[]" multiple class="hidden" accept="image/*" onchange="previewImages(this)">
                            </label>
                        </div>
                        <button type="submit" class="inline-flex items-center px-6 py-2 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-blue-500 hover:from-purple-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Post Platform
                        </button>
                    </div>
                </form>
            </div>

            <!-- Platform Posts -->
            <?php foreach ($platforms as $platform): ?>
            <div class="bg-white rounded-2xl shadow-lg mb-8 border border-gray-200 overflow-hidden transition-transform duration-200 hover:scale-[1.02]" data-platform-id="<?php echo $platform['platform_id']; ?>">
                <div class="p-6">
                    <!-- Platform Header -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-blue-500 rounded-full flex items-center justify-center">
                                <?php if ($candidate['candidate_image_path']): ?>
                                    <img src="../<?php echo htmlspecialchars($candidate['candidate_image_path']); ?>" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="text-gray-800 font-medium"><?php echo htmlspecialchars($candidate['candidate_name']); ?></h3>
                                <p class="text-gray-500 text-sm"><?php echo date('M j, Y g:i A', strtotime($platform['created_at'])); ?></p>
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
                        foreach ($media_files as $media): 
                            $ext = pathinfo($media, PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                        <img src="../<?php echo $media; ?>" alt="Platform Media" 
                             class="rounded-xl shadow-lg max-h-96 w-auto mx-auto">
                        <?php endif; endforeach; ?>
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
                        <div class="flex space-x-4">
                            <a href="edit_platform.php?id=<?php echo $platform['platform_id']; ?>" class="flex items-center space-x-2 text-yellow-600 hover:text-yellow-700 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="text-sm font-medium">Edit</span>
                            </a>
                            <button onclick="deletePlatform(<?php echo $platform['platform_id']; ?>)" class="flex items-center space-x-2 text-red-600 hover:text-red-700 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span class="text-sm font-medium">Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Include JavaScript files -->
<script src="../assets/js/image-preview.js"></script>
<script src="../assets/js/platform.js"></script>
<script src="../assets/js/comments.js"></script>

<?php require_once '../includes/footer.php'; ?>