# TMS - Dispatcher Adjustment & Exception Handling Module
## (Real-time Planning Adjustment & Problem Resolution)

**Tanggal:** 17 November 2025  
**Status:** Design Phase - Dispatcher Flexibility  

---

## 1. REALITAS LAPANGAN - PLANNING ≠ EXECUTION

### 1.1 Problem Statement

```
PLANNING vs REALITAS:

Planning (Jumat):
✅ Driver: Budi (DRV001)
✅ Truck: B-1234-AB (VH001) 
✅ Route: Cikupa → Bandung
✅ Time Window: 4 (06:00-08:00)
✅ Product: Mie Instan 15 ton
✅ Status: APPROVED & DISPATCHED

Execution (Senin Pagi 05:30):
❌ Driver Budi SAKIT! (tidak bisa berangkat)
❌ Truck B-1234-AB ban kempes!
❌ Client telepon: "Ganti tujuan ke Tasikmalaya, bukan Bandung"
❌ Helper tidak datang!
❌ Jam macet, minta ganti time window

DISPATCHER HARUS BISA ADJUST REAL-TIME!
├─ Ganti driver (Budi → Joko)
├─ Ganti truck (B-1234-AB → B-5678-CD)
├─ Ganti route (Bandung → Tasikmalaya)
├─ Ganti time window (Window 4 → Window 5)
├─ Assign helper pengganti
├─ Re-calculate cost & tariff
└─ Notify semua pihak (driver baru, client, supervisor)

Semua adjustment ini HARUS TERCATAT untuk:
- Audit trail (kenapa berubah?)
- Settlement (cost berubah, tariff berubah)
- Performance tracking (on-time delivery %)
- Continuous improvement (analisa root cause)
```

---

## 2. DISPATCHER ADJUSTMENT SCENARIOS

### 2.1 Common Adjustment Scenarios

| No | Scenario | Trigger | Impact | Urgency |
|----|----------|---------|--------|---------|
| 1 | **Driver Sakit/Tidak Datang** | Driver absent pagi hari | Ganti driver, delay possible | 🔴 HIGH (< 1 jam) |
| 2 | **Truck Rusak/Breakdown** | Pre-trip inspection fail | Ganti truck, delay possible | 🔴 HIGH (< 1 jam) |
| 3 | **Helper Tidak Datang** | Helper absent | Assign helper baru atau tanpa helper | 🟡 MEDIUM (< 2 jam) |
| 4 | **Client Ganti Route** | Client phone/WA | Update route, re-calculate cost | 🟡 MEDIUM (< 2 jam) |
| 5 | **Client Ganti Jadwal** | Client request reschedule | Update time window atau trip date | 🟡 MEDIUM (< 4 jam) |
| 6 | **Client Cancel Order** | Client cancel | Cancel dispatch, free resources | 🟢 LOW (< 1 hari) |
| 7 | **Traffic/Force Majeure** | Delay di jalan | Update ETA, notify client | 🟡 MEDIUM (real-time) |
| 8 | **Product/Quantity Change** | Client update load | Update weight, re-calculate tariff | 🟡 MEDIUM (< 2 jam) |
| 9 | **Driver Request Swap** | Driver personal issue | Find replacement driver | 🟡 MEDIUM (< 4 jam) |
| 10 | **Multi-truck Required** | Load > 1 truck capacity | Split order to multiple dispatches | 🔴 HIGH (< 2 jam) |

---

## 3. ADJUSTMENT WORKFLOW

### 3.1 General Adjustment Flow

```
┌─────────────────────────────────────────────────────────────┐
│ STEP 1: DISPATCHER DETECT PROBLEM                           │
│ Source:                                                     │
│ - Driver call/WA: "Pak, saya sakit tidak bisa berangkat"   │
│ - Mechanic report: "Truck B-1234-AB ban bocor"             │
│ - Client call: "Pak, ganti tujuan ke Tasikmalaya"          │
│ - System alert: "Driver DRV001 not check-in (overdue)"     │
└─────────────────┬───────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ STEP 2: OPEN DISPATCH ADJUSTMENT SCREEN                     │
├─────────────────────────────────────────────────────────────┤
│ - Search dispatch: DP-2025-11-18-001                        │
│ - Current status: DISPATCHED                                │
│ - Current assignment: Budi (DRV001), B-1234-AB (VH001)      │
│ - Click [ADJUST] button                                     │
│                                                             │
│ System lock dispatch untuk editing (prevent double edit)    │
└─────────────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ STEP 3: SELECT ADJUSTMENT TYPE                              │
├─────────────────────────────────────────────────────────────┤
│ ☐ Change Driver                                             │
│ ☐ Change Vehicle                                            │
│ ☐ Change Helper                                             │
│ ☐ Change Route                                              │
│ ☐ Change Schedule (Date/Time Window)                        │
│ ☐ Change Product/Load                                       │
│ ☐ Cancel Dispatch                                           │
│ ☐ Split to Multiple Dispatches                              │
│                                                             │
│ Can select multiple (e.g., change driver + vehicle)         │
└─────────────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ STEP 4: MAKE ADJUSTMENT                                     │
├─────────────────────────────────────────────────────────────┤
│ Example: Change Driver                                      │
│                                                             │
│ Old Driver: Budi (DRV001)                                   │
│ ├─ Status: SICK                                             │
│ └─ Action: Mark as unavailable today                        │
│                                                             │
│ New Driver: [Search & Select]                               │
│ ├─ System suggest: Joko (DRV002) ✅ Available               │
│ │                   Siti (DRV003) ✅ Available               │
│ │                   Agus (DRV004) ❌ Already assigned        │
│ │                                                           │
│ └─ Select: Joko (DRV002)                                    │
│    ├─ Check familiarity: Cikupa-Bandung (50 trips, EXPERT) │
│    ├─ Check license: SIM B2 ✅                              │
│    └─ Check rotation: Last trip 2 days ago ✅               │
│                                                             │
│ Reason: [Textarea]                                          │
│ "Driver Budi sakit mendadak (demam), diganti Joko"         │
└─────────────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ STEP 5: SYSTEM RE-CALCULATE (if needed)                     │
├─────────────────────────────────────────────────────────────┤
│ Check impact:                                               │
│                                                             │
│ ✅ Driver change: NO cost impact (same rate)                │
│ ❌ Route change: YES, re-calculate distance, toll, tariff   │
│ ❌ Truck change: YES, re-calculate fuel, tariff             │
│ ❌ Product change: YES, re-calculate tariff                 │
│                                                             │
│ If cost/tariff change:                                      │
│ - Show old vs new comparison                                │
│ - Require approval if margin < threshold                    │
│ - Send notification to client (if tariff change)            │
└─────────────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ STEP 6: SAVE ADJUSTMENT & LOG HISTORY                       │
├─────────────────────────────────────────────────────────────┤
│ - Save to ms_dispatch (update fields)                       │
│ - Insert to ms_dispatch_adjustment_log (audit trail)        │
│ - Update resource status:                                   │
│   * Old driver (DRV001): Free up time slot                  │
│   * New driver (DRV002): Mark as assigned                   │
│ - Send notification:                                        │
│   * Old driver: "Tugas dibatalkan karena sakit"             │
│   * New driver: "Assignment baru: Cikupa-Bandung 06:00"     │
│   * Client: "Driver ganti, no problem"                      │
│   * Supervisor: "Dispatch adjusted by dispatcher01"         │
└─────────────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│ STEP 7: CONTINUE EXECUTION                                  │
├─────────────────────────────────────────────────────────────┤
│ Status: DISPATCHED (with adjustment)                        │
│ New driver (Joko) proceed with trip                         │
│ Tracking continues as normal                                │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. DATABASE DESIGN - ADJUSTMENT LOG

### 4.1 ms_dispatch_adjustment_log

```sql
-- Log semua adjustment yang dilakukan dispatcher
CREATE TABLE ms_dispatch_adjustment_log (
    adjustment_log_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50) NOT NULL,
    
    -- Adjustment Info
    adjustment_type VARCHAR(50) NOT NULL, 
    -- DRIVER_CHANGE, VEHICLE_CHANGE, HELPER_CHANGE, ROUTE_CHANGE, 
    -- SCHEDULE_CHANGE, PRODUCT_CHANGE, LOAD_CHANGE, CANCELLATION, SPLIT_ORDER
    
    adjustment_timestamp DATETIME DEFAULT GETDATE(),
    adjusted_by VARCHAR(50) NOT NULL, -- user_id dispatcher
    
    -- Old vs New Values (JSON atau separate columns)
    field_changed VARCHAR(100), -- 'driver_id', 'vehicle_id', 'route_id', etc.
    old_value VARCHAR(500),
    new_value VARCHAR(500),
    
    -- Financial Impact
    has_cost_impact BIT DEFAULT 0,
    old_total_cost DECIMAL(15,2),
    new_total_cost DECIMAL(15,2),
    cost_variance DECIMAL(15,2), -- new - old
    
    has_tariff_impact BIT DEFAULT 0,
    old_tariff DECIMAL(15,2),
    new_tariff DECIMAL(15,2),
    tariff_variance DECIMAL(15,2),
    
    has_margin_impact BIT DEFAULT 0,
    old_margin_pct DECIMAL(5,2),
    new_margin_pct DECIMAL(5,2),
    
    -- Reason & Approval
    reason TEXT NOT NULL, -- "Driver sakit", "Truck rusak", "Client request", etc.
    requires_approval BIT DEFAULT 0,
    approved_by VARCHAR(50),
    approved_at DATETIME,
    approval_notes TEXT,
    
    -- Notification
    client_notified BIT DEFAULT 0,
    client_notified_at DATETIME,
    supervisor_notified BIT DEFAULT 0,
    supervisor_notified_at DATETIME,
    
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    
    INDEX idx_dispatch (dispatch_id),
    INDEX idx_type (adjustment_type),
    INDEX idx_timestamp (adjustment_timestamp)
)

-- Sample Data
INSERT INTO ms_dispatch_adjustment_log VALUES
('ADJ001', 'DP-2025-11-18-001',
'DRIVER_CHANGE', '2025-11-18 05:30:00', 'dispatcher01',
'driver_id', 'DRV001', 'DRV002',
0, 950000, 950000, 0, -- no cost impact
0, 1200000, 1200000, 0, -- no tariff impact
0, 20.83, 20.83, -- no margin impact
'Driver Budi (DRV001) sakit mendadak (demam), diganti Joko (DRV002). Emergency replacement.',
0, NULL, NULL, NULL, -- no approval needed
1, '2025-11-18 05:32:00', -- client notified
1, '2025-11-18 05:33:00', -- supervisor notified
GETDATE());

INSERT INTO ms_dispatch_adjustment_log VALUES
('ADJ002', 'DP-2025-11-18-002',
'ROUTE_CHANGE', '2025-11-18 06:15:00', 'dispatcher01',
'route_id', 'R001', 'R015',
1, 950000, 1150000, 200000, -- ⚠️ cost +200k (distance longer)
1, 1500000, 1800000, 300000, -- ⚠️ tariff +300k (re-negotiated)
1, 36.67, 36.17, -- margin turun sedikit
'Client PT Unilever request ganti tujuan dari Bandung ke Tasikmalaya (urgent). Route distance +50km. Tariff adjusted.',
1, 'manager01', '2025-11-18 06:20:00', 'Approved, margin still acceptable', -- ⚠️ need approval
1, '2025-11-18 06:18:00', -- client notified (confirmation)
1, '2025-11-18 06:21:00', -- supervisor notified
GETDATE());

INSERT INTO ms_dispatch_adjustment_log VALUES
('ADJ003', 'DP-2025-11-18-003',
'VEHICLE_CHANGE', '2025-11-18 05:45:00', 'dispatcher01',
'vehicle_id', 'VH001', 'VH005',
1, 950000, 920000, -30000, -- cost turun (truck lebih efisien)
0, 1500000, 1500000, 0, -- tariff tetap
1, 36.67, 38.67, -- margin naik!
'Truck B-1234-AB (VH001) ban bocor saat pre-trip inspection. Diganti B-9999-XY (VH005) yang lebih efisien (fuel consumption lebih baik).',
0, NULL, NULL, NULL, -- no approval (cost turun, good!)
1, '2025-11-18 05:47:00',
1, '2025-11-18 05:48:00',
GETDATE());

INSERT INTO ms_dispatch_adjustment_log VALUES
('ADJ004', 'DP-2025-11-18-004',
'CANCELLATION', '2025-11-18 07:00:00', 'dispatcher01',
'status', 'DISPATCHED', 'CANCELLED',
0, 0, 0, 0,
0, 0, 0, 0,
0, 0, 0,
'Client CV ABC cancel order mendadak (< 6 jam notice). Penalty 30% applied = Rp 540k. Driver & truck freed up for other dispatch.',
0, NULL, NULL, NULL,
1, '2025-11-18 07:02:00',
1, '2025-11-18 07:03:00',
GETDATE());
```

### 4.2 ms_dispatch Updates (Add Adjustment Fields)

```sql
ALTER TABLE ms_dispatch ADD COLUMN adjustment_count INT DEFAULT 0;
ALTER TABLE ms_dispatch ADD COLUMN last_adjusted_at DATETIME;
ALTER TABLE ms_dispatch ADD COLUMN last_adjusted_by VARCHAR(50);
ALTER TABLE ms_dispatch ADD COLUMN has_adjustment BIT DEFAULT 0;

-- Update ketika ada adjustment
UPDATE ms_dispatch 
SET adjustment_count = adjustment_count + 1,
    last_adjusted_at = GETDATE(),
    last_adjusted_by = 'dispatcher01',
    has_adjustment = 1
WHERE dispatch_id = 'DP-2025-11-18-001';
```

---

## 5. ADJUSTMENT SCENARIOS DETAIL

### 5.1 Scenario 1: DRIVER CHANGE

```
Problem: Driver Budi (DRV001) sakit
Time: Senin 05:30 (30 menit sebelum berangkat)
Impact: Delay possible

Dispatcher Action:
1. Mark driver unavailable
2. Search replacement driver
   - Filter: Available, same time window, have SIM B2
   - Sort by: Route familiarity DESC
3. Select: Joko (DRV002) - 50 trips Cikupa-Bandung, EXPERT
4. Update dispatch: driver_id = DRV002
5. Notify:
   - Budi: "Istirahat dulu, semoga cepat sembuh"
   - Joko: "Assignment baru jam 06:00, Cikupa-Bandung"
   - Client: "Driver ganti, no problem"
6. Log: ADJ001 (DRIVER_CHANGE)

Cost Impact: ❌ NO (same uang jasa driver rate)
Tariff Impact: ❌ NO
Approval: ❌ NOT REQUIRED
```

**UI Mockup:**
```
┌──────────────────────────────────────────────────────────┐
│ ADJUST DISPATCH: DP-2025-11-18-001                       │
├──────────────────────────────────────────────────────────┤
│ Adjustment Type: [x] Change Driver                       │
│                                                          │
│ Current Driver: Budi (DRV001)                            │
│ └─ Mark as: [ ] Sick [x] Absent [ ] Emergency            │
│                                                          │
│ New Driver: [Search...]                                  │
│                                                          │
│ Available Drivers (Time Window 4):                       │
│ ┌────────────────────────────────────────────────────┐  │
│ │ [Select] Joko (DRV002)                             │  │
│ │          Route Familiarity: EXPERT (50 trips)      │  │
│ │          License: SIM B2 ✅                         │  │
│ │          Last Trip: 2 days ago                     │  │
│ │          Rating: 4.8/5.0                           │  │
│ ├────────────────────────────────────────────────────┤  │
│ │ [Select] Siti (DRV003)                             │  │
│ │          Route Familiarity: COMPETENT (20 trips)   │  │
│ │          License: SIM B2 ✅                         │  │
│ │          Last Trip: 1 day ago                      │  │
│ │          Rating: 4.6/5.0                           │  │
│ └────────────────────────────────────────────────────┘  │
│                                                          │
│ Reason: [Driver Budi sakit mendadak (demam)]             │
│                                                          │
│ Impact Analysis:                                         │
│ - Cost Impact: NO                                        │
│ - Tariff Impact: NO                                      │
│ - Approval Required: NO                                  │
│                                                          │
│ [Cancel] [Save & Notify]                                 │
└──────────────────────────────────────────────────────────┘
```

### 5.2 Scenario 2: VEHICLE CHANGE

```
Problem: Truck B-1234-AB (VH001) ban bocor
Time: Senin 05:45 (15 menit sebelum berangkat)
Impact: Delay possible

Dispatcher Action:
1. Mark vehicle unavailable (send to garage)
2. Search replacement vehicle
   - Filter: Available, same truck type (Tronton), same capacity (≥15 ton)
   - Check: Maintenance status OK, fuel OK, e-toll card OK
3. Select: B-9999-XY (VH005) - Tronton, capacity 18 ton, fuel consumption 3.5 km/l (lebih baik!)
4. Update dispatch: vehicle_id = VH005
5. Re-calculate cost:
   - Old fuel cost: 4.0 km/l × 150km = 37.5L × Rp 6,500 = Rp 243,750
   - New fuel cost: 3.5 km/l × 150km = 42.9L × Rp 6,500 = Rp 278,850
   ⚠️ Wait, this is WORSE! Recalculate...
   
   Correction (km per liter):
   - Old: 150km / 4.0 km/l = 37.5L × Rp 6,500 = Rp 243,750
   - New: 150km / 3.5 km/l = 42.9L × Rp 6,500 = Rp 278,850
   
   Actually let me fix the logic:
   - Old truck: 4.0 km/liter (150km ÷ 4 = 37.5 liter)
   - New truck: 4.5 km/liter (150km ÷ 4.5 = 33.3 liter) ✅ Better!
   - Fuel saving: 4.2L × Rp 6,500 = Rp 27,300
6. Notify driver: "Ganti truck ke B-9999-XY"
7. Log: ADJ003 (VEHICLE_CHANGE)

Cost Impact: ✅ YES (cost turun Rp 30k, margin naik)
Tariff Impact: ❌ NO (client tidak perlu tahu)
Approval: ❌ NOT REQUIRED (cost improvement)
```

### 5.3 Scenario 3: ROUTE CHANGE

```
Problem: Client request ganti tujuan Bandung → Tasikmalaya
Time: Senin 06:15 (setelah driver berangkat 15 menit)
Impact: Distance +50km, tariff harus naik

Dispatcher Action:
1. Confirm with client (phone/WA)
   "Pak, ganti ke Tasikmalaya berarti +50km, tariff naik Rp 300k jadi Rp 1.8 juta, OK?"
2. Client confirm: "OK, urgent soalnya"
3. Update dispatch:
   - Old route: R001 (Cikupa-Bandung, 120km)
   - New route: R015 (Cikupa-Tasikmalaya, 170km)
4. Re-calculate:
   - Distance: 120km → 170km (+50km)
   - Fuel: 120km/4km/l = 30L → 170km/4km/l = 42.5L (+12.5L × Rp 6,500 = +Rp 81,250)
   - E-toll: Rp 65k → Rp 85k (+Rp 20k, different route)
   - Uang jalan: Rp 85k → Rp 100k (+Rp 15k, longer trip)
   - Uang jasa driver: Rp 200k → Rp 250k (+Rp 50k, longer distance)
   - Total cost: Rp 950k → Rp 1,150k (+Rp 200k)
   
   - Tariff: Rp 1,500k → Rp 1,800k (+Rp 300k, negotiated)
   - Margin: Rp 550k (36.67%) → Rp 650k (36.11%) - OK!
5. Notify driver: "Pak, ganti tujuan ke Tasikmalaya, koordinat GPS: -7.xxx, 108.xxx"
6. Update GPS route in system
7. Log: ADJ002 (ROUTE_CHANGE)

Cost Impact: ✅ YES (+Rp 200k)
Tariff Impact: ✅ YES (+Rp 300k, client confirm)
Approval: ✅ REQUIRED (margin change, need manager approval)
```

**Approval UI:**
```
┌──────────────────────────────────────────────────────────┐
│ ADJUSTMENT APPROVAL REQUEST                              │
├──────────────────────────────────────────────────────────┤
│ Dispatch: DP-2025-11-18-002                              │
│ Adjustment Type: ROUTE_CHANGE                            │
│ Requested by: dispatcher01                               │
│ Time: 2025-11-18 06:15:00                                │
│                                                          │
│ Changes:                                                 │
│ - Route: Cikupa-Bandung → Cikupa-Tasikmalaya            │
│ - Distance: 120km → 170km (+50km)                        │
│                                                          │
│ Financial Impact:                                        │
│ ┌────────────────┬──────────────┬──────────────┬────────┐│
│ │ Item           │ Old          │ New          │ Δ      ││
│ ├────────────────┼──────────────┼──────────────┼────────┤│
│ │ Total Cost     │ Rp 950,000   │ Rp 1,150,000 │ +200k  ││
│ │ Tariff         │ Rp 1,500,000 │ Rp 1,800,000 │ +300k  ││
│ │ Margin         │ 36.67%       │ 36.11%       │ -0.56% ││
│ └────────────────┴──────────────┴──────────────┴────────┘│
│                                                          │
│ Reason:                                                  │
│ "Client PT Unilever request ganti tujuan dari Bandung    │
│  ke Tasikmalaya (urgent). Route distance +50km. Tariff   │
│  adjusted after negotiation with client."                │
│                                                          │
│ Client Confirmation: YES (via phone, 06:17)              │
│                                                          │
│ Manager Notes: [Approved, margin still acceptable]       │
│                                                          │
│ [Reject] [Approve]                                       │
└──────────────────────────────────────────────────────────┘
```

### 5.4 Scenario 4: SCHEDULE CHANGE

```
Problem: Client request reschedule (hari ini → besok)
Time: Senin 08:00
Impact: Free up resources today, use tomorrow

Dispatcher Action:
1. Client call: "Pak, hari ini cancel dulu, besok aja jam yang sama"
2. Check: Cancellation policy
   - Time until trip: 22 jam (masih > 6 jam)
   - Penalty: FREE (no penalty)
3. Update dispatch:
   - trip_date: 2025-11-18 → 2025-11-19
   - time_window_id: TW004 (keep same, 06:00-08:00)
   - status: DISPATCHED → RESCHEDULED
4. Free up resources:
   - Driver DRV001: Available 2025-11-18 Window 4
   - Truck VH001: Available 2025-11-18 Window 4
5. Check tomorrow availability:
   - Driver DRV001: ✅ Available 2025-11-19 Window 4
   - Truck VH001: ✅ Available 2025-11-19 Window 4
6. Re-assign for tomorrow
7. Notify:
   - Driver: "Tugas ditunda besok, jam sama"
   - Client: "OK, besok jam 06:00"
8. Log: ADJ005 (SCHEDULE_CHANGE)

Cost Impact: ❌ NO
Tariff Impact: ❌ NO
Approval: ❌ NOT REQUIRED
```

### 5.5 Scenario 5: ORDER CANCELLATION

```
Problem: Client cancel mendadak (< 6 jam notice)
Time: Senin 05:00 (1 jam sebelum trip)
Impact: Penalty applies, free up resources

Dispatcher Action:
1. Client call: "Pak, cancel dulu ordernya, buyer tiba-tiba nunda"
2. Check: Cancellation policy
   - Time until trip: 1 jam (< 6 jam ⚠️)
   - Penalty: 30% × Rp 1,800,000 = Rp 540,000
3. Confirm with client: "Pak, penalty 30% = Rp 540k, OK?"
4. Client confirm: "OK, gpp, keadaan urgent"
5. Update dispatch:
   - status: DISPATCHED → CANCELLED
   - cancellation_reason: "Client urgent issue"
   - cancellation_penalty: Rp 540,000
   - cancelled_by: CLIENT
   - cancelled_at: 2025-11-18 05:00:00
6. Free up resources:
   - Driver DRV004: Available for other dispatch
   - Truck VH004: Available for other dispatch
7. Generate invoice: Penalty Rp 540k (no trip, but charge penalty)
8. Notify:
   - Driver: "Tugas dibatalkan, standby untuk assignment lain"
   - Finance: "Generate penalty invoice"
9. Log: ADJ004 (CANCELLATION)

Cost Impact: ✅ YES (no cost incurred, penalty revenue)
Tariff Impact: ✅ YES (Rp 1.8 juta → Rp 540k penalty)
Approval: ❌ NOT REQUIRED (standard penalty)
```

### 5.6 Scenario 6: SPLIT ORDER (Load > Truck Capacity)

```
Problem: Client kirim order 30 ton, tapi truck Tronton max 18 ton
Time: Jumat 16:00 (planning stage)
Impact: Need 2 trucks

Dispatcher Action:
1. Detect: Order 30 ton > Truck capacity 18 ton
2. Calculate: Need 2 trucks (18 ton + 12 ton)
3. System suggest:
   - Dispatch 1: Tronton (18 ton)
   - Dispatch 2: Engkel (12 ton)
4. Create 2 dispatch orders:
   
   Dispatch 1 (DP-2025-11-18-010):
   - Route: Cikupa-Bandung
   - Truck: B-1234-AB (Tronton, 18 ton)
   - Driver: DRV001
   - Load: 18 ton
   - Tariff: Rp 1,500,000
   
   Dispatch 2 (DP-2025-11-18-011):
   - Route: Cikupa-Bandung (same)
   - Truck: B-5555-CD (Engkel, 12 ton)
   - Driver: DRV005
   - Load: 12 ton
   - Tariff: Rp 1,000,000 (smaller truck, cheaper)
   
5. Link both dispatches: parent_order_id = ORD-XXX
6. Total tariff: Rp 2,500,000 (Rp 1,500k + Rp 1,000k)
7. Notify client: "Order split ke 2 truck (18 ton + 12 ton), total Rp 2.5 juta"
8. Log: ADJ006 (SPLIT_ORDER)

Cost Impact: ✅ YES (2 trips instead of 1)
Tariff Impact: ✅ YES (Rp 1,800k → Rp 2,500k)
Approval: ✅ REQUIRED (client confirm new tariff)
```

---

## 6. APPROVAL MATRIX

### 6.1 When Approval Required?

| Adjustment Type | Approval Required? | Condition | Approver |
|----------------|-------------------|-----------|----------|
| Driver Change | ❌ NO | Same cost | Auto |
| Driver Change | ✅ YES | Cost impact > Rp 100k | Supervisor |
| Vehicle Change | ❌ NO | Cost same or lower | Auto |
| Vehicle Change | ✅ YES | Cost increase > Rp 50k | Supervisor |
| Helper Change | ❌ NO | Same cost | Auto |
| Route Change | ✅ YES | Always (distance/tariff change) | Manager |
| Schedule Change | ❌ NO | Reschedule > 6 jam notice | Auto |
| Schedule Change | ✅ YES | Reschedule < 6 jam notice | Supervisor |
| Product Change | ✅ YES | Weight/volume > 10% change | Supervisor |
| Cancellation | ❌ NO | > 6 jam notice, no penalty | Auto |
| Cancellation | ✅ YES | < 6 jam notice, penalty apply | Manager |
| Split Order | ✅ YES | Always (tariff change significant) | Manager |
| Margin Impact | ✅ YES | Margin drop > 5% | Manager |
| Margin Impact | ✅ YES | Margin drop > 10% | Director |

### 6.2 Approval Workflow

```sql
-- Auto-approval logic
IF adjustment_type IN ('DRIVER_CHANGE', 'HELPER_CHANGE') 
   AND cost_variance = 0 THEN
    -- Auto approve
    UPDATE ms_dispatch_adjustment_log 
    SET requires_approval = 0
    WHERE adjustment_log_id = 'ADJ001';
ELSE IF adjustment_type = 'ROUTE_CHANGE' 
   OR tariff_variance > 0 
   OR margin_drop > 5% THEN
    -- Require approval
    UPDATE ms_dispatch_adjustment_log 
    SET requires_approval = 1,
        approval_status = 'PENDING'
    WHERE adjustment_log_id = 'ADJ002';
    
    -- Send notification to approver
    INSERT INTO notifications VALUES (...);
END IF
```

---

## 7. DISPATCHER UI - ADJUSTMENT MODULE

### 7.1 Dispatch List with Quick Adjust

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ DISPATCH LIST - Today (18 Nov 2025)                                          │
├──────────────────────────────────────────────────────────────────────────────┤
│ [Filter: All] [Status: All] [Search...]                                      │
│                                                                              │
│ ┌──────────┬────────┬─────────────────┬───────────┬────────┬──────────────┐ │
│ │ Dispatch │ Window │ Route           │ Driver    │ Status │ Action       │ │
│ ├──────────┼────────┼─────────────────┼───────────┼────────┼──────────────┤ │
│ │ DP-001   │ 4      │ Cikupa-Bandung  │ Budi      │ ⚠️ LATE│ [Adjust]     │ │
│ │          │06:00   │ 120km           │ DRV001    │ CHECK  │ [Track]      │ │
│ │          │        │ PT Indofood     │ B-1234-AB │        │ [Cancel]     │ │
│ ├──────────┼────────┼─────────────────┼───────────┼────────┼──────────────┤ │
│ │ DP-002   │ 4      │ Cikupa-Bandung  │ Joko      │ 📍 ON  │ [View]       │ │
│ │ [ADJ]    │06:00   │ 120km → 170km   │ DRV002    │ ROUTE  │ [Track]      │ │
│ │          │        │ PT Unilever     │ B-5678-CD │        │              │ │
│ ├──────────┼────────┼─────────────────┼───────────┼────────┼──────────────┤ │
│ │ DP-003   │ 5      │ Jakarta-Sby     │ Siti      │ ✅ DONE│ [View]       │ │
│ │          │08:00   │ 780km           │ DRV003    │ COMP   │ [Settlement] │ │
│ │          │        │ PT Wings        │ B-9999-XY │        │              │ │
│ └──────────┴────────┴─────────────────┴───────────┴────────┴──────────────┘ │
│                                                                              │
│ Legend: [ADJ] = Has adjustment                                               │
└──────────────────────────────────────────────────────────────────────────────┘
```

### 7.2 Quick Adjust Panel

```
┌──────────────────────────────────────────────────────────┐
│ QUICK ADJUST: DP-2025-11-18-001                          │
├──────────────────────────────────────────────────────────┤
│ Common Issues:                                           │
│                                                          │
│ [Driver Absent] → Find replacement driver                │
│ [Truck Problem] → Find replacement vehicle               │
│ [Helper Absent] → Assign new helper                      │
│ [Client Reschedule] → Change date/time                   │
│ [Client Cancel] → Cancel with penalty check              │
│                                                          │
│ Advanced:                                                │
│ [Change Route] [Change Load] [Split Order]               │
│                                                          │
│ Current Assignment:                                      │
│ - Driver: Budi (DRV001) - SIM B2                         │
│ - Vehicle: B-1234-AB (Tronton 18t)                       │
│ - Helper: Andi (HLP001)                                  │
│ - Route: Cikupa-Bandung (120km)                          │
│ - Schedule: 18 Nov 2025, 06:00-08:00                     │
│ - Load: 15 ton Mie Instan                                │
│ - Tariff: Rp 1,500,000                                   │
│                                                          │
│ [Close]                                                  │
└──────────────────────────────────────────────────────────┘
```

### 7.3 Adjustment History

```
┌──────────────────────────────────────────────────────────────────────┐
│ ADJUSTMENT HISTORY: DP-2025-11-18-002                                │
├──────────────────────────────────────────────────────────────────────┤
│ Total Adjustments: 2                                                 │
│                                                                      │
│ #1 - 18 Nov 2025 06:15:00                                            │
│ ┌────────────────────────────────────────────────────────────────┐  │
│ │ Type: ROUTE_CHANGE                                             │  │
│ │ Changed by: dispatcher01                                       │  │
│ │                                                                │  │
│ │ Old Value: Cikupa-Bandung (120km)                              │  │
│ │ New Value: Cikupa-Tasikmalaya (170km)                          │  │
│ │                                                                │  │
│ │ Cost Impact: +Rp 200,000                                       │  │
│ │ Tariff Impact: +Rp 300,000                                     │  │
│ │ Margin: 36.67% → 36.11% (-0.56%)                               │  │
│ │                                                                │  │
│ │ Reason: Client PT Unilever request ganti tujuan urgent         │  │
│ │                                                                │  │
│ │ Approval: ✅ APPROVED by manager01 (06:20:00)                  │  │
│ │ Notes: "Approved, margin still acceptable"                     │  │
│ └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
│ #2 - 18 Nov 2025 07:30:00                                            │
│ ┌────────────────────────────────────────────────────────────────┐  │
│ │ Type: HELPER_CHANGE                                            │  │
│ │ Changed by: dispatcher01                                       │  │
│ │                                                                │  │
│ │ Old Value: Andi (HLP001)                                       │  │
│ │ New Value: Budi Helper (HLP005)                                │  │
│ │                                                                │  │
│ │ Cost Impact: Rp 0 (same rate)                                  │  │
│ │                                                                │  │
│ │ Reason: Helper Andi telat, diganti Budi Helper yang standby   │  │
│ │                                                                │  │
│ │ Approval: ❌ NOT REQUIRED (auto-approved)                      │  │
│ └────────────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 8. NOTIFICATION SYSTEM

### 8.1 Notification Targets

```sql
-- When adjustment happens, notify:

CASE adjustment_type
    WHEN 'DRIVER_CHANGE' THEN
        -- Notify old driver
        SEND_NOTIFICATION('DRV001', 'Tugas dibatalkan karena sakit, istirahat ya');
        -- Notify new driver
        SEND_NOTIFICATION('DRV002', 'Assignment baru: Cikupa-Bandung 06:00');
        -- Notify supervisor
        SEND_NOTIFICATION('supervisor01', 'Dispatch adjusted: Driver change');
        
    WHEN 'ROUTE_CHANGE' THEN
        -- Notify driver (update GPS destination)
        SEND_NOTIFICATION('DRV002', 'Tujuan berubah ke Tasikmalaya, GPS updated');
        -- Notify client (confirm new tariff)
        SEND_NOTIFICATION('client_unilever', 'Route change confirmed, new tariff Rp 1.8 juta');
        -- Notify supervisor
        SEND_NOTIFICATION('supervisor01', 'Route change, need approval');
        -- Notify manager (if need approval)
        SEND_NOTIFICATION('manager01', 'Approval needed: Route change with tariff impact');
        
    WHEN 'CANCELLATION' THEN
        -- Notify driver
        SEND_NOTIFICATION('DRV004', 'Tugas dibatalkan, standby untuk assignment lain');
        -- Notify client (confirm penalty)
        SEND_NOTIFICATION('client_abc', 'Order cancelled, penalty Rp 540k applied');
        -- Notify finance (invoice penalty)
        SEND_NOTIFICATION('finance01', 'Generate penalty invoice: DP-001');
        -- Notify supervisor
        SEND_NOTIFICATION('supervisor01', 'Dispatch cancelled with penalty');
END CASE
```

### 8.2 Notification Channels

- **SMS**: Urgent (driver absent, truck problem)
- **WhatsApp**: Normal (reschedule, minor changes)
- **Email**: Formal (cancellation with penalty, approval request)
- **In-App**: All notifications
- **Dashboard Alert**: Red badge for pending approvals

---

## 9. REPORTS & ANALYTICS

### 9.1 Adjustment Frequency Report

```sql
-- Adjustment frequency by type (current month)
SELECT 
    adjustment_type,
    COUNT(*) as total_adjustments,
    COUNT(DISTINCT dispatch_id) as affected_dispatches,
    
    -- Financial impact
    SUM(CASE WHEN has_cost_impact = 1 THEN 1 ELSE 0 END) as with_cost_impact,
    SUM(cost_variance) as total_cost_variance,
    
    SUM(CASE WHEN has_tariff_impact = 1 THEN 1 ELSE 0 END) as with_tariff_impact,
    SUM(tariff_variance) as total_tariff_variance,
    
    -- Approval
    SUM(CASE WHEN requires_approval = 1 THEN 1 ELSE 0 END) as required_approval,
    SUM(CASE WHEN approved_by IS NOT NULL THEN 1 ELSE 0 END) as approved,
    
    -- Average time to approve
    AVG(DATEDIFF(MINUTE, adjustment_timestamp, approved_at)) as avg_approval_time_minutes
    
FROM ms_dispatch_adjustment_log
WHERE adjustment_timestamp >= DATEADD(month, DATEDIFF(month, 0, GETDATE()), 0)
GROUP BY adjustment_type
ORDER BY total_adjustments DESC;

Result:
| Type            | Total | Dispatches | Cost Impact | Cost Δ      | Approval | Avg Approve |
|-----------------|-------|------------|-------------|-------------|----------|-------------|
| DRIVER_CHANGE   | 45    | 45         | 5           | -Rp 50,000  | 5        | 8 min       |
| HELPER_CHANGE   | 32    | 32         | 0           | Rp 0        | 0        | N/A         |
| ROUTE_CHANGE    | 18    | 18         | 18          | +Rp 3.6 jt  | 18       | 15 min      |
| SCHEDULE_CHANGE | 25    | 25         | 0           | Rp 0        | 3        | 5 min       |
| VEHICLE_CHANGE  | 22    | 22         | 8           | -Rp 120k    | 4        | 6 min       |
| CANCELLATION    | 12    | 12         | 12          | -Rp 18 jt   | 5        | 12 min      |
| SPLIT_ORDER     | 5     | 5          | 5           | +Rp 2.5 jt  | 5        | 20 min      |
```

### 9.2 Root Cause Analysis

```sql
-- Most common reasons for adjustment (text analysis from reason field)
SELECT 
    adjustment_type,
    
    -- Common keywords
    SUM(CASE WHEN reason LIKE '%sakit%' THEN 1 ELSE 0 END) as reason_sick,
    SUM(CASE WHEN reason LIKE '%rusak%' THEN 1 ELSE 0 END) as reason_breakdown,
    SUM(CASE WHEN reason LIKE '%telat%' OR reason LIKE '%tidak datang%' THEN 1 ELSE 0 END) as reason_absent,
    SUM(CASE WHEN reason LIKE '%client%' THEN 1 ELSE 0 END) as reason_client_request,
    SUM(CASE WHEN reason LIKE '%urgent%' THEN 1 ELSE 0 END) as reason_urgent,
    
    COUNT(*) as total
    
FROM ms_dispatch_adjustment_log
WHERE adjustment_timestamp >= DATEADD(month, -1, GETDATE())
GROUP BY adjustment_type;

Result:
| Type          | Sick | Breakdown | Absent | Client Request | Urgent | Total |
|---------------|------|-----------|--------|----------------|--------|-------|
| DRIVER_CHANGE | 28   | 0         | 17     | 0              | 5      | 45    |
| VEHICLE_CHANGE| 0    | 18        | 0      | 0              | 4      | 22    |
| ROUTE_CHANGE  | 0    | 0         | 0      | 18             | 12     | 18    |

Insight:
- Driver change mostly due to SICK (62%) - Need backup driver pool!
- Vehicle change mostly due to BREAKDOWN (82%) - Improve maintenance!
- Route change mostly due to CLIENT REQUEST (100%) - Expected, normal business
```

### 9.3 Performance Impact

```sql
-- On-time delivery rate (with vs without adjustment)
SELECT 
    CASE WHEN d.has_adjustment = 1 THEN 'With Adjustment' ELSE 'No Adjustment' END as dispatch_type,
    
    COUNT(*) as total_dispatches,
    
    SUM(CASE WHEN d.actual_arrival_time <= d.estimated_arrival_time THEN 1 ELSE 0 END) as on_time,
    SUM(CASE WHEN d.actual_arrival_time > d.estimated_arrival_time THEN 1 ELSE 0 END) as late,
    
    CAST(SUM(CASE WHEN d.actual_arrival_time <= d.estimated_arrival_time THEN 1 ELSE 0 END) AS FLOAT) / COUNT(*) * 100 as on_time_pct,
    
    AVG(DATEDIFF(MINUTE, d.estimated_arrival_time, d.actual_arrival_time)) as avg_delay_minutes
    
FROM ms_dispatch d
WHERE d.status = 'COMPLETED'
  AND d.trip_date >= DATEADD(month, -1, GETDATE())
GROUP BY CASE WHEN d.has_adjustment = 1 THEN 'With Adjustment' ELSE 'No Adjustment' END;

Result:
| Type            | Total | On-Time | Late | On-Time % | Avg Delay |
|-----------------|-------|---------|------|-----------|-----------|
| No Adjustment   | 850   | 782     | 68   | 92.0%     | +12 min   |
| With Adjustment | 159   | 125     | 34   | 78.6%     | +35 min   |

Insight:
- Dispatch WITH adjustment has LOWER on-time rate (78.6% vs 92.0%)
- Average delay higher (+35 min vs +12 min)
- Conclusion: Adjustment unavoidable but impact delivery performance
  → Minimize through better planning & resource management
```

---

## 10. SUMMARY

### ✅ Key Features:

1. **Flexible Adjustment**
   - 8 adjustment types (driver, vehicle, helper, route, schedule, product, cancel, split)
   - Real-time adjustment (before & during trip)
   - Auto re-calculation (cost, tariff, margin)

2. **Audit Trail**
   - Complete history log (ms_dispatch_adjustment_log)
   - Who, when, what, why
   - Old vs new values comparison

3. **Approval Workflow**
   - Auto-approval for no-impact changes
   - Manager approval for cost/tariff impact
   - Director approval for significant margin drop

4. **Notification System**
   - Multi-channel (SMS, WA, Email, In-App)
   - Target stakeholders (driver, client, supervisor, manager)
   - Real-time alert for urgent issues

5. **Analytics & Insights**
   - Adjustment frequency tracking
   - Root cause analysis
   - Performance impact measurement
   - Continuous improvement feedback loop

### ✅ Business Benefits:

- **Flexibility**: Handle real-world problems (sick driver, truck breakdown, client changes)
- **Transparency**: Complete audit trail for all changes
- **Control**: Approval workflow prevents unauthorized changes
- **Efficiency**: Quick adjustment without disrupting operations
- **Accountability**: Track who made what changes and why

**Apakah konsep dispatcher adjustment module ini sudah sesuai? Ada skenario lain yang perlu ditambahkan?** 🔧📋

