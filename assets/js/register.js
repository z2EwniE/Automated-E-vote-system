$(document).ready(function() {
    const notyf = new Notyf({
        duration: 3000,
        position: {
            x: 'right',
            y: 'top',
        }
    });

    $('#register_form').on('submit', function(e) {
        e.preventDefault();

        const username = $('#reg_username').val();
        const email = $('#reg_email').val();
        let password = $('#reg_password').val();
        let confirm_password = $('#reg_confirm_password').val();
        const captcha = $('#captcha').val();
        const token = $('#register_token').val();

        // Basic validation
        if (!username || !email || !password || !confirm_password || !captcha) {
            notyf.error('All fields are required');
            return;
        }

        if (!validateEmail(email)) {
            notyf.error('Please enter a valid email address');
            return;
        }

        if (password !== confirm_password) {
            notyf.error('Passwords do not match');
            return;
        }

        // Hash passwords before sending
        password = CryptoJS.SHA512(password).toString();
        confirm_password = CryptoJS.SHA512(confirm_password).toString();

        $.ajax({
            url: '../ajax/register.php',
            type: 'POST',
            data: {
                username: username,
                email: email,
                password: password,
                confirm_password: confirm_password,
                captcha: captcha,
                token: token
            },
            success: function(response) {
                const data = JSON.parse(response);
                
                if (data.success) {
                    notyf.success(data.message);
                    $('#registerModal').modal('hide');
                    $('#register_form')[0].reset();
                } else {
                    notyf.error(data.message);
                }
            },
            error: function() {
                notyf.error('An error occurred. Please try again.');
            }
        });
    });
});

const validateEmail = (email) => {
    return email.match(
        /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
};


