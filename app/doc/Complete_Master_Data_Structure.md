# TMS - Complete Master Data Structure
## (Point, Client, Product, Route, Tariff with Dynamic Pricing)

**Tanggal:** 17 November 2025  
**Last Updated:** 18 November 2025  
**Status:** Design Phase - UPDATED with Tariff & Tanker Support  

**📚 Related Documentation:**
- 📄 **Tariff Structure**: `Tariff_Structure_Analysis.md`  
- 📄 **Tanker Operations**: `Tanker_Truck_Volume_Measurement.md`  
- 📄 **Transaction Tables**: `Database_Transaction_Tables_Plan.md`  
- 📄 **Dispatcher System**: `Transaction_Tables_Dispatcher_Accounting.md`  

---

## 1. REALITAS BISNIS - MENGAPA PERLU TABEL TAMBAHAN?

### 1.1 Problem dengan Design Sebelumnya ❌
```
Design lama:
ms_route: origin_name, destination_name (string hardcoded)
ms_route_tariff: base_rate (fixed pricing)

Problem:
1. Origin/Destination duplicate (Jakarta ditulis 100x di berbeda route)
2. Tidak ada data lokasi lengkap (GPS, alamat, PIC, jam operasional)
3. Tariff FIXED (padahal client A beda dengan client B!)
4. Tidak tahu product apa yang diangkut (general cargo terlalu umum)
5. Tidak bisa tracking: "Bulan ini kirim berapa kali ke PT Indofood?"
```

### 1.2 Realitas yang Benar ✅
```
Skenario Bisnis NYATA:

Client: PT Indofood
Product: Mie Instan (FMCG)
Route: Pabrik Cikupa → DC Cileungsi
Truck: Tronton 15 ton
Tariff: Rp 1,200,000 (CONTRACT RATE - 40 trips/month)

VS

Client: PT Unilever  
Product: Sabun Mandi (FMCG)
Route: Pabrik Cikupa → DC Cileungsi (SAME ROUTE!)
Truck: Tronton 15 ton
Tariff: Rp 1,500,000 (SPOT RATE - 5 trips/month)

ROUTE SAMA, TRUCK SAMA, tapi TARIFF BEDA 25%! Karena:
- Client berbeda (contract vs spot)
- Volume commitment berbeda
- Product handling berbeda (mie instan vs sabun)

Kesimpulan:
Tariff = Route + Truck + Cargo + CLIENT + PRODUCT + SERVICE LEVEL
```

---

## 2. COMPLETE DATABASE STRUCTURE

### 2.1 ms_point (Master Point/Location) ⭐ CRUCIAL
```sql
CREATE TABLE ms_point (
    point_id VARCHAR(50) PRIMARY KEY,
    point_code VARCHAR(20) UNIQUE NOT NULL,
    point_name VARCHAR(200) NOT NULL,
    
    -- Category
    point_type VARCHAR(50) NOT NULL, -- WAREHOUSE, FACTORY, DISTRIBUTION_CENTER, PORT, CUSTOMER_SITE, DEPOT
    
    -- Address
    address_line1 VARCHAR(200),
    address_line2 VARCHAR(200),
    city VARCHAR(100),
    district VARCHAR(100), -- kecamatan
    subdistrict VARCHAR(100), -- kelurahan
    province VARCHAR(100),
    postal_code VARCHAR(10),
    country VARCHAR(50) DEFAULT 'Indonesia',
    
    -- GPS Coordinates
    latitude DECIMAL(10,8), -- -6.2088
    longitude DECIMAL(11,8), -- 106.8456
    geofence_radius_meter INT DEFAULT 100, -- untuk tracking arrival/departure
    
    -- Contact Information
    pic_name VARCHAR(100), -- person in charge
    pic_phone VARCHAR(20),
    pic_email VARCHAR(100),
    
    -- Operational Details
    operational_hours_weekday VARCHAR(50), -- "08:00-17:00"
    operational_hours_weekend VARCHAR(50), -- "08:00-12:00" or "CLOSED"
    is_operational_sunday BIT DEFAULT 0,
    
    -- Loading/Unloading Facilities
    has_loading_dock BIT DEFAULT 0,
    has_forklift BIT DEFAULT 0,
    has_crane BIT DEFAULT 0,
    max_truck_capacity INT, -- berapa truck bisa masuk sekaligus
    average_loading_time_minutes INT, -- rata-rata waktu muat
    average_unloading_time_minutes INT, -- rata-rata waktu bongkar
    
    -- Restrictions
    max_truck_type_allowed VARCHAR(50), -- TRAILER, TRONTON, FUSO, ENGKEL, PICKUP
    requires_appointment BIT DEFAULT 0, -- perlu booking slot atau bisa langsung
    appointment_lead_time_hours INT, -- min 24 jam sebelumnya
    
    -- Costs
    parking_fee DECIMAL(15,2),
    loading_fee DECIMAL(15,2),
    unloading_fee DECIMAL(15,2),
    security_deposit DECIMAL(15,2), -- deposit security (jika ada)
    
    -- Client Relationship
    client_id VARCHAR(50), -- FK ke ms_client (jika point ini milik client tertentu)
    
    -- Metadata
    is_active BIT DEFAULT 1,
    is_favorite BIT DEFAULT 0, -- frequently used
    usage_count INT DEFAULT 0,
    last_visited_date DATE,
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    
    INDEX idx_point_code (point_code),
    INDEX idx_point_type (point_type),
    INDEX idx_city (city),
    INDEX idx_client (client_id),
    INDEX idx_is_active (is_active)
)

-- Sample Data
INSERT INTO ms_point VALUES
('PT001', 'JKT-WH-01', 'Gudang Cakung', 'WAREHOUSE', 'Jl. Cakung Cilincing No.5', NULL, 'Jakarta Utara', 'Cakung', 'Cakung Timur', 'DKI Jakarta', '13910', 'Indonesia', -6.1380, 106.9500, 100, 'Budi Santoso', '081234567890', 'budi@warehouse.com', '08:00-17:00', '08:00-12:00', 0, 1, 1, 0, 5, 30, 30, 'TRONTON', 1, 24, 50000, 100000, 100000, 0, NULL, 1, 1, 156, '2025-11-10', 'Gudang utama Jakarta', 'admin', GETDATE(), NULL, NULL),

('PT002', 'BDG-DC-01', 'Distribution Center Bandung', 'DISTRIBUTION_CENTER', 'Jl. Soekarno Hatta KM 12', NULL, 'Bandung', 'Bojong Soang', 'Bojong Soang', 'Jawa Barat', '40287', 'Indonesia', -6.9497, 107.6314, 150, 'Siti Nurhaliza', '082345678901', 'siti@dcbandung.com', '07:00-18:00', '07:00-13:00', 0, 1, 1, 1, 8, 45, 45, 'TRAILER', 1, 48, 75000, 150000, 150000, 0, 'CL001', 1, 1, 89, '2025-11-15', 'DC milik PT Indofood', 'admin', GETDATE(), NULL, NULL),

('PT003', 'CKP-FAC-01', 'Pabrik Cikupa', 'FACTORY', 'Kawasan Industri Cikupa', 'Blok A5', 'Tangerang', 'Cikupa', 'Pasir Jaya', 'Banten', '15710', 'Indonesia', -6.2380, 106.5050, 200, 'Agus Wijaya', '083456789012', 'agus@factory.com', '00:00-23:59', '00:00-23:59', 1, 1, 1, 1, 10, 60, 20, 'TRAILER', 1, 72, 0, 200000, 50000, 500000, 'CL001', 1, 1, 234, '2025-11-17', 'Pabrik 24/7 operation', 'admin', GETDATE(), NULL, NULL);
```

### 2.2 ms_client (Master Client/Customer) ⭐ CRUCIAL
```sql
CREATE TABLE ms_client (
    client_id VARCHAR(50) PRIMARY KEY,
    client_code VARCHAR(20) UNIQUE NOT NULL,
    client_name VARCHAR(200) NOT NULL,
    
    -- Legal Entity
    legal_name VARCHAR(200), -- PT Indofood Sukses Makmur Tbk
    tax_id VARCHAR(50), -- NPWP
    business_type VARCHAR(100), -- FMCG, Automotive, Pharmaceutical, Construction
    
    -- Contact
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(100),
    
    -- Key Contact Person
    pic_name VARCHAR(100),
    pic_position VARCHAR(100),
    pic_phone VARCHAR(20),
    pic_email VARCHAR(100),
    
    -- Finance Contact
    finance_pic_name VARCHAR(100),
    finance_pic_phone VARCHAR(20),
    finance_pic_email VARCHAR(100),
    
    -- Business Terms
    contract_type VARCHAR(50), -- CONTRACT, SPOT, HYBRID
    contract_start_date DATE,
    contract_end_date DATE,
    minimum_monthly_trips INT, -- min 40 trips/month
    discount_percentage DECIMAL(5,2), -- 10% discount if meet minimum
    
    -- Payment Terms
    payment_term_days INT, -- TOP 30, 45, 60 days
    payment_method VARCHAR(50), -- BANK_TRANSFER, CHEQUE, CASH
    bank_name VARCHAR(100),
    bank_account_number VARCHAR(50),
    bank_account_name VARCHAR(100),
    
    -- Credit Limit
    credit_limit DECIMAL(18,2), -- max outstanding Rp 100 juta
    current_outstanding DECIMAL(18,2) DEFAULT 0,
    
    -- Service Level
    priority_level VARCHAR(20), -- VIP, HIGH, MEDIUM, LOW
    requires_dedicated_driver BIT DEFAULT 0, -- driver tetap untuk client ini
    requires_specific_truck BIT DEFAULT 0, -- truck tertentu untuk client ini
    allows_backhaul BIT DEFAULT 1, -- boleh ambil backhaul atau tidak
    
    -- Rating & Performance
    rating_ontime_payment DECIMAL(3,2), -- 0.00-5.00
    rating_load_accuracy DECIMAL(3,2), -- seberapa akurat info muatan mereka
    total_revenue_ytd DECIMAL(18,2), -- year to date revenue
    total_trips_ytd INT,
    
    -- Status
    status VARCHAR(20) DEFAULT 'ACTIVE', -- ACTIVE, SUSPENDED, BLACKLIST, INACTIVE
    blacklist_reason TEXT,
    
    -- Metadata
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    INDEX idx_client_code (client_code),
    INDEX idx_client_name (client_name),
    INDEX idx_contract_type (contract_type),
    INDEX idx_status (status),
    INDEX idx_is_active (is_active)
)

-- Sample Data
INSERT INTO ms_client VALUES
('CL001', 'CLI-IDF-001', 'PT Indofood', 'PT Indofood Sukses Makmur Tbk', '01.001.234.567.000', 'FMCG', 'Jl. Sudirman Kav 76-78, Jakarta', 'Jakarta Selatan', 'DKI Jakarta', '12910', '021-5795-5888', 'info@indofood.com', 'www.indofood.com', 'Budi Setiawan', 'Logistics Manager', '081234567890', 'budi.setiawan@indofood.com', 'Siti Rahmawati', '081234567891', 'siti.rahmawati@indofood.com', 'CONTRACT', '2025-01-01', '2025-12-31', 40, 15.00, 30, 'BANK_TRANSFER', 'Bank Mandiri', '1234567890', 'PT Indofood Sukses Makmur', 100000000, 25000000, 'VIP', 1, 1, 1, 4.80, 4.90, 850000000, 345, 'ACTIVE', NULL, 1, 'Client VIP dengan contract 1 tahun', 'admin', GETDATE(), NULL, NULL),

('CL002', 'CLI-UNI-001', 'PT Unilever', 'PT Unilever Indonesia Tbk', '01.002.345.678.000', 'FMCG', 'Jl. BSD Green Office Park, Tangerang', 'Tangerang Selatan', 'Banten', '15345', '021-5000-2000', 'info@unilever.co.id', 'www.unilever.co.id', 'Ahmad Fauzi', 'Supply Chain Head', '082345678901', 'ahmad.fauzi@unilever.co.id', 'Dewi Sartika', '082345678902', 'dewi.sartika@unilever.co.id', 'HYBRID', '2025-01-01', '2025-12-31', 20, 10.00, 45, 'BANK_TRANSFER', 'Bank BCA', '9876543210', 'PT Unilever Indonesia', 75000000, 15000000, 'HIGH', 0, 0, 1, 4.70, 4.80, 450000000, 187, 'ACTIVE', NULL, 1, 'Client hybrid: contract untuk shuttle, spot untuk ad-hoc', 'admin', GETDATE(), NULL, NULL),

('CL003', 'CLI-ABC-001', 'CV ABC Logistics', 'CV ABC Logistics', '02.123.456.789.000', 'Logistics Service', 'Jl. Raya Bekasi KM 18', 'Bekasi', 'Jawa Barat', '17520', '021-8888-9999', 'info@abclog.com', NULL, 'Rudi Hartono', 'Owner', '083456789012', 'rudi@abclog.com', 'Rudi Hartono', '083456789012', 'rudi@abclog.com', 'SPOT', NULL, NULL, 0, 0, 7, 'CASH', NULL, NULL, NULL, 10000000, 2500000, 'MEDIUM', 0, 0, 1, 3.50, 3.80, 45000000, 34, 'ACTIVE', NULL, 1, 'Client spot, bayar cash COD', 'admin', GETDATE(), NULL, NULL);
```

### 2.3 ms_product (Master Product) ⭐ CRUCIAL
```sql
CREATE TABLE ms_product (
    product_id VARCHAR(50) PRIMARY KEY,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    
    -- Client Relationship
    client_id VARCHAR(50), -- product milik client mana (optional, bisa null untuk generic)
    
    -- Category
    product_category VARCHAR(100), -- FMCG, ELECTRONICS, CONSTRUCTION, CHEMICAL, FOOD, BEVERAGE
    product_subcategory VARCHAR(100), -- Instant Noodle, Soap, Detergent, etc
    
    -- Cargo Type Mapping
    cargo_type_id VARCHAR(50) NOT NULL, -- link ke ms_cargo_type
    
    -- Physical Characteristics
    packaging_type VARCHAR(50), -- CARTON, PALLET, JUMBO_BAG, DRUM, IBC_TANK
    standard_weight_kg DECIMAL(10,2), -- berat per unit packaging
    standard_volume_cbm DECIMAL(10,4), -- volume per unit packaging
    standard_dimension_length_cm DECIMAL(10,2),
    standard_dimension_width_cm DECIMAL(10,2),
    standard_dimension_height_cm DECIMAL(10,2),
    
    -- Loading Configuration
    units_per_pallet INT, -- berapa carton per pallet
    pallets_per_truck INT, -- berapa pallet muat di truck tertentu
    max_stacking_height INT, -- max berapa layer ditumpuk
    
    -- Handling Requirements
    is_fragile BIT DEFAULT 0,
    is_stackable BIT DEFAULT 1,
    requires_temperature_control BIT DEFAULT 0,
    temperature_min_celsius DECIMAL(5,2),
    temperature_max_celsius DECIMAL(5,2),
    requires_special_permit BIT DEFAULT 0,
    
    -- Value
    estimated_value_per_unit DECIMAL(15,2), -- untuk insurance calculation
    
    -- Metadata
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    FOREIGN KEY (cargo_type_id) REFERENCES ms_cargo_type(cargo_type_id),
    
    INDEX idx_product_code (product_code),
    INDEX idx_client (client_id),
    INDEX idx_cargo_type (cargo_type_id),
    INDEX idx_category (product_category),
    INDEX idx_is_active (is_active)
)

-- Sample Data
INSERT INTO ms_product VALUES
('PRD001', 'IDF-MIE-GORENG', 'Indomie Goreng (Carton)', 'CL001', 'FMCG', 'Instant Noodle', 'CT001', 'CARTON', 8.5, 0.045, 40, 30, 25, 40, 20, 5, 0, 1, 0, NULL, NULL, 0, 35000, 1, 'Best seller Indofood', 'admin', GETDATE(), NULL, NULL),

('PRD002', 'IDF-MIE-KUAH', 'Indomie Kuah Soto (Carton)', 'CL001', 'FMCG', 'Instant Noodle', 'CT001', 'CARTON', 8.0, 0.042, 38, 28, 24, 40, 20, 5, 0, 1, 0, NULL, NULL, 0, 32000, 1, NULL, 'admin', GETDATE(), NULL, NULL),

('PRD003', 'UNI-SABUN-LIFEBUOY', 'Lifebuoy Sabun Batangan (Carton)', 'CL002', 'FMCG', 'Soap', 'CT001', 'CARTON', 12.0, 0.035, 35, 25, 22, 48, 22, 6, 0, 1, 0, NULL, NULL, 0, 85000, 1, NULL, 'admin', GETDATE(), NULL, NULL),

('PRD004', 'UNI-RINSO-LIQUID', 'Rinso Liquid Detergent (Drum)', 'CL002', 'FMCG', 'Detergent', 'CT002', 'DRUM', 45.0, 0.080, 50, 50, 80, NULL, NULL, 2, 1, 0, 0, NULL, NULL, 0, 450000, 1, 'Fragile container', 'admin', GETDATE(), NULL, NULL),

('PRD005', 'GEN-CHEMICAL-001', 'Sodium Hydroxide (Caustic Soda)', NULL, 'CHEMICAL', 'Industrial Chemical', 'CT003', 'IBC_TANK', 1200.0, 1.2, 120, 100, 120, NULL, NULL, 1, 0, 0, 0, NULL, NULL, 1, 8500000, 1, 'Dangerous goods - requires permit', 'admin', GETDATE(), NULL, NULL),

('PRD006', 'GEN-FROZEN-CHICKEN', 'Frozen Chicken (Pallet)', NULL, 'FOOD', 'Frozen Meat', 'CT005', 'PALLET', 500.0, 1.5, 120, 100, 150, NULL, NULL, 3, 0, 1, 1, -18, -10, 0, 3500000, 1, 'Must keep frozen during transport', 'admin', GETDATE(), NULL, NULL);
```

### 2.4 ms_route (UPDATED - menggunakan Point ID)
```sql
CREATE TABLE ms_route (
    route_id VARCHAR(50) PRIMARY KEY,
    route_code VARCHAR(20) UNIQUE NOT NULL,
    route_name VARCHAR(200) NOT NULL,
    route_type VARCHAR(20) NOT NULL, -- SHUTTLE, MULTIPOINT, ADHOC
    
    -- Origin & Destination (menggunakan Point ID) ⭐ CHANGED
    origin_point_id VARCHAR(50) NOT NULL,
    destination_point_id VARCHAR(50) NOT NULL,
    
    -- Route Characteristics
    distance_km DECIMAL(10,2) NOT NULL,
    estimated_time_hours DECIMAL(5,2),
    estimated_time_min DECIMAL(5,2),
    estimated_time_max DECIMAL(5,2),
    
    -- Road Characteristics
    road_type VARCHAR(50),
    road_condition VARCHAR(20),
    traffic_level VARCHAR(20),
    
    -- Route Restrictions
    max_weight_ton DECIMAL(10,2),
    max_height_meter DECIMAL(5,2),
    max_width_meter DECIMAL(5,2),
    restricted_times VARCHAR(200),
    restricted_days VARCHAR(100),
    
    -- Cost Components (BASE)
    base_toll_fee DECIMAL(15,2),
    estimated_fuel_cost DECIMAL(15,2),
    other_cost DECIMAL(15,2),
    
    -- Round-trip
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
    
    FOREIGN KEY (origin_point_id) REFERENCES ms_point(point_id),
    FOREIGN KEY (destination_point_id) REFERENCES ms_point(point_id),
    FOREIGN KEY (alternative_route_id) REFERENCES ms_route(route_id),
    
    INDEX idx_route_code (route_code),
    INDEX idx_origin_dest (origin_point_id, destination_point_id),
    INDEX idx_is_active (is_active)
)

-- Sample Data
INSERT INTO ms_route VALUES
('R001', 'CKP-BDG-01', 'Cikupa - Bandung DC', 'SHUTTLE', 'PT003', 'PT002', 125.5, 3.5, 3.0, 5.0, 'TOLL', 'EXCELLENT', 'MEDIUM', 20.0, 4.0, 2.5, NULL, NULL, 55000, 280000, 15000, 1, 7.5, 30, 0, NULL, 1, 1, 234, '2025-11-17', 'Pabrik Cikupa ke DC Bandung', 'admin', GETDATE(), NULL, NULL),

('R002', 'JKT-BDG-01', 'Jakarta WH - Bandung DC', 'SHUTTLE', 'PT001', 'PT002', 150.5, 4.0, 3.5, 5.5, 'TOLL', 'GOOD', 'HIGH', 20.0, 4.0, 2.5, '{"weekday":"07:00-09:00,17:00-19:00"}', NULL, 65000, 320000, 20000, 1, 8.5, 30, 0, NULL, 1, 1, 156, '2025-11-15', NULL, 'admin', GETDATE(), NULL, NULL);
```

### 2.5 ms_client_contract_tariff (Client-specific Tariff) ⭐ GAME CHANGER
```sql
CREATE TABLE ms_client_contract_tariff (
    contract_tariff_id VARCHAR(50) PRIMARY KEY,
    
    -- Client & Contract
    client_id VARCHAR(50) NOT NULL,
    contract_number VARCHAR(50),
    
    -- Route, Truck, Product ⭐ KEY COMBINATION
    route_id VARCHAR(50) NOT NULL,
    truck_type_id VARCHAR(50) NOT NULL,
    product_id VARCHAR(50), -- optional: specific untuk product tertentu, NULL = semua product client ini
    
    -- Service Type
    service_type VARCHAR(20), -- SHUTTLE, MULTIPOINT, EXPRESS, REGULAR
    
    -- Pricing (CLIENT-SPECIFIC) ⭐⭐⭐
    client_rate DECIMAL(15,2) NOT NULL, -- tarif untuk CLIENT INI (bisa beda dari standard rate)
    rate_per_km DECIMAL(10,2),
    rate_per_ton DECIMAL(10,2),
    rate_per_trip DECIMAL(15,2), -- flat rate per trip (untuk contract)
    
    -- Minimum & Maximum
    minimum_charge DECIMAL(15,2),
    maximum_charge DECIMAL(15,2), -- cap maximum
    
    -- Volume Discount (jika client commit volume tinggi)
    discount_percentage DECIMAL(5,2), -- 15% discount
    minimum_monthly_trips INT, -- min 40 trips untuk dapat discount
    
    -- Driver & Helper Compensation (bisa beda per client)
    uang_jalan DECIMAL(15,2),
    uang_jasa_base DECIMAL(15,2),
    uang_jasa_per_ton DECIMAL(10,2),
    helper_fee DECIMAL(15,2),
    
    -- Reimbursements
    toll_reimbursement DECIMAL(15,2),
    fuel_reimbursement DECIMAL(15,2),
    parking_reimbursement DECIMAL(15,2),
    other_reimbursement DECIMAL(15,2),
    
    -- Loading/Unloading (who pays?)
    loading_cost DECIMAL(15,2),
    unloading_cost DECIMAL(15,2),
    loading_paid_by VARCHAR(20), -- CLIENT, VENDOR, SHARED
    unloading_paid_by VARCHAR(20),
    
    -- Waiting Time (detention charge)
    free_waiting_time_minutes INT DEFAULT 60, -- 1 jam gratis
    waiting_charge_per_hour DECIMAL(15,2), -- Rp 50k per jam setelahnya
    
    -- Performance Bonus
    ontime_bonus DECIMAL(15,2), -- bonus jika on-time
    full_load_bonus DECIMAL(15,2), -- bonus jika full load
    
    -- Validity
    effective_date DATE NOT NULL,
    expired_date DATE,
    
    -- Contract Terms
    payment_term_days INT, -- TOP khusus untuk route ini (bisa override client default)
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    approved_by VARCHAR(50),
    approved_at DATETIME,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (truck_type_id) REFERENCES ms_truck_type(truck_type_id),
    FOREIGN KEY (product_id) REFERENCES ms_product(product_id),
    
    UNIQUE(client_id, route_id, truck_type_id, product_id, effective_date),
    
    INDEX idx_client_route (client_id, route_id),
    INDEX idx_is_active (is_active, effective_date)
)

-- Sample Data

-- PT Indofood: Cikupa-Bandung, Tronton, Mie Instan (CONTRACT RATE)
INSERT INTO ms_client_contract_tariff VALUES
('CCT001', 'CL001', 'PKS/IDF/2025/001', 'R001', 'TT004', 'PRD001', 'SHUTTLE', 
1200000, 0, 0, 1200000, -- flat rate Rp 1.2jt per trip
1000000, 1500000, 15.00, 40, -- 15% discount jika >= 40 trips/month
400000, 300000, 25000, 100000, -- driver compensation
55000, 280000, 15000, 0, -- reimbursement
0, 0, 'CLIENT', 'CLIENT', -- loading/unloading paid by client
60, 50000, -- 1 jam gratis, Rp 50k/jam setelahnya
100000, 50000, -- bonus on-time & full load
'2025-01-01', '2025-12-31', 30, -- valid 1 tahun, TOP 30
1, 'Contract rate untuk Indofood Mie Instan, shuttle daily', 'manager', GETDATE(), 'admin', GETDATE(), NULL, NULL);

-- PT Indofood: Same route, tapi untuk product lain (Mie Kuah) - beda rate
INSERT INTO ms_client_contract_tariff VALUES
('CCT002', 'CL001', 'PKS/IDF/2025/001', 'R001', 'TT004', 'PRD002', 'SHUTTLE',
1150000, 0, 0, 1150000, -- lebih murah Rp 50k
1000000, 1500000, 15.00, 40,
400000, 300000, 25000, 100000,
55000, 280000, 15000, 0,
0, 0, 'CLIENT', 'CLIENT',
60, 50000,
100000, 50000,
'2025-01-01', '2025-12-31', 30,
1, 'Mie Kuah volume lebih rendah, rate sedikit lebih murah', 'manager', GETDATE(), 'admin', GETDATE(), NULL, NULL);

-- PT Unilever: SAME ROUTE, SAME TRUCK, tapi SPOT RATE (no contract)
INSERT INTO ms_client_contract_tariff VALUES
('CCT003', 'CL002', NULL, 'R001', 'TT004', NULL, 'SHUTTLE', -- product_id NULL = all products
1500000, 0, 0, 1500000, -- Rp 1.5jt (25% lebih mahal dari Indofood!)
1300000, 2000000, 0, 0, -- no discount (spot)
450000, 350000, 30000, 120000,
55000, 280000, 15000, 0,
0, 0, 'VENDOR', 'VENDOR', -- vendor yang bayar loading
30, 75000, -- 30 menit gratis, Rp 75k/jam (lebih ketat)
0, 0, -- no bonus (spot rate)
'2025-01-01', NULL, 7, -- valid until further notice, TOP 7 hari (COD-like)
1, 'Spot rate untuk Unilever, no contract commitment', 'manager', GETDATE(), 'admin', GETDATE(), NULL, NULL);

-- Client Spot (CV ABC): Ad-hoc rate (paling mahal)
INSERT INTO ms_client_contract_tariff VALUES
('CCT004', 'CL003', NULL, 'R001', 'TT004', NULL, 'SHUTTLE',
1800000, 0, 0, 1800000, -- Rp 1.8jt (50% lebih mahal!)
1500000, 2500000, 0, 0,
500000, 400000, 35000, 150000,
55000, 280000, 15000, 0,
0, 0, 'VENDOR', 'VENDOR',
0, 100000, -- no free waiting, langsung charge Rp 100k/jam
0, 0,
'2025-01-01', NULL, 0, -- COD (cash on delivery)
1, 'Spot client, cash payment, highest rate', 'manager', GETDATE(), 'admin', GETDATE(), NULL, NULL);
```

### 2.6 ms_route_tariff (Standard/Default Tariff - fallback jika tidak ada contract) 
```sql
-- Tetap diperlukan sebagai FALLBACK atau STANDARD RATE
-- Digunakan jika:
-- 1. Client belum punya contract tariff
-- 2. Route/Product combination tidak ada di contract
-- 3. Sebagai reference untuk sales quotation

CREATE TABLE ms_route_tariff (
    tariff_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    truck_type_id VARCHAR(50) NOT NULL,
    cargo_type_id VARCHAR(50) NOT NULL, -- general category (bukan product specific)
    service_type VARCHAR(20),
    
    -- STANDARD PRICING (not client-specific)
    base_rate DECIMAL(15,2) NOT NULL,
    rate_per_km DECIMAL(10,2),
    rate_per_ton DECIMAL(10,2),
    rate_per_cbm DECIMAL(10,2),
    minimum_charge DECIMAL(15,2),
    
    -- Standard Compensation
    uang_jalan DECIMAL(15,2),
    uang_jasa_base DECIMAL(15,2),
    uang_jasa_per_ton DECIMAL(10,2),
    uang_jasa_performance_bonus DECIMAL(15,2),
    helper_fee DECIMAL(15,2),
    
    -- Standard Reimbursement
    toll_reimbursement DECIMAL(15,2),
    fuel_reimbursement DECIMAL(15,2),
    parking_reimbursement DECIMAL(15,2),
    permit_reimbursement DECIMAL(15,2),
    other_reimbursement DECIMAL(15,2),
    
    -- Special Costs
    temperature_control_cost DECIMAL(15,2),
    security_escort_cost DECIMAL(15,2),
    insurance_cost DECIMAL(15,2),
    
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
    
    UNIQUE(route_id, truck_type_id, cargo_type_id, service_type, effective_date),
    INDEX idx_route_tariff_active (route_id, truck_type_id, cargo_type_id, is_active)
)

-- Sample: Standard rate untuk route Cikupa-Bandung
INSERT INTO ms_route_tariff VALUES
('T001', 'R001', 'TT004', 'CT001', 'SHUTTLE',
1600000, 5000, 80000, NULL, 1300000, -- STANDARD rate (lebih mahal dari contract)
400000, 300000, 25000, 100000, 100000,
55000, 280000, 15000, 0, 0,
0, 0, 15000,
'2025-01-01', NULL, 1, 'Standard rate untuk Tronton + General Cargo', 'admin', GETDATE(), NULL);
```

---

## 3. RELASI LENGKAP ANTAR TABEL

```
┌──────────────┐
│  ms_client   │ (Client punya banyak point, product, dan contract tariff)
└──────┬───────┘
       │
       │ 1:N
       ├─────────────────┐
       │                 │
       ▼                 ▼
┌──────────────┐   ┌─────────────┐
│  ms_point    │   │ ms_product  │
└──────┬───────┘   └──────┬──────┘
       │                  │
       │                  │
       │                  │ N:1
       │                  ▼
       │            ┌──────────────┐
       │            │ms_cargo_type │
       │            └──────────────┘
       │
       │ N:1 (origin)
       │ N:1 (destination)
       ▼
┌──────────────┐
│  ms_route    │ (Route dari Point A ke Point B)
└──────┬───────┘
       │
       │ 1:N
       ├───────────────────────┬──────────────────────┐
       ▼                       ▼                      ▼
┌──────────────────────┐ ┌────────────────┐  ┌─────────────────────────┐
│ms_route_tariff       │ │ms_truck_type   │  │ms_client_contract_tariff│⭐
│(Standard/Fallback)   │ └────────────────┘  │(Client-Specific Pricing)│
└──────────────────────┘                     └─────────────────────────┘
                                                       │
                                                       │ N:1
                                                       ▼
                                                 ┌──────────────┐
                                                 │  ms_client   │
                                                 └──────────────┘
```

---

## 4. PRICING HIERARCHY (Urutan pencarian tariff)

```php
function getApplicableTariff($clientId, $routeId, $truckTypeId, $productId, $serviceType) {
    
    // PRIORITY 1: Client Contract Tariff - Product Specific ⭐⭐⭐ HIGHEST
    $tariff = MsClientContractTariff::where('client_id', $clientId)
                                    ->where('route_id', $routeId)
                                    ->where('truck_type_id', $truckTypeId)
                                    ->where('product_id', $productId)
                                    ->where('service_type', $serviceType)
                                    ->where('is_active', 1)
                                    ->where('effective_date', '<=', now())
                                    ->where(function($q) {
                                        $q->whereNull('expired_date')
                                          ->orWhere('expired_date', '>=', now());
                                    })
                                    ->first();
    
    if ($tariff) {
        return [
            'type' => 'CLIENT_CONTRACT_PRODUCT_SPECIFIC',
            'tariff' => $tariff,
            'priority' => 1
        ];
    }
    
    // PRIORITY 2: Client Contract Tariff - Product Agnostic (product_id = NULL)
    $tariff = MsClientContractTariff::where('client_id', $clientId)
                                    ->where('route_id', $routeId)
                                    ->where('truck_type_id', $truckTypeId)
                                    ->whereNull('product_id') // ⭐ All products
                                    ->where('service_type', $serviceType)
                                    ->where('is_active', 1)
                                    ->where('effective_date', '<=', now())
                                    ->where(function($q) {
                                        $q->whereNull('expired_date')
                                          ->orWhere('expired_date', '>=', now());
                                    })
                                    ->first();
    
    if ($tariff) {
        return [
            'type' => 'CLIENT_CONTRACT_GENERAL',
            'tariff' => $tariff,
            'priority' => 2
        ];
    }
    
    // PRIORITY 3: Standard Route Tariff (fallback) ⭐ LOWEST
    $product = MsProduct::find($productId);
    $cargoTypeId = $product->cargo_type_id;
    
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
    
    if ($tariff) {
        return [
            'type' => 'STANDARD_TARIFF',
            'tariff' => $tariff,
            'priority' => 3,
            'warning' => 'Using standard rate - no contract found for this client'
        ];
    }
    
    // PRIORITY 4: No tariff found
    return [
        'type' => 'NOT_FOUND',
        'tariff' => null,
        'priority' => null,
        'error' => 'No applicable tariff found. Please create contract or standard tariff.'
    ];
}
```

---

## 5. CONTOH SKENARIO LENGKAP

### Skenario 1: Order dari PT Indofood
```php
// Input
$client = 'CL001'; // PT Indofood
$route = 'R001'; // Cikupa - Bandung
$truck = 'TT004'; // Tronton
$product = 'PRD001'; // Indomie Goreng
$service = 'SHUTTLE';
$weight = 15; // ton
$quantity = 300; // carton

// Process
$tariffResult = getApplicableTariff($client, $route, $truck, $product, $service);

// Output
[
    'type' => 'CLIENT_CONTRACT_PRODUCT_SPECIFIC',
    'tariff' => CCT001,
    'priority' => 1,
    
    'pricing' => [
        'client_rate' => 1200000, // contract rate PT Indofood untuk Mie Goreng
        'discount_percentage' => 15.00, // dapat discount karena >= 40 trips/month
        'effective_rate' => 1020000, // setelah discount
        
        'monthly_volume' => [
            'current_month_trips' => 42, // sudah 42 trips bulan ini
            'qualified_for_discount' => true
        ],
        
        'driver_compensation' => [
            'uang_jalan' => 400000,
            'uang_jasa_base' => 300000,
            'uang_jasa_per_ton' => 375000, // 15 ton x 25000
            'ontime_bonus' => 100000, // jika on-time
            'total' => 1175000
        ],
        
        'cost_breakdown' => [
            'toll' => 55000,
            'fuel' => 280000,
            'parking' => 15000,
            'driver_compensation' => 1175000,
            'loading' => 0, // paid by client
            'total_cost' => 1525000
        ],
        
        'margin' => -505000, // LOSS! (karena contract rate rendah)
        'margin_percentage' => -49.51,
        
        'note' => 'Contract rate untuk volume tinggi (40+ trips/month). Margin negatif tapi kompensasi dengan volume & backhaul opportunity.'
    ]
]

Insight:
- PT Indofood dapat rate Rp 1,020,000 (setelah discount 15%)
- Vendor RUGI Rp 505k per trip jika tidak ada backhaul
- Tapi karena volume 40+ trips/month = total revenue Rp 40,800,000
- Jika backhaul rate 70% = tambahan Rp 28 juta
- NET PROFITABLE dengan volume + backhaul strategy ✅
```

### Skenario 2: Order dari PT Unilever (Same Route, Same Truck)
```php
// Input
$client = 'CL002'; // PT Unilever
$route = 'R001'; // Cikupa - Bandung (SAME ROUTE!)
$truck = 'TT004'; // Tronton (SAME TRUCK!)
$product = 'PRD003'; // Sabun Lifebuoy
$service = 'SHUTTLE';

// Output
[
    'type' => 'CLIENT_CONTRACT_GENERAL',
    'tariff' => CCT003,
    'priority' => 2,
    
    'pricing' => [
        'client_rate' => 1500000, // SPOT RATE (25% lebih mahal!)
        'discount_percentage' => 0, // no discount
        'effective_rate' => 1500000,
        
        'driver_compensation' => 1250000,
        'total_cost' => 1600000,
        
        'margin' => -100000, // masih LOSS tapi lebih kecil
        'margin_percentage' => -6.67,
        
        'note' => 'Spot rate untuk Unilever. Lower volume (5-10 trips/month) tapi higher rate.'
    ]
]

Comparison:
| Aspect           | PT Indofood      | PT Unilever      | Difference |
|------------------|------------------|------------------|------------|
| Rate             | Rp 1,020,000     | Rp 1,500,000     | +47%       |
| Volume/month     | 40+ trips        | 5-10 trips       | -75%       |
| Discount         | 15%              | 0%               | -15%       |
| Payment Term     | TOP 30           | TOP 7            | -23 days   |
| Loading cost     | Client pays      | Vendor pays      | -          |
| Margin per trip  | -Rp 505k         | -Rp 100k         | Better     |
| Monthly revenue  | Rp 40.8jt        | Rp 7.5jt         | -82%       |

Strategy:
- Indofood: Volume play + backhaul critical
- Unilever: Spot rate + better margin per trip
```

### Skenario 3: Order dari CV ABC (Spot Client, No Contract)
```php
// Input
$client = 'CL003'; // CV ABC
$route = 'R001'; // Same route
$truck = 'TT004'; // Same truck
$product = 'PRD001'; // Mie Instan (generic)
$service = 'SHUTTLE';

// Output
[
    'type' => 'CLIENT_CONTRACT_GENERAL',
    'tariff' => CCT004,
    'priority' => 2,
    
    'pricing' => [
        'client_rate' => 1800000, // HIGHEST RATE! (50% above Indofood)
        'effective_rate' => 1800000,
        'payment_term' => 0, // COD (cash on delivery)
        
        'driver_compensation' => 1385000,
        'total_cost' => 1750000,
        
        'margin' => 50000, // PROFIT! ✅
        'margin_percentage' => 2.78,
        
        'note' => 'Spot client, cash payment, immediate profit without backhaul needed.'
    ]
]

Strategy:
- Spot rate paling tinggi
- Instant profit (no need backhaul)
- Cash payment (no credit risk)
- Fill capacity gaps between contract trips
```

---

## 6. QUERY EXAMPLES

### 6.1 Find Client's Active Tariff untuk Product Specific
```sql
SELECT 
    c.client_name,
    r.route_name,
    tt.truck_type_name,
    p.product_name,
    cct.client_rate,
    cct.discount_percentage,
    (cct.client_rate * (100 - cct.discount_percentage) / 100) as effective_rate,
    cct.minimum_monthly_trips,
    cct.payment_term_days
FROM ms_client_contract_tariff cct
JOIN ms_client c ON cct.client_id = c.client_id
JOIN ms_route r ON cct.route_id = r.route_id
JOIN ms_truck_type tt ON cct.truck_type_id = tt.truck_type_id
LEFT JOIN ms_product p ON cct.product_id = p.product_id
WHERE c.client_code = 'CLI-IDF-001'
  AND cct.is_active = 1
  AND cct.effective_date <= GETDATE()
  AND (cct.expired_date IS NULL OR cct.expired_date >= GETDATE())
ORDER BY r.route_name, p.product_name;
```

### 6.2 Compare Pricing: Same Route, Different Clients
```sql
SELECT 
    c.client_name,
    c.contract_type,
    r.route_name,
    tt.truck_type_name,
    ISNULL(p.product_name, 'ALL PRODUCTS') as product,
    cct.client_rate,
    cct.discount_percentage,
    (cct.client_rate * (100 - cct.discount_percentage) / 100) as effective_rate,
    cct.payment_term_days,
    CASE WHEN cct.minimum_monthly_trips > 0 
         THEN CAST(cct.minimum_monthly_trips AS VARCHAR) + ' trips for discount'
         ELSE 'No minimum' 
    END as volume_requirement
FROM ms_client_contract_tariff cct
JOIN ms_client c ON cct.client_id = c.client_id
JOIN ms_route r ON cct.route_id = r.route_id
JOIN ms_truck_type tt ON cct.truck_type_id = tt.truck_type_id
LEFT JOIN ms_product p ON cct.product_id = p.product_id
WHERE r.route_code = 'CKP-BDG-01'
  AND tt.truck_type_code = 'TRONTON'
  AND cct.is_active = 1
ORDER BY effective_rate ASC;

Result:
| Client       | Type     | Product      | Client Rate | Discount | Effective Rate | Payment | Volume Req        |
|--------------|----------|--------------|-------------|----------|----------------|---------|-------------------|
| PT Indofood  | CONTRACT | Mie Goreng   | 1,200,000   | 15%      | 1,020,000      | TOP 30  | 40 trips/month    |
| PT Indofood  | CONTRACT | Mie Kuah     | 1,150,000   | 15%      | 977,500        | TOP 30  | 40 trips/month    |
| PT Unilever  | HYBRID   | ALL PRODUCTS | 1,500,000   | 0%       | 1,500,000      | TOP 7   | No minimum        |
| CV ABC Log   | SPOT     | ALL PRODUCTS | 1,800,000   | 0%       | 1,800,000      | COD     | No minimum        |
```

### 6.3 Route Details dengan Point Information
```sql
SELECT 
    r.route_code,
    r.route_name,
    r.route_type,
    -- Origin Point
    po.point_code as origin_code,
    po.point_name as origin_name,
    po.city as origin_city,
    po.latitude as origin_lat,
    po.longitude as origin_lng,
    po.operational_hours_weekday as origin_hours,
    -- Destination Point
    pd.point_code as dest_code,
    pd.point_name as dest_name,
    pd.city as dest_city,
    pd.latitude as dest_lat,
    pd.longitude as dest_lng,
    pd.operational_hours_weekday as dest_hours,
    -- Route Info
    r.distance_km,
    r.estimated_time_hours,
    r.base_toll_fee,
    r.estimated_fuel_cost,
    r.is_roundtrip,
    r.roundtrip_time_hours
FROM ms_route r
JOIN ms_point po ON r.origin_point_id = po.point_id
JOIN ms_point pd ON r.destination_point_id = pd.point_id
WHERE r.is_active = 1
ORDER BY r.route_code;
```

### 6.4 Client Performance Dashboard
```sql
SELECT 
    c.client_code,
    c.client_name,
    c.contract_type,
    c.total_revenue_ytd,
    c.total_trips_ytd,
    c.current_outstanding,
    c.credit_limit,
    (c.credit_limit - c.current_outstanding) as available_credit,
    c.rating_ontime_payment,
    c.payment_term_days,
    COUNT(DISTINCT cct.route_id) as active_routes,
    COUNT(cct.contract_tariff_id) as active_tariffs,
    MIN(cct.client_rate) as lowest_rate,
    MAX(cct.client_rate) as highest_rate,
    AVG(cct.client_rate) as average_rate
FROM ms_client c
LEFT JOIN ms_client_contract_tariff cct ON c.client_id = cct.client_id AND cct.is_active = 1
WHERE c.is_active = 1
GROUP BY c.client_code, c.client_name, c.contract_type, c.total_revenue_ytd, 
         c.total_trips_ytd, c.current_outstanding, c.credit_limit, 
         c.rating_ontime_payment, c.payment_term_days
ORDER BY c.total_revenue_ytd DESC;
```

---

## 7. MIGRATION SEQUENCE

```sql
-- Step 1: Master Client
CREATE TABLE ms_client (...);
INSERT INTO ms_client VALUES (...); -- seed clients

-- Step 2: Master Point (depends on client for FK)
CREATE TABLE ms_point (...);
INSERT INTO ms_point VALUES (...); -- seed points

-- Step 3: Master Cargo Type
CREATE TABLE ms_cargo_type (...);
INSERT INTO ms_cargo_type VALUES (...);

-- Step 4: Master Product (depends on client & cargo_type)
CREATE TABLE ms_product (...);
INSERT INTO ms_product VALUES (...);

-- Step 5: Master Truck Type
CREATE TABLE ms_truck_type (...);
INSERT INTO ms_truck_type VALUES (...);

-- Step 6: Master Route (depends on point)
CREATE TABLE ms_route (...);
INSERT INTO ms_route VALUES (...);

-- Step 7: Standard Route Tariff (fallback)
CREATE TABLE ms_route_tariff (...);
INSERT INTO ms_route_tariff VALUES (...);

-- Step 8: Client Contract Tariff (depends on client, route, truck, product)
CREATE TABLE ms_client_contract_tariff (...);
INSERT INTO ms_client_contract_tariff VALUES (...);

-- Step 9: Create Views for easy querying
CREATE VIEW v_active_client_tariffs AS
SELECT 
    c.client_name,
    r.route_name,
    tt.truck_type_name,
    ISNULL(p.product_name, 'ALL') as product,
    cct.*
FROM ms_client_contract_tariff cct
JOIN ms_client c ON cct.client_id = c.client_id
JOIN ms_route r ON cct.route_id = r.route_id
JOIN ms_truck_type tt ON cct.truck_type_id = tt.truck_type_id
LEFT JOIN ms_product p ON cct.product_id = p.product_id
WHERE cct.is_active = 1
  AND cct.effective_date <= GETDATE()
  AND (cct.expired_date IS NULL OR cct.expired_date >= GETDATE());
```

---

## 8. SUMMARY & KEY TAKEAWAYS

### ✅ Tabel Master Data LENGKAP (8 tabel utama):
1. **ms_client** - Customer/client dengan contract terms
2. **ms_point** - Location/point (warehouse, factory, DC) dengan GPS, operational hours
3. **ms_product** - Product milik client dengan physical characteristics
4. **ms_cargo_type** - Category barang (general, fragile, dangerous, frozen)
5. **ms_truck_type** - Jenis truck (pickup, engkel, fuso, tronton, trailer, box pendingin)
6. **ms_route** - Route dari Point A ke Point B (menggunakan point_id, bukan hardcoded string)
7. **ms_route_tariff** - Standard/default tariff (fallback jika tidak ada contract)
8. **ms_client_contract_tariff** ⭐ - Client-specific pricing (PRIORITAS UTAMA)

### ✅ Pricing Hierarchy:
1. **Client Contract Tariff - Product Specific** (highest priority)
2. **Client Contract Tariff - Product Agnostic** (medium priority)
3. **Standard Route Tariff** (fallback, lowest priority)

### ✅ Business Logic yang Tercapture:
- ✅ Client berbeda = tariff berbeda (even same route, same truck)
- ✅ Product berbeda = tariff bisa berbeda (Mie Goreng vs Mie Kuah)
- ✅ Volume commitment = discount (40 trips/month = 15% off)
- ✅ Contract vs Spot = price difference (Rp 1.02jt vs Rp 1.8jt)
- ✅ Payment term berbeda per client (TOP 30, 7, atau COD)
- ✅ Location details lengkap (GPS, PIC, jam operasional, facilities)
- ✅ Loading/unloading cost allocation (client pays vs vendor pays)
- ✅ Waiting time policy (detention charge)
- ✅ Performance bonus (on-time, full load)

### ✅ Benefit:
- **Accurate costing** per client-route-truck-product combination
- **Contract management** dengan effective/expired date
- **Volume discount** automatic calculation
- **Client profitability analysis** (revenue vs cost per client)
- **Route optimization** dengan point GPS coordinates
- **Operational planning** dengan point operational hours & facilities

---

**Apakah struktur database ini sudah lengkap dan sesuai dengan realitas bisnis? Ada yang perlu ditambahkan?** 🚀
