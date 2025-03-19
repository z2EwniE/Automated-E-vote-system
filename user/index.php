<?php
session_start();
include 'db.php';
include 'check_election_status.php';

// Debug session
error_log("Session data: " . print_r($_SESSION, true));

// Add after your includes
$electionStatus = isElectionPeriod();
error_log("Election Status: " . print_r($electionStatus, true));

// Check both session variables
if (!isset($_SESSION['id']) && !isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// For debugging - show session info at top of page
if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="e-vote, voting system, secure voting, elections">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/professional-icon.png" />
    <title>E-Vote System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link href="css/light.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <!-- Add Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        $(document).ready(function() {
            // Debug Bootstrap availability
            console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Loaded' : 'Not loaded');
            
            // Manual dropdown initialization
            const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
            console.log('Found dropdown elements:', dropdownElementList.length);
            
            dropdownElementList.forEach(function(dropdownToggle) {
                const dropdown = new bootstrap.Dropdown(dropdownToggle);
                
                // Add click handler
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.toggle();
                });
            });
            
            // Add manual toggle for dropdown
            $('.dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                const dropdownMenu = $(this).next('.dropdown-menu');
                dropdownMenu.toggleClass('show');
            });
        });
    </script>
    <style>
    /* General Styles */
    body {
        font-family: 'Arial', sans-serif;
    }

    .container {
        max-width: 1200px;
    }

    /* Hero Section */
    .hero-section {
        position: relative;
        height: 400px;
        background-image: url('img/photos/ok.png');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 50px 20px;
        text-align: center;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        /* Dark overlay */
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .hero-description {
        font-size: 18px;
        font-weight: 400;
    }

    /* Voting Container */
    .voting-container {
        margin: 30px auto;
        padding: 15px;
    }

    /* Voting Cards */
    .vote-card {
        position: relative;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .vote-card.selected {
        border-color: #007bff;
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);
    }

    .vote-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .vote-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px 10px 0 0;
    }

    .card-body {
        padding: 15px;
    }

    .card-title {
        font-size: 18px;
        font-weight: bold;
    }

    .card-department,
    .card-partylist {
        font-size: 14px;
        color: #777;
    }

    /* Selected Badge */
    .badge-selected {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 14px;
        display: none;
    }

    .vote-card.selected .badge-selected {
        display: block;
    }

    /* Buttons */
    .submit-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 12px 30px;
        font-size: 18px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submit-btn:hover {
        background-color: #0056b3;
    }

    /* Confirmation Modal */
    .modal-content {
        border-radius: 10px;
        padding: 20px;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-body {
        padding: 20px;
        max-height: 400px;
        overflow-y: auto;
    }

    .modal-footer {
        border-top: none;
    }

    .modal-footer .btn {
        padding: 10px 25px;
    }

    .row.mb-3 {
        margin-bottom: 15px;
    }

    .img-thumbnail {
        border-radius: 10px;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .hero-section {
            height: 300px;
        }

        .hero-title {
            font-size: 28px;
        }

        .vote-card {
            margin-bottom: 15px;
        }

        .vote-card img {
            height: 150px;
        }

        .submit-btn {
            width: 100%;
        }

        .modal-body {
            padding: 10px;
        }
    }

    .vote-card.disabled {
        opacity: 0.7;
        position: relative;
    }

    .vote-card .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .vote-card .overlay span {
        color: white;
        font-weight: bold;
        background: rgba(0, 0, 0, 0.7);
        padding: 5px 15px;
        border-radius: 20px;
    }

    .alert-info {
        background-color: #e8f4f8;
        border-color: #bee5eb;
        color: #0c5460;
    }

    .alert-info i {
        display: block;
        margin-bottom: 10px;
        color: #17a2b8;
    }
    </style>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <div class="main">
         <?php include_once 'includes/navbar.php'; ?>
            <main class="content">
    <section class="hero-section text-center mb-4" style="background-image: url('img/photos/ok.png'); background-size: cover; background-repeat: no-repeat; background-position: center;">
        <div class="hero-content" style="background: rgba(0, 0, 0, 0.5); padding: 50px;">
            <div class="hero-text">
                <h1 class="hero-title" style="font-family: 'Roboto', sans-serif; color: #fff;">Welcome to the E-Vote System</h1>
                <p class="hero-description" style="font-family: 'Roboto', sans-serif; color: #fff;">Your vote matters! Cast your vote securely and easily in just a few clicks.</p>
            </div>
            <div class="hero-model">
                <img src="img/icons/click-ezgif.com-gif-maker.gif" alt="3D Model Voting" class="model-animation">
            </div>
        </div>
    </section>

    <section class="vote-section mt-4">
        <div class="container-fluid p-4 mt-4" style="background: #f8f9fa; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
            <?php 
            if(isset($_GET['success'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Congratulations, Application for Candidacy Success!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if($electionStatus['status']): ?>
                <div class="container">
                    <?php if(hasVoted()): ?>
                        <?php 
                        $votingDetails = getVotingDetails();
                        if ($votingDetails): 
                            $voted_at = date('F j, Y g:i A', strtotime($votingDetails[0]['voted_at']));
                        ?>
                            <div class="container-fluid p-4">
                                <div class="voting-confirmation text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 2rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);">
                                    <div class="confirmation-header" style="background: #007bff; color: white; padding: 1rem; border-radius: 10px; margin-bottom: 2rem;">
                                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                                        <h4>Thank You for Voting!</h4>
                                        <p class="text-white mb-0">Your vote was cast on <?php echo $voted_at; ?></p>
                                    </div>

                                    <div class="row justify-content-center">
                                        <?php foreach ($votingDetails as $vote): ?>
                                            <div class="col-md-6">
                                                <div class="position-card mb-4">
                                                    <div class="position-title bg-primary text-white py-2 rounded-top">
                                                        <h5 class="mb-0"><?php echo htmlspecialchars($vote['position_name']); ?></h5>
                                                    </div>
                                                    <div class="candidate-info p-3" style="background: white; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                                        <img src="<?php echo $vote['candidate_image_path'] ? '../admin/' . $vote['candidate_image_path'] : 'img/avatars/default.jpg'; ?>" 
                                                             class="rounded-circle mb-3" 
                                                             style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #007bff;">
                                                        <h5 class="candidate-name mb-2">
                                                            <?php echo htmlspecialchars($vote['candidate_name']); ?>
                                                        </h5>
                                                        <?php if ($vote['partylist_name']): ?>
                                                            <p class="party-name mb-0 text-muted"><?php echo htmlspecialchars($vote['partylist_name']); ?></p>
                                                        <?php endif; ?>
                                                        <?php if ($vote['department_name']): ?>
                                                            <p class="department-name text-muted mb-1"><?php echo htmlspecialchars($vote['department_name']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <h2 class="text-center mb-4">Vote for Your Candidates</h2>
                        
                        <!-- Loading spinner -->
                        <div id="loading" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading candidates...</p>
                        </div>

                        <!-- Error message -->
                        <div id="error-message" class="alert alert-danger d-none" role="alert">
                        </div>

                        <!-- Positions and candidates will be loaded here -->
                        <div id="positions-container">
                        </div>

                        <!-- Submit button -->
                        <div class="text-center mt-4 mb-4">
                            <button type="button" class="btn btn-primary btn-lg px-5" onclick="submitVote()" id="submit-vote-btn" disabled>
                                <i class="fas fa-vote-yea me-2"></i>Submit Vote
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Confirmation Modal -->
                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Your Vote</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="selectedCandidatesContainer">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Review Choices</button>
                                <button type="button" class="btn btn-primary" onclick="confirmVote()">Confirm Vote</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                // Add this at the top of your script
                const selectedCandidates = {};

                function fetchVotingData() {
                    console.log('Fetching voting data...');
                    
                    // Show loading spinner
                    $('#loading').removeClass('d-none');
                    $('#error-message').addClass('d-none');
                    $('#positions-container').empty();
                    
                    $.ajax({
                        url: 'fetch_voting_data.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log('Raw response:', response);
                            $('#loading').addClass('d-none');
                            
                            if (response.error) {
                                $('#error-message').removeClass('d-none').text(response.error);
                                return;
                            }
                            
                            if (!response.positions || response.positions.length === 0) {
                                $('#error-message').removeClass('d-none').text('No positions or candidates found.');
                                return;
                            }
                            
                            let html = '';
                            response.positions.forEach(position => {
                                html += `
                                    <div class="voting-container" data-position-id="${position.position_id}">
                                        <h3 class="position-title">Vote for ${position.position_name}</h3>
                                        <div class="row g-4">
                                `;
                                
                                position.candidates.forEach(candidate => {
                                    html += `
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100 vote-card" 
                                                 id="candidate-${candidate.candidate_id}" 
                                                 data-position-id="${position.position_id}"
                                                 data-candidate-id="${candidate.candidate_id}">
                                                <div class="card-body text-center">
                                                    <img src="${candidate.candidate_image_path ? '../admin/' + candidate.candidate_image_path : 'img/avatars/default.jpg'}" 
                                                         class="rounded-circle mb-3" 
                                                         style="width: 128px; height: 128px; object-fit: cover;"
                                                         alt="${candidate.candidate_name}">
                                                    <h5 class="card-title">${candidate.candidate_name}</h5>
                                                    <p class="card-text text-muted mb-2">${candidate.partylist_name || ''}</p>
                                                    <p class="card-text small">${candidate.department_name || ''}</p>
                                                    <div class="badge-selected">Selected</div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                                
                                html += `
                                    </div>
                                </div>
                            `;
                            });
                            
                            $('#positions-container').html(html);
                            
                            // Handle candidate selection
                            $(document).on('click', '.vote-card', function() {
                                const candidateId = $(this).data('candidate-id');
                                const positionId = $(this).closest('.voting-container').data('position-id');
                                
                                // If this candidate is already selected, unselect it
                                if (selectedCandidates[positionId] === candidateId) {
                                    delete selectedCandidates[positionId];
                                    $(this).removeClass('selected');
                                } else {
                                    // Remove selection from other candidates in this position
                                    $(`.voting-container[data-position-id="${positionId}"] .vote-card`).removeClass('selected');
                                    // Select this candidate
                                    $(this).addClass('selected');
                                    selectedCandidates[positionId] = candidateId;
                                }
                                
                                // Update submit button state
                                updateSubmitButton();
                            });

                            updateSubmitButton();
                        },
                        error: function(xhr, status, error) {
                            $('#loading').addClass('d-none');
                            $('#error-message').removeClass('d-none')
                                .text('Error loading candidates. Please try refreshing the page.');
                            console.error('Error:', error);
                        }
                    });
                }

                function updateSubmitButton() {
                    const selections = Object.keys(selectedCandidates).length;
                    // Enable submit button if at least one candidate is selected
                    $('#submit-vote-btn').prop('disabled', selections === 0);
                }

                function submitVote() {
                    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    populateConfirmationModal();
                    modal.show();
                }

                function populateConfirmationModal() {
                    const selectedCandidatesContainer = $('#selectedCandidatesContainer');
                    selectedCandidatesContainer.empty();

                    // First get all positions data
                    $.ajax({
                        url: 'fetch_voting_data.php',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.error || !response.positions) {
                                console.error('Error getting positions data:', response.error);
                                return;
                            }

                            response.positions.forEach(position => {
                                if (selectedCandidates[position.position_id]) {
                                    const selectedCandidate = position.candidates.find(
                                        c => c.candidate_id === selectedCandidates[position.position_id]
                                    );

                                    if (selectedCandidate) {
                                        const modalEntry = `
                                            <div class="selected-candidate mb-3">
                                                <h5 class="position-name">Vote for ${position.position_name}</h5>
                                                <div class="candidate-info d-flex align-items-center">
                                                    <img src="${selectedCandidate.candidate_image_path ? '../admin/' + selectedCandidate.candidate_image_path : 'img/avatars/default.jpg'}" 
                                                         class="candidate-image me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover;"
                                                         alt="${selectedCandidate.candidate_name}"
                                                         onerror="this.src='img/avatars/default.jpg'">
                                                    <div>
                                                        <h6 class="candidate-name mb-1">${selectedCandidate.candidate_name}</h6>
                                                        <p class="party-name mb-0 text-muted">${selectedCandidate.partylist_name || 'Independent'}</p>
                                                    </div>
                                                </div>
                                            </div>`;
                                        selectedCandidatesContainer.append(modalEntry);
                                    }
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data for modal:', error);
                            alert('Error loading confirmation details. Please try again.');
                        }
                    });
                }

                function confirmVote() {
                    // Show loading state
                    const confirmBtn = $('.modal-footer .btn-primary');
                    const originalText = confirmBtn.text();
                    confirmBtn.prop('disabled', true).text('Submitting...');
                    
                    console.log('Submitting votes:', selectedCandidates);
                    
                    $.ajax({
                        url: 'submit_vote.php',
                        type: 'POST',
                        data: { votes: selectedCandidates },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Server response:', response);
                            
                            if (response.success) {
                                // Show success message and reload
                                alert('Your vote has been submitted successfully!');
                                window.location.reload();
                            } else {
                                // Show error message
                                alert(response.error || 'Error submitting vote. Please try again.');
                                
                                // Reset button
                                confirmBtn.prop('disabled', false).text(originalText);
                                
                                // Close modal
                                $('#confirmationModal').modal('hide');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error submitting vote:', error);
                            console.error('Server response:', xhr.responseText);
                            
                            // Show error message
                            alert('An error occurred while submitting your vote. Please try again.');
                            
                            // Reset button
                            confirmBtn.prop('disabled', false).text(originalText);
                            
                            // Close modal
                            $('#confirmationModal').modal('hide');
                        }
                    });
                }

                // Call fetchVotingData when document is ready
                $(document).ready(function() {
                    console.log('Document ready, calling fetchVotingData...');
                    fetchVotingData();
                });
                </script>
            <?php else: ?>
                <div class="container-fluid p-4">
                    <div class="election-status text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 2.5rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);">
                        <div class="status-icon mb-4">
                            <i class="fas fa-clock fa-4x" style="color: #007bff; background: rgba(0, 123, 255, 0.1); padding: 25px; border-radius: 50%;"></i>
                        </div>
                        <h3 class="status-title mb-3" style="color: #2c3e50; font-weight: 600;">No Active Election</h3>
                        <div class="status-message p-3" style="background: white; border-radius: 10px; max-width: 600px; margin: 0 auto;">
                            <p class="mb-3" style="color: #6c757d; font-size: 1.1rem;">There is currently no active election.</p>
                            <div class="additional-info mt-3" style="color: #6c757d;">
                                <i class="fas fa-info-circle me-2"></i>
                                Please check back during the scheduled election period.
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Your Vote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div id="selectedCandidatesContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-arrow-left"></i> Go Back</button>
                    <button type="button" class="btn btn-primary" id="confirmVote">Confirm Vote</button>
                </div>
            </div>
        </div>
    </div>
</main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="text-muted">
                        <div class="text-center">
                            <a class="text-muted"><strong>ISPSC-Tagudin Campus</strong></a> &copy;
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/app.js"></script>

    <script>
    // Debug logging
    console.log('Session data:', <?php echo json_encode($_SESSION); ?>);
    
    // Add AJAX error handling
    $.ajaxSetup({
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            console.error('Response:', jqXHR.responseText);
        }
    });
    
    function fetchVotingData() {
        console.log('Fetching voting data...');
        
        // Show loading state
        $('#positions-container').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p>Loading candidates...</p></div>');
        
        $.ajax({
            url: 'fetch_voting_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Raw response:', response);
                
                if (response.error) {
                    console.error('Server returned error:', response.error);
                    $('#positions-container').html('<div class="alert alert-danger">' + response.error + '</div>');
                    return;
                }
                
                if (!response.positions || response.positions.length === 0) {
                    console.log('No positions or candidates found');
                    $('#positions-container').html('<div class="alert alert-info">No positions or candidates available.</div>');
                    return;
                }
                
                $('#positions-container').empty();
                response.positions.forEach(position => {
                    console.log('Processing position:', position);
                    
                    const positionSection = `
                        <div class="position-section mb-5">
                            <h3 class="mb-4">Vote for ${position.position_name}</h3>
                            <div class="row" id="position-${position.position_id}"></div>
                        </div>
                    `;
                    $('#positions-container').append(positionSection);
                    
                    position.candidates.forEach(candidate => {
                        console.log('Processing candidate:', candidate);
                        
                        const candidateCard = `
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 vote-card" 
                                     id="candidate-${candidate.candidate_id}" 
                                     data-position-id="${position.position_id}"
                                     data-candidate-id="${candidate.candidate_id}">
                                    <div class="card-body text-center">
                                        <img src="${candidate.candidate_image_path ? '../admin/' + candidate.candidate_image_path : 'img/avatars/default.jpg'}" 
                                             class="rounded-circle mb-3" 
                                             style="width: 128px; height: 128px; object-fit: cover;"
                                             alt="${candidate.candidate_name}">
                                        <h5 class="card-title">${candidate.candidate_name}</h5>
                                        <p class="card-text text-muted mb-2">${candidate.partylist_name || ''}</p>
                                        <p class="card-text small">${candidate.department_name || ''}</p>
                                        <div class="badge-selected">Selected</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $(`#position-${position.position_id}`).append(candidateCard);
                    });
                });
                
                // Handle candidate selection
                $(document).on('click', '.vote-card', function() {
                    const candidateId = $(this).data('candidate-id');
                    const positionId = $(this).closest('.voting-container').data('position-id');
                    
                    // If this candidate is already selected, unselect it
                    if (selectedCandidates[positionId] === candidateId) {
                        delete selectedCandidates[positionId];
                        $(this).removeClass('selected');
                    } else {
                        // Remove selection from other candidates in this position
                        $(`.voting-container[data-position-id="${positionId}"] .vote-card`).removeClass('selected');
                        // Select this candidate
                        $(this).addClass('selected');
                        selectedCandidates[positionId] = candidateId;
                    }
                    
                    // Update submit button state
                    updateSubmitButton();
                });

                updateSubmitButton();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching voting data:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $('#positions-container').html('<div class="alert alert-danger">Error loading candidates. Please try again.</div>');
            }
        });
    }

    // Show confirmation modal
    $('#submitVote').on('click', function() {
        if (Object.keys(selectedCandidates).length === 0) {
            alert('Please select at least one candidate before submitting your vote.');
            return;
        }
        populateConfirmationModal();
        $('#confirmationModal').modal('show');
    });

    // Populate modal with selected candidates
    function populateConfirmationModal() {
        const selectedCandidatesContainer = $('#selectedCandidatesContainer');
        selectedCandidatesContainer.empty();

        // First get all positions data
        $.ajax({
            url: 'fetch_voting_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error || !response.positions) {
                    console.error('Error getting positions data:', response.error);
                    return;
                }

                response.positions.forEach(position => {
                    if (selectedCandidates[position.position_id]) {
                        const selectedCandidate = position.candidates.find(
                            c => c.candidate_id === selectedCandidates[position.position_id]
                        );

                        if (selectedCandidate) {
                            const modalEntry = `
                                <div class="row mb-3">
                                    <div class="col-auto">
                                        <img src="${selectedCandidate.candidate_image_path ? '../admin/' + selectedCandidate.candidate_image_path : 'img/avatars/default.jpg'}" class="img-thumbnail" width="60" alt="https://via.placeholder.com/90">
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-0">${selectedCandidate.candidate_name}</h5>
                                        <p class="mb-0 text-muted">${selectedCandidate.partylist_name || 'Independent'}</p>
                                        <small class="text-muted">Position: ${position.position_name}</small>
                                    </div>
                                </div>`;

                            selectedCandidatesContainer.append(modalEntry);
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data for modal:', error);
                alert('Error loading confirmation details. Please try again.');
            }
        });
    }

    // Client-side confirmation of vote submission
    $('#confirmVote').on('click', function() {
        const votes = Object.keys(selectedCandidates).map(positionId => {
            const candidateId = selectedCandidates[positionId];
            const candidateCard = $(`#candidate-${candidateId}`);

            // Check if the candidateCard exists
            if (!candidateCard.length) {
                console.error(
                    `Candidate card for position ${positionId} and candidate ID ${candidateId} does not exist.`
                );
                return null; // Skip if the card does not exist
            }

            const partylistId = candidateCard.data('partylist-id'); // Retrieve partylist ID
            const positionId = candidateCard.data('position-id'); // Get the correct position_id

            // Log values to help debug undefined partylist_id
            console.log(
                `Position: ${positionId}, Candidate ID: ${candidateId}, Partylist ID: ${partylistId}, Position ID: ${positionId}`
            );

            // Validate the partylistId and positionId
            if (!partylistId || !positionId) {
                alert("Error: Partylist ID or Position ID is missing.");
                return null; // Stop execution if IDs are invalid
            }

            return {
                position_id: positionId,
                candidate_id: candidateId,
                partylist_id: partylistId
            };
        }).filter(vote => vote !== null); // Filter out any null values

        if (votes.length === 0) {
            alert("Error: No valid votes found.");
            return; // Stop submission if no valid votes
        }

        $.ajax({
            url: 'submit_vote.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                student_id: '<?php echo $_SESSION['student_id']; ?>',
                votes: votes
            }),
            success: function(response) {
                const result = JSON.parse(response);
                alert(result.message);
                if (result.status === 'success') {
                    window.location.href = 'thanks.php';
                    //window.location.reload(); // Reload page after successful vote
                }
            },
            error: function(err) {
                console.error('Error submitting vote:', err);
                console.error('Server response:', err.responseText);
                
                // Show error message
                alert('An error occurred while submitting your vote. Please try again.');
                
                // Close modal
                $('#confirmationModal').modal('hide');
            }
        });
    });
    </script>

    <!-- Add this JavaScript to disable voting if already voted -->
    <script>
    $(document).ready(function() {
        if (<?php echo hasVoted() ? 'true' : 'false'; ?>) {
            // Disable all vote cards if user has already voted
            $('.vote-card').addClass('disabled').css('pointer-events', 'none');
            $('.vote-card').append('<div class="overlay"><span>Already Voted</span></div>');
        }
    });
    </script>

</body>

</html>