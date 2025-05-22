document.addEventListener("DOMContentLoaded", () => {
    
    // ------------------------ upload profile image ---------------------------

    const profile_pic = document.querySelector("#user-profile-pic img");
    const top_bar_pic = document.querySelector(".profile-pic img");
    const image_input = document.getElementById("image_input");
    const camera = document.querySelector(".camera");

    camera.addEventListener("click", () => {
        image_input.value = "";
        image_input.click();
    });
    
    image_input.addEventListener("change", () => {
        if (image_input.files && image_input.files.length > 0) {
            const selected_file = image_input.files[0];
            console.log("Selected file:", selected_file);
    
            if (selected_file) {
                
                const imageURL = URL.createObjectURL(selected_file);
                profile_pic.src = imageURL;
                top_bar_pic.src = imageURL;
            } else {
                console.error("No file selected.");
            }
        } else {
            console.error("No file selected.");
        }
    });

    // ------------------------ save data to database ---------------------------

    const save_btn = document.getElementById("save");

    const password_pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&^#()_+])[A-Za-z\d@$!%*?&^#()_+]{8,}$/;
    const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    
    function validateInputs() {
        const username = document.querySelector("#username input");
        const email = document.getElementById("email");

        let isValid = true;
        let firstInvalidInput = null;

        // Username validation
        if (!username.value.trim()) {
            username.setCustomValidity("Username is required.");
            isValid = false;
            firstInvalidInput = firstInvalidInput || username;
        } else {
            username.setCustomValidity("");
        }

        // Email validation
        if (!email.value.trim()) {
            email.setCustomValidity("Email is required.");
            isValid = false;
            firstInvalidInput = firstInvalidInput || email;
        } else if (!email_pattern.test(email.value)) {
            email.setCustomValidity("Please enter a valid email address.");
            isValid = false;
            firstInvalidInput = firstInvalidInput || email;
        } else {
            email.setCustomValidity("");
        }

        if (!isValid) {
            firstInvalidInput.reportValidity();
            firstInvalidInput.focus();
        }

        return isValid;
    }

    document.querySelectorAll("input").forEach(input => {
        input.addEventListener("input", () => {
            if (input.validity.customError) {
                input.setCustomValidity(""); 
                input.reportValidity(); 
            }
        });

    });
    
    save_btn.addEventListener("click", (event) => {
        event.preventDefault();              
        if (!validateInputs()) return;        

        const username_input = document.querySelector("#username input").value.trim();
        const email_input = document.getElementById("email").value.trim();

        if (!username_input || !email_input) {
            alert("All fields are required!");
            return;
        }
        
        const form = new FormData();
        form.append("username", username_input);
        form.append("email", email_input);

        if (image_input.files.length > 0) {
            form.append("profile_pic", image_input.files[0]);
        } else {
            form.append("profile_pic", "/Goalify/Img/default_profile.png");
        }

        
        fetch("profileHandler.php", {
            method: "POST",
            body: form
        })
        .then(response => response.text())
        .then(data => {
            console.log("raw response: ", data);
            return JSON.parse(data);
        })
        .then(parsed_data => {
            console.log("parsed: ", parsed_data);
            alert("Information saved successfully.");
        })
        .catch(error => console.error("error: ", error));
 });    

    // ------------------------ change password ---------------------------
    
    const change_password = document.getElementById("password");
    change_password.addEventListener("click", () => {
        createOverlay (
            'change_password_page',
            `
            <div class = "insert-container">
                <label for="new_password"><strong>New password</strong></label>
                <input type="password" name="new_password" id="new_password" required>
                <br>
                <label for="confirm_password"><strong>Confirm password</strong></label>
                <input type="password" name="confirm_password" id="confirm_password" required</input>
                
                <div class="button">
                    <button type="button" id="close-change-password-page">Back</button>
                    <button type="submit" id="save-password">Save</button>
                </div>
            </div>
            `
        );

        const close_change_password_page = document.getElementById("close-change-password-page");
        const password_form = document.getElementById("change_password_page");

        close_change_password_page.addEventListener("click", () => {
            removeOverlay("change_password_page");
        })

        password_form.addEventListener("submit", (event) => {
            event.preventDefault();

            const new_password = document.getElementById("new_password").value;
            const password = document.getElementById("new_password");
            const confirm_password = document.getElementById("confirm_password").value;

            let firstInvalidInput = null;
            
            if (!new_password.trim()) {
                firstInvalidInput = firstInvalidInput || password;
            } else if (!password_pattern.test(new_password)) {
                password.setCustomValidity("Password must have at least 8 characters, including 1 uppercase, 1 lowercase, 1 number, and 1 special character.");
                isValid = false;
                firstInvalidInput = firstInvalidInput || password;
            } else {
                password.setCustomValidity("");
                if (new_password !== confirm_password) {
                    alert("Password do not match !");
                    return;
                }
            }           
            
            const form = new FormData();
            form.append("new_password", new_password);
            form.append("confirm_password", confirm_password);

            fetch("profileHandler.php", {
                method: "POST",
                body: form
            })
            .then(response => response.text())
            .then(data => {
                console.log("raw response: ", data);
                return JSON.parse(data);
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    removeOverlay("change_password_page");
                }else {
                    alert("Error: " + data.error);
                }
                
            })
            .catch(error => console.error("Error: ", error));
        });


    });

    // ------------------------ display data ---------------------------

    const workspace = document.getElementById("workspace-enrolled");
    const project = document.getElementById("project-enrolled");

    fetch("profileHandler.php")
    .then(response => response.text())
    .then(data => {
        console.log(data);
        return JSON.parse(data);
    })
    .then(data => {
        if (data.workspace_count !== undefined) {
            workspace.textContent = `Workspaces owned: ${data.workspace_count}`;
        } else {
            console.error("Error:", data.error);
        }    
        
        if (data.project_count !== undefined) {
            project.textContent = `Projects owned: ${data.project_count}`;
        } else {
            console.error("Error:", data.error);
        }

    })
    .catch(error => console.error("Fetch error:", error));

    
    // ------------------------ display data ---------------------------
        
    fetch('profileHandler.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error:", data.error);
        } else {
            if (data.user) {
                document.querySelector("#username input").value = data.user.name;
                document.getElementById("display_id").value = data.user.userId;
                document.getElementById("email").value = data.user.email;
                document.querySelector("#user-profile-pic img").src = data.user.profile_img || "/Goalify/Img/default_profile.png";
                document.querySelector(".profile-pic img").src = data.user.profile_img || "/Goalify/Img/default_profile.png";
            }
        }
    })
    .catch(error => console.error("Fetch Error:", error));
    

    // ------------------------ log out ---------------------------

    document.getElementById("logout").addEventListener("click", () => {
  
        window.location.href = "../../includes/logout.php";
    })

});