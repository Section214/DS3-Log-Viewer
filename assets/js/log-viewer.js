/*global jQuery, document, window, CodeMirror*/
jQuery(document).ready(function ($) {
    'use strict';

    var textArea = document.getElementById('log-viewer');
    CodeMirror.fromTextArea(textArea, {
        theme: 'eclipse',
        lineNumbers: true,
        readOnly: true,
        autofocus: true
    });

    $('.sk-fading-circle').remove();

    $('input[name="log"]').change(function () {
        var logFile = $(this).val();
        var url = $.queryString.update(window.location.href, {
            action: null,
            log: logFile
        });

        window.location.replace(url);
    });

    $('a[name="clear"]').confirm({
        title: 'Are you sure?',
        content: 'Clearing a log file can not be undone!',
        confirmButton: 'Clear',
        confirmButtonClass: 'btn-danger',
        cancelButton: 'Cancel',
        cancelButtonClass: 'btn-info',
        confirm: function () {
            var url = $.queryString.update(window.location.href, {
                action: 'delete'
            })

            window.location.replace(url);
        }
    });

    $('a[name="reload"]').click(function () {
        var url = $.queryString.update(window.location.href, {
            action: null
        });

        window.location.replace(url);
    });
});