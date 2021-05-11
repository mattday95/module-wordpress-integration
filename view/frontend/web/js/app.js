define([
    "jquery"
], function ($) {
    "use strict";
    return function (config, element) {

        const apiUrl = config.api;
        const postCount = config.postCount;
        const $block = $(element);
        const $postContainer = $block.find('.ponderosa-latest-posts__posts-container');
        const postLimit = postCount ? '?per_page=' + postCount : '';

        $.ajax({
            url: apiUrl + postLimit,
            type: 'GET',
            dataType: 'json',
            complete: function (response) {
                if(response.responseJSON){
                    const data = response.responseJSON;
                    renderPosts(data);
                }
            },
            error: function (xhr, status, errorThrown) {
                alert('Error fetching posts from Wordpress! See console for detailed error.');
                console.log(status);
            }
        });

        function renderPosts( data )
        {

            data.forEach( (post) => {

                console.log(post);

                const title = post.title.rendered;
                const slug = post.slug;
                const image = post.featured_media ? post.featured_media : 'https://via.placeholder.com/300';
                const excerpt = post.excerpt.rendered;
                const urlBase = window.location.origin;

                const template = `
                    <div class="ponderosa-latest-posts__post ponderosa-post-card">
                        <a href=${urlBase + '/posts/' + slug}>
                            <div class="ponderosa-post-card__image">
                                <img src="${image}"/>
                            </div>
                            <div class="ponderosa-post-card__content">
                                <h2>${title}</h2>
                                <div class="ponderosa-post-card__excerpt">
                                    ${excerpt}
                                </div>
                            </div>
                        </a>
                    </div>
                `;

                $postContainer.append(template);
                console.log(post);
            });
        }

    }
})