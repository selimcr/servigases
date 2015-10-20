var Tech506 = Tech506 || {};

Tech506.Sellers = {
    List: {
        yesNoFormat: function(value, row, index) {
            return [
                (value)? 'Si':'No',
            ].join('');
        },
        operateFormatter: function(value, row, index){
            return [
                '<a class="edit" href="javascript:void(0)" title="Editar">',
                '<i class="glyphicon glyphicon-edit"></i>',
                '</a>',
                //'<a class="delete" href="javascript:void(0)" title="Eliminar">',
                //'<i class="glyphicon glyphicon-remove"></i>',
                //'</a>'
            ].join('');
        },
        operateEvents: {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .delete': function (e, value, row, index) {
                if(Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm-delete'])){
                    Tech506.showPleaseWait();
                    Tech506.ajaxCall(Tech506.UI.urls['delete'], {
                            id:     row.id
                        },
                        function(data){
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                            Tech506.hidePleaseWait();
                        }, true);
                }
            },
            'click .edit': function(e, value, row, index) {
                //$("#panel-catalog").addClass("hidden");
                Tech506.UI.vars["id"] = row.id;
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#username").val(row.username);
                $("#name").val(row.name);
                $("#lastname").val(row.lastname);
                $("#email").val(row.email);
                $("#cellPhone").val(row.cellPhone);

                $("#homePhone").val(row.homePhone);
                $("#address").val(row.address);
                $("#birthPlace").val(row.birthPlace);
                $("#identification").val(row.identification);
                $("#identificationType").val(row.identificationType);
                $("#neighborhood").val(row.neighborhood);
                $("#birthDate").val(row.birthDate);
                $("#rh").val(row.rh);
                $("#maritalStatus").val(row.maritalStatus);
                $("#gender").val(row.gender);
                if(row.isActive) {
                    $("#isActive").attr("checked", "checked");
                } else {
                    $("#isActive").removeAttr("checked");
                }
                var avatarPicture = row.picture;
                if(avatarPicture != null && avatarPicture != "null" && avatarPicture != "") {
                    avatarPicture = Tech506.UI.vars['avatars-path'] + avatarPicture;
                } else {
                    avatarPicture = Tech506.UI.vars['default-avatar-url'];
                }
                $('#avatar').fileinput('refresh',
                    {defaultPreviewContent: '<img id="user-avatar-img" src="' + avatarPicture + '"  style="max-width: 200px;">'});
                Tech506.UI.vars["new-avatar"] = false;
                $("#vinculationDate").val(row.vinculationDate);
                $("#vinculationEndingDate").val(row.vinculationEndingDate);
                $("#lastUniformDate").val(row.lastUniformDate);
                $("#shirtSize").val(row.shirtSize);
                $("#pantsSize").val(row.pantsSize);
                $("#shoesSize").val(row.shoesSize);
                $("#motorcycleLicensePlate").val(row.motorcycleLicensePlate);
                $("#observations").val(row.observations);

                $("#eps").val(row.eps);
                $("#arl").val(row.arl);
                $("#compensationFund").val(row.compensationFund);
                $("#pensionFund").val(row.pensionFund);

                //$("#panel-catalog-title").html(Tech506.UI.translates["edit"]);
                $('#form-content').modal('show');
                //$("#panel-catalog").removeClass("hidden");
            }
        },
        init: function(){
            Tech506.UI.vars["new-avatar"] = false;
            var btnCust = '<button type="button" class="btn btn-default" title="Add picture tags" ' +
                'onclick="alert(\'Call your custom code here.\')">' +
                '<i class="glyphicon glyphicon-tag"></i>' +
                '</button>';

            $("#avatar").fileinput({
                overwriteInitial: true,
                maxFileSize: 1500,
                showClose: false,
                showCaption: false,
                browseLabel: '',
                removeLabel: '',
                browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
                removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
                removeTitle: 'Cancel or reset changes',
                elErrorContainer: '#kv-avatar-errors',
                msgErrorClass: 'alert alert-block alert-danger',
                defaultPreviewContent: '<img src="' + Tech506.UI.vars['default-avatar-url'] + '" alt="Your Avatar"  style="max-width: 200px;">',
                layoutTemplates: {main2: '{preview} {remove} {browse}'},
                allowedFileExtensions: ["jpg", "png", "gif"],
                uploadUrl: Tech506.UI.urls['saveAvatar'],
                uploadExtraData: function () {
                    return {
                        "user_id": Tech506.UI.vars["id"]
                    };
                }
            });
            $('#avatar').on('filebatchuploadcomplete', function(event, files, extra) {
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#catalog-list").bootstrapTable('refresh');
                $('#form-content').modal('hide');
                Tech506.showInfoMessage(Tech506.UI.vars["msg"]);
            });
            $('#avatar').on('fileimageloaded', function(event, previewId) {
                Tech506.UI.vars["new-avatar"] = true;
            });

            $("#btn-new").click(function(e){
                $("#username").val("");
                $("#name").val("");
                $("#lastname").val("");
                $("#email").val("");
                $("#cellPhone").val("");
                $("#isActive").attr("checked", "checked");
                $("#homePhone").val("");
                $("#address").val("");
                $("#birthPlace").val("");
                $("#identification").val("");
                $("#identificationType").val("");
                $("#neighborhood").val("");
                $("#birthDate").val("");
                $("#rh").val("");
                $("#maritalStatus").val(0);
                $("#gender").val(0);
                var avatarPicture = Tech506.UI.vars['default-avatar-url'];
                $('#avatar').fileinput('refresh',
                    {defaultPreviewContent: '<img id="user-avatar-img" src="' + avatarPicture + '"  style="max-width: 200px;">'});
                Tech506.UI.vars["new-avatar"] = false;

                $("#vinculationDate").val("");
                $("#vinculationEndingDate").val("");
                $("#lastUniformDate").val("");
                $("#shirtSize").val("");
                $("#pantsSize").val("");
                $("#shoesSize").val("");
                $("#motorcycleLicensePlate").val("");
                $("#observations").val("");

                $("#eps").val("");
                $("#arl").val("");
                $("#compensationFund").val("");
                $("#pensionFund").val("");

                Tech506.UI.vars["id"] = 0;
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog-title").html($(this).attr("title"));
            });

            $("#btn-cancel").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#panel-catalog").addClass("hidden");
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
            });

            $('#form-catalog').bootstrapValidator({
                excluded: ':disabled',
                message: Tech506.UI.translates['invalid.value'],
                feedbackIcons: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                onSuccess: function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    Tech506.showPleaseWait();
                    $("#id").val(Tech506.UI.vars["id"]);
                    $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: Tech506.UI.urls['save'],
                        data: $("#form-catalog").serialize(), // serializes the form's elements.
                        success: function(data) {
                            Tech506.hidePleaseWait();
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                Tech506.UI.vars["id"] = data.userId;
                                if(Tech506.UI.vars["new-avatar"]) {
                                    Tech506.UI.vars["msg"] = data.msg;
                                    $('#avatar').fileinput('upload');
                                } else {
                                    $("#panel-catalog").addClass("hidden");
                                    $('#form-catalog').data('bootstrapValidator').resetForm(true);
                                    $("#catalog-list").bootstrapTable('refresh');
                                    $('#form-content').modal('hide');
                                    Tech506.showInfoMessage(data.msg);
                                }
                            }
                        }
                    });
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
                    'username': {
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
                    },
                    'identification': {
                        validators: {
                            notEmpty: {
                                message: Tech506.UI.translates['field.not.empty']
                            }
                        }
                    }
                }
            });
        }
    }
};