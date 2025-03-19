<?php
include_once __DIR__ . "/../../config/init.php";

// Add error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $currentDateTime = date('Y-m-d H:i:s');
    
    // Get elections that just ended
    $stmt = $db->prepare("SELECT id FROM election_settings 
                         WHERE election_end < :current_time 
                         AND status != 'ended'");
    $stmt->execute(['current_time' => $currentDateTime]);
    $endedElections = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($endedElections as $electionId) {
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Update election status
            $updateStatus = $db->prepare("UPDATE election_settings 
                                        SET status = 'ended', is_active = 0 
                                        WHERE id = :election_id");
            $updateStatus->execute(['election_id' => $electionId]);
            
            // Calculate and store final results
            $resultsSql = "INSERT INTO election_results 
                          (election_id, position_id, candidate_id, vote_count, rank)
                          SELECT 
                              :election_id,
                              p.position_id,
                              c.candidate_id,
                              COUNT(v.vote_id) as vote_count,
                              DENSE_RANK() OVER (
                                  PARTITION BY p.position_id 
                                  ORDER BY COUNT(v.vote_id) DESC
                              ) as rank
                          FROM positions p
                          LEFT JOIN candidates c ON p.position_id = c.candidate_position
                          LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
                              AND v.election_id = :election_id
                          WHERE c.candidate_id IS NOT NULL
                          GROUP BY p.position_id, c.candidate_id";
            
            $storeResults = $db->prepare($resultsSql);
            $storeResults->execute(['election_id' => $electionId]);
            
            // Create empty signature record
            $signatureSql = "INSERT INTO election_result_signatures (election_id) VALUES (:election_id)";
            $storeSignatures = $db->prepare($signatureSql);
            $storeSignatures->execute(['election_id' => $electionId]);
            
            $db->commit();
            echo "status_updated";
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    if (empty($endedElections)) {
        echo "no_change";
    }
} catch (Exception $e) {
    error_log("Error in check_election_status: " . $e->getMessage());
    echo "error";
} 