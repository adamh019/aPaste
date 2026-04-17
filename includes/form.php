<!-- Slide-in Create New Paste Form -->
<div class="create-form" id="createForm">
    <span class="close-btn" onclick="toggleCreateForm()">×</span>
    <h3>Create New Paste</h3>
    <form id="pasteForm">
        <div class="form-group full-width">
            <input type="text" name="title" class="form-control mb-3" placeholder="Title" required>
        </div>
        <div class="form-group full-width">
            <textarea name="content" class="form-control" rows="15" placeholder="Paste in here" style="resize: none;" required></textarea>
        </div>
        <div class="form-group">
            <label for="visibility">VISIBILITY</label>
            <select name="visibility" class="form-control" id="visibility" required>
                <option value="Unlisted">Unlisted</option>
                <option value="Public">Public</option>
                <option value="Private">Private</option>
            </select>
        </div>
        <div class="form-group">
            <label for="highlighting">CATEGORY</label>
            <select name="category" class="form-control" id="highlighting" required>
                <option value="plaintext">None</option>
                <option value="plaintext">Plain Text</option>
                <option value="csharp">C#</option>
                <option value="cpp">C++</option>
                <option value="html">HTML</option>
                <option value="css">CSS</option>
                <option value="php">PHP</option>
                <option value="sql">SQL</option>
                <option value="javascript">JavaScript</option>
                <option value="python">Python</option>
            </select>
        </div>
        <div class="form-group">
            <label for="expiry">EXPIRY</label>
            <select name="expiry" class="form-control" id="expiry" required>
                <option>Never</option>
                <option>15 Mins</option>
                <option>1 Hour</option>
                <option>1 Day</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password">PASSWORD</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Password (Optional)">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Submit</button>
    </form>
</div>

<script>
    // Get references to the visibility dropdown and password input
    const visibilityDropdown = document.getElementById("visibility");
    const passwordField = document.getElementById("password");

    // Function to toggle the required attribute on the password field
    function togglePasswordRequirement() {
        if (visibilityDropdown.value === "Private") {
            passwordField.setAttribute("required", "true");
            passwordField.placeholder = "Password (Required)";
        } else {
            passwordField.removeAttribute("required");
            passwordField.placeholder = "Password (Optional)";
        }
    }

    // Attach an event listener to the visibility dropdown
    visibilityDropdown.addEventListener("change", togglePasswordRequirement);

    // Run the function once to set the correct state on page load
    togglePasswordRequirement();
</script>
