/**
 * WooCommerce Product Load More - Modern JavaScript
 * Extends the LoadMoreAjax class for product support
 */

class LMAWooCommerce {
    constructor() {
        this.productInstances = new Map();
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeProductBlocks();
        });
    }

    initializeProductBlocks() {
        const productBlocks = document.querySelectorAll('.lma_products_block');

        productBlocks.forEach((block, index) => {
            const instanceId = `lma_product_${index}`;
            const container = block.querySelector('.ajaxproduct_loader');

            if (!container) return;

            const config = this.getProductConfig(container);

            this.productInstances.set(instanceId, {
                block,
                container,
                config,
                isLoading: false,
                currentPage: 1,
            });

            this.setupProductBlock(instanceId);
            this.loadProducts(instanceId, 1, true);
        });
    }

    getProductConfig(container) {
        return {
            limit: parseInt(container.dataset.limit) || 6,
            category: container.dataset.cate || '',
            blockStyle: container.dataset.block_style || '1',
            column: container.dataset.column || 'column_3',
            orderby: container.dataset.orderby || 'date',
            sortOrder: container.dataset.sortOrder || 'DESC',
            featured: container.dataset.featured || 'false',
            onSale: container.dataset.onSale || 'false',
            showRating: container.dataset.showRating !== 'false',
            showPrice: container.dataset.showPrice !== 'false',
            showCartButton: container.dataset.showCartButton !== 'false',
            showSaleBadge: container.dataset.showSaleBadge !== 'false',
        };
    }

    setupProductBlock(instanceId) {
        const instance = this.productInstances.get(instanceId);
        const { block } = instance;

        // Setup load more button
        const button = block.querySelector('.loadmore_products');
        if (button) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                if (!instance.isLoading) {
                    this.loadMoreProducts(instanceId);
                }
            });
        }

        // Setup category filters
        const filters = block.querySelectorAll('.product_cat_filter .ajax_post_cat');
        filters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                this.filterByCategory(instanceId, filter);
            });
        });

        // Setup sort dropdown
        const sortSelect = block.querySelector('.lma_product_sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.sortProducts(instanceId, e.target.value);
            });
        }

        // Setup WooCommerce add to cart events
        this.setupWooCommerceEvents(block);
    }

    async loadProducts(instanceId, page = 1, initial = false) {
        const instance = this.productInstances.get(instanceId);
        if (!instance || instance.isLoading) return;

        instance.isLoading = true;
        const { config, container, block } = instance;
        const button = block.querySelector('.loadmore_products');

        // Show loading state
        if (!initial && button) {
            button.textContent = window.load_more_ajax_lite?.strings?.loading || 'Loading...';
            button.disabled = true;
        }

        try {
            const formData = new FormData();
            formData.append('action', 'lma_load_products');
            formData.append('nonce', window.load_more_ajax_lite?.nonce || '');
            formData.append('order', page);
            formData.append('limit', config.limit);
            formData.append('cate', config.category);
            formData.append('block_style', config.blockStyle);
            formData.append('column', config.column);
            formData.append('sort_by', config.orderby);
            formData.append('sort_order', config.sortOrder);
            formData.append('featured', config.featured);
            formData.append('on_sale', config.onSale);

            const response = await fetch(
                window.load_more_ajax_lite?.ajax_url || '/wp-admin/admin-ajax.php',
                {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                }
            );

            const data = await response.json();

            if (data.success && data.data.products) {
                this.renderProducts(instanceId, data.data.products);
                this.updatePagination(instanceId, data.data.pagination);

                // Trigger custom event
                const event = new CustomEvent('lma_products_loaded', {
                    detail: { products: data.data.products, instanceId }
                });
                document.dispatchEvent(event);
            } else {
                if (button) {
                    button.textContent = window.load_more_ajax_lite?.strings?.no_more || 'No More Products';
                    button.disabled = true;
                }
            }
        } catch (error) {
            console.error('LMA WooCommerce Error:', error);
            if (button) {
                button.textContent = window.load_more_ajax_lite?.strings?.error || 'Error loading products';
            }
        } finally {
            instance.isLoading = false;
        }
    }

    renderProducts(instanceId, products) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const { container, config } = instance;

        products.forEach((product, index) => {
            const productElement = this.createProductElement(product, config);

            // Add animation
            productElement.style.opacity = '0';
            productElement.style.transform = 'translateY(20px)';
            container.appendChild(productElement);

            setTimeout(() => {
                productElement.style.transition = 'opacity 300ms ease, transform 300ms ease';
                productElement.style.opacity = '1';
                productElement.style.transform = 'translateY(0)';
            }, index * 50);
        });

        // Reinitialize WooCommerce add to cart handlers
        if (typeof jQuery !== 'undefined' && jQuery.fn.wc_variation_form) {
            jQuery('.variations_form').each(function() {
                jQuery(this).wc_variation_form();
            });
        }
    }

    createProductElement(product, config) {
        const wrapper = document.createElement('div');
        wrapper.className = 'lma_product_item';

        let html = '<div class="lma_product_image">';
        html += `<a href="${product.permalink}">`;

        if (product.thumbnail) {
            html += `<img src="${product.thumbnail}" alt="${product.thumbnail_alt || product.title}" loading="lazy" />`;
        } else {
            html += this.getPlaceholderImage();
        }

        html += '</a>';

        if (config.showSaleBadge && product.sale_badge) {
            html += product.sale_badge;
        }

        if (product.stock_status) {
            html += `<div class="lma_product_stock">${product.stock_status}</div>`;
        }

        html += '</div>';

        html += '<div class="lma_product_content">';

        if (product.categories) {
            html += `<div class="lma_product_categories">${product.categories}</div>`;
        }

        html += `<h3 class="lma_product_title"><a href="${product.permalink}">${product.title}</a></h3>`;

        if (config.showRating && product.rating) {
            html += `<div class="lma_product_rating">${product.rating}</div>`;
        }

        if (config.showPrice && product.price) {
            html += `<div class="lma_product_price">${product.price}</div>`;
        }

        if (product.short_description) {
            html += `<div class="lma_product_description">${product.short_description}</div>`;
        }

        if (config.showCartButton && product.add_to_cart) {
            html += `<div class="lma_product_cart">${product.add_to_cart}</div>`;
        }

        html += '</div>';

        wrapper.innerHTML = html;
        return wrapper;
    }

    loadMoreProducts(instanceId) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        instance.currentPage++;
        this.loadProducts(instanceId, instance.currentPage);
    }

    filterByCategory(instanceId, filterElement) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const { block, container } = instance;
        const categoryId = filterElement.dataset.cateid;

        // Update active state
        const filters = block.querySelectorAll('.product_cat_filter .ajax_post_cat');
        filters.forEach(f => f.classList.remove('active'));
        filterElement.classList.add('active');

        // Update config and reload
        instance.config.category = categoryId;
        instance.currentPage = 1;
        container.innerHTML = '';

        this.loadProducts(instanceId, 1);

        // Reset button
        const button = block.querySelector('.loadmore_products');
        if (button) {
            button.textContent = window.load_more_ajax_lite?.strings?.load_more || 'Load More Products';
            button.disabled = false;
        }
    }

    sortProducts(instanceId, sortValue) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const { container } = instance;
        const [sortBy, sortOrder] = sortValue.split(':');

        instance.config.orderby = sortBy;
        instance.config.sortOrder = sortOrder;
        instance.currentPage = 1;
        container.innerHTML = '';

        this.loadProducts(instanceId, 1);
    }

    updatePagination(instanceId, pagination) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const button = instance.block.querySelector('.loadmore_products');
        if (!button) return;

        if (pagination.has_more) {
            button.textContent = window.load_more_ajax_lite?.strings?.load_more || 'Load More Products';
            button.disabled = false;
            button.style.display = 'inline-block';
        } else {
            button.textContent = window.load_more_ajax_lite?.strings?.no_more || 'No More Products';
            button.disabled = true;
        }
    }

    setupWooCommerceEvents(block) {
        // Listen for WooCommerce add to cart events
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('added_to_cart', (e, fragments, cart_hash, $button) => {
                if (block.contains($button[0])) {
                    $button.removeClass('loading').addClass('added');
                    setTimeout(() => {
                        $button.removeClass('added');
                    }, 2000);
                }
            });
        }
    }

    getPlaceholderImage() {
        const placeholder = typeof wc_placeholder_img_src !== 'undefined'
            ? wc_placeholder_img_src
            : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300"%3E%3Crect fill="%23e5e5e5" width="300" height="300"/%3E%3C/svg%3E';

        return `<img src="${placeholder}" alt="Placeholder" />`;
    }
}

// Initialize WooCommerce products
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (document.querySelector('.lma_products_block')) {
            window.lmaWooCommerce = new LMAWooCommerce();
        }
    });
} else {
    if (document.querySelector('.lma_products_block')) {
        window.lmaWooCommerce = new LMAWooCommerce();
    }
}

// Make class globally available
window.LMAWooCommerce = LMAWooCommerce;
