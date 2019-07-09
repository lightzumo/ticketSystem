const loading = () => {

    //Add eventlistener to the button "ForgotPassword" and redirect the page to the forgot password page
    document.getElementById("forgotPassword").addEventListener("click", function (event) {
        event.preventDefault();

        window.location.href = "forgotPassword";
    });

    //Add eventlistener to the button "Register" and redirect the page to the register page
    document.getElementById("register").addEventListener("click", function (event) {
        event.preventDefault();

        window.location.href = "register";
    });

    //Control if user is already connected (session) if so redirects him to the home page
    let req = new XMLHttpRequest();

    req.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText === "true") {

                window.location.href = "home.php";
            }

        }
    };


    req.open("POST", 'controlSession.php', true);


    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    req.send();
};

window.addEventListener('load', loading);



