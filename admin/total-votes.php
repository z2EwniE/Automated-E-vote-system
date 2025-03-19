<?php
    include_once __DIR__ . '/../config/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="qoshima">
    <meta name="keywords" content="Admin, dashboard, responsive, css, sass, html, theme, front-end">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <title>E-Vote System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Choose your preferred color scheme -->
    <link href="css/light.css" rel="stylesheet">

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

        #chartsContainer {
            width: 100%;
        }

    
    </style>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>

        <div class="main">
            <?php include_once 'includes/navbar.php'; ?>

            <!-- start here -->
            <main class="content">
                <div class="container-fluid p-0">

                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-success text-black">
                        <div class="d-flex float-end w-25 m-2">
                        <select type="text" id="course" name="course" class="form-control" required>
                    <option value="">Select Course</option> 
                   
                    </select>
                            </div>
   
                            <div class="d-flex float-end w-25 m-2">
                                <select name="year_level" id="year_level" class="form-control">
                                    <option value="">Select a year level</option>
                                </select>
                            </div>
                                <div class="d-flex float-end w-25 m-2">
                                <select id="department" name="department" class="form-select" required>
                        <option value="">Select Department</option>
                        
                    </select>
                            </div>
                        </div>

                        <div class="card-body">                
                                <div id="chartsContainer" style="display: flex; flex-wrap: wrap; gap: 20px;"></div>
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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
      $(document).ready(function() {
    // Fetch departments on page load
    fetchDepartments();

    // Fetch courses when a department is selected
    $('#department').on('change', function() {
        const departmentID = $(this).val();
        if (departmentID) {
            fetchCourses(departmentID);
        } else {
            $('#course').html('<option value="">Select Course</option>');
        }
        renderTotalVotesChart(); // Update the chart when a new department is selected
    });

    // Fetch year levels on page load
    fetchYearLevels();

    // Fetch and render votes when year level or course is changed
    $('#year_level, #course').on('change', function() {
        renderTotalVotesChart();
    });

    // Initial render of the chart on page load
    renderTotalVotesChart();

    // Function to fetch departments
    function fetchDepartments() {
        $.ajax({
            url: 'teen_titans/fetch_department.php',
            method: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#department').append(response);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching departments:', xhr.responseText);
            }
        });
    }

    // Function to fetch courses based on department ID
    function fetchCourses(departmentID) {
        $.ajax({
            type: 'POST',
            url: 'teen_titans/fetch_courses.php',
            data: { department_id: departmentID },
            dataType: 'html',
            success: function(response) {
                $('#course').html('<option value="">Select Course</option>').append(response);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching courses:', xhr.responseText);
            }
        });
    }

    // Function to fetch year levels
    function fetchYearLevels() {
        fetch('teen_titans/fetch_year_levels.php')
            .then(response => response.json())
            .then(data => {
                const yearLevels = data.year_levels;
                const selectElement = document.getElementById('year_level');
                selectElement.innerHTML = '<option value="">Select a year level</option>';

                yearLevels.forEach(level => {
                    const option = document.createElement('option');
                    option.value = level;
                    option.textContent = level;
                    selectElement.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching year levels:', error));
    }

    // Function to fetch votes data
    async function fetchVotesData() {
        try {
            const department = $('#department').val();
            const course = $('#course').val();
            const year_level = $('#year_level').val();

            const response = await fetch(`teen_titans/fetch_votes.php?department=${department}&course=${course}&year_level=${year_level}`);
            const data = await response.json();
            return data.votes;
        } catch (error) {
            console.error('Error fetching votes data:', error);
            return [];
        }
    }

// Function to render the total votes charts using ApexCharts
async function renderTotalVotesChart() {
    try {
        const votesData = await fetchVotesData();

        // Debugging: Log the data fetched
        console.log("Votes Data:", votesData);

        // Remove all previous charts if they exist
        $('#chartsContainer').empty();

        if (votesData && votesData.length > 0) {
            // Prepare data for the chart by grouping by position
            const positionMap = {};
            votesData.forEach(vote => {
                if (!positionMap[vote.position_name]) {
                    positionMap[vote.position_name] = {
                        position: vote.position_name,
                        candidates: [],
                        votes: []
                    };
                }
                positionMap[vote.position_name].candidates.push(vote.candidate_name);
                positionMap[vote.position_name].votes.push(vote.total_votes);
            });

            // Debugging: Log the grouped position data
            console.log("Position Map:", positionMap);

            // Create separate charts for each position
            for (const position in positionMap) {
                const positionData = positionMap[position];

                // Add a new container for each position chart
                const chartId = `votesChartTotal_${position.replace(/\s+/g, '_')}`;
                $('#chartsContainer').append(`<div id="${chartId}" style="width: 100%; max-width: 600px; margin-bottom: 20px;"></div>`);

                // Create the chart using ApexCharts
                var options = {
                    chart: {
                        type: 'bar',
                        height: 400,
                        stacked: false
                    },
                    series: [{
                        name: positionData.position,
                        data: positionData.votes
                    }],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ['#304758']
                        }
                    },
                    xaxis: {
                        categories: positionData.candidates,
                        title: {
                            text: 'Candidates'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Total Votes'
                        }
                    },
                    legend: {
                        position: 'top'
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val;
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
                
                // Debugging: Check if chart instance is created successfully
                console.log(`Rendering chart for position: ${position}`);
                await chart.render(); // Render each chart
            }
        } else {
            console.log('No votes data available for the selected filters.');
        }
    } catch (error) {
        console.error('An error occurred while rendering the total votes chart:', error);
    }
}

});

    </script>

</body>

</html>
