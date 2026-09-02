-- Run this once against an existing database that was set up before the
-- Groq price-explainer feature was added (i.e. before this file existed).
-- A fresh install via schema.sql already includes this column.

ALTER TABLE valuations
    ADD COLUMN ai_explanation TEXT DEFAULT NULL AFTER price_breakdown_json;
