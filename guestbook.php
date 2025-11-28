<?php
// guestbook.php
// Single-file Guestbook CRUD (Create, Read, Update, Delete) with basic security and validation.

// ---------------------- CONFIG ----------------------
session_start();

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'guestbook_db';
$db_port = 3306;

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// ---------------------- CONNECT ----------------------
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
$mysqli->set_charset('utf8mb4');

// ---------------------- HELPERS ----------------------
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$errors = [];
$edit_mode = false;
$edit_data = null;

// ---------------------- EDIT MODE DETECTION ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $edit_id_raw = $_GET['edit'];

    if (ctype_digit((string)$edit_id_raw)) {
        $edit_id = (int)$edit_id_raw;

        $stmt = $mysqli->prepare("SELECT id, guest_name, message_text FROM entries WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $edit_data = $result->fetch_assoc();
            $edit_mode = true;
        }
        $stmt->close();
    }
}

// ---------------------- CREATE ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {

    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], (string)$token)) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $name = trim((string)($_POST['guest_name'] ?? ''));
        $message = trim((string)($_POST['message_text'] ?? ''));

        if ($name === '') {
            $errors[] = 'Name is required.';
        } elseif (mb_strlen($name) > 255) {
            $errors[] = 'Name must be 255 characters or fewer.';
        }

        if ($message === '') {
            $errors[] = 'Message is required.';
        } elseif (mb_strlen($message) > 2000) {
            $errors[] = 'Message must be 2000 characters or fewer.';
        }

        if (empty($errors)) {
            $stmt = $mysqli->prepare("INSERT INTO entries (guest_name, message_text) VALUES (?, ?)");
            $stmt->bind_param('ss', $name, $message);
            $stmt->execute();
            $stmt->close();

            $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }
    }
}

// ---------------------- UPDATE ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {

    $token = $_POST['csrf_token'] ?? '';
    $id_raw = $_POST['entry_id'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], (string)$token)) {
        $errors[] = 'Invalid CSRF token.';
    } elseif (!ctype_digit((string)$id_raw)) {
        $errors[] = 'Invalid entry ID.';
    } else {
        $id = (int)$id_raw;
        $name = trim((string)($_POST['guest_name'] ?? ''));
        $message = trim((string)($_POST['message_text'] ?? ''));

        if ($name === '') {
            $errors[] = 'Name is required.';
        } elseif (mb_strlen($name) > 255) {
            $errors[] = 'Name must be 255 characters or fewer.';
        }

        if ($message === '') {
            $errors[] = 'Message is required.';
        } elseif (mb_strlen($message) > 2000) {
            $errors[] = 'Message must be 2000 characters or fewer.';
        }

        if (empty($errors)) {
            $stmt = $mysqli->prepare("UPDATE entries SET guest_name = ?, message_text = ? WHERE id = ? LIMIT 1");
            $stmt->bind_param('ssi', $name, $message, $id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
            header('Location: guestbook.php');
            exit;
        }
    }
}

// ---------------------- DELETE ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {

    $id_raw = $_GET['delete'];
    $token = $_GET['token'] ?? '';

    if (!ctype_digit((string)$id_raw)) {
        $errors[] = 'Invalid entry id.';
    } elseif (!hash_equals($_SESSION['csrf_token'], (string)$token)) {
        $errors[] = 'Invalid or missing CSRF token.';
    } else {
        $id = (int)$id_raw;

        $check = $mysqli->prepare("SELECT id FROM entries WHERE id = ? LIMIT 1");
        $check->bind_param('i', $id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $errors[] = 'Entry not found.';
        } else {
            $del = $mysqli->prepare("DELETE FROM entries WHERE id = ? LIMIT 1");
            $del->bind_param('i', $id);
            $del->execute();
            $del->close();

            $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
            header('Location: guestbook.php');
            exit;
        }
    }
}

// ---------------------- READ ----------------------
$entries = [];
$res = $mysqli->query("SELECT id, guest_name, message_text, submission_time FROM entries ORDER BY submission_time DESC");
while ($row = $res->fetch_assoc()) {
    $entries[] = $row;
}
$res->free();

// ---------------------- HTML OUTPUT ----------------------
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Guestbook (CRUD)</title>
<meta name="description" content="PHP/MySQL Beginers Test">
<meta name="author" content="Chidiebube Christopher Onwugbufor">
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div>
        <h1>Guestbook: Full CRUD (Create, Read, Update, Delete)</h1>
        <div class="small">Safe inputs, prepared statements, CSRF token for delete/create.</div>
    </div>
</header>

<!-- ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Guestbook (CRUD)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Guestbook: Full CRUD</h1>
</header> -->

<main>

<?php if (!empty($errors)): ?>
<div class="card err">
    <strong>Errors:</strong>
    <ul>
        <?php foreach ($errors as $e): ?>
        <li><?php echo h($e); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<section class="card">
    <h2><?php echo $edit_mode ? 'Edit Message' : 'Post a Message'; ?></h2>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

        <?php if ($edit_mode): ?>
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="entry_id" value="<?php echo h($edit_data['id']); ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <div class="row">
            <label>Name</label>
            <input type="text" name="guest_name" maxlength="255"
                value="<?php echo h($edit_mode ? $edit_data['guest_name'] : ($_POST['guest_name'] ?? '')); ?>">
        </div>

        <div class="row">
            <label>Message</label>
            <textarea name="message_text" rows="5" maxlength="2000"><?php 
                echo h($edit_mode ? $edit_data['message_text'] : ($_POST['message_text'] ?? '')); 
            ?></textarea>
        </div>

        <button type="submit">
            <?php echo $edit_mode ? 'Update Message' : 'Submit'; ?>
        </button>

        <?php if ($edit_mode): ?>
            <a href="guestbook.php" class="cancel-link">Cancel Edit</a>
        <?php endif; ?>
    </form>
</section>

<section>
    <h2>Messages (newest first)</h2>

    <?php foreach ($entries as $entry): ?>
    <article class="card">
        <div class="meta">
            <strong><?php echo h($entry['guest_name']); ?></strong>
            •
            <time><?php echo h($entry['submission_time']); ?></time>

            <a class="edit-link" href="?edit=<?php echo h($entry['id']); ?>">Edit</a>
            <a class="delete-link" href="?delete=<?php echo h($entry['id']); ?>&token=<?php echo h($_SESSION['csrf_token']); ?>"
               onclick="return confirm('Delete this entry?');">Delete</a>
        </div>

        <p><?php echo nl2br(h($entry['message_text'])); ?></p>
    </article>
    <?php endforeach; ?>
</section>
<footer>
        <div class="small">Checklist:
            <ul>
                <li>Database/table: <strong>guestbook_db.entries</strong></li>
                <li>Connection: <strong>MySQLi (utf8mb4)</strong></li>
                <li>Read: <strong>SELECT ... ORDER BY submission_time DESC</strong></li>
                <li>Create: <strong>POST form, prepared statement</strong></li>
                <li>Sanitization: <strong>prepared statements + output escaping</strong></li>
                <li>Delete: <strong>GET id + CSRF token, prepared DELETE ... WHERE id = ? LIMIT 1</strong></li>
            </ul>
        </div>
    </footer>

</main>

</body>
</html>
