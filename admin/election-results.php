<?php
include_once __DIR__ . "/../config/init.php";

$selectedElectionId = isset($_GET['election_id']) ? $_GET['election_id'] : null;

$stmt = $db->query("SELECT id, election_title, election_start, election_end, is_active FROM election_settings ORDER BY election_start DESC");
$elections = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($selectedElectionId) {
    $activeElection = array_filter($elections, function($e) use ($selectedElectionId) { 
        return $e['id'] == $selectedElectionId; 
    });
} else {
    $activeElection = array_filter($elections, function($e) { 
        return $e['is_active'] == 1; 
    });
}
$activeElection = reset($activeElection);

if (!$activeElection && !empty($elections)) {
    $activeElection = $elections[0];
}

// First, check if the election has ended or is active
$currentDateTime = date('Y-m-d H:i:s');
$canShowResults = false;

if ($activeElection) {
    $electionEnd = new DateTime($activeElection['election_end']);
    $currentTime = new DateTime();
    $canShowResults = ($currentTime >= $electionEnd) || ($activeElection['is_active'] == 1);
}

// Modify the SQL query section
if ($canShowResults) {
    $sql = "SELECT 
        p.position_name,
        p.position_id,
        c.candidate_name,
        c.candidate_id,
        pl.partylist_name,
        COUNT(v.vote_id) as vote_count,
        CASE 
            WHEN c.candidate_id IS NOT NULL THEN
                DENSE_RANK() OVER (PARTITION BY p.position_id ORDER BY COUNT(v.vote_id) DESC)
            ELSE NULL 
        END as candidate_rank
    FROM positions p
    LEFT JOIN candidates c ON p.position_id = c.candidate_position
        AND c.election_id = :election_id  -- Add this condition to filter candidates
    LEFT JOIN partylists pl ON c.partylist_id = pl.partylist_id
    LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
        AND v.election_id = :election_id
    GROUP BY 
        p.position_id, 
        p.position_name, 
        c.candidate_id,
        c.candidate_name,
        pl.partylist_name
    ORDER BY p.position_id, candidate_rank";

    $activeElectionId = $activeElection ? $activeElection['id'] : null;
    $stmt = $db->prepare($sql);
    $stmt->execute(['election_id' => $activeElectionId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $results = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Election Results - E-Vote System</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="css/light.css" rel="stylesheet">
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .main {
                margin: 0 !important;
                padding: 0 !important;
            }
            .content {
                padding: 0 !important;
            }
            /* Hide sidebar and navbar when printing */
            .sidebar, 
            .navbar {
                display: none !important;
            }
            /* Remove margin from main content when printing */
            .wrapper .main {
                margin-left: 0 !important;
                width: 100% !important;
            }
            /* Ensure the card takes full width */
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-body {
                padding: 0 !important;
            }
        }

        .print-only {
            display: none;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .result-table th,
        .result-table td {
            border: 1px solid #dee2e6;
            padding: 0.75rem;
            text-align: left;
        }

        .result-table th {
            background-color: #f8f9fa;
        }

        .signature-section {
            margin-top: 3rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 2rem;
            margin-bottom: 0.5rem;
        }

        .editable {
            border: none;
            background: transparent;
            width: 100%;
            text-align: inherit;
        }

        .editable:focus {
            outline: 1px solid #007bff;
            background: #fff;
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
                    <div class="mb-3 no-print">
                        <h1 class="h3 d-inline align-middle">Election Results</h1>
                        <button onclick="window.print()" class="btn btn-primary float-end me-2">
                            <i class="fas fa-print"></i> Print Results
                        </button>
                        <!--<button onclick="saveChanges()" class="btn btn-success float-end me-2">
                            <i class="fas fa-save"></i> Save Signatures
                        </button>-->
                        
                        <!-- Add election period selector -->
                        <select id="electionPeriodSelect" class="form-select float-end me-2" style="width: auto;">
                            <optgroup label="Ended Elections">
                                <?php
                                foreach ($elections as $election) {
                                    $currentTime = new DateTime();
                                    $endTime = new DateTime($election['election_end']);
                                    $isEnded = $currentTime > $endTime;
                                    
                                    if ($isEnded) {
                                        $selected = $election['id'] == $selectedElectionId ? 'selected' : '';
                                        echo "<option value='{$election['id']}' {$selected}>{$election['election_title']} (" . 
                                             date('M d, Y', strtotime($election['election_start'])) . " - " . 
                                             date('M d, Y', strtotime($election['election_end'])) . ")</option>";
                                    }
                                }
                                ?>
                            </optgroup>
                            <optgroup label="Active/Upcoming Elections">
                                <?php
                                foreach ($elections as $election) {
                                    $currentTime = new DateTime();
                                    $endTime = new DateTime($election['election_end']);
                                    $isEnded = $currentTime > $endTime;
                                    
                                    if (!$isEnded) {
                                        $selected = $election['id'] == $selectedElectionId ? 'selected' : '';
                                        echo "<option value='{$election['id']}' {$selected}>{$election['election_title']} (" . 
                                             date('M d, Y', strtotime($election['election_start'])) . " - " . 
                                             date('M d, Y', strtotime($election['election_end'])) . ")</option>";
                                    }
                                }
                                ?>
                            </optgroup>
                        </select>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="../assets/images/logo/ispsc_logo_small.png" alt="ISPSC Logo" height="60" class="mb-2S">
                                <h4>ILOCOS SUR POLYTECHNIC STATE COLLEGE</h4>
                                <h5>TAGUDIN CAMPUS, TAGUDIN, ILOCOS SUR</h5>
                                <h2 class="mt-4">ELECTION RESULT</h2>
                                <h5 class="mt-3" id="electionPeriodTitle">
                                    <?php 
                                    echo $activeElection ? $activeElection['election_title'] : 'SSC ' . date('Y'); 
                                    ?>
                                </h5>
                            </div>

                            <table class="result-table" id="resultTable">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Candidate</th>
                                        <th>Votes</th>
                                        <th>Rank</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!$canShowResults) {
                                        echo '<tr><td colspan="4" class="text-center">Results will be available after the election ends.</td></tr>';
                                    } else {
                                        foreach ($results as $row):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['position_name'] ?? '') ?></td>
                                            <td>
                                                <?php if (!empty($row['candidate_name'])): ?>
                                                    <?= htmlspecialchars($row['candidate_name']) ?>
                                                    <?php if (!empty($row['partylist_name'])): ?>
                                                        <div class="small text-muted"><?= htmlspecialchars($row['partylist_name']) ?></div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No candidate</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $row['vote_count'] ?? 0 ?></td>
                                            <td><?= $row['candidate_rank'] ?? '' ?></td>
                                        </tr>
                                        <?php 
                                        endforeach;
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="signature-section mt-5">
                                <div class="signature-box">
                                    <p>Approved by:</p>
                                    <div class="signature-line"></div>
                                    <input type="text" class="editable text-center" value="Dennis T. Millare" style="font-weight: bold" id="ssc_advisor_name">
                                    <input type="text" class="editable text-center" value="SSC Adviser" id="ssc_advisor_position">
                                </div>
                                <div class="signature-box">
                                    <p>Approved by:</p>
                                    <div class="signature-line"></div>
                                    <input type="text" class="editable text-center" value="MARY ROSE S. ABANIZ" style="font-weight: bold" id="osa_name">
                                    <input type="text" class="editable text-center" value="OSA Coordinator" id="osa_position">
                                </div>
                                <div class="signature-box">
                                    <p>Prepared by:</p>
                                    <div class="signature-line"></div>
                                    <input type="text" class="editable text-center" value="Dr. GEORGE R. VILLANEUVA, JR." style="font-weight: bold" id="twg_name">
                                    <input type="text" class="editable text-center" value="Technical Working Group" id="twg_position">
                                </div>
                                <div class="signature-box">
                                    <p>Extracted by:</p>
                                    <div class="signature-line"></div>
                                    <input type="text" class="editable text-center" value="JIM_MAR F. DE LOS REYES" style="font-weight: bold" id="extractor_name">
                                    <input type="text" class="editable text-center" value="Technical Working Group" id="extractor_position">
                                </div>
                            </div>

                            <div class="text-center mt-5">
                                <div class="signature-box" style="max-width: 300px; margin: 0 auto;">
                                    <p>Noted by:</p>
                                    <div class="signature-line"></div>
                                    <input type="text" class="editable text-center" value="EDERLINA M. SUMAIL" style="font-weight: bold" id="admin_name">
                                    <input type="text" class="editable text-center" value="CAMPUS ADMINISTRATOR" id="admin_position">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>

    <script src="js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    
    <script>
    function saveChanges() {
        // Remove readonly from signature fields
        document.querySelectorAll('.signature-box input').forEach(input => {
            input.removeAttribute('readonly');
        });

        // Collect all the signature data
        const signatures = {
            ssc_advisor: {
                name: document.getElementById('ssc_advisor_name').value,
                position: document.getElementById('ssc_advisor_position').value
            },
            osa_coordinator: {
                name: document.getElementById('osa_name').value,
                position: document.getElementById('osa_position').value
            },
            technical_group: {
                name: document.getElementById('twg_name').value,
                position: document.getElementById('twg_position').value
            },
            extractor: {
                name: document.getElementById('extractor_name').value,
                position: document.getElementById('extractor_position').value
            },
            administrator: {
                name: document.getElementById('admin_name').value,
                position: document.getElementById('admin_position').value
            }
        };

        // Validate that no fields are empty
        for (const role in signatures) {
            if (!signatures[role].name || !signatures[role].position) {
                alert('Please fill in all signature fields');
                return;
            }
        }

        // Send the signature data to the server
        fetch('process/update-election-results.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Add CSRF token if you have one
            },
            body: JSON.stringify({ signatures: signatures })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Signatures saved successfully!');
                // Make fields readonly again
                document.querySelectorAll('.signature-box input').forEach(input => {
                    input.setAttribute('readonly', true);
                });
            } else {
                alert('Error saving signatures: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving signatures. Please try again.');
        });
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Make signature fields editable on double click
        document.querySelectorAll('.signature-box input').forEach(input => {
            input.addEventListener('dblclick', function() {
                this.removeAttribute('readonly');
                this.focus();
            });

            input.addEventListener('blur', function() {
                this.setAttribute('readonly', true);
            });
        });
    });

    document.getElementById('electionPeriodSelect').addEventListener('change', function() {
        const electionId = this.value;
        window.location.href = `election-results.php?election_id=${electionId}`;
    });

    function updateElectionPeriodTitle(electionId) {
        const select = document.getElementById('electionPeriodSelect');
        const selectedOption = select.options[select.selectedIndex];
        document.getElementById('electionPeriodTitle').textContent = selectedOption.text;
    }
    </script>
</body>
</html>