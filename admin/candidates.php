<?php
include_once __DIR__ . "/../config/init.php";

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            // In the switch case 'add' section
            case 'add':
                $name = $_POST['candidate_name'];
                $position = $_POST['candidate_position'];
                $partylist = $_POST['partylist_id'];
                $department = $_POST['department'];
                $election_id = $_POST['election_id'];
                
                // Handle file upload
                $image_path = null;
                if (isset($_FILES['candidate_image']) && $_FILES['candidate_image']['error'] == 0) {
                    $target_dir = "uploads/candidates/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $image_path = $target_dir . time() . '_' . basename($_FILES['candidate_image']['name']);
                    move_uploaded_file($_FILES['candidate_image']['tmp_name'], $image_path);
                }
            
                $sql = "INSERT INTO candidates (candidate_name, candidate_position, partylist_id, department, candidate_image_path, election_id) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$name, $position, $partylist, $department, $image_path, $election_id]);
                break;

            case 'edit':
                $id = $_POST['candidate_id'];
                $name = $_POST['candidate_name'];
                $position = $_POST['candidate_position'];
                $partylist = $_POST['partylist_id'];
                $department = $_POST['department'];

                // Handle file upload for edit
                if (isset($_FILES['candidate_image']) && $_FILES['candidate_image']['error'] == 0) {
                    $target_dir = "uploads/candidates/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $image_path = $target_dir . time() . '_' . basename($_FILES['candidate_image']['name']);
                    move_uploaded_file($_FILES['candidate_image']['tmp_name'], $image_path);
                    
                    $sql = "UPDATE candidates SET candidate_name=?, candidate_position=?, partylist_id=?, department=?, candidate_image_path=? WHERE candidate_id=?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$name, $position, $partylist, $department, $image_path, $id]);
                } else {
                    $sql = "UPDATE candidates SET candidate_name=?, candidate_position=?, partylist_id=?, department=? WHERE candidate_id=?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$name, $position, $partylist, $department, $id]);
                }
                break;

            case 'delete':
                $id = $_POST['candidate_id'];
                $sql = "DELETE FROM candidates WHERE candidate_id=?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$id]);
                break;
        }
        
        // Redirect to prevent form resubmission
        header("Location: candidates.php");
        exit();
    }
}

// Fetch all candidates with related information
// Fetch election periods for dropdown - Show most recent first and indicate active/ended status
$stmt = $db->prepare("
    SELECT id, election_title, 
           CASE 
               WHEN is_active = 1 AND election_end > NOW() THEN 'Active'
               WHEN election_end <= NOW() THEN 'Ended'
               ELSE 'Upcoming'
           END as status
    FROM election_settings 
    ORDER BY election_start DESC");
$stmt->execute();
$election_periods = $stmt->fetchAll();

// Get selected election period from URL parameter or default to active election
$selected_election = isset($_GET['election_id']) ? $_GET['election_id'] : null;

// If no election period is selected, get the active one
if (!$selected_election) {
    $stmt = $db->prepare("SELECT id FROM election_settings WHERE is_active = 1 AND election_end > NOW() LIMIT 1");
    $stmt->execute();
    $active_election = $stmt->fetchColumn();
    $selected_election = $active_election ? $active_election : null;
}

// Get selected election period
$selected_election = isset($_GET['election_id']) ? $_GET['election_id'] : null;

// Modify the candidates query to filter by election period if one is selected
$sql = "SELECT c.*, p.partylist_name, d.department_name, pos.position_name,
        CASE 
            WHEN es.is_active = 1 AND es.election_end > NOW() THEN 'Active'
            WHEN es.election_end <= NOW() THEN 'Ended'
            ELSE 'Upcoming'
        END as election_status
        FROM candidates c 
        LEFT JOIN partylists p ON c.partylist_id = p.partylist_id 
        LEFT JOIN department d ON c.department = d.department_id
        LEFT JOIN positions pos ON c.candidate_position = pos.position_id
        LEFT JOIN election_settings es ON c.election_id = es.id";

// Only add WHERE clause if election is selected
if ($selected_election) {
    $sql .= " WHERE c.election_id = :election_id";
} else {
    // If no election selected, show only active/upcoming election candidates
    $sql .= " WHERE (es.is_active = 1 OR es.election_end > NOW())";
}
$sql .= " ORDER BY c.candidate_id DESC";

$stmt = $db->prepare($sql);
if ($selected_election) {
    $stmt->bindParam(':election_id', $selected_election);
}
$stmt->execute();
$candidates = $stmt->fetchAll();

// Fetch departments for dropdown
$stmt = $db->prepare("SELECT * FROM department");
$stmt->execute();
$departments = $stmt->fetchAll();

// Fetch partylists for dropdown
$stmt = $db->prepare("SELECT * FROM partylists");
$stmt->execute();
$partylists = $stmt->fetchAll();

// Fetch positions for dropdown
$stmt = $db->prepare("SELECT * FROM positions");
$stmt->execute();
$positions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="qoshima">
    <meta name="keywords" content=" Admin, dashboard, responsive, css, sass, html, theme, front-end">
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <title>E-Vote System</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Choose your prefered color scheme -->
    <link href="css/light.css" rel="stylesheet">

    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css" >

    <style>
        body {
            opacity: 0;
        }

        .card {
            border: none;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.08);
            border-radius: 0.5rem;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .candidate-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            margin: 0 0.25rem;
        }

        .btn-edit {
            background-color: #2196F3;
            color: white;
            border: none;
        }

        .btn-edit:hover {
            background-color: #1976D2;
            transform: translateY(-1px);
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0;
        }

        .election-select {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.5rem 2.25rem 0.5rem 0.75rem;
            font-size: 0.875rem;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .add-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .add-btn:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }

        .modal-content {
            border-radius: 0.5rem;
            border: none;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .form-control, .form-select {
            border-radius: 0.25rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>
        <div class="main">
            <?php include_once 'includes/navbar.php'; ?>
            
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h1 class="page-title">Candidates Management</h1>
                            <select class="election-select ms-3" onchange="window.location.href='candidates.php?election_id=' + this.value">
                                <option value="">Select Election Period</option>
                                <?php foreach ($election_periods as $period): ?>
                                    <option value="<?= $period['id'] ?>" <?= $selected_election == $period['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($period['election_title']) ?> 
                                        (<?= $period['status'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                        // Only show Add button if no election is selected (showing all active) or if selected election is not ended
                        $showAddButton = true;
                        if ($selected_election) {
                            foreach ($election_periods as $period) {
                                if ($period['id'] == $selected_election && $period['status'] == 'Ended') {
                                    $showAddButton = false;
                                    break;
                                }
                            }
                        }
                        if ($showAddButton):
                        ?>
                        <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                            <i class="fas fa-plus me-2"></i>Add New Candidate
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>Position</th>
                                                    <th>Partylist</th>
                                                    <th>Department</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($candidates as $candidate): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($candidate['candidate_id']) ?></td>
                                                    <td>
                                                        <?php if ($candidate['candidate_image_path']): ?>
                                                            <img src="<?= htmlspecialchars($candidate['candidate_image_path']) ?>" 
                                                                 alt="Candidate Image" class="candidate-img">
                                                        <?php else: ?>
                                                            <img src="img/avatars/placeholder.jpg" 
                                                                 alt="Placeholder" class="candidate-img">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($candidate['candidate_name']) ?></td>
                                                    <td><?= htmlspecialchars($candidate['position_name']) ?></td>
                                                    <td><?= htmlspecialchars($candidate['partylist_name']) ?></td>
                                                    <td><?= htmlspecialchars($candidate['department_name']) ?></td>
                                                    <td>
                                                        <?php 
                                                        $isEnded = false;
                                                        foreach ($election_periods as $period) {
                                                            if ($period['id'] == $candidate['election_id'] && $period['status'] == 'Ended') {
                                                                $isEnded = true;
                                                                break;
                                                            }
                                                        }
                                                        if (!$isEnded): 
                                                        ?>
                                                            <button type="button" class="btn btn-edit btn-action" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#editCandidateModal"
                                                                    data-id="<?= $candidate['candidate_id'] ?>"
                                                                    data-name="<?= htmlspecialchars($candidate['candidate_name']) ?>"
                                                                    data-position="<?= $candidate['candidate_position'] ?>"
                                                                    data-partylist="<?= $candidate['partylist_id'] ?>"
                                                                    data-department="<?= $candidate['department'] ?>"
                                                                    data-election="<?= $candidate['election_id'] ?>">
                                                                    <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-delete btn-action"
                                                                    data-bs-toggle="modal" data-bs-target="#deleteCandidateModal"
                                                                    data-id="<?= $candidate['candidate_id'] ?>"
                                                                    data-name="<?= htmlspecialchars($candidate['candidate_name']) ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Add Candidate Modal -->
            <div class="modal fade" id="addCandidateModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="add">
                                
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="candidate_name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Position</label>
                                    <select class="form-select" name="candidate_position" required>
                                        <?php foreach ($positions as $position): ?>
                                            <option value="<?= $position['position_id'] ?>">
                                                <?= htmlspecialchars($position['position_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Partylist</label>
                                    <select class="form-select" name="partylist_id" required>
                                        <?php foreach ($partylists as $partylist): ?>
                                            <option value="<?= $partylist['partylist_id'] ?>">
                                                <?= htmlspecialchars($partylist['partylist_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-select" name="department" required>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['department_id'] ?>">
                                                <?= htmlspecialchars($dept['department_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Election Period</label>
                                    <select class="form-select" name="election_id" required>
                                        <?php foreach ($election_periods as $period): ?>
                                            <?php if ($period['status'] !== 'Ended'): ?>
                                                <option value="<?= $period['id'] ?>" <?= ($selected_election == $period['id'] ? 'selected' : '') ?>>
                                                    <?= htmlspecialchars($period['election_title']) ?> (<?= $period['status'] ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" class="form-control" name="candidate_image" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Candidate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Candidate Modal -->
            <div class="modal fade" id="editCandidateModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="candidate_id" id="edit-candidate-id">
                                
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="candidate_name" id="edit-name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Position</label>
                                    <select class="form-select" name="candidate_position" id="edit-position" required>
                                        <?php foreach ($positions as $position): ?>
                                            <option value="<?= $position['position_id'] ?>">
                                                <?= htmlspecialchars($position['position_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Partylist</label>
                                    <select class="form-select" name="partylist_id" id="edit-partylist" required>
                                        <?php foreach ($partylists as $partylist): ?>
                                            <option value="<?= $partylist['partylist_id'] ?>">
                                                <?= htmlspecialchars($partylist['partylist_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <select class="form-select" name="department" id="edit-department" required>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['department_id'] ?>">
                                                <?= htmlspecialchars($dept['department_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Election Period</label>
                                    <select class="form-select" name="election_id" id="edit-election" required>
                                        <?php foreach ($election_periods as $period): ?>
                                            <option value="<?= $period['id'] ?>">
                                                <?= htmlspecialchars($period['election_title']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Image</label>
                                    <input type="file" class="form-control" name="candidate_image" accept="image/*">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Candidate Modal -->
            <div class="modal fade" id="deleteCandidateModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="candidate_id" id="delete-candidate-id">
                                <p>Are you sure you want to delete <span id="delete-candidate-name"></span>?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <strong>E-Vote System</strong> &copy; <?php echo date('Y'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="js/app.js"></script>
    <script>
        // Handle Edit Button Click
        // In the edit button click handler
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const position = this.dataset.position;
                const partylist = this.dataset.partylist;
                const department = this.dataset.department;
                const election = this.dataset.election;
        
                document.getElementById('edit-candidate-id').value = id;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-position').value = position;
                document.getElementById('edit-partylist').value = partylist;
                document.getElementById('edit-department').value = department;
                document.getElementById('edit-election').value = election;
            });
        });

        // Handle Delete Button Click
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;

                document.getElementById('delete-candidate-id').value = id;
                document.getElementById('delete-candidate-name').textContent = name;
            });
        });
    </script>
</body>
</html>