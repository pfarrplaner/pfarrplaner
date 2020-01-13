function addAttachmentRow() {
    attachments++;

    $('#newAttachments').append('<div class="row form-group">' +
        '<div class="col-md-6">' +
        '<input class="form-control" type="text" name="attachment_text['+attachments+']" placeholder="Beschreibung der Datei" />' +
        '</div><div class="col-md-6">' +
        '<input class="form-control" type="file" name="attachments['+attachments+']" placeholder="Datei auswählen" />' +
        '</div>' +
        '</div>');

    $('.attachment-row').click(function(){
        window.location.href = $(this).data('route');
    });

    $('.btn-remove-attachment').click(function(){
       $('#newAttachments').append('<input type="hidden" name="remove_attachment[]" value="'+$(this).data('attachment')+'" />');
       $(this).parent().parent().remove();
    });
}

$(document).ready(function(){
    addAttachmentRow();
});
