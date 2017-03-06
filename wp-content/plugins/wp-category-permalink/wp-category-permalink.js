(function($) {
    'use strict';

    $.fn.sCategoryPermalink = function(options) {
      var me = this;
      this.find('.selectit').append(
        '<span style="display: none; font-size: 11px; font-weight: normal !important; margin-left: 5px; top: -2px; position: relative;" class="taxa-value-selector">' +
        '<span class="dashicons dashicons-heart" style="font-size: 18px; margin-top: 4px; line-height: inherit; color: rgba(255, 0, 0, 0.65);">' +
        '</span>Permalink</span>')
        .mouseenter(function() {
          $(this).find('.taxa-value-selector').show();
        })
        .mouseleave(function() {
          $(this).find('.taxa-value-selector').hide();
        })
        .change(function () {
          if ($(this).prop('checked'))
            return;
          if ($(this).css('fontWeight') == 'bold') {
            $('.permalink-taxa', me).val('');
            $(this).css('fontWeight', 'normal');
          }
        });

      this.find('.taxa-value-selector').click(function (event) {
        event.preventDefault();
        console.debug(event);

        if ($(this).css('fontWeight') == 'bold') {
          $('.permalink-taxa', me).val('');
          $(this).css('fontWeight', 'normal');
        }

        me.find('.selectit').css('fontWeight', '');
        $(this).parents('.selectit').css('fontWeight', 'bold');
        $(this).parents('.selectit').find('input').prop('checked', true);
        var val = $(this).prev().attr('value');
        $('.permalink-taxa', me).val(val);
      });

      var taxonomy = $(this).data('taxonomy');

      $(this).append('<input type="hidden" name="permalinkTaxa[' + taxonomy + ']" class="permalink-taxa" />');
      if (options.current && options.current[taxonomy])
      {
        $(this).find('input[value="' + options.current[taxonomy] + '"]').prop('checked', true).parent('label').css('fontWeight', 'bold')
        $('.permalink-taxa', me).val(options.current[taxonomy]);
      }
      return this;
  };
}(jQuery));

jQuery(function($) {
    'use strict';
    $('.posts .scategory_permalink_name').each(function () {
        var $this = $(this);
        var category = $this.text().trim();
        var categoryWithHtml = $this.html().trim();
        if ( !category )
            return;
        else
        {
            var taxonomy = $this.attr('data-taxonomy');
            if (taxonomy == 'category') {
              taxonomy = 'categories';
            }
            var column = $this.parents('tr').children('.posts .column-' + taxonomy);
            var content = column.html();
            if (content)
            {
                content = content.replace('>'+category+'<', '><b>' + categoryWithHtml + '</b><');
                column.html(content);
            }
            else {
              console.debug('WP Category Permalink could not find a column named' + '.posts .column-' + taxonomy);
            }
        }
    });
});
