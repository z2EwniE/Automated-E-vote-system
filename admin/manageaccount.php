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
</style>
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
                <h1 class="h3 d-inline align-middle">Partylist</h1><a class="badge bg-primary ms-2"
                    target="_blank">SSC<i class="fas fa-fw fa-external-link-alt"></i></a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="card-actions float-end">
                                <div class="dropdown position-relative">
                                    <a href="#" data-bs-toggle="dropdown" data-bs-display="static">
                                        <i class="align-middle" ></i>
                                    </a>

                               
                                </div>
                            </div>
                            <h5 class="card-title mb-0">Registered Partylist</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">

                                    <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addPartylistModal">Add Partylist</button>
                                </div>
                            </div>

                            <div class="container mt-5">

                                <table class="table table-responsive table-striped" id="studentTable"
                                    style="width:100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th onclick="sortTable(0)" class="sortable">No.
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th>Partylist </th>
                                            <th>Department </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                    try {
                        // Assuming $db is a valid PDO instance from init.php
                        $stmt = $db->prepare("SELECT partylist_id AS No, 
                                                            partylist_name AS Partylist, 
                                                            department.department_name FROM partylists INNER JOIN department ON department.department_id = partylists.department");

                        $stmt->execute();
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($results) > 0) {
                            foreach ($results as $row) {
                                echo "<tr>";
                                echo "<td>" . $row['No'] . "</td>";
                                echo "<td>" . $row['Partylist'] . "</td>";
                                echo "<td>" . $row['department_name'] . "</td>";
                                echo "<td>
                                        <div class='btn-group'>
                                            <button class='btn btn-info view_candidates' data-id='". $row['No'] ."' data-bs-toggle='modal' data-bs-target='#viewCandidatesModal'><i class='fa fa-users'></i></button>
                                            <button class='btn btn-primary delete_partylist' data-id='". $row['No'] ."' data-bs-toggle='modal' data-bs-target='#editPartylistModal'><i class='fa fa-edit '></i></button>
                                            <button class='btn btn-danger' onclick='deletePartylist(" . $row['No'] . ")'><i class='fa fa-trash'></i></button>
                                        </div>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No results found</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "Connection or query failed: " . $e->getMessage();
                    }
                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Add Partylist Modal -->
                <div class="modal fade" id="addPartylistModal" tabindex="-1"
                    aria-labelledby="addPartylistModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addPartylistModalLabel">Add Partylist</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addPartylistForm">
                                    <div class="mb-3">
                                        <label for="partylistName" class="form-label">Partylist Name</label>
                                        <input type="text" class="form-control" id="partylistName"
                                            maxlength="100" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department:</label>
                                        <select id="department" name="department" class="form-select"
                                            required>
                                            <option value="">Select Department</option>

                                        </select>
                                    </div>

                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary"
                                    onclick="addPartylist()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Partylist Modal -->
                <div class="modal fade" id="editPartylistModal" tabindex="-1"
                    aria-labelledby="editPartylistModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editPartylistModalLabel">Edit Partylist</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editPartylistForm">
                                    <div class="mb-3">
                                        <label for="editPartylistName" class="form-label">Partylist
                                            Name</label>
                                        <input type="text" class="form-control" id="editPartylistName"
                                            maxlength="100" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editDepartment" class="form-label">Department:</label>
                                        <select id="editDepartment" name="editDepartment"
                                            class="form-select" required>
                                            <option value="">Select Department</option>
                                            <!-- Add department options here -->
                                        </select>
                                    </div>
                                    <input type="hidden" id="editPartylistId">
                                    <button id="editPartylistBtn" type="submit" class="btn btn-primary">Update</button>
                                </form>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                
                            </div>
                        </div>
                    </div>
                </div>





                <!--end of pages -->

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

$(document).on('click', '.delete_partylist', function() {
let id = $(this).data('id');

$.ajax({
    type: 'POST',
    url: 'pList/fetch_pList.php',
    data: {
        partylist_id: id
    },
    success: function(data) {
        var res = JSON.parse(data)
        $("#editPartylistId").val(res.partylist_id)
        $("#editPartylistName").val(res.partylist_name)
        $("#editDepartment").val(res.department)

    }
})
});

$(document).ready(function () {  
$("#editPartylistBtn").on('click', function(e) {
e.preventDefault();

$.ajax({
url: 'pList/e_pList.php',
type: 'POST',
dataType: 'text', // Ensure response is treated as plain text
data: {
    partylist_id: $("#editPartylistId").val(),
    partylist_name: $("#editPartylistName").val(),
    department: $("#editDepartment").val()
},
success: function(data) {
    if (data === "success") {
        alert('Okay na'); // Success message
    } else {
        alert('Error: ' + data); // Shows any error message returned by the server
    }
},
error: function(xhr, status, error) {
    alert('An error occurred: ' + error); // Alerts if AJAX call fails
}
});
});
});





document.getElementById("addPartylistForm").addEventListener("submit", function(event) {
event.preventDefault();
});

function addPartylist() {
const partylistName = document.getElementById('partylistName').value.trim();
const department = document.getElementById('department').value.trim();
if (partylistName === "" || department === "") {
    alert("Please enter all required fields.");
    return;
}

// Send AJAX request to add the partylist to the database
const xhr = new XMLHttpRequest();
xhr.open("POST", "pList/add_pList.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
        if (xhr.responseText.trim() === "success") {
            location.reload();
        } else {
            alert("Failed to add partylist: " + xhr.responseText);
        }
    }
};
xhr.send("partylist_name=" + encodeURIComponent(partylistName) + "&department=" + encodeURIComponent(
    department));
}



function deletePartylist(partylistId) {
if (!confirm("Are you sure you want to delete this partylist?")) {
    return;
}

// Send AJAX request to delete the partylist from the database
const xhr = new XMLHttpRequest();
xhr.open("POST", "pList/dminus_pList.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
        if (xhr.responseText.trim() === "success") {
            location.reload();
        } else {
            alert("Failed to delete partylist: " + xhr.responseText);
        }
    }
};
xhr.send("partylist_id=" + encodeURIComponent(partylistId));
}


$(document).ready(function() {
// Fetch departments on page load
$.ajax({
    url: '../user/fetch_department.php',
    method: 'GET',
    success: function(response) {
        $('#department').append(response);
        $('#editDepartment').append(response);
    }
});
});

$(document).on('click', '.view_candidates', function() {
let partylistId = $(this).data('id');

$.ajax({
    type: 'POST',
    url: 'pList/fetch_candidates.php',
    data: {
        partylist_id: partylistId
    },
    success: function(response) {
        let data = JSON.parse(response);
        $('#partylistTitle').text('Candidates for ' + data.partylist_name);
        
        let candidatesHtml = '';
        if (data.candidates.length > 0) {
            data.candidates.forEach(function(candidate) {
                candidatesHtml += `
                    <tr>
                        <td>${candidate.candidate_name}</td>
                        <td>${candidate.position_name}</td>
                        <td>${candidate.department_name}</td>
                    </tr>
                `;
            });
        } else {
            candidatesHtml = '<tr><td colspan="3" class="text-center">No candidates found for this partylist</td></tr>';
        }
        
        $('#candidatesList').html(candidatesHtml);
    },
    error: function(xhr, status, error) {
        alert('An error occurred: ' + error);
    }
});
});
</script>

<!-- View Candidates Modal -->
<div class="modal fade" id="viewCandidatesModal" tabindex="-1" aria-labelledby="viewCandidatesModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="viewCandidatesModalLabel">Partylist Candidates</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <h6 id="partylistTitle" class="mb-3"></h6>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Candidate Name</th>
                        <th>Position</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody id="candidatesList">
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>

</body>

</html>