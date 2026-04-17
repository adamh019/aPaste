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
    
    <a href="#" onclick="toggleCreateForm()">Create New Paste</a>

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
                    echo "./pastes.php";
                } else {
                    echo "./login.php"; // Ensure the login path is accurate
                }
            ?>
            ">Pastes</a>

            <a href="
            <?php  
                if (isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];
                    echo "./user/$username";  // Adjust this path as needed for your Plesk environment
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
                        // SweetAlert2 success popup
                        Swal.fire({
                            title: 'Logged Out Successfully',
                            text: 'You have been logged out of your account.',
                            icon: 'success',
                            confirmButtonText: 'Go to Homepage'
                        }).then(() => {
                            // Redirect to homepage after confirmation
                            window.location.href = 'index.php';
                        });
                    } else {
                        // SweetAlert2 error popup
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
                    // SweetAlert2 error popup for AJAX failure
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while logging out.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>


