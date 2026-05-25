function login(role) {
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    fetch("login.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `role=${role}&username=${username}&password=${password}`
    })
    .then(Response => Response.text())
    .then(data => {
        data = data.trim();
        alert(data);

        if (data === "Login successful!") {
            if (role === "rider") {
                window.location.href = "Riderhome.php";
            } else {
                window.location.href = "Driverhome.php";
            }
        }
    });
}