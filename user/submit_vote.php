<?php
session_start();
require 'db.php';
require_once 'check_election_status.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the incoming request
error_log("Received vote submission. POST data: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));

try {
    // Check if user is logged in
    if (!isset($_SESSION['id']) && !isset($_SESSION['student_id'])) {
        throw new Exception("User not logged in");
    }

    // Get student ID from session
    $student_id = $_SESSION['id'] ?? $_SESSION['student_id'];
    error_log("Student ID from session: " . $student_id);

    // Check if votes were submitted
    if (!isset($_POST['votes']) || empty($_POST['votes'])) {
        throw new Exception("No votes received");
    }

    // Get the votes data
    $votes = $_POST['votes'];
    error_log("Received votes: " . print_r($votes, true));

    // Get current election ID - only check for active flag
    $query = "SELECT id FROM election_settings WHERE is_active = 1 LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $election = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$election) {
        throw new Exception('No active election found. Please refresh the page.');
    }

    // Start transaction
    $conn->beginTransaction();

    // Check if student has already voted in this election
    $checkVoteQuery = "SELECT COUNT(*) FROM votes 
                       WHERE student_id = :student_id 
                       AND election_id = :election_id";
    $stmt = $conn->prepare($checkVoteQuery);
    $stmt->execute([
        ':student_id' => $student_id,
        ':election_id' => $election['id']
    ]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception("You have already voted in this election");
    }

    // Insert each vote
    foreach ($votes as $position_id => $candidate_id) {
        // Get candidate's partylist
        $getPartylistQuery = "SELECT partylist_id FROM candidates WHERE candidate_id = ?";
        $stmt = $conn->prepare($getPartylistQuery);
        $stmt->execute([$candidate_id]);
        $partylist_id = $stmt->fetchColumn();

        // Insert vote
        $query = "INSERT INTO votes (student_id, candidate_id, position_id, partylist_id, election_id) 
                  VALUES (:student_id, :candidate_id, :position_id, :partylist_id, :election_id)";
        $stmt = $conn->prepare($query);
        
        $stmt->execute([
            ':student_id' => $student_id,
            ':candidate_id' => $candidate_id,
            ':position_id' => $position_id,
            ':partylist_id' => $partylist_id,
            ':election_id' => $election['id']
        ]);
        
        error_log("Inserted vote - Position: $position_id, Candidate: $candidate_id");
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Votes submitted successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Vote submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>