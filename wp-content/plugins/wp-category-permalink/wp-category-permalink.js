(function($) {
    'use strict';

    $.fn.sCategoryPermalink = function(options) {
		// Template for the taxa-value-selector link. Clone this whenever needed.
        var $link = $('<a class="taxa-value-selector" style="position: absolute; margin-top: 2px;">&nbsp;<span class="dashicons dashicons-heart" style="font-size: 15px; margin-top: 1px; line-height: inherit; color: rgba(255, 0, 0, 0.65);"></span>Permalink</a>');
		// The input to hold our selected permalink value. Need one for each and every taxonomy that can be part of a permalink.
        var $input = $('<input type="hidden" class="permalink-taxa" />');
		// Just a selector for checkboxes
        var cbSelector = '.tabs-panel label input[type="checkbox"]';
        var hover = {
                mouseenter: mouseenter,
                mouseleave: mouseleave
            };

		// Of course you know that 'this' is the collection of elements returned by jQuery(...);
        return this
            .on('click', '.taxa-value-selector', click)
            .on('hover', '.categorychecklist li label.selectit', function (event) {
				// In here, 'this' is the hovered item. (that means label.selectit)
                hover[event.type] && hover[event.type].call(this, event);
            })
            .on('change', cbSelector, change)
            .each(setup);

        /**
         * List item was hovered, create a .taxa-value-selector link.
         * Doing this on the fly in case of newly created categories.
         * Event handler so 'this' is the event target. For this event, it's always label.selectit
         *
         * @param   {Event}  event  Who cares? Don't use.
         *
         * @return  {void}
         */
        function mouseenter(event)
        {
            var $cb = $('input[type="checkbox"]', this);

            $link.clone()
                .data('taxaValue', $cb.val())
                .appendTo(this)
                .show();
        }

        /**
         * Trash the .taxa-value-selector, we will create it again next mouseover.
         * Event handler so 'this' is the event target. For this event, it's always label.selectit
         *
         * @param   {Event}  event  Who cares? Don't use.
         *
         * @return  {void}
         */
        function mouseleave(event)
        {
            $('.taxa-value-selector', this).remove();
        }

        /**
         * Create the permalinkTaxa input for this taxonomy.
         * Select the currently active category if there is one.
         * Called by jQuery's 'each' so 'this' is the current element of the set.
         *
         * @return  {void}
         */
        function setup()
        {
            var taxonomy = $(this).data('taxonomy');

            $input.clone().prop('name', 'permalinkTaxa[' + taxonomy + ']').appendTo(this);

            if (options.current && options.current[taxonomy])
            {
                selectValue(options.current[taxonomy], this);
            }
        }

        /**
         * Handle a click of the .taxa-value-selector
         * Event handler so 'this' is the event target. For this event, it's always .taxa-value-selector
         *
         * @param   {Event}  event  Who cares? Don't use.
         *
         * @return  {void}
         */
        function click(event)
        {
            event.preventDefault();

            deselectAll(event.delegateTarget);
            selectValue($(this).data('taxaValue'), event.delegateTarget);
        }

        /**
         * Handle a changed checkbox.
         * If the checkbox for the currently selected category is unchecked,
         * We must deselect that category.
         * Event handler so 'this' is the event target. For this event, it's always a match for cbSelector
         *
         * @return  {void}
         */
        function change()
        {
			// 'this' is a plain element. Get a jQuery.
            var $this = $(this);

            if ($this.prop('checked'))
            {
                return;
            }

            var value = $this.val();
            var $scope = $this.parents('.categorydiv').first();
            var current = $scope.find('.permalink-taxa').val();

            if (current === value)
            {
                deselectAll($scope);
            }
        }

        /**
         * Remove the value from the permalinkTaxa input.
         * Unbold labels.
         *
         * @param   {Mixed}  scope  DOM Element or jQuery
         *
         * @return  {void}
         */
        function deselectAll(scope)
        {
            $(cbSelector, scope)
                .parent('label').css('fontWeight', '');

            $('.permalink-taxa', scope).val('');
        }

        /**
         * Select a value for the permalinkTaxa field.
         * Bold the label and automatically check the box if it's not already.
         *
         * @param   {Integer}  value  Category ID
         * @param   {Mixed}    scope  DOM Element or jQuery
         *
         * @return  {void}
         */
        function selectValue(value, scope)
        {
            $(cbSelector, scope)
                .filter('[value="' + value + '"]').prop('checked', true)
                    .parent('label').css('fontWeight', 'bold');

            $('.permalink-taxa', scope).val(value);
        }
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
