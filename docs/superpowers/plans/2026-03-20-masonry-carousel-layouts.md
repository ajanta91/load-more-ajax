# Masonry & Carousel Layouts Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add Style 4 (Masonry) and Style 5 (Carousel) layouts to Load More Ajax Lite plugin.

**Architecture:** Extend existing pattern — each layout gets its own CSS file, JS template function, admin radio option, and Elementor template. Masonry.js + imagesLoaded for masonry, Swiper.js for carousel. Libraries vendored locally, conditionally loaded.

**Tech Stack:** Masonry.js 4.2.2, imagesLoaded 5.0.0, Swiper.js 11.2.6, WordPress PHP, vanilla JS

**Spec:** `docs/superpowers/specs/2026-03-19-masonry-carousel-layouts-design.md`

**Note on innerHTML:** The JS templates use innerHTML consistent with existing codebase pattern (getPostTemplate/getPostTemplate3). Post data is sanitized server-side by WordPress before JSON response.

---

## File Structure

**New files:**
| File | Responsibility |
|------|---------------|
| `assets/vendor/masonry.pkgd.min.js` | Masonry.js library |
| `assets/vendor/imagesloaded.pkgd.min.js` | imagesLoaded library |
| `assets/vendor/swiper-bundle.min.js` | Swiper.js library |
| `assets/vendor/swiper-bundle.min.css` | Swiper base CSS |
| `assets/css/load-more-ajax-lite-s4.css` | Masonry layout styles |
| `assets/css/load-more-ajax-lite-s5.css` | Carousel styles + Swiper overrides |
| `elementor/widgets/templates/blog/blog-4.php` | Masonry Elementor template |
| `elementor/widgets/templates/blog/blog-5.php` | Carousel Elementor template |

**Modified files:**
| File | Changes |
|------|---------|
| `load-more-ajax-lite.php:175-189,436-452` | DB schema + asset registration |
| `inc/shortcodes.php:3-18,38-45` | New attrs + style enqueue branches |
| `inc/functions.php:151` | Expand block_style validation to 1-5 |
| `assets/js/load-more-ajax-modern.js:60-83,390-423,425-436` | Masonry/Swiper init, templates, reflow |
| `lib/admin/templates/post-block-new.php:73-135` | Style 4&5 radios, carousel settings |
| `lib/admin/templates/post-block-edit.php` | Same as new.php |
| `lib/admin/assets/css/admin.css` | Thumbnails for new styles |
| `lib/admin/assets/js/admin-script.js:33-46,188-200` | Conditional settings, preview |
| `lib/admin/AdminMenu.php:61-91` | Save carousel fields |
| `elementor/widgets/LMA_Blog.php:43-84,466` | Layout options, deps, carousel controls |

---

### Task 1: Vendor Libraries

**Files:**
- Create: `assets/vendor/masonry.pkgd.min.js`
- Create: `assets/vendor/imagesloaded.pkgd.min.js`
- Create: `assets/vendor/swiper-bundle.min.js`
- Create: `assets/vendor/swiper-bundle.min.css`

- [ ] **Step 1: Download Masonry.js 4.2.2**

```bash
curl -o assets/vendor/masonry.pkgd.min.js https://unpkg.com/masonry-layout@4.2.2/dist/masonry.pkgd.min.js
```

- [ ] **Step 2: Download imagesLoaded 5.0.0**

```bash
curl -o assets/vendor/imagesloaded.pkgd.min.js https://unpkg.com/imagesloaded@5.0.0/imagesloaded.pkgd.min.js
```

- [ ] **Step 3: Download Swiper 11.2.6**

```bash
curl -o assets/vendor/swiper-bundle.min.js https://unpkg.com/swiper@11.2.6/swiper-bundle.min.js
curl -o assets/vendor/swiper-bundle.min.css https://unpkg.com/swiper@11.2.6/swiper-bundle.min.css
```

- [ ] **Step 4: Verify files exist and are non-empty**

```bash
ls -la assets/vendor/
```

- [ ] **Step 5: Commit**

```bash
git add assets/vendor/
git commit -m "chore: vendor Masonry.js 4.2.2, imagesLoaded 5.0.0, Swiper.js 11.2.6"
```

---

### Task 2: Database Schema & Asset Registration

**Files:**
- Modify: `load-more-ajax-lite.php:175-189` (DB schema)
- Modify: `load-more-ajax-lite.php:436-452` (asset registration)

- [ ] **Step 1: Add carousel columns to CREATE TABLE in activate_info()**

In `load-more-ajax-lite.php`, find the CREATE TABLE SQL (~line 175-189). Add 4 new columns before the PRIMARY KEY:

```sql
slides_per_view INT DEFAULT 3,
show_arrows TINYINT(1) DEFAULT 1,
show_dots TINYINT(1) DEFAULT 1,
autoplay TINYINT(1) DEFAULT 1,
```

- [ ] **Step 2: Register new CSS assets**

After the existing `wp_register_style` calls (~line 438), add:

```php
wp_register_style( 'load-more-ajax-lite-s4', plugins_url('assets/css/load-more-ajax-lite-s4.css', __FILE__ ), array(), LOAD_MORE_AJAX_LITE_VERSION );
wp_register_style( 'load-more-ajax-lite-s5', plugins_url('assets/css/load-more-ajax-lite-s5.css', __FILE__ ), array(), LOAD_MORE_AJAX_LITE_VERSION );
wp_register_style( 'lma-swiper', plugins_url('assets/vendor/swiper-bundle.min.css', __FILE__ ), array(), '11.2.6' );
```

- [ ] **Step 3: Register new JS assets**

After the existing `wp_register_script` call (~line 447), add:

```php
wp_register_script( 'lma-masonry', plugins_url('assets/vendor/masonry.pkgd.min.js', __FILE__ ), array(), '4.2.2', true );
wp_register_script( 'lma-imagesloaded', plugins_url('assets/vendor/imagesloaded.pkgd.min.js', __FILE__ ), array(), '5.0.0', true );
wp_register_script( 'lma-swiper', plugins_url('assets/vendor/swiper-bundle.min.js', __FILE__ ), array(), '11.2.6', true );
```

- [ ] **Step 4: Commit**

```bash
git add load-more-ajax-lite.php
git commit -m "feat: add DB columns for carousel settings and register masonry/swiper assets"
```

---

### Task 3: Backend Validation & Shortcode

**Files:**
- Modify: `inc/functions.php:151` (validation)
- Modify: `inc/shortcodes.php:3-18,38-45` (attrs + enqueue)

- [ ] **Step 1: Expand block_style validation range**

In `inc/functions.php`, change the validation from range 1-3 to 1-5:

```php
// Change: validate_numeric($_POST['block_style'] ?? 1, 1, 3, 1)
// To:     validate_numeric($_POST['block_style'] ?? 1, 1, 5, 1)
```

- [ ] **Step 2: Add carousel shortcode attributes**

In `inc/shortcodes.php`, add to the `shortcode_atts` array (~line 3-18):

```php
'slides_per_view' => '3',
'show_arrows'     => 'true',
'show_dots'       => 'true',
'autoplay'        => 'true',
```

- [ ] **Step 3: Add style 4 & 5 enqueue branches**

In `inc/shortcodes.php`, after the `elseif ($style == '3')` block (~line 44), add:

```php
elseif ( $style == '4' ) {
    wp_enqueue_style( 'load-more-ajax-lite-s4' );
    wp_enqueue_script( 'lma-masonry' );
    wp_enqueue_script( 'lma-imagesloaded' );
} elseif ( $style == '5' ) {
    wp_enqueue_style( 'lma-swiper' );
    wp_enqueue_style( 'load-more-ajax-lite-s5' );
    wp_enqueue_script( 'lma-swiper' );
}
```

- [ ] **Step 4: Pass carousel data attributes in shortcode output**

Find where the shortcode renders the `ajaxpost_loader` div and add carousel data attributes when style=5. Also hide Load More button and category filter for style 5.

- [ ] **Step 5: Commit**

```bash
git add inc/functions.php inc/shortcodes.php
git commit -m "feat: add style 4/5 validation, shortcode attrs, and conditional asset loading"
```

---

### Task 4: Masonry CSS

**Files:**
- Create: `assets/css/load-more-ajax-lite-s4.css`

- [ ] **Step 1: Create masonry CSS**

Reuse Style 1 card classes. Key differences: no CSS grid on container (Masonry.js handles positioning), no fixed card height, margin-bottom for gap, responsive single-column on mobile. Include: container styles, card widths for column_2/3/4, gutter sizer, thumbnail styles, category overlay, content styles, meta styles, filter bar, load more button, responsive breakpoint at 768px.

- [ ] **Step 2: Commit**

```bash
git add assets/css/load-more-ajax-lite-s4.css
git commit -m "feat: add masonry layout CSS (style 4)"
```

---

### Task 5: Carousel CSS

**Files:**
- Create: `assets/css/load-more-ajax-lite-s5.css`

- [ ] **Step 1: Create carousel CSS**

Swiper base CSS handles mechanics. This file adds: card styling inside slides (matching Style 1 look), Swiper navigation arrow overrides (white circles with shadow, blue on hover), pagination dot overrides (active dot wider), image hover zoom effect, hide classes for no-arrows/no-dots, equal-height slides via flexbox.

- [ ] **Step 2: Commit**

```bash
git add assets/css/load-more-ajax-lite-s5.css
git commit -m "feat: add carousel layout CSS (style 5)"
```

---

### Task 6: Frontend JavaScript — Masonry

**Files:**
- Modify: `assets/js/load-more-ajax-modern.js`

- [ ] **Step 1: Add getPostTemplate4() after getPostTemplate3() (~line 509)**

Reuses Style 1 HTML: thumbnail with category overlay + content with title, meta (author avatar, date, read time), excerpt. Same as getPostTemplate() but always shows categories on image (like style 1).

- [ ] **Step 2: Update createPostElement() (~line 425-436)**

Add `else if (config.blockStyle === '4')` branch routing to `getPostTemplate4()`.

- [ ] **Step 3: Add initMasonry(instanceId) method**

- Add gutter sizer element if not present
- Wrap in `imagesLoaded()` callback
- Init `new Masonry()` with itemSelector, columnWidth, gutter, percentPosition
- Store instance as `instance.masonryInstance`

- [ ] **Step 4: Call initMasonry() in setupBlock() when blockStyle === '4'**

- [ ] **Step 5: Update renderPosts() (~line 390-423) for masonry reflow**

After appending posts for style 4: call `imagesLoaded()` then `masonry.appended(newElements)` + `masonry.layout()`.

- [ ] **Step 6: Update category filter handler for masonry**

After filter re-render: destroy masonry instance, re-init via `initMasonry()`.

- [ ] **Step 7: Commit**

```bash
git add assets/js/load-more-ajax-modern.js
git commit -m "feat: add masonry JS initialization, template, and reflow logic"
```

---

### Task 7: Frontend JavaScript — Carousel

**Files:**
- Modify: `assets/js/load-more-ajax-modern.js`

- [ ] **Step 1: Add getPostTemplate5()**

Same card HTML as style 4 (Style 1 card design).

- [ ] **Step 2: Update createPostElement() for style 5**

Add `else if (config.blockStyle === '5')` branch.

- [ ] **Step 3: Read carousel config in getBlockConfig()**

Add: slidesPerView, showArrows, showDots, autoplay from data attributes.

- [ ] **Step 4: Add initCarousel(instanceId) method**

- Early return if no posts or Swiper undefined
- Wrap existing posts in swiper > swiper-wrapper > swiper-slide structure
- Conditionally add navigation arrows and pagination dots
- Add CSS classes no-arrows/no-dots to wrapper when disabled
- Hide load more button
- Init Swiper with: slidesPerView 1, spaceBetween 24, loop (only if posts > slidesPerView), autoplay (3000ms, pauseOnMouseEnter), navigation, pagination, breakpoints (768: min(2, config), 1024: config)
- Store as instance.swiperInstance

- [ ] **Step 5: Call initCarousel() in setupBlock() when blockStyle === '5'**

- [ ] **Step 6: Skip Load More setup for carousel**

In setupLoadMoreButton(), add early return when blockStyle === '5'.

- [ ] **Step 7: Commit**

```bash
git add assets/js/load-more-ajax-modern.js
git commit -m "feat: add carousel JS initialization with Swiper, template, and config"
```

---

### Task 8: Admin UI — Style Selector & Carousel Settings

**Files:**
- Modify: `lib/admin/templates/post-block-new.php:73-135`
- Modify: `lib/admin/templates/post-block-edit.php`
- Modify: `lib/admin/assets/css/admin.css`
- Modify: `lib/admin/assets/js/admin-script.js`

- [ ] **Step 1: Add Style 4 & 5 radio options to post-block-new.php**

After Style 3 div (~line 108). Style 4 "Masonry" with CSS thumbnail showing 3 columns of staggered-height items. Style 5 "Carousel" with CSS thumbnail showing slides + arrows + dots.

- [ ] **Step 2: Add carousel settings section to post-block-new.php**

After column selector. Contains: slides_per_view select (1-4, default 3), show_arrows toggle, show_dots toggle, autoplay toggle. Hidden when style !== 5.

- [ ] **Step 3: Apply same changes to post-block-edit.php**

- [ ] **Step 4: Add CSS for masonry/carousel thumbnails and carousel settings in admin.css**

Masonry thumb: flex columns with staggered heights. Carousel thumb: slides with arrows and dots. Settings: form rows with labels and controls.

- [ ] **Step 5: Update admin-script.js style click handler**

Show/hide carousel settings, column selector, and filter toggle based on selected style. Limit column options for masonry.

- [ ] **Step 6: Update buildPreviewHTML() for new styles**

Add masonry and carousel preview representations.

- [ ] **Step 7: Commit**

```bash
git add lib/admin/templates/post-block-new.php lib/admin/templates/post-block-edit.php lib/admin/assets/css/admin.css lib/admin/assets/js/admin-script.js
git commit -m "feat: add masonry/carousel style options and carousel settings to admin UI"
```

---

### Task 9: Admin Save Handler

**Files:**
- Modify: `lib/admin/AdminMenu.php:61-91`

- [ ] **Step 1: Add carousel fields to $data array**

```php
"slides_per_view" => intval($_POST['slides_per_view'] ?? 3),
"show_arrows"     => isset($_POST['show_arrows']) ? 1 : 0,
"show_dots"       => isset($_POST['show_dots']) ? 1 : 0,
"autoplay"        => isset($_POST['autoplay']) ? 1 : 0,
```

- [ ] **Step 2: Clamp columns for masonry**

If block_style == 4 and column value < 3 (i.e. more than 4 cols), clamp to 3.

- [ ] **Step 3: Commit**

```bash
git add lib/admin/AdminMenu.php
git commit -m "feat: save carousel settings and clamp masonry columns"
```

---

### Task 10: Elementor Widget

**Files:**
- Modify: `elementor/widgets/LMA_Blog.php:43-84,466`
- Create: `elementor/widgets/templates/blog/blog-4.php`
- Create: `elementor/widgets/templates/blog/blog-5.php`

- [ ] **Step 1: Add layout options 4 & 5 in LMA_Blog.php**

Add to options: `'4' => 'Masonry'`, `'5' => 'Carousel'`

- [ ] **Step 2: Add carousel controls with condition layout=5**

slides_per_view (SELECT), show_arrows (SWITCHER), show_dots (SWITCHER), show_autoplay (SWITCHER)

- [ ] **Step 3: Update get_style_depends()**

Preview mode: add s4, s5, lma-swiper. Add branches for layout 4 and 5.

- [ ] **Step 4: Update get_script_depends()**

Conditionally return masonry/imagesloaded for layout 4, swiper for layout 5.

- [ ] **Step 5: Create blog-4.php**

Copy blog-1.php, change class to `lma_block_style_4`. Keep category filter and load more button.

- [ ] **Step 6: Create blog-5.php**

Carousel template: no cat_filter, no load more button. Add carousel data attributes (slides_per_view, show_arrows, show_dots, autoplay). Add no-arrows/no-dots classes conditionally.

- [ ] **Step 7: Pass carousel settings to template in render()**

Extract settings and make available as template variables before the require.

- [ ] **Step 8: Commit**

```bash
git add elementor/widgets/LMA_Blog.php elementor/widgets/templates/blog/blog-4.php elementor/widgets/templates/blog/blog-5.php
git commit -m "feat: add masonry/carousel Elementor widget options and templates"
```

---

### Task 11: Integration Testing & Fixes

- [ ] **Step 1: Test masonry via shortcode** — `[load_more_ajax_lite style="4" column="3"]`
- [ ] **Step 2: Test carousel via shortcode** — `[load_more_ajax_lite style="5" slides_per_view="3"]`
- [ ] **Step 3: Test admin UI** — create/edit blocks with styles 4 & 5
- [ ] **Step 4: Test Elementor widget** — layouts 4 & 5 in Elementor editor + frontend
- [ ] **Step 5: Fix any issues found**
- [ ] **Step 6: Final commit**
