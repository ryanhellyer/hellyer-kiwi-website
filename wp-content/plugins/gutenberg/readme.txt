=== Gutenberg ===
Contributors: matveb, joen, karmatosed
Requires at least: 5.1.0
Tested up to: 5.2
Stable tag: 6.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The block editor was introduced in core WordPress with version 5.0. This beta plugin allows you to test bleeding-edge features around editing and customization projects before they land in future WordPress releases.

== Description ==

Gutenberg is more than an editor. While the editor is the focus right now, the project will ultimately impact the entire publishing experience including customization (the next focus area).

<a href="https://wordpress.org/gutenberg">Discover more about the project</a>.

= Editing focus =

> The editor will create a new page- and post-building experience that makes writing rich posts effortless, and has “blocks” to make it easy what today might take shortcodes, custom HTML, or “mystery meat” embed discovery. — Matt Mullenweg

One thing that sets WordPress apart from other systems is that it allows you to create as rich a post layout as you can imagine -- but only if you know HTML and CSS and build your own custom theme. By thinking of the editor as a tool to let you write rich posts and create beautiful layouts, we can transform WordPress into something users _love_ WordPress, as opposed something they pick it because it's what everyone else uses.

Gutenberg looks at the editor as more than a content field, revisiting a layout that has been largely unchanged for almost a decade.This allows us to holistically design a modern editing experience and build a foundation for things to come.

Here's why we're looking at the whole editing screen, as opposed to just the content field:

1. The block unifies multiple interfaces. If we add that on top of the existing interface, it would _add_ complexity, as opposed to remove it.
2. By revisiting the interface, we can modernize the writing, editing, and publishing experience, with usability and simplicity in mind, benefitting both new and casual users.
3. When singular block interface takes center stage, it demonstrates a clear path forward for developers to create premium blocks, superior to both shortcodes and widgets.
4. Considering the whole interface lays a solid foundation for the next focus, full site customization.
5. Looking at the full editor screen also gives us the opportunity to drastically modernize the foundation, and take steps towards a more fluid and JavaScript powered future that fully leverages the WordPress REST API.

= Blocks =

Blocks are the unifying evolution of what is now covered, in different ways, by shortcodes, embeds, widgets, post formats, custom post types, theme options, meta-boxes, and other formatting elements. They embrace the breadth of functionality WordPress is capable of, with the clarity of a consistent user experience.

Imagine a custom “employee” block that a client can drag to an About page to automatically display a picture, name, and bio. A whole universe of plugins that all extend WordPress in the same way. Simplified menus and widgets. Users who can instantly understand and use WordPress  -- and 90% of plugins. This will allow you to easily compose beautiful posts like <a href="http://moc.co/sandbox/example-post/">this example</a>.

Check out the <a href="https://wordpress.org/gutenberg/handbook/reference/faq/">FAQ</a> for answers to the most common questions about the project.

= Compatibility =

Posts are backwards compatible, and shortcodes will still work. We are continuously exploring how highly-tailored metaboxes can be accommodated, and are looking at solutions ranging from a plugin to disable Gutenberg to automatically detecting whether to load Gutenberg or not. While we want to make sure the new editing experience from writing to publishing is user-friendly, we’re committed to finding  a good solution for highly-tailored existing sites.

= The stages of Gutenberg =

Gutenberg has three planned stages. The first, aimed for inclusion in WordPress 5.0, focuses on the post editing experience and the implementation of blocks. This initial phase focuses on a content-first approach. The use of blocks, as detailed above, allows you to focus on how your content will look without the distraction of other configuration options. This ultimately will help all users present their content in a way that is engaging, direct, and visual.

These foundational elements will pave the way for stages two and three, planned for the next year, to go beyond the post into page templates and ultimately, full site customization.

Gutenberg is a big change, and there will be ways to ensure that existing functionality (like shortcodes and meta-boxes) continue to work while allowing developers the time and paths to transition effectively. Ultimately, it will open new opportunities for plugin and theme developers to better serve users through a more engaging and visual experience that takes advantage of a toolset supported by core.

= Contributors =

Gutenberg is built by many contributors and volunteers. Please see the full list in <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTORS.md">CONTRIBUTORS.md</a>.

== Frequently Asked Questions ==

= How can I send feedback or get help with a bug? =

We'd love to hear your bug reports, feature suggestions and any other feedback! Please head over to <a href="https://github.com/WordPress/gutenberg/issues">the GitHub issues page</a> to search for existing issues or open a new one. While we'll try to triage issues reported here on the plugin forum, you'll get a faster response (and reduce duplication of effort) by keeping everything centralized in the GitHub repository.

= How can I contribute? =

We’re calling this editor project "Gutenberg" because it's a big undertaking. We are working on it every day in GitHub, and we'd love your help building it.You’re also welcome to give feedback, the easiest is to join us in <a href="https://make.wordpress.org/chat/">our Slack channel</a>, `#core-editor`.

See also <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTING.md">CONTRIBUTING.md</a>.

= Where can I read more about Gutenberg? =

- <a href="http://matiasventura.com/post/gutenberg-or-the-ship-of-theseus/">Gutenberg, or the Ship of Theseus</a>, with examples of what Gutenberg might do in the future
- <a href="https://make.wordpress.org/core/2017/01/17/editor-technical-overview/">Editor Technical Overview</a>
- <a href="https://wordpress.org/gutenberg/handbook/reference/design-principles/">Design Principles and block design best practices</a>
- <a href="https://github.com/Automattic/wp-post-grammar">WP Post Grammar Parser</a>
- <a href="https://make.wordpress.org/core/tag/gutenberg/">Development updates on make.wordpress.org</a>
- <a href="https://wordpress.org/gutenberg/handbook/">Documentation: Creating Blocks, Reference, and Guidelines</a>
- <a href="https://wordpress.org/gutenberg/handbook/reference/faq/">Additional frequently asked questions</a>


== Changelog ==

### Enhancements

*   [Introduce motion](https://github.com/WordPress/gutenberg/pull/16065)/animation when reordering/adding/removing blocks.
*   Improve the [Image block link settings](https://github.com/WordPress/gutenberg/pull/15570) and move it to the block toolbar.
*   Use a snackbar notice when clicking “[Copy all content](https://github.com/WordPress/gutenberg/pull/16265)”.
*   Show [REST API error messages](https://github.com/WordPress/gutenberg/pull/15657) as notices.
*   Clarify the wording of the view link in the [Permalink panel](https://github.com/WordPress/gutenberg/pull/16041).
*   [Hide the “Copy all content”](https://github.com/WordPress/gutenberg/pull/16286) button if the post is empty.
*   [Hide the ungroup action](https://github.com/WordPress/gutenberg/pull/16332) when there are no inner blocks.
*   Use admin schemes dependent [focus state for primary buttons](https://github.com/WordPress/gutenberg/pull/16275).
*   Add support for the [table cells scope attribute](https://github.com/WordPress/gutenberg/pull/16154) when pasting.

### Experiments

*   Introduce a new [Customizer Panel](https://github.com/WordPress/gutenberg/pull/16204) to edit block-based widget areas.
*   Add the [block inspector](https://github.com/WordPress/gutenberg/pull/16203) to the widgets screen.
*   Add a [global inserter](http://add/inserter-widget-areas) to the widgets screen.

### Bug Fixes

*   Show the [pre-publish panel for contributors](https://github.com/WordPress/gutenberg/pull/16424).
*   Fix the [save in progress state](https://github.com/WordPress/gutenberg/pull/16303) of the Publish/Update Button.
*   Fix [adding/removing columns from the table block](https://github.com/WordPress/gutenberg/pull/16410) when using header/footer sections.
*   Fix Image block not [preserving custom dimensions](https://github.com/WordPress/gutenberg/pull/16125) when opening the media library.
*   [Resize Image blocks](https://github.com/WordPress/gutenberg/pull/16398) properly when changing the width from the inspector.
*   Fix php error that can potentially be triggered by [gutenberg_is_block_editor](https://github.com/WordPress/gutenberg/pull/16201).
*   Fix error when using the [“tag” block attribute source type](https://github.com/WordPress/gutenberg/pull/16290).
*   Fix [chrome rendering bug](https://github.com/WordPress/gutenberg/pull/16325) happening when resizing images.
*   Fix the [data-block style selector](https://github.com/WordPress/gutenberg/pull/16207) to avoid affecting third-party components.
*   Allow the [columns layout options](https://github.com/WordPress/gutenberg/pull/16371) to wrap on small screens.
*   Fix [isShallowEqual](https://github.com/WordPress/gutenberg/pull/16329) edge case when the second argument is undefined.
*   Prevent the [disabled block switcher icon](https://github.com/WordPress/gutenberg/pull/16390) from becoming unreadable.
*   Fix [Group Block deprecation](https://github.com/WordPress/gutenberg/pull/16348) and any deprecation relying on hooks.
*   A11y:
    *   Make the [top toolbar wrap](https://github.com/WordPress/gutenberg/pull/16250) at high zoom levels.
    *   Fix the [sticky notices](https://github.com/WordPress/gutenberg/pull/16255) at high zoom levels.

### Performance

*   Improve the performance of the [i18n Tannin library](https://github.com/WordPress/gutenberg/pull/16337).
*   Track the [block parent](https://github.com/WordPress/gutenberg/pull/16392) in the state to optimize hierarchy selectors.
*   Add a [cache key](https://github.com/WordPress/gutenberg/pull/16407) tracked in state to optimize the getBlock selector.

### Documentation

*   Document the [plugin release tool](https://github.com/WordPress/gutenberg/pull/16366).
*   Document the use-cases of the [dynamic blocks](https://github.com/WordPress/gutenberg/pull/16228).
*   Tweaks and typos: [1](https://github.com/WordPress/gutenberg/pull/16267), [2](https://github.com/WordPress/gutenberg/pull/16153), [3](https://github.com/WordPress/gutenberg/pull/16170), [4](https://github.com/WordPress/gutenberg/pull/16312), [5](https://github.com/WordPress/gutenberg/pull/16320), [6](https://github.com/WordPress/gutenberg/pull/16138).

### Various

*   Introduce a [PluginDocumentSettingPanel](https://github.com/WordPress/gutenberg/pull/13361) slot to allow third-party plugins to add panels to the document sidebar tab.
*   [Deploy the playground](https://github.com/WordPress/gutenberg/pull/16345) automatically to Github Pages. [https://wordpress.github.io/gutenberg/](https://wordpress.github.io/gutenberg/)
*   Extract a [generic RichText](http://try/move-rich-text) [component](https://github.com/WordPress/gutenberg/pull/16299) to the @wordpress/rich-text package.
*   Refactor the [editor initialization](https://github.com/WordPress/gutenberg/pull/15444) to rely on a component.
*   Remove unused internal [asType utility](https://github.com/WordPress/gutenberg/pull/16291).
*   Fix [react-no-unsafe-timeout ESlint rule](https://github.com/WordPress/gutenberg/pull/16292) when using variable assignment.
*   Add support for [watching block.json files](https://github.com/WordPress/gutenberg/pull/16150) when running “npm run dev”.
*   Remove: experimental status from [blockEditor.transformStyles](https://github.com/WordPress/gutenberg/pull/16126).
*   Upgrade [PHPCS composer dependencies](https://github.com/WordPress/gutenberg/pull/16387) and use [strict comparisons](https://github.com/WordPress/gutenberg/pull/16381) to align with the PHPCS guidelines.
*   Fix a small console warning when running [performance tests](https://github.com/WordPress/gutenberg/pull/16409).

### Mobile

*   Correct the position of the [block insertion indicator](https://github.com/WordPress/gutenberg/pull/16272).
*   Unify [Editor and Layout](https://github.com/WordPress/gutenberg/pull/16260) components with the web component hierarchy.


