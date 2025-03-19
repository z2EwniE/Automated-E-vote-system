<?php
session_start();
require_once '../config/database.php';

$page_title = "Candidate Registration";
$database = new Database();
$db = $database->getConnection();

// Fetch positions
$positions_query = "SELECT * FROM positions ORDER BY position_name";
$positions_stmt = $db->query($positions_query);
$positions = $positions_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch departments
$departments_query = "SELECT * FROM department ORDER BY department_name";
$departments_stmt = $db->query($departments_query);
$departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch partylists
$partylists_query = "SELECT * FROM partylists ORDER BY partylist_name";
$partylists_stmt = $db->query($partylists_query);
$partylists = $partylists_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['candidate_image']) && $_FILES['candidate_image']['error'] == 0) {
            $upload_dir = '../uploads/candidates/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['candidate_image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '_' . $_FILES['candidate_image']['name'];
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['candidate_image']['tmp_name'], $upload_path)) {
                $image_path = 'uploads/candidates/' . $new_filename;
            }
        }
        
        // Insert into candidates table
        $candidate_query = "INSERT INTO candidates (candidate_position, candidate_name, partylist_id, department, candidate_image_path) 
                          VALUES (:position, :name, :partylist, :department, :image_path)";
        $candidate_stmt = $db->prepare($candidate_query);
        $candidate_stmt->execute([
            ':position' => $_POST['position'],
            ':name' => $_POST['full_name'],
            ':partylist' => $_POST['partylist'],
            ':department' => $_POST['department'],
            ':image_path' => $image_path
        ]);
        
        $candidate_id = $db->lastInsertId();
        
        // Insert into candidate_accounts table
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $account_query = "INSERT INTO candidate_accounts (candidate_id, username, password) 
                         VALUES (:candidate_id, :username, :password)";
        $account_stmt = $db->prepare($account_query);
        $account_stmt->execute([
            ':candidate_id' => $candidate_id,
            ':username' => $_POST['username'],
            ':password' => $hashed_password
        ]);
        
        $db->commit();
        $success = "Registration successful! You can now login.";
        
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Registration failed: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-800 to-indigo-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8 transform hover:-translate-y-1 transition-all duration-300">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden border border-white/20">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                <h2 class="text-center text-2xl font-extrabold text-white flex items-center justify-center">
                    <svg class="h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Candidate Registration
                </h2>
            </div>
            
            <div class="bg-white/90 p-8">
                <?php if (isset($success)): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-green-700"><?php echo $success; ?></span>
                        </div>
                        <div class="mt-3">
                            <a href="candidate_login.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Go to Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-red-700"><?php echo $error; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Full Name
                            </label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                   placeholder="Enter your full name">
                        </div>
                        
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Position
                            </label>
                            <select id="position" name="position" required
                                    class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                <option value="">Select Position</option>
                                <?php foreach ($positions as $position): ?>
                                        <option value="<?php echo $position['position_id']; ?>">
                                            <?php echo $position['position_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Department
                                </label>
                                <select id="department" name="department" required
                                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department['department_id']; ?>">
                                            <?php echo $department['department_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="partylist" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Partylist
                                </label>
                                <select id="partylist" name="partylist" required
                                        class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                    <option value="">Select Partylist</option>
                                    <?php foreach ($partylists as $partylist): ?>
                                        <option value="<?php echo $partylist['partylist_id']; ?>">
                                            <?php echo $partylist['partylist_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Username
                                </label>
                                <input type="text" id="username" name="username" required
                                       class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Choose a username">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Password
                                </label>
                                <input type="password" id="password" name="password" required
                                       class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Choose a password">
                            </div>
                        </div>
                        
                        <div class="col-span-full">
                            <label for="candidate_image" class="block text-sm font-medium text-gray-700 flex items-center">
                                <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Profile Image
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="candidate_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="candidate_image" name="candidate_image" type="file" class="sr-only" accept="image/*" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-span-full space-y-3">
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:-translate-y-0.5">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Register
                            </button>
                            
                            <a href="candidate_login.php" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:-translate-y-0.5">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once '../includes/footer.php'; ?>