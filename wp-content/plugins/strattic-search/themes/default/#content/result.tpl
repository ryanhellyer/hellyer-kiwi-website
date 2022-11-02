<div class="post-{{id}} page type-page status-publish hentry">
	<h3 id="post-{{id}}">
		<a href="{{path}}" rel="bookmark" title="Permanent Link to {{title}}">
			{{title}}
		</a>
	</h3>
	<small>{{date}}</small>

	<p class="postmetadata">
		{{#taxonomies.post_tag.terms.length}}
		Tags:
		{{#taxonomies.post_tag.terms}}
		<a href="/category/{{slug}}/" rel="post_tag tag">{{name}}</a>,
		{{/taxonomies.post_tag.terms}}
		{{/taxonomies.post_tag.terms.length}}

		<br />

		{{#taxonomies.category.terms.length}}
		 Posted in 
		{{#taxonomies.category.terms}}
		<a href="/tag/{{slug}}/" rel="category tag">{{name}}</a> | 
		{{/taxonomies.category.terms}}
		{{/taxonomies.category.terms.length}}
	</p>
</div>