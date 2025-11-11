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
                var $title_limit= $main.getAttribute( 'data-title_limit' );
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
                    var overlay = document.createElement('div');
                    overlay.setAttribute( 'class', 'loading_overlay' );
                    $main.appendChild( overlay );
                }
                $loadmore.addEventListener( 'click',  function(){
                    $loadmore.classList.add( 'loading_btn' );
                } );
                

                var $setting = 'post_type=' + $ptype + '&order=' + $order + '&limit=' + $limit + '&cate=' + $cteId + '&column=' + $column + '&block_style=' + $style + '&text_limit=' + $text_limit + '&title_limit=' + $title_limit;
                
                // Add nonce if available
                if (typeof load_more_ajax_lite !== 'undefined' && load_more_ajax_lite.nonce) {
                    $setting += '&nonce=' + load_more_ajax_lite.nonce;
                }
                
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
                            var $posts      = ( res.data.posts ) ? res.data.posts : [];
                            var $ordernew   = ( res.data.paged ) ? res.data.paged : $order;
                            var block_style = ( res.data.block_style ) ? res.data.block_style : '1';
                            

                            if( $posts.length > 0 ){
                                if( block_style == '1' || block_style == '2' ){
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
                                                    // Handle both old string format and new object format
                                                    if (typeof $self.author === 'object' && $self.author.name) {
                                                        let authorLink = document.createElement('a');
                                                        authorLink.setAttribute('href', $self.author.link);
                                                        if ($self.author.avatar) {
                                                            let authorAvatar = document.createElement('img');
                                                            authorAvatar.setAttribute('src', $self.author.avatar);
                                                            authorAvatar.setAttribute('alt', $self.author.name);
                                                            authorAvatar.setAttribute('class', 'author-avatar');
                                                            authorLink.appendChild(authorAvatar);
                                                        }
                                                        authorLink.appendChild(document.createTextNode(' ' + $self.author.name));
                                                        postAuthor.appendChild(authorLink);
                                                    } else {
                                                        postAuthor.innerHTML = $self.author;
                                                    }
                                                    postMeta.appendChild( postAuthor );

                                                    let date = document.createElement( 'span' );
                                                    date.setAttribute( 'class', 'apl_post_date apl_post_meta_item' );
                                                    // Handle both old string format and new object format
                                                    if (typeof $self.date === 'object' && $self.date.formatted) {
                                                        let timeElement = document.createElement('time');
                                                        timeElement.setAttribute('datetime', $self.date.iso);
                                                        timeElement.textContent = $self.date.formatted;
                                                        date.appendChild(timeElement);
                                                    } else {
                                                        date.innerHTML = $self.date;
                                                    }
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
                                }

                                if( block_style == '3' ) {
                                    for (let $i = 0; $i < $posts.length; $i++) {
                                        let $self = $posts[$i];
                                        if (!$self) {
                                            continue;
                                        }
                                        let noThum = !$self.thumbnail ? ' no_thumbnail' : '';
                                        
                                        var PostsWrapper_3 = document.createElement('div');
                                        PostsWrapper_3.setAttribute('class', 'posts_wrapper_3 ' + $self.class + noThum);
                                        var PostsWrapperInner = document.createElement('div');
                                        PostsWrapperInner.setAttribute('class', 'posts_wrapper_inner');

                                        var PostThumb = document.createElement('div');
                                        PostThumb.setAttribute('class', 'post_thumb');

                                        var PostThumbA = document.createElement('a');
                                        PostThumbA.setAttribute('href', $self.permalink);
                                        PostThumbA.setAttribute('class', 'post_permalink');

                                        var PostThumbImg = document.createElement('img');
                                        PostThumbImg.setAttribute('src', $self.thumbnail);


                                        var PostContent = document.createElement('div');
                                        PostContent.setAttribute('class', 'post_content');
                                        
                                        var PostDate = document.createElement('ul');
                                        PostDate.setAttribute('class', 'post_meta');
                                        
                                        var PostDateLi = document.createElement('li');
                                        PostDateLi.setAttribute('class', 'post_meta_list');
                                        
                                        var PostDateIcon = document.createElement('i');
                                        PostDateIcon.setAttribute('class', 'far fa-calendar-alt');

                                        var PostTitle = document.createElement('h3');
                                        PostTitle.setAttribute('class', 'post_title');

                                        var PostTitleLink = document.createElement('a');
                                        PostTitleLink.setAttribute('href', $self.permalink);
                                        PostTitleLink.setAttribute('class', 'post_permalink');

                                        var PostMetaUl = document.createElement('ul');
                                        PostMetaUl.setAttribute('class', 'post_meta');

                                        var PostAuthor = document.createElement('li');
                                        PostAuthor.setAttribute('class', 'post_author');

                                        var PostAuthorIcon = document.createElement('i');
                                        PostAuthorIcon.setAttribute('class', 'far fa-user');
                                        
                                        var PostComment = document.createElement('li');
                                        PostComment.setAttribute('class', 'post_comment');

                                        var PostCommentIcon = document.createElement('i');
                                        PostCommentIcon.setAttribute('class', 'far fa-comments');
                                        
                                        var PostReadTitme = document.createElement('li');
                                        PostReadTitme.setAttribute('class', 'post_read_time');

                                        var PostReadTimeIcon = document.createElement('i');
                                        PostReadTimeIcon.setAttribute('class', 'far fa-clock');

                                        

                                        $main.appendChild(PostsWrapper_3);
                                            PostsWrapper_3.appendChild(PostsWrapperInner);
                                                if( $self.thumbnail != '' ){
                                                    PostsWrapperInner.appendChild(PostThumb)
                                                    PostThumb.appendChild(PostThumbA);
                                                    PostThumbA.appendChild(PostThumbImg);
                                                }
                                                
                                                PostsWrapperInner.appendChild(PostContent)
                                                    PostContent.appendChild(PostDate);
                                                        PostDate.appendChild(PostDateLi);
                                                            // Handle both old string format and new object format
                                                            if (typeof $self.date === 'object' && $self.date.formatted) {
                                                                var timeElement = document.createElement('time');
                                                                timeElement.setAttribute('datetime', $self.date.iso);
                                                                timeElement.textContent = $self.date.formatted;
                                                                PostDateLi.appendChild(timeElement);
                                                            } else {
                                                                PostDateLi.innerHTML = $self.date;
                                                            }
                                                            PostDateLi.prepend(PostDateIcon);
                                                    PostContent.appendChild(PostTitle);
                                                        PostTitle.appendChild(PostTitleLink);
                                                            PostTitleLink.innerHTML = $self.title_excerpt;
                                                    PostContent.appendChild(PostMetaUl);
                                                        PostMetaUl.appendChild(PostAuthor);
                                                            // Handle both old string format and new object format
                                                            if (typeof $self.author === 'object' && $self.author.name) {
                                                                PostAuthor.appendChild(document.createTextNode($self.author.name));
                                                            } else {
                                                                PostAuthor.innerHTML = $self.author;
                                                            }
                                                            PostAuthor.prepend(PostAuthorIcon);
                                                        PostMetaUl.appendChild(PostComment);
                                                            PostComment.innerHTML = $self.comment_count;
                                                            PostComment.prepend(PostCommentIcon);
                                                        PostMetaUl.appendChild(PostReadTitme);
                                                            PostReadTitme.innerHTML = $self.read_time;
                                                            PostReadTitme.prepend(PostReadTimeIcon);
                                        
                                    }
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
                        
                        $main.removeChild( overlay );
                    },
                });

            }



        } )



        
    } )
    
    
})(jQuery);