(function($){

    $(document).ready(function () {
        $('.copy_block_shortcode').on('click', function () {            
            navigator.clipboard.writeText($(this)[0].innerText);
            console.log($(this)[0].style.background = 'red'); 
            $(this)[0].style.background = '#2271b1';
            $(this)[0].style.color = '#fff';
            $(this)[0].style.padding = '5px';                
        });
    });

})(jQuery);