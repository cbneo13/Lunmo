<!DOCTYPE html>
<html>
  <head>
    <title>Lunmo - the funny search</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="links">
      <a href="submit.php">Submit a page here!</a><br>
      <a href="settings.php">settings</a>
    </div>
    <br>
    <div class="dulinks">
      <a href="//youfilm.lunmo.ws">YouFilm</a> |
      <a href="//mail.lunmo.ws">Lunmo Mail</a>
    </div>

    <h1 class="title">Lunmo</h1>

    <center>
      <form action="search.php" method="GET">
        <input type="text" name="q" style="width:300px" placeholder="search funny sites..." required>
        <button type="submit">Search</button>
      </form>
    </center>

    <br>
    <center><a href="surprise.php">surprise me...</a></center>
    <p align="center">a cool way to search funny sites in the old-web</p>
    <center>
      <a href="about.php">About</a> | 
      <a href="privacy.php">Privacy</a> | 
      <a href="devs.php">Our Devs</a>
    </center>
  </body>
</html>