<?php
session_start();

// Get the username from the query string
$usernameFromUrl = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : null;

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: /project/login.php");
    exit;
}



// The user is logged in and the username matches
$username = $_SESSION['username']; // Get the logged-in user's username
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <h1 class="display-4"><?php echo $_SESSION['username'];?>'s Pastes</h1>
    <p>Share Your Code, Inspire the World: Dive Into the Power of Collaboration!</p>

    <div class="pastes-section">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Your Pastes</h2>
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

    // Get logged-in user's username
    $username = $_SESSION['username'];

    // Fetch all pastes for the user with optional category filtering
    $selectedCategories = isset($_GET['categories']) ? $_GET['categories'] : [];
    $categoryCondition = '';

    if (!empty($selectedCategories)) {
        $categoryList = implode("','", array_map(function($category) use ($connect) {
            return mysqli_real_escape_string($connect, $category);
        }, $selectedCategories));
        $categoryCondition = "AND category IN ('$categoryList')";
    }

    $sql = "SELECT uid, title, content, category, visibility 
            FROM pastes 
            WHERE author = '$username' $categoryCondition 
            ORDER BY time_created DESC";

    $result = mysqli_query($connect, $sql);

    // Display pastes
    if (mysqli_num_rows($result) > 0) {
        echo "<div class='pastes-list mt-4'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['uid'];
            $title = htmlspecialchars($row['title']);
            $visibility = htmlspecialchars($row['visibility']);
            $snippet = htmlspecialchars(substr($row['content'], 0, 100)) . "...";
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
            
            // Map category value to its label
            $categoryKey = array_search($row['category'], $categories);
            $category = htmlspecialchars($categoryKey !== false ? $categoryKey : $row['category']);
            if (!function_exists('getFirstThreeLines')) {
                function getFirstThreeLines($content) {
                    $lines = explode("\n", $content); // Split content by new lines
                    $firstThreeLines = array_slice($lines, 0, 5); // Get the first 3 lines
                    return implode("\n", $firstThreeLines); // Join them back into a string
                }
            }
            
            echo "<div class='paste-item'>
                <h3>
                    $title ($visibility)
                    <span class='badge bg-primary'>" . $category . "</span>
                </h3>
                <div class='code-snippet'>
                    <pre><code class='language-" . htmlspecialchars($row['category']) . "'>" . htmlspecialchars(getFirstThreeLines($row['content'])) . "</code></pre>
                </div>
                <hr>
                <div class='paste-actions'>
                    <a href='view.php?id=$id' class='btn btn-success' onclick='window.location.href=this.href; return false;'>View</a>
                    <a href='#' class='btn btn-info share-btn' data-id='$id'>Share</a>
                    <a href='#' class='btn btn-danger delete-btn' data-id='$id'>Delete</a>
                </div>
            </div>";

        }
        echo "</div>";
    } else {
        echo "<p class='mt-4'>No pastes found for the selected categories.</p>";
    }
    ?>
</div>
</div>


<?php 
include("includes/form.php")
?>


<script>



$(document).ready(function() {
        // Delete button click handler
        $('.delete-btn').on('click', function(event) {
            event.preventDefault(); // Prevent default link behavior

            const pasteId = $(this).data('id'); // Get the paste ID from the button's data-id attribute

            // SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to undo this action!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, redirect to the delete.php page
                    window.location.href = `scripts/delete.php?id=${pasteId}`;
                }
            });
        });
    });

    $(document).ready(function() {
        // Prevent overriding of the View button
        $('.btn-success').on('click', function(event) {
            // Allow default behavior for View button
            return true;
        });
        
        // Select All Functionality
        $('.btn-info').on('click', function(event) {
            event.preventDefault(); // Prevent form submission
            $('#categoryFilterForm input[type="checkbox"]').prop('checked', true); // Check all checkboxes
        });

        // Deselect All Functionality
        $('.btn-success').on('click', function(event) {
            event.preventDefault(); // Prevent form submission
            $('#categoryFilterForm input[type="checkbox"]').prop('checked', false); // Uncheck all checkboxes
        });

    });

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
    $(document).ready(function () {
    // Share button click handler
    $('.share-btn').on('click', function (event) {
        event.preventDefault(); // Prevent default link behavior

        const pasteId = $(this).data('id'); // Get the paste ID from the button's data-id attribute
        const shareLink = `${window.location.origin}/view.php?id=${pasteId}`; // Create the shareable link

        // Copy the share link to the clipboard
        navigator.clipboard.writeText(shareLink).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Link Copied!',
                text: 'The shareable link has been copied to the clipboard.',
                confirmButtonText: 'OK',
            });
        }).catch((err) => {
            console.error('Error copying share link: ', err);
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'There was an error copying the share link.',
                confirmButtonText: 'Try Again',
            });
        });
    });
});

</script>

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
