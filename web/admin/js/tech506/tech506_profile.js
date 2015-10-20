var Tech506 = Tech506 || {};

Tech506.Profile = {
    init: function () {
        $("#btn-new").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            $("#name").val("");
            Tech506.UI.vars["id"] = 0;
            $("#panel-catalog-title").html($(this).attr("title"));
            $('#form-catalog').data('bootstrapValidator').resetForm(true);
            $("#panel-catalog").removeClass("hidden");
        });

        $("#btn-cancel").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            $("#panel-catalog").addClass("hidden");
            $('#form-catalog').data('bootstrapValidator').resetForm(true);
        });

        $('#form-info').bootstrapValidator({
            excluded: ':disabled',
            message: Tech506.UI.translates['invalid.value'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            onSuccess: function (e) {
                e.preventDefault();
                e.stopPropagation();
                Tech506.Profile.updateInfo();
                return false;
            },
            fields: {
                'name': {
                    validators: {
                        notEmpty: {
                            message: Tech506.UI.translates['field.not.empty']
                        }
                    }
                },
                'lastname': {
                    validators: {
                        notEmpty: {
                            message: Tech506.UI.translates['field.not.empty']
                        }
                    }
                },
                'email': {
                    validators: {
                        notEmpty: {
                            message: Tech506.UI.translates['field.not.empty']
                        }
                    }
                },
                'cellPhone': {
                    validators: {
                        notEmpty: {
                            message: Tech506.UI.translates['field.not.empty']
                        }
                    }
                }
            }
        });

        $('#form-password').bootstrapValidator({
            excluded: ':disabled',
            message: Tech506.UI.translates['invalid.value'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            onSuccess: function (e) {
                e.preventDefault();
                e.stopPropagation();
                Tech506.Profile.updatePassword();
                return false;
            },
            fields: {
                'current': {
                    validators: {
                        notEmpty: {
                            message: Tech506.UI.translates['field.not.empty']
                        }
                    }
                },
                'new': {
                    validators: {
                        notEmpty: {
                            message: Tech506.UI.translates['field.not.empty']
                        },
                        identical: {
                            field: 'confirm',
                            message: Tech506.UI.translates['password.confirm.not.same']
                        },
                        stringLength: {
                            message: Tech506.UI.translates['password.lenght.error'],
                            max: 8,
                            min: 6
                        }
                    }
                },
                'confirm': {
                    validators: {
                        identical: {
                            field: 'new',
                            message: Tech506.UI.translates['password.confirm.not.same']
                        }
                    }
                }
            }
        });
    },
    updateInfo: function () {
        var $name = $("#name").val();
        var $lastname = $("#lastname").val();
        var $email = $("#email").val();
        var $cellPhone = $("#cellPhone").val();
        Tech506.showPleaseWait();
        Tech506.ajaxCall(Tech506.UI.urls['update-account'], {
                id: Tech506.UI.vars["id"],
                name: $name,
                lastname: $lastname,
                cellPhone: $cellPhone,
                email: $email
            },
            function (data) {
                Tech506.hidePleaseWait();
                if (data.error) {
                    Tech506.showErrorMessage(data.msg);
                } else {
                    $("#account-name").html($name + " " + $lastname);
                    $("#account-email").html($email);
                    $("#account-phone").html($cellPhone);
                    $('#form-info').data('bootstrapValidator').resetForm(true);
                    $("#name").val($name);
                    $("#lastname").val($lastname);
                    $("#email").val($email);
                    $("#cellPhone").val($cellPhone);
                    Tech506.showInfoMessage(data.msg);
                }
            }, function () {
                Tech506.hidePleaseWait();
            }, true);
    },
    updatePassword: function () {
        var $current = $("#current").val();
        var $new = $("#new").val();
        Tech506.showPleaseWait();
        Tech506.ajaxCall(Tech506.UI.urls['update-password'], {
                id: Tech506.UI.vars["id"],
                current: $current,
                new: $new
            },
            function (data) {
                Tech506.hidePleaseWait();
                if (data.error) {
                    Tech506.showErrorMessage(data.msg);
                } else {
                    $('#form-password').data('bootstrapValidator').resetForm(true);
                    Tech506.showInfoMessage(data.msg);
                }
            }, function () {
                Tech506.hidePleaseWait();
            }, true);
    }
};