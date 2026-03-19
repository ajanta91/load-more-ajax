(function($){

    $(document).ready(function () {

        // ============================================
        // Copy shortcode from block list
        // ============================================
        $(document).on('click', '.copy_block_shortcode', function () {
            const text = this.innerText;
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            $(this).css({ 'background-color': '#2271b1', 'color': '#fff' });
            $(this).after('<span> Copied!</span>');
            setTimeout(() => {
                $(this).css({ 'background-color': '', 'color': '' });
                $(this).next().remove();
            }, 2000);
        });

        // ============================================
        // Block Editor - only run on new/edit pages
        // ============================================
        if ($('#lma-block-form').length === 0) return;

        // --- Style Selector ---
        $(document).on('click', '.lma-style-option', function() {
            $('.lma-style-option').removeClass('active');
            $(this).addClass('active');
            $(this).find('input[type="radio"]').prop('checked', true);

            var selectedStyle = $(this).data('style');

            // Show/hide carousel settings
            if (selectedStyle == '5') {
                $('.lma-carousel-settings').show();
                $('.lma-column-selector').hide();
            } else {
                $('.lma-carousel-settings').hide();
                $('.lma-column-selector').show();
            }

            updatePreview();
        });

        // --- Column Selector ---
        $(document).on('click', '.lma-column-option', function() {
            $('.lma-column-option').removeClass('active');
            $(this).addClass('active');
            $(this).find('input[type="radio"]').prop('checked', true);
            updatePreview();
        });

        // --- Section Toggle ---
        $(document).on('click', '.lma-section-header', function() {
            $(this).closest('.lma-section').toggleClass('collapsed');
        });

        // --- Category checkboxes -> hidden field sync ---
        function syncCategoryIds(listSelector, hiddenSelector) {
            var ids = [];
            $(listSelector + ' input[type="checkbox"]:checked').each(function() {
                ids.push($(this).val());
            });
            $(hiddenSelector).val(ids.join(','));
        }

        $(document).on('change', '#lma-include-cats input', function() {
            syncCategoryIds('#lma-include-cats', '#lma-include-ids');
            updatePreview();
        });

        $(document).on('change', '#lma-exclude-cats input', function() {
            syncCategoryIds('#lma-exclude-cats', '#lma-exclude-ids');
            updatePreview();
        });

        // --- Copy shortcode button ---
        $(document).on('click', '#lma-copy-shortcode', function() {
            var code = $('#lma-shortcode-code').text();
            var textarea = document.createElement('textarea');
            textarea.value = code;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            var $btn = $(this);
            $btn.text('Copied!');
            setTimeout(function() { $btn.text('Copy'); }, 2000);
        });

        // --- Listen for any form change ---
        $(document).on('change input', '#lma-block-form input, #lma-block-form select', function() {
            updatePreview();
        });

        // ============================================
        // Live Preview
        // ============================================
        function getFormValues() {
            return {
                style: $('input[name="block_style"]:checked').val() || '1',
                column: $('input[name="column"]:checked').val() || '4',
                perPage: parseInt($('#post_per_page').val()) || 6,
                filter: $('#is_cat_filter').is(':checked'),
                include: $('#lma-include-ids').val() || '',
                exclude: $('#lma-exclude-ids').val() || '',
                textLimit: parseInt($('#text_limit').val()) || 10,
                titleLimit: parseInt($('#title_limit').val()) || 30,
            };
        }

        function getColCount(columnVal) {
            var map = { '6': 2, '4': 3, '3': 4 };
            return map[columnVal] || 3;
        }

        function buildPreviewHTML(vals) {
            var colCount = getColCount(vals.column);
            var html = '';

            // Category filter pills
            if (vals.filter) {
                html += '<div class="lma-preview-filters">';
                html += '<span class="lma-preview-filter-pill active">All</span>';
                html += '<span class="lma-preview-filter-pill">Category 1</span>';
                html += '<span class="lma-preview-filter-pill">Category 2</span>';
                html += '<span class="lma-preview-filter-pill">Category 3</span>';
                html += '</div>';
            }

            // Grid classes
            var gridClass = 'lma-preview-grid';
            var cardClass = 'lma-preview-card';

            if (vals.style === '2') {
                gridClass += ' style-list';
                cardClass += ' list-style';
            } else if (vals.style === '3') {
                cardClass += ' card-style';
                gridClass += ' cols-' + colCount;
            } else if (vals.style === '4') {
                gridClass += ' cols-' + colCount + ' style-masonry-preview';
            } else if (vals.style === '5') {
                gridClass += ' style-carousel-preview';
            } else {
                gridClass += ' cols-' + colCount;
            }

            html += '<div class="' + gridClass + '">';

            if (vals.style === '5') {
                // Carousel preview
                html += '<div class="carousel-preview-arrow left">&lsaquo;</div>';
                html += '<div class="carousel-preview-slides">';
                var slideCount = Math.min(vals.perPage, 4);
                for (var i = 0; i < slideCount; i++) {
                    html += '<div class="' + cardClass + '">';
                    html += '<div class="card-img"></div>';
                    html += '<div class="card-body">';
                    html += '<div class="card-title"></div>';
                    html += '<div class="card-text"></div>';
                    html += '<div class="card-text short"></div>';
                    html += '</div></div>';
                }
                html += '</div>';
                html += '<div class="carousel-preview-arrow right">&rsaquo;</div>';
                html += '</div>';
                html += '<div style="text-align:center;margin-top:8px;">';
                html += '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#2271b1;margin:0 3px;"></span>';
                html += '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#ddd;margin:0 3px;"></span>';
                html += '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#ddd;margin:0 3px;"></span>';
                html += '</div>';
            } else {
                var cardCount = Math.min(vals.perPage, 6);
                var heights = [80, 60, 100, 70, 90, 65];
                for (var i = 0; i < cardCount; i++) {
                    html += '<div class="' + cardClass + '">';
                    if (vals.style === '4') {
                        html += '<div class="card-img" style="height:' + heights[i % heights.length] + 'px;"></div>';
                    } else {
                        html += '<div class="card-img"></div>';
                    }
                    html += '<div class="card-body">';
                    html += '<div class="card-title"></div>';
                    html += '<div class="card-text"></div>';
                    html += '<div class="card-text short"></div>';
                    html += '<div class="card-meta">';
                    html += '<span class="card-avatar"></span>';
                    html += '<span class="card-meta-line"></span>';
                    html += '</div>';
                    html += '</div></div>';
                }

                html += '</div>';

                // Load more button
                html += '<div class="lma-preview-loadmore"><span>Load More</span></div>';
            }

            return html;
        }

        function buildShortcode(vals) {
            var sc = '[load_more_ajax_lite';
            sc += ' post_type="post"';
            sc += ' style="' + vals.style + '"';
            sc += ' posts_per_page="' + vals.perPage + '"';

            var colMap = { '6': '2', '4': '3', '3': '4' };
            sc += ' column="' + (colMap[vals.column] || '3') + '"';

            sc += ' filter="' + (vals.filter ? 'true' : 'false') + '"';

            if (vals.include) sc += ' include="' + vals.include + '"';
            if (vals.exclude) sc += ' exclude="' + vals.exclude + '"';
            if (vals.textLimit) sc += ' text_limit="' + vals.textLimit + '"';
            if (vals.titleLimit) sc += ' title_limit="' + vals.titleLimit + '"';

            if (vals.style == '5') {
                sc += ' slides_per_view="' + ($('select[name="slides_per_view"]').val() || '3') + '"';
                sc += ' show_arrows="' + ($('input[name="show_arrows"]').is(':checked') ? 'true' : 'false') + '"';
                sc += ' show_dots="' + ($('input[name="show_dots"]').is(':checked') ? 'true' : 'false') + '"';
                sc += ' autoplay="' + ($('input[name="autoplay"]').is(':checked') ? 'true' : 'false') + '"';
            }

            sc += ']';
            return sc;
        }

        function updatePreview() {
            var vals = getFormValues();
            var previewHTML = buildPreviewHTML(vals);
            $('#lma-live-preview').html(previewHTML);

            // Update shortcode display
            var shortcode = buildShortcode(vals);
            $('#lma-shortcode-code').text(shortcode);
            $('#lma-shortcode-display').show();
        }

        // Initial preview render
        updatePreview();
    });

})(jQuery);
