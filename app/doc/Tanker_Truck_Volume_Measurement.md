# TMS - TANKER TRUCK TYPE & VOLUME MEASUREMENT

**Created:** 18 November 2025  
**Purpose:** Master data untuk tanker truck dengan volume capacity dan measurement specification  
**Status:** Reference Data

---

## TANKER TRUCK TYPES FOR AQUA

### 1. Water Tanker 10KL (10,000 Liters)

```sql
INSERT INTO ms_truck_type (
    truck_type_id,
    truck_type_code,
    truck_type_name,
    category,
    
    -- Capacity (Volume-based) ⭐
    capacity_unit,
    capacity_min,
    capacity_max,
    capacity_optimal,
    
    -- Physical Specs
    length_meters,
    width_meters,
    height_meters,
    
    -- Tank Specs
    tank_material,
    tank_compartments,
    has_internal_coating,
    
    -- Measurement
    has_flowmeter,
    flowmeter_brand,
    flowmeter_accuracy_percent,
    has_tank_dip_stick,
    calibration_certificate_required,
    last_calibration_date,
    next_calibration_due,
    
    -- Cargo Type
    cargo_type,
    food_grade,
    chemical_grade,
    hazmat_certified,
    
    -- Compliance
    health_permit_number,
    health_permit_expiry,
    
    -- Operational
    fuel_consumption_per_km,
    avg_speed_kmh,
    
    -- Status
    is_active,
    created_by
) VALUES (
    'TRK006',
    'TANKER-10KL',
    'Water Tanker 10,000 Liters',
    'TANKER',
    
    -- Capacity
    'LITER',
    5000.00,   -- Min 5KL (half tank minimum)
    10000.00,  -- Max 10KL (full capacity)
    9500.00,   -- Optimal 9.5KL (allow for expansion)
    
    -- Dimensions
    6.50,  -- Length
    2.20,  -- Width
    2.80,  -- Height
    
    -- Tank
    'STAINLESS_STEEL_304',
    1,  -- Single compartment
    1,  -- Food grade coating
    
    -- Measurement
    1,  -- Has flowmeter
    'Tokico FRP',
    0.5,  -- ±0.5% accuracy
    1,  -- Has dip stick backup
    1,  -- Calibration required
    '2025-06-15',
    '2026-06-15',
    
    -- Cargo
    'WATER',
    1,  -- Food grade certified
    0,  -- Not for chemicals
    0,  -- Not hazmat
    
    -- Health Permit
    'DINKES-BKS-2025-12345',
    '2026-12-31',
    
    -- Operational
    0.18,  -- 5.5 km/liter
    50,    -- Avg speed
    
    1,
    'ADMIN01'
);
```

---

### 2. Water Tanker 15KL (15,000 Liters)

```sql
INSERT INTO ms_truck_type (
    truck_type_id,
    truck_type_code,
    truck_type_name,
    category,
    capacity_unit,
    capacity_min,
    capacity_max,
    capacity_optimal,
    
    length_meters,
    width_meters,
    height_meters,
    
    tank_material,
    tank_compartments,
    has_internal_coating,
    
    has_flowmeter,
    flowmeter_brand,
    flowmeter_accuracy_percent,
    
    cargo_type,
    food_grade,
    
    health_permit_number,
    health_permit_expiry,
    
    fuel_consumption_per_km,
    is_active,
    created_by
) VALUES (
    'TRK007',
    'TANKER-15KL',
    'Water Tanker 15,000 Liters',
    'TANKER',
    'LITER',
    8000.00,
    15000.00,
    14500.00,
    
    7.50,
    2.40,
    3.00,
    
    'STAINLESS_STEEL_304',
    1,
    1,
    
    1,
    'Tokico FRP',
    0.5,
    
    'WATER',
    1,
    
    'DINKES-BKS-2025-12346',
    '2026-12-31',
    
    0.20,  -- 5 km/liter
    1,
    'ADMIN01'
);
```

---

### 3. Chemical Tanker 12KL (for industrial water/chemicals)

```sql
INSERT INTO ms_truck_type (
    truck_type_id,
    truck_type_code,
    truck_type_name,
    category,
    capacity_unit,
    capacity_min,
    capacity_max,
    capacity_optimal,
    
    tank_material,
    tank_compartments,
    has_internal_coating,
    
    has_flowmeter,
    flowmeter_brand,
    
    cargo_type,
    food_grade,
    chemical_grade,
    hazmat_certified,
    
    requires_special_permit,
    
    is_active,
    created_by
) VALUES (
    'TRK008',
    'TANKER-CHEM-12KL',
    'Chemical Tanker 12,000 Liters',
    'TANKER',
    'LITER',
    6000.00,
    12000.00,
    11500.00,
    
    'STAINLESS_STEEL_316',  -- Higher grade for chemicals
    2,  -- 2 compartments
    1,
    
    1,
    'Tokico FRP',
    
    'CHEMICAL',
    0,  -- NOT food grade
    1,  -- Chemical certified
    1,  -- Hazmat certified
    
    1,  -- Special permit required
    
    1,
    'ADMIN01'
);
```

---

## VOLUME MEASUREMENT SPECIFICATIONS

### Flowmeter Calibration Requirements

```sql
CREATE TABLE ms_flowmeter_calibration (
    calibration_id VARCHAR(20) PRIMARY KEY,
    vehicle_id VARCHAR(20) NOT NULL,
    flowmeter_serial_number VARCHAR(50),
    
    -- Calibration Details
    calibration_date DATE,
    calibration_due_date DATE,
    calibrated_by VARCHAR(100),  -- Lab/company name
    calibration_certificate_number VARCHAR(50),
    
    -- Test Results
    test_volume_1_liters DECIMAL(10,2),  -- Test 1: 1000L
    test_reading_1_liters DECIMAL(10,2),
    test_accuracy_1_percent DECIMAL(5,2),
    
    test_volume_2_liters DECIMAL(10,2),  -- Test 2: 5000L
    test_reading_2_liters DECIMAL(10,2),
    test_accuracy_2_percent DECIMAL(5,2),
    
    test_volume_3_liters DECIMAL(10,2),  -- Test 3: 10000L
    test_reading_3_liters DECIMAL(10,2),
    test_accuracy_3_percent DECIMAL(5,2),
    
    -- Overall Accuracy
    overall_accuracy_percent DECIMAL(5,2),
    passed_calibration BIT DEFAULT 1,
    
    -- Correction Factor
    correction_factor DECIMAL(8,6),  -- e.g., 1.003 = +0.3% correction
    
    -- Certificate
    certificate_file_path VARCHAR(500),
    
    -- Status
    status VARCHAR(20) DEFAULT 'ACTIVE',
    -- ACTIVE, EXPIRED, FAILED
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(20),
    
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(vehicle_id)
);

-- Example: Flowmeter calibration record
INSERT INTO ms_flowmeter_calibration (
    calibration_id,
    vehicle_id,
    flowmeter_serial_number,
    calibration_date,
    calibration_due_date,
    calibrated_by,
    calibration_certificate_number,
    
    test_volume_1_liters,
    test_reading_1_liters,
    test_accuracy_1_percent,
    
    test_volume_2_liters,
    test_reading_2_liters,
    test_accuracy_2_percent,
    
    test_volume_3_liters,
    test_reading_3_liters,
    test_accuracy_3_percent,
    
    overall_accuracy_percent,
    passed_calibration,
    correction_factor,
    
    certificate_file_path,
    status,
    created_by
) VALUES (
    'CAL001',
    'VEH006',
    'TOKICO-FRP-2025-12345',
    '2025-06-15',
    '2026-06-15',
    'PT. Kalibrasi Indonesia',
    'CERT-KAL-2025-06-12345',
    
    1000.00,   -- Test 1: Standard 1000L
    1003.50,   -- Reading 1003.5L
    0.35,      -- +0.35% error
    
    5000.00,   -- Test 2: Standard 5000L
    5012.00,   -- Reading 5012L
    0.24,      -- +0.24% error
    
    10000.00,  -- Test 3: Standard 10000L
    10028.00,  -- Reading 10028L
    0.28,      -- +0.28% error
    
    0.29,      -- Overall ±0.29% (PASS)
    1,         -- Passed
    0.997100,  -- Correction factor (divide by this)
    
    '/certificates/flowmeter/cal001.pdf',
    'ACTIVE',
    'ADMIN01'
);
```

---

## TANKER DELIVERY PROCESS WITH VOLUME MEASUREMENT

### Standard Operating Procedure:

```
┌─────────────────────────────────────────────────────────────────┐
│               TANKER WATER DELIVERY PROCESS                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. PRE-LOADING (at Plant)                                      │
│     ├─ Check tank is empty and clean                           │
│     ├─ Record flowmeter starting reading                       │
│     └─ Photo: Empty tank, flowmeter display                    │
│                                                                 │
│  2. LOADING (at Source - Plant Sentul)                          │
│     ├─ Connect hose to tank inlet                              │
│     ├─ Start pump → flowmeter counting                         │
│     ├─ Monitor real-time volume                                │
│     ├─ Stop at target volume (e.g., 10,000L)                   │
│     ├─ Record flowmeter ending reading                         │
│     ├─ Calculate loaded: End - Start = 10,000L                 │
│     ├─ Record temperature (for density correction)             │
│     ├─ Plant supervisor verify & sign                          │
│     └─ Photo: Flowmeter reading, loading ticket                │
│                                                                 │
│  3. TRANSIT                                                     │
│     ├─ Seal tank outlet                                        │
│     ├─ Lock seal with numbered seal                            │
│     ├─ Record seal number                                      │
│     ├─ GPS tracking during transit                             │
│     └─ Expected spillage: ~50-150L (1-1.5%)                    │
│                                                                 │
│  4. PRE-UNLOADING (at Destination)                              │
│     ├─ Verify seal intact                                      │
│     ├─ Photo: Seal condition                                   │
│     ├─ Break seal                                              │
│     └─ Record flowmeter reading before unload                  │
│                                                                 │
│  5. UNLOADING (at Customer Site)                                │
│     ├─ Connect hose to customer's tank                         │
│     ├─ Record customer's flowmeter start (if available)        │
│     ├─ Start unloading → flowmeter counting                    │
│     ├─ Monitor volume                                          │
│     ├─ Complete unloading                                      │
│     ├─ Record truck flowmeter ending reading                   │
│     ├─ Record customer flowmeter ending reading                │
│     ├─ Calculate delivered: End - Start = 9,850L               │
│     ├─ Calculate variance: 10,000 - 9,850 = 150L (1.5%)        │
│     ├─ Check variance within tolerance (±2%) ✅                │
│     ├─ Customer receive & sign delivery note                   │
│     ├─ Photo: Both flowmeters, delivery note, signature        │
│     └─ Upload to system                                        │
│                                                                 │
│  6. POST-DELIVERY                                               │
│     ├─ Calculate chargeable volume = 9,850L (delivered)        │
│     ├─ Calculate revenue = 9,850L × Rp 50 = Rp 492,500         │
│     ├─ Generate invoice based on delivered volume              │
│     ├─ Archive flowmeter photos & delivery note                │
│     └─ Update system with actual volume                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## VOLUME VARIANCE TOLERANCE

### Acceptable Variance Limits:

| Variance % | Status | Action |
|------------|--------|--------|
| 0% - 1% | ✅ EXCELLENT | No action, normal spillage |
| 1% - 2% | ✅ ACCEPTABLE | Within tolerance, acceptable |
| 2% - 3% | ⚠️ WARNING | Investigate cause, may require explanation |
| 3% - 5% | ❌ HIGH VARIANCE | Investigation required, possible leak |
| > 5% | 🚨 CRITICAL | Stop operation, inspect tank, report incident |

### Common Causes of Variance:

1. **Normal Spillage (0.5-1.5%)**
   - Hose residue (~20-50L)
   - Evaporation during transit
   - Tank expansion/contraction
   - Foam/air bubbles

2. **Acceptable Loss (1.5-2%)**
   - Long distance transit
   - Hot weather (evaporation)
   - Multiple stops
   - Rough road conditions

3. **Excessive Loss (>2%)**
   - Leak in tank or valve
   - Measurement error
   - Theft/unauthorized unloading
   - Flowmeter malfunction

---

## PRICING CALCULATION WITH VOLUME CORRECTION

### Temperature Correction Formula:

```sql
-- Function to calculate volume at standard temperature (20°C)
CREATE FUNCTION fn_CorrectVolumeForTemperature (
    @volume_liters DECIMAL(10,2),
    @temperature_celsius DECIMAL(5,2)
)
RETURNS DECIMAL(10,2)
AS
BEGIN
    DECLARE @volume_at_20C DECIMAL(10,2);
    DECLARE @expansion_coefficient DECIMAL(10,8) = 0.000214;  -- Water expansion per °C
    DECLARE @temp_diff DECIMAL(5,2) = @temperature_celsius - 20.0;
    DECLARE @correction_factor DECIMAL(10,8) = 1.0 - (@expansion_coefficient * @temp_diff);
    
    SET @volume_at_20C = @volume_liters * @correction_factor;
    
    RETURN ROUND(@volume_at_20C, 2);
END;
GO

-- Example usage:
-- Volume at 28°C = 10,000 liters
-- Volume at 20°C = ?
SELECT dbo.fn_CorrectVolumeForTemperature(10000.00, 28.0) AS corrected_volume;
-- Result: 9,982.88 liters (corrected to standard temp)
```

---

## INTEGRATION WITH DISPATCH SYSTEM

### Tanker-specific Fields in tr_tms_dispatcher_main:

```sql
-- Add tanker-specific columns
ALTER TABLE tr_tms_dispatcher_main
ADD 
    -- Volume Planning
    planned_volume_liters DECIMAL(10,2),
    
    -- Loaded Volume (from plant flowmeter)
    loaded_volume_liters DECIMAL(10,2),
    loaded_temperature_celsius DECIMAL(5,2),
    loaded_flowmeter_reading_start DECIMAL(10,2),
    loaded_flowmeter_reading_end DECIMAL(10,2),
    loaded_photo_path VARCHAR(500),
    loaded_verified_by VARCHAR(20),
    
    -- Delivered Volume (from customer flowmeter)
    delivered_volume_liters DECIMAL(10,2),
    delivered_temperature_celsius DECIMAL(5,2),
    delivered_flowmeter_reading_start DECIMAL(10,2),
    delivered_flowmeter_reading_end DECIMAL(10,2),
    delivered_photo_path VARCHAR(500),
    delivered_received_by VARCHAR(100),
    
    -- Variance
    volume_variance_liters DECIMAL(10,2),
    volume_variance_percent DECIMAL(5,2),
    variance_within_tolerance BIT DEFAULT 1,
    variance_explanation NVARCHAR(500),
    
    -- Seal
    seal_number VARCHAR(50),
    seal_intact BIT DEFAULT 1,
    
    -- Chargeable Volume
    chargeable_volume_liters DECIMAL(10,2),
    
    -- Customer Flowmeter (cross-check)
    customer_flowmeter_reading DECIMAL(10,2),
    customer_flowmeter_match BIT DEFAULT 1;
```

---

## REPORTING: TANKER PERFORMANCE

### Daily Tanker Delivery Report:

```sql
SELECT 
    d.dispatch_number,
    d.scheduled_date,
    v.vehicle_plate_number,
    dr.driver_name,
    c.client_name,
    r.route_name,
    
    -- Volume
    d.planned_volume_liters,
    d.loaded_volume_liters,
    d.delivered_volume_liters,
    d.volume_variance_liters,
    d.volume_variance_percent,
    
    CASE 
        WHEN d.volume_variance_percent <= 1.0 THEN 'EXCELLENT'
        WHEN d.volume_variance_percent <= 2.0 THEN 'ACCEPTABLE'
        WHEN d.volume_variance_percent <= 3.0 THEN 'WARNING'
        ELSE 'CRITICAL'
    END AS variance_status,
    
    -- Revenue
    d.chargeable_volume_liters,
    d.base_rate AS rate_per_liter,
    d.net_revenue,
    
    -- Status
    d.status
    
FROM tr_tms_dispatcher_main d
INNER JOIN ms_vehicle v ON d.vehicle_id = v.vehicle_id
INNER JOIN ms_driver dr ON d.driver_id = dr.driver_id
INNER JOIN ms_client c ON d.client_id = c.client_id
INNER JOIN ms_route r ON d.route_id = r.route_id
WHERE d.dispatch_type = 'TANKER'
  AND d.scheduled_date >= '2025-11-18'
  AND d.scheduled_date < '2025-11-25'
ORDER BY d.scheduled_date, d.dispatch_number;
```

---

## ADVANTAGES: VOLUME-BASED PRICING

### ✅ Benefits:

1. **Fair Pricing** - Customer pays for actual volume received
2. **Transparent** - Flowmeter readings verified by both parties
3. **Traceable** - Photo evidence of all measurements
4. **Variance Control** - Automatic tolerance checking
5. **Spillage Accountability** - Track normal vs excessive loss
6. **Quality Assurance** - Temperature correction for accuracy

### ⚠️ Challenges:

1. **Equipment Required** - Calibrated flowmeters needed
2. **Calibration Cost** - Annual calibration required
3. **Dispute Potential** - Different flowmeter readings
4. **Documentation** - More photos/paperwork required
5. **Training** - Drivers must understand measurement

---

**Document Status**: Ready for Implementation  
**Next Phase**: Create migration files for tanker-specific tables  
**Integration**: Volume measurement → Pricing → Invoicing 🚀
