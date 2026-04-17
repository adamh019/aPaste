<!-- Sidebar Toggle Button for Mobile -->
<button class="sidebar-toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h4>aPaste</h4>
    <a href="index.php">Home</a>
    <a href="public.php">Public</a>
    <?php 
    if(!isset($_SESSION['username'])){
        echo ("<a href='./login.php'>Login</a>");
    }
    ?>
    
    <hr style="width: 80%; border-top: 1px solid #333;">



    <!-- Profile section at the bottom of the sidebar -->
    <div class="profile-section">
        <a onclick="toggleProfileDropdown()">
            <div class="profile-pic-small">
            <?php 
                if(isset($_SESSION['username'])){
                    echo "<img src='https://proficon.appserver.uk/api/initials/" . $_SESSION['username'] . "' alt='Profile Picture'>";
                } else {
                    echo "<img src='https://proficon.appserver.uk/api/initials/?' alt='Profile Picture'>";
                }
            ?>
            </div>
            <?php 
            if (isset($_SESSION['username'])) {
                echo $_SESSION['username']; 
            } else {
                echo "Account";
            }
            ?>
            <span class="arrow-icon" id="arrowIcon">▼</span>
        </a>
        <div class="profile-dropdown" id="profileDropdown">
            <a href="
            <?php  
                if (isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];
                    echo "/project/pastes.php?username={$username}";
                } else {
                    echo "/project/login.php"; 
                }
            ?>
            ">Pastes</a>

            <a href="
            <?php  
                if (isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];
                    echo "/project/user/$username";  
                } else {
                    echo "./login.php";
                }            
            ?>
            ">Profile/Settings</a>

            <?php 
                if (isset($_SESSION['username'])) {
                    echo '<a href="#" id="logoutBtn">Logout</a>';
                }
            ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#logoutBtn').on('click', function(event) {
        event.preventDefault();

        $.ajax({
            url: './scripts/logout.php',  
            type: 'GET', 
            success: function(response) {
                console.log(response);
                if (response === "success") {
                    // Show SweetAlert2 popup for successful logout
                    Swal.fire({
                        title: 'Logged Out',
                        text: 'You have been successfully logged out!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Redirect to homepage after clicking OK
                        window.location.href = 'index.php';
                    });
                } else {
                    // Show SweetAlert2 error popup
                    Swal.fire({
                        title: 'Error',
                        text: 'Logout failed! Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + status + " - " + error);
                // Show SweetAlert2 error popup for AJAX errors
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while logging out.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });


        // Select All functionality
        $('#selectAllBtn').on('click', function() {
            $('#categoryFilterForm input[type="checkbox"]').prop('checked', true);
        });

        // Unselect All functionality
        $('#unselectAllBtn').on('click', function() {
            $('#categoryFilterForm input[type="checkbox"]').prop('checked', false);
        });
    });
</script>


<style>
    /* Styling for categories section */
    .category-table {
        margin-top: 10px;
    }

    .table-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .category-name {
        text-align: left;
        flex: 1; /* Pushes name to the left */
    }

    .checkbox-cell {
        text-align: center;
        width: 40px; /* Fixed width for alignment */
    }

    /* Styling buttons */
    .btn {
        padding: 5px 10px;
        font-size: 12px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
    }
</style>