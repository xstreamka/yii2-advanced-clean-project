(function($) {
    $(function() {
        $(document).ready(function() {


            // Фича для сброса параметров фильтра админки.
            var filters = $('.filters');
            $('input', filters).wrap('<div class="xs-clear-filter"></div>').after('<div class="xs-clear glyphicon glyphicon-remove"></div>');

            $('input', filters).each(function () {
                if ($(this).val() !== '') {
                    $(this).next().addClass('on');
                } else {
                    $(this).next().removeClass('on');
                }
            });

            $('.xs-clear-filter .xs-clear', filters).on('click', function(){
                $(this).prev().val('').change();
            });

            $('.selectpicker').selectpicker();


        });
    });
})(jQuery);