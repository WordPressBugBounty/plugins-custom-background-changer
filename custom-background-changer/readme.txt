=== Custom Background Changer ===
Contributors: anshuln90
Donate link: http://www.paypal.me/anshulgangrade
Tags: custom background, background changer, gradient background, video background, page background
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set a unique background color, gradient, image, overlay, or video for any individual post or page — all from a single elegant meta box.

== Description ==

**Custom Background Changer** lets you control the background of every post and page individually. Go far beyond plain colors with beautiful CSS gradients, full-screen background images, translucent color overlays, and looping MP4 video backgrounds — all from a premium tabbed settings panel right inside the post editor.

**Features in v4.0**

* **Solid Color** - Pick any background color via the color picker.
* **CSS Gradient** - Pick two colors and an angle to generate a smooth linear gradient.
* **Background Image** - Upload an image with full control over attachment, repeat, position, and size (including cover/contain).
* **Color Overlay** - Add a translucent color layer on top of your image or video to improve text readability.
* **MP4 Video Background** - Set a looping, auto-playing, muted video as your page background.
* **Performance** - CSS and JS assets load only on post/page edit screens in the admin. Zero impact on all other pages.
* **Secure** - All input is sanitized. All output is properly escaped. Nonce-protected saves.

== Installation ==

1. Upload the `custom-background-changer` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Edit any post or page and look for the **Custom Background Changer** meta box.
4. Enable it using the checkbox, choose your settings, and publish/update the post.

== Frequently Asked Questions ==

= Does the video background have sound? =
No. The video is set to `muted` and `autoplay` with `loop`, ensuring a seamless, silent background experience.

= Can I combine a video with an overlay? =
Yes! The Overlay tab works on top of both video and image backgrounds.

= Does this work with Gutenberg? =
Yes. The plugin uses a standard meta box which appears in both the Classic Editor and the Gutenberg sidebar.

== Changelog ==

= 4.0 =
* NEW: CSS Gradient background support with angle control and live preview.
* NEW: Color Overlay with opacity slider for images and videos.
* NEW: MP4 Video Background support (autoplay, loop, muted).
* NEW: Background size control (auto/cover/contain).
* IMPROVED: Premium tabbed admin UI for easy organization.
* IMPROVED: Live gradient preview directly in the admin.

= 3.0 =
* SECURITY: Patched XSS vulnerabilities.
* SECURITY: Added capability checks.
* PERFORMANCE: Conditional asset loading on edit screens only.
* COMPATIBILITY: Tested up to WordPress 7.0.

= 2.0 =
Fixed some bugs.
Change metabox layout.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 4.0 =
Major feature update with gradients, video backgrounds, and overlay support.