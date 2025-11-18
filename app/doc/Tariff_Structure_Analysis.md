# TMS - TARIFF STRUCTURE ANALYSIS
## Many-to-Many Relationship: Tariff × Route × Customer

**Created:** 18 November 2025  
**Purpose:** Analisis struktur tarif dengan many-to-many relationship  
**Decision:** ✅ RECOMMENDED (Best practice for TMS pricing)

---

## PROBLEM STATEMENT

### Business Reality:
1. **Satu route** bisa punya **tarif berbeda** untuk **customer berbeda**
   - Example: Route Cikupa-Bandung
     * AQUA (contract): Rp 800,000 (discount 15%)
     * Indofood (spot): Rp 950,000 (no discount)
     * Wings (hybrid): Rp 850,000 (discount 10%)

2. **Satu customer** bisa punya **tarif berbeda** untuk **route berbeda**
   - Example: AQUA
     * Short haul (< 50 km): Rp 12,000/km
     * Medium haul (50-150 km): Rp 10,000/km
     * Long haul (> 150 km): Rp 8,500/km

3. **Tarif berubah** berdasarkan:
   - Contract type (weekly planning vs ad-hoc)
   - Volume commitment (>100 trips/month = discount)
   - Seasonal pricing (peak season +20%)
   - Truck type (JUGRACK vs PALLET)
   - Time window (night shift +30%)

4. **Different Pricing Models** ⭐ NEW:
   - **TRIP-BASED**: Shuttle (Rp 800,000 per trip) - JUGRACK/PALLET
   - **VOLUME-BASED**: Tanker (Rp 50 per liter air) - TANKER
   - **WEIGHT-BASED**: General cargo (Rp 150 per kg)
   - **DISTANCE-BASED**: Ad-hoc (Rp 12,000 per km)
   - **TIME-BASED**: Rental (Rp 100,000 per hour)

---

## SOLUTION: MANY-TO-MANY TARIFF STRUCTURE

### Design Pattern: **Bridge Table with Pricing Attributes**

```
┌─────────────────────────────────────────────────────────────────┐
│                    TARIFF RELATIONSHIP MODEL                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ms_client (1)                                                 │
│   ├─ CUS001: AQUA                                               │
│   ├─ CUS002: Indofood                                           │
│   └─ CUS003: Wings                                              │
│         │                                                       │
│         │ Many-to-Many                                          │
│         ▼                                                       │
│   ms_tariff_contract (Bridge Table) ⭐                          │
│   ├─ Contract terms per client-route                            │
│   ├─ Pricing rules & conditions                                │
│   ├─ Valid period                                               │
│   └─ Discount tiers                                             │
│         │                                                       │
│         │ Many-to-Many                                          │
│         ▼                                                       │
│   ms_route (1)                                                  │
│   ├─ ROU001: Cikupa-Bandung                                     │
│   ├─ ROU002: Sentul-Ciputat                                     │
│   └─ ROU003: Mekarsari-Kawasan                                  │
│                                                                 │
│   PLUS: Additional dimensions                                   │
│   ├─ ms_truck_type (JUGRACK, PALLET)                            │
│   ├─ ms_time_window (Window 1-12)                               │
│   └─ Season/period (PEAK, NORMAL, LOW)                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## PROPOSED TABLE STRUCTURE

### 1. ms_tariff_contract (Main Tariff Contract)

**Purpose**: Master contract antara client dan route dengan pricing

```sql
CREATE TABLE ms_tariff_contract (
    -- Primary Key
    contract_id VARCHAR(20) PRIMARY KEY,  -- TC001, TC002
    contract_number VARCHAR(50) UNIQUE,  -- TC-AQUA-2025-001
    
    -- Parties
    client_id VARCHAR(20) NOT NULL,
    
    -- Contract Info
    contract_name NVARCHAR(200),  -- "AQUA Weekly Shuttle Contract 2025"
    contract_type VARCHAR(20),  -- WEEKLY_PLANNING, ADHOC, HYBRID
    
    -- Period
    valid_from DATE NOT NULL,
    valid_to DATE NOT NULL,
    is_active BIT DEFAULT 1,
    auto_renew BIT DEFAULT 0,
    
    -- Volume Commitment
    minimum_trips_per_month INT,  -- Min trips to get discount
    maximum_trips_per_month INT,  -- Cap for volume pricing
    
    -- Pricing Model
    pricing_model VARCHAR(20),  -- PER_TRIP, PER_KM, PER_HOUR, PER_KG
    base_currency VARCHAR(3) DEFAULT 'IDR',
    
    -- Payment Terms
    payment_terms VARCHAR(20),  -- NET30, NET45, NET60, COD
    payment_method VARCHAR(20),  -- BANK_TRANSFER, CASH, GIRO
    credit_limit DECIMAL(15,2),
    
    -- Discount Structure
    volume_discount_tier1_trips INT,  -- >50 trips
    volume_discount_tier1_percent DECIMAL(5,2),  -- 5%
    volume_discount_tier2_trips INT,  -- >100 trips
    volume_discount_tier2_percent DECIMAL(5,2),  -- 10%
    volume_discount_tier3_trips INT,  -- >200 trips
    volume_discount_tier3_percent DECIMAL(5,2),  -- 15%
    
    early_payment_discount_days INT,  -- Payment within 7 days
    early_payment_discount_percent DECIMAL(5,2),  -- 2%
    
    -- Penalties
    late_cancellation_penalty_hours INT,  -- <24h cancellation
    late_cancellation_penalty_percent DECIMAL(5,2),  -- 50%
    no_show_penalty_amount DECIMAL(15,2),
    
    -- Special Terms
    backhaul_allowed BIT DEFAULT 1,
    backhaul_revenue_share_percent DECIMAL(5,2),  -- 30% to client
    overtime_surcharge_percent DECIMAL(5,2),  -- +20%
    night_shift_surcharge_percent DECIMAL(5,2),  -- +30%
    weekend_surcharge_percent DECIMAL(5,2),  -- +25%
    
    -- Status
    status VARCHAR(20) DEFAULT 'DRAFT',
    -- Status: DRAFT → PENDING_APPROVAL → ACTIVE → EXPIRED → TERMINATED
    
    -- Approval
    approved_at DATETIME,
    approved_by VARCHAR(20),
    
    -- Notes
    terms_and_conditions NVARCHAR(MAX),
    special_notes NVARCHAR(1000),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    created_by VARCHAR(20),
    updated_by VARCHAR(20),
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id)
);

CREATE INDEX idx_contract_client ON ms_tariff_contract(client_id, valid_from);
CREATE INDEX idx_contract_period ON ms_tariff_contract(valid_from, valid_to, is_active);
CREATE INDEX idx_contract_status ON ms_tariff_contract(status);
```

---

### 2. ms_tariff_rate (Route-specific Pricing Detail)

**Purpose**: Bridge table untuk many-to-many Contract × Route dengan pricing detail

```sql
CREATE TABLE ms_tariff_rate (
    -- Primary Key
    rate_id VARCHAR(20) PRIMARY KEY,  -- RT001, RT002
    
    -- Link to Contract & Route
    contract_id VARCHAR(20) NOT NULL,
    route_id VARCHAR(20) NOT NULL,
    
    -- Rate Type
    rate_type VARCHAR(20),  -- STANDARD, BACKHAUL, URGENT, SPOT
    
    -- Pricing Model Selection ⭐
    pricing_model VARCHAR(20),  -- TRIP, DISTANCE, VOLUME, WEIGHT, TIME
    -- Models:
    --   TRIP: Per trip (shuttle, regular delivery)
    --   DISTANCE: Per kilometer (ad-hoc, flexible routes)
    --   VOLUME: Per liter/m3 (tanker, liquid, gas)
    --   WEIGHT: Per kilogram/ton (general cargo)
    --   TIME: Per hour (rental, charter)
    
    -- Base Pricing (choose based on pricing_model)
    rate_per_trip DECIMAL(15,2),  -- For TRIP model
    rate_per_km DECIMAL(10,2),  -- For DISTANCE model
    rate_per_liter DECIMAL(10,4),  -- ⭐ For VOLUME model (tanker air)
    rate_per_m3 DECIMAL(12,2),  -- For VOLUME model (gas, chemical)
    rate_per_kg DECIMAL(8,2),  -- For WEIGHT model
    rate_per_ton DECIMAL(12,2),  -- For WEIGHT model (bulk)
    rate_per_hour DECIMAL(10,2),  -- For TIME model
    
    -- Volume-based Specific (for Tanker) ⭐
    minimum_volume_liters DECIMAL(10,2),  -- Min volume to charge
    maximum_volume_liters DECIMAL(10,2),  -- Tanker capacity limit
    volume_tolerance_percent DECIMAL(5,2),  -- Acceptable variance (±2%)
    
    -- Measurement Method (for volume)
    volume_measurement_method VARCHAR(20),  -- FLOWMETER, TANK_DIP, MANUAL
    requires_loading_measurement BIT DEFAULT 0,  -- Measure at loading?
    requires_unloading_measurement BIT DEFAULT 0,  -- Measure at unloading?
    charge_based_on VARCHAR(20),  -- LOADED, DELIVERED, AVERAGE
    
    -- Minimum & Maximum Charges
    minimum_charge DECIMAL(15,2),  -- Min charge even if small volume/distance
    maximum_charge DECIMAL(15,2),  -- Cap for large volume/distance
    
    -- Truck Type Pricing (different rates for different trucks)
    truck_type_id VARCHAR(20),  -- Optional: specific truck type
    truck_type_multiplier DECIMAL(5,2),  -- 1.0 = standard, 1.2 = +20% for larger truck
    
    -- Time Window Surcharges
    window_1_surcharge_percent DECIMAL(5,2),  -- 00:00-02:00: +30%
    window_2_surcharge_percent DECIMAL(5,2),  -- 02:00-04:00: +30%
    window_3_surcharge_percent DECIMAL(5,2),  -- 04:00-06:00: +20%
    window_5_surcharge_percent DECIMAL(5,2),  -- 08:00-10:00: 0%
    window_6_surcharge_percent DECIMAL(5,2),  -- 10:00-12:00: 0%
    window_7_surcharge_percent DECIMAL(5,2),  -- 12:00-14:00: 0%
    window_8_surcharge_percent DECIMAL(5,2),  -- 14:00-16:00: 0%
    window_9_surcharge_percent DECIMAL(5,2),  -- 16:00-18:00: +10%
    window_10_surcharge_percent DECIMAL(5,2), -- 18:00-20:00: +20%
    window_11_surcharge_percent DECIMAL(5,2), -- 20:00-22:00: +30%
    window_12_surcharge_percent DECIMAL(5,2), -- 22:00-00:00: +30%
    
    -- Additional Charges
    loading_fee DECIMAL(15,2),  -- Fee for loading assistance
    unloading_fee DECIMAL(15,2),  -- Fee for unloading
    waiting_time_rate_per_hour DECIMAL(10,2),  -- Charge if delayed
    detention_fee_per_hour DECIMAL(10,2),  -- If truck detained
    
    -- Fuel Surcharge (dynamic)
    fuel_surcharge_enabled BIT DEFAULT 0,
    fuel_surcharge_formula VARCHAR(100),  -- e.g., "2% per Rp 1000 above Rp 12000"
    
    -- Toll & Parking
    toll_cost_included BIT DEFAULT 1,  -- Toll included in rate?
    parking_cost_included BIT DEFAULT 1,
    estimated_toll_cost DECIMAL(15,2),
    estimated_parking_cost DECIMAL(15,2),
    
    -- Valid Period (can override contract period)
    valid_from DATE,
    valid_to DATE,
    is_active BIT DEFAULT 1,
    
    -- Priority
    priority INT DEFAULT 1,  -- If multiple rates match, use highest priority
    
    -- Notes
    rate_notes NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    created_by VARCHAR(20),
    updated_by VARCHAR(20),
    
    FOREIGN KEY (contract_id) REFERENCES ms_tariff_contract(contract_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (truck_type_id) REFERENCES ms_truck_type(truck_type_id),
    
    -- Unique constraint: One rate per contract-route-truck type combination
    UNIQUE (contract_id, route_id, truck_type_id, rate_type)
);

CREATE INDEX idx_rate_contract ON ms_tariff_rate(contract_id, is_active);
CREATE INDEX idx_rate_route ON ms_tariff_rate(route_id, is_active);
CREATE INDEX idx_rate_truck ON ms_tariff_rate(truck_type_id);
CREATE INDEX idx_rate_period ON ms_tariff_rate(valid_from, valid_to);
```

---

### 3. ms_tariff_special_condition (Special Pricing Rules)

**Purpose**: Store special conditions untuk pricing exceptions

```sql
CREATE TABLE ms_tariff_special_condition (
    -- Primary Key
    condition_id VARCHAR(20) PRIMARY KEY,
    
    -- Link to Rate
    rate_id VARCHAR(20) NOT NULL,
    
    -- Condition Type
    condition_type VARCHAR(50),
    -- Types: SEASONAL, PROMOTIONAL, VOLUME_BASED, WEATHER_BASED, HOLIDAY
    
    -- Condition Rules (JSON or specific fields)
    condition_name NVARCHAR(200),
    condition_description NVARCHAR(500),
    
    -- Date-based Conditions
    applicable_from DATE,
    applicable_to DATE,
    applicable_days_of_week VARCHAR(50),  -- "MON,TUE,WED" or "ALL" or "WEEKDAY" or "WEEKEND"
    
    -- Volume-based Conditions
    min_trips_per_day INT,
    max_trips_per_day INT,
    min_trips_per_week INT,
    max_trips_per_week INT,
    
    -- Weather-based (optional, advanced)
    weather_condition VARCHAR(50),  -- RAIN, FLOOD, EXTREME_HEAT
    
    -- Holiday-based
    is_public_holiday BIT DEFAULT 0,
    
    -- Price Adjustment
    adjustment_type VARCHAR(20),  -- DISCOUNT, SURCHARGE, FIXED_PRICE
    adjustment_value DECIMAL(15,2),  -- Amount or percentage
    adjustment_percent DECIMAL(5,2),
    
    -- Priority
    priority INT DEFAULT 1,
    
    -- Status
    is_active BIT DEFAULT 1,
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (rate_id) REFERENCES ms_tariff_rate(rate_id)
);

CREATE INDEX idx_condition_rate ON ms_tariff_special_condition(rate_id, is_active);
CREATE INDEX idx_condition_period ON ms_tariff_special_condition(applicable_from, applicable_to);
```

---

## REAL EXAMPLE: AQUA CONTRACT

### Example 1: AQUA Weekly Shuttle Contract

```sql
-- Step 1: Create Contract
INSERT INTO ms_tariff_contract (
    contract_id, contract_number, client_id,
    contract_name, contract_type,
    valid_from, valid_to, is_active,
    minimum_trips_per_month, maximum_trips_per_month,
    pricing_model, payment_terms,
    volume_discount_tier1_trips, volume_discount_tier1_percent,
    volume_discount_tier2_trips, volume_discount_tier2_percent,
    volume_discount_tier3_trips, volume_discount_tier3_percent,
    backhaul_allowed, backhaul_revenue_share_percent,
    night_shift_surcharge_percent,
    status, created_by
) VALUES (
    'TC001', 'TC-AQUA-2025-001', 'CUS001',
    'AQUA Weekly Shuttle Contract 2025', 'WEEKLY_PLANNING',
    '2025-01-01', '2025-12-31', 1,
    100, 500,  -- Min 100, max 500 trips/month
    'PER_TRIP', 'NET30',
    50, 5.00,   -- >50 trips = 5% discount
    100, 10.00, -- >100 trips = 10% discount
    200, 15.00, -- >200 trips = 15% discount
    1, 30.00,  -- Backhaul allowed, 30% revenue share
    30.00,  -- Night shift +30%
    'ACTIVE', 'MGR001'
);

-- Step 2: Create Route-specific Rates

-- Rate 1: Ciherang → Palapa (Short haul)
INSERT INTO ms_tariff_rate (
    rate_id, contract_id, route_id,
    rate_type, rate_per_trip, minimum_charge,
    truck_type_id, truck_type_multiplier,
    window_1_surcharge_percent, window_2_surcharge_percent,
    window_11_surcharge_percent, window_12_surcharge_percent,
    toll_cost_included, estimated_toll_cost,
    is_active, created_by
) VALUES (
    'RT001', 'TC001', 'ROU001',  -- Ciherang-Palapa
    'STANDARD', 800000.00, 700000.00,  -- Rp 800K per trip, min Rp 700K
    'TRK001', 1.0,  -- JUGRACK Green, standard multiplier
    30.00, 30.00,  -- Night windows (1, 2): +30%
    30.00, 30.00,  -- Night windows (11, 12): +30%
    1, 15000.00,  -- Toll included, estimated Rp 15K
    1, 'MGR001'
);

-- Rate 2: Mekarsari → Bandung (Long haul)
INSERT INTO ms_tariff_rate (
    rate_id, contract_id, route_id,
    rate_type, rate_per_trip, minimum_charge,
    truck_type_id, truck_type_multiplier,
    window_10_surcharge_percent, window_11_surcharge_percent,
    toll_cost_included, estimated_toll_cost,
    is_active, created_by
) VALUES (
    'RT002', 'TC001', 'ROU003',  -- Mekarsari-Bandung (180 km)
    'STANDARD', 2500000.00, 2200000.00,  -- Rp 2.5M per trip (long haul)
    'TRK004', 1.0,  -- ODOL-864
    20.00, 30.00,  -- Evening/night: +20%, +30%
    1, 50000.00,  -- Toll included, Rp 50K
    1, 'MGR001'
);

-- Rate 3: Same route, different truck (PALLET)
INSERT INTO ms_tariff_rate (
    rate_id, contract_id, route_id,
    rate_type, rate_per_trip,
    truck_type_id, truck_type_multiplier,
    is_active, created_by
) VALUES (
    'RT003', 'TC001', 'ROU001',  -- Same Ciherang-Palapa
    'STANDARD', 800000.00,
    'TRK005', 1.15,  -- PALLET truck: +15% (larger capacity)
    1, 'MGR001'
);

-- Step 3: Add Special Conditions

-- Condition 1: Peak Season Surcharge (Dec-Jan)
INSERT INTO ms_tariff_special_condition (
    condition_id, rate_id,
    condition_type, condition_name,
    applicable_from, applicable_to,
    adjustment_type, adjustment_percent,
    is_active
) VALUES (
    'SC001', 'RT001',
    'SEASONAL', 'Peak Season Surcharge (Year-end)',
    '2025-12-01', '2026-01-15',
    'SURCHARGE', 20.00,  -- +20% during peak
    1
);

-- Condition 2: Rainy Season Surcharge (Nov-Feb)
INSERT INTO ms_tariff_special_condition (
    condition_id, rate_id,
    condition_type, condition_name,
    applicable_from, applicable_to,
    weather_condition,
    adjustment_type, adjustment_percent,
    is_active
) VALUES (
    'SC002', 'RT002',
    'WEATHER_BASED', 'Rainy Season Surcharge (Bandung route)',
    '2025-11-01', '2026-02-28',
    'RAIN',
    'SURCHARGE', 15.00,  -- +15% for rain risk
    1
);

-- Condition 3: Weekend Discount (to fill capacity)
INSERT INTO ms_tariff_special_condition (
    condition_id, rate_id,
    condition_type, condition_name,
    applicable_days_of_week,
    adjustment_type, adjustment_percent,
    is_active
) VALUES (
    'SC003', 'RT001',
    'PROMOTIONAL', 'Weekend Fill-up Discount',
    'SAT,SUN',
    'DISCOUNT', 10.00,  -- -10% on weekends
    1
);
```

---

## TARIFF CALCULATION LOGIC

### Function: Calculate Trip Tariff

```sql
CREATE FUNCTION fn_CalculateTripTariff (
    @client_id VARCHAR(20),
    @route_id VARCHAR(20),
    @truck_type_id VARCHAR(20),
    @window_id VARCHAR(20),
    @trip_date DATE,
    @is_backhaul BIT = 0
)
RETURNS DECIMAL(15,2)
AS
BEGIN
    DECLARE @base_rate DECIMAL(15,2) = 0;
    DECLARE @final_rate DECIMAL(15,2) = 0;
    DECLARE @window_number INT;
    DECLARE @window_surcharge DECIMAL(5,2) = 0;
    DECLARE @special_adjustment DECIMAL(5,2) = 0;
    DECLARE @volume_discount DECIMAL(5,2) = 0;
    DECLARE @trips_this_month INT;
    
    -- Step 1: Get window number
    SELECT @window_number = window_number 
    FROM ms_time_window 
    WHERE window_id = @window_id;
    
    -- Step 2: Get base rate from active contract
    SELECT TOP 1 
        @base_rate = rate_per_trip,
        @window_surcharge = CASE @window_number
            WHEN 1 THEN window_1_surcharge_percent
            WHEN 2 THEN window_2_surcharge_percent
            WHEN 3 THEN window_3_surcharge_percent
            WHEN 5 THEN window_5_surcharge_percent
            WHEN 6 THEN window_6_surcharge_percent
            WHEN 7 THEN window_7_surcharge_percent
            WHEN 8 THEN window_8_surcharge_percent
            WHEN 9 THEN window_9_surcharge_percent
            WHEN 10 THEN window_10_surcharge_percent
            WHEN 11 THEN window_11_surcharge_percent
            WHEN 12 THEN window_12_surcharge_percent
            ELSE 0
        END
    FROM ms_tariff_rate r
    INNER JOIN ms_tariff_contract c ON r.contract_id = c.contract_id
    WHERE c.client_id = @client_id
      AND r.route_id = @route_id
      AND r.truck_type_id = @truck_type_id
      AND r.is_active = 1
      AND c.is_active = 1
      AND @trip_date BETWEEN c.valid_from AND c.valid_to
      AND @trip_date BETWEEN r.valid_from AND r.valid_to
    ORDER BY r.priority DESC;
    
    -- Step 3: Apply window surcharge
    SET @final_rate = @base_rate * (1 + @window_surcharge / 100);
    
    -- Step 4: Check special conditions
    SELECT @special_adjustment = ISNULL(SUM(
        CASE adjustment_type
            WHEN 'SURCHARGE' THEN adjustment_percent
            WHEN 'DISCOUNT' THEN -adjustment_percent
            ELSE 0
        END
    ), 0)
    FROM ms_tariff_special_condition sc
    INNER JOIN ms_tariff_rate r ON sc.rate_id = r.rate_id
    INNER JOIN ms_tariff_contract c ON r.contract_id = c.contract_id
    WHERE c.client_id = @client_id
      AND r.route_id = @route_id
      AND sc.is_active = 1
      AND @trip_date BETWEEN sc.applicable_from AND sc.applicable_to
      AND (sc.applicable_days_of_week = 'ALL' 
           OR sc.applicable_days_of_week LIKE '%' + DATENAME(WEEKDAY, @trip_date) + '%');
    
    -- Step 5: Apply special conditions
    SET @final_rate = @final_rate * (1 + @special_adjustment / 100);
    
    -- Step 6: Calculate volume discount
    SELECT @trips_this_month = COUNT(*)
    FROM ms_dispatch d
    INNER JOIN ms_planning_weekly p ON d.planning_id = p.planning_id
    WHERE p.client_id = @client_id
      AND MONTH(d.scheduled_date) = MONTH(@trip_date)
      AND YEAR(d.scheduled_date) = YEAR(@trip_date)
      AND d.status = 'COMPLETED';
    
    SELECT @volume_discount = CASE
        WHEN @trips_this_month >= volume_discount_tier3_trips THEN volume_discount_tier3_percent
        WHEN @trips_this_month >= volume_discount_tier2_trips THEN volume_discount_tier2_percent
        WHEN @trips_this_month >= volume_discount_tier1_trips THEN volume_discount_tier1_percent
        ELSE 0
    END
    FROM ms_tariff_contract
    WHERE client_id = @client_id
      AND is_active = 1
      AND @trip_date BETWEEN valid_from AND valid_to;
    
    -- Step 7: Apply volume discount
    SET @final_rate = @final_rate * (1 - @volume_discount / 100);
    
    -- Step 8: Backhaul rate (if applicable)
    IF @is_backhaul = 1
    BEGIN
        SET @final_rate = @final_rate * 0.7;  -- Backhaul typically 70% of standard
    END
    
    RETURN ROUND(@final_rate, 0);  -- Round to nearest Rupiah
END;
GO
```

### Usage Example:

```sql
-- Calculate tariff for AQUA trip
SELECT dbo.fn_CalculateTripTariff(
    'CUS001',      -- AQUA
    'ROU001',      -- Ciherang-Palapa
    'TRK001',      -- JUGRACK Green
    'WIN002',      -- Window 2 (02:00-04:00)
    '2025-11-18',  -- Monday
    0              -- Not backhaul
) AS calculated_tariff;

-- Result: Rp 1,040,000
-- Breakdown:
--   Base rate: Rp 800,000
--   Window 2 surcharge (+30%): Rp 240,000
--   Volume discount (100 trips, -10%): -Rp 104,000
--   Final: Rp 1,040,000
```

---

## ADVANTAGES vs DISADVANTAGES

### ✅ ADVANTAGES (Many-to-Many)

1. **Flexibility**
   - Beda customer, beda harga untuk route sama
   - Beda route, beda pricing model untuk customer sama
   - Easy to add new pricing rules without schema changes

2. **Contract Management**
   - One contract can cover multiple routes
   - One route can have multiple contracts (different customers)
   - Easy to expire/renew contracts

3. **Dynamic Pricing**
   - Time-based surcharges (night shift, weekend)
   - Volume-based discounts (loyalty program)
   - Seasonal adjustments (peak season, rainy season)
   - Weather-based pricing (flood risk)

4. **Audit Trail**
   - Complete history of rate changes
   - Who approved what rate when
   - Easy to track pricing evolution

5. **Scalability**
   - Add new customers without changing structure
   - Add new routes without changing structure
   - Add new pricing dimensions (fuel surcharge, etc.)

6. **Business Intelligence**
   - Easy to compare rates across customers
   - Easy to analyze profitability per contract
   - Easy to identify best/worst performing routes

---

### ❌ DISADVANTAGES (Potential Issues)

1. **Complexity**
   - More tables to maintain
   - Complex queries to calculate final price
   - Need stored functions/procedures for pricing logic

2. **Performance**
   - Multiple joins to calculate tariff
   - Need indexing strategy
   - May need caching for frequently-used rates

3. **Data Integrity**
   - Risk of overlapping rate periods
   - Need validation: one active rate per client-route-truck combo
   - Need cascade delete rules

4. **User Interface**
   - Complex UI for rate setup
   - Need wizard/multi-step forms
   - Risk of user input errors

---

## ALTERNATIVE: SIMPLE STRUCTURE (NOT RECOMMENDED)

### Simple Flat Table (❌ Not Flexible)

```sql
-- Bad design: All in one table
CREATE TABLE ms_tariff_simple (
    tariff_id VARCHAR(20),
    client_id VARCHAR(20),
    route_id VARCHAR(20),
    rate DECIMAL(15,2),  -- One rate for all
    -- Problem: How to handle night shift? Weekend? Volume discount?
);
```

**Why NOT recommended:**
- Cannot handle time-based surcharges
- Cannot handle volume discounts
- Cannot handle seasonal pricing
- Cannot handle contract periods
- Hard to audit rate changes

---

## REAL EXAMPLE: AQUA TANKER (VOLUME-BASED PRICING) ⭐

### Example 2: AQUA Water Tanker Contract (Rp 50 per Liter)

**Business Case:**
- AQUA perlu transport air bersih dengan tanker
- Charge berdasarkan volume air yang diterima (liters)
- Measurement di loading point (plant) dan unloading point (destination)
- Bayar berdasarkan volume yang delivered (bukan loaded, karena bisa ada spillage)

```sql
-- Step 1: Create Tanker Contract
INSERT INTO ms_tariff_contract (
    contract_id, contract_number, client_id,
    contract_name, contract_type,
    valid_from, valid_to, is_active,
    minimum_trips_per_month, maximum_trips_per_month,
    pricing_model,  -- ⭐ VOLUME
    payment_terms,
    volume_discount_tier1_trips, volume_discount_tier1_percent,
    volume_discount_tier2_trips, volume_discount_tier2_percent,
    status, created_by
) VALUES (
    'TC002', 'TC-AQUA-TANKER-2025-001', 'CUS001',
    'AQUA Water Tanker Contract 2025', 'ADHOC',
    '2025-01-01', '2025-12-31', 1,
    20, 200,  -- Min 20, max 200 trips/month
    'VOLUME',  -- ⭐ Volume-based pricing
    'NET30',
    50, 5.00,   -- >50 trips = 5% discount
    100, 10.00, -- >100 trips = 10% discount
    'ACTIVE', 'MGR001'
);

-- Step 2: Create Volume-based Rate (Tanker)

-- Rate 1: Plant Sentul → Customer Location A (Tanker 10,000L)
INSERT INTO ms_tariff_rate (
    rate_id, contract_id, route_id,
    rate_type, 
    
    -- ⭐ Pricing Model: VOLUME
    pricing_model,
    rate_per_liter,
    
    -- Volume constraints
    minimum_volume_liters,
    maximum_volume_liters,
    volume_tolerance_percent,
    
    -- Measurement
    volume_measurement_method,
    requires_loading_measurement,
    requires_unloading_measurement,
    charge_based_on,
    
    -- Minimum charge (even if small volume)
    minimum_charge,
    
    -- Truck type
    truck_type_id, truck_type_multiplier,
    
    -- Time window surcharges
    window_1_surcharge_percent,
    window_11_surcharge_percent,
    
    is_active, created_by
) VALUES (
    'RT004', 'TC002', 'ROU005',  -- Sentul → Location A
    'STANDARD',
    
    -- Volume pricing
    'VOLUME',
    50.00,  -- ⭐ Rp 50 per liter
    
    -- Volume constraints
    5000.00,   -- Min 5,000 liters (half tank)
    10000.00,  -- Max 10,000 liters (full tank capacity)
    2.00,      -- ±2% tolerance
    
    -- Measurement method
    'FLOWMETER',  -- Use flowmeter for accurate measurement
    1,  -- YES - Measure at loading
    1,  -- YES - Measure at unloading
    'DELIVERED',  -- ⭐ Charge based on delivered volume (not loaded)
    
    -- Minimum charge
    250000.00,  -- Min charge Rp 250K (even if small volume)
    
    -- Truck type: TANKER
    'TRK006', 1.0,  -- Water tanker 10KL
    
    -- Night shift surcharge
    30.00,  -- Window 1: +30%
    30.00,  -- Window 11: +30%
    
    1, 'MGR001'
);

-- Rate 2: Same route, bigger tanker (15,000L capacity)
INSERT INTO ms_tariff_rate (
    rate_id, contract_id, route_id,
    rate_type, pricing_model, rate_per_liter,
    minimum_volume_liters, maximum_volume_liters,
    volume_measurement_method,
    requires_loading_measurement, requires_unloading_measurement,
    charge_based_on,
    minimum_charge,
    truck_type_id, truck_type_multiplier,
    is_active, created_by
) VALUES (
    'RT005', 'TC002', 'ROU005',
    'STANDARD', 'VOLUME', 48.00,  -- ⭐ Rp 48/L (cheaper for bigger tank)
    8000.00, 15000.00,  -- 8KL - 15KL
    'FLOWMETER', 1, 1, 'DELIVERED',
    350000.00,  -- Higher min charge
    'TRK007', 1.0,  -- Water tanker 15KL
    1, 'MGR001'
);
```

---

### Tanker Dispatch Example (Volume-based)

```sql
-- Create Dispatch for Tanker
INSERT INTO tr_tms_dispatcher_main (
    dispatch_id, dispatch_number, dispatch_type,
    client_id, route_id,
    origin_location_id, destination_location_id,
    vehicle_id, truck_type_id, driver_id,
    scheduled_date, scheduled_departure_time,
    
    -- Cargo (Volume-based) ⭐
    cargo_description,
    qty_planned,  -- Volume planned (liters)
    uom,
    
    -- Pricing
    tariff_contract_id, tariff_rate_id,
    base_rate,  -- Rate per liter
    
    -- Revenue calculation
    gross_revenue,  -- Will be calculated after delivery
    net_revenue,
    
    status, created_by
) VALUES (
    'DSP002', 'SPJ-TANKER-002/TMS/XI/2025', 'ADHOC',
    'CUS001', 'ROU005',
    'LOC003', 'LOC999',  -- Sentul Plant → Customer
    'VEH006', 'TRK006', 'DRV005',
    '2025-11-18', '14:00:00',
    
    -- Cargo
    'Air Bersih (Water Tanker)',
    10000.00,  -- Plan to deliver 10,000 liters
    'LITER',
    
    -- Pricing
    'TC002', 'RT004',
    50.00,  -- Rp 50 per liter
    
    -- Revenue (estimated)
    500000.00,  -- 10,000 L × Rp 50 = Rp 500,000 (estimate)
    500000.00,
    
    'DRAFT', 'DISPATCHER01'
);
```

---

### Tanker Delivery with Volume Measurement

```sql
-- Create detail table untuk tanker measurement
CREATE TABLE tr_tms_tanker_delivery (
    tanker_delivery_id VARCHAR(20) PRIMARY KEY,
    dispatch_id VARCHAR(20) UNIQUE NOT NULL,
    
    -- Loading Measurement ⭐
    loading_location_id VARCHAR(20),
    loading_datetime DATETIME,
    
    loading_measurement_method VARCHAR(20),  -- FLOWMETER, TANK_DIP
    loading_flowmeter_reading_start DECIMAL(10,2),
    loading_flowmeter_reading_end DECIMAL(10,2),
    loading_volume_liters DECIMAL(10,2),  -- Calculated
    
    loading_temperature_celsius DECIMAL(5,2),  -- For volume correction
    loading_density DECIMAL(8,4),  -- kg/L
    
    loading_photo_flowmeter VARCHAR(500),  -- Photo of flowmeter
    loading_measured_by VARCHAR(100),  -- Plant operator name
    loading_verified_by VARCHAR(20),  -- Supervisor
    loading_verified_at DATETIME,
    
    -- Transit
    estimated_spillage_liters DECIMAL(10,2),  -- Expected loss
    
    -- Unloading Measurement ⭐
    unloading_location_id VARCHAR(20),
    unloading_datetime DATETIME,
    
    unloading_measurement_method VARCHAR(20),
    unloading_flowmeter_reading_start DECIMAL(10,2),
    unloading_flowmeter_reading_end DECIMAL(10,2),
    unloading_volume_liters DECIMAL(10,2),  -- ⭐ Actual delivered
    
    unloading_temperature_celsius DECIMAL(5,2),
    unloading_density DECIMAL(8,4),
    
    unloading_photo_flowmeter VARCHAR(500),
    unloading_received_by VARCHAR(100),  -- Customer name
    unloading_verified_by VARCHAR(20),
    unloading_verified_at DATETIME,
    
    -- Variance Analysis
    volume_variance_liters DECIMAL(10,2),  -- Loading - Unloading
    variance_percent DECIMAL(5,2),  -- (Variance / Loading) × 100
    variance_within_tolerance BIT DEFAULT 1,  -- Within ±2%?
    variance_reason NVARCHAR(500),
    
    -- Chargeable Volume ⭐
    chargeable_volume_liters DECIMAL(10,2),  -- Volume to charge
    chargeable_based_on VARCHAR(20),  -- DELIVERED, LOADED, AVERAGE
    
    -- Final Pricing Calculation ⭐
    rate_per_liter DECIMAL(10,4),
    gross_revenue DECIMAL(15,2),  -- Volume × Rate
    surcharges DECIMAL(15,2),
    discounts DECIMAL(15,2),
    net_revenue DECIMAL(15,2),  -- Final amount
    
    -- Quality Check
    water_quality_ok BIT DEFAULT 1,
    quality_test_report VARCHAR(500),
    
    -- Notes
    loading_notes NVARCHAR(500),
    unloading_notes NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (dispatch_id) REFERENCES tr_tms_dispatcher_main(dispatch_id)
);

-- Example: Record tanker delivery
INSERT INTO tr_tms_tanker_delivery (
    tanker_delivery_id, dispatch_id,
    
    -- Loading (at Plant Sentul)
    loading_location_id, loading_datetime,
    loading_measurement_method,
    loading_flowmeter_reading_start,
    loading_flowmeter_reading_end,
    loading_volume_liters,
    loading_temperature_celsius,
    loading_photo_flowmeter,
    loading_measured_by,
    loading_verified_by,
    
    -- Unloading (at Customer)
    unloading_location_id, unloading_datetime,
    unloading_measurement_method,
    unloading_flowmeter_reading_start,
    unloading_flowmeter_reading_end,
    unloading_volume_liters,  -- ⭐ Actual delivered
    unloading_photo_flowmeter,
    unloading_received_by,
    
    -- Variance
    volume_variance_liters,
    variance_percent,
    variance_within_tolerance,
    variance_reason,
    
    -- Chargeable ⭐
    chargeable_volume_liters,
    chargeable_based_on,
    
    -- Pricing
    rate_per_liter,
    gross_revenue,  -- ⭐ 9,850 L × Rp 50
    net_revenue,
    
    created_by
) VALUES (
    'TNK001', 'DSP002',
    
    -- Loading
    'LOC003', '2025-11-18 14:30:00',
    'FLOWMETER',
    12345.50,  -- Meter start
    22345.50,  -- Meter end
    10000.00,  -- Loaded 10,000 liters
    28.5,  -- Temperature
    '/uploads/tanker/loading_tnk001.jpg',
    'Pak Budi (Plant Operator)',
    'SUPER01',
    
    -- Unloading
    'LOC999', '2025-11-18 16:30:00',
    'FLOWMETER',
    54321.00,
    64171.00,  -- 64171 - 54321 = 9,850 L
    9850.00,  -- ⭐ Delivered 9,850 liters (150L lost during transit)
    '/uploads/tanker/unloading_tnk001.jpg',
    'Ibu Siti (Customer)',
    
    -- Variance
    150.00,  -- 10,000 - 9,850 = 150 L lost
    1.50,    -- (150/10,000) × 100 = 1.5%
    1,       -- ✅ Within tolerance (< 2%)
    'Normal spillage during transit and hose residue',
    
    -- Chargeable
    9850.00,  -- ⭐ Charge based on DELIVERED volume
    'DELIVERED',
    
    -- Pricing
    50.00,  -- Rp 50 per liter
    492500.00,  -- ⭐ 9,850 L × Rp 50 = Rp 492,500
    492500.00,
    
    'DRIVER05'
);

-- Update dispatch with actual revenue
UPDATE tr_tms_dispatcher_main
SET 
    qty_actual_delivered = 9850.00,  -- Actual volume delivered
    gross_revenue = 492500.00,  -- ⭐ Based on delivered volume
    net_revenue = 492500.00,
    status = 'COMPLETED'
WHERE dispatch_id = 'DSP002';
```

---

### Pricing Calculation Function (Volume-based)

```sql
CREATE FUNCTION fn_CalculateTankerTariff (
    @client_id VARCHAR(20),
    @route_id VARCHAR(20),
    @truck_type_id VARCHAR(20),
    @volume_delivered_liters DECIMAL(10,2),
    @trip_date DATE
)
RETURNS DECIMAL(15,2)
AS
BEGIN
    DECLARE @rate_per_liter DECIMAL(10,4) = 0;
    DECLARE @base_revenue DECIMAL(15,2) = 0;
    DECLARE @final_revenue DECIMAL(15,2) = 0;
    DECLARE @volume_discount DECIMAL(5,2) = 0;
    DECLARE @trips_this_month INT;
    DECLARE @minimum_charge DECIMAL(15,2) = 0;
    
    -- Step 1: Get rate per liter from active contract
    SELECT TOP 1 
        @rate_per_liter = rate_per_liter,
        @minimum_charge = minimum_charge
    FROM ms_tariff_rate r
    INNER JOIN ms_tariff_contract c ON r.contract_id = c.contract_id
    WHERE c.client_id = @client_id
      AND r.route_id = @route_id
      AND r.truck_type_id = @truck_type_id
      AND r.pricing_model = 'VOLUME'
      AND r.is_active = 1
      AND c.is_active = 1
      AND @trip_date BETWEEN c.valid_from AND c.valid_to
    ORDER BY r.priority DESC;
    
    -- Step 2: Calculate base revenue
    SET @base_revenue = @volume_delivered_liters * @rate_per_liter;
    
    -- Step 3: Apply minimum charge
    IF @base_revenue < @minimum_charge
        SET @base_revenue = @minimum_charge;
    
    -- Step 4: Calculate volume discount (based on trip count)
    SELECT @trips_this_month = COUNT(*)
    FROM tr_tms_dispatcher_main d
    WHERE d.client_id = @client_id
      AND MONTH(d.scheduled_date) = MONTH(@trip_date)
      AND YEAR(d.scheduled_date) = YEAR(@trip_date)
      AND d.status = 'COMPLETED';
    
    SELECT @volume_discount = CASE
        WHEN @trips_this_month >= volume_discount_tier2_trips THEN volume_discount_tier2_percent
        WHEN @trips_this_month >= volume_discount_tier1_trips THEN volume_discount_tier1_percent
        ELSE 0
    END
    FROM ms_tariff_contract
    WHERE client_id = @client_id
      AND is_active = 1
      AND @trip_date BETWEEN valid_from AND valid_to;
    
    -- Step 5: Apply discount
    SET @final_revenue = @base_revenue * (1 - @volume_discount / 100);
    
    RETURN ROUND(@final_revenue, 0);
END;
GO

-- Usage:
SELECT dbo.fn_CalculateTankerTariff(
    'CUS001',      -- AQUA
    'ROU005',      -- Sentul → Customer
    'TRK006',      -- Tanker 10KL
    9850.00,       -- ⭐ Delivered 9,850 liters
    '2025-11-18'
) AS tanker_revenue;

-- Result: Rp 492,500 (9,850 L × Rp 50)
```

---

## COMPARISON: TRIP-BASED vs VOLUME-BASED PRICING

| Aspect | TRIP-BASED (Shuttle) | VOLUME-BASED (Tanker) ⭐ |
|--------|----------------------|---------------------------|
| **Pricing Model** | Fixed rate per trip | Rate per liter delivered |
| **Example Rate** | Rp 800,000 per trip | Rp 50 per liter |
| **Measurement** | Count trips | Measure volume (flowmeter) |
| **Charge Based On** | Trip completed | Volume delivered (actual) |
| **Variance Handling** | N/A | Spillage tolerance ±2% |
| **Min Charge** | N/A (always per trip) | Rp 250K minimum |
| **Documentation** | BTB (quantity in cartons) | Flowmeter reading + photo |
| **Revenue Calculation** | Fixed × surcharges/discounts | Volume × rate × adjustments |
| **Suitable For** | JUGRACK, PALLET (shuttle) | TANKER (water, fuel, chemical) |

---

## RECOMMENDATION: ✅ USE MANY-TO-MANY

### Implementation Priority:

**Phase 1 (MVP):**
1. ✅ `ms_tariff_contract` - Basic contract
2. ✅ `ms_tariff_rate` - Route-specific rates with **multiple pricing models** ⭐
   - TRIP-based (shuttle)
   - VOLUME-based (tanker) ⭐ NEW
   - DISTANCE-based (ad-hoc)
   - WEIGHT-based (general cargo)
   - TIME-based (rental)
3. ✅ Pricing calculation functions (per model)

**Phase 2 (Enhanced):**
4. ✅ `ms_tariff_special_condition` - Seasonal/promotional pricing
5. ✅ Volume discount auto-calculation
6. ✅ Contract approval workflow
7. ✅ `tr_tms_tanker_delivery` - Volume measurement tracking ⭐

**Phase 3 (Advanced):**
8. ✅ Fuel surcharge formula (dynamic)
9. ✅ Weather-based pricing (API integration)
10. ✅ AI-based pricing optimization
11. ✅ Flowmeter integration (IoT) for real-time volume tracking

---

## MIGRATION PLAN

```bash
# Phase 1: Create tariff tables
php artisan migrate --path=database/migrations/2025_11_18_012_create_ms_tariff_contract_table.php
php artisan migrate --path=database/migrations/2025_11_18_013_create_ms_tariff_rate_table.php
php artisan migrate --path=database/migrations/2025_11_18_014_create_ms_tariff_special_condition_table.php

# Phase 2: Seed AQUA contracts (shuttle + tanker)
php artisan db:seed --class=AquaTariffContractSeeder

# Phase 3: Create tanker delivery table
php artisan migrate --path=database/migrations/2025_11_18_015_create_tr_tms_tanker_delivery_table.php

# Phase 4: Test pricing calculation
php artisan tinker
>>> # Test shuttle pricing
>>> DB::select('SELECT dbo.fn_CalculateTripTariff(?, ?, ?, ?, ?, ?)', [...])
>>> # Test tanker pricing ⭐
>>> DB::select('SELECT dbo.fn_CalculateTankerTariff(?, ?, ?, ?, ?)', [...])
```

---

## CONCLUSION

### ✅ **HIGHLY RECOMMENDED**

**Many-to-Many Tariff structure is the BEST PRACTICE for TMS pricing because:**

1. **Matches Business Reality**: Different customers DO have different rates
2. **Future-Proof**: Easy to add new pricing dimensions
3. **Contract Management**: Professional contract lifecycle
4. **Competitive Advantage**: Dynamic pricing = more profitable
5. **Industry Standard**: All major TMS systems use this pattern

**Trade-off**: Complexity vs Flexibility
- **Accept**: Complexity in database design
- **Gain**: Maximum flexibility in pricing strategy

### Next Steps:
1. Create migration files for 3 tariff tables
2. Create seeder for AQUA contract example
3. Create SQL function for pricing calculation
4. Build UI for contract management
5. Test with real AQUA data

---

**Document prepared by**: TMS Development Team  
**Date**: 18 November 2025  
**Decision**: ✅ APPROVED - Implement Many-to-Many Tariff Structure  
**Status**: Ready for Migration Development 🚀
