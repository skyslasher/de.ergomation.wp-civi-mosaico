
function wordpressPostsWidgetPluginAjaxGetPosts( pageNum )
{
    // get available posts
    $.ajax(
        {
            type: 'POST',
            url: '/wp-admin/admin.php?page=CiviCRM&q=civicrm/wp_civi_mosaico/wp-posts',
            dataType: "json",
            data:
            {
                page_num : pageNum
            },
            error: function( jqXHR, textStatus, errorThrown )
            {
                console.log( jqXHR, textStatus, errorThrown );
            },
            success: function( response )
            {
                window.viewModel.wpPostsAvailablePostsClearPost();
                window.viewModel.wpPostsAvailablePostsNumPages( response[ "num_pages" ] );
                $.each(
                    response[ "posts" ],
                    function( key, value )
                    {
                        window.viewModel.wpPostsAvailablePostsAddPost(
                            value[ "ID" ],
                            value[ "post_title" ],
                            value[ "post_content" ],
                            value[ "guid" ],
                            value[ "author_info" ],
                            value[ "author_images" ],
                            value[ "author_from" ],
                            value[ "author_conjunction" ],
                            value[ "featured_image" ],
                            value[ "reading_time" ],
                            value[ "reading_time_caption" ]
                        );
                    }
                );
            }
        }
    );
}

window.onload = function()
{
    wordpressPostsWidgetPluginAjaxGetPosts( 1 );
}

var wordpressPostsWidgetPlugin = {
    widget: function($, ko, kojqui) {
        return {
            widget: 'wp-posts',
            parameters: {
                param: true
            },
            html: function( propAccessor, onfocusbinding, parameters )
            {
                theHTML = '<select class="WP_Posts" size="11" data-bind="foreach: $root.wpPostsAvailablePosts">';
                theHTML += '<option data-bind="value: postID, text: postTitle, click: $root.wpPostsPostSelected"></option>';
                theHTML += '</select>';
                theHTML += '<div class="wp-post-pager"><div class="wp-post-pager-button-container">';
                theHTML += '<button class="ui-button-icon-primary ui-icon fa fa-fast-backward" data-bind="click: $root.wpPostsFirstClick"></button>';
                theHTML += '<button class="ui-button-icon-primary ui-icon fa fa-backward" data-bind="click: $root.wpPostsBackClick, enable: $root.wpPostsAvailablePostsCurrentPage != 1"></button>';
                theHTML += '<span class="ui-button-text" data-bind="text: $root.wpPostsPageText"></span>';
                theHTML += '<button class="ui-button-icon-primary ui-icon fa fa-forward" data-bind="click: $root.wpPostsForwardClick,enable: $root.wpPostsAvailablePostsNumPages != $root.wpPostsAvailablePostsCurrentPage"></button>';
                theHTML += '<button class="ui-button-icon-primary ui-icon fa fa-fast-forward" data-bind="click: $root.wpPostsLastClick"></button>';
                theHTML += '</div></div>';
                theHTML += '<label>Excerpt length (words): <input id="WP_excerpt_len" size="7" type="text" value="95"/></label>';
                return theHTML;
            }
        };
    },
    viewModel: function( vm )
    {
        vm_self = vm;
        function wpPostsPostObject( postID, postTitle, postContent, postLink, postAuthors, postAuthorImages, postAuthorFrom, postAuthorConjunction, postFeaturedImage, postReadingTime, postReadingTimeCaption )
        {
            var self = this;
            self.postID = postID;
            self.postTitle = $( "<div>" + postTitle + "</div>" ).text();
            self.postContent = $( "<div>" + postContent + "</div>" ).text();
            self.postLink = postLink;
            self.postAuthors = postAuthors;
            self.postAuthorImages = postAuthorImages;
            self.postAuthorFrom = postAuthorFrom;
            self.postAuthorConjunction = postAuthorConjunction;
            self.postFeaturedImage = postFeaturedImage;
            self.postReadingTime = postReadingTime;
            self.postReadingTimeCaption = postReadingTimeCaption;
            self.applyPostID = function()
            {

                if ( 0 != self.postID )
                {
                    author_desc = '<span class="wp-post-authors">Von ';
                    author_img = '<div class="mobile-full">';
                    author_len = $( self.postAuthors ).length;

                    $( self.postAuthors ).each( function( index )
                        {
                            // author description
                            author_desc += '<span class="wp-post-author">' + this.author + '</span>';
                            if ( index < author_len - 2 )
                            {
                                author_desc += ', ';
                            }
                            if ( index == author_len - 2 )
                            {
                                author_desc += ' ' + self.postAuthorConjunction + ' ';
                            }
                            // author images
                            author_img += '<table align="left" border="0" cellpadding="0" cellspacing="0"><tr>';
                            author_img += '<td align="left"><img src="' + this.image + '" alt="' + this.author + '" class="wp-user-avatar avatar" width="108" height="108"></td>';
                            author_img += '<td width="16"></td>';
                            author_img += '</tr><tr><td colspan="2" height="6"></td>';
                            author_img += '</tr></table>';
                        }
                    );
                    author_img += '</div>';
                    author_desc += '</span>';

                    excerpt_len = $( '#WP_excerpt_len' ).val();

                    for ( i = 0; i < self.postContent.length; i++ )
                    {
                        if ( self.postContent.charAt( i ) == ' ' )
                        {
                            if ( !--excerpt_len )
                            {
                                break;
                            }
                        }
                    }
                    excerpt = self.postContent.substring( 0, i ) + ' …';
                    read_more_link = self.postLink;
                    read_more_text = self.postReadingTimeCaption + self.postReadingTime + " &gt;";
                    read_more = ' <a href="' + self.postLink + '">' + read_more_text + '</a>';
                    contents = excerpt;
                }
                else
                {
                    contents = '';
                }

                block = vm.selectedBlock();
                blockId = block.id();

                // set new post header
                block.text( self.postTitle );
                block.authorImagesText( author_img );
                block.authorImagesVisible( true );
                block.authorsText( author_desc );
                block.authorsVisible( true );

                // set new post button contents, if exists
                if ( typeof block.buttonLink !== "undefined" )
                {
                    block.buttonLink().url( read_more_link );
                    block.buttonLink().text( read_more_text );
                }
                else
                {
                    contents += read_more;
                }
                // set new post contents
                block.longText( contents );

                if ( typeof block.image !== "undefined" )
                    if ( "" != self.postFeaturedImage )
                    {
                        block.image().src( self.postFeaturedImage );
                        block.imageVisible( true );
                    }
                    else
                    {
                        block.image().src( '' );
                        block.imageVisible( false );
                    }
            }
        }
        // this array holds the current paged result of the blogposts
        vm_self.wpPostsAvailablePosts = ko.observableArray( [ new wpPostsPostObject( 0, '(no post)', '' ) ] );
        // this holds the number of pages and the current page of the blogposts result
        vm_self.wpPostsAvailablePostsNumPages = ko.observable( 1 );
        vm_self.wpPostsAvailablePostsCurrentPage = ko.observable( 1 );
        vm_self.wpPostsFirstClick = function()
        {
            vm_self.wpPostsAvailablePostsCurrentPage( 1 );
            wordpressPostsWidgetPluginAjaxGetPosts( 1 );
        }
        vm_self.wpPostsLastClick = function()
        {
            np = vm_self.wpPostsAvailablePostsNumPages();
            vm_self.wpPostsAvailablePostsCurrentPage( np );
            wordpressPostsWidgetPluginAjaxGetPosts( np );
        }
        vm_self.wpPostsBackClick = function()
        {
            cp = vm_self.wpPostsAvailablePostsCurrentPage();
            if ( 1 < cp )
            {
                vm_self.wpPostsAvailablePostsCurrentPage( cp - 1 );
                wordpressPostsWidgetPluginAjaxGetPosts( cp - 1 );
            }
        }
        vm_self.wpPostsForwardClick = function()
        {
            cp = vm_self.wpPostsAvailablePostsCurrentPage();
            np = vm_self.wpPostsAvailablePostsNumPages();
            if ( np > cp )
            {
                vm_self.wpPostsAvailablePostsCurrentPage( cp + 1 );
                wordpressPostsWidgetPluginAjaxGetPosts( cp + 1 );
            }
        }
        vm_self.wpPostsPageText = ko.computed(
            function()
            {
                return vm_self.wpPostsAvailablePostsCurrentPage() + "/" + vm_self.wpPostsAvailablePostsNumPages();
            },
            this
        );
        vm_self.wpPostsAvailablePostsClearPost = function()
        {
            vm_self.wpPostsAvailablePosts.removeAll();
        }
        vm_self.wpPostsAvailablePostsAddPost = function( postID, postTitle, postContent, postLink, postAuthors, postAuthorImages, postAuthorFrom, postAuthorConjunction, postFeaturedImage, postReadingTime, postReadingTimeCaption )
        {
            vm_self.wpPostsAvailablePosts.push( new wpPostsPostObject( postID, postTitle, postContent, postLink, postAuthors, postAuthorImages, postAuthorFrom, postAuthorConjunction, postFeaturedImage, postReadingTime, postReadingTimeCaption ) );
        }
        vm_self.wpPostsPostSelected = function( post )
        {
            post.applyPostID();
        }
    }
};
