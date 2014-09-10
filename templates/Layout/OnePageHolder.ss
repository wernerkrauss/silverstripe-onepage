<% require block('framework/thirdparty/jquery/jquery.js') %>
<% require javascript('onepage/javascript/jquery-1.11.1.min.js') %>
<% require javascript('onepage/javascript/parallax-imagescroll/jquery.imageScroll.js') %>
<% require javascript('onepage/javascript/onepage.js') %>
<% require themedCSS('onepage','onepage') %>

<div class="onepage content-container unit size4of4 lastUnit">
	<article>
		<h1>$Title</h1>
		<div class="content">$Content</div>
	</article>
    <% loop $Children %>
        <% if $BackgroundImage %>
            <div class="img-holder" data-image="$BackgroundImage.URL"
                    style="background-image:url($BackgroundImage.URL);">
            </div>
        <% end_if %>
        <article<% if $OnePageSlideStyle %> style="$OnePageSlideStyle"<% end_if %><% if $AdditionalCSSClass %> class="$AdditionalCSSClass"<% end_if %>>
            <h2>$Title</h2>
            <div class="content">$Content</div>
        </article>
    <% end_loop %>
</div>