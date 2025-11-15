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
        // Always try to initialize immediately for interactive/complete states
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            this.initializeProductBlocks();
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeProductBlocks();
            });
        }
    }

    initializeProductBlocks() {
        const productBlocks = document.querySelectorAll('.lma_products_block');

        productBlocks.forEach((block, index) => {
            // Skip if already initialized
            if (block.dataset.lmaInitialized) {
                return;
            }

            const instanceId = `lma_product_${Date.now()}_${index}`;
            const container = block.querySelector('.ajaxproduct_loader');

            if (!container) {
                return;
            }

            const config = this.getProductConfig(container);

            this.productInstances.set(instanceId, {
                block,
                container,
                config,
                isLoading: false,
                currentPage: 1,
            });

            this.setupProductBlock(instanceId);
            
            // Mark as initialized
            block.dataset.lmaInitialized = 'true';
            container.dataset.instanceId = instanceId;

            // Load initial products
            this.loadProducts(instanceId, false);
        });
    }

    getProductConfig(container) {
        return {
            limit: parseInt(container.dataset.limit) || 6,
            category: container.dataset.cate || '',
            layout: container.dataset.blockStyle || '1',
            column: container.dataset.column || 'column_3',
            orderby: container.dataset.orderby || 'date',
            order: container.dataset.sortorder || 'DESC',
            featured: container.dataset.featured === 'true',
            onSale: container.dataset.onsale === 'true',
            showRating: container.dataset.showrating !== 'false',
            showPrice: container.dataset.showprice !== 'false',
            showCartButton: container.dataset.showcartbutton !== 'false',
            showSaleBadge: container.dataset.showsalebadge !== 'false',
            showCount: container.dataset.showcount === 'true',
            enableAnimation: container.dataset.enableanimation !== 'false',
            // Modern layout specific settings
            showActionButtons: container.dataset.showactionbuttons !== 'false',
            showDescription: container.dataset.showdescription !== 'false',
            showStockStatus: container.dataset.showstockstatus !== 'false',
            enableHoverEffects: container.dataset.enablehovereffects !== 'false',
        };
    }

    setupProductBlock(instanceId) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const { block, container } = instance;
        
        // Setup load more button
        const button = block.querySelector('.loadmore_products');
        if (button) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                if (instance.isLoading) {
                    return;
                }
                
                this.loadProducts(instanceId, true);
            });
        }

        // Setup category filters
        const filters = block.querySelectorAll('.ajax_post_cat');
        filters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                this.filterByCategory(instanceId, filter);
            });
        });

        // Setup sort dropdown
        const sortSelect = block.querySelector('.lma_product_sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                this.sortProducts(instanceId, sortSelect.value);
            });
        }

        // Initialize action buttons for modern layout
        this.initActionButtons(block);
    }

    async loadProducts(instanceId, isLoadMore = false) {
        const instance = this.productInstances.get(instanceId);
        if (!instance || instance.isLoading) return;

        const { container, config, block } = instance;
        const button = block.querySelector('.loadmore_products');
        
        instance.isLoading = true;

        // Update button state
        if (button && isLoadMore) {
            const loadingText = button.dataset.loadingText || 'Loading...';
            button.textContent = loadingText;
            button.disabled = true;
        }

        try {
            const currentPage = isLoadMore ? instance.currentPage + 1 : 1;
            
            const formData = new FormData();
            formData.append('action', 'lma_load_products');
            formData.append('nonce', window.load_more_ajax_lite?.nonce || '');
            formData.append('order', currentPage);
            formData.append('limit', config.limit);
            formData.append('cate', config.category);
            formData.append('sort_by', config.orderby);
            formData.append('sort_order', config.order);
            formData.append('block_style', config.layout);
            formData.append('column', config.column);
            formData.append('featured', config.featured ? 'true' : 'false');
            formData.append('on_sale', config.onSale ? 'true' : 'false');

            const response = await fetch(window.load_more_ajax_lite?.ajax_url || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                let productsHtml = '';
                
                // Check if we have pre-rendered HTML (for modern layout)
                if (data.data.html) {
                    productsHtml = data.data.html;
                } else if (data.data.products) {
                    // Build HTML from product data (for other layouts)
                    const products = data.data.products;
                    productsHtml = products.map(product => this.buildProductHTML(product, config)).join('');
                }
                
                if (productsHtml) {
                    if (isLoadMore) {
                        container.insertAdjacentHTML('beforeend', productsHtml);
                        instance.currentPage = currentPage;
                    } else {
                        container.innerHTML = productsHtml;
                        instance.currentPage = 1;
                    }

                    // Update product count if enabled
                    if (config.showCount && data.data.total_products !== undefined) {
                        this.updateProductCount(instanceId, data.data);
                    }

                    // Hide load more button if no more products
                    if (button) {
                        const hasMore = data.data.has_more !== false && (data.data.products ? data.data.products.length > 0 : true);
                        if (!hasMore) {
                            const noMoreText = button.dataset.noMoreText || 'No More Products';
                            button.textContent = noMoreText;
                            button.disabled = true;
                            button.style.display = 'none';
                        } else {
                            const buttonText = button.dataset.buttonText || 'Load More Products';
                            button.textContent = buttonText;
                            button.disabled = false;
                            button.style.display = '';
                        }
                    }
                }

                // Animate new products if enabled
                if (config.enableAnimation && isLoadMore) {
                    this.animateNewProducts(container);
                }

                // Trigger custom event
                const event = new CustomEvent('lmaProductsLoaded', {
                    detail: { instanceId, isLoadMore, data: data.data }
                });
                document.dispatchEvent(event);

            } else {
                throw new Error(data.data?.message || 'Failed to load products');
            }

        } catch (error) {
            if (button) {
                const buttonText = button.dataset.buttonText || 'Load More Products';
                button.textContent = buttonText;
                button.disabled = false;
            }
        } finally {
            instance.isLoading = false;
        }
    }

    updateProductCount(instanceId, data) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const countElement = instance.block.querySelector('.lma_showing_text');
        if (countElement && data.total_products !== undefined) {
            const currentCount = (instance.currentPage * instance.config.limit);
            const showingCount = Math.min(currentCount, data.total_products);
            countElement.textContent = `Showing ${showingCount} of ${data.total_products} products`;
        }
    }

    animateNewProducts(container) {
        const newProducts = container.querySelectorAll('.lma_product_item:not(.lma-animated)');
        newProducts.forEach((product, index) => {
            product.classList.add('lma-animated');
            product.style.opacity = '0';
            product.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                product.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                product.style.opacity = '1';
                product.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    buildProductHTML(product, config) {
        let html = `<div class="lma_product_item">`;
        
        // Product image
        if (product.thumbnail) {
            html += `<div class="lma_product_thumb">
                <a href="${product.permalink}">
                    <img src="${product.thumbnail}" alt="${product.thumbnail_alt || product.title}" loading="lazy">
                </a>`;
            
            // Sale badge
            if (config.showSaleBadge && product.on_sale && product.sale_badge) {
                html += `<span class="lma_sale_badge">${product.sale_badge}</span>`;
            }
            
            html += `</div>`;
        }

        // Product categories
        if (product.categories) {
            html += `<div class="lma_product_categories">${product.categories}</div>`;
        }

        // Product title
        html += `<div class="lma_product_title">
            <a href="${product.permalink}">${product.title}</a>
        </div>`;

        // Product rating
        if (config.showRating && product.rating) {
            html += `<div class="lma_product_rating">${product.rating}</div>`;
        }

        // Product price
        if (config.showPrice && product.price) {
            html += `<div class="lma_product_price">${product.price}</div>`;
        }

        // Add to cart button
        if (config.showCartButton && product.add_to_cart) {
            html += `<div class="lma_product_cart">${product.add_to_cart}</div>`;
        }

        html += `</div>`;
        return html;
    }

    filterByCategory(instanceId, filterElement) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        // Update active filter
        const block = instance.block;
        const categoryId = filterElement.dataset.cateid;
        
        // Remove active class from all filters
        block.querySelectorAll('.ajax_post_cat').forEach(f => f.classList.remove('active'));
        // Add active class to clicked filter
        filterElement.classList.add('active');

        // Reset button state when filtering
        const button = block.querySelector('.loadmore_products');
        if (button) {
            const buttonText = button.dataset.buttonText || 'Load More Products';
            button.textContent = buttonText;
            button.disabled = false;
            button.style.display = '';
        }

        // Update config and reload
        instance.config.category = categoryId;
        instance.currentPage = 1;

        this.loadProducts(instanceId, false);
    }

    sortProducts(instanceId, sortValue) {
        const instance = this.productInstances.get(instanceId);
        if (!instance) return;

        const [orderby, order] = sortValue.split(':');
        
        // Reset button state when sorting
        const button = instance.block.querySelector('.loadmore_products');
        if (button) {
            const buttonText = button.dataset.buttonText || 'Load More Products';
            button.textContent = buttonText;
            button.disabled = false;
            button.style.display = '';
        }
        
        // Update config
        instance.config.orderby = orderby;
        instance.config.order = order || 'DESC';
        instance.currentPage = 1;

        this.loadProducts(instanceId, false);
    }

    /**
     * Initialize action buttons for modern layout
     */
    initActionButtons(block) {
        // Quick View functionality
        block.addEventListener('click', (e) => {
            if (e.target.closest('.action-btn[title*="Quick View"]')) {
                e.preventDefault();
                const btn = e.target.closest('.action-btn');
                const productId = btn.getAttribute('data-product-id');
                this.handleQuickView(productId);
            }
        });

        // Wishlist functionality
        block.addEventListener('click', (e) => {
            if (e.target.closest('.action-btn[title*="Wishlist"]')) {
                e.preventDefault();
                const btn = e.target.closest('.action-btn');
                const productId = btn.getAttribute('data-product-id');
                this.handleWishlist(btn, productId);
            }
        });

        // Compare functionality
        block.addEventListener('click', (e) => {
            if (e.target.closest('.action-btn[title*="Compare"]')) {
                e.preventDefault();
                const btn = e.target.closest('.action-btn');
                const productId = btn.getAttribute('data-product-id');
                this.handleCompare(btn, productId);
            }
        });
    }

    /**
     * Handle Quick View
     */
    handleQuickView(productId) {
        console.log('Quick view for product:', productId);
        // Implement your quick view functionality here
        // For example, open a modal with product details
    }

    /**
     * Handle Wishlist
     */
    handleWishlist(btn, productId) {
        console.log('Add to wishlist:', productId);
        
        // Visual feedback
        const icon = btn.querySelector('i');
        if (icon) {
            btn.style.background = '#ff6b6b';
            btn.style.color = 'white';
            icon.classList.remove('far');
            icon.classList.add('fas');
        }
        
        // Add your wishlist implementation here
        // For example, AJAX call to add to wishlist
    }

    /**
     * Handle Compare
     */
    handleCompare(btn, productId) {
        console.log('Add to compare:', productId);
        
        // Visual feedback
        btn.style.background = '#28a745';
        btn.style.color = 'white';
        
        // Add your compare implementation here
        // For example, add to compare list
    }
}

// Initialize when products are detected
function initLMAProducts() {
    if (document.querySelector('.lma_products_block')) {
        if (!window.lmaWooCommerce) {
            window.lmaWooCommerce = new LMAWooCommerce();
        }
    }
}

// Initial load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLMAProducts);
} else {
    // Use setTimeout to ensure DOM is fully ready
    setTimeout(initLMAProducts, 50);
}

// Re-initialize on Elementor preview changes
document.addEventListener('elementor/popup/show', initLMAProducts);
document.addEventListener('elementor/frontend/init', initLMAProducts);

// MutationObserver for dynamic content
if (window.MutationObserver) {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1 && (
                        node.classList?.contains('lma_products_block') ||
                        node.querySelector?.('.lma_products_block')
                    )) {
                        setTimeout(initLMAProducts, 100);
                    }
                });
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}