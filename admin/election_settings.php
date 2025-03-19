<?php
require_once '../config/config.php';
require_once '../includes/auth_validate.php';

// Fetch existing election periods
$elections = $db->query("SELECT * FROM election_settings ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get current active election if any
$active_election = $db->query("SELECT * FROM election_settings WHERE is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container-fluid p-0">
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3>Election Period Settings</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create New Election Period</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="election_settings_process.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Election Title</label>
                                    <input type="text" class="form-control" name="election_title" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Start Date & Time</label>
                                    <input type="datetime-local" class="form-control" name="election_start" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">End Date & Time</label>
                                    <input type="datetime-local" class="form-control" name="election_end" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                                <label class="form-check-label" for="is_active">Make Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Election Period</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Election Periods</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($elections as $election): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($election['election_title']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($election['election_start'])); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($election['election_end'])); ?></td>
                                <td>
                                    <?php if ($election['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="toggle_election.php?id=<?php echo $election['id']; ?>" 
                                       class="btn btn-sm <?php echo $election['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                        <?php echo $election['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                    <a href="delete_election.php?id=<?php echo $election['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this election period?')">
                                        Delete
                                    </a>
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

<?php include '../includes/footer.php'; ?> 