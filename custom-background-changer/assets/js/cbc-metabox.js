/*
 * Custom Background Changer WP Plugin v4.1.0
 * Admin Metabox JavaScript
 */
jQuery(document).ready(function ($) {

    /* =============================================
       TABS
       ============================================= */
    var $nav   = $('.cbc-tabs-nav li');
    var $panels = $('.cbc-tab-panel');

    $nav.on('click', 'a', function (e) {
        e.preventDefault();
        var target = $(this).data('tab');
        $nav.removeClass('active');
        $(this).parent().addClass('active');
        $panels.removeClass('active');
        $('#' + target).addClass('active');
    });

    /* =============================================
       COLOR PICKERS
       ============================================= */
    // Background color & gradient color 2
    $('#cbc-bgcolor').wpColorPicker({
        change: function () { setTimeout(cbcUpdateGradientPreview, 50); },
        clear: function ()  { setTimeout(cbcUpdateGradientPreview, 50); }
    });

    $('#cbc-gradient-color2').wpColorPicker({
        change: function () { setTimeout(cbcUpdateGradientPreview, 50); },
        clear: function ()  { setTimeout(cbcUpdateGradientPreview, 50); }
    });

    // Overlay color picker
    $('#cbc-overlay-color').wpColorPicker();

    /* =============================================
       GRADIENT PREVIEW
       ============================================= */
    function cbcUpdateGradientPreview() {
        var c1 = $('#cbc-bgcolor').val()   || '#667eea';
        var c2 = $('#cbc-gradient-color2').val() || c1;
        var angle = $('#cbc-gradient-angle').val() || '135';
        $('.cbc-gradient-preview').css('background', 'linear-gradient(' + angle + 'deg, ' + c1 + ', ' + c2 + ')');
    }

    $('#cbc-gradient-angle').on('input change', cbcUpdateGradientPreview);
    cbcUpdateGradientPreview();

    /* =============================================
       OVERLAY OPACITY SLIDER DISPLAY
       ============================================= */
    var $opacityInput   = $('#cbc-overlay-opacity');
    var $opacityDisplay = $('.cbc-opacity-display');

    function cbcUpdateOpacity() {
        $opacityDisplay.text(parseFloat($opacityInput.val()).toFixed(1));
    }

    $opacityInput.on('input change', cbcUpdateOpacity);
    cbcUpdateOpacity();

    /* =============================================
       BACKGROUND IMAGE UPLOADER
       ============================================= */
    var meta_image_frame;

    $('#cbc-bgimage-button').on('click', function (e) {
        e.preventDefault();

        if (meta_image_frame) {
            meta_image_frame.open();
            return;
        }

        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title:    meta_image.title,
            button:   { text: meta_image.button },
            library:  { type: 'image' },
            multiple: false
        });

        meta_image_frame.on('select', function () {
            var attachment = meta_image_frame.state().get('selection').first().toJSON();
            $('#cbc-bgimage').val(attachment.url);
        });

        meta_image_frame.open();
    });

    /* =============================================
       VIDEO FILE UPLOADER
       ============================================= */
    var video_frame;

    $('#cbc-bgvideo-button').on('click', function (e) {
        e.preventDefault();

        if (video_frame) {
            video_frame.open();
            return;
        }

        video_frame = wp.media.frames.video_frame = wp.media({
            title:    'Choose a Background Video',
            button:   { text: 'Use this video' },
            library:  { type: 'video' },
            multiple: false
        });

        video_frame.on('select', function () {
            var attachment = video_frame.state().get('selection').first().toJSON();
            $('#cbc-bgvideo').val(attachment.url);
        });

        video_frame.open();
    });

});