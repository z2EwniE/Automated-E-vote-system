<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug logging
error_log("Fetch voting data started");
error_log("Session data: " . print_r($_SESSION, true));

try {
    global $conn;
    
    if (!$conn) {
        throw new PDOException("Database connection not available");
    }

    // Test database connection and tables
    try {
        // Test positions table
        $testQuery = "SELECT COUNT(*) FROM positions";
        $count = $conn->query($testQuery)->fetchColumn();
        error_log("Number of positions: " . $count);
        
        // Test candidates table
        $testQuery = "SELECT COUNT(*) FROM candidates";
        $count = $conn->query($testQuery)->fetchColumn();
        error_log("Number of candidates: " . $count);
        
        // Test specific candidate data
        $testQuery = "SELECT * FROM candidates LIMIT 1";
        $candidate = $conn->query($testQuery)->fetch(PDO::FETCH_ASSOC);
        error_log("Sample candidate data: " . print_r($candidate, true));
    } catch (PDOException $e) {
        error_log("Table test failed: " . $e->getMessage());
        throw $e;
    }

    // Get all positions
    $positionsQuery = "SELECT * FROM positions ORDER BY position_id";
    $positionsStmt = $conn->query($positionsQuery);
    $positions = $positionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get current active election
    $activeElectionQuery = "SELECT id FROM election_settings WHERE is_active = 1 AND election_end > NOW() LIMIT 1";
    $activeElectionStmt = $conn->query($activeElectionQuery);
    $activeElection = $activeElectionStmt->fetch(PDO::FETCH_ASSOC);

    if (!$activeElection) {
        error_log("No active election found");
        echo json_encode(['error' => 'No active election available']);
        exit;
    }

    $activeElectionId = $activeElection['id'];
    error_log("Active election ID: " . $activeElectionId);

    if (empty($positions)) {
        error_log("No positions found in database");
        echo json_encode(['error' => 'No positions found']);
        exit;
    }
    
    error_log("Found positions: " . print_r($positions, true));
    
    $result = [];
    
    foreach ($positions as $position) {
        $position_id = $position['position_id'];
        $position_name = $position['position_name'];
        
        // Get candidates for this position
        // Modify the candidates query to include election period filter
        $candidatesQuery = "
            SELECT 
                c.candidate_id,
                c.candidate_name,
                c.candidate_position,
                c.candidate_image_path,
                c.partylist_id,
                c.department as department_id,
                d.department_name,
                pl.partylist_name
            FROM candidates c
            LEFT JOIN department d ON c.department = d.department_id
            LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
            WHERE c.candidate_position = :position_id
            AND c.election_id = :election_id";  // Add election period filter
        
        $candidatesStmt = $conn->prepare($candidatesQuery);
        $candidatesStmt->bindParam(':position_id', $position_id, PDO::PARAM_INT);
        $candidatesStmt->bindParam(':election_id', $activeElectionId, PDO::PARAM_INT);
        $candidatesStmt->execute();
        
        $candidates = $candidatesStmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Candidates for position {$position_name}: " . print_r($candidates, true));
        
        if (!empty($candidates)) {
            $result[] = [
                'position_id' => $position_id,
                'position_name' => $position_name,
                'candidates' => array_map(function($candidate) {
                    // Ensure all required fields are present
                    return [
                        'candidate_id' => $candidate['candidate_id'],
                        'candidate_name' => $candidate['candidate_name'],
                        'candidate_position' => $candidate['candidate_position'],
                        'candidate_image_path' => $candidate['candidate_image_path'] ?? 'img/avatars/avatar-2.jpg',
                        'department_name' => $candidate['department_name'] ?? '',
                        'partylist_name' => $candidate['partylist_name'] ?? '',
                        'partylist_id' => $candidate['partylist_id']
                    ];
                }, $candidates)
            ];
        }
    }
    
    if (empty($result)) {
        error_log("No candidates found for any position");
        echo json_encode(['error' => 'No candidates found']);
        exit;
    }
    
    $response = ['positions' => $result];
    error_log("Final response: " . print_r($response, true));
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>