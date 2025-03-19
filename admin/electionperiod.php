<?php

            include_once __DIR__ . "/../config/init.php";

         
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

        <link rel="stylesheet" href="https://atugatran.github.io/FontAwesome6Pro/css/all.min.css">

        <style>
        body {
            opacity: 0;
        }

        th.sortable {
            cursor: pointer;
            white-space: nowrap;
        }

        th.sortable i {

            margin-left: 5px;
            font-size: 0.85em;
        }

        th {
            vertical-align: middle;
        }

        /* Add hover effect for better interaction */
        table thead th:hover {
            background-color: #f8f9fa;
        }

        /* Add padding for better spacing */
        table thead th,
        table tbody td {
            padding: 12px;
            vertical-align: middle;
        }

        /* Highlight the active column for better clarity */
        .table-striped tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Align the sort icon next to the text */
        th.sortable i {
            margin-left: 5px;
        }

        /* Custom styling for sort icons */
        th.sortable i.fa-sort-up,
        th.sortable i.fa-sort-down {
            color: #000;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 500;
        }
        .status-active {
            background-color: #28a745;
            color: white;
        }
        .status-inactive {
            background-color: #dc3545;
            color: white;
        }
        .status-ended {
            background-color: #6c757d;
            color: white;
        }
        </style>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>


    <body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
        <div class="wrapper">

            <?php
            include_once 'includes/sidebar.php';
        ?>

            <div class="main">

                <?php
                include_once 'includes/navbar.php';
            ?>
                <!-- start here -->
                <main class="content">
                    <div class="container-fluid p-0">



                        <div class="mb-3">
                            <h1 class="h3 d-inline align-middle">Election Period Management</h1>
                        </div>

                        <?php
                        $stmt = $db->query("SELECT COUNT(*) FROM election_settings WHERE is_active = 1");
                        $activeElectionCount = $stmt->fetchColumn();
                        ?>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Create New Election Period</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($activeElectionCount > 0): ?>
                                            <div class="alert alert-warning">
                                                Cannot create new election period while another election is active.
                                            </div>
                                        <?php else: ?>
                                        <form id="addElectionForm">
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
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active">
                                                    <label class="form-check-label" for="is_active">Make Active</label>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Create Election Period</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
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
                                                <?php
                                                $stmt = $db->query("SELECT * FROM election_settings ORDER BY created_at DESC");
                                                $elections = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($elections as $election) {
                                                    $currentTime = new DateTime();
                                                    $endTime = new DateTime($election['election_end']);
                                                    
                                                    $status = '';
                                                    $statusClass = '';
                                                    
                                                    if ($election['is_active']) {
                                                        if ($currentTime > $endTime) {
                                                            $status = 'Ended';
                                                            $statusClass = 'status-ended';
                                                        } else {
                                                            $status = 'Active';
                                                            $statusClass = 'status-active';
                                                        }
                                                    } else {
                                                        if ($currentTime > $endTime) {
                                                            $status = 'Ended';
                                                            $statusClass = 'status-ended';
                                                        } else {
                                                            $status = 'Inactive';
                                                            $statusClass = 'status-inactive';
                                                        }
                                                    }
                                                    
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($election['election_title']) . "</td>";
                                                    echo "<td>" . date('M d, Y h:i A', strtotime($election['election_start'])) . "</td>";
                                                    echo "<td>" . date('M d, Y h:i A', strtotime($election['election_end'])) . "</td>";
                                                    echo "<td><span class='status-badge {$statusClass}'>{$status}</span></td>";
                                                    echo "<td>";
                                                    if ($status !== 'Ended') {
                                                        echo "<button class='btn btn-sm btn-primary edit-election' data-id='" . $election['id'] . "'><i class='fas fa-edit'></i></button> ";
                                                        echo "<button class='btn btn-sm " . ($election['is_active'] ? 'btn-danger' : 'btn-success') . " toggle-status' data-id='" . $election['id'] . "'>"
                                                            . ($election['is_active'] ? '<i class="fas fa-times"></i> Deactivate' : '<i class="fas fa-check"></i> Activate') . "</button>";
                                                    } else {
                                                        echo "<a href='election-results.php?election_id=" . $election['id'] . "' class='btn btn-sm btn-info'><i class='fas fa-chart-bar'></i> View Results</a>";
                                                    }
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row text-muted">
                            <div class="col-6 text-start">
                                <p class="mb-0">
                                <a target="_blank" class="text-muted"><strong>eVote System</strong></a> &copy;
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                <a class="text-muted" href="#">ISPSC - Tagudin Campus</a>
                                </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="js/app.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

        <script>
        $(document).ready(function() {
            // Check and update election status periodically
            function checkElectionStatus() {
                $.ajax({
                    url: './election/check_election_status.php',
                    type: 'GET',
                    success: function(response) {
                        console.log("Status check response:", response);
                        if (response === "status_updated") {
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Status check error:", error);
                    }
                });
            }

            // Check status every minute
            setInterval(checkElectionStatus, 60000);

            // Handle form submission
            $("#addElectionForm").on('submit', function(e) {
                e.preventDefault();
                
                // Basic validation
                const startDate = new Date($(this).find('[name="election_start"]').val());
                const endDate = new Date($(this).find('[name="election_end"]').val());
                
                if (startDate >= endDate) {
                    alert('End date must be after start date');
                    return;
                }
                
                $.ajax({
                    url: './election/add_election.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log("Add election response:", response);
                        if (response === "success") {
                            alert('Election period created successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Add election error:", error);
                        alert('Error: ' + error);
                    }
                });
            });

            // Handle status toggle
            $(document).on('click', '.toggle-status', function() {
                let id = $(this).data('id');
                let isCurrentlyActive = $(this).closest('tr').find('.status-badge').hasClass('status-active');
                let newStatus = !isCurrentlyActive;
                
                let confirmMessage = isCurrentlyActive ? 
                    'Are you sure you want to deactivate this election period?' :
                    'Are you sure you want to activate this election period? This will deactivate any other active election.';
                
                if (confirm(confirmMessage)) {
                    $.ajax({
                        url: './election/toggle_status.php',
                        type: 'POST',
                        data: { 
                            id: id,
                            status: newStatus
                        },
                        success: function(response) {
                            console.log("Toggle status response:", response);
                            if (response === "success") {
                                location.reload();
                            } else {
                                alert('Error: ' + response);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Toggle status error:", error);
                            alert('Error: ' + error);
                        }
                    });
                }
            });

            // Handle edit (you'll need to implement this)
            $(document).on('click', '.edit-election', function() {
                let id = $(this).data('id');
                
                // Fetch election details
                $.ajax({
                    url: './election/get_election.php',
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        try {
                            const election = JSON.parse(response);
                            
                            // Format datetime for input
                            const startDate = new Date(election.election_start);
                            const endDate = new Date(election.election_end);
                            
                            // Fill the form
                            $('#edit_election_id').val(election.id);
                            $('#edit_election_title').val(election.election_title);
                            $('#edit_election_start').val(startDate.toISOString().slice(0, 16));
                            $('#edit_election_end').val(endDate.toISOString().slice(0, 16));
                            
                            // Show modal
                            $('#editElectionModal').modal('show');
                        } catch (e) {
                            console.error("Parse error:", e);
                            alert('Error loading election details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Fetch error:", error);
                        alert('Error loading election details');
                    }
                });
            });

            // Add save button handler
            $('#saveEditButton').click(function() {
                const form = $('#editElectionForm');
                
                // Basic validation
                const startDate = new Date(form.find('#edit_election_start').val());
                const endDate = new Date(form.find('#edit_election_end').val());
                
                if (startDate >= endDate) {
                    alert('End date must be after start date');
                    return;
                }
                
                $.ajax({
                    url: './election/update_election.php',
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response === "success") {
                            alert('Election period updated successfully');
                            $('#editElectionModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Update error:", error);
                        alert('Error updating election period');
                    }
                });
            });
        });
        </script>

        <!-- Edit Election Modal -->
        <div class="modal fade" id="editElectionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Election Period</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editElectionForm">
                            <input type="hidden" name="election_id" id="edit_election_id">
                            <div class="mb-3">
                                <label class="form-label">Election Title</label>
                                <input type="text" class="form-control" name="election_title" id="edit_election_title" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" name="election_start" id="edit_election_start" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" name="election_end" id="edit_election_end" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveEditButton">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

    </body>

    </html>