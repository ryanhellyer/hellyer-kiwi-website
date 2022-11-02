<div class="post" id="post-{{id}}">
	<div class="post_thumbnail">
		<img width="100" height="100" src="{{#attachments.thumbnail}}{{attachments.thumbnail}}{{/attachments.thumbnail}}{{^attachments.thumbnail}}/wp-content/themes/pixopoint-2/images/ryan-cut-small.png{{/attachments.thumbnail}}" class="attachment-home-post-thumbnail size-home-post-thumbnail wp-post-image" alt="">
	</div>
	<h2 class="post_title">
		<a href="{{path}}" rel="bookmark" title="Permanent Link to {{title}}">
			{{title}}
		</a>
	</h2>
	<p class="post_subheader">
		Published {{date}} under {{#taxonomies.category.terms.length}}
			{{#taxonomies.category.terms}}
			<a href="/category/{{slug}}/" rel="category tag">{{name}}</a>,
			{{/taxonomies.category.terms}}
		</span>
		{{/taxonomies.category.terms.length}}
	</p>
	<div class="postcontent">
		<p>{{excerpt}}</p>
	</div>
</div>