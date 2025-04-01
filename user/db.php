<?php
try {
    // Database configuration
    $host = '139.99.97.250'; 
    $dbname = 'evote'; 
    $username = 'evote';   
    $password = '2Ty4th4TVHnTUFsL'; 

    // Create PDO connection with proper error handling
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Test connection
    $test = $conn->query("SELECT 1");
    error_log("Database connection successful");
    
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

// Test if election_settings table exists and has data
try {
    $result = $conn->query("
        SELECT id, election_title, is_active, election_start, election_end 
        FROM election_settings 
        WHERE is_active = 1
    ");
    $activeElection = $result->fetch(PDO::FETCH_ASSOC);
    error_log("Active election check: " . print_r($activeElection, true));
} catch (PDOException $e) {
    error_log("Election settings check failed: " . $e->getMessage());
}

// Test if tables exist
try {
    $tables = ['positions', 'candidates', 'department', 'partylists', 'students', 'votes'];
    foreach ($tables as $table) {
        $result = $conn->query("SELECT 1 FROM $table LIMIT 1");
        error_log("Table $table exists and is accessible");
    }
} catch (PDOException $e) {
    error_log("Table check failed: " . $e->getMessage());
}

function isCandidate() {
    global $conn;
    if (!isset($_SESSION['id'])) return false;

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM candidates WHERE student_id = ?");
        $stmt->execute([$_SESSION['id']]);
        return (int)$stmt->fetch(PDO::FETCH_COLUMN) > 0;
    } catch (PDOException $e) {
        error_log("Error in isCandidate: " . $e->getMessage());
        return false;
    }
}

function fetchStudent($id) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error in fetchStudent: " . $e->getMessage());
        return null;
    }
}

function hasStudentApplied() {
    global $conn;
    if (!isset($_SESSION['id'])) return false;

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM candidates WHERE student_id = ?");
        $stmt->execute([$_SESSION['id']]);
        return (int)$stmt->fetch(PDO::FETCH_COLUMN) > 0;
    } catch (PDOException $e) {
        error_log("Error in hasStudentApplied: " . $e->getMessage());
        return false;
    }
}

function hasVoted() {
    global $conn;
    if (!isset($_SESSION['id'])) return false;

    try {
        // Get current active election
        $stmt = $conn->prepare("SELECT id FROM election_settings WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $election = $stmt->fetch();
        
        if (!$election) return false;

        // Check if user has voted in this election
        $stmt = $conn->prepare("SELECT COUNT(*) FROM votes WHERE student_id = ? AND election_id = ?");
        $stmt->execute([$_SESSION['id'], $election['id']]);
        return (int)$stmt->fetch(PDO::FETCH_COLUMN) > 0;
    } catch (PDOException $e) {
        error_log("Error in hasVoted: " . $e->getMessage());
        return false;
    }
}

function getVotingDetails() {
    global $conn;
    if (!isset($_SESSION['id'])) return null;

    try {
        // Get current active election
        $stmt = $conn->prepare("SELECT id FROM election_settings WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $election = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$election) return null;

        // Get voting details
        $stmt = $conn->prepare("
            SELECT 
                v.voted_at,
                c.candidate_name,
                c.candidate_image_path,
                p.position_name,
                pl.partylist_name,
                d.department_name
            FROM votes v
            JOIN candidates c ON v.candidate_id = c.candidate_id
            JOIN positions p ON v.position_id = p.position_id
            LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
            LEFT JOIN department d ON c.department = d.department_id
            WHERE v.student_id = ? 
            AND v.election_id = ?
            ORDER BY p.position_id ASC
        ");
        
        $stmt->execute([$_SESSION['id'], $election['id']]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    } catch (PDOException $e) {
        error_log("Error in getVotingDetails: " . $e->getMessage());
        return null;
    }
}
?>