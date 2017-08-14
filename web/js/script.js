var currentEvent = null;
var global;
$(document).ready(function() {
    $('#login_form').on('submit', function(e) {
        e.preventDefault();
        var $self = $(this);
        $.ajax({
            url: $self.attr('action'),
            type: $self.attr('method'),
            data: $self.serialize(),
            success: function(response, status, object) {
                if(response.status) {
                    $('#login_dropdown').addClass('hidden');
                    $('#logged_user').removeClass('hidden');
                    getUserCalendar();
                } else {
                    $('#login_form_error').text(response.description);
                }
            }
        });
    });

    $('#add_new_event').on('submit', function(e) {
        e.preventDefault();
        var $self = $(this);

        $.ajax({
            url: $self.attr('action'),
            type: $self.attr('method'),
            data: $self.serialize(),
            success: function(response) {
                if(response.status) {
                    $self[0].reset();
                    $('#add_event_modal').modal('hide');
                    $('#add_event_modal').one('hidden.bs.modal', function() {
                        setTimeout(function() {
                            getUserCalendar();
                        }, 2000);
                    });
                }
            }
        });
    });

    $('#edit_event_button').on('click', function(e) {
        e.preventDefault();
        $('#event_details').modal('hide');

        $('#event_details').one('hidden.bs.modal', function(e) {

            var $form = $('#add_new_event');
            $form.find('#event_id').val(currentEvent.id);
            $form.find('#event_title').val(currentEvent.title);
            $form.find('#event_description').val(currentEvent.description);
            $form.find('#event_starts_at').val(currentEvent.start.format('Y-MM-DD') + 'T' + currentEvent.start.format('HH:mm'));
            $form.find('#event_ends_at').val(currentEvent.end.format('Y-MM-DD') + 'T' + currentEvent.end.format('HH:mm'));
            $form.find('#event_color').val(currentEvent.color);

            $('#add_event_modal').modal();
        });
    })
});

function getUserCalendar() {
    $.ajax({
        url: '/get-user-credentials',
        success: function(resp) {
            if(resp.status) {
                $('#container').append(resp.calendar);
                $('#login_dropdown').before(resp.user_info_view);

                $('#calendar').fullCalendar({

                });
            }
        }
    });
}