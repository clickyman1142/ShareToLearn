<section id="content" class="eight column row pull-left singlepost">
    <?php
    $allowed = $this->session->userdata('passPosts') == NULL ? array() : $this->session->userdata('passPosts');
    if(strlen($post->getPassword()) > 0 && 
            (!array_key_exists($post->getId(), $allowed) || 
            $post->getPassword() != $allowed[$post->getId()])) {
    ?>
    <div id="password_required">
        This post is password protected. To view it please enter your password below and press enter key:
        <form action="<?php echo base_url() . 'user/checkLogin?action=passpost'?>" method="post">
            <input type="hidden" name="id" value="<?php echo $post->getId(); ?>"/>
            <br/><input type="password" placeholder="Enter password" style="width: 200px; display: inline;" name="password"/>
        </form>
    </div>
    <?php
    } else {
    ?>
    <!-- =========================================================== -->
    <a class="featured-img"><img height="200" src="<?php echo base_url() . "assets/upload/images/" . $post->getBanner(); ?>" alt=""></a>

    <h1 class="post-title"><?php echo $post->getTitle(); ?></h1>

    <p></p>

    <blockquote><?php echo $post->getExcerpt(); ?></blockquote>

    <p><?php echo $post->getContent(); ?></p>

    <div class="post-meta">
        <span class="comments"><a href="#"><?php echo $post->getComments(); ?></a></span>
        <span class="author"><a href="#"><?php echo $post->getAuthor()->getFull_name(); ?></a></span>
        <span class="date"><a href="#"><?php echo $post->getPublished(); ?></a></span>
    </div>

    <div class="social-media clearfix">
        <ul>
            <li class="twitter">
                <a href="https://twitter.com/share" class="twitter-share-button" 
                   data-url="<?php echo base_url() . $post->getGuid() . '-' . $post->getId() . '.html'; ?>" data-text="">Tweet</a>
                <script>!function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = "//platform.twitter.com/widgets.js";
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, "script", "twitter-wjs");</script>
            </li>
            <li class="facebook">
                <div id="fb-root"></div>
                <div class="fb-like" 
                     data-href="<?php echo base_url() . $post->getGuid() . '-' . $post->getId() . '.html'; ?>" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"></div>
            </li>
            <li class="google_plus">
                <!-- Place this tag where you want the +1 button to render. -->
                <div class="g-plusone" data-size="medium"></div>

                <!-- Place this tag after the last +1 button tag. -->
                <script type="text/javascript">
                    (function () {
                        var po = document.createElement('script');
                        po.type = 'text/javascript';
                        po.async = true;
                        po.src = 'https://apis.google.com/js/plusone.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(po, s);
                    })();
                </script>
            </li>
        </ul>
    </div>

    <div class="clear"></div>

    <?php
        if($post->getCmt_allow()) {
    ?>
    <!-- Comment -->
    <div class="line"></div>

    <h4 class="post-title">Bình luận</h4>

    <br>

    <ol id="comments">
        <?php echo $comments; ?>
    </ol>
    <!-- End Comments -->

    <div id="comment_part" class="line"></div>

    <h4 class="post-title">Gửi một bình luận</h4>
    <div id="reply" style="display: none; cursor: pointer; color: orangered;" title="Click here to cancel reply mode">REPLY model</div>
    <div id="form_error"></div>
    <!-- Contact Form -->
    <div class="contact-form comment cleafix">
        <form id="contact" action="<?php echo base_url() . "comment/addComment"; ?>" method="POST">
            <input tabindex="1" name="name" class="left" type="text" data-value="Name" placeholder="Name (required)"/>
            <input tabindex="3" name="website" class="right" type="text" data-value="Website" placeholder="Website"/>
            <input tabindex="2" name="mail" class="right" type="text" data-value="E-mail" placeholder="E-mail (required)"/>
            <textarea tabindex="4" name="content" class="twelve column" data-value="Comment" placeholder="Content"></textarea>
            <input type="hidden" name="postId" value="<?php echo $post->getId(); ?>">
            <input tabindex="5" data-value="0" id="submit" type="submit" value="Send">
        </form>
    </div>
    <!-- End Contact Form -->

    <script lang="javascript">
        $(document).ready(function () {
            // Event for reply button
            $('.comment-reply-link').click(function (e) {
                e.preventDefault();
                $('#reply').show();
                $('body, html').animate({
                    scrollTop: $('#comment_part').offset().top
                }, 800);
                $('#submit').attr('data-value', $(this).attr('href'));
            });
            
            // Exit reply mode
            $('#reply').on('click', function() {
                $('#submit').attr('data-value', '0');
                $(this).hide();
            });
            
            // Submit form
            $('#submit').click(function (e) {
                e.preventDefault();
                $('#submit').val('Sending...');
                var author_name = $('input[name=name]').val();
                var cmt_content = $('textarea[name=content]').val();
                var parent_id = $(this).attr('data-value');
                
                $.ajax({
                    url: <?php echo "\"" . base_url() . "comment/addComment\"" ?>,
                    type: "POST",
                    dataType: "text",
                    data: {
                        postId: $('input[name=postId]').val(),
                        name: author_name,
                        website: $('input[name=website]').val(),
                        content: cmt_content,
                        parent: parent_id,
                        email: $('input[name=mail]').val(), 
                        type: 'comment'
                    },
                    success: function (res) {
                        if(res !== 'failure' && !$.isNumeric(res)) {
                            $('#form_error').empty().prepend(res);
                            $('#submit').val('Send');
                            return;
                        }
                        
                        if(res !== 'failure' && parent_id !== '0') {
                            if($('#cmt_' + parent_id).has('ul').length) {
                                $('#cmt_' + parent_id + ' ul').prepend(
                                    '<li id="cmt_'+ res +'">' +
                                        '<div class="author-avatar"><img alt="" src ="<?php echo base_url(); ?>assets/client/images/avatar.jpg"/></div>' +
                                        '<div class="comment-author"><a>' + author_name + '</a></div>' +
                                        '<div class="comment-date">Mới đây</div>' +
                                        '<div class="comment-text"><p>' + cmt_content + '</p></div>' +
                                        '<div class="comment-reply">Đang chờ duyệt...</div>' +
                                    '</li>'
                                );
                            } else {
                                $('#cmt_' + parent_id).append(
                                    '<ul class="children"><li id="cmt_'+ res +'">' +
                                        '<div class="author-avatar"><img alt="" src ="<?php echo base_url(); ?>assets/client/images/avatar.jpg"/></div>' +
                                        '<div class="comment-author"><a>' + author_name + '</a></div>' +
                                        '<div class="comment-date">Mới đây</div>' +
                                        '<div class="comment-text"><p>' + cmt_content + '</p></div>' +
                                        '<div class="comment-reply">Đang chờ duyệt...</div>' +
                                    '</li></ul>'
                                );
                            }
                            $('#submit').val('Sent. Thanks!');
                            // Delete comment parent id
                            $('#submit').attr('data-value', '0');
                        } else if(res !== 'failure') {
                            $('#comments').prepend(
                                    '<li id="cmt_'+ res +'">' +
                                        '<div class="author-avatar"><img alt="" src ="<?php echo base_url(); ?>assets/client/images/avatar.jpg"/></div>' +
                                        '<div class="comment-author"><a>' + author_name + '</a></div>' +
                                        '<div class="comment-date">Mới đây</div>' +
                                        '<div class="comment-text"><p>' + cmt_content + '</p></div>' +
                                        '<div class="comment-reply">Đang chờ duyệt...</div>' +
                                    '</li>'
                            );
                            $('#submit').val('Sent. Thanks!');
                        } else {
                            $('#submit').val('Error');
                        }
                        
                        // Scroll to recent comment
                        $('body, html').animate({
                            scrollTop: $('#cmt_' + res).offset().top
                        }, 800);
                    },
                    failure: function (error) {
                        alert(error);
                        // Delete comment parent id
                        $('#submit').attr('data-value', '0');
                    }
                });
            });
        });
    </script>
    <?php
        }
    ?>
    <?php
    }
    ?>
</section>