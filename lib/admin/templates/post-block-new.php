<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Default block data — must be defined BEFORE referencing $block_data keys
$block_data = array(
    'block_title'  => '',
    'block_style'  => '1',
    'post_type'    => 'post',
    'taxonomy'     => 'category',
    'per_page'     => 6,
    'title_limit'  => 30,
    'text_limit'   => 10,
    'is_filter'    => '1',
    'include_post' => '',
    'exclude_post' => '',
    'post_column'  => 3,
);
$is_edit = false;
$page_title = __( 'Create New Block', 'load-more-ajax-lite' );

// Get all public post types
$post_types = get_post_types( ['public' => true], 'objects' );
unset( $post_types['attachment'] );

$current_post_type = $block_data['post_type'] ?? 'post';
$current_taxonomy  = $block_data['taxonomy'] ?? 'category';

// Fetch terms for current taxonomy
$categories = get_terms( array(
    'taxonomy'   => $current_taxonomy,
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );
if ( is_wp_error( $categories ) ) $categories = [];
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html( $page_title ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=load_more_ajax' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Back to All Blocks', 'load-more-ajax-lite' ); ?></a>

    <form action="" method="post" id="lma-block-form">
        <div class="lma-block-editor">
            <!-- Left Column: Settings -->
            <div class="lma-editor-main">

                <!-- General Settings -->
                <div class="lma-section">
                    <div class="lma-section-header">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php esc_html_e( 'General Settings', 'load-more-ajax-lite' ); ?>
                        <span class="dashicons dashicons-arrow-down-alt2 lma-toggle-icon"></span>
                    </div>
                    <div class="lma-section-body">
                        <div class="lma-field">
                            <label for="block_title"><?php esc_html_e( 'Block Name', 'load-more-ajax-lite' ); ?></label>
                            <input type="text" id="block_title" name="block_title" value="<?php echo esc_attr( $block_data['block_title'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. Blog Posts Grid', 'load-more-ajax-lite' ); ?>" required>
                        </div>
                        <div class="lma-field">
                            <label for="post_per_page"><?php esc_html_e( 'Posts Per Page', 'load-more-ajax-lite' ); ?></label>
                            <input type="number" id="post_per_page" name="posts_number" value="<?php echo esc_attr( $block_data['per_page'] ); ?>" min="1" max="100">
                            <p class="description"><?php esc_html_e( 'Number of posts to load each time.', 'load-more-ajax-lite' ); ?></p>
                        </div>
                        <div class="lma-field">
                            <label for="title_limit"><?php esc_html_e( 'Title Character Limit', 'load-more-ajax-lite' ); ?></label>
                            <input type="number" id="title_limit" name="title_limit" value="<?php echo esc_attr( $block_data['title_limit'] ); ?>" min="0" max="200">
                        </div>
                        <div class="lma-field">
                            <label for="text_limit"><?php esc_html_e( 'Excerpt Word Limit', 'load-more-ajax-lite' ); ?></label>
                            <input type="number" id="text_limit" name="text_limit" value="<?php echo esc_attr( $block_data['text_limit'] ); ?>" min="0" max="200">
                        </div>
                    </div>
                </div>

                <!-- Layout Style -->
                <div class="lma-section">
                    <div class="lma-section-header">
                        <span class="dashicons dashicons-layout"></span>
                        <?php esc_html_e( 'Layout Style', 'load-more-ajax-lite' ); ?>
                        <span class="dashicons dashicons-arrow-down-alt2 lma-toggle-icon"></span>
                    </div>
                    <div class="lma-section-body">
                        <div class="lma-field">
                            <label><?php esc_html_e( 'Select Style', 'load-more-ajax-lite' ); ?></label>
                            <div class="lma-style-selector">
                                <!-- Style 1: Grid -->
                                <div class="lma-style-option <?php echo $block_data['block_style'] == '1' ? 'active' : ''; ?>" data-style="1">
                                    <input type="radio" name="block_style" value="1" <?php checked( $block_data['block_style'], '1' ); ?>>
                                    <span class="lma-style-check"><span class="dashicons dashicons-yes"></span></span>
                                    <div class="lma-style-thumb style-grid">
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                    </div>
                                    <span class="lma-style-name"><?php esc_html_e( 'Classic Grid', 'load-more-ajax-lite' ); ?></span>
                                </div>

                                <!-- Style 2: List -->
                                <div class="lma-style-option <?php echo $block_data['block_style'] == '2' ? 'active' : ''; ?>" data-style="2">
                                    <input type="radio" name="block_style" value="2" <?php checked( $block_data['block_style'], '2' ); ?>>
                                    <span class="lma-style-check"><span class="dashicons dashicons-yes"></span></span>
                                    <div class="lma-style-thumb style-list">
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                    </div>
                                    <span class="lma-style-name"><?php esc_html_e( 'List View', 'load-more-ajax-lite' ); ?></span>
                                </div>

                                <!-- Style 3: Card -->
                                <div class="lma-style-option <?php echo $block_data['block_style'] == '3' ? 'active' : ''; ?>" data-style="3">
                                    <input type="radio" name="block_style" value="3" <?php checked( $block_data['block_style'], '3' ); ?>>
                                    <span class="lma-style-check"><span class="dashicons dashicons-yes"></span></span>
                                    <div class="lma-style-thumb style-card">
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                    </div>
                                    <span class="lma-style-name"><?php esc_html_e( 'Modern Card', 'load-more-ajax-lite' ); ?></span>
                                </div>

                                <!-- Style 4: Masonry -->
                                <div class="lma-style-option <?php echo $block_data['block_style'] == '4' ? 'active' : ''; ?>" data-style="4">
                                    <input type="radio" name="block_style" value="4" <?php checked( $block_data['block_style'], '4' ); ?>>
                                    <span class="lma-style-check"><span class="dashicons dashicons-yes"></span></span>
                                    <div class="lma-style-thumb style-masonry">
                                        <div class="masonry-col"><div class="masonry-item tall"></div><div class="masonry-item"></div></div>
                                        <div class="masonry-col"><div class="masonry-item"></div><div class="masonry-item tall"></div></div>
                                        <div class="masonry-col"><div class="masonry-item medium"></div><div class="masonry-item"></div></div>
                                    </div>
                                    <span class="lma-style-name"><?php esc_html_e( 'Masonry', 'load-more-ajax-lite' ); ?></span>
                                </div>

                                <!-- Style 5: Carousel -->
                                <div class="lma-style-option <?php echo $block_data['block_style'] == '5' ? 'active' : ''; ?>" data-style="5">
                                    <input type="radio" name="block_style" value="5" <?php checked( $block_data['block_style'], '5' ); ?>>
                                    <span class="lma-style-check"><span class="dashicons dashicons-yes"></span></span>
                                    <div class="lma-style-thumb style-carousel">
                                        <div class="carousel-arrow left">&lsaquo;</div>
                                        <div class="carousel-slides"><div class="carousel-slide"></div><div class="carousel-slide"></div><div class="carousel-slide"></div></div>
                                        <div class="carousel-arrow right">&rsaquo;</div>
                                        <div class="carousel-dots"><span class="dot active"></span><span class="dot"></span><span class="dot"></span></div>
                                    </div>
                                    <span class="lma-style-name"><?php esc_html_e( 'Carousel', 'load-more-ajax-lite' ); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="lma-field">
                            <label><?php esc_html_e( 'Columns', 'load-more-ajax-lite' ); ?></label>
                            <div class="lma-column-selector">
                                <?php
                                $columns = array(
                                    '6' => __( '2 Col', 'load-more-ajax-lite' ),
                                    '4' => __( '3 Col', 'load-more-ajax-lite' ),
                                    '3' => __( '4 Col', 'load-more-ajax-lite' ),
                                );
                                foreach ( $columns as $value => $label ) :
                                    $col_count = 12 / intval( $value );
                                    $is_active = $block_data['post_column'] == $value ? 'active' : '';
                                ?>
                                <div class="lma-column-group">
                                    <div class="lma-column-option <?php echo esc_attr( $is_active ); ?>" data-column="<?php echo esc_attr( $value ); ?>">
                                        <input type="radio" name="column" value="<?php echo esc_attr( $value ); ?>" <?php checked( $block_data['post_column'], $value ); ?>>
                                        <?php for ( $i = 0; $i < $col_count; $i++ ) : ?>
                                            <span class="col-bar"></span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="lma-column-label"><?php echo esc_html( $label ); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carousel Settings -->
                <div class="lma-section lma-carousel-settings" style="<?php echo ($block_data['block_style'] ?? '') == '5' ? '' : 'display:none;'; ?>">
                    <h3 class="lma-section-title"><?php esc_html_e( 'Carousel Settings', 'load-more-ajax-lite' ); ?></h3>
                    <div class="lma-form-row">
                        <label><?php esc_html_e( 'Slides Per View', 'load-more-ajax-lite' ); ?></label>
                        <select name="slides_per_view">
                            <?php for ($i = 1; $i <= 4; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php selected( $block_data['slides_per_view'] ?? 3, $i ); ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="lma-form-row">
                        <label><?php esc_html_e( 'Show Arrows', 'load-more-ajax-lite' ); ?></label>
                        <label class="lma-toggle"><input type="checkbox" name="show_arrows" value="1" <?php checked( $block_data['show_arrows'] ?? 1, 1 ); ?>><span class="lma-toggle-slider"></span></label>
                    </div>
                    <div class="lma-form-row">
                        <label><?php esc_html_e( 'Show Dots', 'load-more-ajax-lite' ); ?></label>
                        <label class="lma-toggle"><input type="checkbox" name="show_dots" value="1" <?php checked( $block_data['show_dots'] ?? 1, 1 ); ?>><span class="lma-toggle-slider"></span></label>
                    </div>
                    <div class="lma-form-row">
                        <label><?php esc_html_e( 'Autoplay', 'load-more-ajax-lite' ); ?></label>
                        <label class="lma-toggle"><input type="checkbox" name="autoplay" value="1" <?php checked( $block_data['autoplay'] ?? 1, 1 ); ?>><span class="lma-toggle-slider"></span></label>
                    </div>
                </div>

                <!-- Post Type & Taxonomy -->
                <div class="lma-section">
                    <div class="lma-section-header">
                        <span class="dashicons dashicons-category"></span>
                        <?php esc_html_e( 'Post Type & Taxonomy', 'load-more-ajax-lite' ); ?>
                        <span class="dashicons dashicons-arrow-down-alt2 lma-toggle-icon"></span>
                    </div>
                    <div class="lma-section-body">
                        <div class="lma-field">
                            <label><?php esc_html_e( 'Post Type', 'load-more-ajax-lite' ); ?></label>
                            <select name="post_type" id="lma-post-type">
                                <?php foreach ( $post_types as $pt ) : ?>
                                    <option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $current_post_type, $pt->name ); ?>>
                                        <?php echo esc_html( $pt->labels->singular_name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="lma-field">
                            <label><?php esc_html_e( 'Taxonomy', 'load-more-ajax-lite' ); ?></label>
                            <select name="taxonomy" id="lma-taxonomy">
                                <?php
                                $taxonomies = get_object_taxonomies( $current_post_type, 'objects' );
                                foreach ( $taxonomies as $tax ) :
                                    if ( ! $tax->public ) continue;
                                ?>
                                    <option value="<?php echo esc_attr( $tax->name ); ?>" <?php selected( $current_taxonomy, $tax->name ); ?>>
                                        <?php echo esc_html( $tax->labels->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="lma-field">
                            <div class="lma-toggle">
                                <input type="checkbox" id="is_cat_filter" name="category_filter" value="1" <?php checked( $block_data['is_filter'], '1' ); ?>>
                                <label class="lma-toggle-switch" for="is_cat_filter"></label>
                                <span class="lma-toggle-label"><?php esc_html_e( 'Show category filter bar on frontend', 'load-more-ajax-lite' ); ?></span>
                            </div>
                        </div>

                        <div class="lma-field">
                            <label><?php esc_html_e( 'Include Categories', 'load-more-ajax-lite' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Only show posts from selected categories. Leave empty to show all.', 'load-more-ajax-lite' ); ?></p>
                            <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                <div class="lma-category-list" id="lma-include-cats">
                                    <?php
                                    $included = array_filter( array_map( 'trim', explode( ',', $block_data['include_post'] ) ) );
                                    foreach ( $categories as $cat ) : ?>
                                        <label>
                                            <input type="checkbox" name="include_cats[]" value="<?php echo esc_attr( $cat->term_id ); ?>" <?php echo in_array( $cat->term_id, $included ) ? 'checked' : ''; ?>>
                                            <?php echo esc_html( $cat->name ); ?>
                                            <span class="lma-cat-count">(<?php echo esc_html( $cat->count ); ?>)</span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p class="description"><?php esc_html_e( 'No categories found.', 'load-more-ajax-lite' ); ?></p>
                            <?php endif; ?>
                            <input type="hidden" name="include" id="lma-include-ids" value="<?php echo esc_attr( $block_data['include_post'] ); ?>">
                        </div>

                        <div class="lma-field">
                            <label><?php esc_html_e( 'Exclude Categories', 'load-more-ajax-lite' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Hide posts from selected categories.', 'load-more-ajax-lite' ); ?></p>
                            <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                                <div class="lma-category-list" id="lma-exclude-cats">
                                    <?php
                                    $excluded = array_filter( array_map( 'trim', explode( ',', $block_data['exclude_post'] ) ) );
                                    foreach ( $categories as $cat ) : ?>
                                        <label>
                                            <input type="checkbox" name="exclude_cats[]" value="<?php echo esc_attr( $cat->term_id ); ?>" <?php echo in_array( $cat->term_id, $excluded ) ? 'checked' : ''; ?>>
                                            <?php echo esc_html( $cat->name ); ?>
                                            <span class="lma-cat-count">(<?php echo esc_html( $cat->count ); ?>)</span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p class="description"><?php esc_html_e( 'No categories found.', 'load-more-ajax-lite' ); ?></p>
                            <?php endif; ?>
                            <input type="hidden" name="exclude" id="lma-exclude-ids" value="<?php echo esc_attr( $block_data['exclude_post'] ); ?>">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="created_by" value="<?php echo esc_attr( get_current_user_id() ); ?>">
                <?php wp_nonce_field( 'add_new_block' ); ?>
                <?php submit_button( __( 'Save Block', 'load-more-ajax-lite' ), 'primary large', 'submit_block' ); ?>
            </div>

            <!-- Right Column: Live Preview -->
            <div class="lma-editor-preview">
                <div class="lma-preview-panel">
                    <div class="lma-preview-header">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Live Preview', 'load-more-ajax-lite' ); ?>
                    </div>
                    <div class="lma-preview-body" id="lma-live-preview">
                        <!-- Preview renders here via JS -->
                    </div>
                </div>

                <div class="lma-shortcode-display" id="lma-shortcode-display" style="display:none;">
                    <strong><?php esc_html_e( 'Shortcode:', 'load-more-ajax-lite' ); ?></strong>
                    <code id="lma-shortcode-code"></code>
                    <button type="button" class="button button-small" id="lma-copy-shortcode"><?php esc_html_e( 'Copy', 'load-more-ajax-lite' ); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
