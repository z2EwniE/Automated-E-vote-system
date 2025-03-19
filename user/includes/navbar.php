<nav class="navbar navbar-expand navbar-light navbar-bg">
    <div class="container-fluid">
        <a class="" href="#">
            <strong>E-Vote</strong>
        </a>

        <div class="ms-auto">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['student_id'])): ?>
                <li class="nav-item dropdown">
                    <button class="btn btn-link nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        ID: <?php echo $_SESSION['student_id']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Debug info -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownToggle) {
        console.log('Dropdown toggle found');
        dropdownToggle.addEventListener('click', function(e) {
            console.log('Dropdown clicked');
        });
    } else {
        console.log('Dropdown toggle not found');
    }
    
    if (dropdownMenu) {
        console.log('Dropdown menu found');
    } else {
        console.log('Dropdown menu not found');
    }
});
</script>