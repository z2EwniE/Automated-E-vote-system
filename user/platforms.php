<?php
session_start();

include_once __DIR__ . '/db.php';


// Redirect to login page if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php'); 
    exit();
}

$sql = "SELECT 
            p.platform_id, 
            p.platform_title, 
            p.platform_content, 
            p.platform_image, 
            p.date_posted, 
            CONCAT(s.first_name, ' ', s.middle_name, ' ', s.last_name) AS candidate_name,
            d.department_name
        FROM platforms p
        INNER JOIN candidates c ON c.candidate_id = p.candidate_id
        INNER JOIN students s ON s.id = c.student_id
        INNER JOIN department d ON d.department_id = s.department";

$stmt = $conn->prepare($sql);
$stmt->execute();

$platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <script src="https://cdn.tiny.cloud/1/s1zhsk9a77bb6jujpqu2nhrpnr7b1t7hxzjuawrdlj68aps4/tinymce/7/tinymce.min.js"
    referrerpolicy="origin"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.2/dist/sweetalert2.min.css" rel="stylesheet">

    <script>
    </script>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <div class="main">
            <?php include_once 'includes/navbar.php'; ?>

            <main class="content">
                <div class="container">
                    <h2 class="mb-4">Candidate Platforms</h2>

                    <?php if(isCandidate()): ?>

                    <div class="card mb-5">
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="platform_title" name="platform_title" placeholder="Platform Title"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea id="platform_content" name="platform_content" class="form-control" id="content" rows="2"
                                        placeholder="Describe your platform..." required></textarea>
                                </div>
                                <button type="submit" id="postPlatformButton" class="btn btn-primary">Post Platform</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row post-list">
                    <div class="col-md-12">
                    <section class="platforms-section mt-4">
                        <div class="container">
                            <h4 class="section-title">Candidate Platforms</h4>
                            <div class="row">
                                <?php if ($platforms): ?>
                                <?php foreach ($platforms as $platform): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card platform-card" style="border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                                        <img src="<?php echo htmlspecialchars($platform['platform_image'] ?: 'https://via.placeholder.com/300'); ?>" class="card-img-top" alt="Platform Image">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($platform['platform_title']); ?></h5>
                                            <p class="card-text"><?php echo (substr($platform['platform_content'], 0, 100)) . '...'; ?></p>
                                            <p class="text-muted"><small>By <?php echo htmlspecialchars($platform['candidate_name']); ?>, <?php echo htmlspecialchars($platform['department_name']); ?></small></p>
                                            <p class="text-muted"><small>Posted on: <?php echo date('F j, Y', strtotime($platform['date_posted'])); ?></small></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <p>No platforms available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
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
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.2/dist/sweetalert2.min.js"></script>
    <script>
    tinymce.init({
        selector: 'textarea',
        plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media',
            'searchreplace', 'table', 'visualblocks', 'wordcount',
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Dec 5, 2024:
            'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker',
            'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage',
            'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
            'autocorrect', 'typography', 'inlinecss', 'markdown',
            // Early access to document converters
            'importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [{
                value: 'First.Name',
                title: 'First Name'
            },
            {
                value: 'Email',
                title: 'Email'
            },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
            'See docs to implement AI Assistant')),
        exportpdf_converter_options: {
            'format': 'Letter',
            'margin_top': '1in',
            'margin_right': '1in',
            'margin_bottom': '1in',
            'margin_left': '1in'
        },
        exportword_converter_options: {
            'document': {
                'size': 'Letter'
            }
        },
        importword_converter_options: {
            'formatting': {
                'styles': 'inline',
                'resets': 'inline',
                'defaults': 'inline',
            }
        },
    });
   
        $(document).ready(function() {
            $("#postPlatformButton").on('click', function(e){
                e.preventDefault();
                    let platform_title = $("#platform_title").val()
                    let platform_content = tinymce.get('platform_content').getContent();
                $.ajax({
                    type: "POST",
                    url: "actions/add_platform.php",
                    data: {
                action: 'add_platform',
                platform_title: platform_title,
                platform_content: platform_content
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'index.php'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                        confirmButtonText: 'Try Again'
                    });
                }
            },
            error: function() {
                $('#responseMessage').html('<p style="color: red;">An error occurred while processing the request.</p>');
            }
        });
    });
        });
    </script>

</body>

</html>