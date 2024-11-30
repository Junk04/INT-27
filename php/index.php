<?php
function checkLogin($pdo, $username, $password) {
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    try {
        $stmt = $pdo->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Ошибка при выполнении запроса: " . htmlspecialchars($e->getMessage()));
    }
}

$host = 'postgres';
$port = '5432';
$dbname = 'main';
$dbuser = 'connection_user';
$dbpassword = '123';

$authMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $dbuser, $dbpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $user = checkLogin($pdo, $username, $password);
        if ($user) {
            $authMessage = 'Авторизация успешна!';
        } else {
            $authMessage = 'Неправильный логин или пароль.';
        }
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage()));
    }
}

$sqlResults = '';
$sqlQuery = 'SELECT * FROM information';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sql_query'])) {
    $sqlQuery = $_POST['sql_query'];
}

try {
    if (!empty($pdo) && $authMessage === 'Авторизация успешна!') {
        $stmt = $pdo->query($sqlQuery);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sqlResults .= '<tr>';
            foreach ($row as $column) {
                $sqlResults .= '<td>' . htmlspecialchars($column) . '</td>';
            }
            $sqlResults .= '</tr>';
        }
    }
} catch (PDOException $e) {
    $sqlResults = '<tr><td colspan="3">Ошибка: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Demo</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            margin-top: 10px;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>SQL Injection Demo</h1>

    <h2>Login</h2>
    <form method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
    <p><strong><?php echo $authMessage; ?></strong></p>

    <?php if ($authMessage === 'Авторизация успешна!'): ?>
        <h2>Execute SQL Query</h2>
        <form method="post">
            <label for="sql_query">SQL Query:</label><br>
            <textarea name="sql_query" id="sql_query" rows="4" cols="3"><?php echo htmlspecialchars($sqlQuery); ?></textarea><br>
            <button type="submit">Выполнить запрос</button>
        </form>

        <p><strong>Выполненный запрос:</strong> <?php echo htmlspecialchars($sqlQuery); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Что?</th>
                    <th>Где?</th>
                    <th>Когда?</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $sqlResults; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
