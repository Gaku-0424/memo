

<?php
    $host = '127.0.0.1';
    $port = 3306;
    $dbname = 'memo';
    $username = 'root';
    $password = 'root';

    $error = '';
    $rows = [];

    $editRow = null;

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'delete') {
                $id = $_POST['id'];
                $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
                $stmt->execute([':id' => $id]);
                header('Location: index.php');
                exit;
            } elseif ($action === 'update') {
                $id = $_POST['id'];
                $name = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['name']);
                $message = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['message']);
                if ($name === '' || $message === '') {
                    $error = '名前とメッセージを入力してください。';
                    $editRow = ['id' => $id, 'name' => $name, 'message' => $message];
                } else {
                    $stmt = $pdo->prepare('UPDATE posts SET name = :name, message = :message WHERE id = :id');
                    $stmt->execute([':name' => $name, ':message' => $message, ':id' => $id]);
                    header('Location: index.php');
                    exit;
                }
            
            } else {
                $name = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['name']);
                $message = preg_replace('/^[\s　]+|[\s　]+$/u', '', $_POST['message']);
                if ($name === '' || $message === '') {
                    $error = '名前とメッセージを入力してください。';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO posts (name, message) VALUES (:name, :message)');
                    $stmt->execute([':name' => $name, ':message' => $message]);
                    header('Location: index.php');
                    exit;
                }
                
            }
        }

        $rows = $pdo->query('SELECT * FROM posts ORDER BY id DESC')->fetchAll();

        if (isset($_GET['edit'])) {
            $editId = $_GET['edit'];
            $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id');
            $stmt->execute([':id' => $editId]);
            $editRow = $stmt->fetch();
        }

    } catch (PDOException $e) {
        echo '接続エラー:' . htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        );
    }

    

?>
<?php if ($error !== ''): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($editRow): ?>
    <form action="index.php" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($editRow['id']) ?>">
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($editRow['name']) ?>">
        <textarea name="message"><?= htmlspecialchars($editRow['message']) ?></textarea>
        <input type="submit" value="更新">
        <input type="hidden" name="action" value="update">
    </form>
    <?php else: ?>
    <form action="index.php" method="post">
        <input type="hidden" name="action" value="create">
        <input type="text" name="name" id="name">
        <textarea name="message"></textarea>
        <input type="submit" value="投稿">
    </form>
    <?php endif; ?> 

<?php
 foreach ($rows as $row) {
    $safename = htmlspecialchars($row['name']);
    $safemessage = htmlspecialchars($row['message']);
    $safecreated_at = htmlspecialchars($row['created_at']);
    $safeid = htmlspecialchars($row['id']);

    echo <<<HTML
        <div class="post">
            <p class="name">{$safename}</p>
            <p>{$safemessage}</p>
            <p class="date">{$safecreated_at}</p>
            <form method="post">
                <input type="hidden" name="id" value="{$safeid}">
                <input type="submit" value="削除">
                <input type="hidden" name="action" value="delete">
            </form>
            <a href="index.php?edit={$safeid}">編集</a>
        </div>
        HTML;
    }
?>

<style>
    body {
        max-width: 600px;
        margin: 40px auto;
        padding: 0 16px;
        font-family: sans-serif;
        background: #f5f5f5;
        color: #333;
    }
    form {
        background: #fff;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    input[type="text"], textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 8px;
        margin-bottom: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }
    textarea {
        height: 80px;
    }
    input[type="submit"] {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        background: #3b82f6;
        color: #fff;
        cursor: pointer;
    }
    .post {
        background: #fff;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 12px;
    }
    .post p {
        margin: 0 0 8px;
    }
    .post .name {
        font-weight: bold;
    }
    .post .date {
        font-size: 12px;
        color: #888;
    }
    .post form {
        display: inline;
        padding: 0;
        margin: 0;
        background: none;
    }
    .error {
        color: #dc2626;
    }
</style>

    

