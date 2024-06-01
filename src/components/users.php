<?php
session_start();
require_once 'dataBase/Database.php';
$user = new User();

if (!isset($_SESSION['userName'])) {
    header("Location: logIn.php");
    exit();
}
$username = $_SESSION['userName'];
$userData = $user->getUser($username);

if ($userData['UserRole'] !== 'admin') {
    header("Location: index.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=ticketon;", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['action']) && isset($_GET['username'])) {
        $username = $_GET['username'];

        switch ($_GET['action']) {
            case 'change_role' :
                $newRole = ($_GET['new_role']);
                $user->changeRole($newRole, $username);
                break;
            case 'ban_user' :
                $user->banUser($username);
                break;
            case 'unban_user' :
                $user->unbanUser($username);
                break;
        }
    }

    $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'username';
    $sortOrder = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'ASC';

    $filterRole = isset($_GET['role']) ? $_GET['role'] : null;
    $filterStatus = isset($_GET['status']) ? $_GET['status'] : null;

    $roleCondition = $filterRole && $filterRole !== 'all' ? "UserRole = '$filterRole'" : '1';
    $statusCondition = $filterStatus && $filterStatus !== 'all' ? "UserStatus = '$filterStatus'" : '1';

    $queryAll = "SELECT * FROM users WHERE $roleCondition AND $statusCondition ORDER BY $sortColumn $sortOrder";

    $stmtAll = $conn->prepare($queryAll);
    $stmtAll->execute();
    $allUsers = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="ticketon.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2>Users</h2>
        <?php
            if (isset($successMessage)) {
                echo '<div class="alert alert-success" role="alert">' . $successMessage . '</div>';
            } elseif (isset($errorMessage)) {
                echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
            }
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th><a class="link-body-emphasis text-decoration-none" href="?sort=username&order=<?php echo $sortColumn === 'username' ? ($sortOrder === 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>&role=<?= $filterRole ?>&status=<?= $filterStatus ?>">Username <?php echo $sortColumn === 'username' ? ($sortOrder === 'ASC' ? '▼' : '▲') : ''; ?></a></th>
                    <th><a class="link-body-emphasis text-decoration-none" href="?sort=FirstName&order=<?php echo $sortColumn === 'FirstName' ? ($sortOrder === 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>&role=<?= $filterRole ?>&status=<?= $filterStatus ?>">First Name <?php echo $sortColumn === 'FirstName' ? ($sortOrder === 'ASC' ? '▼' : '▲') : ''; ?></a></th>
                    <th><a class="link-body-emphasis text-decoration-none" href="?sort=LastName&order=<?php echo $sortColumn === 'LastName' ? ($sortOrder === 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>&role=<?= $filterRole ?>&status=<?= $filterStatus ?>">Last Name <?php echo $sortColumn === 'LastName' ? ($sortOrder === 'ASC' ? '▼' : '▲') : ''; ?></a></th>
                    <th><a class="link-body-emphasis text-decoration-none" href="?sort=Email&order=<?php echo $sortColumn === 'Email' ? ($sortOrder === 'ASC' ? 'DESC' : 'ASC') : 'ASC'; ?>&role=<?= $filterRole ?>&status=<?= $filterStatus ?>">Email <?php echo $sortColumn === 'Email' ? ($sortOrder === 'ASC' ? '▼' : '▲') : ''; ?></a></th>
                    <th>
                        <label for="roleFilter">Role:</label>
                        <select id="roleFilter" name="role" onchange="filterByRole(this.value)">
                            <option value="">-</option>
                            <option value="admin" <?php echo $filterRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="organizer" <?php echo $filterRole === 'organizer' ? 'selected' : ''; ?>>Organizer</option>
                            <option value="user" <?php echo $filterRole === 'user' ? 'selected' : ''; ?>>User</option>
                        </select>
                    </th>
                    <th>
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter" name="status" onchange="filterByStatus(this.value)">
                            <option value="">-</option>
                            <option value="active" <?php echo $filterStatus === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="banned" <?php echo $filterStatus === 'banned' ? 'selected' : ''; ?>>Banned</option>
                        </select>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($allUsers as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['Username']); ?></td>
                        <td><?= htmlspecialchars($user['FirstName']); ?></td>
                        <td><?= htmlspecialchars($user['LastName']); ?></td>
                        <td><?= htmlspecialchars($user['Email']); ?></td>
                        <td><?= htmlspecialchars($user['UserRole']); ?>
                            <?php if ($user['UserRole'] !== 'admin'): ?>
                                <a href="?action=change_role&username=<?= htmlspecialchars($user['Username']); ?>&new_role=admin">Set as Admin</a>
                            <?php endif; ?>
                            <?php if ($user['UserRole'] !== 'organizer'): ?>
                                <a href="?action=change_role&username=<?= htmlspecialchars($user['Username']); ?>&new_role=organizer">Set as Organizer</a>
                            <?php endif; ?>
                            <?php if ($user['UserRole'] !== 'user'): ?>
                                <a href="?action=change_role&username=<?= htmlspecialchars($user['Username']); ?>&new_role=user">Set as User</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['UserStatus'] === 'banned'): ?>
                                <a href="?action=unban_user&username=<?= htmlspecialchars($user['Username']); ?>">Unban</a>
                            <?php else: ?>
                                <a href="?action=ban_user&username=<?= htmlspecialchars($user['Username']); ?>">Ban</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        function filterByRole(role) {
            var url = window.location.href.split('?')[0];
            
            if (role !== null) {
                url += '?role=' + role;
            }
            window.location.href = url;
        }

        function filterByStatus(status) {
            var url = window.location.href.split('?')[0];
            
            if (status !== null) {
                url += '?status=' + status;
            }
            window.location.href = url;
        }
    </script>
</body>
</html>
