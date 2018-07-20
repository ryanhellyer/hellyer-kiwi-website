=== Gutenberg ===
Contributors: matveb, joen, karmatosed
Requires at least: 4.9.6
Tested up to: 4.9.6
Stable tag: 3.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A new editing experience for WordPress is in the works, with the goal of making it easier than ever to make your words, pictures, and layout look just right. This is the beta plugin for the project.

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

Check out the <a href="https://github.com/WordPress/gutenberg/blob/master/docs/faq.md">FAQ</a> for answers to the most common questions about the project.

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
- <a href="http://gutenberg-devdoc.surge.sh/reference/design-principles/">Design Principles and block design best practices</a>
- <a href="https://github.com/Automattic/wp-post-grammar">WP Post Grammar Parser</a>
- <a href="https://make.wordpress.org/core/tag/gutenberg/">Development updates on make.wordpress.org</a>
- <a href="http://gutenberg-devdoc.surge.sh/">Documentation: Creating Blocks, Reference, and Guidelines</a>
- <a href="https://github.com/WordPress/gutenberg/blob/master/docs/faq.md">Additional frequently asked questions</a>


== Changelog ==

= Latest =

* Add new Archives block for displaying site archives.
* Add new Latest Comments block to widgets category.
* Add “Convert to blocks” option in HTML block.
* Correct caret placement when merging to inline boundary.
* Move block switcher from header to multi-block toolbar for multiselection.
* Add video block attributes for Autoplay, Controls, Loop, Muted.
* Remove HTML beautification and preserve whitespace on save.
* Formalize RichText children value abstraction.
* Allow transformation of image block to file block and vice-versa.
* Support preload attribute for Audio Block.
* Avoid popover refresh on Tip mount.
* Introduce “registry” concept to the Data Module.
* Convert successive shortcodes properly.
* Hide “Convert to Shared Block” button on Classic blocks.
* Update spacing in pre-publish panel titles.
* Use do_blocks to render core blocks content.
* Remove restoreContentAndSplit in RichText.
* Hide insertion point when it is not possible to insert the default block.
* Refactor block converters to share common UI functionality.
* Replace the apiRequest module with api-fetch module.
* Add audio/video settings title to settings panel.
* Normalize the behavior of BlockListBlock’s “Enter” key handling to insert the default block.
* Rename baseUrl entities property as baseURL in entities.
* Rename UrlInput component as URLInput.
* Give File block a low files transform priority.
* Make tooltips persist when hovering them.
* Optimise design of heading line heights.
* Add a filter(‘editor.FeaturedImage’) for the FeaturedImage component.
* Fix vertical arrow navigation skips in writing flow.
* Fix incorrect polyfill script handles.
* Fix template example so that it is correct.
* Fix exception error when saving a new shared block.
* Fix getInserterItems caching bug and add new test case.
* Fix issue with spacer block resizing and sibling inserter.
* Fix files configuration entry in package.json for wordpress/babel-preset-default.
* Fix config and regenerate updated docs.
* Fix dependency mistake in api-fetch.
* Fix metaboxes save request (parse: false).
* Fix issue with name field not being focused when a shared block is created.
* Fix box sizing for pseudo elements.
* Fix an error which occurs when assigning the URL of a Button block.
* Improve usage and documentation of the landmark region labels.
* Substitute the remaining uses of unfiltered_html capability and withAPIData.
* Remove the “Extended Settings” meta box wrapper.
* Remove NewBlock event handling from RichText.
* Remove legacy context API child context from Block API.
* Remove Text Columns block from insertion menus in preparation for Try outreach.
* Remove unused autocompleter backcompat case.
* Change label in Cover Image block for background opacity.
* Change the text label on Image block from “Source Type” to “Image Size”.
* Backup and restore global $post when preloading API data.
* Move packages repository into Gutenberg with its history.
* Enhance the deprecated module to log a message only once per session.
* Switch tests away from using enzyme (enzyme.shallow, enzyme.mount, etc).
* Unblock tests from being skipped.
* Add basic test for shortcode transformation.
* Add e2e test for block icons.
* Add e2e tests for the NUX tips.
* Add e2e tests for shared blocks.
* Remove data-test attribute from UrlInputButton output.
* Deprecate id prop in favor of clientId.
* Rename MediaPlaceholder onSelectUrl prop as onSelectURL.
* Remove unnecessary default prop from test.
* Point the package entry to src directly for native mobile.
* Use clearer filenames for saved vendor scripts.
* Update local install instructions and add add more verbose instructions when node versions don’t match.
* Reorder package.json devDependencies alphabetically.
* Coding Guidelines: Prescribe specific camelCasing behaviors.
* Regenerate docs using docs:build command.
* Add documentation for ALLOWED_BLOCKS in Columns.
* Add link to support forum in plugin menu.
* Deprecate buildTermTree function in utilities.
* Deprecate property source in Block API.
* Deprecate uid in favor of clientId.
* Deprecate grouped inner blocks layouts.
* Improve eslint checks for deep imports.
* Improve IntelliSense support when using VS Code.
* Move the components module partially to the packages folder.
* Add the blocks module to the packages folder.
* Add wp-deprecated dependency to wp-element.
* Add @babel/runtime as a dependency to wordpress/components.
* Add @babel/runtime as a dependency for packages.
* Add a new compose package.
* Extract entities package.
* Extract viewport package.
* Extract @wordpress/nux package.
* Create new spec-parser package.
* Update Dashicons to latest build.
* Update test for babel-preset-default.
* Update code to work with Babel 7.
* Update package-lock.json with eslint-scope version 3.7.3.
* Update node-sass.
