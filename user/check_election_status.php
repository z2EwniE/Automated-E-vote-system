<?php
require_once 'db.php';

// Set timezone
date_default_timezone_set('Asia/Manila');

// Debug: Check database connection and election data
try {
    global $conn;
    $debug_stmt = $conn->query("SELECT * FROM election_settings");
    $all_elections = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("All elections in database: " . print_r($all_elections, true));
} catch (PDOException $e) {
    error_log("Debug query error: " . $e->getMessage());
}

function isElectionPeriod() {
    global $conn;
    
    try {
        // Simply check if there's an active election
        $stmt = $conn->prepare("
            SELECT 
                id,
                election_title,
                election_start,
                election_end,
                is_active
            FROM election_settings 
            WHERE is_active = 1 
            LIMIT 1
        ");
        $stmt->execute();
        $election = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug the query result
        error_log("Active election query result: " . print_r($election, true));
        
        if (!$election) {
            error_log("No active election found");
            return [
                'status' => false,
                'title' => 'No Active Election',
                'message' => 'There is currently no active election.'
            ];
        }

        // If we found an active election, return true status
        return [
            'status' => true,
            'title' => $election['election_title'],
            'message' => 'Election is currently ongoing until ' . 
                        (new DateTime($election['election_end']))->format('F j, Y \a\t g:i A')
        ];

    } catch (PDOException $e) {
        error_log("Database error in isElectionPeriod: " . $e->getMessage());
        return [
            'status' => false,
            'title' => 'System Error',
            'message' => 'Unable to check election status. Please try again later.'
        ];
    }
}

// Debug: Test the function immediately
$testResult = isElectionPeriod();
error_log("Test election status result: " . print_r($testResult, true));
?> 