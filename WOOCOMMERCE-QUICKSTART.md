# WooCommerce Quick Start Guide

## 🚀 Get Started in 3 Steps

### Step 1: Verify Requirements
- ✅ WooCommerce is installed and activated
- ✅ You have some products created
- ✅ Plugin files are in place

### Step 2: Add Products to Your Page

Choose one of these methods:

#### Method A: Using Shortcode (Easiest)
1. Edit any page or post
2. Add this shortcode:
```
[lma_products]
```
3. Publish and view!

#### Method B: Using Elementor
1. Open page in Elementor
2. Search for "WooCommerce Products [LMA]"
3. Drag the widget to your page
4. Configure settings in left panel
5. Update page

#### Method C: In Theme Template
Add to your theme file (e.g., `page-shop.php`):
```php
<?php echo do_shortcode('[lma_products]'); ?>
```

### Step 3: Customize (Optional)

Add parameters to customize display:
```
[lma_products column="4" posts_per_page="8" style="2"]
```

## 📋 Common Use Cases

### Show Only Sale Products
```
[lma_products on_sale="true" show_sale_badge="true"]
```

### Featured Products Grid
```
[lma_products featured="true" column="4" style="2"]
```

### Products with Category Filter
```
[lma_products filter="true" column="3"]
```

### Latest Products
```
[lma_products orderby="date" order="DESC"]
```

## 🎨 Quick Styling

Add to **Appearance > Customize > Additional CSS**:

```css
/* Change button color */
.lma_product_cart .button {
    background: #your-color !important;
}

/* Adjust product spacing */
.lma_products_block .ajaxproduct_loader {
    gap: 40px;
}
```

## ⚡ Key Features

- ✨ AJAX load more (no page refresh)
- 🎯 Category filtering
- 📊 Multiple sort options
- 💰 Price display with sales
- ⭐ Star ratings
- 🛒 Add to cart buttons
- 📱 Fully responsive
- 🎨 3 layout styles
- 🚀 Performance optimized

## 🔧 All Shortcode Parameters

| Parameter | Values | Default |
|-----------|--------|---------|
| `column` | 2, 3, 4, 5, full | 3 |
| `posts_per_page` | Any number | 6 |
| `style` | 1, 2, 3 | 1 |
| `filter` | true, false | true |
| `orderby` | date, price, popularity, rating | date |
| `order` | ASC, DESC | DESC |
| `featured` | true, false | false |
| `on_sale` | true, false | false |
| `show_rating` | true, false | true |
| `show_price` | true, false | true |
| `show_cart_button` | true, false | true |

## 📚 Need More Help?

- Check `WOOCOMMERCE-GUIDE.md` for detailed documentation
- See `WOOCOMMERCE-EXAMPLES.php` for code examples
- Review existing products setup in WooCommerce

## 🐛 Troubleshooting

### Products Not Showing?
1. Ensure WooCommerce is active
2. Verify you have published products
3. Clear cache (Ctrl+F5)
4. Check browser console for errors

### Styling Issues?
1. Hard refresh page (Ctrl+Shift+R)
2. Check for theme conflicts
3. Use browser inspector to debug CSS

### AJAX Not Working?
1. Check JavaScript console for errors
2. Verify jQuery is loaded
3. Ensure no plugin conflicts

## 💡 Pro Tips

1. **Start Simple**: Use basic shortcode first, then add parameters
2. **Test Different Layouts**: Try different column and style combinations
3. **Use Category Filter**: Enable filter for better UX
4. **Optimize Images**: Use appropriate product image sizes
5. **Cache Friendly**: Plugin uses built-in caching for speed

## 🎯 Recommended Settings

### For Homepage Hero Section
```
[lma_products featured="true" column="4" posts_per_page="4" filter="false" style="2"]
```

### For Shop Page
```
[lma_products filter="true" enable_sort="true" column="3" posts_per_page="9"]
```

### For Sidebar
```
[lma_products column="2" posts_per_page="4" filter="false" style="1"]
```

### For Sale Page
```
[lma_products on_sale="true" orderby="price" order="ASC" show_sale_badge="true"]
```

---

**Ready to go?** Just add `[lma_products]` to any page and you're live! 🎉
