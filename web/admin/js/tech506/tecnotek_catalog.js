var Tech506 = Tech506 || {};

Tech506.Catalog = {
    List: {
        catalogTableParams: function() {
            return {
                entity: Tech506.UI.vars["entity"]
            }
        },
        operateFormatter: function(value, row, index){
            return [
                '<a class="edit" href="javascript:void(0)" title="Editar">',
                '<i class="glyphicon glyphicon-edit"></i>',
                '</a>',
                '<a class="delete" href="javascript:void(0)" title="Eliminar">',
                '<i class="glyphicon glyphicon-remove"></i>',
                '</a>'
            ].join('');
        },
        operateEvents: {
            'click .like': function (e, value, row, index) {
                alert('You click like action, row: ' + JSON.stringify(row));
            },
            'click .delete': function (e, value, row, index) {
                if(Tech506.showConfirmationQuestion(Tech506.UI.translates['confirm-delete'])){
                    Tech506.ajaxCall(Tech506.UI.urls['delete'], {
                            id:     row.id,
                            entity: Tech506.UI.vars["entity"]
                        },
                        function(data){
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                        }, true);
                }
            },
            'click .edit': function(e, value, row, index) {
                $("#panel-catalog").addClass("hidden");
                Tech506.UI.vars["id"] = row.id;
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#name").val(row.name);
                $("#panel-catalog-title").html(Tech506.UI.translates["edit"]);
                $("#panel-catalog").removeClass("hidden");
            }
        },
        init: function(){
            $("#btn-new").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $("#name").val("");
                Tech506.UI.vars["id"] = 0;
                $("#panel-catalog-title").html($(this).attr("title"));
                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                $("#panel-catalog").removeClass("hidden");
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
                    var $name = $("#name").val();
                    Tech506.ajaxCall(Tech506.UI.urls['save'], {
                            id:     Tech506.UI.vars["id"],
                            name:   $name,
                            entity: Tech506.UI.vars["entity"]
                        },
                        function(data){
                            if(data.error){
                                Tech506.showErrorMessage(data.msg);
                            } else {
                                $("#panel-catalog").addClass("hidden");
                                $('#form-catalog').data('bootstrapValidator').resetForm(true);
                                $("#catalog-list").bootstrapTable('refresh');
                                Tech506.showInfoMessage(data.msg);
                            }
                        }, function(){
                        }, true);
                    return false;
                },
                fields: {
                    'name': {
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