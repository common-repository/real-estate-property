jQuery(document).ready(function ($) {
    //scroll comment sectoin in chat box
    function setChatBoxHeight() {
        jQuery('.swiftChatConversion, .askQuestionContainer').css('height', jQuery(window).innerHeight());
    }
    function scrollToBottom() {
        setTimeout(function () {
            jQuery(".swiftChatConversion, .askQuestionContainer").mCustomScrollbar('scrollTo', 'bottom');
        }, 1000);
    }

    if (jQuery(window).width() > 767) {
        setChatBoxHeight();
        jQuery(window).resize(function () {
            setChatBoxHeight();
        });
    }

    jQuery(".swiftChatConversion, .askQuestionContainer").mCustomScrollbar({
        theme: "dark",
        callbacks: {
            onInit: function () {
                scrollToBottom();
            }
        }
    });

    jQuery("#scheduleDemo").click(function () {
        jQuery(".swiftChat").fadeIn();
        jQuery(".swiftChatConversion .scheduleDemoMsg").removeClass('display-none');
    });

    //scroll comment sectoin in chat box
    if (jQuery(window).width() > 767) {
        setChatBoxHeight();
        jQuery(window).resize(function () {
            setChatBoxHeight();
        });
    }

    // close chat window
    jQuery(document).on('click', ".swiftChatClose, .chatClose, .btnChatClose", function () {
        jQuery(".swiftChatConversion, .swiftCloudThemeChat").fadeOut();
    });
    function setChatBoxHeight() {
        if (jQuery('.askQuestionContainer').outerHeight() + 150 > jQuery(window).height() && !jQuery('.askQuestionContainer').hasClass('activeScrollbar')) {
            jQuery('.askQuestionContainer').css('height', jQuery(window).height() - jQuery('.askQuestionContainer .swiftChatReplayBox').outerHeight() - 100);
            jQuery('.askQuestionContainer').addClass('activeScrollbar');
        }

        jQuery(".askQuestionContainer").mCustomScrollbar('update');
        jQuery(".askQuestionContainer").mCustomScrollbar('scrollTo', 'bottom');
    }

    // ============= ASK A QUESTION =============
    jQuery("#askQusModal").click(function () {
        jQuery(".askQuestionContainer").fadeIn();
        jQuery(".askQuestionMainContainer .chatClose").show();
    });

    var chatSeqCnt = 0;
    var chatBotMsgArr = [
        // arguments
        // 1 = msg, 2 = cookie var, 3 = question types, 4 = validation
        ['Got it - I\'ll get you an answer asap. Should I...<div class="swiftChatBtnMultiCheckbox mt-2"><input type="radio" name="contact_by[]" class="swift_cw_radio labelauty" data-labelauty="text you|text you" value="sms"><input type="radio" name="contact_by[]" class="swift_cw_radio labelauty" data-labelauty="Phone|Phone" value="Phone"><input type="radio" name="contact_by[]" class="swift_cw_radio labelauty" data-labelauty="Email|Email" value="Email"><button type="button" class="btn btn-primary btnConfirmMultiCheckbox"><i class="fa fa-check"></i> Confirm</button></div>', 'checkbox', 'question', 'required'],
        ['Great. To what number?', '', 'question', 'phone'],
        ['What\'s the best email for this?', '', 'question', 'email'],
        ['Got it. Thanks! We\'ll be in touch.<br><a class="btn btn-default btnChatClose" href="javascript:;"><i class="fa fa-times"></i> Close</a>', '', 'close', ''],
    ];

    jQuery('.askQuestionContainer .sendBtn').click(function () {
        addChatMsg();
    });

    if (jQuery('.swiftChatAskQueReplay').length > 0) {
        jQuery('.swiftChatAskQueReplay').keypress(function (event) {
            event.stopPropagation();
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                addChatMsg();
            }
        });
    }

    function addChatMsg() {
        var datetime = getCurrentDateTime();
        var msg = jQuery.trim(jQuery('.askQuestionContainer .swiftChatAskQueReplay').val());
        jQuery('.chatError').remove();  // remove chat error msg

        if (msg.length <= 0) {
            return false;
        }
        // check validation
        if (jQuery('#fieldValidation').val() !== '') {
            switch (jQuery('#fieldValidation').val()) {
                case 'phone':
                case 'sms':
                    var validPhonePattern = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/;
                    if (!validPhonePattern.test(msg)) {
                        jQuery('.askQuestionContainer .swiftChatAskQueReplay').after('<span class="chatError">Please enter valid phone number</span>');
                        return false;
                    }
                    break;
                case 'email':
                    var validEmailPattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                    if (!validEmailPattern.test(msg)) {
                        jQuery('.askQuestionContainer .swiftChatAskQueReplay').after('<span class="chatError">Please enter valid email address</span>');
                        return false;
                    }
                    break;
            }
        }

        jQuery('.askQuestionContainer .swiftChatAskQueReplay').val('');
        jQuery('.askQuestionContainer .swiftChatReplayBox').before('<div class="userChat"><div class="userMsg"><div class="msgContant"><p>' + msg + '</p></div><div class="timeRow"><time class="timeago" datetime=' + datetime + '></time></div></div></div>');
        jQuery('[data-toggle="tooltip"]').tooltip();
        //        playAudio();
        setTimeout(function () {
            jQuery('.askQuestionContainer .swiftChatAskQueReplay').val('');
        }, 300);
        jQuery("time.timeago").timeago();
        setChatBoxHeight();
        jQuery(".askQuestionContainer").mCustomScrollbar('scrollTo', 'bottom');

        // ask next question
        if (jQuery('#isAnswerRequired').val() == 'Yes') {
            askNextQuestion();
        }
    }

    function askNextQuestion() {
        var datetime = getCurrentDateTime();
        var CT = jQuery.now();
        var getMsg = chatBotMsgArr[chatSeqCnt][0];

        if (typeof chatBotMsgArr[chatSeqCnt][2] == 'undefined') {
            return false;
        }

        if (chatBotMsgArr[chatSeqCnt][2] !== 'pause') {
            jQuery('.swiftChatConversion').append('<img src="' + swiftproperty_ajax_object.plugin_url + '/images/22.gif" class="chatLoading" />');
        }

        setTimeout(function () {
            var RobotChatMsg = '<div class="swiftTeamChat chatSeq_' + CT + '"><div class="sTeamMsg">';
            RobotChatMsg += '<div class="msgContent">';
            RobotChatMsg += '<div class="actualMsgContent">' + getMsg + '</div>';
            RobotChatMsg += '</div>';
            RobotChatMsg += '<div class="timeRow"><time class="timeago" datetime=' + datetime + '></time></div>';
            RobotChatMsg += '</div></div>';

            if (chatBotMsgArr[chatSeqCnt][2] == 'pause') {
                var GuestChatMsg = '<div class="swiftTeamChat pauseChatLoader chatSeq_' + CT + '"><div class="sTeamMsg"><div class="msgContent"><img src="' + swiftproperty_ajax_object.plugin_url + '/images/22.gif" /></div></div></div>';
                setTimeout(function () {
                    jQuery('.pauseChatLoader').remove();
                }, 1000);
            } else {
                var GuestChatMsg = '<div class="swiftTeamChat chatSeq_' + CT + '"><div class="sTeamAvtar tooltip-right" data-tooltip="Robot Assistant"><img src="' + swiftproperty_ajax_object.plugin_url + '/images/BotSmall.gif"></div><div class="sTeamMsg"><div class="msgContent"><p>' + getMsg + '</p></div><div class="timeRow"><time class="timeago" datetime=' + datetime + '></time></div></div></div>';
            }
            jQuery('.askQuestionContainer .swiftChatReplayBox').before(GuestChatMsg);
            jQuery('.swiftChatAskQueReplay').val('');
            jQuery('[data-toggle="tooltip"]').tooltip();
            jQuery(".swiftChatConversion").mCustomScrollbar('scrollTo', 'bottom');
            //            playAudio();
            jQuery("time.timeago").timeago();
            setChatBoxHeight();

            // if question required multi choise from user, disable chat box and button
            if (chatBotMsgArr[chatSeqCnt][1] == 'checkbox') {
                jQuery(".swift_cw_radio").labelauty();  // for circle word
                jQuery('.swiftChatAskQueReplay').attr('readonly', 'readonly');
                jQuery('.askQuestionContainer .sendBtn').attr('disabled', 'disabled');
            } else {
                jQuery('.swiftChatAskQueReplay').removeAttr('readonly');
                jQuery('.askQuestionContainer .sendBtn').removeAttr('disabled');
            }

            // if answer is required, then wait for user input
            if (chatBotMsgArr[chatSeqCnt][2] == 'question') {
                jQuery('#isAnswerRequired').val('Yes');
            } else {
                jQuery('#isAnswerRequired').val('No');
            }

            // field validation
            if (chatBotMsgArr[chatSeqCnt][3] != '') {
                jQuery('#fieldValidation').val(chatBotMsgArr[chatSeqCnt][3]);
            }

            // if that's the last question, remove reply chat box
            if (chatBotMsgArr[chatSeqCnt][2] == 'close') {
                jQuery('.swiftChatReplayBox').hide();
            }

            if (chatBotMsgArr[chatSeqCnt][2] == 'nextQue') {
                chatSeqCnt++;
                askNextQuestion();
            } else if (chatBotMsgArr[chatSeqCnt][2] == 'pause') {
                chatSeqCnt++;
                askNextQuestion();
            } else {
                chatSeqCnt++;
            }
        }, 1000);

        jQuery('.chatLoading').remove();
    }

    // radio/checkbox...
    jQuery(".swift_cw_radio").labelauty();
    jQuery(document).on('click', '.askQuestionContainer .swiftTeamChat .multiRadioContainer input[type=radio]', function () {
        var datetime = getCurrentDateTime();
        var CT = jQuery.now();
        var getSwiftCWRadio = jQuery(this).val();
        if (getSwiftCWRadio !== 'Phone' && getSwiftCWRadio !== 'sms') {
            chatSeqCnt++;
        }
        setChatBoxHeight();
        jQuery(this).parents(".multiRadioContainer").addClass('display-none');
        sendNextQuestion();
    });

    // checkbox
    jQuery(document).on('click', '.swiftChatBtnMultiCheckbox .btnConfirmMultiCheckbox', function () {
        //        var selectedChkBox = [];
        //        jQuery.each(jQuery(".swiftChatBtnMultiCheckbox input[type='radio']:checked"), function () {
        //            selectedChkBox.push(jQuery(this).val());
        //        });

        if (jQuery(".swiftChatBtnMultiCheckbox input[type='radio']:checked").val() != '') {
            var datetime = getCurrentDateTime();
            jQuery('.askQuestionContainer .swiftChatReplayBox').before('<div class="userChat"><div class="userMsg"><div class="msgContant"><p>' + jQuery(".swiftChatBtnMultiCheckbox input[type='radio']:checked").val() + '</p></div><div class="timeRow"><time class="timeago" datetime=' + datetime + '></time></div></div></div>');
            jQuery('[data-toggle="tooltip"]').tooltip();
            jQuery(".swiftChatConversion").mCustomScrollbar('scrollTo', 'bottom');
            setTimeout(function () {
                jQuery('.preLoading').addClass('display-none');
                jQuery('.preText').removeClass('display-none');
                //                playAudio();
            }, 1000);

            if (jQuery(".swiftChatBtnMultiCheckbox input[type='radio']:checked").val() == 'Email') {
                chatSeqCnt++;
            }
            jQuery(this).parents('.swiftChatBtnMultiCheckbox').remove();
            setChatBoxHeight();
            askNextQuestion();
        }


    });

    if (jQuery(".swiftChatAskQueReplay").length > 0) {
        var swiftChatAskQueReplay = document.querySelector('.swiftChatAskQueReplay');
        swiftChatAskQueReplay.addEventListener('keydown', autosize);
    }
    function autosize() {
        var el = this;
        setTimeout(function () {
            el.style.cssText = 'height:auto;';
            // for box-sizing other than "content-box" use:
            // el.style.cssText = '-moz-box-sizing:content-box';
            el.style.cssText = 'height:' + el.scrollHeight + 'px';
        }, 0);
    }
    function getTwoDigitDateFormat(monthOrDate) {
        return (monthOrDate < 10) ? '0' + monthOrDate : '' + monthOrDate;
    }
    function getCurrentDateTime() {
        var currentdate = new Date();
        var hours = currentdate.getHours();
        if (hours < 10)
            hours = '0' + hours;
        var minutes = currentdate.getMinutes();
        if (minutes < 10) {
            minutes = '0' + minutes;
        } else {
            minutes = minutes + '';
        }
        var twoDigitDate = getTwoDigitDateFormat(currentdate.getDate());
        var twoDigitMonth = getTwoDigitDateFormat(currentdate.getMonth() + 1);
        var datetime = currentdate.getFullYear() + "-"
                + twoDigitMonth + "-"
                + twoDigitDate + "T"
                + hours + ":"
                + minutes + ":"
                + currentdate.getSeconds();
        return datetime;
    }
});