-- Check sample data in product_options for product_id 144
SELECT id, product_id, option_type, name, slug, price_delta, sort_order, is_active
FROM product_options
WHERE product_id = 144
ORDER BY option_type, sort_order;

-- Check product base price  
SELECT id, name, base_price, discount_type, discount_value
FROM products
WHERE id = 144;
