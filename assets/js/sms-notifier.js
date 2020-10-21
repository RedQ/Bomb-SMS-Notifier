"use strict";
jQuery(document).ready(function ($) {
	init();
	function init() {
		showGatewayDynamicFields();
		sendQuickSms();
	}

	/**
	 * Show and hide provider input fields
	 * based on gateway dropdown value
	 */
	function showGatewayDynamicFields() {
		$("#gateway").on("change", function (e) {
			const provider = e.target.value;
			$(".la-input-group").each(function (index, element) {
				const elementProvider = $(element).data("id");
				if (
					provider === elementProvider ||
					elementProvider === "gateway" ||
					elementProvider === "general"
				) {
					$(element).show();
				} else {
					$(element).hide();
				}
			});
		});
	}

	/**
	 * Quick SMS send functionality
	 */
	function sendQuickSms() {
		console.log("here");
		$("#send-message").on("click", function (e) {
			console.log(e.target.id, "what id");
			var phoneNumber = $("#quick-sms-number").val();
			var message = $("#message-text").val();
			console.log(phoneNumber, message, "whats up");
			if (phoneNumber && message) {
        $("#send-message").addClass('has-spinner');
				$.ajax({
					type: "post",
					dataType: "json",
					url: SMS_NOTIFIER_DATA.ajaxUrl,
					data: {
						action: SMS_NOTIFIER_DATA.action,
						action_type: "quick_sms",
						nonce: SMS_NOTIFIER_DATA.nonce,
						phone_number: phoneNumber,
						message,
					},
					success: function (response) {
            console.log(response, "what success");
            $("#send-message").removeClass('has-spinner');
					},
					error: function (request, status, error) {
            console.log(error, "what error");
            $("#send-message").removeClass('has-spinner');
					},
				});
			}
		});
	}
});
