<?php
include_once __DIR__ . "/../../config/init.php";

if (isset($_POST['partylist_id'])) {
    $partylistId = $_POST['partylist_id'];
    
    try {
        // First get the partylist name
        $stmt = $db->prepare("SELECT partylist_name FROM partylists WHERE partylist_id = ?");
        $stmt->execute([$partylistId]);
        $partylist = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get all candidates for this partylist with additional details
        $stmt = $db->prepare("
            SELECT 
                c.candidate_name,
                c.candidate_image_path,
                p.position_name,
                d.department_name,
                es.election_title,
                CASE 
                    WHEN es.is_active = 1 AND es.election_end > NOW() THEN 'Active'
                    WHEN es.election_end <= NOW() THEN 'Ended'
                    ELSE 'Upcoming'
                END as election_status
            FROM candidates c
            JOIN positions p ON c.candidate_position = p.position_id
            JOIN department d ON c.department = d.department_id
            LEFT JOIN election_settings es ON c.election_id = es.id
            WHERE c.partylist_id = ?
            ORDER BY es.election_start DESC, p.position_id
        ");
        $stmt->execute([$partylistId]);
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'partylist_name' => $partylist['partylist_name'],
            'candidates' => $candidates
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}