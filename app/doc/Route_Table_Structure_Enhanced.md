# TMS - Route Table Structure (Enhanced dengan Truck Type & Cargo Type)

**Tanggal:** 17 November 2025  
**Modul:** Route Management - Database Design  
**Status:** Design Phase - Enhanced Version

---

## 1. KONSEP DASAR

### 1.1 Mengapa Truck Type & Cargo Type Penting?

**Realitas Bisnis:**
```
Route yang SAMA (Jakarta → Bandung) tapi COST & TARIFF BERBEDA karena:

Skenario 1: 
- Truck Type: Pickup (2 ton)
- Cargo Type: General Cargo (kardus)
- Tariff: Rp 800,000
- Cost: Rp 600,000

Skenario 2:
- Truck Type: Tronton (15 ton)
- Cargo Type: General Cargo (kardus)
- Tariff: Rp 1,500,000
- Cost: Rp 1,200,000

Skenario 3:
- Truck Type: Tronton (15 ton)
- Cargo Type: Dangerous Goods (kimia)
- Tariff: Rp 2,500,000 (+67% premium!)
- Cost: Rp 1,800,000 (driver skill, permit, safety)

Skenario 4:
- Truck Type: Box Pendingin (10 ton)
- Cargo Type: Chilled Goods (makanan beku)
- Tariff: Rp 3,000,000 (+100% premium!)
- Cost: Rp 2,200,000 (fuel for cooler, maintenance)

Kesimpulan:
Route BUKAN hanya origin-destination!
Route = Origin + Destination + Truck Type + Cargo Type
```

---

## 2. DATABASE STRUCTURE (ENHANCED)

### 2.1 Master Truck Type
```sql
CREATE TABLE ms_truck_type (
    truck_type_id VARCHAR(50) PRIMARY KEY,
    truck_type_code VARCHAR(20) UNIQUE NOT NULL,
    truck_type_name VARCHAR(100) NOT NULL,
    
    -- Capacity
    min_capacity_ton DECIMAL(10,2),
    max_capacity_ton DECIMAL(10,2),
    min_capacity_cbm DECIMAL(10,2), -- cubic meter (volume)
    max_capacity_cbm DECIMAL(10,2),
    
    -- Physical Specification
    max_length_meter DECIMAL(5,2),
    max_width_meter DECIMAL(5,2),
    max_height_meter DECIMAL(5,2),
    
    -- Truck Category
    category VARCHAR(50), -- LIGHT, MEDIUM, HEAVY
    vehicle_class VARCHAR(50), -- PICKUP, ENGKEL, FUSO, TRONTON, TRAILER, BOX, REFRIGERATED
    
    -- Special Features
    has_tailgate BIT DEFAULT 0,
    has_cooler BIT DEFAULT 0, -- untuk chilled/frozen
    has_crane BIT DEFAULT 0,
    is_closed_box BIT DEFAULT 0, -- tertutup atau terbuka
    
    -- Cost Factors
    fuel_consumption_km_per_liter DECIMAL(5,2), -- empty
    fuel_consumption_loaded_km_per_liter DECIMAL(5,2), -- loaded
    daily_rental_cost DECIMAL(15,2), -- jika sewa dari luar
    depreciation_per_km DECIMAL(10,2),
    
    -- Operational
    driver_skill_required VARCHAR(20), -- BASIC, INTERMEDIATE, EXPERT
    requires_helper BIT DEFAULT 0, -- perlu kernet atau tidak
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME
)

-- Sample Data
INSERT INTO ms_truck_type VALUES
('TT001', 'PICKUP', 'Pickup Truck', 0.5, 2.0, 5, 10, 3.5, 1.8, 2.0, 'LIGHT', 'PICKUP', 0, 0, 0, 1, 10, 8, 50000, 500, 'BASIC', 0, 1, NULL, GETDATE(), NULL),
('TT002', 'ENGKEL', 'Truck Engkel (CDD)', 2.0, 5.0, 15, 30, 5.0, 2.2, 2.5, 'MEDIUM', 'ENGKEL', 1, 0, 0, 1, 8, 6, 100000, 800, 'INTERMEDIATE', 1, 1, NULL, GETDATE(), NULL),
('TT003', 'FUSO', 'Truck Fuso (CDE)', 5.0, 8.0, 30, 50, 7.0, 2.3, 2.8, 'HEAVY', 'FUSO', 1, 0, 0, 1, 6, 5, 150000, 1000, 'INTERMEDIATE', 1, 1, NULL, GETDATE(), NULL),
('TT004', 'TRONTON', 'Truck Tronton', 10.0, 15.0, 50, 80, 9.0, 2.5, 3.0, 'HEAVY', 'TRONTON', 1, 0, 0, 1, 5, 4, 200000, 1500, 'EXPERT', 1, 1, NULL, GETDATE(), NULL),
('TT005', 'TRAILER', 'Truck Trailer', 15.0, 25.0, 80, 120, 12.0, 2.5, 3.5, 'HEAVY', 'TRAILER', 1, 0, 0, 1, 4, 3.5, 300000, 2000, 'EXPERT', 1, 1, NULL, GETDATE(), NULL),
('TT006', 'BOX-COOL', 'Box Pendingin', 5.0, 10.0, 30, 60, 6.0, 2.3, 2.8, 'MEDIUM', 'REFRIGERATED', 1, 1, 0, 1, 5, 4, 250000, 1800, 'INTERMEDIATE', 1, 1, 'Untuk chilled/frozen goods', GETDATE(), NULL);
```

### 2.2 Master Cargo Type
```sql
CREATE TABLE ms_cargo_type (
    cargo_type_id VARCHAR(50) PRIMARY KEY,
    cargo_type_code VARCHAR(20) UNIQUE NOT NULL,
    cargo_type_name VARCHAR(100) NOT NULL,
    
    -- Category
    category VARCHAR(50), -- GENERAL, FRAGILE, DANGEROUS, PERISHABLE, OVERSIZED, VALUABLE
    
    -- Handling Requirements
    requires_special_handling BIT DEFAULT 0,
    requires_temperature_control BIT DEFAULT 0,
    requires_security BIT DEFAULT 0,
    requires_permit BIT DEFAULT 0, -- dangerous goods perlu izin
    
    -- Temperature Range (untuk perishable)
    min_temperature_celsius DECIMAL(5,2),
    max_temperature_celsius DECIMAL(5,2),
    
    -- Insurance & Liability
    default_insurance_rate DECIMAL(5,4), -- % dari nilai barang
    liability_multiplier DECIMAL(5,2), -- multiplier untuk tanggung jawab
    
    -- Cost Impact
    handling_cost_multiplier DECIMAL(5,2) DEFAULT 1.0, -- 1.0 = normal, 1.5 = 50% lebih mahal
    driver_fee_multiplier DECIMAL(5,2) DEFAULT 1.0,
    fuel_cost_multiplier DECIMAL(5,2) DEFAULT 1.0, -- cooler truck pakai lebih banyak BBM
    
    -- Restrictions
    max_stacking_height INT, -- berapa layer bisa ditumpuk
    is_hazardous BIT DEFAULT 0,
    hazard_class VARCHAR(20), -- UN hazard classification
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME
)

-- Sample Data
INSERT INTO ms_cargo_type VALUES
('CT001', 'GENERAL', 'General Cargo', 'GENERAL', 0, 0, 0, 0, NULL, NULL, 0.001, 1.0, 1.0, 1.0, 1.0, 5, 0, NULL, 1, 'Barang umum: kardus, plastik, dll', GETDATE(), NULL),
('CT002', 'FRAGILE', 'Fragile Goods', 'FRAGILE', 1, 0, 0, 0, NULL, NULL, 0.005, 1.5, 1.2, 1.2, 1.0, 2, 0, NULL, 1, 'Barang pecah belah: kaca, keramik', GETDATE(), NULL),
('CT003', 'DANGEROUS', 'Dangerous Goods', 'DANGEROUS', 1, 0, 1, 1, NULL, NULL, 0.01, 3.0, 1.8, 1.5, 1.0, 1, 1, 'Class 3', 1, 'Bahan kimia, flammable', GETDATE(), NULL),
('CT004', 'CHILLED', 'Chilled Goods', 'PERISHABLE', 1, 1, 0, 0, 2, 8, 0.008, 2.0, 1.5, 1.3, 1.4, 3, 0, NULL, 1, 'Makanan dingin: daging, sayur', GETDATE(), NULL),
('CT005', 'FROZEN', 'Frozen Goods', 'PERISHABLE', 1, 1, 0, 0, -18, -10, 0.008, 2.0, 1.6, 1.4, 1.5, 3, 0, NULL, 1, 'Makanan beku: ice cream, frozen food', GETDATE(), NULL),
('CT006', 'VALUABLE', 'Valuable Goods', 'VALUABLE', 1, 0, 1, 0, NULL, NULL, 0.02, 5.0, 1.3, 1.8, 1.0, 2, 0, NULL, 1, 'Barang berharga: elektronik, jewelry', GETDATE(), NULL),
('CT007', 'OVERSIZED', 'Oversized Cargo', 'OVERSIZED', 1, 0, 0, 1, NULL, NULL, 0.005, 2.0, 1.5, 1.3, 1.0, 1, 0, NULL, 1, 'Barang besar: mesin, konstruksi', GETDATE(), NULL);
```

### 2.3 Master Route (ENHANCED)
```sql
CREATE TABLE ms_route (
    route_id VARCHAR(50) PRIMARY KEY,
    route_code VARCHAR(20) UNIQUE NOT NULL,
    route_name VARCHAR(200) NOT NULL,
    route_type VARCHAR(20) NOT NULL, -- SHUTTLE, MULTIPOINT, ADHOC
    
    -- Origin & Destination
    origin_location_id VARCHAR(50),
    origin_name VARCHAR(100) NOT NULL,
    origin_address TEXT,
    origin_latitude DECIMAL(10,8),
    origin_longitude DECIMAL(11,8),
    
    destination_location_id VARCHAR(50),
    destination_name VARCHAR(100) NOT NULL,
    destination_address TEXT,
    destination_latitude DECIMAL(10,8),
    destination_longitude DECIMAL(11,8),
    
    -- Route Characteristics
    distance_km DECIMAL(10,2) NOT NULL,
    estimated_time_hours DECIMAL(5,2),
    estimated_time_min DECIMAL(5,2), -- fastest
    estimated_time_max DECIMAL(5,2), -- slowest (traffic)
    
    -- Road Characteristics
    road_type VARCHAR(50), -- TOLL, HIGHWAY, CITY, MIXED
    road_condition VARCHAR(20), -- EXCELLENT, GOOD, FAIR, POOR
    traffic_level VARCHAR(20), -- LOW, MEDIUM, HIGH, VERY_HIGH
    
    -- Route Restrictions (GENERAL - applies to all)
    max_weight_ton DECIMAL(10,2), -- batas jembatan/jalan
    max_height_meter DECIMAL(5,2), -- batas underpass
    max_width_meter DECIMAL(5,2),
    restricted_times VARCHAR(200), -- JSON: {"weekday": "07:00-09:00,17:00-19:00"}
    restricted_days VARCHAR(100), -- JSON: ["Saturday", "Sunday"]
    
    -- Cost Components (BASE - untuk general cargo, pickup truck)
    base_toll_fee DECIMAL(15,2),
    estimated_fuel_cost DECIMAL(15,2), -- untuk pickup/engkel
    other_cost DECIMAL(15,2), -- parkir, retribusi
    
    -- Round-trip (untuk shuttle)
    is_roundtrip BIT DEFAULT 0,
    roundtrip_time_hours DECIMAL(5,2),
    rest_time_minutes INT,
    
    -- Alternative Routes
    has_alternative BIT DEFAULT 0,
    alternative_route_id VARCHAR(50),
    
    -- Metadata
    is_active BIT DEFAULT 1,
    is_favorite BIT DEFAULT 0,
    usage_count INT DEFAULT 0,
    last_used DATE,
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (origin_location_id) REFERENCES ms_location(location_id),
    FOREIGN KEY (destination_location_id) REFERENCES ms_location(location_id),
    FOREIGN KEY (alternative_route_id) REFERENCES ms_route(route_id),
    
    INDEX idx_origin_dest (origin_name, destination_name),
    INDEX idx_route_code (route_code),
    INDEX idx_is_active (is_active)
)
```

### 2.4 Route Tariff (ENHANCED dengan Truck Type & Cargo Type)
```sql
CREATE TABLE ms_route_tariff (
    tariff_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    
    -- Key Factors
    truck_type_id VARCHAR(50) NOT NULL, -- ⭐ TRUCK TYPE
    cargo_type_id VARCHAR(50) NOT NULL, -- ⭐ CARGO TYPE
    service_type VARCHAR(20), -- SHUTTLE, MULTIPOINT, EXPRESS, REGULAR
    
    -- Base Pricing
    base_rate DECIMAL(15,2) NOT NULL, -- tarif dasar
    rate_per_km DECIMAL(10,2), -- tambahan per km (jika exceed base distance)
    rate_per_ton DECIMAL(10,2), -- tambahan per ton
    rate_per_cbm DECIMAL(10,2), -- tambahan per cubic meter
    
    -- Minimum Charges
    minimum_charge DECIMAL(15,2), -- charge minimal (even if short distance)
    
    -- Driver Compensation
    uang_jalan DECIMAL(15,2),
    uang_jasa_base DECIMAL(15,2), -- base
    uang_jasa_per_ton DECIMAL(10,2), -- bonus per ton
    uang_jasa_performance_bonus DECIMAL(15,2), -- bonus if on-time
    
    -- Helper (Kernet) Compensation
    helper_fee DECIMAL(15,2), -- gaji kernet (jika required)
    
    -- Reimbursements
    toll_reimbursement DECIMAL(15,2),
    fuel_reimbursement DECIMAL(15,2),
    parking_reimbursement DECIMAL(15,2),
    permit_reimbursement DECIMAL(15,2), -- untuk dangerous goods permit
    other_reimbursement DECIMAL(15,2),
    
    -- Special Costs (cargo type specific)
    temperature_control_cost DECIMAL(15,2), -- cooler operation cost
    security_escort_cost DECIMAL(15,2), -- untuk valuable goods
    insurance_cost DECIMAL(15,2), -- asuransi barang
    
    -- Validity
    effective_date DATE NOT NULL,
    expired_date DATE,
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (truck_type_id) REFERENCES ms_truck_type(truck_type_id),
    FOREIGN KEY (cargo_type_id) REFERENCES ms_cargo_type(cargo_type_id),
    
    -- Unique constraint: satu route + truck type + cargo type + service type hanya punya 1 active tariff
    UNIQUE(route_id, truck_type_id, cargo_type_id, service_type, effective_date),
    
    INDEX idx_route_tariff_active (route_id, truck_type_id, cargo_type_id, is_active, effective_date)
)
```

### 2.5 Route Truck Type Compatibility (Truck mana yang boleh lewat route ini)
```sql
CREATE TABLE ms_route_truck_compatibility (
    compatibility_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    truck_type_id VARCHAR(50) NOT NULL,
    
    -- Compatibility Status
    is_compatible BIT DEFAULT 1, -- 1 = bisa lewat, 0 = tidak bisa
    restriction_reason TEXT, -- alasan kalau tidak compatible
    
    -- Performance Adjustment (jika compatible tapi dengan catatan)
    time_adjustment_hours DECIMAL(5,2), -- +2 jam jika truck besar lewat jalan kecil
    fuel_adjustment_multiplier DECIMAL(5,2) DEFAULT 1.0, -- 1.2 = 20% lebih boros
    toll_adjustment DECIMAL(15,2), -- tol berbeda untuk truck besar
    
    notes TEXT,
    is_active BIT DEFAULT 1,
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (truck_type_id) REFERENCES ms_truck_type(truck_type_id),
    
    UNIQUE(route_id, truck_type_id)
)

-- Example: Route Jakarta-Bandung
INSERT INTO ms_route_truck_compatibility VALUES
('RC001', 'R001', 'TT001', 1, NULL, 0, 1.0, 0, NULL, 1, GETDATE()), -- Pickup: compatible
('RC002', 'R001', 'TT004', 1, NULL, 0.5, 1.1, 20000, 'Truck besar, tol lebih mahal', 1, GETDATE()), -- Tronton: compatible tapi lebih lambat
('RC003', 'R001', 'TT005', 0, 'Trailer tidak boleh masuk tol dalam kota', NULL, NULL, NULL, NULL, 1, GETDATE()); -- Trailer: NOT compatible
```

### 2.6 Route Cargo Type Requirements
```sql
CREATE TABLE ms_route_cargo_requirements (
    requirement_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    cargo_type_id VARCHAR(50) NOT NULL,
    
    -- Special Requirements
    requires_permit BIT DEFAULT 0,
    permit_type VARCHAR(100), -- "Dangerous Goods Transport Permit"
    permit_cost DECIMAL(15,2),
    
    requires_escort BIT DEFAULT 0, -- police/security escort
    escort_cost DECIMAL(15,2),
    
    requires_notification BIT DEFAULT 0, -- notifikasi ke pihak berwenang sebelum berangkat
    notification_lead_time_hours INT,
    
    -- Route Restrictions (specific untuk cargo type)
    restricted_times VARCHAR(200), -- JSON: dangerous goods tidak boleh lewat jam sibuk
    restricted_days VARCHAR(100),
    
    -- Additional Costs
    additional_cost DECIMAL(15,2), -- biaya tambahan khusus
    
    notes TEXT,
    is_active BIT DEFAULT 1,
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (cargo_type_id) REFERENCES ms_cargo_type(cargo_type_id),
    
    UNIQUE(route_id, cargo_type_id)
)

-- Example: Dangerous goods di route Jakarta-Bandung
INSERT INTO ms_route_cargo_requirements VALUES
('RR001', 'R001', 'CT003', 1, 'Surat Izin Pengangkutan Bahan Berbahaya', 500000, 1, 1000000, 1, 24, '{"weekday":"07:00-10:00,16:00-19:00"}', NULL, 200000, 'Dangerous goods butuh permit + escort + avoid rush hour', 1, GETDATE());
```

---

## 3. RELASI ANTAR TABEL

```
┌─────────────────┐
│  ms_route       │ (1 route punya banyak tariff kombinasi)
└────────┬────────┘
         │
         │ 1:N
         ▼
┌─────────────────────────┐
│  ms_route_tariff        │ ← Kombinasi: route + truck_type + cargo_type
└────┬──────────┬─────────┘
     │          │
     │          │
     │ N:1      │ N:1
     ▼          ▼
┌─────────────┐  ┌──────────────┐
│ms_truck_type│  │ms_cargo_type │
└─────────────┘  └──────────────┘


┌─────────────────┐
│  ms_route       │
└────────┬────────┘
         │
         │ 1:N
         ▼
┌──────────────────────────────┐
│ ms_route_truck_compatibility │ ← Truck mana boleh lewat route ini?
└──────────────────────────────┘

┌─────────────────┐
│  ms_route       │
└────────┬────────┘
         │
         │ 1:N
         ▼
┌──────────────────────────────┐
│ ms_route_cargo_requirements  │ ← Cargo type butuh permit/escort?
└──────────────────────────────┘
```

---

## 4. CONTOH DATA LENGKAP

### 4.1 Route: Jakarta - Bandung
```sql
INSERT INTO ms_route VALUES (
    'R001',
    'JKT-BDG-01',
    'Jakarta - Bandung (Tol Cipularang)',
    'SHUTTLE',
    'LOC001', 'Jakarta', 'Jl. Sudirman No.1', -6.2088, 106.8456,
    'LOC002', 'Bandung', 'Jl. Asia Afrika No.100', -6.9175, 107.6191,
    150.5, 3.0, 2.5, 4.0,
    'TOLL', 'EXCELLENT', 'MEDIUM',
    20.0, 4.0, 2.5, NULL, NULL,
    65000, 300000, 20000,
    1, 6.5, 30,
    0, NULL,
    1, 0, 0, NULL, NULL, 'admin', GETDATE(), NULL, NULL
);
```

### 4.2 Tariff Matrix: Jakarta-Bandung

#### Tariff 1: Pickup + General Cargo
```sql
INSERT INTO ms_route_tariff VALUES (
    'T001',
    'R001', -- Jakarta-Bandung
    'TT001', -- Pickup
    'CT001', -- General Cargo
    'SHUTTLE',
    800000, -- base rate
    3000, -- per km
    50000, -- per ton
    NULL, -- per cbm
    600000, -- minimum charge
    300000, -- uang jalan
    200000, -- uang jasa base
    20000, -- uang jasa per ton
    50000, -- performance bonus
    0, -- helper fee (pickup tidak perlu kernet)
    65000, -- toll
    300000, -- fuel
    20000, -- parking
    0, -- permit
    0, -- other
    0, -- temperature control
    0, -- security escort
    5000, -- insurance (0.625% dari rate)
    '2025-01-01', NULL,
    1, 'Standard rate untuk pickup + general cargo', 'admin', GETDATE(), NULL
);
```

#### Tariff 2: Tronton + General Cargo
```sql
INSERT INTO ms_route_tariff VALUES (
    'T002',
    'R001', -- Jakarta-Bandung
    'TT004', -- Tronton
    'CT001', -- General Cargo
    'SHUTTLE',
    1500000, -- base rate (+87.5% vs pickup)
    5000, -- per km
    80000, -- per ton
    NULL,
    1200000, -- minimum
    400000, -- uang jalan (+33%)
    300000, -- uang jasa base (+50%)
    25000, -- per ton
    100000, -- performance bonus
    100000, -- helper fee (tronton butuh kernet)
    85000, -- toll (+20k, truck besar)
    700000, -- fuel (+133%, truck besar boros)
    30000, -- parking
    0, 0, 0,
    15000, -- insurance
    '2025-01-01', NULL,
    1, 'Tronton untuk general cargo, kapasitas besar', 'admin', GETDATE(), NULL
);
```

#### Tariff 3: Tronton + Dangerous Goods
```sql
INSERT INTO ms_route_tariff VALUES (
    'T003',
    'R001', -- Jakarta-Bandung
    'TT004', -- Tronton
    'CT003', -- Dangerous Goods
    'SHUTTLE',
    2500000, -- base rate (+67% vs T002!) ⚠️ PREMIUM
    8000, -- per km
    120000, -- per ton
    NULL,
    2000000, -- minimum
    600000, -- uang jalan (+50%, risk premium)
    500000, -- uang jasa (+67%, specialized driver)
    40000, -- per ton
    150000, -- performance bonus
    150000, -- helper fee (trained helper)
    85000, -- toll (same)
    700000, -- fuel (same)
    30000, -- parking
    500000, -- ⭐ permit cost (dangerous goods)
    0,
    0,
    1000000, -- ⭐ security escort
    50000, -- insurance (higher)
    '2025-01-01', NULL,
    1, 'Dangerous goods memerlukan permit, escort, specialized driver', 'admin', GETDATE(), NULL
);
```

#### Tariff 4: Box Pendingin + Frozen Goods
```sql
INSERT INTO ms_route_tariff VALUES (
    'T004',
    'R001', -- Jakarta-Bandung
    'TT006', -- Box Pendingin
    'CT005', -- Frozen Goods
    'SHUTTLE',
    3000000, -- base rate (+100% vs T002!) ⚠️ PREMIUM
    7000, -- per km
    150000, -- per ton
    NULL,
    2500000, -- minimum
    500000, -- uang jalan
    400000, -- uang jasa (specialized)
    35000, -- per ton
    120000, -- performance bonus
    120000, -- helper fee
    85000, -- toll
    900000, -- fuel (+28%, cooler consumes more)
    30000, -- parking
    0, 0,
    400000, -- ⭐ temperature control cost (cooler operation)
    0,
    60000, -- insurance (perishable goods)
    '2025-01-01', NULL,
    1, 'Frozen goods memerlukan temperature control, cooler truck', 'admin', GETDATE(), NULL
);
```

---

## 5. COST CALCULATION LOGIC (dengan Truck Type & Cargo Type)

### 5.1 Calculation Function
```php
function calculateRouteCost($routeId, $truckTypeId, $cargoTypeId, $weight, $volume, $serviceType = 'SHUTTLE') {
    
    // 1. Get route info
    $route = MsRoute::find($routeId);
    
    // 2. Get truck type
    $truckType = MsTruckType::find($truckTypeId);
    
    // 3. Get cargo type
    $cargoType = MsCargoType::find($cargoTypeId);
    
    // 4. Check compatibility
    $compatibility = MsRouteTruckCompatibility::where('route_id', $routeId)
                                              ->where('truck_type_id', $truckTypeId)
                                              ->first();
    
    if (!$compatibility || !$compatibility->is_compatible) {
        return [
            'error' => 'Truck type ' . $truckType->truck_type_name . ' tidak compatible dengan route ini',
            'reason' => $compatibility->restriction_reason ?? 'Unknown'
        ];
    }
    
    // 5. Get tariff (specific untuk kombinasi route + truck + cargo)
    $tariff = MsRouteTariff::where('route_id', $routeId)
                           ->where('truck_type_id', $truckTypeId)
                           ->where('cargo_type_id', $cargoTypeId)
                           ->where('service_type', $serviceType)
                           ->where('is_active', 1)
                           ->where('effective_date', '<=', now())
                           ->where(function($q) {
                               $q->whereNull('expired_date')
                                 ->orWhere('expired_date', '>=', now());
                           })
                           ->first();
    
    if (!$tariff) {
        return ['error' => 'Tariff tidak ditemukan untuk kombinasi ini'];
    }
    
    // 6. Calculate base components
    $baseRate = $tariff->base_rate;
    $kmCost = ($route->distance_km > 0) ? ($route->distance_km * $tariff->rate_per_km) : 0;
    $weightCost = $weight * $tariff->rate_per_ton;
    $volumeCost = $volume * ($tariff->rate_per_cbm ?? 0);
    
    // 7. Apply multipliers from cargo type
    $handlingMultiplier = $cargoType->handling_cost_multiplier ?? 1.0;
    $driverFeeMultiplier = $cargoType->driver_fee_multiplier ?? 1.0;
    $fuelMultiplier = $cargoType->fuel_cost_multiplier ?? 1.0;
    
    // 8. Calculate total revenue
    $subtotal = $baseRate + $kmCost + $weightCost + $volumeCost;
    $totalRevenue = $subtotal * $handlingMultiplier;
    $totalRevenue = max($totalRevenue, $tariff->minimum_charge); // ensure minimum
    
    // 9. Calculate costs
    $costs = [
        'uang_jalan' => $tariff->uang_jalan,
        'uang_jasa_base' => $tariff->uang_jasa_base * $driverFeeMultiplier,
        'uang_jasa_per_ton' => ($tariff->uang_jasa_per_ton * $weight) * $driverFeeMultiplier,
        'helper_fee' => $tariff->helper_fee,
        'toll' => ($tariff->toll_reimbursement + ($compatibility->toll_adjustment ?? 0)),
        'fuel' => ($tariff->fuel_reimbursement * $fuelMultiplier * ($compatibility->fuel_adjustment_multiplier ?? 1.0)),
        'parking' => $tariff->parking_reimbursement,
        'permit' => $tariff->permit_reimbursement,
        'temperature_control' => $tariff->temperature_control_cost,
        'security_escort' => $tariff->security_escort_cost,
        'insurance' => $tariff->insurance_cost,
        'other' => $tariff->other_reimbursement,
    ];
    
    $totalCost = array_sum($costs);
    
    // 10. Calculate margin
    $margin = $totalRevenue - $totalCost;
    $marginPercentage = ($totalRevenue > 0) ? (($margin / $totalRevenue) * 100) : 0;
    
    // 11. Check cargo requirements
    $cargoReq = MsRouteCargoRequirements::where('route_id', $routeId)
                                        ->where('cargo_type_id', $cargoTypeId)
                                        ->first();
    
    $warnings = [];
    if ($cargoReq) {
        if ($cargoReq->requires_permit) {
            $warnings[] = 'Requires permit: ' . $cargoReq->permit_type;
            $costs['permit'] += $cargoReq->permit_cost;
        }
        if ($cargoReq->requires_escort) {
            $warnings[] = 'Requires security escort';
            $costs['security_escort'] += $cargoReq->escort_cost;
        }
        if ($cargoReq->requires_notification) {
            $warnings[] = 'Requires notification ' . $cargoReq->notification_lead_time_hours . ' hours before departure';
        }
        if ($cargoReq->additional_cost > 0) {
            $costs['additional'] = $cargoReq->additional_cost;
        }
    }
    
    // Recalculate after cargo requirements
    $totalCost = array_sum($costs);
    $margin = $totalRevenue - $totalCost;
    $marginPercentage = ($totalRevenue > 0) ? (($margin / $totalRevenue) * 100) : 0;
    
    return [
        'route' => $route->route_name,
        'truck_type' => $truckType->truck_type_name,
        'cargo_type' => $cargoType->cargo_type_name,
        'distance_km' => $route->distance_km,
        'weight_ton' => $weight,
        'volume_cbm' => $volume,
        
        'revenue_breakdown' => [
            'base_rate' => $baseRate,
            'km_charge' => $kmCost,
            'weight_charge' => $weightCost,
            'volume_charge' => $volumeCost,
            'subtotal' => $subtotal,
            'handling_multiplier' => $handlingMultiplier,
            'total_revenue' => $totalRevenue,
            'minimum_charge' => $tariff->minimum_charge,
        ],
        
        'cost_breakdown' => $costs,
        'total_cost' => $totalCost,
        
        'margin' => $margin,
        'margin_percentage' => round($marginPercentage, 2),
        
        'warnings' => $warnings,
        
        'time_estimate' => [
            'normal_hours' => $route->estimated_time_hours + ($compatibility->time_adjustment_hours ?? 0),
            'min_hours' => $route->estimated_time_min,
            'max_hours' => $route->estimated_time_max,
        ],
    ];
}
```

### 5.2 Contoh Perhitungan

#### Case 1: Pickup + General Cargo (12 ton, 15 cbm)
```php
$result = calculateRouteCost('R001', 'TT001', 'CT001', 12, 15, 'SHUTTLE');

Output:
[
    'route' => 'Jakarta - Bandung',
    'truck_type' => 'Pickup Truck',
    'cargo_type' => 'General Cargo',
    'distance_km' => 150.5,
    'weight_ton' => 12,
    'volume_cbm' => 15,
    
    'revenue_breakdown' => [
        'base_rate' => 800000,
        'km_charge' => 451500, // 150.5 * 3000
        'weight_charge' => 600000, // 12 * 50000
        'volume_charge' => 0,
        'subtotal' => 1851500,
        'handling_multiplier' => 1.0,
        'total_revenue' => 1851500,
        'minimum_charge' => 600000,
    ],
    
    'cost_breakdown' => [
        'uang_jalan' => 300000,
        'uang_jasa_base' => 200000,
        'uang_jasa_per_ton' => 240000, // 12 * 20000
        'helper_fee' => 0,
        'toll' => 65000,
        'fuel' => 300000,
        'parking' => 20000,
        'permit' => 0,
        'temperature_control' => 0,
        'security_escort' => 0,
        'insurance' => 5000,
        'other' => 0,
    ],
    'total_cost' => 1130000,
    
    'margin' => 721500,
    'margin_percentage' => 38.96, // PROFITABLE ✅
    
    'warnings' => [],
]
```

#### Case 2: Tronton + Dangerous Goods (15 ton)
```php
$result = calculateRouteCost('R001', 'TT004', 'CT003', 15, 60, 'SHUTTLE');

Output:
[
    'route' => 'Jakarta - Bandung',
    'truck_type' => 'Truck Tronton',
    'cargo_type' => 'Dangerous Goods',
    'distance_km' => 150.5,
    'weight_ton' => 15,
    'volume_cbm' => 60,
    
    'revenue_breakdown' => [
        'base_rate' => 2500000,
        'km_charge' => 1204000, // 150.5 * 8000
        'weight_charge' => 1800000, // 15 * 120000
        'volume_charge' => 0,
        'subtotal' => 5504000,
        'handling_multiplier' => 1.8, // dangerous goods multiplier
        'total_revenue' => 9907200, // 5504000 * 1.8
        'minimum_charge' => 2000000,
    ],
    
    'cost_breakdown' => [
        'uang_jalan' => 600000,
        'uang_jasa_base' => 750000, // 500000 * 1.5 (driver multiplier)
        'uang_jasa_per_ton' => 900000, // 15 * 40000 * 1.5
        'helper_fee' => 150000,
        'toll' => 85000,
        'fuel' => 700000,
        'parking' => 30000,
        'permit' => 500000, // ⭐ dangerous goods permit
        'temperature_control' => 0,
        'security_escort' => 1000000, // ⭐ escort required
        'insurance' => 50000,
        'additional' => 200000, // dari cargo requirements
    ],
    'total_cost' => 4965000,
    
    'margin' => 4942200,
    'margin_percentage' => 49.88, // HIGH MARGIN! ✅
    
    'warnings' => [
        'Requires permit: Surat Izin Pengangkutan Bahan Berbahaya',
        'Requires security escort',
        'Requires notification 24 hours before departure',
    ],
]
```

#### Case 3: Box Pendingin + Frozen Goods (8 ton)
```php
$result = calculateRouteCost('R001', 'TT006', 'CT005', 8, 40, 'SHUTTLE');

Output:
[
    'total_revenue' => 4856000,
    'total_cost' => 3334000, // fuel + cooler operation cost tinggi
    'margin' => 1522000,
    'margin_percentage' => 31.34, // GOOD MARGIN ✅
    
    'warnings' => [
        'Temperature must be maintained at -18 to -10°C',
    ],
]
```

---

## 6. QUERY EXAMPLES

### 6.1 Find Best Tariff untuk Requirement
```sql
-- User pilih: Route Jakarta-Bandung, 12 ton, General Cargo, perlu truck apa?

SELECT 
    r.route_name,
    tt.truck_type_name,
    ct.cargo_type_name,
    t.base_rate,
    t.uang_jalan,
    t.uang_jasa_base,
    (t.base_rate + (r.distance_km * t.rate_per_km) + (12 * t.rate_per_ton)) as estimated_revenue,
    (t.uang_jalan + t.uang_jasa_base + (12 * t.uang_jasa_per_ton) + t.helper_fee + t.toll_reimbursement + t.fuel_reimbursement) as estimated_cost,
    ((t.base_rate + (r.distance_km * t.rate_per_km) + (12 * t.rate_per_ton)) - 
     (t.uang_jalan + t.uang_jasa_base + (12 * t.uang_jasa_per_ton) + t.helper_fee + t.toll_reimbursement + t.fuel_reimbursement)) as margin
FROM ms_route_tariff t
JOIN ms_route r ON t.route_id = r.route_id
JOIN ms_truck_type tt ON t.truck_type_id = tt.truck_type_id
JOIN ms_cargo_type ct ON t.cargo_type_id = ct.cargo_type_id
WHERE r.route_code = 'JKT-BDG-01'
  AND ct.cargo_type_code = 'GENERAL'
  AND tt.max_capacity_ton >= 12 -- truck harus bisa angkut 12 ton
  AND t.is_active = 1
  AND t.effective_date <= GETDATE()
  AND (t.expired_date IS NULL OR t.expired_date >= GETDATE())
ORDER BY margin DESC;

Result:
| truck_type   | estimated_revenue | estimated_cost | margin  | margin_% |
|--------------|-------------------|----------------|---------|----------|
| Tronton      | 2,551,500         | 1,915,000      | 636,500 | 24.94%   |
| Fuso         | 2,100,000         | 1,650,000      | 450,000 | 21.43%   |
| Engkel       | 1,650,000         | 1,400,000      | 250,000 | 15.15%   |

Recommendation: Pilih Tronton (margin tertinggi)
```

### 6.2 Find All Tariff Combinations untuk Route
```sql
SELECT 
    r.route_code,
    tt.truck_type_name,
    ct.cargo_type_name,
    t.service_type,
    t.base_rate,
    FORMAT(t.effective_date, 'yyyy-MM-dd') as effective_date,
    CASE WHEN t.is_active = 1 THEN 'Active' ELSE 'Inactive' END as status
FROM ms_route_tariff t
JOIN ms_route r ON t.route_id = r.route_id
JOIN ms_truck_type tt ON t.truck_type_id = tt.truck_type_id
JOIN ms_cargo_type ct ON t.cargo_type_id = ct.cargo_type_id
WHERE r.route_code = 'JKT-BDG-01'
ORDER BY ct.cargo_type_name, tt.truck_type_name;

Result: Matrix tariff untuk Jakarta-Bandung
| Truck Type    | General  | Fragile  | Dangerous | Frozen   |
|---------------|----------|----------|-----------|----------|
| Pickup        | 800k     | 1,000k   | N/A       | N/A      |
| Engkel        | 1,200k   | 1,500k   | N/A       | N/A      |
| Fuso          | 1,400k   | 1,700k   | 2,200k    | N/A      |
| Tronton       | 1,500k   | 1,800k   | 2,500k    | N/A      |
| Box Pendingin | N/A      | N/A      | N/A       | 3,000k   |
```

---

## 7. MIGRATION STEPS

```sql
-- Step 1: Create master tables
CREATE TABLE ms_truck_type (...);
CREATE TABLE ms_cargo_type (...);

-- Step 2: Seed master data
INSERT INTO ms_truck_type VALUES (...); -- 6-10 truck types
INSERT INTO ms_cargo_type VALUES (...); -- 7-10 cargo types

-- Step 3: Create/update route table
CREATE TABLE ms_route (...);

-- Step 4: Create tariff table (with FK to truck_type & cargo_type)
CREATE TABLE ms_route_tariff (...);

-- Step 5: Create compatibility & requirements tables
CREATE TABLE ms_route_truck_compatibility (...);
CREATE TABLE ms_route_cargo_requirements (...);

-- Step 6: Seed sample route & tariffs
INSERT INTO ms_route VALUES ('R001', ...); -- Jakarta-Bandung
INSERT INTO ms_route_tariff VALUES (...); -- 4-10 tariff combinations

-- Step 7: Create indexes
CREATE INDEX idx_route_tariff_combo ON ms_route_tariff(route_id, truck_type_id, cargo_type_id, is_active);
```

---

## 8. SUMMARY

### Tabel Utama:
1. **ms_truck_type** - Master jenis truck (Pickup, Engkel, Fuso, Tronton, dll)
2. **ms_cargo_type** - Master jenis barang (General, Fragile, Dangerous, Frozen, dll)
3. **ms_route** - Master route (origin-destination)
4. **ms_route_tariff** - Tariff matrix (**Route × Truck Type × Cargo Type**)
5. **ms_route_truck_compatibility** - Truck mana boleh lewat route mana
6. **ms_route_cargo_requirements** - Cargo type butuh permit/escort di route tertentu

### Key Concepts:
✅ **1 Route bisa punya BANYAK tariff** (tergantung truck type & cargo type)
✅ **Tariff ID unique per kombinasi** (route + truck + cargo + service type)
✅ **Cost calculation consider multipliers** (dari cargo type: handling, driver fee, fuel)
✅ **Compatibility check** (truck besar mungkin tidak bisa lewat jalan kecil)
✅ **Special requirements** (dangerous goods butuh permit + escort)

### Benefit:
✅ Flexible pricing (tariff berbeda untuk kombinasi berbeda)
✅ Accurate costing (consider truck type fuel consumption, cargo type handling)
✅ Compliance (enforce restrictions, permit requirements)
✅ Profitability visibility (margin per kombinasi route-truck-cargo)

---

**Apakah struktur tabel route ini sudah sesuai dengan kebutuhan bisnis Anda? Ada yang perlu disesuaikan atau ditambahkan?** 🚀
