<div class="topbar">

    <div class="topbar-left">

        <h2>
            <i class="fa-solid fa-chart-pie"></i> FINTRACK
        </h2>

    </div>

    <div class="topbar-right">

        <span class="user-info">
            <?php echo $_SESSION['full_name']; ?>
        </span>

        <span class="user-role">
            (<?php echo ucfirst($_SESSION['role']); ?>)
        </span>

    </div>

</div>