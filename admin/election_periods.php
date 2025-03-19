<?php
include_once __DIR__ . "/../config/init.php";

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = $_POST['period_name'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                
                $sql = "INSERT INTO election_periods (period_name, start_date, end_date) VALUES (?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$name, $start_date, $end_date]);
                break;

            case 'edit':
                $id = $_POST['period_id'];
                $name = $_POST['period_name'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $status = $_POST['status'];

                $sql = "UPDATE election_periods SET period_name=?, start_date=?, end_date=?, status=? WHERE period_id=?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$name, $start_date, $end_date, $status, $id]);
                break;

            case 'delete':
                $id = $_POST['period_id'];
                
                // Check if there are any candidates in this period
                $stmt = $db->prepare("SELECT COUNT(*) FROM candidates WHERE election_period_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $_SESSION['error'] = "Cannot delete election period that has candidates";
                } else {
                    $sql = "DELETE FROM election_periods WHERE period_id=?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$id]);
                }
                break;
        }
        
        // Redirect to prevent form resubmission
        header("Location: election_periods.php");
        exit();
    }
}

// Fetch all election periods
$sql = "SELECT *, 
        (SELECT COUNT(*) FROM candidates WHERE election_period_id = ep.period_id) as candidate_count 
        FROM election_periods ep 
        ORDER BY start_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$periods = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="qoshima">
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <title>Election Periods - E-Vote System</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="css/light.css" rel="stylesheet">
    <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css">
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>
        <div class="main">
            <?php include_once 'includes/navbar.php'; ?>
            
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <h1 class="h3 d-inline align-middle">Election Periods</h1>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                            <i class="fas fa-plus"></i> Add New Period
                        </button>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Candidates</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($periods as $period): ?>
                                                <tr>
                                                    <td><?php echo $period['period_id']; ?></td>
                                                    <td><?php echo htmlspecialchars($period['period_name']); ?></td>
                                                    <td><?php echo $period['start_date']; ?></td>
                                                    <td><?php echo $period['end_date']; ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $period['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <?php echo ucfirst($period['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $period['candidate_count']; ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editPeriodModal"
                                                                data-id="<?php echo $period['period_id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($period['period_name']); ?>"
                                                                data-start="<?php echo $period['start_date']; ?>"
                                                                data-end="<?php echo $period['end_date']; ?>"
                                                                data-status="<?php echo $period['status']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <?php if ($period['candidate_count'] == 0): ?>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#deletePeriodModal"
                                                                data-id="<?php echo $period['period_id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($period['period_name']); ?>">
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

            <!-- Add Period Modal -->
            <div class="modal fade" id="addPeriodModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Election Period</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="election_periods.php" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="add">
                                
                                <div class="mb-3">
                                    <label class="form-label">Period Name</label>
                                    <input type="text" class="form-control" name="period_name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Period</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Period Modal -->
            <div class="modal fade" id="editPeriodModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Election Period</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="election_periods.php" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="period_id" id="edit-period-id">
                                
                                <div class="mb-3">
                                    <label class="form-label">Period Name</label>
                                    <input type="text" class="form-control" name="period_name" id="edit-period-name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="edit-start-date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="edit-end-date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status" id="edit-status" required>
                                        <option value="active">Active</option>
                                        <option value="ended">Ended</option>
                                    </select>
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

            <!-- Delete Period Modal -->
            <div class="modal fade" id="deletePeriodModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Election Period</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="election_periods.php" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="period_id" id="delete-period-id">
                                <p>Are you sure you want to delete the election period "<span id="delete-period-name"></span>"?</p>
                                <p class="text-danger">This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Edit Button Click
            const editModal = document.getElementById('editPeriodModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const start = button.getAttribute('data-start');
                const end = button.getAttribute('data-end');
                const status = button.getAttribute('data-status');
                
                editModal.querySelector('#edit-period-id').value = id;
                editModal.querySelector('#edit-period-name').value = name;
                editModal.querySelector('#edit-start-date').value = start;
                editModal.querySelector('#edit-end-date').value = end;
                editModal.querySelector('#edit-status').value = status;
            });

            // Handle Delete Button Click
            const deleteModal = document.getElementById('deletePeriodModal');
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                
                deleteModal.querySelector('#delete-period-id').value = id;
                deleteModal.querySelector('#delete-period-name').textContent = name;
            });
        });
    </script>
</body>
</html>
