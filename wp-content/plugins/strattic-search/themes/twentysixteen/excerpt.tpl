<article id="post-{{id}}" class="post-{{id}} page type-page status-publish hentry">

	<header class="entry-header">
		<h2 class="entry-title">
			<a href="{{path}}" rel="bookmark">
				{{title}}
			</a>
		</h2>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<p>{{excerpt}}</p>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">

		<span class="posted-on">
			<span class="screen-reader-text">
				Posted on 
			</span>
			<a href="{{title}}" rel="bookmark">
				<time class="entry-date published updated" datetime="{{date_yy_mm_dd}}T{{H:i:s}}+00:00">
					{{date}}
				</time>
			</a>
		</span>

		{{#taxonomies.category.terms.length}}
		<span class="cat-links">
			<span class="screen-reader-text">
				Categories 
			</span>

			{{#taxonomies.category.terms}}
			<a href="/category/{{slug}}/" rel="category tag">{{name}}</a> 
			{{/taxonomies.category.terms}}
		</span>
		{{/taxonomies.category.terms.length}}

		{{#taxonomies.post_tag.terms.length}}
		<span class="tags-links">
			<span class="screen-reader-text">
				Tags 
			</span>
			{{#taxonomies.post_tag.terms}}
			<a href="/tag/{{slug}}/" rel="tag">{{name}}</a> 
			{{/taxonomies.post_tag.terms}}
		</span>
		{{/taxonomies.post_tag.terms.length}}

	</footer>

</article><!-- #post-{{id}} -->