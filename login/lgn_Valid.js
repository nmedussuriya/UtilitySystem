document.getElementById("login_form").addEventListener("submit", function(event) {
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("types").value.trim();

    if(username === "") {
        alert("Please enter your username.");
        event.preventDefault();
        return;
    }

    if(role === "") {
        alert("Please choose a User Type.");
        event.preventDefault(); 
        return;
    }

    function validatePassword(password) {
        const minLength = 8;
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[!@#$%^&*]/.test(password);

        if(password.length < minLength) return "Password must be at least 8 characters long.";
        if(!hasNumber) return "Password must contain at least one number.";
        if(!hasSpecialChar) return "Password must contain at least one special character (!@#$%^&*).";
        return "";
    }

    const passwordError = validatePassword(password);
    if(passwordError !== ""){
        alert(passwordError);
        event.preventDefault(); 
        return;
    }

});
