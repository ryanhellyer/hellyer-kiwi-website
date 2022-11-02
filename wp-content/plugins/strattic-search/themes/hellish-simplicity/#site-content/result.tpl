<article id="post-{{id}}" class="post-{{id}} post hentry{{#taxonomies.category.terms.length}}{{#taxonomies.category.terms}} category-{{slug}}{{/taxonomies.category.terms}}{{/taxonomies.category.terms.length}}{{#taxonomies.post_tag.terms.length}}{{#taxonomies.post_tag.terms}} post-tag-{{slug}}{{/taxonomies.post_tag.terms}}{{/taxonomies.post_tag.terms.length}}{{#taxonomies.post_format.terms.length}}{{#taxonomies.post_format.terms}} post-format-{{slug}}{{/taxonomies.post_format.terms}}{{/taxonomies.post_format.terms.length}}">

	<header class="entry-header">
		<h2 class="entry-title">
			<a href="{{path}}" title="{{title}}" rel="bookmark">
				{{title}}
			</a>
		</h2><!-- .entry-title -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<p>
			{{excerpt}}
		</p>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		Posted on <a href="{{path}}" title="{{date_time_g_i_a}}" rel="bookmark">
			<time class="entry-date updated" datetime="{{date_yy_mm_dd}}T{{H:i:s}}+00:00">
				{{date}}
			</time>
		</a>
		<span class="byline">
			 by <span class="author vcard">
				<a class="url fn n" href="{{author.path}}" title="View all posts by {{author.display_name}}" rel="author">
					{{author.display_name}}
				</a>
			</span>
		</span>
		<span class="sep"> | </span>

		{{#taxonomies.category.terms.length}}
		<span class="cat-links">
			in 
			{{#taxonomies.category.terms}}
			<a href="/category/{{slug}}/" rel="category tag">{{name}}</a> 
			{{/taxonomies.category.terms}}
		</span>
		<span class="sep"> | </span>
		{{/taxonomies.category.terms.length}}

		{{#taxonomies.post_tag.terms.length}}
		<span class="tags-links">
			Tagged {{#taxonomies.post_tag.terms}}<a href="/tag/{{slug}}/" rel="tag">{{name}}</a> {{/taxonomies.post_tag.terms}}
		</span>
		<span class="sep"> | </span>
		{{/taxonomies.post_tag.terms.length}}

		<span class="comments-link">
			<a href="{{path}}#respond">
				Leave a comment
			</a>
		</span>
	</footer><!-- .entry-meta -->

</article><!-- #post-{{id}} -->