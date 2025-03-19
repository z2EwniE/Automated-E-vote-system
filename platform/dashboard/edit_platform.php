<?php
session_start();
require_once '../config/database.php';

// Check if candidate is logged in
if (!isset($_SESSION['candidate_id'])) {
    header("Location: ../login/candidate_login.php");
    exit();
}

// Check if platform ID is provided
if (!isset($_GET['id'])) {
    header("Location: candidate_dashboard.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get platform details and verify ownership
$platform_query = "SELECT p.*, c.candidate_name 
                   FROM platforms p 
                   JOIN candidates c ON p.candidate_id = c.candidate_id 
                   WHERE p.platform_id = :platform_id AND p.candidate_id = :candidate_id";
$stmt = $db->prepare($platform_query);
$stmt->execute([
    ':platform_id' => $_GET['id'],
    ':candidate_id' => $_SESSION['candidate_id']
]);
$platform = $stmt->fetch(PDO::FETCH_ASSOC);

// If platform not found or doesn't belong to candidate, redirect
if (!$platform) {
    header("Location: candidate_dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate content
        if (empty(trim($_POST['content']))) {
            throw new Exception("Platform content cannot be empty");
        }

        $db->beginTransaction();
        
        $media_paths = [];
        $existing_media = $platform['image_path'] ? json_decode($platform['image_path'], true) : [];
        
        // Keep existing media files that weren't deleted
        if (isset($_POST['keep_media']) && is_array($_POST['keep_media'])) {
            foreach ($_POST['keep_media'] as $media) {
                if (in_array($media, $existing_media)) {
                    $media_paths[] = $media;
                }
            }
        }
        
        // Handle new file uploads
        if (!empty($_FILES['media']['name'][0])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['media']['error'][$key] == 0) {
                    // Validate file type
                    $file_type = $_FILES['media']['type'][$key];
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception("Invalid file type. Only JPG, PNG, and GIF files are allowed.");
                    }
                    
                    // Validate file size
                    if ($_FILES['media']['size'][$key] > $max_size) {
                        throw new Exception("File size exceeds limit. Maximum size is 5MB.");
                    }
                    
                    $upload_dir = '../uploads/platforms/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['media']['name'][$key], PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '_' . $_FILES['media']['name'][$key];
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $media_paths[] = 'uploads/platforms/' . $new_filename;
                    } else {
                        throw new Exception("Failed to upload file: " . $_FILES['media']['name'][$key]);
                    }
                }
            }
        }
        
        // Delete removed media files
        foreach ($existing_media as $media) {
            if (!in_array($media, $media_paths)) {
                $file_path = '../' . $media;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        // Update platform
        $update_query = "UPDATE platforms SET content = :content, image_path = :image_path, updated_at = NOW() WHERE platform_id = :platform_id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute([
            ':content' => trim($_POST['content']),
            ':image_path' => !empty($media_paths) ? json_encode($media_paths) : null,
            ':platform_id' => $_GET['id']
        ]);
        
        $db->commit();
        $_SESSION['success_message'] = "Platform updated successfully!";
        header("Location: candidate_dashboard.php");
        exit();
        
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error_message'] = "Failed to update platform: " . $e->getMessage();
    }
}

$page_title = "Edit Platform";
require_once '../includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Platform</h1>
                <p class="text-gray-600">Last updated: <?php echo date('M j, Y g:i A', strtotime($platform['updated_at'] ?? $platform['created_at'])); ?></p>
            </div>
            <a href="candidate_dashboard.php" class="inline-flex items-center text-purple-600 hover:text-purple-700 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-600">
            <?php 
            echo htmlspecialchars($_SESSION['error_message']);
            unset($_SESSION['error_message']);
            ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-200">
            <form method="POST" enctype="multipart/form-data" class="space-y-6" id="platformForm">
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Platform Content</label>
                    <textarea id="content" name="content" rows="6" required
                        class="mt-1 w-full bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 p-4"
                        placeholder="Share your thoughts, ideas, and plans..."><?php echo htmlspecialchars($platform['content']); ?></textarea>
                    <p class="mt-2 text-sm text-gray-600" id="contentCount">0 characters</p>
                </div>

                <?php if ($platform['image_path']): ?>
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700">Current Media</label>
                    <div class="grid grid-cols-2 gap-4" id="mediaGrid">
                        <?php 
                        $media_files = json_decode($platform['image_path'], true);
                        foreach ($media_files as $media): 
                            $ext = pathinfo($media, PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                        <div class="relative group" data-media="<?php echo htmlspecialchars($media); ?>">
                            <img src="../<?php echo htmlspecialchars($media); ?>" alt="Platform Media" 
                                 class="rounded-xl shadow-lg w-full h-48 object-cover cursor-pointer"
                                 onclick="previewImage('../' + '<?php echo htmlspecialchars($media); ?>')">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-xl">
                                <input type="hidden" name="keep_media[]" value="<?php echo htmlspecialchars($media); ?>">
                                <button type="button" class="text-white hover:text-red-400 transition-colors duration-200" 
                                        onclick="removeMedia(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add New Media</label>
                    <div class="space-y-4">
                        <label class="cursor-pointer inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gray-100 text-gray-700 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Add Media
                            <input type="file" name="media[]" multiple class="hidden" accept="image/*" id="mediaInput">
                        </label>
                        <div id="mediaPreview" class="grid grid-cols-2 gap-4"></div>
                        <p class="text-sm text-gray-600">Supported formats: JPG, PNG, GIF (Max: 5MB per file)</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="candidate_dashboard.php" class="px-6 py-2 rounded-xl text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-blue-500 hover:from-purple-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-white/90 flex items-center justify-center">
    <div class="max-w-4xl w-full p-4">
        <img id="modalImage" src="" alt="Preview" class="max-h-[80vh] w-auto mx-auto rounded-xl shadow-2xl">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const contentCount = document.getElementById('contentCount');
    const mediaInput = document.getElementById('mediaInput');
    const mediaPreview = document.getElementById('mediaPreview');
    const platformForm = document.getElementById('platformForm');

    // Character counter
    function updateCharCount() {
        const count = contentTextarea.value.length;
        contentCount.textContent = `${count} characters`;
    }
    contentTextarea.addEventListener('input', updateCharCount);
    updateCharCount();

    // Media preview
    mediaInput.addEventListener('change', function() {
        Array.from(this.files).forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                alert(`File ${file.name} exceeds 5MB limit`);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="rounded-xl shadow-lg w-full h-48 object-cover cursor-pointer"
                         onclick="previewImage(this.src)">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-xl">
                        <button type="button" class="text-white hover:text-red-400 transition-colors duration-200" 
                                onclick="removePreview(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                mediaPreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
});

// Remove preview image
function removePreview(button) {
    button.closest('.relative').remove();
}

// Remove existing media
function removeMedia(button) {
    const mediaContainer = button.closest('.relative');
    const input = mediaContainer.querySelector('input[name="keep_media[]"]');
    input.remove();
    mediaContainer.remove();
}

// Image preview functionality
function previewImage(src) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = src;
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
<?php require_once '../includes/footer.php'; ?>