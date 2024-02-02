<?php
require_once 'display.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Title Here</title>
    <!-- Include any CSS files or stylesheets here -->
</head>
<body>
    <?php
    displayHead();
    ?>
    <div class="container">
        <?php
        displayThemeSelector();
        displayNavbar();
        displayFooter();
        ?>
    </div>
    <!-- Include any JavaScript files or scripts here -->
    <script src="indexselector.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
</body>
</html>

