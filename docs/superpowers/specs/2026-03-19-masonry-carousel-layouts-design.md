# Masonry & Carousel Layout Design Spec

**Date:** 2026-03-19
**Status:** Approved
**Task:** Add masonry and carousel/slider layouts to Load More Ajax Lite

---

## Summary

Add two new blog post layouts (Style 4: Masonry, Style 5: Carousel) following the existing pattern where each layout has its own CSS file, JS template function, admin radio option, and Elementor template. Third-party libraries: Masonry.js + imagesLoaded.js for masonry, Swiper.js for carousel.

---

## Layout Specifications

### Style 4 — Masonry

- True Pinterest-style masonry using Masonry.js + imagesLoaded.js
- Columns: 2, 3, 4 (no 5/full — cards become too narrow)
- Reuses Style 1 card design (thumbnail + categories overlay + title + meta)
- Category filter enabled — reflows grid on filter change
- Load More button appends posts then triggers `masonry.appended()` + `masonry.layout()` reflow
- Responsive: 1 column on mobile (<768px), configured columns on desktop

### Style 5 — Carousel/Slider

- Swiper.js powered slider
- Configurable slides per view: 1, 2, 3, 4 (default: 3)
- Navigation: arrows + dots, each toggleable in admin (both default on)
- Autoplay with pause-on-hover (default on, 3000ms interval)
- Reuses Style 1 card design
- No category filter (bad UX in slider context)
- No Load More button — all posts loaded into slider at once (capped at posts_per_page setting)
- Responsive breakpoints: 1 slide (<768px), 2 slides (768-1024px), configured count (>1024px)
- If fewer posts than slides_per_view: Swiper `loop: false`, slides left-aligned
- Empty state: if 0 posts, hide the carousel container entirely (no Swiper init)

---

## New Files

| File | Purpose |
|------|---------|
| `assets/css/load-more-ajax-lite-s4.css` | Masonry layout styles |
| `assets/css/load-more-ajax-lite-s5.css` | Carousel layout styles + Swiper overrides |
| `assets/vendor/masonry.pkgd.min.js` | Masonry.js library |
| `assets/vendor/imagesloaded.pkgd.min.js` | imagesLoaded library |
| `assets/vendor/swiper-bundle.min.js` | Swiper.js library |
| `assets/vendor/swiper-bundle.min.css` | Swiper base CSS |
| `elementor/widgets/templates/blog/blog-4.php` | Masonry Elementor template |
| `elementor/widgets/templates/blog/blog-5.php` | Carousel Elementor template |

## Modified Files

| File | Changes |
|------|---------|
| `load-more-ajax-lite.php` | Register new CSS/JS assets with conditional enqueue |
| `assets/js/load-more-ajax-modern.js` | Add `getPostTemplate4()`, `getPostTemplate5()`, masonry init/reflow, Swiper init |
| `inc/shortcodes.php` | Add style 4 & 5 CSS/JS enqueue branches, new carousel shortcode attributes |
| `inc/functions.php` | Allow block_style values 4 and 5 in validation |
| `lib/admin/templates/post-block-new.php` | Add Style 4 & 5 radio options with CSS thumbnails |
| `lib/admin/templates/post-block-edit.php` | Same for edit form |
| `lib/admin/assets/css/admin.css` | Thumbnail previews for masonry/carousel in style selector |
| `lib/admin/assets/js/admin-script.js` | Live preview for new styles, carousel settings toggle visibility |
| `elementor/widgets/LMA_Blog.php` | Add styles 4 & 5 to layout control, conditional asset deps, carousel controls |

---

## Admin UI

### Style Selector

Two new radio options added to the visual style selector:
- Style 4: "Masonry" — CSS-drawn thumbnail showing staggered-height cards
- Style 5: "Carousel" — CSS-drawn thumbnail showing slider with arrows

### Conditional Settings

**When Masonry (style 4) is selected:**
- Column selector limited to 2, 3, 4

**When Carousel (style 5) is selected:**
- Slides per view: dropdown (1, 2, 3, 4) — default 3
- Show arrows: toggle switch — default on
- Show dots: toggle switch — default on
- Autoplay: toggle switch — default on
- Column selector hidden (not applicable)
- Category filter toggle hidden (always disabled)

### Live Preview

Preview panel updates to reflect masonry stagger and carousel slide appearance.

### Database

No schema changes needed. `block_style` column (INT) already exists, accepts 4 and 5. Carousel settings stored as new columns in `wp_load_more_post_shortcode_list`:

| Column | Type | Default | Notes |
|--------|------|---------|-------|
| `slides_per_view` | INT | 3 | Carousel only (1-4) |
| `show_arrows` | TINYINT(1) | 1 | Carousel only |
| `show_dots` | TINYINT(1) | 1 | Carousel only |
| `autoplay` | TINYINT(1) | 1 | Carousel only |

These columns are added via `dbDelta()` in the plugin's activation hook (existing pattern). For existing installs, `dbDelta()` adds missing columns with defaults — no migration needed.

**Column fallback for masonry:** If a block has columns=5 and user switches to style 4, clamp to 4 on save.

---

## Shortcode

New attributes for carousel:

```
[load_more_ajax_lite
  style="4"                    // Masonry
  style="5"                    // Carousel
  slides_per_view="3"          // Carousel only, default 3
  show_arrows="true"           // Carousel only, default true
  show_dots="true"             // Carousel only, default true
  autoplay="true"              // Carousel only, default true
]
```

Asset enqueue by style:
- Style 4: `load-more-ajax-lite-s4.css` + `masonry.pkgd.min.js` + `imagesloaded.pkgd.min.js`
- Style 5: `load-more-ajax-lite-s5.css` + `swiper-bundle.min.css` + `swiper-bundle.min.js`

Column attribute works for masonry (clamped to 2-4), ignored for carousel.

---

## Elementor Widget

### New Controls in LMA_Blog.php

- Layout select: add `'4' => 'Masonry'` and `'5' => 'Carousel'`
- Carousel section (condition: `layout === '5'`):
  - Slides per view (select: 1-4)
  - Show arrows (switcher)
  - Show dots (switcher)
  - Autoplay (switcher)
- `get_style_depends()` updated for styles 4 & 5 conditional loading
- `get_script_depends()` updated to include masonry/imagesLoaded or Swiper based on layout

### Elementor Templates

- `blog-4.php`: Same container structure as blog-1.php with `lma_block_style_4` class, Masonry.js initializes on it
- `blog-5.php`: Wraps posts in Swiper markup (`swiper` > `swiper-wrapper` > `swiper-slide`), reads carousel config from data attributes

---

## Frontend JavaScript

### Masonry (style 4)

1. `initializeBlocks()` detects `data-block_style="4"`, initializes Masonry.js on `.ajaxpost_loader`
2. `imagesLoaded` wraps masonry init to prevent layout shifts
3. `createPostElement()` routes to `getPostTemplate4()` — same HTML as Style 1 without fixed height
4. After Load More: `masonry.appended(newElements)` + `masonry.layout()`
5. Category filter: destroy masonry, re-render filtered posts, re-init masonry

### Carousel (style 5)

1. `initializeBlocks()` detects `data-block_style="5"`, wraps posts in Swiper markup
2. Reads config from data attributes: `data-slides_per_view`, `data-show_arrows`, `data-show_dots`, `data-autoplay`
3. Initializes Swiper with responsive breakpoints
4. `getPostTemplate5()` — Style 1 card wrapped in `swiper-slide` div
5. No Load More button rendered

### Elementor Integration

Same pattern as existing: `elementorFrontend` hooks + `data-lma-initialized` double-init guard. For Elementor editor preview: use `setTimeout` to delay init (container needs visible dimensions). Masonry uses `imagesLoaded` before layout. Swiper inits after Elementor's widget render event.

### Empty States

- **Masonry (0 posts):** Show empty container, no Masonry init
- **Carousel (0 posts):** Hide carousel container entirely, no Swiper init

### RTL

Deferred to Task 6 (Accessibility & i18n). For now, layouts must not break in RTL — Masonry defaults to LTR origin and Swiper defaults to LTR. No explicit RTL config added yet.

---

## CSS

### Masonry (`load-more-ajax-lite-s4.css`)

- No CSS Grid — Masonry.js uses absolute positioning
- Cards get `margin-bottom` for vertical gap
- Column width calculated by Masonry from container width / column count
- No fixed card height — natural content height drives stagger
- Reuses Style 1 card classes with minor overrides

### Carousel (`load-more-ajax-lite-s5.css`)

- Swiper base CSS handles slide mechanics
- Custom overrides: arrow styling, dot styling, slide spacing
- Cards inside slides reuse Style 1 structure
- Navigation arrows match plugin's existing design language
- Hover states on arrows/dots

### No changes to existing CSS files (styles 1-3 untouched)

---

## Dependencies

| Library | Version | Size | Loaded When |
|---------|---------|------|-------------|
| Masonry.js | 4.2.2 | ~8KB min+gzip | style=4 only |
| imagesLoaded | 5.0.0 | ~2KB min+gzip | style=4 only |
| Swiper.js | 11.2.6 | ~40KB min+gzip | style=5 only |

All vendored in `assets/vendor/` — no CDN dependency. Swiper uses the full bundle (modular build adds build-step complexity not warranted for a WordPress plugin).

**Browser support:** Modern browsers (Chrome, Firefox, Safari, Edge — last 2 versions). No IE support.

---

## Acceptance Criteria

- [ ] Masonry layout renders with true staggered heights using Masonry.js
- [ ] Masonry reflows correctly after Load More appends new posts
- [ ] Masonry reflows correctly after category filter change
- [ ] Carousel initializes with correct slide count per responsive breakpoint
- [ ] Carousel arrows and dots toggle on/off via admin settings
- [ ] Carousel autoplay works with pause-on-hover
- [ ] Carousel handles fewer posts than slides_per_view gracefully
- [ ] Both layouts render correctly in Elementor preview without page reload
- [ ] Switching styles in admin shows/hides conditional settings (carousel options, column selector)
- [ ] Assets (CSS/JS) only enqueued when the corresponding style is active on the page
- [ ] Shortcode attributes override admin defaults correctly
- [ ] Admin visual style selector shows CSS thumbnails for masonry and carousel
- [ ] Live preview panel updates for new styles
- [ ] Empty state (0 posts) handled without JS errors
- [ ] Responsive: 1 column/slide on mobile for both layouts

---

## Pro Upsell Boundary

- Free: 5 layouts (Classic Grid, List View, Modern Card, Masonry, Carousel)
- Pro: 12+ layouts (additional masonry variants, advanced carousel with thumbnails, timeline, etc.)
- Free: basic carousel settings (slides, arrows, dots, autoplay)
- Pro: advanced carousel (custom transitions, thumbnail navigation, video slides, lazy loading)
