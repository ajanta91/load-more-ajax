/**
 * Load More Ajax Lite - Modern JavaScript
 * 
 * @package Load_More_Ajax_Lite
 * @since 1.1.2
 */

class LoadMoreAjax {
    constructor() {
        this.instances = new Map();
        this.defaults = {
            loadMoreText: 'Load More',
            loadingText: 'Loading...',
            noMoreText: 'No More Posts',
            errorText: 'Something went wrong. Please try again.',
            infiniteScroll: false,
            scrollThreshold: 200,
            animationDuration: 300,
            enableSearch: false,
            enableSort: false,
        };
        
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeBlocks();
        });
    }

    initializeBlocks() {
        const blocks = document.querySelectorAll('.apl_block_wraper');
        
        blocks.forEach((block, index) => {
            const instanceId = `lma_${index}`;
            const config = this.getBlockConfig(block);
            
            this.instances.set(instanceId, {
                block,
                config,
                isLoading: false,
                currentPage: 1,
                totalPages: 1,
                totalPosts: 0,
                loadedPosts: 0,
            });

            this.setupBlock(instanceId);
        });
    }

    getBlockConfig(block) {
        const loader = block.querySelector('.ajaxpost_loader');
        const config = { ...this.defaults };

        if (loader) {
            config.postType = loader.dataset.post_type || 'post';
            config.limit = parseInt(loader.dataset.limit) || 6;
            config.column = loader.dataset.column || 'column_3';
            config.blockStyle = loader.dataset.block_style || '1';
            config.textLimit = loader.dataset.text_limit || '10';
            config.titleLimit = loader.dataset.title_limit || '30';
            config.category = loader.dataset.cate || '';
        }

        // Check for infinite scroll attribute
        config.infiniteScroll = block.dataset.infiniteScroll === 'true';
        config.enableSearch = block.dataset.enableSearch === 'true';
        config.enableSort = block.dataset.enableSort === 'true';

        return config;
    }

    setupBlock(instanceId) {
        const instance = this.instances.get(instanceId);
        const { block, config } = instance;

        // Add search functionality if enabled
        if (config.enableSearch) {
            this.addSearchBox(block, instanceId);
        }

        // Add sort functionality if enabled
        if (config.enableSort) {
            this.addSortControls(block, instanceId);
        }

        // Add post count display
        this.addPostCountDisplay(block, instanceId);

        // Setup load more button or infinite scroll
        if (config.infiniteScroll) {
            this.setupInfiniteScroll(instanceId);
        } else {
            this.setupLoadMoreButton(instanceId);
        }

        // Setup category filters
        this.setupCategoryFilters(instanceId);

        // Load initial posts
        this.loadPosts(instanceId);
    }

    addSearchBox(block, instanceId) {
        const searchContainer = document.createElement('div');
        searchContainer.className = 'lma-search-container';
        searchContainer.innerHTML = `
            <div class="lma-search-box">
                <input type="text" class="lma-search-input" placeholder="Search posts..." />
                <button type="button" class="lma-search-btn" aria-label="Search">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </button>
                <button type="button" class="lma-search-clear" aria-label="Clear search" style="display: none;">×</button>
            </div>
        `;

        const catFilter = block.querySelector('.cat_filter');
        if (catFilter) {
            catFilter.parentNode.insertBefore(searchContainer, catFilter.nextSibling);
        } else {
            block.insertBefore(searchContainer, block.firstChild);
        }

        // Bind search events
        const searchInput = searchContainer.querySelector('.lma-search-input');
        const searchBtn = searchContainer.querySelector('.lma-search-btn');
        const clearBtn = searchContainer.querySelector('.lma-search-clear');

        const performSearch = this.debounce(() => {
            const searchTerm = searchInput.value.trim();
            if (searchTerm.length >= 3) {
                this.searchPosts(instanceId, searchTerm);
                clearBtn.style.display = 'block';
            }
        }, 500);

        searchInput.addEventListener('input', performSearch);
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        searchBtn.addEventListener('click', performSearch);
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.style.display = 'none';
            this.resetSearch(instanceId);
        });
    }

    addSortControls(block, instanceId) {
        const sortContainer = document.createElement('div');
        sortContainer.className = 'lma-sort-container';
        sortContainer.innerHTML = `
            <div class="lma-sort-controls">
                <label for="lma-sort-${instanceId}">Sort by:</label>
                <select id="lma-sort-${instanceId}" class="lma-sort-select">
                    <option value="date-desc">Newest First</option>
                    <option value="date-asc">Oldest First</option>
                    <option value="title-asc">Title A-Z</option>
                    <option value="title-desc">Title Z-A</option>
                    <option value="modified-desc">Recently Modified</option>
                </select>
            </div>
        `;

        const catFilter = block.querySelector('.cat_filter');
        if (catFilter) {
            catFilter.parentNode.insertBefore(sortContainer, catFilter.nextSibling);
        } else {
            block.insertBefore(sortContainer, block.firstChild);
        }

        const sortSelect = sortContainer.querySelector('.lma-sort-select');
        sortSelect.addEventListener('change', () => {
            const [sortBy, sortOrder] = sortSelect.value.split('-');
            this.setSorting(instanceId, sortBy, sortOrder.toUpperCase());
        });
    }

    addPostCountDisplay(block, instanceId) {
        const countContainer = document.createElement('div');
        countContainer.className = 'lma-post-count';
        countContainer.innerHTML = '<span class="lma-count-text">Loading...</span>';

        const loader = block.querySelector('.load_more_wrapper');
        loader.append(countContainer);
      
    }

    updatePostCount(instanceId) {
        const instance = this.instances.get(instanceId);
        const countElement = instance.block.querySelector('.lma-count-text');
        
        if (countElement && instance.totalPosts > 0) {
            countElement.textContent = `Showing ${instance.loadedPosts} of ${instance.totalPosts} posts`;
        }
    }

    setupLoadMoreButton(instanceId) {
        const instance = this.instances.get(instanceId);
        const button = instance.block.querySelector('.loadmore_ajax');
        
        if (button) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                if (!instance.isLoading) {
                    this.loadMorePosts(instanceId);
                }
            });
        }
    }

    setupInfiniteScroll(instanceId) {
        const instance = this.instances.get(instanceId);
        const { config } = instance;
        
        let isNearBottom = false;
        
        const scrollHandler = this.throttle(() => {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            const distanceFromBottom = documentHeight - (scrollTop + windowHeight);
            
            if (distanceFromBottom < config.scrollThreshold && !isNearBottom && !instance.isLoading) {
                isNearBottom = true;
                this.loadMorePosts(instanceId);
            } else if (distanceFromBottom > config.scrollThreshold) {
                isNearBottom = false;
            }
        }, 100);

        window.addEventListener('scroll', scrollHandler);
        
        // Store reference for cleanup
        instance.scrollHandler = scrollHandler;
    }

    setupCategoryFilters(instanceId) {
        const instance = this.instances.get(instanceId);
        const catFilters = instance.block.querySelectorAll('.ajax_post_cat');
        
        catFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Update active state
                catFilters.forEach(f => f.classList.remove('active'));
                filter.classList.add('active');
                
                // Reset and reload
                this.resetPosts(instanceId);
                const categoryId = filter.dataset.cateid;
                instance.config.category = categoryId;
                this.loadPosts(instanceId);
            });
        });
    }

    async loadPosts(instanceId, append = false) {
        const instance = this.instances.get(instanceId);
        const { config } = instance;
        
        if (instance.isLoading) return;
        
        instance.isLoading = true;
        this.showLoading(instanceId);

        try {
            const response = await this.makeAjaxRequest('ajaxpostsload', {
                post_type: config.postType,
                order: append ? instance.currentPage + 1 : 1,
                limit: config.limit,
                cate: config.category,
                column: config.column,
                block_style: config.blockStyle,
                text_limit: config.textLimit,
                title_limit: config.titleLimit,
                sort_by: config.sortBy || 'date',
                sort_order: config.sortOrder || 'DESC',
            });

            if (response.success) {
                this.renderPosts(instanceId, response.data, append);
                this.updatePagination(instanceId, response.data.pagination);
                this.updatePostCount(instanceId);
            } else {
                this.showError(instanceId, response.data?.message);
            }
        } catch (error) {
            console.error('LoadMoreAjax Error:', error);
            this.showError(instanceId, config.errorText);
        } finally {
            instance.isLoading = false;
            this.hideLoading(instanceId);
        }
    }

    async loadMorePosts(instanceId) {
        await this.loadPosts(instanceId, true);
    }

    async searchPosts(instanceId, searchTerm) {
        const instance = this.instances.get(instanceId);
        
        try {
            const response = await this.makeAjaxRequest('lma_search_posts', {
                search: searchTerm,
                post_type: instance.config.postType,
                limit: instance.config.limit,
            });

            if (response.success) {
                this.renderSearchResults(instanceId, response.data);
            } else {
                this.showError(instanceId, response.data?.message);
            }
        } catch (error) {
            this.showError(instanceId, 'Search failed');
        }
    }

    resetSearch(instanceId) {
        this.resetPosts(instanceId);
        this.loadPosts(instanceId);
    }

    setSorting(instanceId, sortBy, sortOrder) {
        const instance = this.instances.get(instanceId);
        instance.config.sortBy = sortBy;
        instance.config.sortOrder = sortOrder;
        
        this.resetPosts(instanceId);
        this.loadPosts(instanceId);
    }

    resetPosts(instanceId) {
        const instance = this.instances.get(instanceId);
        const loader = instance.block.querySelector('.ajaxpost_loader');
        
        if (loader) {
            loader.innerHTML = '';
        }
        
        instance.currentPage = 1;
        instance.loadedPosts = 0;
    }

    renderPosts(instanceId, data, append = false) {
        const instance = this.instances.get(instanceId);
        const loader = instance.block.querySelector('.ajaxpost_loader');
        const { config } = instance;
        
        if (!loader || !data.posts) return;

        if (!append) {
            loader.innerHTML = '';
        }

        data.posts.forEach((post, index) => {
            const postElement = this.createPostElement(post, config);
            
            // Add animation delay for staggered effect
            postElement.style.opacity = '0';
            postElement.style.transform = 'translateY(20px)';
            
            loader.appendChild(postElement);
            
            // Animate in
            setTimeout(() => {
                postElement.style.transition = `opacity ${config.animationDuration}ms ease, transform ${config.animationDuration}ms ease`;
                postElement.style.opacity = '1';
                postElement.style.transform = 'translateY(0)';
            }, index * 50);
        });

        instance.loadedPosts += data.posts.length;
        
        if (append) {
            instance.currentPage++;
        }
    }

    createPostElement(post, config) {
        const wrapper = document.createElement('div');
        wrapper.className = `apl_post_wraper ${post.class} ${!post.thumbnail ? 'no_thumbnail' : ''}`;
        
        if (config.blockStyle === '1' || config.blockStyle === '2') {
            wrapper.innerHTML = this.getPostTemplate(post, config);
        } else if (config.blockStyle === '3') {
            wrapper.innerHTML = this.getPostTemplate3(post, config);
        }
        
        return wrapper;
    }

    getPostTemplate(post, config) {
        return `
            <div class="apl_thumnbail_wrap">
                ${post.thumbnail ? `
                    <a href="${post.permalink}" class="permalink_thumn">
                        <img src="${post.thumbnail}" alt="${post.thumbnail_alt || post.title}" loading="lazy" />
                    </a>
                ` : ''}
                ${config.blockStyle === '1' && post.cats ? `
                    <div class="apl_cat_wraper">${post.cats}</div>
                ` : ''}
            </div>
            <div class="apl_content_wraper">
                ${config.blockStyle === '2' && post.cats ? `
                    <div class="apl_cat_wraper2">${post.cats}</div>
                ` : ''}
                <a class="apl_title_permalink" href="${post.permalink}">
                    <h2 class="apl_post_title" title="${post.title}">${post.title_excerpt}</h2>
                </a>
                <div class="apl_post_meta">
                    <span class="apl_post_author apl_post_meta_item">
                        <a href="${post.author.link}">
                            <img src="${post.author.avatar}" alt="${post.author.name}" class="author-avatar" />
                            ${post.author.name}
                        </a>
                    </span>
                    <span class="apl_post_date apl_post_meta_item">
                        <time datetime="${post.date.iso}">${post.date.formatted}</time>
                    </span>
                    <span class="apl_post_readtime apl_post_meta_item">${post.read_time}</span>
                </div>
                <p>${post.content}</p>
                ${config.blockStyle === '2' ? `
                    <a href="${post.permalink}" class="apl_read_more_btn">Read More</a>
                ` : ''}
            </div>
        `;
    }

    getPostTemplate3(post, config) {
        return `
            <div class="posts_wrapper_inner">
                ${post.thumbnail ? `
                    <div class="post_thumb">
                        <a href="${post.permalink}" class="post_permalink">
                            <img src="${post.thumbnail}" alt="${post.thumbnail_alt || post.title}" loading="lazy" />
                        </a>
                    </div>
                ` : ''}
                <div class="post_content">
                    <h3 class="post_title">
                        <a href="${post.permalink}" class="post_permalink">${post.title_excerpt}</a>
                    </h3>
                    <ul class="post_meta">
                         <li class="post_meta_list">
                            <i class="far fa-calendar-alt"></i>
                            <time datetime="${post.date.iso}">${post.date.formatted}</time>
                        </li>
                        <li class="post_author">
                            <i class="far fa-user"></i> ${post.author.name}
                        </li>
                        <li class="post_comment">
                            <i class="far fa-comments"></i> ${post.comment_text}
                        </li>
                        <li class="post_read_time">
                            <i class="far fa-clock"></i> ${post.read_time}
                        </li>
                    </ul>
                </div>
            </div>
        `;
    }

    renderSearchResults(instanceId, data) {
        const instance = this.instances.get(instanceId);
        const loader = instance.block.querySelector('.ajaxpost_loader');
        
        if (!loader) return;

        loader.innerHTML = '';
        
        if (data.results && data.results.length > 0) {
            const searchResults = document.createElement('div');
            searchResults.className = 'lma-search-results';
            searchResults.innerHTML = `
                <div class="search-header">
                    <h3>Search Results for "${data.search_term}" (${data.total} found)</h3>
                </div>
            `;
            
            data.results.forEach(result => {
                const resultElement = document.createElement('div');
                resultElement.className = 'search-result-item';
                resultElement.innerHTML = `
                    <h4><a href="${result.permalink}">${result.title}</a></h4>
                    <p>${result.excerpt}</p>
                    <small>${result.date}</small>
                `;
                searchResults.appendChild(resultElement);
            });
            
            loader.appendChild(searchResults);
        } else {
            loader.innerHTML = '<div class="no-search-results">No posts found matching your search.</div>';
        }
    }

    updatePagination(instanceId, pagination) {
        const instance = this.instances.get(instanceId);
        const button = instance.block.querySelector('.loadmore_ajax');
        
        if (button) {
            if (pagination.has_more) {
                button.style.display = 'block';
                button.textContent = instance.config.loadMoreText;
                button.disabled = false;
            } else {
                button.textContent = instance.config.noMoreText;
                button.disabled = true;
            }
        }

        instance.totalPages = pagination.total_pages;
        instance.totalPosts = pagination.total_posts;
    }

    showLoading(instanceId) {
        const instance = this.instances.get(instanceId);
        const button = instance.block.querySelector('.loadmore_ajax');
        const loader = instance.block.querySelector('.ajaxpost_loader');
        
        if (button) {
            button.textContent = instance.config.loadingText;
            button.disabled = true;
            button.classList.add('loading_btn');
        }

        // Add loading overlay
        if (loader && !loader.querySelector('.loading_overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'loading_overlay';
            overlay.innerHTML = '<div class="loading-spinner"></div>';
            loader.appendChild(overlay);
        }
    }

    hideLoading(instanceId) {
        const instance = this.instances.get(instanceId);
        const button = instance.block.querySelector('.loadmore_ajax');
        const overlay = instance.block.querySelector('.loading_overlay');
        
        if (button) {
            button.classList.remove('loading_btn');
        }

        if (overlay) {
            overlay.remove();
        }
    }

    showError(instanceId, message) {
        const instance = this.instances.get(instanceId);
        const loader = instance.block.querySelector('.ajaxpost_loader');
        
        if (loader) {
            const errorElement = document.createElement('div');
            errorElement.className = 'lma-error-message';
            errorElement.innerHTML = `
                <p>${message || instance.config.errorText}</p>
                <button type="button" class="retry-btn">Try Again</button>
            `;
            
            loader.appendChild(errorElement);
            
            // Bind retry
            errorElement.querySelector('.retry-btn').addEventListener('click', () => {
                errorElement.remove();
                this.loadPosts(instanceId);
            });
        }
    }

    async makeAjaxRequest(action, data) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', window.load_more_ajax_lite?.nonce || '');
        
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });

        const response = await fetch(window.load_more_ajax_lite?.ajax_url || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Public API methods
    reload(instanceId) {
        this.resetPosts(instanceId);
        this.loadPosts(instanceId);
    }

    setCategory(instanceId, categoryId) {
        const instance = this.instances.get(instanceId);
        if (instance) {
            instance.config.category = categoryId;
            this.reload(instanceId);
        }
    }

    destroy(instanceId) {
        const instance = this.instances.get(instanceId);
        if (instance && instance.scrollHandler) {
            window.removeEventListener('scroll', instance.scrollHandler);
        }
        this.instances.delete(instanceId);
    }
}

// Initialize when DOM is ready
const loadMoreAjax = new LoadMoreAjax();

// Make it globally available
window.LoadMoreAjax = LoadMoreAjax;
window.loadMoreAjaxInstance = loadMoreAjax;