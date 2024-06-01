<?php
    $db = new Database();
    $totalSum = $db->getTotalSum();
?>
<header class="p-2 sticky-top bg-light" >
    <div class="container d-flex align-items-center justify-content-around">
        <a href="index.php">
            <svg height="9.8mm" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 16282 2750">
                <defs>
                    <style type="text/css">
                        .fil0 {fill:#2B2A29}
                        .fil1 {fill:#FF7200}
                        .fil2 {fill:#2B2A29;fill-rule:nonzero}
                    </style>
                </defs>
                <g id="_2186066587888">
                    <path class="fil0" d="M12466 911c66,0 129,13 186,38l-75 160 -136 289 289 -136 160 -76c26,58 41,121 41,189 0,256 -208,464 -465,464 -256,0 -464,-208 -464,-464 0,-257 208,-464 464,-464z"/>
                    <path class="fil1" d="M12466 0c760,0 1375,616 1375,1375 0,759 -615,1375 -1375,1375 -759,0 -1374,-616 -1374,-1375 0,-759 615,-1375 1374,-1375zm0 496c486,0 879,393 879,879 0,485 -393,879 -879,879 -485,0 -878,-394 -878,-879 0,-486 393,-879 878,-879z"/>
                    <path class="fil2" d="M4569 2660l-540 0 0 -1396c0,-161 6,-298 18,-411l-11 0c-25,48 -67,119 -128,210l-1048 1597 -576 0 0 -2541 540 0 0 1405c0,174 -5,290 -14,349l7 0c6,-14 50,-83 131,-207l1008 -1547 613 0 0 2541z"/>
                    <polygon class="fil2" points="2020,585 1295,585 1295,2660 721,2660 721,585 0,585 0,120 2020,120 "/>
                    <path class="fil2" d="M7171 2660l-723 0 -740 -1104c-15,-21 -37,-68 -68,-140l-8 0 0 1244 -573 0 0 -2540 573 0 0 1200 8 0c15,-33 38,-80 72,-141l701 -1059 682 0 -886 1211 962 1329z"/>
                    <polygon class="fil2" points="8939,2660 7415,2660 7415,120 8880,120 8880,585 7988,585 7988,1151 8818,1151 8818,1614 7988,1614 7988,2195 8939,2195 "/>
                    <polygon class="fil2" points="11111,585 10386,585 10386,2660 9812,2660 9812,585 9091,585 9091,120 11111,120 "/>
                    <polygon class="fil2" points="16282,2660 15708,2660 15708,1625 14656,1625 14656,2660 14083,2660 14083,120 14656,120 14656,1133 15708,1133 15708,120 16282,120 "/>
                </g>
            </svg>
        </a>

        <form style="width: 55%;" role="search" action="./index.php" method='post'>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Find among <?php echo $totalSum; ?> events" name="InputSearch">
                <button type="submit" class="btn btn-warning">Search</button>
            </div>
        </form>

        <!-- <div class="dropdown-center d-flex align-items-center">
            <a  class="link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" href="#">
                Help
            </a>
            <ul class="dropdown-menu text-small">
                <li><a class="dropdown-item" href="#">Payment methods</a></li>
                <li><a class="dropdown-item" href="#">Refund Methods</a></li>
                <li><a class="dropdown-item" href="#">Memo of actions in case of emergency</a></li>
                <li><a class="dropdown-item" href="#">Assistance to the organizers</a></li>
            </ul>
        </div>

        <div class="dropdown-center d-flex align-items-center">
            <a  class="link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" href="#">
                About us
            </a>
            <ul class="dropdown-menu text-small">
                <li><a class="dropdown-item" href="#">Contacts</a></li>
                <li><a class="dropdown-item" href="#">About the system</a></li>
                <li><a class="dropdown-item" href="#">Team</a></li>
            </ul>
        </div> -->
        <?php
            if(isset($userData['UserRole']) && ($userData['UserRole'] == 'organizer' || $userData['UserRole'] == 'admin')) {
                echo '<a href="add_event.php" class="btn btn-warning">ADD Event</a>';
            }
            if(isset($userData['UserRole']) && $userData['UserRole'] == 'admin') {
                echo '<a href="users.php" class="btn btn-warning">Users</a>';
            } 
        ?>
        <?php
            if (!isset($_SESSION['userName'])) {
        ?>
                <button class="btn btn-warning" onclick="location.href='logIn.php'" type="button">log in</button>
        <?php
            } else {
        ?>
                <button class="btn btn-warning" onclick="location.href='profile.php'" type="button">
                    <?php echo $userData['LastName']; ?>
                </button>
        <?php
            }
        ?>
    </div>
</header>