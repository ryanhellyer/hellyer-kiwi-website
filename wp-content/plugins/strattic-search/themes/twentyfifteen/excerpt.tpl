<article id="post-{{id}}" class="post-{{id}} post hentry{{#taxonomies.category.terms.length}}{{#taxonomies.category.terms}} category-{{slug}}{{/taxonomies.category.terms}}{{/taxonomies.category.terms.length}}{{#taxonomies.post_tag.terms.length}}{{#taxonomies.post_tag.terms}} post-tag-{{slug}}{{/taxonomies.post_tag.terms}}{{/taxonomies.post_tag.terms.length}}{{#taxonomies.post_format.terms.length}}{{#taxonomies.post_format.terms}} post-format-{{slug}}{{/taxonomies.post_format.terms}}{{/taxonomies.post_format.terms.length}}">
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

		<span class="byline">
			<span class="author vcard">
				<span class="screen-reader-text">
					Author 
				</span> 
				<a class="url fn n" href="{{author.path}}/">{{author.display_name}}</a>
			</span>
		</span>

		<span class="posted-on">
			<span class="screen-reader-text">
				Posted on 
			</span>
			<a href="{{path}}" rel="bookmark">
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

	</footer><!-- .entry-footer -->

</article><!-- #post-{{id}} -->