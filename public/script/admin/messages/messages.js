function handleOptions() {
    const messageType = document.getElementById('messageType').value;
    const targetType = document.getElementById('targetType').value;
    const targetGroup = document.getElementById('targetGroup').value;

    if (messageType === 'message') {
        $('#target').show();
        $('#targetType').attr('required', true);
        if (targetType === 'single') {
            $('#recipient-email').show();
            $('#target-group').show();
            $('#recipientData').attr('required', true);
        } else if (targetType === 'broadcast') {
            $('#recipient-email').hide();
            $('#target-group').show();
            $('#recipientData').removeAttr('required');
        } else {
            $('#recipient-email').hide();
            $('#target-group').hide();
        }
    } else if (messageType === 'popup') {
        $('#target').show();
        $('#targetType').attr('required', true);
        if (targetType === 'single') {
            $('#recipient-email').show();
            $('#target-group').show();
            $('#recipientData').attr('required', true);
        } else if (targetType === 'broadcast') {
            $('#recipient-email').hide();
            $('#target-group').show();
        } else {
            $('#recipient-email').hide();
            $('#target-group').hide();
        }
    } else {
        $('#target').hide();
        $('#recipient-email').hide();
        $('#target-group').hide();
    }
}
