<?php
session_start();
require_once '../config/database.php';

$page_title = "Student Login";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $student_id = $_POST['student_id'];
    $last_name = $_POST['last_name'];
    
    $query = "SELECT * FROM students WHERE student_id = :student_id AND last_name = :last_name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":student_id", $student_id);
    $stmt->bindParam(":last_name", $last_name);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
        header("Location: ../dashboard/index.php");
        exit();
    } else {
        $error = "Invalid student ID or last name";
    }
}

require_once '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-800 to-indigo-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 transform hover:-translate-y-1 transition-all duration-300">
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden border border-white/20">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                <h2 class="text-center text-2xl font-extrabold text-white flex items-center justify-center">
                    <svg class="h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Student Login
                </h2>
            </div>
            
            <div class="bg-white/90 p-8">
                <?php if (isset($error)): ?>
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-red-700"><?php echo $error; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" onsubmit="showSpinner()" class="space-y-6">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Student ID
                        </label>
                        <input type="text" id="student_id" name="student_id" required
                               class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your Student ID">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name" required
                               class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter your Last Name">
                    </div>
                    
                    <div class="space-y-3">
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:-translate-y-0.5">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-blue-200 group-hover:text-blue-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            <span class="spinner-border spinner-border-sm mr-2 hidden" role="status" aria-hidden="true"></span>
                            Login
                        </button>
                        
                    </div>
                    
                
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showSpinner() {
        document.querySelector('.spinner-border').style.display = 'inline-block';
    }
</script>

<?php require_once '../includes/footer.php'; ?>