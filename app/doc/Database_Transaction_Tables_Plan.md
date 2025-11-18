# TMS - DATABASE TRANSACTION TABLES PLAN

**Created:** 18 November 2025  
**Last Updated:** 18 November 2025  
**Purpose:** Detailed planning untuk semua tabel transaksi TMS  
**Status:** Design Phase - UPDATED with Dispatcher & Accounting Integration

---

## OVERVIEW: TABEL TRANSAKSI vs MASTER DATA

### Klasifikasi Tabel

#### **MASTER DATA TABLES** (Static/Slow-changing)
- **Client & Location**: ms_client, ms_location, ms_route, ms_time_window
- **Fleet**: ms_driver, ms_vehicle, ms_truck_type
- **Tariff & Pricing** ⭐ NEW: ms_tariff_contract, ms_tariff_rate, ms_tariff_special_condition
- **Policy**: ms_driver_rest_policy, ms_cost_component
- **Tanker Specific** ⭐ NEW: ms_flowmeter_calibration
- **Purpose**: Reference data yang jarang berubah

#### **TRANSACTION TABLES** ⭐ (Dynamic/Frequent changes)
- **Planning**: ms_planning_weekly, ms_planning_assignment, ms_planning_approval
- **Dispatch** ⭐ REVISED: tr_tms_dispatcher_main, tr_tms_dispatcher_detail, tr_tms_dispatcher_shuttle
- **Execution**: ms_trip_execution, ms_gps_tracking, ms_pod
- **Tanker Operations** ⭐ NEW: tr_tms_tanker_delivery
- **Accounting Integration** ⭐ NEW: tr_acc_tms_transaksi_sales, tr_acc_tms_transaksi_kasir
- **Settlement**: ms_settlement, ms_settlement_detail, ms_driver_payment
- **Audit**: ms_dispatch_adjustment_log
- **Purpose**: Operational data yang berubah setiap hari

#### **RELATED DOCUMENTS**:
- 📄 **Dispatcher & Accounting**: `Transaction_Tables_Dispatcher_Accounting.md`
- 📄 **Shuttle Document Flow**: `Shuttle_Dispatcher_Document_Flow.md`
- 📄 **Tariff Structure**: `Tariff_Structure_Analysis.md`
- 📄 **Tanker Operations**: `Tanker_Truck_Volume_Measurement.md`

---

## ALUR TRANSAKSI TMS (Transaction Flow)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    TRANSACTION LIFECYCLE (UPDATED)                          │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  1. PLANNING (Jumat) → ms_planning_weekly                                   │
│     ├─ Client submit CO Planning Excel (AQUA weekly planning)              │
│     ├─ Import 412 shipments → 145 trips                                    │
│     ├─ Status: IMPORTED → VALIDATED → ASSIGNED → APPROVED                  │
│     └─ Link to: ms_planning_assignment                                     │
│                                                                             │
│  2. ASSIGNMENT (Sabtu) → ms_planning_assignment                             │
│     ├─ Auto-scheduling engine assign driver+truck                          │
│     ├─ Rest time compliance check (min 8 hours)                            │
│     ├─ Cost estimation & margin calculation                                │
│     ├─ Status: PENDING → AUTO_ASSIGNED → MANUAL_ADJUSTED → FINALIZED      │
│     └─ Link to: ms_planning_approval                                       │
│                                                                             │
│  3. APPROVAL (Minggu) → ms_planning_approval                                │
│     ├─ Supervisor review assignment results                                │
│     ├─ Check: rest violations, margin targets, resource utilization        │
│     ├─ Status: PENDING_APPROVAL → APPROVED → REJECTED                      │
│     └─ Trigger: Generate tr_tms_dispatcher_main ⭐                         │
│                                                                             │
│  4. DISPATCH ORDER (Senin Pagi) → tr_tms_dispatcher_main ⭐ NEW             │
│     ├─ "Surat Perintah Jalan" (SPJ) - Main transaction                     │
│     ├─ Contains: Route, Driver, Vehicle, Pricing, Uang Jalan, Uang Jasa   │
│     ├─ Status: DRAFT → APPROVED → DISPATCHED → COMPLETED → INVOICED       │
│     ├─ Triggers:                                                           │
│     │  ├─ APPROVED → Auto-create Uang Jalan (tr_acc_tms_transaksi_kasir)  │
│     │  └─ COMPLETED → Auto-create Invoice + Uang Jasa                      │
│     └─ Links to:                                                           │
│        ├─ tr_tms_dispatcher_shuttle (for shuttle-specific docs) ⭐ NEW    │
│        ├─ tr_tms_dispatcher_detail (for multi-drop)                        │
│        ├─ tr_tms_tanker_delivery (for volume measurement) ⭐ NEW          │
│        ├─ tr_acc_tms_transaksi_sales (customer invoice) ⭐ NEW            │
│        └─ tr_acc_tms_transaksi_kasir (driver payments) ⭐ NEW             │
│                                                                             │
│  5. SHUTTLE DOCUMENT FLOW (Senin) → tr_tms_dispatcher_shuttle ⭐ NEW       │
│     ├─ Serah Terima: Dispatcher → Driver (SPJ, CO)                         │
│     ├─ DN (Delivery Note): From Danone at loading                          │
│     ├─ BTB (Berita Terima Barang): From customer at delivery               │
│     ├─ Return Dokumen: Driver → Dispatcher (DN, BTB originals)             │
│     └─ Archive: Scan & file physical documents                             │
│                                                                             │
│  6. EXECUTION (Senin-Sabtu) → ms_trip_execution                             │
│     ├─ Driver check-in, start trip, POD upload                             │
│     ├─ Real-time GPS tracking                                              │
│     ├─ Performance tracking (on-time, fuel, distance)                      │
│     ├─ Status: PENDING → IN_PROGRESS → COMPLETED                           │
│     └─ Link to: ms_gps_tracking, ms_pod                                    │
│                                                                             │
│  7. GPS TRACKING (Real-time) → ms_gps_tracking                              │
│     ├─ Update interval: 30 sec (real-time mode)                            │
│     ├─ Volume: ~201,600 rows/week (HIGH VOLUME!)                           │
│     ├─ Track: location, speed, geofence                                    │
│     └─ Alert: speeding, route deviation, panic button                      │
│                                                                             │
│  8. PROOF OF DELIVERY → ms_pod                                              │
│     ├─ Upload: 4 photos, signature, receiver name                          │
│     ├─ Location verification (GPS matching)                                │
│     ├─ Timestamp & coordinates                                             │
│     └─ Status: UPLOADED → VERIFIED → APPROVED                              │
│                                                                             │
│  9. TANKER VOLUME MEASUREMENT → tr_tms_tanker_delivery ⭐ NEW              │
│     ├─ Loading: Flowmeter reading at plant (10,000 L)                      │
│     ├─ Unloading: Flowmeter reading at customer (9,850 L)                  │
│     ├─ Variance: 150 L (1.5% - within tolerance)                           │
│     ├─ Charge: 9,850 L × Rp 50/L = Rp 492,500                              │
│     └─ Photos: Flowmeter readings, delivery note                           │
│                                                                             │
│  10. ACCOUNTING INTEGRATION ⭐ NEW                                          │
│      ├─ tr_acc_tms_transaksi_sales (Customer Invoice)                      │
│      │  ├─ Trigger: Dispatch COMPLETED + POD verified                      │
│      │  ├─ Generate invoice with PPN 11%                                   │
│      │  ├─ Payment tracking (UNPAID → PAID)                                │
│      │  └─ Status: DRAFT → APPROVED → SENT → PAID                          │
│      │                                                                      │
│      └─ tr_acc_tms_transaksi_kasir (Driver Cash Transactions)              │
│         ├─ UANG JALAN (Cash Advance)                                       │
│         │  ├─ Trigger: Dispatch APPROVED                                   │
│         │  ├─ Amount: Fuel + Toll + Parking + Meal                         │
│         │  ├─ Disbursed before trip                                        │
│         │  └─ Reconcile after trip with receipts                           │
│         │                                                                   │
│         └─ UANG JASA (Driver Fee/Commission)                               │
│            ├─ Trigger: Trip COMPLETED                                      │
│            ├─ Calculation: Base + Bonus - Penalty                          │
│            ├─ Approval workflow                                            │
│            └─ Payment: Cash/Transfer                                       │
│                                                                             │
│  11. SETTLEMENT (Weekly/Monthly) → ms_settlement                            │
│      ├─ Calculate costs, revenue, driver salary                            │
│      ├─ Gross margin analysis                                              │
│      ├─ Approve payment                                                    │
│      ├─ Status: DRAFT → CALCULATED → VERIFIED → APPROVED → PAID            │
│      └─ Link to: ms_settlement_detail, ms_driver_payment                   │
│                                                                             │
│  12. ADJUSTMENT LOG (Anytime) → ms_dispatch_adjustment_log                  │
│      ├─ Track all changes: Driver, Vehicle, Route, Cancellation            │
│      ├─ Old vs New values with reason                                      │
│      ├─ Cost/revenue impact analysis                                       │
│      ├─ Approval workflow (Auto/Supervisor/Manager)                        │
│      └─ Complete audit trail                                               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

**See detailed documentation:**
- 📋 **Dispatch & Accounting**: `Transaction_Tables_Dispatcher_Accounting.md`
- 📋 **Shuttle Documents**: `Shuttle_Dispatcher_Document_Flow.md`
- 📋 **Tanker Operations**: `Tanker_Truck_Volume_Measurement.md`

---

## DETAIL TABEL TRANSAKSI (Transaction Tables Detail)

### 1. ms_planning_weekly (Weekly Planning from Client)

**Purpose**: Store CO Planning yang di-upload client setiap Jumat

**Lifecycle**: IMPORTED → VALIDATED → ASSIGNED → APPROVED

```sql
CREATE TABLE ms_planning_weekly (
    -- Primary Key
    planning_id VARCHAR(20) PRIMARY KEY,  -- PLN001, PLN002
    
    -- Client & Order Info
    client_id VARCHAR(20) NOT NULL,
    shipment_ref_no VARCHAR(50),  -- From Excel: S25111400788
    so_number VARCHAR(50),  -- SAP order: 4505414745
    order_type VARCHAR(20) DEFAULT 'STANDARD',  -- STANDARD, URGENT
    movement_type VARCHAR(50),  -- FACTORY_TO_DEPO, FACTORY_TO_DISTRIBUTOR
    
    -- Route & Schedule
    route_id VARCHAR(20) NOT NULL,
    window_id VARCHAR(20) NOT NULL,
    shipment_date DATE NOT NULL,
    pickup_start_time DATETIME NOT NULL,
    pickup_end_time DATETIME NOT NULL,
    estimated_arrival_time DATETIME,
    
    -- Load Info
    qty_cartons INT NOT NULL,
    total_weight_kg DECIMAL(10,2),
    total_volume_cbm DECIMAL(10,2),
    truck_type_id VARCHAR(20),
    equipment_type VARCHAR(50),  -- From Excel: TNWB_JUGRACK_GREEN-TIV
    
    -- Pre-assignment (optional dari Excel)
    pre_assigned_truck_id VARCHAR(20),  -- If Excel has Truck Id
    pre_assigned_driver_id VARCHAR(20),  -- If Excel has Driver Name
    is_pre_assigned BIT DEFAULT 0,
    
    -- Status & Workflow
    status VARCHAR(20) DEFAULT 'IMPORTED',
    -- Status flow: IMPORTED → ASSIGNED → APPROVED → REJECTED
    import_batch_id VARCHAR(20),  -- Group import session
    imported_at DATETIME,
    imported_by VARCHAR(20),
    assigned_at DATETIME,
    assigned_by VARCHAR(20),
    approved_at DATETIME,
    approved_by VARCHAR(20),
    
    -- Notes
    client_notes NVARCHAR(500),
    internal_notes NVARCHAR(500),
    rejection_reason NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    created_by VARCHAR(20),
    updated_by VARCHAR(20),
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (window_id) REFERENCES ms_time_window(window_id),
    FOREIGN KEY (truck_type_id) REFERENCES ms_truck_type(truck_type_id)
);

CREATE INDEX idx_planning_date ON ms_planning_weekly(shipment_date, status);
CREATE INDEX idx_planning_client ON ms_planning_weekly(client_id, shipment_date);
CREATE INDEX idx_planning_batch ON ms_planning_weekly(import_batch_id);
```

**Sample Data:**
```sql
INSERT INTO ms_planning_weekly VALUES (
    'PLN001', 'CUS001', 'S25111400788', '4505414745', 'STANDARD', 'FACTORY_TO_DEPO',
    'ROU001', 'WIN002', '2025-11-18', '2025-11-18 02:00', '2025-11-18 04:00', '2025-11-18 05:30',
    2900, 12000.00, 35.50, 'TRK001', 'TNWB_JUGRACK_GREEN-TIV',
    NULL, NULL, 0,  -- Not pre-assigned
    'IMPORTED', 'BATCH001', '2025-11-15 16:30', 'USER001',
    NULL, NULL, NULL, NULL,
    'Urgent delivery', NULL, NULL,
    GETDATE(), NULL, 'USER001', NULL
);
```

---

### 2. ms_planning_assignment (Assignment Result from Auto-Scheduling)

**Purpose**: Store hasil auto-scheduling (driver+truck assignment) untuk setiap planning

**Lifecycle**: PENDING → AUTO_ASSIGNED → MANUAL_ADJUSTED → FINALIZED

```sql
CREATE TABLE ms_planning_assignment (
    -- Primary Key
    assignment_id VARCHAR(20) PRIMARY KEY,  -- ASG001
    
    -- Link to Planning
    planning_id VARCHAR(20) NOT NULL,
    
    -- Resource Assignment
    driver_id VARCHAR(20) NOT NULL,
    vehicle_id VARCHAR(20) NOT NULL,
    helper_id VARCHAR(20),  -- Optional
    
    -- Assignment Method
    assignment_method VARCHAR(20),  -- AUTO, MANUAL, HYBRID
    assignment_score DECIMAL(5,2),  -- Scoring dari auto-scheduling (0-100)
    assignment_reason NVARCHAR(500),  -- Why this driver assigned?
    
    -- Assignment Details
    assigned_at DATETIME,
    assigned_by VARCHAR(20),
    manually_adjusted BIT DEFAULT 0,
    adjusted_at DATETIME,
    adjusted_by VARCHAR(20),
    adjustment_reason NVARCHAR(500),
    
    -- Cost Estimation (from auto-scheduling)
    estimated_fuel_cost DECIMAL(15,2),
    estimated_toll_cost DECIMAL(15,2),
    estimated_driver_salary DECIMAL(15,2),
    estimated_total_cost DECIMAL(15,2),
    estimated_revenue DECIMAL(15,2),
    estimated_margin DECIMAL(15,2),
    estimated_margin_percent DECIMAL(5,2),
    
    -- Rest Time Compliance ⭐ NEW
    previous_trip_id VARCHAR(20),  -- Link to last trip
    previous_trip_arrival DATETIME,  -- When previous trip ended
    required_rest_hours DECIMAL(4,2),  -- Min rest required (from policy)
    earliest_next_departure DATETIME,  -- Calculated allowed departure time
    actual_rest_hours DECIMAL(4,2),  -- Planning: estimated, Actual: real rest
    rest_compliance BIT,  -- 1 = compliant, 0 = violation
    rest_violation_reason NVARCHAR(200),
    
    -- Actual Rest Time (filled during execution)
    actual_pool_arrival DATETIME,  -- When driver actually arrived at pool
    actual_next_departure DATETIME,  -- When driver actually started next trip
    actual_rest_compliance BIT,
    actual_rest_violation_reason NVARCHAR(200),
    
    -- Fatigue Management
    is_long_trip BIT DEFAULT 0,  -- >8h or >500km
    is_overtime BIT DEFAULT 0,  -- >10h driving today
    is_night_shift BIT DEFAULT 0,  -- Depart 22:00-06:00
    cumulative_driving_hours_today DECIMAL(4,2),  -- Total driving hours today
    trip_sequence_today INT,  -- Trip ke-berapa hari ini (1, 2, 3)
    fatigue_risk_level VARCHAR(20),  -- LOW, MEDIUM, HIGH, CRITICAL
    
    -- Payment Calculation
    overtime_payment DECIMAL(15,2),
    night_shift_allowance DECIMAL(15,2),
    
    -- Backhaul Opportunity
    has_backhaul BIT DEFAULT 0,
    backhaul_route_id VARCHAR(20),
    backhaul_revenue DECIMAL(15,2),
    
    -- Status
    status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → AUTO_ASSIGNED → MANUAL_ADJUSTED → FINALIZED → REJECTED
    
    -- Notification
    notification_sent BIT DEFAULT 0,
    notification_sent_at DATETIME,
    driver_confirmed BIT DEFAULT 0,
    driver_confirmed_at DATETIME,
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (planning_id) REFERENCES ms_planning_weekly(planning_id),
    FOREIGN KEY (driver_id) REFERENCES ms_driver(driver_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(vehicle_id),
    FOREIGN KEY (helper_id) REFERENCES ms_driver(driver_id)
);

CREATE INDEX idx_assignment_planning ON ms_planning_assignment(planning_id);
CREATE INDEX idx_assignment_driver ON ms_planning_assignment(driver_id, assigned_at);
CREATE INDEX idx_assignment_vehicle ON ms_planning_assignment(vehicle_id, assigned_at);
CREATE INDEX idx_assignment_status ON ms_planning_assignment(status);
CREATE INDEX idx_assignment_rest ON ms_planning_assignment(rest_compliance, fatigue_risk_level);
```

**Sample Data:**
```sql
INSERT INTO ms_planning_assignment VALUES (
    'ASG001', 'PLN001', 
    'DRV001', 'VEH010', NULL,
    'AUTO', 95.00, 'Best score: High familiarity (50 trips), adequate rest (11.5h)',
    '2025-11-16 10:15', 'SYSTEM',
    0, NULL, NULL, NULL,  -- Not manually adjusted
    150000, 15000, 350000, 515000, 800000, 285000, 35.63,  -- Cost estimation
    'ASG998', '2025-11-17 18:30', 4.0, '2025-11-17 22:30', 11.5, 1, NULL,  -- Rest compliance OK
    NULL, NULL, 0, NULL,  -- Actual rest (will be filled during execution)
    0, 0, 0, 2.5, 1, 'LOW',  -- Fatigue: low risk
    0, 0,  -- No overtime/night shift payment
    0, NULL, NULL,  -- No backhaul
    'AUTO_ASSIGNED',
    0, NULL, 0, NULL,
    GETDATE(), NULL
);
```

---

### 3. ms_planning_approval (Supervisor Approval Process)

**Purpose**: Track supervisor approval untuk planning yang sudah di-assign

**Lifecycle**: PENDING_APPROVAL → APPROVED → REJECTED → APPROVED_WITH_NOTES

```sql
CREATE TABLE ms_planning_approval (
    -- Primary Key
    approval_id VARCHAR(20) PRIMARY KEY,  -- APV001
    
    -- Link to Assignment
    assignment_id VARCHAR(20) NOT NULL,
    
    -- Approval Info
    submitted_for_approval_at DATETIME,
    submitted_by VARCHAR(20),
    
    -- Reviewer
    reviewer_id VARCHAR(20),  -- Supervisor/Manager
    reviewed_at DATETIME,
    review_status VARCHAR(20),  -- PENDING, APPROVED, REJECTED, APPROVED_WITH_NOTES
    review_notes NVARCHAR(1000),
    
    -- Approval Details
    auto_assignment_ratio DECIMAL(5,2),  -- % auto vs manual
    total_trips INT,
    total_drivers INT,
    total_revenue DECIMAL(15,2),
    total_cost DECIMAL(15,2),
    total_margin DECIMAL(15,2),
    margin_percentage DECIMAL(5,2),
    margin_target_met BIT,  -- 1 if >= target
    
    -- Risk Flags
    has_rest_violations BIT DEFAULT 0,
    rest_violation_count INT DEFAULT 0,
    has_high_fatigue_risk BIT DEFAULT 0,
    high_fatigue_count INT DEFAULT 0,
    has_cost_overrun BIT DEFAULT 0,
    cost_overrun_amount DECIMAL(15,2),
    
    -- Approval Decision
    approved_trip_count INT,
    rejected_trip_count INT,
    modification_required BIT DEFAULT 0,
    modification_notes NVARCHAR(1000),
    
    -- Post-Approval Actions
    dispatch_generated BIT DEFAULT 0,
    dispatch_generated_at DATETIME,
    notification_sent BIT DEFAULT 0,
    notification_sent_at DATETIME,
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (assignment_id) REFERENCES ms_planning_assignment(assignment_id)
);

CREATE INDEX idx_approval_assignment ON ms_planning_approval(assignment_id);
CREATE INDEX idx_approval_status ON ms_planning_approval(review_status);
CREATE INDEX idx_approval_reviewer ON ms_planning_approval(reviewer_id, reviewed_at);
```

---

### 4. ms_dispatch (Dispatch Order - Final Execution Order)

**Purpose**: Dispatch order yang di-generate setelah approval, dikirim ke driver

**Lifecycle**: DISPATCHED → CONFIRMED → IN_PROGRESS → COMPLETED → CANCELLED

```sql
CREATE TABLE ms_dispatch (
    -- Primary Key
    dispatch_id VARCHAR(20) PRIMARY KEY,  -- DSP001
    dispatch_number VARCHAR(50) UNIQUE,  -- Auto-generated: DSP-20251118-001
    
    -- Link to Planning & Assignment
    planning_id VARCHAR(20) NOT NULL,
    assignment_id VARCHAR(20) NOT NULL,
    approval_id VARCHAR(20),
    
    -- Route & Schedule
    route_id VARCHAR(20) NOT NULL,
    origin_location_id VARCHAR(20) NOT NULL,
    destination_location_id VARCHAR(20) NOT NULL,
    window_id VARCHAR(20) NOT NULL,
    
    scheduled_date DATE NOT NULL,
    scheduled_departure DATETIME NOT NULL,
    scheduled_arrival DATETIME NOT NULL,
    
    -- Resources
    driver_id VARCHAR(20) NOT NULL,
    vehicle_id VARCHAR(20) NOT NULL,
    helper_id VARCHAR(20),
    
    -- Load Info
    qty_cartons INT NOT NULL,
    total_weight_kg DECIMAL(10,2),
    total_volume_cbm DECIMAL(10,2),
    
    -- Instructions
    special_instructions NVARCHAR(1000),
    client_notes NVARCHAR(500),
    safety_notes NVARCHAR(500),
    
    -- Status & Timeline
    status VARCHAR(20) DEFAULT 'DISPATCHED',
    -- Status: DISPATCHED → CONFIRMED → IN_PROGRESS → COMPLETED → CANCELLED
    
    dispatched_at DATETIME,
    dispatched_by VARCHAR(20),
    
    confirmed_at DATETIME,  -- Driver confirmed receipt
    confirmed_by VARCHAR(20),
    
    started_at DATETIME,  -- Driver started trip
    completed_at DATETIME,  -- Driver completed trip
    
    cancelled_at DATETIME,
    cancelled_by VARCHAR(20),
    cancellation_reason NVARCHAR(500),
    
    -- Notification
    notification_sent BIT DEFAULT 0,
    notification_sent_at DATETIME,
    notification_method VARCHAR(50),  -- APP, SMS, WA, EMAIL
    
    -- Financial
    agreed_rate DECIMAL(15,2),
    estimated_cost DECIMAL(15,2),
    estimated_margin DECIMAL(15,2),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (planning_id) REFERENCES ms_planning_weekly(planning_id),
    FOREIGN KEY (assignment_id) REFERENCES ms_planning_assignment(assignment_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (driver_id) REFERENCES ms_driver(driver_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(vehicle_id)
);

CREATE INDEX idx_dispatch_date ON ms_dispatch(scheduled_date, status);
CREATE INDEX idx_dispatch_driver ON ms_dispatch(driver_id, scheduled_date);
CREATE INDEX idx_dispatch_vehicle ON ms_dispatch(vehicle_id, scheduled_date);
CREATE INDEX idx_dispatch_status ON ms_dispatch(status);
CREATE INDEX idx_dispatch_number ON ms_dispatch(dispatch_number);
```

---

### 5. ms_trip_execution (Real Trip Execution - Actual Data)

**Purpose**: Track actual execution dari dispatch order (real data vs planning)

**Lifecycle**: PENDING → CHECKED_IN → IN_PROGRESS → ARRIVED → UNLOADING → COMPLETED

```sql
CREATE TABLE ms_trip_execution (
    -- Primary Key
    execution_id VARCHAR(20) PRIMARY KEY,  -- EXE001
    
    -- Link to Dispatch
    dispatch_id VARCHAR(20) NOT NULL,
    
    -- Actual Timeline
    actual_check_in_time DATETIME,  -- Driver check-in at origin
    actual_departure_time DATETIME,  -- Truck left origin
    actual_arrival_time DATETIME,  -- Truck arrived at destination
    actual_unloading_start DATETIME,
    actual_unloading_end DATETIME,
    actual_completion_time DATETIME,  -- POD uploaded, trip complete
    actual_pool_return_time DATETIME,  -- Driver back to pool
    
    -- Actual Route
    actual_distance_km DECIMAL(10,2),  -- From GPS tracking
    actual_duration_hours DECIMAL(5,2),
    actual_idle_time_minutes INT,  -- Time stopped (not moving)
    actual_driving_time_hours DECIMAL(5,2),
    
    -- Performance Metrics
    planned_vs_actual_variance_minutes INT,  -- Delay/early
    on_time_departure BIT,  -- Within 15 min window
    on_time_arrival BIT,  -- Within 30 min window
    
    -- Fuel Consumption
    fuel_at_departure_liters DECIMAL(10,2),
    fuel_at_arrival_liters DECIMAL(10,2),
    fuel_consumed_liters DECIMAL(10,2),
    fuel_efficiency_km_per_liter DECIMAL(5,2),
    
    -- Odometer
    odometer_start_km DECIMAL(10,2),
    odometer_end_km DECIMAL(10,2),
    
    -- GPS Tracking Summary
    total_gps_points INT,
    route_deviation_count INT,  -- How many times off-route
    max_speed_kmh DECIMAL(5,2),
    avg_speed_kmh DECIMAL(5,2),
    speeding_violations_count INT,  -- Exceeded speed limit
    geofence_alerts_count INT,
    
    -- Issues & Incidents
    has_issues BIT DEFAULT 0,
    issue_description NVARCHAR(1000),
    issue_reported_at DATETIME,
    issue_resolved BIT DEFAULT 0,
    issue_resolution NVARCHAR(1000),
    
    -- Delay Analysis
    is_delayed BIT DEFAULT 0,
    delay_minutes INT,
    delay_reason VARCHAR(100),  -- TRAFFIC, BREAKDOWN, WEATHER, LOADING_DELAY
    delay_notes NVARCHAR(500),
    
    -- Status
    status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → CHECKED_IN → IN_PROGRESS → ARRIVED → UNLOADING → COMPLETED
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
);

CREATE INDEX idx_execution_dispatch ON ms_trip_execution(dispatch_id);
CREATE INDEX idx_execution_status ON ms_trip_execution(status);
CREATE INDEX idx_execution_date ON ms_trip_execution(actual_departure_time);
CREATE INDEX idx_execution_performance ON ms_trip_execution(on_time_departure, on_time_arrival);
```

---

### 6. ms_gps_tracking (Real-time GPS Tracking Data)

**Purpose**: Store GPS coordinates setiap 30 detik (real-time mode)

**Lifecycle**: Continuous insert during trip execution

**Data Volume**: ~120 rows per hour per truck (30 sec interval)

```sql
CREATE TABLE ms_gps_tracking (
    -- Primary Key
    gps_id BIGINT IDENTITY(1,1) PRIMARY KEY,  -- Auto-increment
    
    -- Link to Execution
    execution_id VARCHAR(20) NOT NULL,
    dispatch_id VARCHAR(20) NOT NULL,
    driver_id VARCHAR(20) NOT NULL,
    vehicle_id VARCHAR(20) NOT NULL,
    
    -- GPS Data
    latitude DECIMAL(10,8) NOT NULL,  -- -6.12345678
    longitude DECIMAL(11,8) NOT NULL,  -- 106.12345678
    altitude_meters DECIMAL(8,2),
    accuracy_meters DECIMAL(5,2),  -- GPS accuracy (lower = better)
    
    -- Movement Data
    speed_kmh DECIMAL(5,2),
    heading_degrees DECIMAL(5,2),  -- 0-360 (compass direction)
    is_moving BIT,  -- Speed > 5 km/h
    
    -- Timestamp
    gps_timestamp DATETIME NOT NULL,  -- Device time
    server_received_at DATETIME DEFAULT GETDATE(),  -- Server time
    
    -- Device Info
    device_id VARCHAR(50),  -- Mobile IMEI or GPS device ID
    battery_level INT,  -- 0-100%
    signal_strength INT,  -- 0-100%
    
    -- Location Context
    nearest_landmark VARCHAR(100),  -- Geocoded address
    is_in_geofence BIT DEFAULT 0,
    geofence_id VARCHAR(20),
    
    -- Alerts
    is_speeding BIT DEFAULT 0,  -- Exceeded route speed limit
    is_off_route BIT DEFAULT 0,  -- Deviated from planned route
    is_idle_too_long BIT DEFAULT 0,  -- Stopped > 30 min
    panic_button_pressed BIT DEFAULT 0,  -- Emergency alert
    
    -- Data Source
    data_source VARCHAR(20),  -- MOBILE_APP, GPS_DEVICE
    
    FOREIGN KEY (execution_id) REFERENCES ms_trip_execution(execution_id),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
);

-- Clustered index on timestamp for fast time-range queries
CREATE CLUSTERED INDEX idx_gps_timestamp ON ms_gps_tracking(gps_timestamp);
CREATE INDEX idx_gps_execution ON ms_gps_tracking(execution_id, gps_timestamp);
CREATE INDEX idx_gps_dispatch ON ms_gps_tracking(dispatch_id, gps_timestamp);
CREATE INDEX idx_gps_alerts ON ms_gps_tracking(is_speeding, is_off_route, panic_button_pressed);

-- Partition by month for performance (optional, for high volume)
-- ALTER TABLE ms_gps_tracking PARTITION BY MONTH(gps_timestamp);
```

**Data Retention Policy:**
- **Real-time**: Keep last 7 days in hot storage
- **Archive**: Move 8-30 days data to warm storage
- **Historical**: Move 31-365 days to cold storage
- **Purge**: Delete data > 1 year (except incidents)

---

### 7. ms_pod (Proof of Delivery)

**Purpose**: Store POD dokumen (photo, signature, receiver info)

**Lifecycle**: UPLOADED → VERIFIED → APPROVED → REJECTED

```sql
CREATE TABLE ms_pod (
    -- Primary Key
    pod_id VARCHAR(20) PRIMARY KEY,  -- POD001
    
    -- Link to Execution
    execution_id VARCHAR(20) NOT NULL,
    dispatch_id VARCHAR(20) NOT NULL,
    
    -- Delivery Info
    delivered_at DATETIME NOT NULL,
    delivered_by VARCHAR(20),  -- Driver ID
    
    -- Receiver Info
    receiver_name NVARCHAR(100) NOT NULL,
    receiver_phone VARCHAR(20),
    receiver_position VARCHAR(50),  -- Manager, Staff, Security
    receiver_signature_image VARCHAR(200),  -- File path or blob
    
    -- Photo Evidence
    photo_1_path VARCHAR(200),  -- Unloading photo
    photo_2_path VARCHAR(200),  -- Truck at location
    photo_3_path VARCHAR(200),  -- Goods condition
    photo_4_path VARCHAR(200),  -- Additional
    
    -- Delivery Details
    qty_delivered INT NOT NULL,
    qty_damaged INT DEFAULT 0,
    qty_rejected INT DEFAULT 0,
    damage_notes NVARCHAR(500),
    
    -- Location Verification
    delivery_latitude DECIMAL(10,8),
    delivery_longitude DECIMAL(11,8),
    is_location_verified BIT,  -- Within geofence?
    location_variance_meters DECIMAL(10,2),
    
    -- Condition
    goods_condition VARCHAR(20),  -- GOOD, DAMAGED, PARTIAL
    condition_notes NVARCHAR(500),
    
    -- Status & Approval
    status VARCHAR(20) DEFAULT 'UPLOADED',
    -- Status: UPLOADED → VERIFIED → APPROVED → REJECTED
    
    uploaded_at DATETIME DEFAULT GETDATE(),
    uploaded_by VARCHAR(20),
    
    verified_at DATETIME,
    verified_by VARCHAR(20),
    verification_notes NVARCHAR(500),
    
    rejected_at DATETIME,
    rejected_by VARCHAR(20),
    rejection_reason NVARCHAR(500),
    
    -- Dispute
    is_disputed BIT DEFAULT 0,
    dispute_reason NVARCHAR(500),
    dispute_resolved BIT DEFAULT 0,
    dispute_resolution NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (execution_id) REFERENCES ms_trip_execution(execution_id),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
);

CREATE INDEX idx_pod_execution ON ms_pod(execution_id);
CREATE INDEX idx_pod_dispatch ON ms_pod(dispatch_id);
CREATE INDEX idx_pod_status ON ms_pod(status);
CREATE INDEX idx_pod_date ON ms_pod(delivered_at);
```

---

### 8. ms_dispatch_adjustment_log (Real-time Adjustment Tracking)

**Purpose**: Track semua perubahan dispatch order (driver sick, truck rusak, etc)

**Lifecycle**: Created whenever dispatcher makes adjustment

```sql
CREATE TABLE ms_dispatch_adjustment_log (
    -- Primary Key
    adjustment_id VARCHAR(20) PRIMARY KEY,  -- ADJ001
    
    -- Link to Dispatch
    dispatch_id VARCHAR(20) NOT NULL,
    
    -- Adjustment Info
    adjustment_type VARCHAR(50) NOT NULL,
    -- Types: DRIVER_CHANGE, VEHICLE_CHANGE, ROUTE_CHANGE, SCHEDULE_CHANGE,
    --        CANCELLATION, SPLIT_ORDER, PRODUCT_CHANGE, HELPER_CHANGE
    
    adjustment_reason NVARCHAR(500) NOT NULL,
    urgency_level VARCHAR(20),  -- LOW, MEDIUM, HIGH, CRITICAL
    
    -- Old Values (Before Change)
    old_driver_id VARCHAR(20),
    old_vehicle_id VARCHAR(20),
    old_route_id VARCHAR(20),
    old_window_id VARCHAR(20),
    old_scheduled_date DATE,
    old_scheduled_departure DATETIME,
    
    -- New Values (After Change)
    new_driver_id VARCHAR(20),
    new_vehicle_id VARCHAR(20),
    new_route_id VARCHAR(20),
    new_window_id VARCHAR(20),
    new_scheduled_date DATE,
    new_scheduled_departure DATETIME,
    
    -- Impact Analysis
    cost_impact DECIMAL(15,2),  -- Additional cost from change
    revenue_impact DECIMAL(15,2),
    margin_impact DECIMAL(15,2),
    time_impact_minutes INT,  -- Delay caused
    
    -- Approval Workflow
    requires_approval BIT DEFAULT 0,
    approval_threshold VARCHAR(20),  -- AUTO, SUPERVISOR, MANAGER, DIRECTOR
    
    approved BIT DEFAULT 0,
    approved_at DATETIME,
    approved_by VARCHAR(20),
    approval_notes NVARCHAR(500),
    
    rejected BIT DEFAULT 0,
    rejected_at DATETIME,
    rejected_by VARCHAR(20),
    rejection_reason NVARCHAR(500),
    
    -- Adjustment Made By
    adjusted_at DATETIME DEFAULT GETDATE(),
    adjusted_by VARCHAR(20) NOT NULL,  -- Dispatcher ID
    
    -- Notification
    driver_notified BIT DEFAULT 0,
    driver_notified_at DATETIME,
    client_notified BIT DEFAULT 0,
    client_notified_at DATETIME,
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
);

CREATE INDEX idx_adjustment_dispatch ON ms_dispatch_adjustment_log(dispatch_id);
CREATE INDEX idx_adjustment_type ON ms_dispatch_adjustment_log(adjustment_type);
CREATE INDEX idx_adjustment_date ON ms_dispatch_adjustment_log(adjusted_at);
CREATE INDEX idx_adjustment_approval ON ms_dispatch_adjustment_log(requires_approval, approved);
```

---

### 9. ms_settlement (Weekly Settlement Calculation)

**Purpose**: Calculate weekly settlement untuk revenue, cost, driver salary

**Lifecycle**: DRAFT → CALCULATED → VERIFIED → APPROVED → PAID

```sql
CREATE TABLE ms_settlement (
    -- Primary Key
    settlement_id VARCHAR(20) PRIMARY KEY,  -- STL001
    settlement_number VARCHAR(50) UNIQUE,  -- STL-2025-W47
    
    -- Period
    settlement_period VARCHAR(20),  -- WEEKLY, MONTHLY
    period_start_date DATE NOT NULL,
    period_end_date DATE NOT NULL,
    week_number INT,
    month_number INT,
    year_number INT,
    
    -- Client (optional, can be per-client or all-clients)
    client_id VARCHAR(20),
    
    -- Summary
    total_trips INT,
    completed_trips INT,
    cancelled_trips INT,
    completion_rate DECIMAL(5,2),  -- %
    
    -- Revenue
    total_revenue DECIMAL(15,2),
    revenue_from_shuttle DECIMAL(15,2),
    revenue_from_backhaul DECIMAL(15,2),
    revenue_from_adhoc DECIMAL(15,2),
    
    -- Costs
    total_cost DECIMAL(15,2),
    fuel_cost DECIMAL(15,2),
    toll_cost DECIMAL(15,2),
    driver_salary_cost DECIMAL(15,2),
    helper_salary_cost DECIMAL(15,2),
    maintenance_cost DECIMAL(15,2),
    overhead_cost DECIMAL(15,2),
    
    -- Margin
    gross_margin DECIMAL(15,2),
    gross_margin_percent DECIMAL(5,2),
    net_margin DECIMAL(15,2),
    net_margin_percent DECIMAL(5,2),
    
    -- Status
    status VARCHAR(20) DEFAULT 'DRAFT',
    -- Status: DRAFT → CALCULATED → VERIFIED → APPROVED → PAID
    
    calculated_at DATETIME,
    calculated_by VARCHAR(20),
    
    verified_at DATETIME,
    verified_by VARCHAR(20),
    verification_notes NVARCHAR(500),
    
    approved_at DATETIME,
    approved_by VARCHAR(20),
    
    paid_at DATETIME,
    paid_by VARCHAR(20),
    payment_reference VARCHAR(100),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id)
);

CREATE INDEX idx_settlement_period ON ms_settlement(period_start_date, period_end_date);
CREATE INDEX idx_settlement_client ON ms_settlement(client_id, period_start_date);
CREATE INDEX idx_settlement_status ON ms_settlement(status);
```

---

### 10. ms_settlement_detail (Detail per Trip in Settlement)

**Purpose**: Detail breakdown per trip dalam settlement

```sql
CREATE TABLE ms_settlement_detail (
    -- Primary Key
    settlement_detail_id VARCHAR(20) PRIMARY KEY,
    
    -- Link to Settlement & Trip
    settlement_id VARCHAR(20) NOT NULL,
    dispatch_id VARCHAR(20) NOT NULL,
    execution_id VARCHAR(20),
    
    -- Trip Info
    trip_date DATE,
    route_id VARCHAR(20),
    driver_id VARCHAR(20),
    vehicle_id VARCHAR(20),
    
    -- Revenue
    trip_revenue DECIMAL(15,2),
    revenue_type VARCHAR(20),  -- SHUTTLE, BACKHAUL, ADHOC
    
    -- Costs
    trip_fuel_cost DECIMAL(15,2),
    trip_toll_cost DECIMAL(15,2),
    trip_driver_salary DECIMAL(15,2),
    trip_helper_salary DECIMAL(15,2),
    trip_total_cost DECIMAL(15,2),
    
    -- Margin
    trip_margin DECIMAL(15,2),
    trip_margin_percent DECIMAL(5,2),
    
    -- Performance
    on_time BIT,
    has_issues BIT,
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (settlement_id) REFERENCES ms_settlement(settlement_id),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
);

CREATE INDEX idx_settlement_detail_settlement ON ms_settlement_detail(settlement_id);
CREATE INDEX idx_settlement_detail_dispatch ON ms_settlement_detail(dispatch_id);
```

---

### 11. ms_driver_payment (Driver Salary Payment)

**Purpose**: Track driver salary payment per settlement period

```sql
CREATE TABLE ms_driver_payment (
    -- Primary Key
    payment_id VARCHAR(20) PRIMARY KEY,
    
    -- Link to Settlement & Driver
    settlement_id VARCHAR(20) NOT NULL,
    driver_id VARCHAR(20) NOT NULL,
    
    -- Period
    period_start_date DATE,
    period_end_date DATE,
    
    -- Trip Summary
    total_trips INT,
    completed_trips INT,
    cancelled_trips INT,
    total_distance_km DECIMAL(10,2),
    total_driving_hours DECIMAL(8,2),
    
    -- Base Salary
    base_salary DECIMAL(15,2),
    salary_per_trip DECIMAL(15,2),
    salary_per_km DECIMAL(10,2),
    
    -- Allowances
    overtime_allowance DECIMAL(15,2),
    night_shift_allowance DECIMAL(15,2),
    long_haul_allowance DECIMAL(15,2),
    performance_bonus DECIMAL(15,2),
    
    -- Deductions
    penalty_late DECIMAL(15,2),
    penalty_damage DECIMAL(15,2),
    penalty_absence DECIMAL(15,2),
    other_deductions DECIMAL(15,2),
    
    -- Total
    gross_salary DECIMAL(15,2),
    total_deductions DECIMAL(15,2),
    net_salary DECIMAL(15,2),
    
    -- Payment
    payment_status VARCHAR(20),  -- PENDING, PAID, HOLD
    payment_method VARCHAR(20),  -- BANK_TRANSFER, CASH, CHECK
    payment_date DATE,
    payment_reference VARCHAR(100),
    
    -- Notes
    payment_notes NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (settlement_id) REFERENCES ms_settlement(settlement_id),
    FOREIGN KEY (driver_id) REFERENCES ms_driver(driver_id)
);

CREATE INDEX idx_driver_payment_settlement ON ms_driver_payment(settlement_id);
CREATE INDEX idx_driver_payment_driver ON ms_driver_payment(driver_id, period_start_date);
CREATE INDEX idx_driver_payment_status ON ms_driver_payment(payment_status);
```

---

## TRANSACTION TABLE SUMMARY (UPDATED)

### Core Transaction Tables

| # | Table Name | Purpose | Avg Rows/Week | Retention | Document |
|---|-----------|---------|---------------|-----------|----------|
| 1 | ms_planning_weekly | CO Planning import | 150 | 2 years | This doc |
| 2 | ms_planning_assignment | Auto-scheduling result | 150 | 2 years | This doc |
| 3 | ms_planning_approval | Supervisor approval | 1 per week | 2 years | This doc |
| **4** | **tr_tms_dispatcher_main** ⭐ | **Main dispatch order (SPJ)** | **150** | **2 years** | **Transaction_Tables_Dispatcher_Accounting.md** |
| 5 | tr_tms_dispatcher_detail | Multi-drop breakdown | ~50 | 2 years | Transaction_Tables_Dispatcher_Accounting.md |
| **6** | **tr_tms_dispatcher_shuttle** ⭐ | **Shuttle doc flow (CO/DN/BTB)** | **150** | **2 years** | **Shuttle_Dispatcher_Document_Flow.md** |
| 7 | ms_trip_execution | Actual execution | 150 | 2 years | This doc |
| 8 | ms_gps_tracking | GPS coordinates | ~201,600 | 1 year ⚠️ | This doc |
| 9 | ms_pod | Proof of delivery | 150 | 5 years | This doc |
| **10** | **tr_tms_tanker_delivery** ⭐ | **Volume measurement (tanker)** | **~20** | **2 years** | **Tanker_Truck_Volume_Measurement.md** |

### Accounting Integration Tables ⭐ NEW

| # | Table Name | Purpose | Avg Rows/Week | Retention | Document |
|---|-----------|---------|---------------|-----------|----------|
| **11** | **tr_acc_tms_transaksi_sales** ⭐ | **Customer invoice (AR)** | **150** | **Permanent** | **Transaction_Tables_Dispatcher_Accounting.md** |
| **12** | **tr_acc_tms_transaksi_kasir** ⭐ | **Uang Jalan + Uang Jasa** | **~300** | **Permanent** | **Transaction_Tables_Dispatcher_Accounting.md** |

### Settlement Tables

| # | Table Name | Purpose | Avg Rows/Week | Retention | Document |
|---|-----------|---------|---------------|-----------|----------|
| 13 | ms_settlement | Weekly settlement | 1 | Permanent | This doc |
| 14 | ms_settlement_detail | Settlement detail | 150 | Permanent | This doc |
| 15 | ms_driver_payment | Driver salary | ~12 | Permanent | This doc |
| 16 | ms_dispatch_adjustment_log | Audit trail | ~50 | 2 years | This doc |

### Master Data Tables (Tariff) ⭐ NEW

| # | Table Name | Purpose | Avg Rows | Document |
|---|-----------|---------|----------|----------|
| **17** | **ms_tariff_contract** ⭐ | **Tariff contracts** | **~20** | **Tariff_Structure_Analysis.md** |
| **18** | **ms_tariff_rate** ⭐ | **Route-specific rates** | **~500** | **Tariff_Structure_Analysis.md** |
| 19 | ms_tariff_special_condition | Special pricing rules | ~100 | Tariff_Structure_Analysis.md |
| **20** | **ms_flowmeter_calibration** ⭐ | **Tanker flowmeter certs** | **~20** | **Tanker_Truck_Volume_Measurement.md** |

**⚠️ High Volume Table**: ms_gps_tracking
- **Volume**: ~120 rows/hour/truck × 20 trucks × 12 hours/day × 7 days = ~201,600 rows/week
- **Storage**: ~10 MB/week
- **Optimization**: Partitioning by month, archive old data

**Total Tables**: 20 (11 existing + 9 new)  
**Total Weekly Rows**: ~152,900 rows/week  
**Storage Growth**: ~50 MB/week

---

## KEY RELATIONSHIPS (UPDATED)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        TABLE RELATIONSHIPS                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ms_client (1) ──┬─→ ms_planning_weekly (N)                                │
│                  ├─→ ms_tariff_contract (N) ⭐ NEW                          │
│                  └─→ tr_tms_dispatcher_main (N)                             │
│                                                                             │
│  ms_route (1) ───┬─→ ms_planning_weekly (N)                                │
│                  ├─→ ms_tariff_rate (N) ⭐ NEW                              │
│                  └─→ tr_tms_dispatcher_main (N)                             │
│                                                                             │
│  ms_tariff_contract (1) ──→ ms_tariff_rate (N) ⭐ NEW                       │
│  ms_tariff_rate (1) ──→ ms_tariff_special_condition (N)                     │
│                                                                             │
│  ms_planning_weekly (1) ──┬─→ ms_planning_assignment (N)                   │
│                           └─→ tr_tms_dispatcher_shuttle (1) ⭐ NEW          │
│                                                                             │
│  ms_planning_assignment (1) ──→ tr_tms_dispatcher_main (1) ⭐ UPDATED       │
│                                                                             │
│  tr_tms_dispatcher_main (1) ──┬─→ tr_tms_dispatcher_detail (N)             │
│                               ├─→ tr_tms_dispatcher_shuttle (1) ⭐ 1-to-1  │
│                               ├─→ tr_tms_tanker_delivery (1) ⭐ NEW        │
│                               ├─→ tr_acc_tms_transaksi_sales (1) ⭐ NEW   │
│                               ├─→ tr_acc_tms_transaksi_kasir (2) ⭐ NEW   │
│                               │   ├─ Uang Jalan (1)                        │
│                               │   └─ Uang Jasa (1)                         │
│                               ├─→ ms_trip_execution (1)                     │
│                               └─→ ms_dispatch_adjustment_log (N)            │
│                                                                             │
│  ms_trip_execution (1) ──┬─→ ms_gps_tracking (N)                           │
│                          └─→ ms_pod (1)                                    │
│                                                                             │
│  ms_settlement (1) ──┬─→ ms_settlement_detail (N)                          │
│                      └─→ ms_driver_payment (N)                             │
│                                                                             │
│  ms_vehicle (1) ──→ ms_flowmeter_calibration (N) ⭐ NEW (tanker only)      │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## MIGRATION EXECUTION PLAN (UPDATED)

### Phase 1: Master Data - Tariff Tables (Week 1) ⭐ NEW
```bash
# Create tariff tables first (referenced by dispatch)
php artisan migrate --path=database/migrations/2025_11_18_011_create_ms_tariff_contract_table.php
php artisan migrate --path=database/migrations/2025_11_18_012_create_ms_tariff_rate_table.php
php artisan migrate --path=database/migrations/2025_11_18_013_create_ms_tariff_special_condition_table.php
php artisan migrate --path=database/migrations/2025_11_18_014_create_ms_flowmeter_calibration_table.php
```

### Phase 2: Planning Tables (Week 1)
```bash
# Create planning tables
php artisan migrate --path=database/migrations/2025_11_18_001_create_ms_planning_weekly_table.php
php artisan migrate --path=database/migrations/2025_11_18_002_create_ms_planning_assignment_table.php
php artisan migrate --path=database/migrations/2025_11_18_003_create_ms_planning_approval_table.php
```

### Phase 3: Dispatcher Tables (Week 2) ⭐ UPDATED
```bash
# Create main dispatcher table (replaces old ms_dispatch)
php artisan migrate --path=database/migrations/2025_11_18_004_create_tr_tms_dispatcher_main_table.php
php artisan migrate --path=database/migrations/2025_11_18_005_create_tr_tms_dispatcher_detail_table.php
php artisan migrate --path=database/migrations/2025_11_18_006_create_tr_tms_dispatcher_shuttle_table.php
php artisan migrate --path=database/migrations/2025_11_18_007_create_tr_tms_tanker_delivery_table.php
```

### Phase 4: Accounting Integration (Week 2) ⭐ NEW
```bash
# Create accounting tables
php artisan migrate --path=database/migrations/2025_11_18_008_create_tr_acc_tms_transaksi_sales_table.php
php artisan migrate --path=database/migrations/2025_11_18_009_create_tr_acc_tms_transaksi_kasir_table.php
```

### Phase 5: Execution Tables (Week 2)
```bash
php artisan migrate --path=database/migrations/2025_11_18_020_create_ms_trip_execution_table.php
php artisan migrate --path=database/migrations/2025_11_18_021_create_ms_gps_tracking_table.php
php artisan migrate --path=database/migrations/2025_11_18_022_create_ms_pod_table.php
```

### Phase 6: Settlement & Audit (Week 3)
```bash
php artisan migrate --path=database/migrations/2025_11_18_030_create_ms_settlement_table.php
php artisan migrate --path=database/migrations/2025_11_18_031_create_ms_settlement_detail_table.php
php artisan migrate --path=database/migrations/2025_11_18_032_create_ms_driver_payment_table.php
php artisan migrate --path=database/migrations/2025_11_18_033_create_ms_dispatch_adjustment_log_table.php
```

### Phase 7: Triggers & Functions (Week 3) ⭐ NEW
```bash
# Create auto-triggers for accounting integration
# - Dispatch APPROVED → Create Uang Jalan
# - Dispatch COMPLETED + POD → Create Invoice + Uang Jasa
php artisan db:seed --class=CreateAccountingTriggersSeeder
```

### Phase 3: Execution Tables (Week 2)
```bash
php artisan migrate --path=database/migrations/2025_11_18_006_create_ms_trip_execution_table.php
php artisan migrate --path=database/migrations/2025_11_18_007_create_ms_gps_tracking_table.php
php artisan migrate --path=database/migrations/2025_11_18_008_create_ms_pod_table.php
```

### Phase 4: Settlement Tables (Week 2)
```bash
php artisan migrate --path=database/migrations/2025_11_18_009_create_ms_settlement_table.php
php artisan migrate --path=database/migrations/2025_11_18_010_create_ms_settlement_detail_table.php
php artisan migrate --path=database/migrations/2025_11_18_011_create_ms_driver_payment_table.php
```

---

## DATA FLOW EXAMPLE (End-to-End)

```
Jumat 15 Nov, 16:30
└─> INSERT ms_planning_weekly (PLN001-PLN150) ← Excel import
    Status: IMPORTED

Sabtu 16 Nov, 10:00
└─> Auto-Scheduling runs
    └─> INSERT ms_planning_assignment (ASG001-ASG150)
        Status: AUTO_ASSIGNED (95%) + MANUAL_ADJUSTED (5%)

Minggu 17 Nov, 14:00
└─> Supervisor review & approve
    └─> INSERT ms_planning_approval (APV001)
        Status: APPROVED
        └─> Auto-generate ms_dispatch (DSP001-DSP150)
            Status: DISPATCHED

Senin 18 Nov, 08:00
└─> Driver receive notification
    └─> UPDATE ms_dispatch: status = CONFIRMED

Senin 18 Nov, 06:00 (Trip start)
└─> Driver check-in
    └─> INSERT ms_trip_execution (EXE001)
        └─> Start GPS tracking
            └─> INSERT ms_gps_tracking (continuous every 30 sec)

Senin 18 Nov, 09:30 (Arrived destination)
└─> Driver upload POD
    └─> INSERT ms_pod (POD001)
        └─> UPDATE ms_trip_execution: status = COMPLETED
            └─> UPDATE ms_dispatch: status = COMPLETED

Minggu 24 Nov (End of week)
└─> Settlement calculation
    └─> INSERT ms_settlement (STL001)
        └─> INSERT ms_settlement_detail (150 rows)
            └─> INSERT ms_driver_payment (12 drivers)

Senin 25 Nov
└─> Manager approve settlement
    └─> UPDATE ms_settlement: status = APPROVED
        └─> Process payment
            └─> UPDATE ms_driver_payment: status = PAID
```

---

**Document prepared by**: TMS Development Team  
**Date**: 18 November 2025  
**Next Step**: Create Laravel migration files for each table  
**Status**: Ready for Development 🚀
