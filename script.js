/**
 * Attaches the mechanics on our plugin's button
 *
 * @param {jQuery} $btn the button itself
 * @param {object} props unused
 * @param {string} edid the editor's ID
 * @return {string}
 */
function addBtnActionPlugin_scrapbook($btn, props, edid) {
    var pickerid = 'picker' + (pickercounter++);
    var $picker = jQuery(createPicker(pickerid, [], edid))
            .attr('aria-hidden', 'true')
            .addClass('plugin-scrapbook')
        ;

    /**
     * Insert the scrap associated witht he clicked button
     *
     * @param {jQuery} $el the clicked button in the picker
     */
    var insertScrap = function ($el) {
        pickerInsert($el.data('scrap'), edid);
    };

    // handle click in the picker
    $picker.click(function (e) {
        if (e.target.nodeName.toLowerCase() != 'button') return;
        var $el = jQuery(e.target);
        if (!$el.data('id')) return;

        // scraps are loaded on demand, but only once
        if ($el.data('scrap')) {
            insertScrap($el);
        } else {
            jQuery.post(
                DOKU_BASE + 'doku.php',
                {
                    id: $el.data('id'),
                    do: 'export_raw',
                    scrapbookinsert: JSINFO.id
                },
                function (data) {
                    $el.data('scrap', data);
                    insertScrap($el);
                }
            );
        }

        e.preventDefault();
        pickerClose();
    });

    // when the toolbar button is clicked
    $btn.click(
        function (e) {
            // load the list of scraps if they haven't been loaded, yet
            if (!$picker.find('button').length) {
                $picker.html('<div>' + LANG.plugins.scrapbook.loading + '</div>');
                jQuery.post(
                    DOKU_BASE + 'lib/exe/ajax.php',
                    {
                        call: 'plugin_scrapbook'
                    },
                    function (data) {
                        $picker.html(data);
                    }
                )
            }

            // open/close the picker
            pickerToggle(pickerid, $btn);
            e.preventDefault();
            return '';
        }
    );

    return pickerid;
}
