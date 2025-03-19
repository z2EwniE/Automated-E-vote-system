$(document).ready(function(e) {
	var notyf = new Notyf({
		duration: 1000,
		position: {
			x: 'right',
			y: 'top',
		}
	});

	$('#verifyPassBtn').on('click', function() {
		var password = $('#2fapasswordverify').val();
		var setup = new bootstrap.Modal(document.getElementById('tfaSetup'));
		var validatePass = new bootstrap.Modal(document.getElementById('validatePass'));

		validatePass.hide();
		password = CryptoJS.SHA512(password).toString();

		$.ajax({
			type: 'POST',
			url: 'sendData',
			data: {
				action: 'verifyUser',
				password: password,
			},
			beforeSend: function() {
				$('#verifyPassBtn').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Wait...')
			},

			success: function(response) {
				var res = JSON.parse(response);
				if (res.success) {

					$.ajax({
						type: 'POST',
						url: 'sendData',
						data: {
							action: 'requestTfa'
						},
						success: function(response) {
							var res = JSON.parse(response);
							$("#secret_key").val(res.secret);
							$("#secretKey").text(res.secret);
							$('#tfaQRCode').attr("src", res.qr_code);
						}
					});

					setup.show();

				} else {
					notyf.error(res.message)
					validatePass.show();
					$('#verifyPassBtn').html('&nbsp; Confirm')

				}
			}
		});
	});

	$('#confirmSetup').on('click', function() {
		var setup = new bootstrap.Modal(document.getElementById('tfaSetup'));

		var code = $('#6digitcode').val();
		var secretKey = $('#secret_key').val();

		$.ajax({
			type: 'POST',
			url: 'sendData',
			data: {
				action: 'verifySecretKey',
				code: code,
				secret_key: secretKey
			},
			beforeSend: function() {
				$('#confirmSetup').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Wait...')
			},
			success: function(response) {
				var res = JSON.parse(response);
				if (res.success) {
					$('#confirmSetup').html('&nbsp; Confirm');
					notyf.success(res.message);
					setup.hide();
					setTimeout(window.location.reload.bind(window.location), 3000);
				} else {
					notyf.error(res.message);
					$('#confirmSetup').html('&nbsp; Confirm')
				}
				$('#confirmSetup').html('&nbsp; Confirm')
			}
		})

	});

	$('#disable2faBtn').on('click', function() {
		var password = $('#2fadisablepassword').val();

		var modal = new bootstrap.Modal(document.getElementById('disable2fa'));


		password = CryptoJS.SHA512(password).toString();

		$.ajax({
			type: 'POST',
			url: 'sendData',
			data: {
				action: 'verifyUser',
				password: password,
			},
			beforeSend: function() {
				$('#disable2faBtn').html('<i class="fa fa-spinner fa-spin"></i> &nbsp; Wait...')
			},
			success: function(response) {
				var res = JSON.parse(response);
				if (res.success) {


					$.ajax({
						type: 'POST',
						url: 'sendData',
						data: {
							action: 'disableTfa'
						},
						success: function(response) {
							var res = JSON.parse(response);
							if (res.success) {
								notyf.success(res.message);
								setTimeout(window.location.reload.bind(window.location), 3000);
							} else {
								notyf.error(res.message)
							}
						}
					});

				} else {
					notyf.error(res.message);
					$('#disable2faBtn').html('&nbsp; Turn off')
					modal.show();
				}
			}
		});
	});



});