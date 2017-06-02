<% require themedCSS('onepage','onepage') %>

<div class="onepage content-container unit size4of4 lastUnit">
	<article>
		<h1<% if $HeadingColor %> style="color: $HeadingColor"<% end_if %>>$Title</h1>
		<div class="content">$Content</div>
	</article>
    <% loop $Children %>
        $OnePageContent
    <% end_loop %>
</div>
