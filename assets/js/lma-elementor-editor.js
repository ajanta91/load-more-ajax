(function() {
    if (typeof elementor === 'undefined') return;

    elementor.hooks.addAction('panel/open_editor/widget/lma-blog', function(panel, model, view) {
        var debounceTimer;
        function debounce(fn, delay) {
            return function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fn, delay);
            };
        }

        model.on('change:settings', debounce(function() {
            var postType = model.getSetting('lma_post_type');
            if (!postType) return;

            wp.ajax.post('lma_get_taxonomies_and_terms', {
                post_type: postType,
                nonce: lmaElementorEditor.nonce
            }).done(function(data) {
                var taxControl = model.controls.lma_taxonomy;
                if (taxControl) {
                    var newOptions = {};
                    data.taxonomies.forEach(function(tax) {
                        newOptions[tax.slug] = tax.label;
                    });
                    taxControl.options = newOptions;

                    var currentTax = model.getSetting('lma_taxonomy');
                    if (!newOptions[currentTax] && data.taxonomies.length) {
                        model.setSetting('lma_taxonomy', data.taxonomies[0].slug);
                    }
                }

                var termsControl = model.controls.lma_terms;
                if (termsControl) {
                    var selectedTax = model.getSetting('lma_taxonomy');
                    var terms = data.terms[selectedTax] || [];
                    var termOptions = {};
                    terms.forEach(function(term) {
                        termOptions[term.term_id] = term.name;
                    });
                    termsControl.options = termOptions;
                }

                view.renderOnChange(model);
            });
        }, 300));
    });
})();
