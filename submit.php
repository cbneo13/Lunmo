<?php
require 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');

    if (empty($url)) {
        $error = 'URL is required.';
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error = 'Please enter a valid URL.';
    } else {
        $title = parse_url($url, PHP_URL_HOST) ?: $url;

        $stmt = $pdo->prepare("
            INSERT INTO sites (title, url, description, is_approved)
            VALUES (:title, :url, '', 0)
        ");
        $stmt->execute([
            'title' => $title,
            'url' => $url
        ]);

        $message = 'Site submitted! Waiting for approval.';
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Lunmo - the funny search</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>

    <?php if ($message): ?>
      <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      url: <input type="text" name="url">
      <br>
      <br>
      <button type="submit">Submit</button>
    </form>

    <p class="submit-text">what type of sites can be indexed?</p>
    <p class="submit-text">Only Web1.0 websites that works on old browsers like Internet Explorer 6 ,Mozilla Firefox 46 or Google Chrome 33 </p>

  </body>
</html>