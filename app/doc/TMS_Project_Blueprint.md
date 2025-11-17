# TMS (Trucking Management System) - Project Blueprint

**Tanggal:** 17 November 2025  
**Versi:** 1.0  
**Status:** Planning & Design Phase

---

## 1. OVERVIEW APLIKASI

### 1.1 Tujuan Sistem
Sistem manajemen logistik terintegrasi untuk perintah, penjadwalan, monitoring pengiriman, dan settlement dengan dukungan multiple planning modes, real-time GPS tracking, dan mobile driver app.

### 1.2 Jenis Pengiriman yang Didukung

1. **Shuttle (Bolak-Balik):** Pengiriman point-to-point reguler
   - **Mode Weekly Planning:** Client submit Excel setiap Jumat untuk minggu depan (contract client)
   - **Mode Ad-hoc Order:** Client order harian via WA/Email/Phone (spot client)
   - **Mode Hybrid:** Kombinasi planning + ad-hoc (mixed client)
   - Planning mingguan dengan 12 time windows per hari
   - Backhaul management untuk profitabilitas
   
2. **Multi-Point FMCG:** Satu truck mengunjungi banyak titik pengiriman
   - Route optimization & grouping
   - Delivery tracking per point
   - Sequence management

3. **Ad-hoc/Emergency:** Order urgent tanpa planning
   - Quick assignment (first available)
   - Premium pricing
   - Flexible scheduling

### 1.3 Fitur Utama

**Planning & Scheduling:**
- 3 planning modes (Weekly, Ad-hoc, Hybrid) untuk fleksibilitas client
- Weekly planning dengan Excel import dan batch processing
- Ad-hoc order untuk urgent/spot client
- Auto-scheduling engine dengan resource optimization
- 12 time windows per hari (2 jam per window)
- Availability calendar (driver, truck, helper)

**Dispatcher Operations:**
- Real-time adjustment (driver sick, truck rusak, route change)
- Complete audit trail untuk semua perubahan
- Approval workflow (auto-approval vs manager approval)
- Quick reassignment dengan driver suggestion
- Multi-channel notification (app, SMS, WA)

**Driver Communication:**
- Mobile driver app (Android/iOS) sebagai primary channel
- Complete order detail (route map, GPS, contact person)
- Two-way confirmation (confirm/reject dengan reason)
- Digital check-in dengan geofencing & QR scan truck
- Pre-trip inspection checklist
- SMS fallback untuk driver tanpa smartphone

**Cost Management:**
- Client-specific pricing (contract vs spot rate)
- Truck type & cargo type pricing matrix
- Pre-cost calculation otomatis
- 6 cost components: Uang jasa driver, Uang jalan, E-toll, Uang kenek, BBM, Other
- E-toll card integration dengan auto top-up
- Fuel card management dengan variance tracking
- Digital settlement dengan accountability

**Monitoring & Tracking:**
- Dual GPS tracking (Mobile app + GPS device)
- Real-time location monitoring
- Geofencing & milestone tracking
- ETA calculation & update
- Alert system (delay, route deviation, geofence exit)

**Settlement & Finance:**
- Digital uang jalan dengan nota verification
- Driver settlement (uang jasa + bonus - deductions ± variance)
- Helper settlement
- E-toll transaction auto-recorded
- BBM consumption tracking
- Automated payment calculation

### 1.4 Key Differentiators

✅ **Flexible Planning:** Support 3 client types (contract planning, spot ad-hoc, hybrid)
✅ **Real-time Adjustment:** Dispatcher bisa adjust apapun real-time dengan full audit trail
✅ **Mobile Driver App:** Complete driver experience dari assignment sampai settlement
✅ **Dual GPS Tracking:** Mobile app (mandatory) + GPS device (backup/validation)
✅ **Cashless Operations:** E-toll card + fuel card, minimal cash handling
✅ **Client-specific Pricing:** Same route beda client beda harga (contract vs spot)
✅ **Complete Transparency:** Digital record untuk semua transaksi, zero paper trail

---

## 2. MODUL APLIKASI

### 2.1 Master Data Management
**Status:** Partial ✅ | In Progress ⏳ | Not Started ❌

| Modul | Status | Prioritas |
|-------|--------|-----------|
| Master Driver | ✅ Sudah ada | HIGH |
| Master Armada/Truck | ✅ Sudah ada | HIGH |
| Master Route | ❌ Belum | CRITICAL |
| Master Customer | ❌ Belum | CRITICAL |
| Master Lokasi/Point | ❌ Belum | HIGH |
| Master Tarif | ❌ Belum | HIGH |
| Master Time Window | ❌ Belum | CRITICAL |

### 2.2 Planning & Scheduling (Shuttle)
**Status:** Not Started ❌

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Import Excel Planning | ❌ Belum | CRITICAL |
| Weekly Planning Dashboard | ❌ Belum | CRITICAL |
| Auto Scheduling Engine | ❌ Belum | HIGH |
| Manual Assignment Interface | ❌ Belum | HIGH |
| Planning Approval Workflow | ❌ Belum | HIGH |
| Availability Calendar | ❌ Belum | MEDIUM |

### 2.3 Order Management (FMCG & Ad-hoc)
**Status:** Not Started ❌

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Create Order | ❌ Belum | HIGH |
| Order Detail (Multi-point) | ❌ Belum | HIGH |
| Route Grouping Tool | ❌ Belum | MEDIUM |
| Order Approval | ❌ Belum | MEDIUM |

### 2.4 Dispatch & Assignment
**Status:** Template Only ⏳

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Dispatcher Dashboard | ⏳ Template ada | HIGH |
| Generate Dispatch Order | ❌ Belum | HIGH |
| Driver Assignment | ❌ Belum | HIGH |
| Truck Assignment | ❌ Belum | HIGH |
| Dispatch Notification | ❌ Belum | MEDIUM |

### 2.5 Cost Calculation
**Status:** Not Started ❌

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Pre-Cost Calculator | ❌ Belum | HIGH |
| Uang Jalan Calculation | ❌ Belum | HIGH |
| Uang Jasa Calculation | ❌ Belum | HIGH |
| Cost vs Actual Analysis | ❌ Belum | LOW |

### 2.6 Monitoring & Tracking
**Status:** Not Started ❌

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Real-time Dashboard | ❌ Belum | MEDIUM |
| Delivery Status Tracking | ❌ Belum | HIGH |
| GPS Tracking (Optional) | ❌ Belum | LOW |
| Delivery Confirmation | ❌ Belum | HIGH |
| Alert & Notification | ❌ Belum | MEDIUM |

### 2.7 Settlement & Finance
**Status:** Not Started ❌

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Settlement Uang Jalan | ❌ Belum | MEDIUM |
| Settlement Uang Jasa | ❌ Belum | MEDIUM |
| Payment Tracking | ❌ Belum | LOW |
| Financial Report | ❌ Belum | LOW |

### 2.8 Reporting
**Status:** Not Started ❌

| Fitur | Status | Prioritas |
|-------|--------|-----------|
| Delivery Report | ❌ Belum | MEDIUM |
| Driver Performance | ❌ Belum | LOW |
| Truck Utilization | ❌ Belum | LOW |
| Cost Analysis Report | ❌ Belum | LOW |
| Custom Report Builder | ❌ Belum | LOW |

---

## 3. STRUKTUR DATABASE

### 3.1 Tabel yang Sudah Ada ✅
```sql
-- Master Data
ms_tms_driver (driver information from ms_driver)
ms_vehicle (truck/fleet data)
ms_driver (source data for driver)
```

### 3.2 Tabel yang Dibutuhkan ❌

#### A. Master Data
```sql
-- Route Master
CREATE TABLE ms_route (
    route_id VARCHAR(50) PRIMARY KEY,
    route_code VARCHAR(20) UNIQUE,
    route_name VARCHAR(100),
    origin VARCHAR(100),
    destination VARCHAR(100),
    distance_km DECIMAL(10,2),
    estimated_time_hours DECIMAL(5,2),
    is_active BIT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
)

-- Customer Master
CREATE TABLE ms_customer (
    customer_id VARCHAR(50) PRIMARY KEY,
    customer_code VARCHAR(20) UNIQUE,
    customer_name VARCHAR(100),
    address TEXT,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    business_type VARCHAR(50), -- FMCG, Retail, Manufacturing, etc
    is_active BIT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
)

-- Lokasi/Point Master
CREATE TABLE ms_location (
    location_id VARCHAR(50) PRIMARY KEY,
    location_code VARCHAR(20),
    location_name VARCHAR(100),
    address TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    customer_id VARCHAR(50),
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    is_active BIT DEFAULT 1,
    FOREIGN KEY (customer_id) REFERENCES ms_customer(customer_id)
)

-- Tarif Master
CREATE TABLE ms_tariff (
    tariff_id VARCHAR(50) PRIMARY KEY,
    service_type VARCHAR(20), -- SHUTTLE, MULTIPOINT
    route_id VARCHAR(50),
    base_rate DECIMAL(15,2),
    rate_per_km DECIMAL(10,2),
    rate_per_ton DECIMAL(10,2),
    uang_jalan_standard DECIMAL(15,2),
    uang_jasa_standard DECIMAL(15,2),
    effective_date DATE,
    expired_date DATE,
    is_active BIT DEFAULT 1,
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id)
)

-- Time Window Master
CREATE TABLE ms_time_window (
    window_id INT PRIMARY KEY,
    window_name VARCHAR(20), -- Window 1, Window 2, ...
    start_time TIME,
    end_time TIME,
    description VARCHAR(100),
    is_active BIT DEFAULT 1
)

-- Driver Rest Time Policy Master ⭐ NEW
CREATE TABLE ms_driver_rest_policy (
    policy_id VARCHAR(50) PRIMARY KEY,
    policy_name VARCHAR(100),
    policy_description TEXT,
    
    -- REST TIME PARAMETERS (for planning calculation)
    min_rest_hours_between_trips DECIMAL(4,2) DEFAULT 2.0, -- minimal 2 jam istirahat antar trip
    min_rest_hours_after_long_trip DECIMAL(4,2) DEFAULT 4.0, -- minimal 4 jam istirahat setelah trip panjang (>8 jam)
    max_driving_hours_per_day DECIMAL(4,2) DEFAULT 12.0, -- maksimal 12 jam mengemudi per hari
    max_trips_per_day INT DEFAULT 3, -- maksimal 3 trip per hari
    
    -- LONG TRIP THRESHOLD
    long_trip_threshold_hours DECIMAL(4,2) DEFAULT 8.0, -- trip dianggap "panjang" jika >= 8 jam
    long_trip_threshold_km DECIMAL(10,2) DEFAULT 500.0, -- atau >= 500 km
    
    -- OVERTIME RULES
    overtime_threshold_hours DECIMAL(4,2) DEFAULT 10.0, -- lebih dari 10 jam = overtime
    allow_overtime BIT DEFAULT 1, -- boleh overtime atau tidak
    max_overtime_hours_per_week DECIMAL(4,2) DEFAULT 20.0, -- maksimal 20 jam overtime per minggu
    
    -- NIGHT SHIFT RULES
    night_shift_start_time TIME DEFAULT '22:00', -- shift malam mulai jam 22:00
    night_shift_end_time TIME DEFAULT '06:00', -- shift malam sampai jam 06:00
    min_rest_after_night_shift DECIMAL(4,2) DEFAULT 8.0, -- minimal 8 jam istirahat setelah shift malam
    
    -- BACKHAUL RULES
    min_rest_at_pool_before_backhaul DECIMAL(4,2) DEFAULT 1.0, -- minimal 1 jam di pool sebelum backhaul
    allow_backhaul_same_day BIT DEFAULT 1, -- boleh backhaul di hari yang sama
    
    -- MEAL BREAK RULES
    require_meal_break BIT DEFAULT 1, -- wajib istirahat makan
    meal_break_after_hours DECIMAL(4,2) DEFAULT 6.0, -- wajib istirahat setelah 6 jam
    meal_break_duration_minutes INT DEFAULT 30, -- durasi istirahat makan 30 menit
    
    -- APPLICATION SCOPE
    apply_to_all_drivers BIT DEFAULT 1, -- berlaku untuk semua driver atau per driver
    effective_date DATE,
    expired_date DATE,
    is_active BIT DEFAULT 1,
    
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME
)

-- Driver-specific Rest Policy (optional override) ⭐ NEW
CREATE TABLE ms_driver_rest_exception (
    exception_id VARCHAR(50) PRIMARY KEY,
    driver_id VARCHAR(50) NOT NULL,
    policy_id VARCHAR(50), -- NULL = use default policy
    
    -- CUSTOM REST PARAMETERS (override policy)
    custom_min_rest_hours DECIMAL(4,2), -- NULL = use policy default
    custom_max_driving_hours DECIMAL(4,2),
    custom_max_trips_per_day INT,
    
    -- REASON FOR EXCEPTION
    exception_reason TEXT, -- "Senior driver, perlu lebih banyak istirahat"
    approved_by VARCHAR(50),
    approved_at DATETIME,
    
    effective_date DATE,
    expired_date DATE,
    is_active BIT DEFAULT 1,
    
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (policy_id) REFERENCES ms_driver_rest_policy(policy_id)
)

-- Sample Data: Default Rest Policy
INSERT INTO ms_driver_rest_policy VALUES
('POL001', 'Standard Driver Rest Policy', 
'Kebijakan waktu istirahat standar untuk semua driver sesuai peraturan ketenagakerjaan',
2.0, -- min 2 jam antar trip
4.0, -- min 4 jam setelah trip panjang
12.0, -- max 12 jam mengemudi/hari
3, -- max 3 trip/hari
8.0, -- trip panjang >= 8 jam
500.0, -- atau >= 500 km
10.0, -- overtime > 10 jam
1, -- allow overtime
20.0, -- max 20 jam overtime/minggu
'22:00', '06:00', -- night shift 22:00-06:00
8.0, -- min 8 jam rest setelah night shift
1.0, -- min 1 jam di pool sebelum backhaul
1, -- allow backhaul same day
1, -- require meal break
6.0, -- meal break setelah 6 jam
30, -- meal break 30 menit
1, -- apply to all drivers
'2025-01-01', NULL, 1,
'system', GETDATE(), NULL);

-- Sample Data: Senior Driver Exception
INSERT INTO ms_driver_rest_exception VALUES
('EXC001', 'DRV001', 'POL001',
3.0, -- custom: min 3 jam istirahat (lebih lama dari standard 2 jam)
10.0, -- custom: max 10 jam mengemudi (lebih pendek dari standard 12 jam)
2, -- custom: max 2 trip/hari (lebih sedikit dari standard 3 trip)
'Driver senior (usia 55+), perlu waktu istirahat lebih banyak sesuai kondisi kesehatan',
'manager01', '2025-01-15 10:00:00',
'2025-01-01', NULL, 1,
GETDATE(), NULL);
```

#### B. Planning & Scheduling
```sql
-- Weekly Planning (Shuttle)
CREATE TABLE ms_planning_weekly (
    planning_id VARCHAR(50) PRIMARY KEY,
    planning_week VARCHAR(10), -- 2025-W47
    customer_id VARCHAR(50),
    tanggal DATE,
    window_id INT,
    route_id VARCHAR(50),
    origin VARCHAR(100),
    destination VARCHAR(100),
    qty_tonase DECIMAL(10,2),
    notes TEXT,
    status VARCHAR(20), -- Draft, Approved, Dispatched, Completed, Cancelled
    imported_from VARCHAR(100), -- Excel filename
    created_by VARCHAR(50),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (customer_id) REFERENCES ms_customer(customer_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (window_id) REFERENCES ms_time_window(window_id)
)

-- Planning Assignment
CREATE TABLE ms_planning_assignment (
    assignment_id VARCHAR(50) PRIMARY KEY,
    planning_id VARCHAR(50),
    driver_id VARCHAR(50),
    vehicle_id VARCHAR(50),
    dispatch_id VARCHAR(50),
    
    -- PLANNING TIME ESTIMATES
    estimated_departure DATETIME,
    estimated_arrival DATETIME,
    estimated_duration_hours DECIMAL(5,2),
    estimated_driving_hours DECIMAL(5,2), -- exclude loading/unloading
    
    -- REST TIME CALCULATION (PLANNING) ⭐ NEW
    previous_trip_arrival DATETIME, -- kapan trip sebelumnya selesai
    required_rest_hours DECIMAL(4,2), -- berapa jam rest yang diperlukan (from policy)
    earliest_next_departure DATETIME, -- earliest bisa berangkat lagi (arrival + rest)
    rest_compliance BIT DEFAULT 1, -- apakah planning comply dengan rest policy
    rest_violation_reason TEXT, -- jika tidak comply, alasan apa
    
    -- ACTUAL TIME (from execution)
    actual_departure DATETIME,
    actual_arrival DATETIME,
    actual_duration_hours DECIMAL(5,2),
    actual_driving_hours DECIMAL(5,2),
    
    -- REST TIME ACTUAL (EXECUTION) ⭐ NEW
    actual_rest_hours DECIMAL(4,2), -- actual rest yang driver dapat
    actual_pool_arrival DATETIME, -- kapan driver tiba kembali di pool
    actual_next_departure DATETIME, -- kapan driver berangkat lagi untuk trip berikutnya
    actual_rest_compliance BIT, -- apakah actual comply
    actual_rest_violation_reason TEXT,
    
    -- FATIGUE & SAFETY INDICATORS ⭐ NEW
    is_long_trip BIT DEFAULT 0, -- apakah trip ini dianggap long trip
    is_overtime BIT DEFAULT 0, -- apakah trip ini overtime
    is_night_shift BIT DEFAULT 0, -- apakah trip ini night shift
    cumulative_driving_hours_today DECIMAL(5,2), -- total jam mengemudi hari ini
    trip_sequence_today INT, -- trip ke berapa hari ini (1, 2, 3...)
    fatigue_risk_level VARCHAR(20), -- LOW, MEDIUM, HIGH, CRITICAL
    
    -- COST & PAYMENT
    estimated_cost DECIMAL(15,2),
    uang_jalan DECIMAL(15,2),
    uang_jasa DECIMAL(15,2),
    overtime_payment DECIMAL(15,2), -- tambahan bayaran overtime
    night_shift_allowance DECIMAL(15,2), -- tunjangan shift malam
    
    status VARCHAR(20), -- Planned, Assigned, Dispatched, Completed
    assigned_by VARCHAR(50),
    assigned_at DATETIME,
    
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (planning_id) REFERENCES ms_planning_weekly(planning_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id),
    
    INDEX idx_driver_date (driver_id, estimated_departure),
    INDEX idx_rest_compliance (rest_compliance),
    INDEX idx_fatigue_risk (fatigue_risk_level)
)

-- Resource Availability Calendar
CREATE TABLE ms_availability (
    availability_id VARCHAR(50) PRIMARY KEY,
    resource_type VARCHAR(10), -- DRIVER, VEHICLE
    resource_id VARCHAR(50),
    date DATE,
    window_id INT,
    status VARCHAR(20), -- Available, Booked, Maintenance, Off, Leave
    booking_reference VARCHAR(50), -- planning_id atau dispatch_id
    notes TEXT,
    UNIQUE(resource_type, resource_id, date, window_id),
    FOREIGN KEY (window_id) REFERENCES ms_time_window(window_id)
)
```

#### C. Order Management (FMCG)
```sql
-- Order Header
CREATE TABLE ms_order (
    order_id VARCHAR(50) PRIMARY KEY,
    order_number VARCHAR(30) UNIQUE,
    customer_id VARCHAR(50),
    order_date DATE,
    service_type VARCHAR(20), -- SHUTTLE, MULTIPOINT, ADHOC
    total_points INT,
    total_qty DECIMAL(10,2),
    estimated_cost DECIMAL(15,2),
    status VARCHAR(20), -- Draft, Submitted, Approved, Dispatched, Completed
    created_by VARCHAR(50),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (customer_id) REFERENCES ms_customer(customer_id)
)

-- Order Detail (Multi-point)
CREATE TABLE ms_order_detail (
    detail_id VARCHAR(50) PRIMARY KEY,
    order_id VARCHAR(50),
    location_id VARCHAR(50),
    sequence_no INT,
    qty DECIMAL(10,2),
    delivery_notes TEXT,
    delivery_status VARCHAR(20), -- Pending, Delivered, Failed
    delivered_at DATETIME,
    proof_of_delivery VARCHAR(200), -- photo/signature path
    FOREIGN KEY (order_id) REFERENCES ms_order(order_id),
    FOREIGN KEY (location_id) REFERENCES ms_location(location_id)
)

-- Route Grouping (untuk FMCG)
CREATE TABLE ms_route_group (
    group_id VARCHAR(50) PRIMARY KEY,
    group_name VARCHAR(100),
    dispatch_id VARCHAR(50),
    total_points INT,
    total_distance_km DECIMAL(10,2),
    estimated_duration_hours DECIMAL(5,2),
    created_at DATETIME
)

CREATE TABLE ms_route_group_detail (
    detail_id VARCHAR(50) PRIMARY KEY,
    group_id VARCHAR(50),
    order_detail_id VARCHAR(50),
    sequence_no INT,
    FOREIGN KEY (group_id) REFERENCES ms_route_group(group_id),
    FOREIGN KEY (order_detail_id) REFERENCES ms_order_detail(detail_id)
)
```

#### D. Dispatch & Execution
```sql
-- Dispatch Order
CREATE TABLE ms_dispatch (
    dispatch_id VARCHAR(50) PRIMARY KEY,
    dispatch_number VARCHAR(30) UNIQUE,
    dispatch_date DATE,
    source_type VARCHAR(20), -- PLANNING, ORDER, ADHOC
    source_id VARCHAR(50), -- planning_id atau order_id
    driver_id VARCHAR(50),
    vehicle_id VARCHAR(50),
    route_id VARCHAR(50),
    departure_time DATETIME,
    arrival_time DATETIME,
    actual_departure DATETIME,
    actual_arrival DATETIME,
    status VARCHAR(20), -- Scheduled, Departed, In-Transit, Arrived, Completed
    uang_jalan DECIMAL(15,2),
    uang_jasa DECIMAL(15,2),
    actual_cost DECIMAL(15,2),
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id)
)

-- Dispatch Tracking (GPS)
CREATE TABLE ms_dispatch_tracking (
    tracking_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    altitude DECIMAL(8,2),
    speed DECIMAL(6,2), -- km/h
    heading DECIMAL(5,2), -- degrees 0-360
    accuracy DECIMAL(6,2), -- meters
    location_name VARCHAR(100),
    source VARCHAR(20), -- 'MOBILE_APP', 'GPS_DEVICE', 'MANUAL'
    device_id VARCHAR(100), -- identifier GPS device atau mobile
    battery_level INT, -- percentage
    signal_strength INT, -- percentage
    status VARCHAR(50),
    notes TEXT,
    tracked_at DATETIME,
    created_at DATETIME,
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    INDEX idx_dispatch_time (dispatch_id, tracked_at),
    INDEX idx_tracked_at (tracked_at)
)

-- GPS Device Master
CREATE TABLE ms_gps_device (
    device_id VARCHAR(50) PRIMARY KEY,
    device_imei VARCHAR(50) UNIQUE,
    device_type VARCHAR(50), -- brand/model
    vehicle_id VARCHAR(50),
    phone_number VARCHAR(20), -- SIM card number
    installation_date DATE,
    last_maintenance DATE,
    status VARCHAR(20), -- Active, Inactive, Maintenance, Damaged
    notes TEXT,
    is_active BIT DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id)
)

-- Geofence Master (optional)
CREATE TABLE ms_geofence (
    geofence_id VARCHAR(50) PRIMARY KEY,
    geofence_name VARCHAR(100),
    location_id VARCHAR(50),
    center_latitude DECIMAL(10,8),
    center_longitude DECIMAL(11,8),
    radius_meters INT, -- radius lingkaran
    geofence_type VARCHAR(20), -- ORIGIN, DESTINATION, CHECKPOINT, RESTRICTED
    is_active BIT DEFAULT 1,
    FOREIGN KEY (location_id) REFERENCES ms_location(location_id)
)

-- Geofence Events (alert history)
CREATE TABLE ms_geofence_event (
    event_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50),
    geofence_id VARCHAR(50),
    event_type VARCHAR(20), -- ENTER, EXIT
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    event_time DATETIME,
    auto_detected BIT DEFAULT 1,
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (geofence_id) REFERENCES ms_geofence(geofence_id)
)
```

#### E. Settlement & Finance
```sql
-- Settlement Header
CREATE TABLE ms_settlement (
    settlement_id VARCHAR(50) PRIMARY KEY,
    settlement_number VARCHAR(30) UNIQUE,
    settlement_date DATE,
    period_from DATE,
    period_to DATE,
    total_trips INT,
    total_uang_jalan DECIMAL(15,2),
    total_uang_jasa DECIMAL(15,2),
    total_amount DECIMAL(15,2),
    status VARCHAR(20), -- Draft, Approved, Paid
    approved_by VARCHAR(50),
    approved_at DATETIME,
    paid_at DATETIME,
    created_at DATETIME
)

-- Settlement Detail
CREATE TABLE ms_settlement_detail (
    detail_id VARCHAR(50) PRIMARY KEY,
    settlement_id VARCHAR(50),
    dispatch_id VARCHAR(50),
    driver_id VARCHAR(50),
    uang_jalan DECIMAL(15,2),
    uang_jasa DECIMAL(15,2),
    deduction DECIMAL(15,2),
    net_amount DECIMAL(15,2),
    notes TEXT,
    FOREIGN KEY (settlement_id) REFERENCES ms_settlement(settlement_id),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id)
)
```

---

## 4. BUSINESS FLOW PROCESS

### 4.1 Flow Shuttle (Weekly Planning)
```
┌─────────────────────────────────────────────────────────────────┐
│ WEEK N-1: PLANNING PHASE                                        │
└─────────────────────────────────────────────────────────────────┘
1. Client kirim Excel rencana pengiriman week N
   ↓
2. Admin import Excel → ms_planning_weekly (status: Draft)
   ↓
3. System validasi:
   - Route exist?
   - Customer exist?
   - Window valid (1-12)?
   ↓
4. Weekly Planning Dashboard tampilkan:
   - Calendar view (7 hari x 12 windows)
   - Total trips per day/window
   - Kebutuhan truck & driver
   ↓
5. Auto Scheduling Engine:
   - Cari available driver & truck
   - Assign berdasarkan route familiarity
   - Detect conflicts (double booking)
   ↓
6. Dispatcher review & manual adjustment:
   - Reassign driver/truck jika perlu
   - Split trip jika overload
   - Mark external rental if needed
   ↓
7. Manager approve planning
   Status: Draft → Approved
   ↓
8. System generate dispatch orders
   - Create ms_dispatch per trip
   - Update ms_availability (book resource)
   - Calculate uang jalan & jasa
   ↓
9. Export planning:
   - PDF untuk driver (weekly schedule)
   - Excel untuk management review

┌─────────────────────────────────────────────────────────────────┐
│ WEEK N: EXECUTION PHASE                                         │
└─────────────────────────────────────────────────────────────────┘
10. Per hari/window → dispatch driver
    Status: Approved → Dispatched
    ↓
11. Driver confirm departure (actual_departure)
    Status: Dispatched → In-Transit
    ↓
12. Tracking & monitoring real-time
    ↓
13. Driver confirm arrival (actual_arrival)
    ↓
14. Upload proof of delivery
    Status: In-Transit → Completed

┌─────────────────────────────────────────────────────────────────┐
│ WEEK N+1: SETTLEMENT PHASE                                      │
└─────────────────────────────────────────────────────────────────┘
15. Finance create settlement
    - Period: week N
    - Group by driver
    ↓
16. System calculate:
    - Total trips per driver
    - Total uang jalan
    - Total uang jasa
    - Deductions (if any)
    ↓
17. Manager approve settlement
    ↓
18. Finance process payment
    Status: Approved → Paid
```

### 4.2 Flow FMCG (Multi-Point)
```
1. Customer service create order (ms_order)
   ↓
2. Input multiple delivery points (ms_order_detail)
   ↓
3. System suggest route grouping:
   - Geographical clustering
   - Truck capacity validation
   - Time window consideration
   ↓
4. Dispatcher review & finalize grouping
   ↓
5. Pre-cost calculation
   ↓
6. Manager approve order
   ↓
7. Generate dispatch dengan route plan
   ↓
8. Driver execute delivery per sequence
   ↓
9. Confirm delivery per point
   ↓
10. Settlement (same as shuttle)
```

---

## 5. COMPLETE WORKFLOW (UPDATED)

### 5.1 Workflow Mode 1: WEEKLY PLANNING (Contract Client)

```
┌─────────────────────────────────────────────────────────────────┐
│ JUMAT (D-7 to D-3) - CLIENT SUBMIT CO PLANNING                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ CLIENT ACTION:                                                  │
│ 1. Download template Excel dari system (if needed)             │
│ 2. Prepare CO Planning Excel (format Aqua Danone standard)     │
│ 3. Upload CO Planning.xlsx ke TMS via web portal               │
│    Deadline: Jumat 17:00 untuk minggu depan                    │
│                                                                 │
│ EXCEL FORMAT REAL (CO Planning AQUA DANONE): ⭐                 │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ Total Columns: 29 columns                                   ││
│ │                                                             ││
│ │ CRITICAL COLUMNS (must map to TMS):                         ││
│ │ ├─ PK: AQUA.S25111400788 (unique identifier)               ││
│ │ ├─ Shipment ID: S25111400788 (shipment number)             ││
│ │ ├─ SO/STO No: 4505414745 (sales order SAP)                 ││
│ │ ├─ Truck Id: HGS/B9873KYX (pre-assigned or blank)          ││
│ │ ├─ Driver Name: DEDI KUSNADI (pre-assigned or blank)       ││
│ │ ├─ Total Item Package Count: 2900 (carton qty)             ││
│ │ ├─ Pick Up Window: "2" (string, not integer!) ⭐           ││
│ │ ├─ Pickup Start Date: 18/11/2025 02:00 Asia/Jakarta        ││
│ │ ├─ Pickup End Date: 18/11/2025 04:00 Asia/Jakarta          ││
│ │ ├─ Source Location Name: 9000 ID CIHERANG PLANT TIV        ││
│ │ ├─ Destination Location Name: 9000 ID PALAPA DC TIV        ││
│ │ ├─ Source Location ID: 9018 (plant code)                   ││
│ │ ├─ Destination Location ID: 9025 (DC code)                 ││
│ │ ├─ First Equipment Group ID: TNWB_JUGRACK_GREEN-TIV        ││
│ │ ├─ Order Type: STANDARD                                    ││
│ │ ├─ Movement Type: FACTORY TO DEPO                          ││
│ │ └─ Status: SECURE RESOURCES_ACCEPTED                       ││
│ │                                                             ││
│ │ SAMPLE ROW (from real CO Planning):                         ││
│ │ PK: AQUA.S25111400788                                       ││
│ │ Shipment: S25111400788                                      ││
│ │ SO: 4505414745                                              ││
│ │ Qty: 2900 cartons                                           ││
│ │ Window: "2" → Map to ms_time_window.window_number = 2       ││
│ │ Start: 18/11/2025 02:00 → Parse to datetime                 ││
│ │ End: 18/11/2025 04:00                                       ││
│ │ Origin: "9000 ID CIHERANG PLANT TIV" → Extract "9018"       ││
│ │ Dest: "9000 ID PALAPA DC TIV" → Extract "9025"              ││
│ │ Route: Auto-detect from origin+dest → ROU001               ││
│ │ Truck type: "TNWB_JUGRACK_GREEN-TIV" → Map to TRK001        ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ TMS SYSTEM IMPORT PROCESS:                                      │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ STEP 1: Upload & Parse Excel                                ││
│ │ ├─ Read all rows (skip header)                              ││
│ │ ├─ Detect columns by name matching                          ││
│ │ └─ Store in temporary staging table                         ││
│ │                                                             ││
│ │ STEP 2: Data Mapping & Transformation                       ││
│ │ ├─ Parse date: "18/11/2025 02:00 Asia/Jakarta"             ││
│ │ │   → Convert to: 2025-11-18 02:00:00                      ││
│ │ ├─ Extract origin code: "9000 ID CIHERANG PLANT TIV"       ││
│ │ │   → Lookup ms_location: code = "9018" or name LIKE "%..."││
│ │ ├─ Extract destination code: "9000 ID PALAPA DC TIV"       ││
│ │ │   → Lookup ms_location: code = "9025"                    ││
│ │ ├─ Detect route: origin_id + destination_id                ││
│ │ │   → Lookup ms_route: WHERE origin = 9018 AND dest = 9025 ││
│ │ │   → Result: route_id = ROU001                            ││
│ │ ├─ Map time window: "2" (string)                           ││
│ │ │   → Lookup ms_time_window: WHERE window_number = 2       ││
│ │ │   → Result: window_id = WIN002 (02:00-04:00)             ││
│ │ ├─ Map truck type: "TNWB_JUGRACK_GREEN-TIV"                ││
│ │ │   → Lookup ms_truck_type: WHERE code LIKE "%JUGRACK%"    ││
│ │ │   → Result: truck_type_id = TRK001                       ││
│ │ └─ Detect pre-assignment:                                  ││
│ │     IF Truck Id NOT blank → Mark pre_assigned_truck        ││
│ │     IF Driver Name NOT blank → Mark pre_assigned_driver    ││
│ │                                                             ││
│ │ STEP 3: Validation Rules                                    ││
│ │ ├─ ❌ Route not found in ms_route → ERROR                  ││
│ │ ├─ ❌ Time window invalid (13-24) → ERROR                  ││
│ │ ├─ ❌ Date in past → ERROR                                 ││
│ │ ├─ ❌ Qty > truck capacity → WARNING                       ││
│ │ ├─ ⚠️ Truck pre-assigned but not in fleet → WARNING       ││
│ │ └─ ⚠️ Driver name not found → WARNING (create new?)        ││
│ │                                                             ││
│ │ STEP 4: Insert to ms_planning_weekly                        ││
│ │ INSERT INTO ms_planning_weekly (                            ││
│ │   planning_id, client_id, route_id, window_id,             ││
│ │   shipment_date, shipment_ref_no, so_number,               ││
│ │   qty_cartons, truck_type_id,                              ││
│ │   pre_assigned_truck_id, pre_assigned_driver_id,           ││
│ │   status, imported_at                                      ││
│ │ ) VALUES (                                                  ││
│ │   'PLN001', 'CUS001', 'ROU001', 'WIN002',                  ││
│ │   '2025-11-18', 'S25111400788', '4505414745',              ││
│ │   2900, 'TRK001',                                          ││
│ │   'VEH010', 'DRV005',  -- if pre-assigned                  ││
│ │   'IMPORTED', NOW()                                        ││
│ │ )                                                           ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ EXAMPLE IMPORT SESSION (Jumat 14 Nov 2025, 16:30):             │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ File: CO_Planning_Week47_2025.xlsx                          ││
│ │ Uploaded by: PT Aqua Danone (CUS001)                        ││
│ │                                                             ││
│ │ Import Summary:                                             ││
│ │ ├─ Total rows: 412 shipments                                ││
│ │ ├─ Valid: 405 rows (98.3%)                                  ││
│ │ ├─ Errors: 3 rows (route not found)                         ││
│ │ ├─ Warnings: 4 rows (driver name typo)                      ││
│ │ │                                                           ││
│ │ ├─ Grouped by route+window: 145 unique trips                ││
│ │ │   (multiple shipments per trip)                          ││
│ │ │                                                           ││
│ │ ├─ Routes detected:                                         ││
│ │ │   * Ciherang → Palapa: 35 trips (Window 1-8)             ││
│ │ │   * Mekarsari → Kawasan: 48 trips (Window 1-12)          ││
│ │ │   * Mekarsari → Bandung: 12 trips (Window 10-11)         ││
│ │ │   * Sentul → Ciputat: 28 trips (Window 1-9)              ││
│ │ │   * Citeureup → Cimanggis: 15 trips (Window 1-8)         ││
│ │ │   * Caringin → Cibinong: 7 trips (Window 1-8)            ││
│ │ │                                                           ││
│ │ ├─ Pre-assigned resources:                                  ││
│ │ │   * 12 trips with truck pre-assigned (8%)                ││
│ │ │   * 12 trips with driver pre-assigned (8%)               ││
│ │ │   * 133 trips need auto-scheduling (92%)                 ││
│ │ │                                                           ││
│ │ └─ Status: IMPORTED, ready for auto-scheduling              ││
│ │                                                             ││
│ │ Validation Errors (3 rows):                                 ││
│ │ Row 45: Origin "ID SUKABUMI PLANT" not found in master      ││
│ │ Row 128: Window "15" invalid (max 12)                       ││
│ │ Row 302: Date "15/11/2025" is in the past                   ││
│ │                                                             ││
│ │ Warnings (4 rows):                                          ││
│ │ Row 67: Driver "DEDI KUSNADI" → Found "DEDI KUSNANDI"       ││
│ │          (name similarity 95%, auto-correct?)              ││
│ │ Row 89: Truck "HGS/B9999XXX" not in fleet (new truck?)      ││
│ │                                                             ││
│ │ [FIX ERRORS] [APPROVE IMPORT] [CANCEL]                      ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ Dispatcher: Click [APPROVE IMPORT]                             │
│                                                                 │
│ System:                                                         │
│ - Insert 405 valid rows to ms_planning_weekly                  │
│ - Log 3 errors, 4 warnings to import_log                       │
│ - Status: IMPORTED                                             │
│ - Send email notification to Supervisor:                       │
│   "CO Planning Week 47 imported: 145 trips, 92% need assign"   │
│                                                                 │
│ Database state after import:                                    │
│ ms_planning_weekly: 145 records, status = 'IMPORTED'           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SABTU (D-2) - DISPATCHER HYBRID ASSIGNMENT                     │
│ (Auto-Scheduling + Manual Review & Adjustment)                 │
├─────────────────────────────────────────────────────────────────┤
│ PHASE 1: AUTO-SCHEDULING ENGINE                                │
│                                                                 │
│ Dispatcher: Click [RUN AUTO-SCHEDULING] button                 │
│                                                                 │
│ System: Execute auto-assignment algorithm (5-10 menit)          │
│                                                                 │
│ Step 1: Load planning data                                     │
│ ├─ 19 trips dari 3 clients untuk week 18-24 Nov                │
│ ├─ Group by date & time window                                 │
│ └─ Sort by priority (contract client > spot client)            │
│                                                                 │
│ Step 2: For each trip, find resources                          │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ Trip: PT Indofood, 18 Nov, Window 4, Cikupa-Bandung         ││
│ │                                                             ││
│ │ A. Find Available Drivers:                                  ││
│ │    Query ms_availability:                                   ││
│ │    WHERE resource_type = 'DRIVER'                           ││
│ │      AND date = '2025-11-18'                                ││
│ │      AND window_id = 4                                      ││
│ │      AND status = 'AVAILABLE'                               ││
│ │                                                             ││
│ │    Result: 8 drivers available                              ││
│ │    ├─ DRV001 (Budi) - Route familiarity: EXPERT (50 trips) ││
│ │    ├─ DRV002 (Joko) - Route familiarity: EXPERT (45 trips) ││
│ │    ├─ DRV003 (Siti) - Route familiarity: COMPETENT (20×)   ││
│ │    └─ ... (5 others)                                        ││
│ │                                                             ││
│ │ B. Score & Rank Drivers:                                    ││
│ │    Scoring formula:                                         ││
│ │    Score = (Familiarity × 40%) + (Rotation × 30%) +        ││
│ │            (Rating × 20%) + (Last trip days × 10%)         ││
│ │                                                             ││
│ │    ├─ Budi: 95 pts (familiarity 100, rotation 90, ...)     ││
│ │    ├─ Joko: 92 pts (familiarity 100, rotation 85, ...)     ││
│ │    └─ Siti: 78 pts (familiarity 70, rotation 95, ...)      ││
│ │                                                             ││
│ │ B2. Check Rest Time Compliance ⭐ NEW:                      ││
│ │    Get driver rest policy:                                  ││
│ │    - Budi: Has exception (POL001 + EXC001)                  ││
│ │      * Min rest: 3 hours (custom, not 2 hours)              ││
│ │      * Max driving: 10 hours/day (custom, not 12)           ││
│ │      * Max trips: 2/day (custom, not 3)                     ││
│ │    - Joko: Standard policy (POL001)                         ││
│ │      * Min rest: 2 hours                                    ││
│ │      * Max driving: 12 hours/day                            ││
│ │      * Max trips: 3/day                                     ││
│ │                                                             ││
│ │    Check Budi's schedule for 18 Nov:                        ││
│ │    ┌─────────────────────────────────────────────────────┐ ││
│ │    │ Previous trip (17 Nov):                             │ ││
│ │    │ - Route: Jakarta → Surabaya                         │ ││
│ │    │ - Departure: 17 Nov 06:00                           │ ││
│ │    │ - Arrival at pool: 17 Nov 18:30 ⭐                  │ ││
│ │    │ - Trip duration: 12.5 hours (long trip!)            │ ││
│ │    │                                                     │ ││
│ │    │ New trip (18 Nov):                                  │ ││
│ │    │ - Planned departure: 18 Nov 06:00                   │ ││
│ │    │                                                     │ ││
│ │    │ REST TIME CALCULATION:                              │ ││
│ │    │ ├─ Pool arrival: 17 Nov 18:30                       │ ││
│ │    │ ├─ Next departure: 18 Nov 06:00                     │ ││
│ │    │ ├─ Actual rest: 11.5 hours ✅                       │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Required rest (check policy):                    │ ││
│ │    │ │  * Previous trip = 12.5h > 8h threshold           │ ││
│ │    │ │  * = LONG TRIP                                    │ ││
│ │    │ │  * Required: 4 hours (after long trip)            │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Budi custom exception:                           │ ││
│ │    │ │  * Wait! Budi has custom min rest = 3h           │ ││
│ │    │ │  * For long trip: Apply max(3h, 4h) = 4h         │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Compliance check:                                │ ││
│ │    │ │  * Actual rest: 11.5h                            │ ││
│ │    │ │  * Required: 4h                                   │ ││
│ │    │ │  * Compliance: 11.5h > 4h = ✅ OK!               │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Driving hours check (cumulative today):          │ ││
│ │    │ │  * Current trip: 0h (belum jalan)                │ ││
│ │    │ │  * New trip estimated: 2.5h (Cikupa-Bdg)         │ ││
│ │    │ │  * Total today: 2.5h                             │ ││
│ │    │ │  * Max allowed (Budi custom): 10h                │ ││
│ │    │ │  * Compliance: 2.5h < 10h = ✅ OK!               │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Trip count check:                                │ ││
│ │    │ │  * Trips today: 0 (belum ada)                    │ ││
│ │    │ │  * New trip: 1                                    │ ││
│ │    │ │  * Total: 1 trip                                  │ ││
│ │    │ │  * Max allowed (Budi custom): 2 trips/day        │ ││
│ │    │ │  * Compliance: 1 < 2 = ✅ OK!                    │ ││
│ │    │ │                                                   │ ││
│ │    │ └─ RESULT: Budi ELIGIBLE ✅                         │ ││
│ │    │    Can be assigned to this trip                     │ ││
│ │    └─────────────────────────────────────────────────────┘ ││
│ │                                                             ││
│ │    Check Joko's schedule for 18 Nov:                        ││
│ │    ┌─────────────────────────────────────────────────────┐ ││
│ │    │ Previous trip (17 Nov):                             │ ││
│ │    │ - Route: Bandung → Cikupa                           │ ││
│ │    │ - Pool arrival: 17 Nov 16:00 ⭐                     │ ││
│ │    │ - Trip duration: 3 hours (short trip)               │ ││
│ │    │                                                     │ ││
│ │    │ Already assigned today (18 Nov):                    │ ││
│ │    │ - Trip 1: Cikupa-Bandung (06:00-09:00, 3h)         │ ││
│ │    │ - Trip 2: Already assigned Window 8 (14:00)        │ ││
│ │    │ - Trips today: 2                                    │ ││
│ │    │                                                     │ ││
│ │    │ New trip request:                                   │ ││
│ │    │ - Would be trip #3 today                           │ ││
│ │    │                                                     │ ││
│ │    │ REST TIME CALCULATION:                              │ ││
│ │    │ ├─ Rest from yesterday: 14h (16:00→06:00) ✅       │ ││
│ │    │ ├─ Required: 2h (standard, short trip) ✅           │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Cumulative driving today:                        │ ││
│ │    │ │  * Trip 1: 3h                                    │ ││
│ │    │ │  * Trip 2: 3h                                    │ ││
│ │    │ │  * New trip: 2.5h                                │ ││
│ │    │ │  * Total: 8.5h                                   │ ││
│ │    │ │  * Max: 12h (standard)                           │ ││
│ │    │ │  * Compliance: 8.5h < 12h = ✅ OK                │ ││
│ │    │ │                                                   │ ││
│ │    │ ├─ Trip count:                                      │ ││
│ │    │ │  * Current: 2 trips                              │ ││
│ │    │ │  * New: would be 3 trips                         │ ││
│ │    │ │  * Max: 3 trips/day (standard)                   │ ││
│ │    │ │  * Compliance: 3 = 3 = ✅ OK (at limit!)         │ ││
│ │    │ │                                                   │ ││
│ │    │ └─ RESULT: Joko ELIGIBLE ✅ but AT LIMIT           │ ││
│ │    │    Can assign but flag as "high workload"          │ ││
│ │    └─────────────────────────────────────────────────────┘ ││
│ │                                                             ││
│ │    Final scoring with rest time factor:                     ││
│ │    ├─ Budi: 95 pts × 1.0 (good rest) = 95 pts ⭐ BEST     ││
│ │    ├─ Joko: 92 pts × 0.8 (at limit) = 73.6 pts            ││
│ │    └─ Siti: 78 pts × 1.0 (fresh) = 78 pts                  ││
│ │                                                             ││
│ │    Decision: Assign Budi (best score + good rest status)   ││
│ │                                                             ││
│ │ C. Find Available Trucks:                                   ││
│ │    Query ms_availability + ms_vehicle:                      ││
│ │    WHERE resource_type = 'VEHICLE'                          ││
│ │      AND date = '2025-11-18'                                ││
│ │      AND window_id = 4                                      ││
│ │      AND status = 'AVAILABLE'                               ││
│ │      AND truck_type = 'TRONTON' (from route requirement)   ││
│ │      AND capacity >= 15 ton                                 ││
│ │                                                             ││
│ │    Result: 5 trucks available                               ││
│ │    ├─ VH001 (B-1234-AB) - Fuel efficiency: 4.5 km/l        ││
│ │    ├─ VH002 (B-5678-CD) - Fuel efficiency: 4.0 km/l        ││
│ │    └─ ... (3 others)                                        ││
│ │                                                             ││
│ │ D. Match Driver + Truck:                                    ││
│ │    Preference: Driver yang familiar dengan truck tertentu   ││
│ │    ├─ Budi + VH001 (often paired, good combo)              ││
│ │    └─ Score: 98 pts (best match!)                           ││
│ │                                                             ││
│ │ E. Find Available Helper:                                   ││
│ │    Query ms_availability:                                   ││
│ │    WHERE resource_type = 'HELPER'                           ││
│ │      AND date = '2025-11-18'                                ││
│ │      AND window_id = 4                                      ││
│ │      AND status = 'AVAILABLE'                               ││
│ │                                                             ││
│ │    Result: Andi (HLP001) - Available, good rating           ││
│ │                                                             ││
│ │ F. Check Conflicts:                                         ││
│ │    ├─ Double booking: None ✅                               ││
│ │    ├─ Maintenance schedule: None ✅                         ││
│ │    └─ Leave/off days: None ✅                               ││
│ │                                                             ││
│ │ G. Assign & Lock:                                           ││
│ │    ├─ Planning ID: PL-2025-11-18-001                        ││
│ │    ├─ Driver: Budi (DRV001) ✅                              ││
│ │    ├─ Truck: B-1234-AB (VH001) ✅                           ││
│ │    ├─ Helper: Andi (HLP001) ✅                              ││
│ │    └─ Assignment method: AUTO                               ││
│ │                                                             ││
│ │    Update ms_availability:                                  ││
│ │    - Mark Budi as BOOKED for 18 Nov, Window 4               ││
│ │    - Mark VH001 as BOOKED for 18 Nov, Window 4              ││
│ │    - Mark Andi as BOOKED for 18 Nov, Window 4               ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ Step 3: Repeat for all 19 trips                                │
│ ├─ Trip 1: ✅ Assigned (Budi + VH001)                          │
│ ├─ Trip 2: ✅ Assigned (Joko + VH002)                          │
│ ├─ Trip 3: ✅ Assigned (Siti + VH003)                          │
│ ├─ ...                                                         │
│ ├─ Trip 18: ✅ Assigned (Auto)                                 │
│ └─ Trip 19: ⚠️ CONFLICT! (No available driver in Window 5)     │
│                                                                 │
│ Step 4: Detect backhaul opportunities                          │
│ ├─ Trip 1: Cikupa→Bandung (06:00) + Trip 15: Bandung→Cikupa   │
│ │          (14:00) = Same driver Budi? ✅ MATCH!               │
│ ├─ Trip 3: Jakarta→Surabaya + No return = ❌ No backhaul      │
│ └─ Total backhaul matched: 6 pairs                             │
│                                                                 │
│ Auto-Scheduling Result:                                        │
│ ├─ Successfully assigned: 18 trips (95%)                       │
│ ├─ Conflicts detected: 1 trip (5%)                             │
│ ├─ Backhaul matched: 6 pairs (optimize profit)                 │
│ └─ Status: AUTO_ASSIGNED (ready for manual review)             │
│                                                                 │
│ ═══════════════════════════════════════════════════════════════ │
│                                                                 │
│ PHASE 2: DISPATCHER MANUAL REVIEW & ADJUSTMENT                 │
│                                                                 │
│ Dispatcher UI: Weekly Calendar View                            │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │        Mon 18  Tue 19  Wed 20  Thu 21  Fri 22  Sat 23       ││
│ │ W4     ■■■■    ■■      ■       ■■      ■       -            ││
│ │ 06:00  3 trips 2 trips 1 trip  2 trips 1 trip               ││
│ │                                                             ││
│ │ W5     ■■      ⚠️■     ■■      ■       ■■      -            ││
│ │ 08:00  2 trips CONFLICT 2 trips 1 trip  2 trips             ││
│ │                                                             ││
│ │ W6     ■       ■       -       ■       -       -            ││
│ │ 10:00  1 trip  1 trip          1 trip                       ││
│ │                                                             ││
│ │ Legend: ■ = Auto-assigned ✅   ⚠️ = Conflict/Manual needed  ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ 1. Review Auto-Assignments:                                    │
│    Dispatcher clicks on each trip to review:                   │
│    ┌─────────────────────────────────────────────────────────┐│
│    │ Trip: PT Indofood - Cikupa→Bandung                      ││
│    │ Date: Mon 18 Nov, Window 4 (06:00-08:00)                ││
│    │                                                         ││
│    │ AUTO-ASSIGNED: ✅                                       ││
│    │ ├─ Driver: Budi (DRV001) - Score 95                    ││
│    │ ├─ Truck: B-1234-AB (VH001) - Match 98%                ││
│    │ ├─ Helper: Andi (HLP001)                               ││
│    │ └─ Backhaul: Yes (return 14:00)                        ││
│    │                                                         ││
│    │ Cost Calculation:                                       ││
│    │ ├─ Uang jasa: Rp 200,000                               ││
│    │ ├─ Uang jalan: Rp 85,000                               ││
│    │ ├─ E-toll: Rp 65,000                                   ││
│    │ ├─ BBM: Rp 195,000 (120km ÷ 4.5km/l × Rp 6,500)       ││
│    │ ├─ Helper: Rp 75,000                                   ││
│    │ └─ Total: Rp 620,000                                   ││
│    │                                                         ││
│    │ Revenue: Rp 1,200,000 (contract rate)                  ││
│    │ Margin: Rp 580,000 (48.3%) ✅ GOOD                     ││
│    │                                                         ││
│    │ [APPROVE AS-IS] [ADJUST] [REASSIGN]                    ││
│    └─────────────────────────────────────────────────────────┘│
│                                                                 │
│    Dispatcher: Click [APPROVE AS-IS] (accept auto-assignment)  │
│                                                                 │
│ 2. Resolve Conflicts (Manual Assignment):                      │
│    Trip 19: ⚠️ No driver available in Window 5                 │
│                                                                 │
│    Dispatcher options:                                         │
│    A. Adjust time window: Window 5 → Window 6 (if client OK)   │
│    B. Find driver from other window (swap assignments)         │
│    C. Request overtime (assign driver with 2 trips same day)   │
│                                                                 │
│    Dispatcher: Drag driver "Tono" from Window 6 → Window 5     │
│    System: Check conflict → OK, Tono available ✅               │
│    Manual override: Assign Tono + VH007 to Trip 19             │
│                                                                 │
│ 3. Optimize Backhaul Matching:                                 │
│    Dispatcher: Review backhaul suggestions                     │
│    ├─ Trip 1 (Budi, 06:00 Cikupa→Bdg) + Trip 15 (14:00 return)│
│    │  System suggest: Match ✅ (same driver, good timing)      │
│    │  Dispatcher: Approve match                                │
│    │                                                           │
│    ├─ Trip 5 (Joko, 08:00 Jkt→Sby) + No return order          │
│    │  System: No backhaul available ❌                         │
│    │  Dispatcher: Check ad-hoc orders, find return cargo       │
│    │  Manual: Create ad-hoc order for return trip              │
│    └─ Manual backhaul: +Rp 1,200k revenue (convert loss!)      │
│                                                                 │
│ 4. Manual Adjustments:                                         │
│    ├─ Swap driver preference (Siti request specific route)     │
│    ├─ Change truck (maintenance schedule conflict)             │
│    ├─ Add special instructions per trip                        │
│    └─ Flag VIP client trips (priority handling)                │
│                                                                 │
│ 5. Final Validation:                                           │
│    System: Run validation check                                │
│    ├─ ✅ No double booking                                     │
│    ├─ ✅ All trips assigned                                    │
│    ├─ ✅ All conflicts resolved                                │
│    ├─ ✅ Truck capacity validated                              │
│    ├─ ✅ Driver license validated                              │
│    └─ ✅ Cost calculation complete                             │
│                                                                 │
│ 6. Dispatcher: Click [SUBMIT FOR APPROVAL]                     │
│                                                                 │
│ System:                                                         │
│ - Status: IMPORTED → ASSIGNED                                  │
│ - Lock planning for supervisor review                          │
│ - Generate assignment summary report                           │
│ - Notify supervisor: "Planning ready for approval"             │
│                                                                 │
│ Assignment Summary:                                            │
│ ├─ Total trips: 19                                             │
│ ├─ Auto-assigned: 18 (95%)                                     │
│ ├─ Manual-assigned: 1 (5%)                                     │
│ ├─ Drivers used: 12 drivers                                    │
│ ├─ Trucks used: 12 trucks                                      │
│ ├─ Backhaul matched: 7 pairs (optimization +37%)               │
│ ├─ Total cost: Rp 11,780,000                                   │
│ ├─ Total revenue: Rp 22,800,000                                │
│ └─ Overall margin: 48.3% ✅                                     │
│                                                                 │
│ Time spent: Auto (10 min) + Manual review (45 min) = 55 min    │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ MINGGU/SENIN - SUPERVISOR REVIEW & APPROVE                     │
├─────────────────────────────────────────────────────────────────┤
│ Supervisor: Login & view pending approval                      │
│                                                                 │
│ Dashboard shows:                                               │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ 📋 WEEKLY PLANNING APPROVAL REQUEST                         ││
│ │                                                             ││
│ │ Week: 18-24 November 2025 (Week 47)                         ││
│ │ Submitted by: Dispatcher01 (Sabtu, 16 Nov, 14:30)           ││
│ │ Status: PENDING_APPROVAL ⏳                                 ││
│ │                                                             ││
│ │ SUMMARY:                                                    ││
│ │ ├─ Total Clients: 3 (Indofood, Unilever, Wings)            ││
│ │ ├─ Total Trips: 19 trips                                   ││
│ │ ├─ Assignment Method:                                       ││
│ │ │   * Auto-assigned: 18 trips (95%) ✅                     ││
│ │ │   * Manual-assigned: 1 trip (5%) ⚠️                      ││
│ │ ├─ Resource Utilization:                                    ││
│ │ │   * Drivers: 12/20 (60%)                                 ││
│ │ │   * Trucks: 12/18 (67%)                                  ││
│ │ │   * Helpers: 12/15 (80%)                                 ││
│ │ ├─ Backhaul Optimization:                                   ││
│ │ │   * Backhaul matched: 7 pairs (37% optimization)         ││
│ │ │   * Empty return: 5 trips                                ││
│ │ └─ Financial Summary:                                       ││
│ │     * Total Cost: Rp 11,780,000                            ││
│ │     * Total Revenue: Rp 22,800,000                         ││
│ │     * Overall Margin: Rp 11,020,000 (48.3%) ✅            ││
│ │                                                             ││
│ │ [VIEW DETAILS] [APPROVE ALL] [REJECT] [REQUEST REVISION]   ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ 1. Supervisor: Click [VIEW DETAILS]                            │
│                                                                 │
│    Detail View - Calendar with assignments:                    │
│    ┌─────────────────────────────────────────────────────────┐│
│    │ MONDAY 18 NOVEMBER 2025                                 ││
│    │                                                         ││
│    │ Window 4 (06:00-08:00): 3 trips                         ││
│    │ ├─ Trip 1: PT Indofood, Cikupa→Bdg                     ││
│    │ │  Driver: Budi (AUTO ✅) | Truck: B-1234-AB            ││
│    │ │  Cost: 620k | Revenue: 1,200k | Margin: 48%          ││
│    │ │  Backhaul: Yes (return 14:00) ✅                      ││
│    │ │                                                       ││
│    │ ├─ Trip 2: PT Unilever, Cikupa→Bdg                     ││
│    │ │  Driver: Joko (AUTO ✅) | Truck: B-5678-CD            ││
│    │ │  Cost: 615k | Revenue: 1,500k | Margin: 59%          ││
│    │ │  Backhaul: No ❌                                      ││
│    │ │                                                       ││
│    │ └─ Trip 3: PT Wings, Jkt→Sby                           ││
│    │    Driver: Siti (AUTO ✅) | Truck: B-9999-XY            ││
│    │    Cost: 1,250k | Revenue: 3,000k | Margin: 58%        ││
│    │    Backhaul: No ❌                                      ││
│    │                                                         ││
│    │ Window 5 (08:00-10:00): 2 trips                         ││
│    │ ├─ Trip 4: PT Indofood, Cikupa→Tasik                   ││
│    │ │  Driver: Agus (AUTO ✅) | Truck: B-1111-AA            ││
│    │ │  Cost: 880k | Revenue: 1,800k | Margin: 51%          ││
│    │ │                                                       ││
│    │ └─ Trip 5: PT Wings, Bdg→Garut                         ││
│    │    Driver: Tono (MANUAL ⚠️) | Truck: B-2222-BB         ││
│    │    Cost: 420k | Revenue: 900k | Margin: 53%            ││
│    │    Note: Manual override - conflict resolved            ││
│    │    [VIEW ADJUSTMENT LOG]                                ││
│    └─────────────────────────────────────────────────────────┘│
│                                                                 │
│ 2. Supervisor: Review manual assignments (red flag items)      │
│    Click [VIEW ADJUSTMENT LOG] on Trip 5:                      │
│    ┌─────────────────────────────────────────────────────────┐│
│    │ ADJUSTMENT LOG - Trip 5                                 ││
│    │                                                         ││
│    │ Original Auto-Assignment:                               ││
│    │ - Driver: NOT ASSIGNED (no available driver)            ││
│    │ - Reason: All drivers in Window 5 already booked        ││
│    │                                                         ││
│    │ Manual Override by Dispatcher01:                        ││
│    │ - Action: Moved driver "Tono" from Window 6 to Window 5 ││
│    │ - Timestamp: Sabtu 16 Nov, 13:45                        ││
│    │ - Reason: "Conflict resolved - Tono available after     ││
│    │           adjusting his original window 6 assignment    ││
│    │           to window 7 (client approved time change)"    ││
│    │ - Impact: No cost change, margin maintained 53%         ││
│    │                                                         ││
│    │ Validation:                                             ││
│    │ ✅ Driver available (no double booking)                 ││
│    │ ✅ Truck available                                      ││
│    │ ✅ Route license match (SIM B2)                         ││
│    │ ✅ Client confirmed time window change                  ││
│    │ ✅ Margin acceptable (>40%)                             ││
│    └─────────────────────────────────────────────────────────┘│
│                                                                 │
│    Supervisor: OK, manual adjustment reasonable ✅              │
│                                                                 │
│ 3. Supervisor: Review backhaul optimization                    │
│    ┌─────────────────────────────────────────────────────────┐│
│    │ BACKHAUL MATCHING REPORT                                ││
│    │                                                         ││
│    │ Matched Pairs (7):                                      ││
│    │ ├─ Budi: Cikupa→Bdg (06:00) + Bdg→Cikupa (14:00)       ││
│    │ │  Revenue: Rp 1,200k + Rp 1,200k = Rp 2,400k           ││
│    │ │  Cost: Rp 620k + Rp 450k = Rp 1,070k                  ││
│    │ │  Margin: Rp 1,330k (55%) vs empty return loss 892k ✅ ││
│    │ │                                                       ││
│    │ ├─ Joko: Jkt→Sby (08:00) + Sby→Jkt (20:00)             ││
│    │ │  Revenue: Rp 3,000k + Rp 2,800k = Rp 5,800k           ││
│    │ │  Margin improved: +1,200k (backhaul) ✅               ││
│    │ │                                                       ││
│    │ └─ ... (5 other pairs)                                  ││
│    │                                                         ││
│    │ Empty Returns (5):                                      ││
│    │ ├─ Siti: Jkt→Sby (no return cargo available)           ││
│    │ │  Loss: -Rp 892k (empty return cost)                   ││
│    │ │  Recommendation: Find ad-hoc return orders ⚠️         ││
│    │ │                                                       ││
│    │ └─ ... (4 other empty returns)                          ││
│    │                                                         ││
│    │ Total Backhaul Savings: Rp 4,120,000 ✅                 ││
│    └─────────────────────────────────────────────────────────┘│
│                                                                 │
│ 4. Supervisor: Review financial summary                        │
│    ┌─────────────────────────────────────────────────────────┐│
│    │ FINANCIAL BREAKDOWN (19 trips)                          ││
│    │                                                         ││
│    │ REVENUE:                                                ││
│    │ ├─ Contract clients: Rp 14,400,000 (12 trips)          ││
│    │ ├─ Spot clients: Rp 8,400,000 (7 trips)                ││
│    │ └─ Total revenue: Rp 22,800,000                        ││
│    │                                                         ││
│    │ COSTS:                                                  ││
│    │ ├─ Uang jasa driver: Rp 3,800,000 (19 × 200k)          ││
│    │ ├─ Uang jalan: Rp 1,615,000 (19 × 85k)                 ││
│    │ ├─ E-toll: Rp 1,235,000 (varies by route)              ││
│    │ ├─ BBM: Rp 3,705,000 (varies by distance)              ││
│    │ ├─ Helper: Rp 1,425,000 (19 × 75k)                     ││
│    │ └─ Total cost: Rp 11,780,000                           ││
│    │                                                         ││
│    │ MARGIN:                                                 ││
│    │ ├─ Gross margin: Rp 11,020,000                         ││
│    │ ├─ Margin %: 48.3%                                     ││
│    │ └─ Target margin: >40% ✅ ACHIEVED                     ││
│    │                                                         ││
│    │ COMPARISON:                                             ││
│    │ ├─ Last week margin: 45.2%                             ││
│    │ ├─ This week: 48.3% (+3.1%) ✅ IMPROVEMENT             ││
│    │ └─ Reason: Better backhaul optimization                 ││
│    └─────────────────────────────────────────────────────────┘│
│                                                                 │
│ 5. Supervisor Decision:                                        │
│                                                                 │
│    Option A: APPROVE ALL ✅                                    │
│    ├─ All assignments validated                               │
│    ├─ Margin meets target (48.3% > 40%)                       │
│    ├─ Manual adjustments reasonable                           │
│    └─ Backhaul optimization good (7 pairs)                    │
│                                                                 │
│    Option B: REQUEST REVISION ⚠️                               │
│    ├─ Reason: Margin too low / conflicts unresolved           │
│    ├─ Send back to dispatcher with notes                      │
│    └─ Dispatcher re-work on Senin pagi                        │
│                                                                 │
│    Option C: APPROVE WITH NOTES 📝                            │
│    ├─ Approve planning as-is                                  │
│    ├─ Add supervisor notes/instructions                       │
│    └─ Flag priority trips for special handling                │
│                                                                 │
│ Supervisor: Click [APPROVE ALL] ✅                             │
│                                                                 │
│ Enter approval notes (optional):                               │
│ "Planning approved. Good backhaul optimization this week.      │
│  Please monitor empty returns and try to find ad-hoc orders    │
│  for Siti's return trip. Overall performance excellent."       │
│                                                                 │
│ Click [CONFIRM APPROVAL]                                       │
│                                                                 │
│ System:                                                         │
│ - Update status: ASSIGNED → APPROVED                           │
│ - Lock planning (prevent further edits)                        │
│ - Record approval timestamp: Minggu 17 Nov, 20:00              │
│ - Record approver: supervisor01                                │
│ - Generate dispatch orders (ms_dispatch for each 19 trips)     │
│ - Update availability calendar (mark all resources as BOOKED)  │
│ - Calculate response deadline for driver notification          │
│   (12 hours from now = Senin 08:00)                            │
│ - Prepare notifications (queue for sending Senin 08:00)        │
│ - Generate assignment documents (PDF per driver)               │
│ - Send summary email to manager & dispatcher                   │
│                                                                 │
│ Auto-generated outputs:                                        │
│ ├─ 19 dispatch orders (DP-2025-11-18-001 to DP-2025-11-24-019)│
│ ├─ 12 driver assignment sheets (PDF)                           │
│ ├─ 1 weekly master schedule (Excel)                            │
│ ├─ 1 cost summary report (PDF)                                 │
│ └─ 19 surat jalan templates (ready for printing)               │
│                                                                 │
│ Notifications queued:                                          │
│ ├─ Manager: "Week 47 planning approved, margin 48.3%"          │
│ ├─ Dispatcher: "Planning approved, ready for execution"        │
│ ├─ Finance: "Cost breakdown Week 47: Rp 11.78 juta"            │
│ └─ 12 drivers: "Order assignment for Week 47" (send Senin pagi)│
│                                                                 │
│ Dashboard updated:                                             │
│ Status: ✅ APPROVED (ready for driver notification Senin pagi) │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SENIN 08:00 - DRIVER RECEIVE ORDER (MOBILE APP)                │
├─────────────────────────────────────────────────────────────────┤
│ System: Send batch notifications to 12 drivers                 │
│                                                                 │
│ 1. Driver: Receive push notification                           │
│    "🚛 Order baru untuk minggu ini, 18-24 Nov"                 │
│    "Total: 2 trips assigned to you"                            │
│                                                                 │
│ 2. Driver: Open app → View order detail                        │
│    ├─ Route map dengan GPS coordinates                         │
│    ├─ Contact person origin & destination                      │
│    ├─ Cargo detail & special instructions                      │
│    ├─ Truck assignment: B-1234-AB (Tronton)                    │
│    ├─ Helper: Andi (phone number)                              │
│    ├─ Uang jasa: Rp 200,000 (+ bonus Rp 20k if on-time)        │
│    └─ Uang jalan: Rp 85,000 (cash advance)                     │
│                                                                 │
│ 3. Driver: Choose action                                       │
│    Option A: Tap [CONFIRM] → Status: CONFIRMED                 │
│             System notify dispatcher, lock driver schedule     │
│                                                                 │
│    Option B: Tap [REJECT] → Select reason (sick/emergency)     │
│             System notify dispatcher URGENT                    │
│             Dispatcher find replacement driver                 │
│                                                                 │
│ 4. If no response after 6 hours: Send reminder                 │
│ 5. If no response after 12 hours: Auto-escalate dispatcher     │
│    Dispatcher call driver or reassign to other                 │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 05:45 (D-DAY) - DRIVER CHECK-IN                         │
├─────────────────────────────────────────────────────────────────┤
│ 1. Driver: Arrive at depot (GPS detected in geofence)          │
│                                                                 │
│ 2. Driver: Open app → Tap [CHECK-IN]                           │
│    System verify:                                              │
│    ├─ ✅ Location: Within 100m from depot                      │
│    ├─ ✅ Time: Within check-in window (15-30 min before)       │
│    └─ ✅ Trip date: Today                                      │
│                                                                 │
│ 3. Driver: Scan QR code on truck windshield                    │
│    System verify:                                              │
│    ├─ ✅ Truck match: B-1234-AB                                │
│    └─ ✅ Truck status: Available (not in maintenance)          │
│                                                                 │
│ 4. Driver: Pre-trip inspection checklist                       │
│    ☑️ Tire pressure OK                                         │
│    ☑️ Brake system OK                                          │
│    ☑️ Lights working                                           │
│    ☑️ Fuel level ≥50%                                          │
│    ☑️ Documents complete (SIM, STNK, KIR)                      │
│                                                                 │
│    If issue: Tap [REPORT PROBLEM]                              │
│    → Dispatcher notified → Adjust assignment (ganti truck)     │
│                                                                 │
│ 5. Driver: Confirm helper present                              │
│    ☑️ Andi checked in at 05:43                                 │
│                                                                 │
│ 6. Driver: Collect cash advance                                │
│    ├─ Uang jalan: Rp 85,000 (from cashier)                     │
│    ├─ E-toll card: ETOLL-001 (balance Rp 350k, auto top-up)    │
│    └─ Fuel card: BBM-001 (balance Rp 500k)                     │
│                                                                 │
│ 7. Driver: Tap [CONFIRM CHECK-IN COMPLETE]                     │
│                                                                 │
│ System:                                                         │
│ - Record check-in time: 05:45                                  │
│ - Record GPS location: -6.234567, 106.512345                   │
│ - Insert ms_driver_check_in record                             │
│ - Update dispatch status: APPROVED → READY                     │
│ - Notify dispatcher: "Budi ready, DP-001"                      │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 06:00 - START TRIP                                      │
├─────────────────────────────────────────────────────────────────┤
│ 1. Driver: Tap [START TRIP] in app                             │
│                                                                 │
│ System:                                                         │
│ - Update status: READY → IN_PROGRESS                           │
│ - Record actual_departure: 06:00                               │
│ - Start GPS tracking (every 30 sec):                           │
│   * Mobile app GPS (primary)                                   │
│   * GPS device (backup/validation)                             │
│ - Enable turn-by-turn navigation                               │
│ - Start trip timer                                             │
│                                                                 │
│ 2. Driver: Navigate to loading point (PT Indofood Cikupa)      │
│    GPS tracking auto-update setiap 30 detik:                   │
│    ├─ Location: -6.xxx, 106.xxx                                │
│    ├─ Speed: 45 km/h                                           │
│    ├─ Heading: 135° (Southeast)                                │
│    └─ ETA update real-time                                     │
│                                                                 │
│ 3. Dispatcher: Monitor real-time di dashboard                  │
│    ├─ Live map view: Truck B-1234-AB moving                    │
│    ├─ Status: En route to loading (12 km, 15 min)              │
│    └─ Alert if delay > 15 min                                  │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 06:20 - ARRIVE LOADING POINT                            │
├─────────────────────────────────────────────────────────────────┤
│ 1. System: Detect geofence entry (radius 100m)                 │
│    Auto-trigger: "Arrived at PT Indofood Cikupa"               │
│                                                                 │
│ 2. Driver: Tap [ARRIVED AT LOADING]                            │
│                                                                 │
│ 3. Driver: Loading process (30-60 min)                         │
│    ├─ Contact: Pak Joko (warehouse)                            │
│    ├─ Load: 15 ton Mie Instan                                  │
│    ├─ Take photo: Loaded truck (optional)                      │
│    └─ Get signed surat jalan                                   │
│                                                                 │
│ 4. Driver: Tap [LOADING COMPLETE]                              │
│    System: Update milestone "Loading completed 07:10"          │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 07:15 - DEPART TO DESTINATION                           │
├─────────────────────────────────────────────────────────────────┤
│ 1. Driver: Tap [DEPART TO DESTINATION]                         │
│                                                                 │
│ 2. GPS tracking continues (every 30 sec)                       │
│    Trip: Cikupa → Bandung (120 km, ~2.5 jam)                   │
│                                                                 │
│ 3. Auto-deduct e-toll:                                         │
│    ├─ 07:25 - Gate Cikupa: Tap e-toll card                     │
│    ├─ Auto-deduct: Rp 65,000                                   │
│    ├─ Balance after: Rp 285,000                                │
│    └─ Auto-record: ms_etoll_transaction (TOLL_PAYMENT)         │
│                                                                 │
│ 4. Swipe fuel card (if refuel):                                │
│    ├─ SPBU Shell KM 45                                         │
│    ├─ Fuel: 40 liter × Rp 6,500 = Rp 260,000                   │
│    ├─ Auto-deduct from fuel card                               │
│    └─ Auto-record: ms_fuel_consumption                         │
│                                                                 │
│ 5. Cash expenses from uang jalan:                              │
│    ├─ Parkir loading: Rp 15,000                                │
│    ├─ Makan: Rp 30,000                                         │
│    └─ Keep receipts! (for settlement)                          │
│                                                                 │
│ 6. Real-time monitoring:                                       │
│    Dispatcher: See truck on highway (80 km/h, normal)          │
│    ETA: 09:45 (updated setiap 2 menit)                         │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 09:40 - ARRIVE DESTINATION                              │
├─────────────────────────────────────────────────────────────────┤
│ 1. System: Detect geofence entry Bandung DC                    │
│    Auto-alert: "Arriving at destination (ETA 5 min)"           │
│                                                                 │
│ 2. Driver: Tap [ARRIVED AT DESTINATION]                        │
│    Record: actual_arrival = 09:40                              │
│                                                                 │
│ 3. Driver: Unloading process (30-45 min)                       │
│    ├─ Contact: Bu Siti (receiving)                             │
│    ├─ Unload: 15 ton Mie Instan                                │
│    ├─ Inspection: Check quantity & condition                   │
│    └─ Sign POD (proof of delivery)                             │
│                                                                 │
│ 4. Driver: Upload POD                                          │
│    ├─ Take photo: Signed surat jalan                           │
│    ├─ Take photo: Unloaded warehouse (optional)                │
│    └─ Upload via app                                           │
│                                                                 │
│ 5. Driver: Tap [COMPLETE TRIP]                                 │
│                                                                 │
│ System:                                                         │
│ - Update status: IN_PROGRESS → COMPLETED                       │
│ - Record completion time: 10:25                                │
│ - Stop GPS tracking                                            │
│ - Calculate actual duration: 4h 25min (vs estimated 3h 15min)  │
│ - Calculate actual distance: 125 km (vs estimated 120 km)      │
│ - Mark driver available for next assignment                    │
│ - Notify dispatcher: "DP-001 completed by Budi"                │
│ - Calculate on-time status: LATE (-70 min, no bonus ❌)        │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 11:00 - BACKHAUL TRIP (if available)                    │
├─────────────────────────────────────────────────────────────────┤
│ System: Check backhaul opportunity                             │
│ ├─ Route: Bandung → Cikupa (return trip)                       │
│ ├─ Available order: DP-002 (PT Unilever, Window 8)             │
│ └─ Suggest: "Backhaul available, Rp 1,200k"                    │
│                                                                 │
│ Dispatcher: Assign backhaul to Budi                            │
│ Driver: Repeat process (loading → transit → unloading)         │
│                                                                 │
│ Benefit: Convert -Rp 892k loss → +Rp 307k profit! (backhaul)   │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 18:00 - DRIVER RETURN TO DEPOT                          │
├─────────────────────────────────────────────────────────────────┤
│ 1. Driver: Submit accountability (uang jalan)                  │
│    ├─ Cash received: Rp 85,000                                 │
│    ├─ Expenses:                                                │
│    │   * Parkir: Rp 15,000 (nota ✅)                           │
│    │   * Retribusi: Rp 10,000 (nota ✅)                        │
│    │   * Makan: Rp 30,000 (nota ✅)                            │
│    │   Total: Rp 55,000                                        │
│    ├─ Return balance: Rp 30,000 (give back to cashier)         │
│    └─ Upload photos of receipts via app                        │
│                                                                 │
│ 2. E-toll operator: Verify e-toll transactions                 │
│    ├─ Check ms_etoll_transaction                               │
│    ├─ Toll payment: Rp 65,000 ✅                               │
│    ├─ Balance remaining: Rp 285,000                            │
│    └─ Mark as verified                                         │
│                                                                 │
│ 3. Fuel operator: Verify BBM consumption                       │
│    ├─ Check ms_fuel_consumption                                │
│    ├─ Refuel: 40 liter = Rp 260,000 ✅                         │
│    ├─ Expected: 125km ÷ 4km/l = 31.25L                         │
│    ├─ Variance: +8.75L (investigate? or normal?)               │
│    └─ Mark as verified                                         │
│                                                                 │
│ 4. Supervisor: Verify uang jalan accountability                │
│    ├─ Check receipts (parkir, retribusi, makan)                │
│    ├─ Total expenses: Rp 55,000 ✅                             │
│    ├─ Return balance: Rp 30,000 ✅                             │
│    └─ Approve accountability                                   │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ AKHIR BULAN - SETTLEMENT                                       │
├─────────────────────────────────────────────────────────────────┤
│ Finance: Calculate driver settlement (monthly)                  │
│                                                                 │
│ Driver: Budi (18 trips completed this month)                    │
│                                                                 │
│ INCOME:                                                        │
│ ├─ Uang jasa base: 18 trips × Rp 200,000 = Rp 3,600,000       │
│ ├─ Bonus on-time: 15 trips × Rp 20,000 = Rp 300,000           │
│ └─ Total income: Rp 3,900,000                                  │
│                                                                 │
│ DEDUCTIONS:                                                    │
│ ├─ Uang jalan variance: -Rp 50,000 (1 trip missing receipt)   │
│ ├─ Penalty damage: -Rp 100,000 (1 case cargo damage)          │
│ └─ Total deductions: -Rp 150,000                               │
│                                                                 │
│ NET PAYMENT:                                                   │
│ Rp 3,900,000 - Rp 150,000 = Rp 3,750,000                       │
│                                                                 │
│ Payment method: Bank transfer                                  │
│ Transfer to: BCA 1234567890 (Budi Santoso)                     │
│ Payment date: 5th next month                                   │
│                                                                 │
│ Driver app: View settlement detail & slip gaji                 │
└─────────────────────────────────────────────────────────────────┘

---

### 5.4 Rest Time Monitoring (Planning vs Actual) ⭐ NEW

```
┌─────────────────────────────────────────────────────────────────┐
│ SCENARIO: BUDI - BACKHAUL TRIP WITH REST TIME VALIDATION       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ TRIP 1 (Morning): Cikupa → Bandung                             │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                                 │
│ PLANNING:                                                       │
│ ├─ Departure: 06:00                                            │
│ ├─ Arrival Bandung: 09:15 (estimated)                          │
│ ├─ Driving time: 2.5 hours                                     │
│ └─ Total time: 3.25 hours (include loading/unloading)          │
│                                                                 │
│ ACTUAL:                                                        │
│ ├─ Departure: 06:00 ✅                                         │
│ ├─ Arrival Bandung: 09:40 (delay 25 min - traffic)            │
│ ├─ Trip complete: 10:25 (after unloading)                      │
│ └─ Actual duration: 4.42 hours                                 │
│                                                                 │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                                 │
│ REST TIME AT BANDUNG (Before Backhaul)                         │
│                                                                 │
│ System: Detect trip completion at 10:25                         │
│                                                                 │
│ Next trip (BACKHAUL): Bandung → Cikupa, Window 8 (14:00-16:00) │
│                                                                 │
│ REST TIME CALCULATION:                                         │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ Current time: 10:25                                         ││
│ │ Next trip departure: 14:00 (planned)                        ││
│ │                                                             ││
│ │ Available rest time: 3.58 hours (3h 35min)                  ││
│ │                                                             ││
│ │ Check policy (Budi exception POL001 + EXC001):              ││
│ │ ├─ Min rest between trips: 3 hours (custom)                ││
│ │ ├─ Min rest at pool before backhaul: 1 hour (policy)       ││
│ │ ├─ Required: MAX(3h, 1h) = 3 hours                         ││
│ │ │                                                           ││
│ │ ├─ Available: 3.58 hours                                   ││
│ │ ├─ Required: 3.0 hours                                     ││
│ │ └─ Compliance: 3.58h > 3.0h = ✅ OK!                       ││
│ │                                                             ││
│ │ Cumulative driving check:                                   ││
│ │ ├─ Trip 1 driving: 2.5h                                    ││
│ │ ├─ Trip 2 driving (estimated): 2.5h                        ││
│ │ ├─ Total: 5.0h                                             ││
│ │ ├─ Max allowed (Budi): 10h/day                             ││
│ │ └─ Compliance: 5.0h < 10h = ✅ OK                          ││
│ │                                                             ││
│ │ Trip count check:                                           ││
│ │ ├─ Trips today: 1 (completed)                              ││
│ │ ├─ New trip: would be 2nd                                  ││
│ │ ├─ Max allowed (Budi): 2 trips/day                         ││
│ │ └─ Compliance: 2 = 2 = ✅ OK (at limit)                    ││
│ │                                                             ││
│ │ DECISION: Backhaul APPROVED ✅                              ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ System: Send notification to driver                            │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ 📱 Driver App Notification:                                 ││
│ │                                                             ││
│ │ ✅ Trip 1 completed!                                        ││
│ │                                                             ││
│ │ 🔄 Backhaul available:                                      ││
│ │ Bandung → Cikupa (14:00)                                    ││
│ │ Revenue: Rp 1,200,000                                       ││
│ │                                                             ││
│ │ ⏰ Rest Time Status:                                        ││
│ │ ├─ Current time: 10:25                                     ││
│ │ ├─ Required rest: 3 hours                                  ││
│ │ ├─ You must depart by: 13:25 (earliest)                    ││
│ │ ├─ Planned departure: 14:00                                ││
│ │ └─ Rest time available: 3h 35min ✅                        ││
│ │                                                             ││
│ │ 📍 Recommended actions:                                     ││
│ │ ├─ Lunch break: 30 min (11:00-11:30)                       ││
│ │ ├─ Rest/sleep: 2 hours (11:30-13:30)                       ││
│ │ └─ Loading preparation: 30 min (13:30-14:00)               ││
│ │                                                             ││
│ │ [ACCEPT BACKHAUL] [REJECT - TOO TIRED]                      ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ Driver: Tap [ACCEPT BACKHAUL]                                  │
│                                                                 │
│ System:                                                         │
│ - Record rest start: 10:25                                     │
│ - Set alert: 13:30 (remind driver to prepare)                  │
│ - Update trip 2 status: CONFIRMED                              │
│ - Track driver location (ensure driver resting, not driving)   │
│                                                                 │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                                 │
│ ACTUAL REST MONITORING:                                        │
│                                                                 │
│ 10:25 - Trip 1 complete, driver at Bandung warehouse           │
│ 10:30 - GPS: Driver moving (search for restaurant)             │
│ 11:00 - GPS: Driver stationary at Warung Nasi Ibu Siti         │
│        (geofence: restaurant area)                             │
│ 11:35 - GPS: Driver moving (back to warehouse/parking)         │
│ 11:45 - GPS: Driver stationary at warehouse rest area          │
│ 13:30 - System send alert: "Waktu istirahat selesai, prepare   │
│         for loading"                                           │
│ 13:35 - Driver check-in: "Ready for Trip 2"                    │
│ 13:50 - Loading start                                          │
│ 14:05 - Driver tap [START TRIP 2] (5 min late, acceptable)     │
│                                                                 │
│ ACTUAL REST TIME RECORDED:                                     │
│ ├─ Rest start: 10:25 (trip 1 complete)                         │
│ ├─ Rest end: 14:05 (trip 2 start)                              │
│ ├─ Actual rest: 3.67 hours (3h 40min) ✅                       │
│ ├─ Required: 3.0 hours                                         │
│ ├─ Compliance: 3.67h > 3.0h = ✅ COMPLIANT                     │
│ └─ Overtime: No (rest adequate)                                │
│                                                                 │
│ System update ms_planning_assignment:                          │
│ - actual_pool_arrival: NULL (not at pool, at Bandung)          │
│ - actual_rest_hours: 3.67                                      │
│ - actual_rest_compliance: 1 (true)                             │
│ - cumulative_driving_hours_today: 5.0h                         │
│ - trip_sequence_today: 2                                       │
│ - fatigue_risk_level: 'LOW' (good rest, within limits)         │
│                                                                 │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                                 │
│ TRIP 2 (Afternoon): Bandung → Cikupa (Backhaul)                │
│                                                                 │
│ 14:05 - Depart Bandung                                         │
│ 17:30 - Arrive Cikupa warehouse                                │
│ 18:00 - Unloading complete                                     │
│ 18:15 - Arrive back at POOL ⭐ (important!)                    │
│                                                                 │
│ System: Record pool arrival                                    │
│ - actual_pool_arrival: 18:15                                   │
│ - Update driver status: AVAILABLE (after rest)                 │
│                                                                 │
│ Check if driver has trip tomorrow (19 Nov):                     │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ Next trip: 19 Nov, Window 4 (06:00-08:00)                   ││
│ │                                                             ││
│ │ REST TIME CALCULATION:                                      ││
│ │ ├─ Pool arrival today: 18 Nov 18:15                        ││
│ │ ├─ Next departure: 19 Nov 06:00                            ││
│ │ ├─ Rest available: 11.75 hours (11h 45min)                 ││
│ │ │                                                           ││
│ │ ├─ Today cumulative driving: 5.0h                          ││
│ │ ├─ Is long trip? No (5h < 8h threshold)                    ││
│ │ ├─ Required rest: 3 hours (Budi custom minimum)            ││
│ │ │                                                           ││
│ │ ├─ Compliance: 11.75h > 3h = ✅ OK!                        ││
│ │ └─ Fatigue risk: LOW (adequate overnight rest)             ││
│ │                                                             ││
│ │ Tomorrow's trip APPROVED ✅                                 ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ SCENARIO 2: REST TIME VIOLATION ALERT                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ Driver: Joko                                                    │
│ Date: 18 Nov 2025                                               │
│                                                                 │
│ Trip 1: 06:00-09:00 (Cikupa-Bandung, 3h)                       │
│ Trip 2: 10:00-13:00 (Bandung-Tasikmalaya, 3h)                  │
│ Trip 3 request: 14:00-16:00 (Tasik-Garut, 2h)                  │
│                                                                 │
│ System: Check compliance for Trip 3                            │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ REST TIME CHECK:                                            ││
│ │ ├─ Trip 2 complete: 13:00                                   ││
│ │ ├─ Trip 3 planned: 14:00                                    ││
│ │ ├─ Rest available: 1 hour                                   ││
│ │ ├─ Required (policy): 2 hours                               ││
│ │ └─ Compliance: 1h < 2h = ❌ VIOLATION!                      ││
│ │                                                             ││
│ │ CUMULATIVE HOURS CHECK:                                     ││
│ │ ├─ Trip 1: 3h                                               ││
│ │ ├─ Trip 2: 3h                                               ││
│ │ ├─ Trip 3: 2h                                               ││
│ │ ├─ Total: 8h                                                ││
│ │ ├─ Max: 12h                                                 ││
│ │ └─ Compliance: 8h < 12h = ✅ OK                             ││
│ │                                                             ││
│ │ TRIP COUNT CHECK:                                           ││
│ │ ├─ Current: 2 trips                                         ││
│ │ ├─ New: 3 trips                                             ││
│ │ ├─ Max: 3 trips/day                                         ││
│ │ └─ Compliance: 3 = 3 = ✅ OK (at limit)                     ││
│ │                                                             ││
│ │ OVERALL: ⚠️ REST TIME VIOLATION                             ││
│ │ Cannot assign Trip 3 without approval                       ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ System: Block auto-assignment                                  │
│ - Trip 3 marked: PENDING_OVERRIDE                              │
│ - Send alert to dispatcher                                     │
│                                                                 │
│ Dispatcher Dashboard Alert:                                    │
│ ┌─────────────────────────────────────────────────────────────┐│
│ │ ⚠️ REST TIME VIOLATION WARNING                              ││
│ │                                                             ││
│ │ Driver: Joko (DRV002)                                       ││
│ │ Trip: Tasikmalaya-Garut (14:00)                             ││
│ │                                                             ││
│ │ Issue: Insufficient rest time                               ││
│ │ ├─ Available: 1 hour                                        ││
│ │ ├─ Required: 2 hours                                        ││
│ │ └─ Shortage: 1 hour ❌                                      ││
│ │                                                             ││
│ │ Options:                                                    ││
│ │ 1. [DELAY TRIP] - Reschedule to 15:00 (give 2h rest)       ││
│ │ 2. [REASSIGN] - Assign to different driver                 ││
│ │ 3. [OVERRIDE] - Manager approval required (emergency)       ││
│ │ 4. [CANCEL] - Cancel this trip                              ││
│ │                                                             ││
│ │ Recommendation: REASSIGN or DELAY                           ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ Dispatcher: Select [DELAY TRIP]                                │
│ - Change time window: Window 8 → Window 9 (16:00-18:00)        │
│ - New rest time: 3 hours (13:00 → 16:00)                       │
│ - Compliance: 3h > 2h = ✅ OK                                  │
│ - Client notified: "Slight delay for safety"                   │
│                                                                 │
│ System:                                                         │
│ - Update trip 3 departure: 16:00                               │
│ - Record adjustment reason: "Rest time compliance"             │
│ - Log in ms_dispatch_adjustment_log                            │
│ - Notify driver: "Trip 3 rescheduled to 16:00, rest time OK"   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```
```

### 5.2 Workflow Mode 2: AD-HOC ORDER (Spot Client)

```
┌─────────────────────────────────────────────────────────────────┐
│ HARI H-1 atau H - CLIENT ORDER VIA WA/PHONE                    │
├─────────────────────────────────────────────────────────────────┤
│ Client: "Pak, besok jam 8 pagi kirim barang dari Cikupa ke     │
│         Bandung, 15 ton. Bisa ga?"                             │
│                                                                 │
│ Dispatcher:                                                     │
│ 1. Check availability:                                         │
│    ├─ Driver available window 4: Joko, Siti, Agus              │
│    ├─ Truck available: B-5678-CD (Tronton)                     │
│    └─ Route: Cikupa-Bandung shuttle                            │
│                                                                 │
│ 2. Check spot rate:                                            │
│    ├─ Client: CV ABC (no contract, spot rate)                  │
│    └─ Rate: Rp 1,800,000 (vs contract Rp 1,200k)               │
│                                                                 │
│ 3. Quote to client:                                            │
│    "Bisa pak, rate Rp 1.8 juta, truck Tronton. OK?"            │
│                                                                 │
│ Client: "OK pak, deal!"                                        │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ DISPATCHER CREATE ORDER MANUALLY                               │
├─────────────────────────────────────────────────────────────────┤
│ 1. Input ke system:                                            │
│    ├─ Client: CV ABC                                           │
│    ├─ Order source: ADHOC_WA                                   │
│    ├─ Route: Cikupa-Bandung                                    │
│    ├─ Date: Tomorrow (18 Nov)                                  │
│    ├─ Time window: 4 (06:00-08:00)                             │
│    ├─ Weight: 15 ton                                           │
│    ├─ Product: General cargo                                   │
│    └─ Quoted rate: Rp 1,800,000 (spot rate ✅)                 │
│                                                                 │
│ 2. System: Auto-calculate cost                                 │
│    ├─ Uang jasa: Rp 200,000                                    │
│    ├─ Uang jalan: Rp 85,000                                    │
│    ├─ E-toll: Rp 65,000                                        │
│    ├─ BBM: Rp 243,750                                          │
│    ├─ Helper: Rp 75,000                                        │
│    ├─ Total cost: Rp 668,750                                   │
│    └─ Margin: Rp 1,131,250 (63%) ✅ GOOD!                      │
│                                                                 │
│ 3. Dispatcher: Assign driver & truck manually                  │
│    ├─ Driver: Joko (DRV002) - Available ✅                     │
│    ├─ Truck: B-5678-CD - Available ✅                          │
│    └─ Helper: Budi Helper (HLP005) - Available ✅              │
│                                                                 │
│ 4. Dispatcher: Create dispatch order                           │
│    Status: CONFIRMED (langsung, no weekly planning)            │
│                                                                 │
│ 5. System: Send notification to driver Joko                    │
│    (same flow as weekly planning - driver confirm/reject)      │
└─────────────────────────────────────────────────────────────────┘
                           │
                           ▼
│ (Continue with same execution flow as weekly planning)         │
│ - Driver check-in                                              │
│ - Start trip                                                   │
│ - GPS tracking                                                 │
│ - Complete trip                                                │
│ - Settlement                                                   │
```

### 5.3 Workflow DISPATCHER ADJUSTMENT (Real-time Problem Solving)

```
┌─────────────────────────────────────────────────────────────────┐
│ SELASA 05:30 - DRIVER SICK! (30 min before departure)          │
├─────────────────────────────────────────────────────────────────┤
│ Driver Budi: Call dispatcher                                    │
│ "Pak maaf, saya demam tidak bisa berangkat"                     │
│                                                                 │
│ Dispatcher:                                                     │
│ 1. Open dispatch: DP-2025-11-18-001                            │
│ 2. Click [ADJUST] button                                       │
│ 3. Select adjustment type: [x] Change Driver                   │
│                                                                 │
│ 4. Mark old driver:                                            │
│    ├─ Budi (DRV001) → Status: SICK                             │
│    └─ Mark unavailable today                                   │
│                                                                 │
│ 5. System suggest replacement:                                 │
│    ┌────────────────────────────────────────┐                  │
│    │ [✅] Joko (DRV002)                     │                  │
│    │      Route familiarity: EXPERT (50×)   │                  │
│    │      Last trip: 2 days ago             │                  │
│    │      Rating: 4.8/5.0                   │                  │
│    ├────────────────────────────────────────┤                  │
│    │ [ ] Siti (DRV003)                      │                  │
│    │      Route familiarity: COMPETENT (20×)│                  │
│    └────────────────────────────────────────┘                  │
│                                                                 │
│ 6. Dispatcher: Select Joko → Enter reason:                     │
│    "Driver Budi sakit mendadak (demam), diganti Joko"          │
│                                                                 │
│ 7. System: Auto-check impact                                   │
│    ├─ Cost impact: NO (same uang jasa rate)                    │
│    ├─ Tariff impact: NO                                        │
│    └─ Approval required: NO (auto-approved)                    │
│                                                                 │
│ 8. Dispatcher: Click [SAVE & NOTIFY]                           │
│                                                                 │
│ System:                                                         │
│ - Update ms_dispatch: driver_id = DRV002                       │
│ - Insert ms_dispatch_adjustment_log (DRIVER_CHANGE)            │
│ - Free up Budi's time slot                                     │
│ - Mark Joko as assigned                                        │
│ - Send notification:                                           │
│   * Budi: "Istirahat dulu, semoga cepat sembuh"                │
│   * Joko: "⚡ URGENT: Assignment baru jam 06:00 Cikupa-Bandung" │
│   * Supervisor: "DP-001 adjusted: Driver changed (Budi sick)"  │
│                                                                 │
│ Time: 5 minutes to resolve! ✅                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ SELASA 06:15 - CLIENT CHANGE ROUTE (after driver departed!)    │
├─────────────────────────────────────────────────────────────────┤
│ Client call: "Pak, ganti tujuan dari Bandung ke Tasikmalaya"   │
│                                                                 │
│ Dispatcher:                                                     │
│ 1. Calculate impact:                                           │
│    ├─ Old route: Cikupa-Bandung (120 km)                       │
│    ├─ New route: Cikupa-Tasikmalaya (170 km) = +50 km          │
│    └─ Additional cost: +Rp 200,000                             │
│                                                                 │
│ 2. Negotiate with client:                                      │
│    "Pak, +50km berarti tariff naik Rp 300k jadi Rp 1.8 juta"   │
│                                                                 │
│ Client: "OK urgent, approve!"                                  │
│                                                                 │
│ 3. Dispatcher: Adjust dispatch                                 │
│    ├─ Click [ADJUST] → [x] Change Route                        │
│    ├─ Select new route: Cikupa-Tasikmalaya                     │
│    ├─ System auto re-calculate cost & tariff                   │
│    └─ Reason: "Client urgent request, tariff adjusted"         │
│                                                                 │
│ 4. System: Check approval requirement                          │
│    ├─ Tariff impact: YES (+Rp 300k)                            │
│    ├─ Margin: 36.67% → 36.11% (-0.56%)                         │
│    └─ Require approval: YES (Manager)                          │
│                                                                 │
│ 5. Manager: Receive approval request notification              │
│    Review → Approve: "Margin OK, client confirmed"             │
│                                                                 │
│ 6. System: Update & notify                                     │
│    ├─ Update route in ms_dispatch                              │
│    ├─ Log adjustment in ms_dispatch_adjustment_log             │
│    ├─ Update GPS destination coordinates                       │
│    ├─ Notify driver: "⚠️ TUJUAN BERUBAH ke Tasikmalaya,        │
│    │                    GPS updated, check app!"               │
│    └─ Notify client: "Route change confirmed, new tariff       │
│                       Rp 1.8 juta"                             │
│                                                                 │
│ Driver: Lihat app → Destination updated → Follow new GPS       │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. TIME WINDOW SYSTEM

### 5.1 Window Definition (12 Windows x 2 Jam)
| Window | Time Range | Typical Use |
|--------|------------|-------------|
| 1 | 00:00 - 02:00 | Night shift / Early departure |
| 2 | 02:00 - 04:00 | Night shift |
| 3 | 04:00 - 06:00 | Early morning |
| 4 | 06:00 - 08:00 | Morning rush |
| 5 | 08:00 - 10:00 | Morning peak |
| 6 | 10:00 - 12:00 | Late morning |
| 7 | 12:00 - 14:00 | Noon |
| 8 | 14:00 - 16:00 | Early afternoon |
| 9 | 16:00 - 18:00 | Late afternoon |
| 10 | 18:00 - 20:00 | Evening |
| 11 | 20:00 - 22:00 | Night |
| 12 | 22:00 - 00:00 | Late night |

### 5.2 Window Configuration
- Bisa enable/disable per window (operational hours)
- Bisa custom nama window per client
- Bisa atur max trips per window

---

## 6. EXCEL IMPORT FORMAT

### 6.1 Template Planning Mingguan
```
Sheet Name: Planning_Shuttle_[Week_Number]

| No | Tanggal    | Window | Route Code | Origin   | Destination | Customer | Qty/Ton | Notes   |
|----|------------|--------|------------|----------|-------------|----------|---------|---------|
| 1  | 18/11/2025 | 5      | JKT-BDG    | Jakarta  | Bandung     | PT ABC   | 10.5    | Urgent  |
| 2  | 18/11/2025 | 5      | JKT-BDG    | Jakarta  | Bandung     | PT XYZ   | 8.0     | -       |
| 3  | 18/11/2025 | 7      | SBY-MLG    | Surabaya | Malang      | PT DEF   | 12.0    | Fragile |
```

**Mandatory Fields:**
- Tanggal (Date format: DD/MM/YYYY)
- Window (Integer 1-12)
- Route Code ATAU (Origin + Destination)
- Customer (must exist in ms_customer)
- Qty/Ton (Decimal)

**Optional Fields:**
- Notes

### 6.2 Import Validation Rules
1. Tanggal harus dalam range week yang dipilih
2. Window harus 1-12
3. Route Code harus exist di ms_route (atau create on-the-fly)
4. Customer harus exist di ms_customer
5. Qty/Ton harus numeric > 0
6. Duplicate detection (same date + window + route + customer)

---

## 7. YANG MASIH KURANG & DIBUTUHKAN

### 7.1 Kebutuhan Teknis
❌ **Database Schema:**
- Semua tabel modul baru belum dibuat
- Foreign key relationships perlu diset
- Indexing untuk performance (route_id, customer_id, date, window_id)

❌ **Authentication & Authorization:**
- User roles: Admin, Dispatcher, Driver, Finance, Manager
- Permission matrix per role
- Driver mobile app login

❌ **API Endpoints:** (CRITICAL - Required untuk GPS tracking)
- ✅ RESTful API untuk mobile app driver (MUST HAVE)
  - Authentication (login/logout)
  - Get trip list
  - Get trip detail
  - Update GPS position (POST bulk coordinates)
  - Check-in/check-out
  - Upload POD
- ✅ API untuk GPS device integration (MUST HAVE)
  - Receive GPS position (webhook/polling)
  - Device registration
  - Device status update
- ⏳ Webhook untuk notification

❌ **Excel Import Library:**
- PHPSpreadsheet atau Laravel Excel
- Template validation
- Error reporting

❌ **Auto Scheduling Algorithm:**
- Logic assignment truck & driver
- Conflict resolution rules
- Optimization criteria (distance, cost, familiarity)

### 7.2 Kebutuhan Business Logic
❌ **Calculation Engine:**
- Formula uang jalan (base + per km + per ton?)
- Formula uang jasa (flat rate + bonus?)
- Overtime calculation
- Deduction rules

❌ **Business Rules:**
- Max trips per driver per day/window
- Truck maintenance schedule blocking
- Driver leave/off blocking
- Priority customer handling
- Emergency/urgent trip handling

❌ **Notification System:**
- Email notification (planning approved, dispatch created)
- SMS/WhatsApp untuk driver
- Push notification (mobile app)
- Alert for conflicts/delays

❌ **Reporting Requirements:**
- Report templates
- Export formats (PDF, Excel, CSV)
- Scheduled reports
- Dashboard KPIs

### 7.3 Kebutuhan UI/UX
❌ **Planning Dashboard:**
- Calendar component (week view)
- Drag & drop assignment interface
- Color coding by status
- Filter & search

❌ **Dispatcher Dashboard:**
- Real-time status board
- Map view (optional)
- Quick action buttons
- Conflict alerts

❌ **Driver Mobile Interface:**
- Simple trip list view
- Check-in/check-out
- POD upload (photo/signature)
- Navigation integration

❌ **Management Dashboard:**
- Executive summary KPIs
- Charts & graphs
- Drill-down capability
- Export functionality

### 7.4 Kebutuhan Integrasi
❓ **Third-party Services:**
- ✅ GPS tracking: Mobile app + GPS device (dual tracking)
- ⏳ SMS gateway?
- ⏳ Email service (SMTP/API)?
- ⏳ Payment gateway (for settlement)?
- ✅ Map/routing API (Google Maps, HERE, etc) - untuk display tracking

❓ **Existing Systems:**
- Integrasi dengan accounting system?
- Integrasi dengan ERP client?
- Integrasi dengan warehouse system?

### 7.5 Kebutuhan Non-Functional
❌ **Performance:**
- Response time target?
- Concurrent user capacity?
- Data retention policy?

❌ **Security:**
- Data encryption?
- Audit trail?
- Backup strategy?

❌ **Scalability:**
- Multi-tenant support?
- Multiple warehouse/branch?
- Cloud deployment strategy?

---

## 8. PERTANYAAN KLARIFIKASI BISNIS

### 8.1 Time Window
1. **Apakah semua 12 window digunakan, atau hanya jam operasional tertentu?**
   - Contoh: Operasional 06:00-22:00 = Window 4-11 saja?
   - Perlu custom window per client?

2. **Apakah 1 driver bisa dapat multiple trips dalam 1 window?**
   - Misal: trip short distance, bisa 2x dalam window yang sama?

3. **Buffer time antar window?**
   - Driver perlu rest time antar trip?

### 8.2 Planning & Scheduling
4. **Berapa lama lead time untuk planning?**
   - Client kirim Excel berapa hari sebelum week execution?
   - Grace period untuk changes?

5. **Approval workflow?**
   - Single approval atau multi-level?
   - Siapa yang approve apa?

6. **Handling changes setelah approved?**
   - Client request perubahan di mid-week?
   - Cancellation policy?

### 8.3 Cost Calculation
7. **Formula uang jalan & uang jasa?**
   - Fixed per trip?
   - Variable by distance/ton?
   - Overtime/shift rate?
   - Incentive/bonus structure?

8. **Deduction policy?**
   - Penalty untuk delay?
   - Penalty untuk damage?
   - Fuel cost responsibility?

### 8.4 FMCG Multi-Point
9. **Route optimization logic?**
   - Nearest neighbor?
   - Time window consideration?
   - Truck capacity constraint?

10. **Partial delivery handling?**
    - Jika 1 point gagal, trip tetap lanjut?
    - Rerouting on-the-fly?

### 8.5 Tracking & Monitoring
11. **GPS tracking requirement?** ✅ ANSWERED
    - ✅ Real-time GPS WAJIB
    - ✅ Dual source: Mobile app driver + GPS device
    - ⏳ Geofencing alert? (TBD)

12. **Proof of Delivery (POD)?**
    - Photo wajib?
    - Signature digital?
    - Barcode/QR scan?

### 8.6 Settlement
13. **Payment cycle?**
    - Weekly? Bi-weekly? Monthly?
    - Cut-off date?

14. **Payment method?**
    - Transfer bank?
    - Cash?
    - Integration with payroll?

### 8.7 Multi-tenant
15. **Apakah aplikasi untuk 1 perusahaan atau multi-client?**
    - Single company (CWU internal)?
    - SaaS untuk multiple trucking companies?

---

## 9. ROADMAP & LANGKAH SELANJUTNYA

### SPRINT 1 (Week 1-2): Foundation & Master Data
**Prioritas: CRITICAL**

#### Tasks:
1. **Database Setup**
   - [ ] Create all master tables (route, customer, location, tariff, time_window)
   - [ ] Setup foreign keys & indexes
   - [ ] Seed time_window data (12 windows)
   
2. **Master Route Module**
   - [ ] Model: MsRoute
   - [ ] Controller: MsRouteController (CRUD)
   - [ ] Views: index, create, edit, show
   - [ ] Route resource di web.php
   
3. **Master Customer Module**
   - [ ] Model: MsCustomer
   - [ ] Controller: MsCustomerController (CRUD)
   - [ ] Views: index, create, edit, show
   - [ ] Route resource di web.php
   
4. **Master Location Module**
   - [ ] Model: MsLocation
   - [ ] Controller: MsLocationController (CRUD)
   - [ ] Views: index, create, edit, show (with map preview)
   - [ ] Route resource di web.php
   
5. **Master Tariff Module**
   - [ ] Model: MsTariff
   - [ ] Controller: MsTariffController (CRUD)
   - [ ] Views: index, create, edit, show
   - [ ] Route resource di web.php
   
6. **Update Sidebar Navigation**
   - [ ] Add route, customer, location, tariff menu

**Deliverables:**
- ✅ All master data modules functional
- ✅ Sample data seeded for testing
- ✅ Navigation accessible from sidebar

---

### SPRINT 2 (Week 3-4): Planning & Import
**Prioritas: CRITICAL**

#### Tasks:
1. **Database Setup**
   - [ ] Create planning tables (ms_planning_weekly, ms_planning_assignment, ms_availability)
   
2. **Excel Import Module**
   - [ ] Install PHPSpreadsheet/Laravel Excel
   - [ ] Create import controller: PlanningImportController
   - [ ] Upload form view
   - [ ] Validation logic
   - [ ] Error reporting view
   - [ ] Preview before confirm import
   
3. **Weekly Planning Dashboard**
   - [ ] Calendar component (7 days x 12 windows)
   - [ ] Display imported planning data
   - [ ] Filter: week, customer, status
   - [ ] Summary statistics (total trips, trucks needed)
   
4. **Planning Management**
   - [ ] View planning detail
   - [ ] Edit planning (date, window, route)
   - [ ] Delete planning
   - [ ] Bulk actions (approve, cancel)

**Deliverables:**
- ✅ Excel import functional
- ✅ Weekly calendar view
- ✅ Planning CRUD operations

---

### SPRINT 3 (Week 5-6): Auto Scheduling & Assignment
**Prioritas: HIGH**

#### Tasks:
1. **Availability Calendar**
   - [ ] Track driver availability
   - [ ] Track truck availability
   - [ ] Maintenance schedule
   - [ ] Leave/off days
   
2. **Auto Scheduling Engine**
   - [ ] Algorithm: find available driver & truck
   - [ ] Logic: route familiarity scoring
   - [ ] Conflict detection (double booking)
   - [ ] Suggest alternatives
   
3. **Manual Assignment Interface**
   - [ ] Drag & drop driver to trip
   - [ ] Drag & drop truck to trip
   - [ ] Swap assignment
   - [ ] Unassign
   
4. **Planning Approval Workflow**
   - [ ] Approval button
   - [ ] Status update: Draft → Approved
   - [ ] Generate dispatch orders
   - [ ] Lock planning (prevent edit after approved)
   
5. **Export Planning**
   - [ ] Export to PDF (weekly schedule per driver)
   - [ ] Export to Excel (management review)

**Deliverables:**
- ✅ Auto scheduling working
- ✅ Manual assignment UI
- ✅ Approval workflow functional
- ✅ Export capabilities

---

### SPRINT 4 (Week 7-8): Dispatch & Execution
**Prioritas: HIGH**

#### Tasks:
1. **Database Setup**
   - [ ] Create dispatch tables (ms_dispatch, ms_dispatch_tracking)
   
2. **Dispatch Module**
   - [ ] Model: MsDispatch
   - [ ] Controller: DispatchController
   - [ ] Generate dispatch from approved planning
   - [ ] Manual create dispatch (ad-hoc)
   
3. **Dispatcher Dashboard Enhancement**
   - [ ] Today's dispatches
   - [ ] Filter by status
   - [ ] Quick actions (mark departed, arrived, completed)
   - [ ] Alert for delays
   
4. **Driver Mobile View (Web-based)**
   - [ ] Login driver
   - [ ] My trips today
   - [ ] Check-in (departure)
   - [ ] Check-out (arrival)
   - [ ] Upload POD photo
   
5. **Tracking & Monitoring**
   - [ ] GPS tracking integration (mobile app)
   - [ ] GPS tracking integration (GPS device)
   - [ ] Real-time map view
   - [ ] Status timeline view
   - [ ] Real-time dashboard (in-transit trips)
   - [ ] Geofencing alerts (optional)

**Deliverables:**
- ✅ Dispatch generation from planning
- ✅ Dispatcher dashboard functional
- ✅ Driver web interface
- ✅ Basic tracking

---

### SPRINT 5 (Week 9-10): FMCG Order & Cost Calculation
**Prioritas: MEDIUM**

#### Tasks:
1. **Database Setup**
   - [ ] Create order tables (ms_order, ms_order_detail)
   - [ ] Create route grouping tables
   
2. **Order Management**
   - [ ] Model: MsOrder, MsOrderDetail
   - [ ] Controller: OrderController
   - [ ] Create order with multi-point
   - [ ] Route grouping tool
   
3. **Pre-Cost Calculation**
   - [ ] Cost calculator service class
   - [ ] Formula configuration
   - [ ] Calculate for planning
   - [ ] Calculate for order
   - [ ] Display on dispatch
   
4. **Uang Jalan & Jasa Calculation**
   - [ ] Base calculation logic
   - [ ] Distance/weight factor
   - [ ] Overtime calculation
   - [ ] Display on dispatch detail

**Deliverables:**
- ✅ FMCG order module
- ✅ Cost calculation functional
- ✅ Uang jalan/jasa computed

---

### SPRINT 6 (Week 11-12): Settlement & Reporting
**Prioritas: MEDIUM**

#### Tasks:
1. **Database Setup**
   - [ ] Create settlement tables (ms_settlement, ms_settlement_detail)
   
2. **Settlement Module**
   - [ ] Model: MsSettlement, MsSettlementDetail
   - [ ] Controller: SettlementController
   - [ ] Create settlement (select period)
   - [ ] Auto populate completed dispatches
   - [ ] Calculate totals
   - [ ] Approval workflow
   - [ ] Mark as paid
   
3. **Reporting Module**
   - [ ] Delivery report (per period, customer, driver)
   - [ ] Cost analysis report (estimated vs actual)
   - [ ] Driver performance report
   - [ ] Truck utilization report
   - [ ] Export to Excel/PDF
   
4. **Dashboard KPIs**
   - [ ] Total trips (today, week, month)
   - [ ] Total revenue
   - [ ] On-time delivery rate
   - [ ] Top drivers/trucks
   - [ ] Charts & graphs

**Deliverables:**
- ✅ Settlement module functional
- ✅ Basic reports available
- ✅ Dashboard KPIs displayed

---

### SPRINT 7+ (Future Enhancements): Advanced Features
**Prioritas: MEDIUM-HIGH**

#### GPS & Mobile App Features:
- [ ] **Mobile app (native iOS/Android)** - REQUIRED
  - [ ] Driver login & authentication
  - [ ] GPS tracking background service
  - [ ] Trip list & details
  - [ ] Check-in/out dengan GPS coordinate
  - [ ] POD capture (photo/signature)
  - [ ] Offline mode support
  - [ ] Push notification
  
- [ ] **GPS Device Integration** - REQUIRED
  - [ ] API integration dengan GPS device vendor
  - [ ] Real-time position polling
  - [ ] Data synchronization dengan mobile app
  - [ ] Fallback mechanism jika salah satu source mati
  
- [ ] **Real-time Tracking Dashboard**
  - [ ] Live map view (Google Maps/Leaflet)
  - [ ] Display truck position real-time
  - [ ] Route playback history
  - [ ] ETA calculation based on GPS
  - [ ] Geofencing alerts (arrival/departure detection)
  
#### Other Optional Features:
- [ ] WhatsApp notification integration
- [ ] Advanced route optimization (AI/ML)
- [ ] Predictive maintenance
- [ ] Driver scoring & rating
- [ ] Customer portal (self-service order)
- [ ] Multi-language support
- [ ] Multi-currency support
- [ ] API for third-party integration

---

## 10. RISK & MITIGATION

### 10.1 Technical Risks
| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Excel format inconsistent | HIGH | MEDIUM | Strict template + validation |
| Auto scheduling conflict | MEDIUM | HIGH | Manual override option |
| Performance issue (large data) | HIGH | MEDIUM | Indexing + pagination + caching |
| Mobile responsiveness | MEDIUM | LOW | Bootstrap 5 + testing |

### 10.2 Business Risks
| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Requirement change mid-development | HIGH | HIGH | Agile sprint + weekly review |
| User adoption resistance | MEDIUM | MEDIUM | Training + simple UI |
| Data migration from old system | HIGH | MEDIUM | Import tools + validation |

---

## 11. SUCCESS CRITERIA

### 11.1 Functional
- ✅ Import Excel planning dengan 95%+ success rate
- ✅ Auto scheduling assign 80%+ trips tanpa conflict
- ✅ Dispatch generation < 5 detik per planning
- ✅ Driver dapat access trip info via mobile
- ✅ Settlement calculation 100% akurat

### 11.2 Non-Functional
- ✅ Page load < 3 detik
- ✅ Support 50+ concurrent users
- ✅ 99% uptime
- ✅ Mobile responsive on all devices
- ✅ Zero data loss

### 11.3 Business
- ✅ Reduce planning time 50%
- ✅ Increase truck utilization 20%
- ✅ Reduce manual errors 80%
- ✅ Settlement processing time < 1 hari

---

## 12. NEXT IMMEDIATE ACTIONS

### Hari Ini / Besok:
1. **✅ Approve blueprint document ini**
   - Review dengan stakeholder
   - Finalize requirement
   
2. **✅ Answer klarifikasi bisnis (Section 8)**
   - Time window policy
   - Cost calculation formula
   - Settlement cycle
   
3. **✅ Setup development environment**
   - Database backup current state
   - Git branch untuk development
   
4. **🚀 START SPRINT 1**
   - Create database tables (master data)
   - Build Master Route module
   - Build Master Customer module

### Minggu Ini:
5. **Complete SPRINT 1 (50%)**
   - Master Route ✅
   - Master Customer ✅
   - Master Location (in progress)

### Minggu Depan:
6. **Complete SPRINT 1 (100%)**
   - Master Tariff ✅
   - Time Window seed ✅
   - Sidebar update ✅
   
7. **START SPRINT 2**
   - Excel import preparation
   - Planning tables creation

---

## APPROVAL & SIGN-OFF

**Document Prepared By:** AI Assistant  
**Date:** 17 November 2025  
**Version:** 1.0

**Stakeholder Review Required:**
- [ ] Business Owner
- [ ] Project Manager
- [ ] Technical Lead
- [ ] Operations Manager

**Status:** ⏳ Awaiting Approval

---

**Catatan:** 
Dokumen ini adalah living document dan akan di-update seiring development progress. 
Setiap perubahan requirement harus documented dan approved.
