var Tech506 = Tech506 || {};

Tech506.Reports = {
    DailySchedule: {
        init: function() {
            $("#visitDate").datepicker({
                format: "dd/mm/yyyy",
                todayBtn: true,
                language: "es",
                todayHighlight: true,
                autoclose: true
            });
        }
    },
    List: {
        init : function() {
            $('#btnPrint').click(function(event){
                $("#report").printElement({printMode:'popup', pageTitle:$(this).attr('rel')});
            });

            $("#activityTypeId").change(function(event){
                event.preventDefault();
                $('#activitiesOptions').append('<option value="-1">Seleccione</option>');
                Tecnotek.Reports.loadGroupActivities($(this).val());
            });
            $("#activitiesOptions").change(function(event){
                event.preventDefault();
                Tecnotek.Reports.loadGroupPatients($("#activityTypeId").val(), $(this).val());
            });
        }
        /*initButtons : function() {
            alert("llego");
            $('#btnPrint').click(function(event){

                $("#report").printElement({printMode:'popup', pageTitle:$(this).attr('rel')});

            })
        }*/

    },
    /*loadGroupActivities: function($activityTypeId) {
        console.debug("Load groups of activity: " + $activityTypeId);
        if(($activityTypeId!==null)){
            $('#activitiesOptions').children().remove();
            //$('#subentryFormParent').empty();
            //$('#tableContainer').hide();
             Tecnotek.ajaxCall(Tecnotek.UI.urls["loadGroupsOfActivity"],
                {   activityTypeId: $activityTypeId },
                function(data){
                    console.debug("Total: " + data.activities.length);
                    if(data.error === true) {
                        Tecnotek.showErrorMessage(data.message,true, "", false);
                    } else {
                        for(i=0; i<data.activities.length; i++) {
                            $('#activitiesOptions').append('<option value="' + data.activities[i].id + '">' + data.activities[i].name + '</option>');
                        }
                    }
                },
                function(jqXHR, textStatus){
                    Tecnotek.showErrorMessage("Error getting data: " + textStatus + ".", true, "", false);
                    $(this).val("");
                }, true);
        }
    }*/
    loadGroupActivities: function($entity) {
        console.debug("Load groups of activity: " + $entity);
        if(($entity!==null || $entity!= '-1')){
            $('#activitiesOptions').children().remove();
            $('#activitiesOptions').append('<option value="-1">Seleccione</option>');
             //$('#patientsTable').empty();
             Tecnotek.ajaxCall(Tecnotek.UI.urls["loadGroupsOfActivity"],
                {   entity: $entity },
                function(data){
                     console.debug("Total: " + data.activities.length);
                     if(data.error === true) {
                     Tecnotek.showErrorMessage(data.message,true, "", false);
                     } else {
                     for(i=0; i<data.activities.length; i++) {
                     $('#activitiesOptions').append('<option value="' + data.activities[i].id + '">' + data.activities[i].name + '</option>');
                     }
                     }
                 },
                 function(jqXHR, textStatus){
                     Tecnotek.showErrorMessage("Error getting data: " + textStatus + ".", true, "", false);
                     $(this).val("");
                 }, true);
        }
    },
    loadGroupPatients: function($entity, $activityId) {
        console.debug("Load groups of patients: " + $entity +"-"+$activityId);
        if(($entity!==null || $entity!= '-1' || $activityId!= '-1')){
            //$('#activitiesOptions').children().remove();
            $('#patientsTable').empty();
            $('#patientsTable').append('<tr><td style="float: left;  width:120px;">Identificación</td><td style="float: left;  width:300px;">Nombre</td></tr>');
            $('#activityLabel').empty();
            $('#activityTypeLabel').empty();
            $('#activityLabel').append($('#activityTypeId :selected').text());
            $('#activityTypeLabel').append($('#activitiesOptions :selected').text());
            //$('#tableContainer').hide();
            console.debug("Ids: " + $entity +"- " + $activityId);
            Tecnotek.ajaxCall(Tecnotek.UI.urls["loadGroupsOfPatients"],
                {   entity: $entity, activityId: $activityId  },
                function(data){
                    console.debug("Total: " + data.patients.length);
                    if(data.error === true) {
                        Tecnotek.showErrorMessage(data.message,true, "", false);
                    } else {
                        for(i=0; i<data.patients.length; i++) {
                            $('#patientsTable').append('<tr><td style="float: left;  width:120px;">' + data.patients[i].id + '</td><td style="float: left;  width:300px;">' + data.patients[i].name + '</td></tr>');
                        }
                    }
                },
                function(jqXHR, textStatus){
                    Tecnotek.showErrorMessage("Error getting data: " + textStatus + ".", true, "", false);
                    $(this).val("");
                }, true);
        }
    }
};