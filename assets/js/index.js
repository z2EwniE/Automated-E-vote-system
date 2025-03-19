$(function() {
    let notyf = new Notyf({duration: 1000, position: {x: 'right', y: 'top',}});

    /*** Change profile  **/
    $("#changeProfileBtn").on("click", function(event){

        let avatar = $('#avatar').prop('files')[0];
        let email = $("#email-input").val();
        let first_name = $("#firstname-input").val();
        let last_name = $("#lastname-input").val();
        let birth_day = $("#birth_day").val();
        let birth_month = $("#birth_month").val();
        let birth_year = $("#birth_year").val();

        let action = "editProfile";
        let birthdate = birth_year + '-' + birth_month + '-' + birth_day;

        let data = new FormData();
        data.append('avatar', avatar);
        data.append('email', email);
        data.append('first_name', first_name);
        data.append('last_name', last_name);
        data.append('birthdate',birthdate);
        data.append('action',action);


        if (!validateEmail(email)){
            notyf.error("Email is not valid");
            return;
        }

        $.ajax({
            type: "POST",
            url: 'sendData',
            data: data,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('#changeProfileBtn').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Saving Changes');
            },
            success: function (response){

                var res = JSON.parse(response);


                if(res.success){
                    notyf.success(res.message)
                    $('#changeProfileBtn').html('&nbsp; Save Changes');
                    setTimeout(function(){
                       location.reload()
                    }, 1000);
                  
                } else {
                    notyf.error(res.message)
                    $('#changeProfileBtn').html('&nbsp; Save Changes');

                }

            }
        })

    });

    $("#changeEmailBtn").on("click", function(event){

        let email = $("#email-input").val();

        if (!validateEmail(email)){
            notyf.error("Email is not valid");
            return;
        }

        $.ajax({
            type: "POST",
            url: 'sendData',
            data: {
                action: 'changeEmail',
                email: email,
            },
            beforeSend: function(){
                $('#changeEmailBtn').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Saving Changes');
            },
            success: function (response){

                var res = JSON.parse(response);


                if(res.success){
                    notyf.success(res.message)
                    $('#changeEmailBtn').html('&nbsp; Save Changes');
                } else {
                    notyf.error(res.message)
                    $('#changeEmailBtn').html('&nbsp; Save Changes');

                }
            }

        })

    });
    
    $("#changePasswordBtn").on("click", function(e){

        let oldPassword = $("#old_password").val();
        let newPassword = $("#new_password").val();
        let confirmPassword = $("#confirm_password").val();
        
        if(oldPassword.trim() === "" || newPassword.trim() === "" || confirmPassword.trim() === ""){
            notyf.error("Field required");
            return;
        }


        oldPassword = CryptoJS.SHA512(oldPassword).toString();
        newPassword = CryptoJS.SHA512(newPassword).toString();
        confirmPassword = CryptoJS.SHA512(confirmPassword).toString();


        $.ajax({
            type: "POST",
            url: 'sendData',
            data: {
                action: 'changePassword',
                oldPassword: oldPassword,
                newPassword: newPassword,
                confirmPassword: confirmPassword
            },
            beforeSend: function (e){
                $('#changePasswordBtn').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Saving Changes');
            },
             success: function (response){

                var res = JSON.parse(response);


                if(res.success){
                    notyf.success(res.message)
                    $('#changePasswordBtn').html('&nbsp; Save Changes');
                } else {
                    notyf.error(res.message)
                    $('#changePasswordBtn').html('&nbsp; Save Changes');

                }
            }

        });



    });

    $("#confirm_button").on("click", function (e){
        e.preventDefault();

        let key = $("#confirmation_code").val();

        $.ajax({
            type: "POST",
            url: "sendData",
            data: {
                action: "confirmAccount",
                key: key
            },
            beforeSend: function (){
                $('#confirm_button').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Confirming Account ...');
            },
            success: function (response){

                var res = JSON.parse(response);


                if(res.success){
                    notyf.success(res.message)
                    $('#confirm_button').html('&nbsp; Confirm Account');

                    setTimeout(() => {
                        window.location = 'admin/index.html';
                    }, 3000);
                } else {
                    notyf.error(res.message)
                    $('#confirm_button').html('&nbsp; Confirm Account');

                }

               
            }
        })

    });

    $("#reset_pass_button").on("click", function (e){
        e.preventDefault();

        let key = $("#reset_code").val();
        let new_password = $("#new_password").val();

        if (new_password.trim() === ""){
            notyf.error("Enter your new password");
            return;
        }

        new_password = CryptoJS.SHA512(new_password).toString();

        $.ajax({
            type: "POST",
            url: "sendData",
            data: {
                action: "resetPassword",
                key: key,
                new_password: new_password
            },
            beforeSend: function (){
                $('#reset_pass_button').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Resetting Password ...');
            },
            success: function (response){

                var res = JSON.parse(response);


                if(res.success){
                    notyf.success(res.message)
                    $('#reset_pass_button').html('&nbsp; Reset Password');
                    setTimeout(() => {
                        window.location = 'login.php';
                    }, 3000);
                } else {
                    notyf.error(res.message)
                    $('#reset_pass_button').html('&nbsp; Reset Password');

                }

               
            }
        })

    })

    function updateActivity(){

        $.ajax({
            url: "sendData",
            type: "post",
            data: {action: "updateActivity", id: id},
            success: function (data){
                // console.log(data)
            }
        })
    }


    function userStatus(){
        $.ajax({
            url: "sendData",
            type: 'post',
            data: {
                action: 'checkActivity',
                id: id
            }, success: function (data){
                if (data.status == "0"){
                    $("#status").addClass("offline")
                } else {
                    $("#status").addClass("online")
                }
            }
        })
    }

    setInterval(function(){
        updateActivity();
    }, 3000);

    userStatus();
});



