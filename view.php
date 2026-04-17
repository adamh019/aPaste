<?php
session_start();

require_once './database/_connect.php';

// Define categories for readability
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the paste content from the database based on the id
    $sql = "SELECT title, content, category, author, time_created, visibility, LENGTH(content) as file_size, password FROM pastes WHERE uid = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $paste = $result->fetch_assoc();
        $title = htmlspecialchars($paste['title']);
        $content = htmlspecialchars($paste['content']);
        $category = htmlspecialchars($paste['category']);
        $author = htmlspecialchars($paste['author']);
        $visibility = htmlspecialchars($paste['visibility']);
        $time_created = strtotime($paste['time_created']);
        $file_size = $paste['file_size'];
        $password = $paste['password']; // Retrieve the password directly
        $time_ago = time() - $time_created; // Time difference
        $time_ago = human_readable_time($time_ago);
        ?>
        <?php if ($visibility === 'Private' && !empty($password)): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Enter Password',
                        input: 'password',
                        inputLabel: 'Password',
                        inputPlaceholder: 'Enter the password to view this paste',
                        showCancelButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('scripts/validate_password.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ pasteId: <?php echo $id; ?>, password: result.value })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('mainContent').style.display = 'block'; // Show content
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Access Denied',
                                        text: data.error
                                    }).then(() => {
                                        window.history.back();
                                    });
                                }
                            });
                        } else {
                            window.history.back();
                        }
                    });
                });
            </script>
        <?php endif; ?>
        <?php
    } else {
        echo "Paste not found!";
        exit;
    }
} else {
    echo "No paste id provided!";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div id="mainContent" 
    <?php if ($visibility === "Private" && !empty($password)): ?>
        style="display: none;"
    <?php endif; ?>>
    <div class="container mt-4">
        <!-- Title and Category displayed side by side -->
        <div class="d-flex align-items-center">
            <h1 class="display-4"><?php echo $title; ?></h1>
            <span class="badge bg-primary ms-4 fs-4 px-3 py-2"><?php echo array_search($category, $categories); ?></span>
        </div>

        <div class="mt-2">
            <?php 
            if (!empty($author) && $author !== 'NULL') {
                echo "<p><strong>Author:</strong> " . htmlspecialchars($author) . "</p>";
            } else {
                echo "<p><strong>No Author</strong></p>";
            }
            ?>
            <p><?php echo $time_ago; ?> <strong>│</strong> <?php echo number_format($file_size) . " B"; ?></p>
        </div>
        <hr>

        <!-- Code Snippet -->
        <div class="code-snippet">
            <pre><code id="codeContent" class="language-<?php echo $category; ?>"><?php echo $content; ?></code></pre>
        </div>
        <hr>

        <!-- Copy Button -->
        <button id="copyButton" class="btn btn-secondary mt-3">Copy</button>
        <button onclick="history.back()" class="btn btn-primary mt-3">Back to Pastes</button>
    </div>
</div>


        <br><br><br>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            hljs.highlightAll();

            // Add click event listener to the Copy button
            const copyButton = document.getElementById('copyButton');
            const codeContent = document.getElementById('codeContent').innerText; // Get code content

            copyButton.addEventListener('click', () => {
                // Use the Clipboard API to copy the text
                navigator.clipboard.writeText(codeContent).then(() => {
                    // Show SweetAlert2 success popup
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'The code has been copied to the clipboard.',
                        confirmButtonText: 'OK'
                    });
                }).catch(err => {
                    console.error('Error copying text: ', err);
                    // Show SweetAlert2 error popup if there’s an issue
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'There was an error copying the text.',
                        confirmButtonText: 'Try Again'
                    });
                });
            });
        });
    </script>
</body>
</html>
