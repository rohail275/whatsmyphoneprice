-- Starting phones/pricing data — 18 popular models sold in Pakistan.
-- base_price_pkr is an average of prices observed on PriceOye.pk and
-- WhatMobile.com.pk (Sept 2026). These drift daily with USD/PKR rate —
-- re-run the base-price aggregation script (see README Build Order #3)
-- regularly to keep this table current; treat these as launch-day seed
-- values, not a permanent source of truth.

INSERT INTO phones (brand, model, variant, slug, release_year, base_price_pkr, price_sources, price_updated_at) VALUES
('Apple',   'iPhone 12',            '128GB',        'apple-iphone-12-128gb',            2020, 175000.00, 'WhatMobile, PriceOye', '2026-09-02 00:00:00'),
('Apple',   'iPhone 13',            '128GB',        'apple-iphone-13-128gb',            2021, 180000.00, 'PriceOye, WhatMobile', '2026-09-02 00:00:00'),
('Apple',   'iPhone 14',            '128GB',        'apple-iphone-14-128gb',            2022, 184000.00, 'PriceOye',             '2026-09-02 00:00:00'),
('Apple',   'iPhone 15',            '128GB',        'apple-iphone-15-128gb',            2023, 265000.00, 'PriceOye',             '2026-09-02 00:00:00'),

('Samsung', 'Galaxy A15',           '128GB',        'samsung-galaxy-a15-128gb',         2024,  48000.00, 'PriceOye, WhatMobile', '2026-09-02 00:00:00'),
('Samsung', 'Galaxy A34',           '128GB',        'samsung-galaxy-a34-128gb',         2023, 110000.00, 'WhatMobile',           '2026-09-02 00:00:00'),
('Samsung', 'Galaxy A54',           '256GB',        'samsung-galaxy-a54-256gb',         2023,  93000.00, 'PriceOye',             '2026-09-02 00:00:00'),
('Samsung', 'Galaxy S23',           '256GB',        'samsung-galaxy-s23-256gb',         2023, 230000.00, 'PriceOye, WhatMobile', '2026-09-02 00:00:00'),
('Samsung', 'Galaxy S23 Ultra',     '256GB',        'samsung-galaxy-s23-ultra-256gb',   2023, 270000.00, 'PriceOye',             '2026-09-02 00:00:00'),
('Samsung', 'Galaxy S23 FE',        '128GB',        'samsung-galaxy-s23-fe-128gb',      2023, 110000.00, 'PriceOye',             '2026-09-02 00:00:00'),

('Xiaomi',  'Redmi Note 12',        '128GB',        'xiaomi-redmi-note-12-128gb',       2023,  47000.00, 'PriceOye, WhatMobile', '2026-09-02 00:00:00'),
('Xiaomi',  'Redmi Note 13',        '128GB',        'xiaomi-redmi-note-13-128gb',       2024,  47000.00, 'WhatMobile',           '2026-09-02 00:00:00'),
('Xiaomi',  'Redmi Note 13 Pro',    '256GB',        'xiaomi-redmi-note-13-pro-256gb',   2024,  65000.00, 'WhatMobile',           '2026-09-02 00:00:00'),
('Xiaomi',  'Xiaomi 13 Pro',        '256GB',        'xiaomi-13-pro-256gb',              2023, 215000.00, 'PriceOye, WhatMobile', '2026-09-02 00:00:00'),
('Xiaomi',  'POCO X6 Pro',          '256GB',        'xiaomi-poco-x6-pro-256gb',         2024,  99000.00, 'WhatMobile',           '2026-09-02 00:00:00'),

('OnePlus', 'OnePlus 9 Pro',        '256GB',        'oneplus-9-pro-256gb',              2021, 165000.00, 'WhatMobile',           '2026-09-02 00:00:00'),
('OnePlus', 'OnePlus Nord CE 3',    '128GB',        'oneplus-nord-ce-3-128gb',          2023,  65000.00, 'PriceOye',             '2026-09-02 00:00:00'),
('OnePlus', 'OnePlus 11',           '256GB',        'oneplus-11-256gb',                 2023, 165000.00, 'WhatMobile',           '2026-09-02 00:00:00');
