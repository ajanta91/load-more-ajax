<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$block_id = isset( $_GET['post_block'] ) ? intval( $_GET['post_block'] ) : 0;
$block_data = array(
    'id'           => 0,
    'block_title'  => '',
    'block_style'  => '1',
    'per_page'     => 6,
    'title_limit'  => 30,
    'text_limit'   => 10,
    'is_filter'    => '1',
    'include_post' => '',
    'exclude_post' => '',
    'post_column'  => 3,
);

if ( ! empty( $block_id ) ) {
    $block_block = new PostBlock();
    $fetched = $block_block->block_update_data( $block_id );
    if ( $fetched ) {
        $block_data = array_merge( $block_data, $fetched );
    }
}

// Fetch categories
$categories = get_terms( array(
    'taxonomy'   => 'category',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
) );

$page_title = __( 'Edit Block', 'load-more-ajax-lite' );
$is_edit = true;
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
                                <div class="lma-style-option <?php echo $block_data['block_style'] == '2' ? 'active' : ''; ?>" data-style="2">
                                    <input type="radio" name="block_style" value="2" <?php checked( $block_data['block_style'], '2' ); ?>>
                                    <span class="lma-style-check"><span class="dashicons dashicons-yes"></span></span>
                                    <div class="lma-style-thumb style-list">
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                        <div class="thumb-card"><div class="thumb-img"></div><div class="thumb-lines"><div class="thumb-line"></div><div class="thumb-line short"></div></div></div>
                                    </div>
                                    <span class="lma-style-name"><?php esc_html_e( 'List View', 'load-more-ajax-lite' ); ?></span>
                                </div>
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

                <!-- Category Filter -->
                <div class="lma-section">
                    <div class="lma-section-header">
                        <span class="dashicons dashicons-category"></span>
                        <?php esc_html_e( 'Category Filter', 'load-more-ajax-lite' ); ?>
                        <span class="dashicons dashicons-arrow-down-alt2 lma-toggle-icon"></span>
                    </div>
                    <div class="lma-section-body">
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
                            <?php endif; ?>
                            <input type="hidden" name="exclude" id="lma-exclude-ids" value="<?php echo esc_attr( $block_data['exclude_post'] ); ?>">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="created_by" value="<?php echo esc_attr( get_current_user_id() ); ?>">
                <input type="hidden" name="block_id" value="<?php echo esc_attr( $block_data['id'] ); ?>">
                <?php wp_nonce_field( 'add_new_block' ); ?>
                <?php submit_button( __( 'Update Block', 'load-more-ajax-lite' ), 'primary large', 'submit_block' ); ?>
            </div>

            <!-- Right Column: Live Preview -->
            <div class="lma-editor-preview">
                <div class="lma-preview-panel">
                    <div class="lma-preview-header">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Live Preview', 'load-more-ajax-lite' ); ?>
                    </div>
                    <div class="lma-preview-body" id="lma-live-preview">
                    </div>
                </div>

                <div class="lma-shortcode-display" id="lma-shortcode-display">
                    <strong><?php esc_html_e( 'Shortcode:', 'load-more-ajax-lite' ); ?></strong>
                    <code id="lma-shortcode-code"></code>
                    <button type="button" class="button button-small" id="lma-copy-shortcode"><?php esc_html_e( 'Copy', 'load-more-ajax-lite' ); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>
