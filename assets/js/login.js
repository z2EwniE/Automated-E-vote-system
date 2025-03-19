$(function() {

    var notyf = new Notyf({duration: 1000, position: {x: 'right', y: 'top',}});

    $("#login_form").on("submit", function(e){

        e.preventDefault();


        let username = $("#username").val();
        let password = $("#password").val();

        var remember = ($('#remember').is(':checked')) ? 1 : 0;

        if(username.trim() === "" && password.trim() === ""){
            notyf.error("All fields are required");
            return;
        }

        if (username.trim() === ""){
            notyf.error("Username is required");
            return;
        }

        if(password.trim() === ""){
            notyf.error("Password is required");
            return;
        }

        password = CryptoJS.SHA512(password).toString();

        $.ajax({
            url: 'sendData',
            type: "POST",
            data: {
                action: 'userLogin',
                username: username,
                password: password,
                remember: remember,
                token: $("#token").val()
            },
            beforeSend: function(){
                $("#login_button").html('<i class="fa fa-spinner spin"></i> Logging in... &nbsp;');
            },
            success: function(response){
                console.log(response);


                var res = JSON.parse(response);


                if(res.success){
                    if(res.tfa){
                        notyf.success(res.message)
                        setTimeout(() => {
                            window.location = 'challenge.php';
                        }, 3000);
                    } else {
                        notyf.success(res.message)
                        setTimeout(() => {
                            window.location = 'index.php';
                        }, 3000);
                    }
                } else {
                    notyf.error(res.message)
                }

                $("#login_button").html('Log in&nbsp;');

            }
        })

        });

    $("#tfa_form").on('submit', function(e){
        e.preventDefault();

        let code = $("#code").val();
        let token = $("#token").val();

        if(code.trim() === ""){
            notyf.error("Please enter code");
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'sendData',
            data: {
                action: 'tfaChallenge',
                code: code
            },
            beforeSend: function(){
                $('#challenge_btn').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Submitting Code...')
            },
            success: function (response) {
                var res = JSON.parse(response);
                if (res.success) {
                    notyf.success(res.message);
                    setTimeout('window.location = "admin/index.html`";', 3000);
                } else {
                    notyf.error(res.message);
                    $('#challenge_btn').html('&nbsp; Submit Code');
                }
              
            }
        });

    });


    $("#forgotPasswordBtn").on('click',function(e){
        e.preventDefault()

        let email = $("#forgotPasswordEmail").val();

        if(email.trim() === ""){
            notyf.error("Please enter your email");
            return;
        }

        $.ajax({
            url: 'sendData',
            type: "POST",
            data: {
                action: 'sendResetCode',
                email: email
            },
            beforeSend: function(){
                $("#forgotPasswordBtn").html('<i class="fa fa-spinner spin"></i> Resetting Password... &nbsp;');
            }, success: function(response){

                console.log(response);

                var res = JSON.parse(response);


                if(res.success){
                    notyf.success(res.message)
                    setTimeout(() => {
                        window.location = 'login.php';
                    }, 3000);
                } else {
                    notyf.error(res.message)
                }

                $("#forgotPasswordBtn").html('<i class="fa fa-spinner spin"></i> Reset Password&nbsp;');
            }
        })

    });


});

    // Function to toggle password visibility
    document.addEventListener("DOMContentLoaded", function() {
        var passwordField = document.querySelector('input[data-eye]');
        var togglePassword = document.createElement('span');
        togglePassword.innerHTML = '👁️';
        togglePassword.style.cursor = 'pointer';
        togglePassword.style.position = 'absolute';
        togglePassword.style.right = '10px';
        togglePassword.style.top = '10px';
        
        // Append toggle button next to the password field
        passwordField.parentNode.style.position = 'relative';
        passwordField.parentNode.appendChild(togglePassword);

        togglePassword.addEventListener('click', function() {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.innerHTML = '🙈';
            } else {
                passwordField.type = "password";
                togglePassword.innerHTML = '👁️';
            }
        });
    });

