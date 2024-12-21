console.log("User.js loaded");

$(document).ready(function () {
    const usersTable = $("#users_table tbody");

    // Function to load users
    function loadUsers() {
        console.log("Loading users...");
        $.ajax({
            url: "../MAIN/roles/admin_/view/user-view.php", // Ensure this path is correct
            method: "GET",
            dataType: "html", // Expecting HTML response
            success: function (response) {
                console.log("Users loaded successfully");
                usersTable.html(response); // Populate the table with response
                bindActions(); // Bind actions to buttons (edit, delete)
            },
            error: function (error) {
                console.error("Error loading users:", error.responseText);
            },
        });
    }


    function bindActions() {
        console.log("Binding actions...");
        
        // Bind Add User Button
        $("#add-user").off("click").on("click", function () {
            addUser();
        });
    
        // Bind Edit User Button
        $(".edit-user").off("click").on("click", function () {
            const userId = $(this).data("id");
            editUser(userId);
        });
    
        // Bind Delete User Button
        $(".delete-user").off("click").on("click", function () {
            const userId = $(this).data("id");
            const username = $(this).data("username");
            deleteUser(userId, username);
        });
    
        // Bind Search Input (Search Users)
        $("#search_bar").off("keyup").on("keyup", function () {
            searchUser();
        });
    
        // Bind Role Filter Dropdown
        $("#role_filter").off("change").on("change", function () {
            filterRole();
        });

        // Bind Sort Table Button
        $(".btn-primary").off("click").on("click", function () {
            sortTable();
        });
    }
    

    // Function to show Add User modal
    function addUser() {
        console.log("Opening Add User modal...");
        $.ajax({
            url: "../MAIN/roles/admin_/add/user-add-modal.html",
            method: "GET",
            success: function (view) {
                $(".modal-container").html(view);
                $("#addUserModal").modal("show");

                // Handle Add User form submission
                $("#form-add-user").on("submit", function (e) {
                    e.preventDefault();
                    saveUser();
                });
            },
            error: function () {
                alert("Error loading Add User modal.");
            },
        });
    }

    // Function to save a new user
    function saveUser() {
        const userData = {
            username: $("#username").val(),
            password: $("#password").val(),
            first_name: $("#first_name").val(),
            last_name: $("#last_name").val(),
            email: $("#email").val(),
            role: $("#role").val(),
        };

        $.ajax({
            url: "../MAIN/roles/admin_/add/user-add.php",
            method: "POST",
            dataType: "json",
            data: userData,
            success: function (response) {
                if (response.status === "success") {
                    $("#addUserModal").modal("hide");
                    loadUsers(); // Reload the user list
                } else {
                    alert(response.message || "Error adding user.");
                }
            },
            error: function () {
                alert("Error saving user.");
            },
        });
    }

    // Function to show Edit User modal
    function editUser(userId) {
        console.log("Opening Edit User modal for userId:", userId);
        $.ajax({
            url: `../MAIN/roles/admin_/edit/user-edit-modal.html?id=${userId}`,
            method: "GET",
            success: function (view) {
                $(".modal-container").html(view);
                $("#editUserModal").modal("show");

                // Populate user details
                $.ajax({
                    url: `../MAIN/roles/admin_/edit/user-edit-fetch.php?id=${userId}`,
                    method: "GET",
                    dataType: "json",
                    success: function (user) {
                        $("#username").val(user.username);
                        $("#first_name").val(user.first_name);
                        $("#last_name").val(user.last_name);
                        $("#email").val(user.email);
                        $("#role").val(user.role);
                    },
                    error: function () {
                        alert("Error fetching user details.");
                    },
                });

                // Handle Edit User form submission
                $("#form-edit-user").on("submit", function (e) {
                    e.preventDefault();
                    updateUser(userId);
                });
            },
            error: function () {
                alert("Error loading Edit User modal.");
            },
        });
    }

    // Function to update a user
    function updateUser(userId) {
        const userData = {
            user_id: userId,
            username: $("#username").val(),
            first_name: $("#first_name").val(),
            last_name: $("#last_name").val(),
            email: $("#email").val(),
            role: $("#role").val(),
        };

        $.ajax({
            url: "../MAIN/roles/admin_/edit/user-edit.php",
            method: "POST",
            dataType: "json",
            data: userData,
            success: function (response) {
                if (response.status === "success") {
                    $("#editUserModal").modal("hide");
                    loadUsers(); // Reload the user list
                } else {
                    alert(response.message || "Error updating user.");
                }
            },
            error: function () {
                alert("Error updating user.");
            },
        });
    }

    // Function to delete a user
    function deleteUser(userId, username) {
        if (confirm(`Are you sure you want to delete the user "${username}"?`)) {
            $.ajax({
                url: "../MAIN/roles/admin_/delete/user-delete.php",
                method: "POST",
                dataType: "json",
                data: { user_id: userId },
                success: function (response) {
                    if (response.status === "success") {
                        alert("User deleted successfully.");
                        loadUsers(); // Reload the user list to reflect the deletion
                    } else {
                        alert(response.message || "Error deleting user.");
                    }
                },
                error: function () {
                    alert("Error deleting user.");
                },
            });
        }
    }

    // Function to search users
    function searchUser() {
        const searchQuery = document.getElementById('search_bar').value.toLowerCase();
        const rows = document.querySelectorAll('#users_table_body tr');
        
        rows.forEach(row => {
            const usernameCell = row.cells[0]; // Assuming username is in the first column
            const username = usernameCell.textContent.toLowerCase();
            if (username.includes(searchQuery)) {
                row.style.display = ''; // Show row if it matches the search query
            } else {
                row.style.display = 'none'; // Hide row if it does not match the search query
            }
        });
    }

    // Function to filter users by role
    // Function to filter users by role
function filterRole() {
    const selectedRole = document.getElementById('role_filter').value;
    const rows = document.querySelectorAll('#users_table_body tr');
    
    rows.forEach(row => {
        const roleCell = row.cells[1]; // Assuming role is in the second column
        const role = roleCell.textContent.toLowerCase();
        
        if (selectedRole === '' || role === selectedRole.toLowerCase()) {
            row.style.display = ''; // Show row if it matches the selected role
        } else {
            row.style.display = 'none'; // Hide row if it does not match the selected role
        }
    });
}


    // Function to sort the table alphabetically by username
    function sortTable() {
        const tableBody = document.getElementById('users_table_body');
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        rows.sort((rowA, rowB) => {
            const usernameA = rowA.cells[0].textContent.toLowerCase();
            const usernameB = rowB.cells[0].textContent.toLowerCase();
            
            if (usernameA < usernameB) return -1;
            if (usernameA > usernameB) return 1;
            return 0;
        });

        // Re-append rows in the sorted order
        rows.forEach(row => {
            tableBody.appendChild(row);
        });
    }

    // Initial Load
    loadUsers();
});
