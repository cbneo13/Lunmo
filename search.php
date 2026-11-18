<?php
require 'config.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$results = [];
$total = 0;
$isCommand = false;
$commandOutput = '';
if ($query !== '' && str_starts_with($query, '/')) {

    $isCommand = true;
    $cmd = strtolower($query);

    switch ($cmd) {

        
        case '/submit':
            header('Location: submit.php');
            exit;

        case '/about':
            header('Location: about.php');
            exit;

        case '/settings':
            header('Location: settings.php');
            exit;

        
        case '/surprise':
            header('Location: surprise.php');
            exit;

        case '/help':
            $commandOutput = "
                <b>Available commands:</b><br><br>
                <b>Navigation:</b><br>
                /submit - go to submit page<br>
                /about - go to about page<br>
                /settings - go to settings page<br><br>
                <b>Useful:</b><br>
                /surprise - random site<br>
                /help - show this list<br>
                /status - how many sites are indexed<br>
                /latest - latest website indexed<br><br>
                <b>Funny:</b><br>
                /time - show current time<br>
                /pongmsg - show pong message
            ";
            break;

        case '/status':
            $totalSites = $pdo->query("SELECT COUNT(*) FROM sites WHERE is_approved = 1")->fetchColumn();
            $pending = $pdo->query("SELECT COUNT(*) FROM sites WHERE is_approved = 0")->fetchColumn();
            $commandOutput = "Lunmo currently has <b>$totalSites</b> approved websites indexed.<br>Pending approval: <b>$pending</b>";
            break;

        case '/latest':
            $stmt = $pdo->query("SELECT * FROM sites WHERE is_approved = 1 ORDER BY id DESC LIMIT 1");
            $latest = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($latest) {
                $commandOutput = "
                    <b>Latest website indexed:</b><br><br>
                    <a href=\"" . htmlspecialchars($latest['url']) . "\" target=\"_blank\">" . htmlspecialchars($latest['title']) . "</a><br>
                    " . htmlspecialchars($latest['url']) . "<br>
                    " . htmlspecialchars($latest['description']);
            } else {
                $commandOutput = "No websites indexed yet.";
            }
            break;

       
        case '/time':
            $commandOutput = "Current time: <b>" . date('H:i:s') . "</b><br>Date: " . date('d/m/Y');
            break;

        case '/pongmsg':
            $commandOutput = "pong";
            break;

        default:
            $commandOutput = "Unknown command: <b>" . htmlspecialchars($query) . "</b><br>Type /help to see available commands.";
            break;
    }
}

if (!$isCommand && $query !== '') {
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) FROM sites 
        WHERE is_approved = 1 
        AND (
            title LIKE :q 
            OR description LIKE :q 
            OR url LIKE :q 
            OR tags LIKE :q
        )
    ");
    $countStmt->execute(['q' => "%$query%"]);
    $total = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT * FROM sites 
        WHERE is_approved = 1 
        AND (
            title LIKE :q 
            OR description LIKE :q 
            OR url LIKE :q 
            OR tags LIKE :q
        )
        ORDER BY id DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':q', "%$query%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalPages = $total > 0 ? ceil($total / $perPage) : 0;
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Lunmo - the funny search</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <a href="index.php">lunmo</a> 
    <form action="search.php" method="GET" style="display:inline;">
      <input type="text" name="q" value="<?= htmlspecialchars($query) ?>">
      <button type="submit">Search</button>
    </form>

    <div class="links"><a href="settings.php">settings</a></div>
    
    <?php if ($isCommand): ?>
      <p class="results"><?= $commandOutput ?></p>

    <?php elseif ($query === ''): ?>
      <p class="results">Results goes here xd!</p>

    <?php elseif ($total === 0): ?>
      <p class="results">No results found for "<?= htmlspecialchars($query) ?>"</p>

    <?php else: ?>


      <?php foreach ($results as $site): ?>
        <p>
          <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank">
            <?= htmlspecialchars($site['title']) ?>
          </a><br>
          <?= htmlspecialchars($site['url']) ?><br>
          <?= htmlspecialchars($site['description']) ?>
        </p>
      <?php endforeach; ?>

      <p>
        <?php if ($page > 1): ?>
          <a href="search.php?q=<?= urlencode($query) ?>&page=<?= $page - 1 ?>">« previous</a>
        <?php endif; ?>

        <?php if ($page > 1 && $page < $totalPages): ?>
          &nbsp;|&nbsp;
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
          <a href="search.php?q=<?= urlencode($query) ?>&page=<?= $page + 1 ?>">next »</a>
        <?php endif; ?>
      </p>
    <?php endif; ?>

  </body>
</html>