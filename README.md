# WordPress Feed (WPFeed) for Moodle

WPFeed is the block for Moodle to easy output posts/news from external WordPress site to your Moodle site via [WordPress REST API plugin v2](https://wordpress.org/plugins/rest-api/). The plugin (block) is very flexible: output skins supporting (you can create an own one), a lot of useful settings, response caching and so on.

![WPFeed logo](https://i.imgsafe.org/67e742b.png)

## Getting Started

You will be interested about the plugin if you have an external site (for customers or promoting your internal e-learning Moodle project) based on [WordPress CMS](https://wordpress.org/). Block settings enabled only for Moodle administrators.

1.	Install and activate [WordPress REST API plugin v2](https://wordpress.org/plugins/rest-api/) on your WordPress external site
2.	Make sure it works. Visit `http(s)://yoursite.com/wp-json/wp/v2/posts`. It should to show JSON-based array with site posts
3.	*(optional)* Customize your API namespace
4.	*(Download and)* install WPFeed on your Moodle as a block (via FTP - `/blocks/wpfeed` folder or via Moodle based plugins installer). Make sure to activate the plugin
5.	Insert the block to your Moodle pages via edit mode
6.	Go to block settings. **Administration -> Site administration -> Plugins -> Blocks -> WordPress Feed**. Lets talk more about settings:
  - **Block title**: You can set your own title for a block, i.e. `My WordPress news`
  - **WordPress site URL**: Your WordPress external website URL, i.e. `http(s)://yoursite.com` **without slashes**
  - **WordPress REST API URI prefix**: WP REST API namespace (prefix). Default is `wp-json/wp/v2`. *You can customize prefix at WordPress functions.php*
  - **WordPress post type**: Post type to output (make request to). [WordPress supports several post types](https://codex.wordpress.org/Post_Types) and also custom post types. Default post type is `posts`. So if your site has custom post type, you can change this setting to the one.
  - **Caching interval**: [(Moodle-based) cache](https://docs.moodle.org/28/en/Caching) update interval in MINUTES. It recommended to use caching to optimize and secure WordPress site. In development mode recommended to disable caching: `0 = no caching`.
  - **Store response in user session**: additional *(optional)* cache method to user Moodle-based `$SESSION` object
  - **WordPress posts categories id-s**: If you want to get posts from specified WordPress-based category id(s) separated by `,`
  - **Show posts thumbnails**: Option to enable/disable thumbnail show
  - **Thumbnail size**: WordPress-based [thumbnail size handler](https://codex.wordpress.org/Post_Thumbnails). Default is `thumbnail` handler
  - **Thumbnail width**: HTML width (px) attribute of the thumbnail
  - **Thumbnail is a link**: Make thumbnail as a link or not
  - **Posts limit**: Number of (last) posts to output. Default is `5`
  - **Post date format**: Post date format. Based on [PHP date function](http://php.net/manual/en/function.date.php).
  - **Content excerpt length**: Symbols length of post content excerpt
  - **Output skin**: Name of skin handler
  - **Open links/posts in new window/tab**: HTML link attribute `target="_blank"`
  - **Enable noindex**: `<noindex>` tag enable or not. Default is `no (disabled)`. Useful for SEO sometimes.
  - **Developer mode**: If you just start to tuning the plugin and got some problems there, you need to turn on this option to catch all of API data in request and response. It will helps to find possible errors and catch the ones. NOTE: works only with Moodle-based developer mode: **Administration -> Site administration -> Development -> Debugging**

## Skins
Skin - output scheme of block posts list with own **PHP class, CSS and JS files**. The plugin has two base built-in skins located in `skins` folder:
- **default**
- **bootstrap**

### Built-in CSS classes to customize output styles without custom skins
General WPFeed elements in the block has general CSS classes, some elements has own CSS id. You can customize these styles just writing an external CSS code. Let's look at HTML structure of every WPFeed block content.

- **$SKIN_NAME**: output skin from block settings.
- **$POST_ID**: current post id from API
- **$POST_TITLE**: current post title from API

```html
<div class="block_wpfeed_wrapper block_wpfeed_wrapper_$SKIN_NAME">
    <ul class="block_wpfeed_list">
        <li id="block_wpfeed_list_item_$POST_ID" class="block_wpfeed_list_item block_wpfeed_list_item_$SKIN_NAME">
            <div class="block_wpfeed_list_item_wrapper block_wpfeed_list_item_wrapper_$SKIN_NAME">
                <!--some code from method $this->_item_wrapper_start() if specified in skin class-->
                <!--IF THUMBNAIL ENABLED-->
                <div class="block_wpfeed_thumbnail block_wpfeed_thumbnail_$SKIN_NAME">
                    <a href="if_thumbnail_as_link">
                        <img src="..." alt="..." some_attrs_like_width />
                    </a>
                </div>
                <!--/IF THUMBNAIL ENABLED-->
                <!--HTML CODE FROM $this->_output_item() skin method specified in skin class-->
                <!--some code from method $this->_item_wrapper_end() if specified in skin class-->
            </div>
        </li>
    </ul>
</div>
```

If you want to create an own skin with own HTML, CSS, JS - you're welcome, read next.

### Custom skins development
If you want to create an own output, don't edit base skins files, create the one! WPFeed supports custom skins: it's very simple if you know PHP bases and HTML. **Simple steps to create an own skin**:

#### 1. Create a new folder in your Moodle FTP directory named `wpfeed_skins`
Go to your Moodle server/hosting via console or FTP to the folder where located folders like *admin, blocks etc* and create a new one named `wpfeed_skins`
#### 2. Create subdirectory
Choose your custom skin name, ie `myskin`. So you need to create subdirectory with path `wpfeed_skins/myskin`
#### 3. Create PHP file with skin class extends to the base WPFeed skins PHP class `block_wpfeed_skins`
Create PHP file in your skin folder named `SKINNAME_skin.php`, in your case is `myskin_skin.php`

In this PHP file you need to create a PHP class extends from base WPFeed skins class `block_wpfeed_skins`. Code example:

```php
defined('MOODLE_INTERNAL') || die();

class block_wpfeed_skin_myskin extends block_wpfeed_skins {
    //code with overwritten code of some methods
}
```
**IMPORTANT**: your class name must be with template `block_wpfeed_skin_` + `SKINNAME`

#### 4. Overwrite some methods/functions of base skins class
Base skins class some methods that can be overwritten by skin developer:

**protected function _output_item( $post, $link_attrs )**: post item output function. Abstract in base class, need to be overwritten

  - **$post**: associated array with post item prepared data. Keys:
    - **id**: post internal id
    - **type**: post type
    - **link**: post link/url
    - **title**: post title
    - **sticky**: post sticky (true/false)
    - **content**: post content raw
    - **excerpt**: post excerpt raw
    - **excerpt_trimmed**: trimmed post content raw by plugin method **trim_text**. Symbols length taken from option **Content excerpt length**
    - **date**: post date from API
    - **date_time**: date time as UNIX timestamp string, ie *1456666480*
    - **date_time_str**: [PHP date function](http://php.net/manual/en/function.date.php) string with format from option **Post date format**
    - **date_gmt**: post GMT date from API
    - **date_gmt_time**: post GMT date as UNIX timestamp
    - **thumbnail_url**: HTTP-based path to the post thumbnail from API
    - **categories**: post categories prepared associative array (not API based). Category array item example: `[0] => array( 'id' => 123, 'name' => 'xyz', 'link' => 'http://mysite.com/category/posts/', 'slug' => 'posts', 'description' => 'Super category' );`
    - **comments**: post comments prepared associative array (not API based). Comment array item example: `[0] => array( 'id' => 123, 'text' => 'My comment is the best' );`
    - **comments_count**: post comments count

#### Example:
```php
protected function _output_item( array $post, $link_attrs = array() ) {
    $return = '';
    //$this->name = SKIN NAME
    $return .= html_writer::div( html_writer::link( $post['link'], $post['title'], $link_attrs ), 'block_wpfeed_title block_wpfeed_title_' . $this->name );
    $return .= html_writer::tag( 'small', $post['date_time'] );
    $return .= html_writer::tag( 'p', $post['excerpt_trimmed'] );
    $return .= html_writer::tag( 'p', 'Comments:' . $post['comments_count'] );
    
    return $return;
}
```

**protected function _item_wrapper_start()**: Custom HTML code after `<div class="block_wpfeed_list_item_wrapper">`

#### Example:
```php
protected function _item_wrapper_start() {
    return html_writer::start_div('divclass');
}
```

**protected function _item_wrapper_end()**: Custom HTML code before `<div class="block_wpfeed_list_item_wrapper">` closed

#### Example:
```php
protected function _item_wrapper_end() {
    return html_writer::end_div();
}
```

**NOTE**: It's highly recommended to use [Moodle based **html_writer** class](https://docs.moodle.org/dev/html_writer) to build HTML code strings.

#### 5. *(optional)* add custom CSS files
If you need to use custom CSS files in your skin, you can require the ones:

1. Create subfolder named **css** `wpfeed_skins/myskin/css`
2. Add CSS files in this folder

#### 6. *(optional)* add custom JS files

If you need to use custom JS files in your skin, you can require the ones:

1. Create subfolder named **js** `wpfeed_skins/myskin/js`
2. Add JS files in this folder

## Contributing
You're welcome for pull requests but against master branch. Thanks!

## Changelog
#### Version 1.0 (2016022800)
- initial release.

## License

WPFeed is licensed under [GNU General Public License v2 (or later)](http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).

## Author
Copyright 2016, [Igor Sazonov](https://twitter.com/tigusigalpa) (sovletig@yandex.ru)
