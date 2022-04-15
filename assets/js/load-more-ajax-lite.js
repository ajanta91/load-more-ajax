;(function($){
    "use strict";

    $( document ).ready( function(){ 
        
        $('.apl_block_wraper').each( function(){
            
            let $main = this.querySelector('.ajaxpost_loader');
            let $loadmore = this.querySelector('.loadmore_ajax');

            loadmore_post_ajax();

            let ajax_post_cat = this.getElementsByClassName('ajax_post_cat');
            
            if( ajax_post_cat.length > 0 ){
                $( ajax_post_cat ).each( function(){
                    $(this).click( function(){
                        
                        $('.ajax_post_cat').removeClass('active');

                
                        if( $main ) {
                            $main.setAttribute( 'data-cate', 0 );
                        }    

                        $(this).addClass('active');

                    } ).on( 'click', loadmore_post_ajax );
                } );
                
                
            }

            function loadmore_post_ajax(){

                var $ptype      = $main.getAttribute( 'data-post_type' );
                var $order      = $main.getAttribute( 'data-order' );
                var $limit      = $main.getAttribute( 'data-limit' );
                var $cteId      = $main.getAttribute( 'data-cate' );
                var $column     = $main.getAttribute( 'data-column' );
                var $text_limit = $main.getAttribute( 'data-text_limit' );
                var $style      = $main.getAttribute( 'data-block_style' );
                if ( ! $cteId ) {
                    $cteId = -1;
                }

                var $cateSelect = $main.parentElement.querySelector( '.ajax_post_cat.active' );
                if( $cateSelect ){
                    var $cteIdnew = $cateSelect.getAttribute( 'data-cateid' );
                    if( $cteIdnew !== $cteId ){
                        $main.innerHTML = '';
                        $main.setAttribute( 'data-order', 1 );
                        $order = 1;

                        $cteId = $cteIdnew;
                        $main.setAttribute( 'data-cate', $cteIdnew );
                    }

                }

                
                if( $loadmore ){
                    $loadmore.addEventListener( 'click', loadmore_post_ajax );
                    $loadmore.classList.add( 'loading_btn' );
                }

                var $setting = 'post_type=' + $ptype + '&order=' + $order + '&limit=' + $limit + '&cate=' + $cteId + '&column=' + $column + '&block_style=' + $style + '&text_limit=' + $text_limit;
                
                $.ajax({
                    type: "post",
                    url: load_more_ajax_lite.ajax_url+'?action=ajaxpostsload',
                    data: $setting,
                    dataType: "JSON",
                    beforeSend: function() {
                        if( $loadmore ) {
                            $loadmore.setAttribute('disabled', 'disabled');
                            $loadmore.innerHTML = 'Load More';
                        }
                    },
                    success: function ( res ) {
                        if( res.success ){
                            var $posts = ( res.data.posts ) ? res.data.posts : [];
                            var $ordernew = ( res.data.paged ) ? res.data.paged : $order;

                            if( $posts.length > 0){
                                for ( let $i = 0; $i < $posts.length; $i++ ) {
                                    let $self = $posts[$i];
                                    if( !$self ){
                                        continue;
                                    }

                                    let noThumbnail = ! $self.thumbnail ? ' no_thumbnail' : '';
                                    let wraper = document.createElement( 'div' );
                                    wraper.setAttribute( 'class', 'apl_post_wraper '+ $self.class + noThumbnail );
                                        
                                        let thumbWrap = document.createElement( 'div' );
                                        thumbWrap.setAttribute( 'class', 'apl_thumnbail_wrap' );
                                            
                                            if( $self.thumbnail ){
                                                let thumPermalink = document.createElement( 'a' );
                                                thumPermalink.setAttribute( 'href', $self.permalink );
                                                thumPermalink.setAttribute( 'class', 'permalink_thumn' );
                                                    
                                                    let thumbnail = document.createElement( 'img' );
                                                    thumbnail.setAttribute( 'src', $self.thumbnail );
                                                    thumPermalink.appendChild( thumbnail );

                                                thumbWrap.appendChild( thumPermalink );
                                            }

                                            if( $self.block_style == '1' ){
                                                let catWraper = document.createElement( 'div' );
                                                catWraper.setAttribute( 'class', 'apl_cat_wraper' );
                                                catWraper.innerHTML = $self.cats;
                                                thumbWrap.appendChild( catWraper );
                                            }
                                            
                                        let contentWraper = document.createElement( 'div' );
                                        contentWraper.setAttribute( 'class', 'apl_content_wraper' );

                                            if( $self.block_style == '2' && $self.cats != '' ){
                                                let catWraper2 = document.createElement( 'div' );
                                                catWraper2.setAttribute( 'class', 'apl_cat_wraper2' );
                                                catWraper2.innerHTML = $self.cats;
                                                contentWraper.appendChild( catWraper2 );
                                            }

                                            let titlePermalink = document.createElement( 'a' );
                                            titlePermalink.setAttribute( 'class', 'apl_title_permalink' );
                                            titlePermalink.setAttribute( 'href', $self.permalink );

                                                let title = document.createElement( 'h2' );
                                                title.setAttribute( 'class', 'apl_post_title' );
                                                title.setAttribute( 'title', $self.title );
                                                title.innerHTML = $self.title_excerpt;

                                            titlePermalink.appendChild( title );
                                            contentWraper.appendChild( titlePermalink );


                                            let postMeta = document.createElement( 'div' );
                                            postMeta.setAttribute( 'class', 'apl_post_meta' );

                                                let postAuthor = document.createElement( 'span' );
                                                postAuthor.setAttribute( 'class', 'apl_post_author apl_post_meta_item' );
                                                postAuthor.innerHTML = $self.author;
                                                postMeta.appendChild( postAuthor );

                                                let date = document.createElement( 'span' );
                                                date.setAttribute( 'class', 'apl_post_date apl_post_meta_item' );
                                                date.innerHTML = $self.date;
                                                postMeta.appendChild( date );

                                                let readTime = document.createElement( 'span' );
                                                readTime.setAttribute( 'class', 'apl_post_readtime apl_post_meta_item' )
                                                readTime.innerHTML = $self.read_time;
                                                postMeta.appendChild( readTime );

                                            contentWraper.appendChild( postMeta );

                                            let content = document.createElement( 'p' );
                                            content.innerHTML = $self.content;
                                            contentWraper.appendChild( content );

                                            if( $self.block_style == '2' ){
                                                let readMore = document.createElement( 'a' );
                                                readMore.setAttribute( 'href', $self.permalink );
                                                readMore.setAttribute( 'class', 'apl_read_more_btn' );
                                                readMore.innerHTML = 'Read More';
                                                contentWraper.appendChild( readMore );
                                            }
                                                
                                    
                                    wraper.appendChild( thumbWrap );
                                
                                    wraper.appendChild( contentWraper );

                                    $main.appendChild( wraper );

                                }
                                $main.setAttribute('data-order', $ordernew);
                                if( $loadmore ) {
                                    $loadmore.removeAttribute('disabled');
                                }
                            }
                            else {
                                if( $loadmore ) {
                                    $loadmore.setAttribute('disabled', 'disabled');
                                    $loadmore.innerHTML = 'No More Post';
                                }
                            }
                        }
                    },
                    //error ajax page
                    error: function(res){
                        console.log(res);
                    },
                    // ajax complate function
                    complete: function() {
                        $loadmore.classList.remove( 'loading_btn' );
                    },
                });

            }



        } )



        
    } )
    
    
})(jQuery);