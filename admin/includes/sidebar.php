<?php
// Get current user data
$user = new User();
$userData = $user->getUserData($_SESSION['uid'] ?? 0);
$username = htmlspecialchars($userData['username'] ?? 'Guest');
$role = htmlspecialchars($userData['role'] ?? 'User');
?>
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class='sidebar-brand'>
					<span class="sidebar-brand-text align-middle">
						E-vote System
						<sup><small class="badge bg-success text-uppercase">ISPSC</small></sup>
					</span>
            <svg class="sidebar-brand-icon align-middle" width="32px" height="32px" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="1.5"
                 stroke-linecap="square" stroke-linejoin="miter" color="#FFFFFF" style="margin-left: -3px">
                <path d="M12 4L20 8.00004L12 12L4 8.00004L12 4Z"></path>
                <path d="M20 12L12 16L4 12"></path>
                <path d="M20 16L12 20L4 16"></path>
            </svg>
        </a>

        <div class="sidebar-user">
            <div class="d-flex justify-content-center">
                <!--<div class="flex-shrink-0">
                    <img src="img/avatars/default.jpg" class="avatar img-fluid rounded me-1" alt="<?= $username ?>" />
                </div>-->
                <div class="flex-grow-1 ps-2">
                    <a class="sidebar-user-title" href="#" >
                        <?= $username ?>
                    </a>

                    <div class="sidebar-user-subtitle"><?= $role ?></div>
                </div>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Home
            </li>
            <li class="sidebar-item">
                <a data-bs-target="#dashboards" data-bs-toggle="collapse" class="sidebar-link">
                    <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboards</span>
                </a>
                <ul id="dashboards" class="sidebar-dropdown list-unstyled collapse show" data-bs-parent="#sidebar">
                    <li class="sidebar-item" ><a class='sidebar-link' href='index.php'>Analytics</a></li>
                  

                </ul>
            </li>

            <ul class="sidebar-nav">
            <li class="sidebar-header">
                Election
            </li>   
            
            <li class="sidebar-item">
                <a class='sidebar-link' href='candidates.php'>
                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Candidates</span>
                </a>
            </li>

            <li class="sidebar-item">
            <a class='sidebar-link' href='partylist.php'>
                    <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Partylist</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class='sidebar-link' href='electionperiod.php'>
                    <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Election Period</span>
                </a>
            </li>
            </ul>

            <ul class="sidebar-nav">
        <li class="sidebar-header">
               Election Results
            </li>

            <!--<li class="sidebar-item">
                <a href="total-votes.php" class="sidebar-link">
                    <i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Total Votes</span>
                </a>
            </li>-->

            <li class="sidebar-item">
                <a class="sidebar-link" href="election-results.php">
                    <i class="align-middle" data-feather="file-text"></i> 
                    <span class="align-middle">Election Results</span>
                </a>
            </li>

            <ul class="sidebar-nav">
            <li class="sidebar-header">
               Programs
            </li>

            <li class="sidebar-item">
    <a data-bs-target="#icons" data-bs-toggle="collapse" class="sidebar-link">
        <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Departments</span>
        <!-- <span class="sidebar-badge badge bg-light">1.500+</span> -->
    </a>
        <ul id="icons" class="sidebar-dropdown list-unstyled collapse " data-bs-parent="#sidebar">
        <li class="sidebar-item">
            <a class="sidebar-link" href="cas.php">
                <i class="align-middle" data-feather="activity"></i> 
                CAS
            </a>
        </li>
        
        <li class="sidebar-item">
            <a class="sidebar-link" href="cbme.php">
                <i class="align-middle" data-feather="briefcase"></i> 
               CBME
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="cte.php">
                <i class="align-middle" data-feather="edit"></i> 
               CTE
            </a>
            </li>
        </ul>
        </li>

        <!-- Only show management section for Super Admin -->
        <?php if ($role === 'super_admin'): ?>
        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Management
            </li>   
            
            <li class="sidebar-item">
                <a class='sidebar-link' href='manageacc.php'>
                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Manage user acc</span>
                </a>
            </li>
        </ul>
        <?php endif; ?>


   
    </div>
    </nav>
 