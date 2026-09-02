-- WhatsMyPhonePrice.com — MySQL schema
-- Charset: utf8mb4 throughout for Urdu text support.

CREATE TABLE IF NOT EXISTS phones (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    variant VARCHAR(50) NOT NULL,          -- e.g. "128GB", "8GB/256GB"
    slug VARCHAR(160) NOT NULL,            -- for /phone/<slug> SEO pages
    release_year SMALLINT UNSIGNED NOT NULL,
    base_price_pkr DECIMAL(10,2) NOT NULL, -- current new-phone price, averaged across sources
    price_sources VARCHAR(255) DEFAULT NULL, -- e.g. "PriceOye, WhatMobile"
    image_url VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    price_updated_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_phones_slug (slug),
    KEY idx_phones_brand_model (brand, model)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,     -- e.g. 03001234567
    phone_verified TINYINT(1) NOT NULL DEFAULT 0,
    otp_code VARCHAR(6) DEFAULT NULL,
    otp_expires_at DATETIME DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,
    rating_avg DECIMAL(3,2) NOT NULL DEFAULT 0,
    rating_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_phone (phone_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS valuations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    phone_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED DEFAULT NULL,     -- nullable: guests can get a valuation

    -- condition inputs (mirrors README "Valuation Form Fields")
    screen_condition ENUM('none','minor','cracked') NOT NULL,
    touch_working TINYINT(1) NOT NULL DEFAULT 1,
    touch_issue_notes VARCHAR(255) DEFAULT NULL,
    front_camera_working TINYINT(1) NOT NULL DEFAULT 1,
    back_camera_working TINYINT(1) NOT NULL DEFAULT 1,

    battery_health_pct TINYINT UNSIGNED DEFAULT NULL, -- optional
    purchase_year SMALLINT UNSIGNED NOT NULL,          -- required fallback age input
    battery_full_day TINYINT(1) DEFAULT NULL,
    battery_drains_fast TINYINT(1) DEFAULT NULL,
    battery_random_shutoff TINYINT(1) DEFAULT NULL,

    water_damage TINYINT(1) NOT NULL DEFAULT 0,
    repair_history ENUM('none','original_parts','non_original_parts') NOT NULL DEFAULT 'none',

    box_included TINYINT(1) NOT NULL DEFAULT 0,
    charger_included TINYINT(1) NOT NULL DEFAULT 0,
    headphones_included TINYINT(1) DEFAULT NULL,

    pta_status ENUM('approved','non_pta','blocked') NOT NULL,
    network_lock ENUM('unlocked','locked') NOT NULL DEFAULT 'unlocked',
    imei VARCHAR(20) DEFAULT NULL,
    bill_available TINYINT(1) NOT NULL DEFAULT 0,
    color VARCHAR(50) DEFAULT NULL,

    -- result
    estimated_price_pkr DECIMAL(10,2) NOT NULL,
    price_breakdown_json TEXT DEFAULT NULL, -- stored deduction-by-deduction breakdown for the result page

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_valuations_phone (phone_id),
    KEY idx_valuations_user (user_id),
    CONSTRAINT fk_valuations_phone FOREIGN KEY (phone_id) REFERENCES phones(id),
    CONSTRAINT fk_valuations_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS valuation_photos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    valuation_id INT UNSIGNED NOT NULL,
    photo_path VARCHAR(255) NOT NULL,      -- real device photos, human/buyer review only (see README AI section)
    uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_valphotos_valuation (valuation_id),
    CONSTRAINT fk_valphotos_valuation FOREIGN KEY (valuation_id) REFERENCES valuations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    valuation_id INT UNSIGNED NOT NULL,    -- condition card is locked from this valuation
    user_id INT UNSIGNED NOT NULL,         -- seller
    title VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    asking_price_pkr DECIMAL(10,2) NOT NULL,
    city VARCHAR(100) NOT NULL,
    area VARCHAR(100) DEFAULT NULL,
    status ENUM('active','sold','removed') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_listings_valuation (valuation_id),
    KEY idx_listings_user (user_id),
    KEY idx_listings_city_status (city, status),
    CONSTRAINT fk_listings_valuation FOREIGN KEY (valuation_id) REFERENCES valuations(id),
    CONSTRAINT fk_listings_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_photos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    listing_id INT UNSIGNED NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_listingphotos_listing (listing_id),
    CONSTRAINT fk_listingphotos_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ratings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    listing_id INT UNSIGNED DEFAULT NULL,  -- deal this rating is about, if any
    rater_user_id INT UNSIGNED NOT NULL,
    rated_user_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,      -- 1-5
    comment VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ratings_rated (rated_user_id),
    KEY idx_ratings_listing (listing_id),
    CONSTRAINT fk_ratings_listing FOREIGN KEY (listing_id) REFERENCES listings(id),
    CONSTRAINT fk_ratings_rater FOREIGN KEY (rater_user_id) REFERENCES users(id),
    CONSTRAINT fk_ratings_rated FOREIGN KEY (rated_user_id) REFERENCES users(id),
    CONSTRAINT chk_rating_range CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
