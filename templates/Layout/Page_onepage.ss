<% if $BackgroundImage %>
    <div class="img-holder" data-stellar-background-ratio="0.5"
         style="background-image:url($BackgroundImage.URL);">
    </div>
<% end_if %>
<a name="$URLSegment"></a>
<article<% if $OnePageSlideStyle %> style="$OnePageSlideStyle"<% end_if %><% if $AdditionalCSSClass %> class="$AdditionalCSSClass"<% end_if %>>
    <h2<% if $HeadingColor %> style="color: $HeadingColor"<% end_if %>>$Title</h2>
    <div class="content">$Content</div>
</article>
