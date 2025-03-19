<?php
include_once __DIR__ . "/../../config/init.php";

// Check if user is logged in and has appropriate permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['signatures'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

try {
    // Start transaction
    $db->beginTransaction();

    // Update signatures in the database
    // You'll need to create a table for storing these signatures
    $sql = "INSERT INTO election_signatures 
            (election_year, ssc_advisor_name, ssc_advisor_position, 
             osa_name, osa_position, twg_name, twg_position,
             extractor_name, extractor_position, admin_name, admin_position)
            VALUES 
            (:year, :ssc_name, :ssc_pos, :osa_name, :osa_pos, 
             :twg_name, :twg_pos, :ext_name, :ext_pos, :admin_name, :admin_pos)
            ON DUPLICATE KEY UPDATE
            ssc_advisor_name = VALUES(ssc_advisor_name),
            ssc_advisor_position = VALUES(ssc_advisor_position),
            osa_name = VALUES(osa_name),
            osa_position = VALUES(osa_position),
            twg_name = VALUES(twg_name),
            twg_position = VALUES(twg_position),
            extractor_name = VALUES(extractor_name),
            extractor_position = VALUES(extractor_position),
            admin_name = VALUES(admin_name),
            admin_position = VALUES(admin_position)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        'year' => date('Y'),
        'ssc_name' => $input['signatures']['ssc_advisor']['name'],
        'ssc_pos' => $input['signatures']['ssc_advisor']['position'],
        'osa_name' => $input['signatures']['osa_coordinator']['name'],
        'osa_pos' => $input['signatures']['osa_coordinator']['position'],
        'twg_name' => $input['signatures']['technical_group']['name'],
        'twg_pos' => $input['signatures']['technical_group']['position'],
        'ext_name' => $input['signatures']['extractor']['name'],
        'ext_pos' => $input['signatures']['extractor']['position'],
        'admin_name' => $input['signatures']['administrator']['name'],
        'admin_pos' => $input['signatures']['administrator']['position']
    ]);

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Signatures updated successfully']);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 