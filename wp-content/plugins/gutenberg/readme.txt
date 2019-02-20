=== Gutenberg ===
Contributors: matveb, joen, karmatosed
Requires at least: 5.0.0
Tested up to: 5.0
Stable tag: 5.0.0
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

= Latest =

## Features

*   Add a new [Search block](https://github.com/WordPress/gutenberg/pull/13583).
*   Add a new [Calendar](https://github.com/WordPress/gutenberg/pull/13772) block.
*   Add a new [Tag Cloud](https://github.com/WordPress/gutenberg/pull/7875) block.

## Enhancements

*   Add micro-animations to the editor UI:
    *   Opening [Popovers](https://github.com/WordPress/gutenberg/pull/13617).
    *   Opening [Sidebars](https://github.com/WordPress/gutenberg/pull/13635).
*   [Restore the block movers](https://github.com/WordPress/gutenberg/pull/12758) for the floated blocks.
*   [Consistency in alignment options](https://github.com/WordPress/gutenberg/pull/9469) between archives and categories blocks.
*   Set the minimum size for [form fields on mobile](https://github.com/WordPress/gutenberg/pull/13639).
*   [Disable the block navigation](https://github.com/WordPress/gutenberg/pull/12185) in the code editor mode.
*   Consistency for the [modal styles](https://github.com/WordPress/gutenberg/pull/13669).
*   Improve the [FormToggle](https://github.com/WordPress/gutenberg/pull/12385) styling when used outside of WordPress context.
*   Use the block [icons in the media placeholders](https://github.com/WordPress/gutenberg/pull/11788).
*   Fix [rounded corners](https://github.com/WordPress/gutenberg/pull/13659) for the block svg icons.
*   Improve the [CSS specificity](https://github.com/WordPress/gutenberg/pull/13025) [of the paragraph](https://github.com/WordPress/gutenberg/pull/12998) block [styles](https://github.com/WordPress/gutenberg/pull/13821).
*   Require an initial [click on embed previews](https://github.com/WordPress/gutenberg/pull/12981) before being interactive.
*   Improve the [disabled block switcher](https://github.com/WordPress/gutenberg/pull/13721) styles.
*   [Do not split paragraph line breaks](https://github.com/WordPress/gutenberg/pull/13832) when transforming multiple paragraphs to a list.
*   Enhance the Quote block styling for [different text alignments](https://github.com/WordPress/gutenberg/pull/13248).
*   Remove the [left padding from the Quote](https://github.com/WordPress/gutenberg/pull/13846) block when it’s centered.
*   A11y:
    *   Improve the [permalink field label](https://github.com/WordPress/gutenberg/pull/12959).
    *   Improve the [region navigation](https://github.com/WordPress/gutenberg/pull/8554) styling.
*   Remove the [3 keywords limit](https://github.com/WordPress/gutenberg/pull/13848) for the block registration.
*   Add consistent background colors to the [hovered menu items](https://github.com/WordPress/gutenberg/pull/13732).
*   Allow the [editor notices to push down](https://github.com/WordPress/gutenberg/pull/13614) the content.
*   Rename the [default block styles](https://github.com/WordPress/gutenberg/pull/13670).

## Bug Fixes

*   Fix a number of formatting issues:
    *   [Multiple formats](https://github.com/WordPress/gutenberg/issues/12973).
    *   [Flashing backgrounds](https://github.com/WordPress/gutenberg/issues/12978) when typing.
    *   [Highlighted format](https://github.com/WordPress/gutenberg/issues/11091) buttons.
    *   [Inline code](https://github.com/WordPress/gutenberg/pull/13807) with [backticks](https://github.com/WordPress/gutenberg/issues/11276).
    *   [Spaces deleted](https://github.com/WordPress/gutenberg/issues/12529) after formats.
    *   Inline [boundaries styling](https://github.com/WordPress/gutenberg/issues/11423) issues.
    *   [Touch Bar](https://github.com/WordPress/gutenberg/pull/13833) format buttons.
*   Fix a number of list block writing flow issues:
    *   Allow [line breaks](https://github.com/WordPress/gutenberg/pull/13546) in list items.
    *   [Empty items](https://github.com/WordPress/gutenberg/issues/13864) not being removed.
    *   Backspace [merging list items](https://github.com/WordPress/gutenberg/issues/12398).
    *   [Selecting formats](https://github.com/WordPress/gutenberg/issues/11741) at the beginning of list items.
*   Fix the [color picker styling](https://github.com/WordPress/gutenberg/pull/12747).
*   Set default values for the [image dimensions inputs](https://github.com/WordPress/gutenberg/pull/7687).
*   Fix [sidebar panels spacing](https://github.com/WordPress/gutenberg/pull/13181).
*   Fix [wording of the nux tip](https://github.com/WordPress/gutenberg/pull/12911) nudging about the sidebar settings.
*   Fix [the translator comments](https://github.com/WordPress/gutenberg/pull/9440) pot extraction.
*   Fix the [plugins icons](https://github.com/WordPress/gutenberg/pull/13719) color overriding.
*   Fix [conflicting notices styles](https://github.com/WordPress/gutenberg/pull/13817) when using editor styles.
*   Fix [controls recursion](https://github.com/WordPress/gutenberg/pull/13818) in the redux-routine package.
*   Fix the generic embed block when using [Giphy as provider](https://github.com/WordPress/gutenberg/pull/13825).
*   Fix the [i18n message](https://github.com/WordPress/gutenberg/pull/13830) used in the Gallery block edit button.
*   Fix the [icon size](https://github.com/WordPress/gutenberg/pull/13767) of the block switcher menu.
*   Fix the [loading state](https://github.com/WordPress/gutenberg/pull/13758) of the FlatTermSelector (tags selector).
*   Fix the [embed placeholders](https://github.com/WordPress/gutenberg/pull/13590) styling.
*   Fix incorrectly triggered [auto-saves for published posts](https://github.com/WordPress/gutenberg/pull/12624).
*   Fix [missing classname](https://github.com/WordPress/gutenberg/pull/13834) in the Latest comments block.
*   Fix [HTML in shortcodes](https://github.com/WordPress/gutenberg/pull/13609) breaking block validation.
*   Fix JavaScript errors when [typing quickly](https://github.com/WordPress/gutenberg/pull/11209) and creating undo levels.
*   Fix issue with [mover colors](https://github.com/WordPress/gutenberg/pull/13869) in dark themes.
*   Fix [internationalisation issue](https://github.com/WordPress/gutenberg/pull/13551) with permalink slugs.

## Various

*   Implement the [inline format boundaries](https://github.com/WordPress/gutenberg/pull/13697) without relying on the DOM.
*   Introduce the [Registry Selectors](https://github.com/WordPress/gutenberg/pull/13662) in the data module.
*   Introduce the [Registry Controls](https://github.com/WordPress/gutenberg/pull/13722) in the data module.
*   Allow extending the [latest posts block query](https://github.com/WordPress/gutenberg/pull/11984) by using get_posts.
*   Extend the [range of allowed years](https://github.com/WordPress/gutenberg/pull/13602) in the DateTime component.
*   Allow [null values](https://github.com/WordPress/gutenberg/pull/12963) for the DateTime component.
*   Do not render the [FontSizePicker](https://github.com/WordPress/gutenberg/pull/13782) if [no sizes](https://github.com/WordPress/gutenberg/pull/13824) [defined](https://github.com/WordPress/gutenberg/pull/13844).
*   Add className prop support to the [UrlInput](https://github.com/WordPress/gutenberg/pull/13800) component.
*   Add [inline image resizing UI](https://github.com/WordPress/gutenberg/pull/13737).

## Chore

*   Update [lodash](https://github.com/WordPress/gutenberg/pull/13651) and [deasync](https://github.com/WordPress/gutenberg/pull/13839) [dependencies](https://github.com/WordPress/gutenberg/pull/13876).
*   Use [addQueryArgs](https://github.com/WordPress/gutenberg/pull/13653) consistently to generate WordPress links.
*   Remove merged PHP code:
    *   jQuery to Hooks [heartbeat proxyfying](https://github.com/WordPress/gutenberg/pull/13576).
    *   References to the [classic editor](https://github.com/WordPress/gutenberg/pull/13544).
    *   [gutenberg_can_edit_post](https://github.com/WordPress/gutenberg/pull/13470) function.
*   [Disable CSS](https://github.com/WordPress/gutenberg/pull/13769) [animations](https://github.com/WordPress/gutenberg/pull/13779) in e2e tests.
*   ESLint
    *   Add a rule to ensure the [consistency](https://github.com/WordPress/gutenberg/pull/13785) [of the import groups](https://github.com/WordPress/gutenberg/pull/13757).
    *   Add a rule to protect against [invalid sprintf use](https://github.com/WordPress/gutenberg/pull/13756).
*   Remove [obsolete](https://github.com/WordPress/gutenberg/pull/13871) [CSS](https://github.com/WordPress/gutenberg/pull/13867) rules.
*   Add e2e tests for [tags creation](https://github.com/WordPress/gutenberg/pull/13129).
*   Add the [feature flags](https://github.com/WordPress/gutenberg/pull/13324) setup.
*   Implement [block editor styles](https://github.com/WordPress/gutenberg/pull/13625) using a filter.

## Documentation

*   Add a new [tutorial about the editor notices](https://github.com/WordPress/gutenberg/pull/13703).
*   Add JavaScript [build tools](https://github.com/WordPress/gutenberg/pull/13629) [documentation](https://github.com/WordPress/gutenberg/pull/13853).
*   Enhance the block’s [edit/save documentation](https://github.com/WordPress/gutenberg/pull/13578) and code examples.
*   Use [Title Case](https://github.com/WordPress/gutenberg/pull/13714) consistently.
*   Add [e2e test utils](https://github.com/WordPress/gutenberg/pull/13856) documentation.
*   Small enhancements and typos: [1](https://github.com/WordPress/gutenberg/pull/13593), [2](https://github.com/WordPress/gutenberg/pull/13671), [3](https://github.com/WordPress/gutenberg/pull/13711), [4](https://github.com/WordPress/gutenberg/pull/13746), [5](https://github.com/WordPress/gutenberg/pull/13742), [6](https://github.com/WordPress/gutenberg/pull/13733), [7](https://github.com/WordPress/gutenberg/pull/13744), [8](https://github.com/WordPress/gutenberg/pull/13752), [9](https://github.com/WordPress/gutenberg/pull/13574), [10](https://github.com/WordPress/gutenberg/pull/13745), [11](https://github.com/WordPress/gutenberg/pull/13781), [12](https://github.com/WordPress/gutenberg/pull/13694), [13](https://github.com/WordPress/gutenberg/pull/13810), [14](https://github.com/WordPress/gutenberg/pull/13891).

## Mobile

*   Add bottom sheet settings for the image block:
    *   [alt description](https://github.com/WordPress/gutenberg/pull/13631).
    *   [Links](https://github.com/WordPress/gutenberg/pull/13654).
*   Implement [the media upload options](https://github.com/WordPress/gutenberg/pull/13656) sheet.
*   Implementing [Clear All Settings](https://github.com/WordPress/gutenberg/pull/13753) button on Image Settings.
*   [Avoid hard-coded font family](https://github.com/WordPress/gutenberg/pull/13677) styling for the image blocks.
*   Improve [the post title](https://github.com/WordPress/gutenberg/pull/13548) [component](https://github.com/WordPress/gutenberg/pull/13874).
*   Fix the bottom sheet [styling for RTL](https://github.com/WordPress/gutenberg/pull/13815) layouts.
*   Support the [placeholder](https://github.com/WordPress/gutenberg/pull/13699) [prop](https://github.com/WordPress/gutenberg/pull/13738) in the RichText component.
