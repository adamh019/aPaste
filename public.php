<?php
session_start();

// Get the username from the query string
$usernameFromUrl = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : null;


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>aPaste</title>
    <?php include("includes/bootstrap.php") ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/sidebar.css">
    <?php include "includes/fontGlobal.php"?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Highlight.js Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>

    <!-- Highlight.js All Languages -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/html.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/cpp.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/csharp.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            hljs.highlightAll();
        });
    </script>
</head>
<body>

<?php include("includes/sidebar.php");?>

<!-- Main Content -->
<div class="main-content">
    <h1 class="display-4">aPaste Public Posts</h1>
    <p>Explore. Share. Inspire: Dive into the World of Public Code with aPaste!</p>

    <div class="pastes-section">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Public Pastes</h2>
        <!-- Filter Button -->
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            Filters
        </button>
    </div>

    <!-- Filter Collapse -->
    <div class="collapse" id="filterCollapse">
        <form id="categoryFilterForm" method="GET" class="mt-3">
            <div class="row">
                <?php
                // List of categories
                $categories = [
                    "Plain Text" => "plaintext",
                    "C#" => "csharp",
                    "C++" => "cpp",
                    "HTML" => "html",
                    "CSS" => "css",
                    "PHP" => "php",
                    "SQL" => "sql",
                    "JavaScript" => "javascript",
                    "Python" => "python"
                ];

                $selectedCategories = isset($_GET['categories']) ? $_GET['categories'] : [];

                foreach ($categories as $label => $value) {
                    $checked = in_array($value, $selectedCategories) ? "checked" : "";
                    echo "
                        <div class='col-6 col-md-4'>
                            <div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='categories[]' id='category_$value' value='$value' $checked>
                                <label class='form-check-label' for='category_$value'>$label</label>
                            </div>
                        </div>";
                }
                ?>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <button type="submit" class="btn btn-info">Select All</button>
                <button type="submit" class="btn btn-success">Deselect All</button>

            </div>
        </form>
    </div>

    <!-- Pastes List -->
    <?php
    require_once './database/_connect.php';
    ?>


<?php
require_once './database/_connect.php'; // Include database connection

// Fetch selected categories from the GET request
$selectedCategories = isset($_GET['categories']) ? $_GET['categories'] : [];

// Build the category filter condition for the SQL query
$categoryCondition = '';
if (!empty($selectedCategories)) {
    // Escape and format selected categories for the SQL query
    $escapedCategories = array_map(function($category) use ($connect) {
        return "'" . mysqli_real_escape_string($connect, $category) . "'";
    }, $selectedCategories);
    $categoryCondition = "AND category IN (" . implode(',', $escapedCategories) . ")";
}

// SQL query to fetch posts with filters applied
$sql = "SELECT uid, title, visibility, category, author, UNIX_TIMESTAMP(time_created) AS time_created, LENGTH(content) AS file_size
        FROM pastes 
        WHERE visibility IN ('Public', 'Private') 
        $categoryCondition 
        ORDER BY time_created DESC";

// Execute the query
$result = mysqli_query($connect, $sql);

// Check if the query was successful
if (!$result) {
    echo "<p class='mt-4'>Error: Unable to fetch posts. " . mysqli_error($connect) . "</p>";
    exit;
}

// Convert time to a human-readable format (e.g., "5 seconds ago")
function human_readable_time($seconds) {
    if ($seconds < 60) {
        return "$seconds seconds ago";
    } elseif ($seconds < 3600) {
        return floor($seconds / 60) . " minutes ago";
    } elseif ($seconds < 86400) {
        return floor($seconds / 3600) . " hours ago";
    } elseif ($seconds < 2592000) {
        return floor($seconds / 86400) . " days ago";
    } elseif ($seconds < 31536000) {
        return floor($seconds / 2592000) . " months ago";
    } else {
        return floor($seconds / 31536000) . " years ago";
    }
}

// Display the posts
if (mysqli_num_rows($result) > 0) {
    echo "<div class='pastes-list mt-4'>";
    while ($row = mysqli_fetch_assoc($result)) {
        $id = htmlspecialchars($row['uid']);
        $title = htmlspecialchars($row['title']);
        $visibility = htmlspecialchars($row['visibility']);
        $category = htmlspecialchars($row['category']); // Fixed: Fetch the category
        $author = htmlspecialchars($row['author']);
        $file_size = number_format($row['file_size']) . "B";
        $timeCreated = $row['time_created']; // Use the raw value (already a UNIX timestamp from the query)
        $time_ago = time() - $timeCreated; // Calculate time difference in seconds
        $time_ago = human_readable_time($time_ago); // Convert to a human-readable format
        // Map category value to its label
        $categoryKey = array_search($row['category'], $categories);
        $category = htmlspecialchars($categoryKey !== false ? $categoryKey : $row['category']);

        echo "<div class='paste-item mb-4'>
            <h3 class='mb-2'>
                $title ($visibility)
                <span class='badge bg-primary'>$category</span>
            </h3>
            <p><strong>Author:</strong> $author | $time_ago | $file_size</p>
            <a href='view.php?id=$id' class='btn btn-secondary'>View Full</a>
        </div><hr>";
    }
    echo "</div>";
} else {
    echo "<p class='mt-4'>No posts available for the selected filters.</p>";
}
?>

<?php 
include("includes/form.php")
?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('pasteForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(event.target);

    // Send AJAX request
    fetch('./scripts/insert_pastes.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                });
                event.target.reset();
                // Optionally reload the page or fetch updated data
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error,
                });
            }
        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
            });
            console.error('Error:', error);
        });
});


    // Function to toggle the visibility of the Create New Paste form
    function toggleCreateForm() {
        const form = document.getElementById('createForm');
        form.classList.toggle('open');
    }


    // Sidebar toggle for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    }

    // Toggle profile dropdown visibility and arrow direction
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        const arrowIcon = document.getElementById('arrowIcon');
        dropdown.classList.toggle('show');
        arrowIcon.classList.toggle('arrow-up');
    }
    
    

</script>

</body>
</html>
