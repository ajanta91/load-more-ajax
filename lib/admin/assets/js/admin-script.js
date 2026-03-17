(function($){

    $(document).ready(function () {
        
        $(document).on('click', '.copy_block_shortcode', function () {
            const text = this.innerText;
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            $(this).css({
                'background-color': '#2271b1',
                'color': '#fff',
            });
            $(this).after('<span> Copied!</span>');
            setTimeout(() => {
                $(this).css({
                    'background-color': '',
                    'color': '',
                });
                $(this).next().remove();                
            }, 2000);
        });

    });

})(jQuery);