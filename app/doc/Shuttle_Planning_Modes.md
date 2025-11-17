# TMS - Shuttle Route Planning Modes
## (Weekly Planning vs Ad-hoc/Daily Order)

**Tanggal:** 17 November 2025  
**Status:** Design Phase - Planning Flexibility  

---

## 1. REALITAS BISNIS - 2 MODE SHUTTLE

### 1.1 Problem Statement

```
TIDAK SEMUA CLIENT SAMA!

Client Type A (Big Corporation - Contract):
- PT Indofood, PT Unilever, PT Wings
- Punya PLANNING MINGGUAN (Weekly Planning)
- Submit Excel file setiap Jumat untuk minggu depan
- Route tetap: Cikupa → Bandung (40 trips/month)
- Dapat DISCOUNT 15% karena commitment

Client Type B (SME - Spot/Mixed):
- CV ABC, PT XYZ Distributor
- TIDAK ADA planning mingguan
- Order HARIAN/AD-HOC via WhatsApp/Email/Phone
- "Besok kirim dong, jam 8 pagi"
- Route sama (shuttle) tapi tidak regular
- Bayar SPOT RATE (lebih mahal)

Kesimpulan:
System HARUS support 2 mode:
1. Weekly Planning (untuk contract client)
2. Ad-hoc/Daily Order (untuk spot client)
```

---

## 2. MODE 1: WEEKLY PLANNING (Contract Client)

### 2.1 Karakteristik
- Client: **Contract-based** (min commitment 20-40 trips/month)
- Route: **Shuttle tetap** (origin-destination fixed)
- Planning: **Mingguan** (submit Jumat untuk minggu depan)
- Format: **Excel upload** (template standardized)
- Pricing: **Discounted rate** (karena volume & predictability)
- Approval: **Batch approval** (review & assign semua trips sekaligus)

### 2.2 Workflow Weekly Planning

```
┌─────────────────────────────────────────────────────┐
│ STEP 1: CLIENT SUBMIT WEEKLY PLANNING               │
│ Deadline: Jumat 17:00 untuk minggu depan            │
├─────────────────────────────────────────────────────┤
│ - Client download template Excel                    │
│ - Isi planning: Tanggal, Time Window, Qty, Product  │
│ - Upload ke system atau email ke dispatcher         │
│                                                     │
│ Example (PT Indofood - Mie Instan):                 │
│ ┌──────────┬────────┬────────┬─────────┬──────┐    │
│ │ Date     │ Window │ Origin │ Dest    │ Qty  │    │
│ ├──────────┼────────┼────────┼─────────┼──────┤    │
│ │ 18/11/25 │ 4      │ Cikupa │ Bandung │ 15t  │    │
│ │ 18/11/25 │ 5      │ Cikupa │ Bandung │ 15t  │    │
│ │ 19/11/25 │ 4      │ Cikupa │ Bandung │ 15t  │    │
│ │ 20/11/25 │ 6      │ Cikupa │ Bandung │ 15t  │    │
│ │ 21/11/25 │ 4      │ Cikupa │ Bandung │ 15t  │    │
│ │ 22/11/25 │ 5      │ Cikupa │ Bandung │ 15t  │    │
│ └──────────┴────────┴────────┴─────────┴──────┘    │
│ Total: 6 trips untuk minggu 18-24 Nov 2025          │
└─────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 2: SYSTEM IMPORT & VALIDATE                    │
├─────────────────────────────────────────────────────┤
│ ✅ Check route exists (Cikupa-Bandung)              │
│ ✅ Check time window valid (1-12)                   │
│ ✅ Check date in future                             │
│ ✅ Check product exists                             │
│ ✅ Check client has active contract                 │
│ ✅ Calculate cost & tariff                          │
│                                                     │
│ Status: IMPORTED (pending assignment)               │
└─────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 3: AUTO-SCHEDULING (System Suggest)            │
├─────────────────────────────────────────────────────┤
│ For each trip:                                      │
│ - Find available driver (check time window)         │
│ - Find available truck (check capacity)             │
│ - Match driver-route familiarity                    │
│ - Check driver rotation (fair distribution)         │
│ - Suggest backhaul opportunity                      │
│                                                     │
│ Output: Suggested assignment for all 6 trips        │
└─────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 4: DISPATCHER REVIEW & ADJUST                  │
├─────────────────────────────────────────────────────┤
│ - Review auto-assignment                            │
│ - Drag-drop to reassign (if needed)                 │
│ - Resolve conflicts (double booking)                │
│ - Add notes/special instructions                    │
│ - Optimize backhaul opportunities                   │
│                                                     │
│ UI: Gantt chart / Calendar view                     │
└─────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 5: APPROVE & DISPATCH                          │
├─────────────────────────────────────────────────────┤
│ - Manager approve weekly planning                   │
│ - Generate dispatch orders (6 dispatch)             │
│ - Notify drivers (SMS/WhatsApp/App)                 │
│ - Export PDF/Excel for client confirmation          │
│                                                     │
│ Status: PLANNED (ready to execute)                  │
└─────────────────────────────────────────────────────┘
```

### 2.3 Database untuk Weekly Planning

```sql
-- Planning Header (per client per week)
CREATE TABLE ms_planning_weekly (
    planning_weekly_id VARCHAR(50) PRIMARY KEY,
    planning_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Client
    client_id VARCHAR(50) NOT NULL,
    contract_tariff_id VARCHAR(50), -- kontrak yang digunakan
    
    -- Period
    week_number INT, -- week 47, 48, dst
    week_start_date DATE,
    week_end_date DATE,
    year INT,
    
    -- Submission
    submitted_by VARCHAR(50), -- client user
    submitted_at DATETIME,
    submission_method VARCHAR(50), -- EXCEL_UPLOAD, MANUAL_INPUT, API
    excel_file_url VARCHAR(500),
    
    -- Status
    status VARCHAR(20) DEFAULT 'DRAFT', -- DRAFT, IMPORTED, ASSIGNED, APPROVED, CANCELLED
    
    -- Summary
    total_trips INT,
    total_weight_ton DECIMAL(10,2),
    
    -- Approval
    reviewed_by VARCHAR(50),
    reviewed_at DATETIME,
    approved_by VARCHAR(50),
    approved_at DATETIME,
    
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    
    INDEX idx_client_week (client_id, week_start_date),
    INDEX idx_status (status)
)

-- Planning Detail (per trip)
CREATE TABLE ms_planning_detail (
    planning_detail_id VARCHAR(50) PRIMARY KEY,
    planning_weekly_id VARCHAR(50) NOT NULL,
    
    -- Trip Info
    trip_date DATE NOT NULL,
    time_window_id VARCHAR(50) NOT NULL,
    
    -- Route & Product
    route_id VARCHAR(50) NOT NULL,
    product_id VARCHAR(50),
    
    -- Load
    weight_ton DECIMAL(10,2),
    volume_cbm DECIMAL(10,2),
    quantity INT,
    
    -- Assignment (from auto-scheduling or manual)
    assigned_driver_id VARCHAR(50),
    assigned_vehicle_id VARCHAR(50),
    assigned_helper_id VARCHAR(50),
    assignment_method VARCHAR(50), -- AUTO, MANUAL
    
    -- Dispatch Reference (after approval)
    dispatch_id VARCHAR(50), -- link ke ms_dispatch setelah di-approve
    
    -- Status
    status VARCHAR(20) DEFAULT 'PLANNED', -- PLANNED, ASSIGNED, DISPATCHED, CANCELLED
    
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (planning_weekly_id) REFERENCES ms_planning_weekly(planning_weekly_id),
    FOREIGN KEY (time_window_id) REFERENCES ms_time_window(time_window_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (product_id) REFERENCES ms_product(product_id),
    FOREIGN KEY (assigned_driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (assigned_vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
)
```

---

## 3. MODE 2: AD-HOC/DAILY ORDER (Spot Client)

### 3.1 Karakteristik
- Client: **Spot/Mixed** (no minimum commitment)
- Route: **Bisa shuttle** (same route) tapi **tidak regular**
- Planning: **Harian/Ad-hoc** (order via WA/Email/Phone hari ini untuk besok)
- Format: **Manual input** (dispatcher input langsung ke system)
- Pricing: **Spot rate** (lebih mahal, no discount)
- Approval: **Per order** (approve 1-1, not batch)

### 3.2 Workflow Ad-hoc Order

```
┌─────────────────────────────────────────────────────┐
│ STEP 1: CLIENT CONTACT VIA WA/EMAIL/PHONE           │
│ "Pak, besok kirim barang dari Cikupa ke Bandung     │
│  15 ton, jam 8 pagi. Bisa ga?"                      │
└─────────────────┬───────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 2: DISPATCHER CHECK AVAILABILITY               │
├─────────────────────────────────────────────────────┤
│ - Check driver availability (besok jam 8)           │
│ - Check truck availability                          │
│ - Check route: Cikupa-Bandung shuttle               │
│ - Check spot rate untuk client ini                  │
│                                                     │
│ Response: "Bisa pak, rate Rp 1.8 juta, OK?"         │
└─────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 3: CLIENT CONFIRM (Verbal/WA)                  │
│ "OK, besok jam 8 ya. Nanti kirim detailnya."        │
└─────────────────┬───────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 4: DISPATCHER CREATE ORDER MANUALLY            │
├─────────────────────────────────────────────────────┤
│ - Input ke system: ms_order                         │
│   * Client: CV ABC                                  │
│   * Route: Cikupa-Bandung                           │
│   * Date: 18 Nov 2025                               │
│   * Time Window: Window 4 (06:00-08:00)             │
│   * Weight: 15 ton                                  │
│   * Product: General Cargo                          │
│                                                     │
│ - System calculate tariff (spot rate)               │
│ - Assign driver & truck                             │
│                                                     │
│ Status: CONFIRMED (siap dispatch)                   │
└─────────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ STEP 5: CREATE DISPATCH & NOTIFY DRIVER             │
├─────────────────────────────────────────────────────┤
│ - Generate dispatch order                           │
│ - Notify driver: "Besok jam 8, Cikupa-Bandung"      │
│ - Prepare uang jalan, e-toll, fuel card             │
│                                                     │
│ Status: DISPATCHED                                  │
└─────────────────────────────────────────────────────┘
```

### 3.3 Database untuk Ad-hoc Order

```sql
-- Order (individual order, bisa dari weekly planning atau ad-hoc)
CREATE TABLE ms_order (
    order_id VARCHAR(50) PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Client
    client_id VARCHAR(50) NOT NULL,
    
    -- Order Source ⭐ KEY FIELD
    order_source VARCHAR(50) NOT NULL, -- WEEKLY_PLANNING, ADHOC_WA, ADHOC_EMAIL, ADHOC_PHONE, API
    planning_detail_id VARCHAR(50), -- NULL jika ad-hoc, isi jika dari weekly planning
    
    -- Route & Schedule
    route_id VARCHAR(50) NOT NULL,
    trip_date DATE NOT NULL,
    time_window_id VARCHAR(50) NOT NULL,
    
    -- Product & Load
    product_id VARCHAR(50),
    weight_ton DECIMAL(10,2),
    volume_cbm DECIMAL(10,2),
    quantity INT,
    
    -- Pricing
    contract_tariff_id VARCHAR(50), -- tariff yang digunakan (contract atau spot)
    quoted_rate DECIMAL(15,2), -- rate yang di-quote ke client
    is_spot_rate BIT DEFAULT 0, -- 1 = spot rate, 0 = contract rate
    
    -- Contact Person (untuk ad-hoc order)
    contact_name VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    
    -- Special Instructions
    special_instructions TEXT,
    requires_helper BIT DEFAULT 0,
    requires_specific_driver BIT DEFAULT 0,
    specific_driver_id VARCHAR(50),
    
    -- Status
    status VARCHAR(20) DEFAULT 'DRAFT', -- DRAFT, CONFIRMED, DISPATCHED, COMPLETED, CANCELLED
    
    -- Timestamps
    ordered_at DATETIME,
    confirmed_at DATETIME,
    confirmed_by VARCHAR(50),
    
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    FOREIGN KEY (planning_detail_id) REFERENCES ms_planning_detail(planning_detail_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (time_window_id) REFERENCES ms_time_window(time_window_id),
    FOREIGN KEY (product_id) REFERENCES ms_product(product_id),
    FOREIGN KEY (contract_tariff_id) REFERENCES ms_client_contract_tariff(contract_tariff_id),
    
    INDEX idx_client_date (client_id, trip_date),
    INDEX idx_order_source (order_source),
    INDEX idx_status (status)
)
```

---

## 4. COMPARISON: WEEKLY vs AD-HOC

| Aspect | Weekly Planning | Ad-hoc Order |
|--------|----------------|--------------|
| **Client Type** | Contract (VIP, High volume) | Spot (SME, Low volume) |
| **Commitment** | Min 20-40 trips/month | No commitment |
| **Planning Horizon** | 1 week ahead | Same day / 1 day ahead |
| **Submission** | Excel upload (Jumat) | WA/Email/Phone (kapan saja) |
| **Processing** | Batch (semua trips sekaligus) | Individual (1 order at a time) |
| **Pricing** | Discounted (15% off) | Spot rate (premium) |
| **Example Rate** | Rp 1,020,000 | Rp 1,800,000 |
| **Auto-scheduling** | ✅ Yes (optimize all trips) | ⚠️ Limited (assign available) |
| **Backhaul Opportunity** | ✅ High (planned ahead) | ❌ Low (last minute) |
| **Payment Term** | TOP 30-45 days | TOP 7 days or COD |
| **Cancellation** | ⚠️ Penalty (if < 24h notice) | ✅ Flexible (if > 6h notice) |
| **Driver Assignment** | Optimized (rotation, familiarity) | Quick (first available) |
| **System Flow** | ms_planning_weekly → ms_planning_detail → ms_order → ms_dispatch | ms_order → ms_dispatch |

---

## 5. MIXED SCENARIO (Hybrid Client)

### 5.1 Realitas Bisnis
```
Client: PT Unilever (Hybrid)

Base Contract:
- 20 trips/month shuttle Cikupa-Bandung (weekly planning)
- Rate: Rp 1,500,000 (contract rate, 10% discount)

Additional Ad-hoc:
- 5-10 trips/month (unpredictable, ad-hoc order)
- Rate: Rp 1,650,000 (spot rate, slightly higher)

Total: 25-30 trips/month
Strategy:
- Submit weekly planning untuk 20 trips (rutin)
- Call/WA untuk 5-10 trips tambahan (urgent/ad-hoc)
```

### 5.2 System Handling

```sql
-- PT Unilever Week 47 (18-24 Nov 2025)

-- Weekly Planning: 5 trips (from Excel)
INSERT INTO ms_planning_weekly VALUES
('PW001', 'PLAN-UNI-2025-W47', 'CL002', 'CCT003', 
47, '2025-11-18', '2025-11-24', 2025,
'unilever_user', '2025-11-15 16:30:00', 'EXCEL_UPLOAD', '/uploads/planning_unilever_w47.xlsx',
'APPROVED', 5, 75.0,
'dispatcher01', '2025-11-15 17:00:00', 'manager01', '2025-11-16 09:00:00',
'Regular weekly planning', GETDATE(), NULL);

-- Ad-hoc Order: 2 trips (from WhatsApp)
INSERT INTO ms_order VALUES
('ORD001', 'ORD-UNI-20251118-001', 'CL002',
'ADHOC_WA', NULL, -- ⭐ ad-hoc, no planning_detail_id
'R001', '2025-11-18', 'TW006',
'PRD003', 15.0, 30.0, 300,
'CCT003', 1650000, 1, -- ⭐ spot rate, lebih mahal
'Siti Logistic Coordinator', '082345678902', 'siti@unilever.co.id',
'Urgent order, butuh besok pagi', 1, 0, NULL,
'CONFIRMED',
'2025-11-17 14:30:00', '2025-11-17 14:35:00', 'dispatcher01',
'Ad-hoc order via WhatsApp', 'dispatcher01', GETDATE(), NULL);

-- Total Unilever Week 47: 5 (planning) + 2 (ad-hoc) = 7 trips
```

---

## 6. CLIENT CONFIGURATION

### 6.1 ms_client Enhancement

```sql
ALTER TABLE ms_client ADD COLUMN planning_mode VARCHAR(50); 
-- WEEKLY_ONLY, ADHOC_ONLY, HYBRID

UPDATE ms_client SET planning_mode = 'WEEKLY_ONLY' WHERE client_id = 'CL001'; -- PT Indofood
UPDATE ms_client SET planning_mode = 'HYBRID' WHERE client_id = 'CL002'; -- PT Unilever
UPDATE ms_client SET planning_mode = 'ADHOC_ONLY' WHERE client_id = 'CL003'; -- CV ABC
```

### 6.2 Query: Client Planning Mode

```sql
SELECT 
    c.client_code,
    c.client_name,
    c.contract_type,
    c.planning_mode,
    c.minimum_monthly_trips,
    
    -- Current month statistics
    (SELECT COUNT(*) 
     FROM ms_planning_weekly pw 
     WHERE pw.client_id = c.client_id 
       AND pw.week_start_date >= DATEADD(month, DATEDIFF(month, 0, GETDATE()), 0)
       AND pw.status = 'APPROVED') as weekly_plannings_this_month,
    
    (SELECT COUNT(*) 
     FROM ms_order o 
     WHERE o.client_id = c.client_id 
       AND o.order_source LIKE 'ADHOC%'
       AND o.trip_date >= DATEADD(month, DATEDIFF(month, 0, GETDATE()), 0)
       AND o.status IN ('CONFIRMED', 'DISPATCHED', 'COMPLETED')) as adhoc_orders_this_month,
    
    (SELECT SUM(pw.total_trips)
     FROM ms_planning_weekly pw 
     WHERE pw.client_id = c.client_id 
       AND pw.week_start_date >= DATEADD(month, DATEDIFF(month, 0, GETDATE()), 0)
       AND pw.status = 'APPROVED') as total_planned_trips,
    
    c.total_trips_ytd,
    
    CASE 
        WHEN c.planning_mode = 'WEEKLY_ONLY' THEN 'Submit Excel setiap Jumat'
        WHEN c.planning_mode = 'ADHOC_ONLY' THEN 'Order via WA/Email/Phone'
        WHEN c.planning_mode = 'HYBRID' THEN 'Weekly Planning + Ad-hoc Order'
    END as instruction
    
FROM ms_client c
WHERE c.is_active = 1
ORDER BY c.total_revenue_ytd DESC;

Result:
| Client       | Type     | Planning Mode | Min Trips | Weekly Plans | Ad-hoc Orders | Instruction                   |
|--------------|----------|---------------|-----------|--------------|---------------|-------------------------------|
| PT Indofood  | CONTRACT | WEEKLY_ONLY   | 40        | 4            | 0             | Submit Excel setiap Jumat     |
| PT Unilever  | HYBRID   | HYBRID        | 20        | 3            | 8             | Weekly Planning + Ad-hoc      |
| CV ABC       | SPOT     | ADHOC_ONLY    | 0         | 0            | 12            | Order via WA/Email/Phone      |
```

---

## 7. DISPATCHER DASHBOARD (Combined View)

### 7.1 UI Mockup

```
┌────────────────────────────────────────────────────────────────────┐
│ DISPATCHER DASHBOARD - Week 47 (18-24 Nov 2025)                    │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│ [Weekly Planning] [Ad-hoc Orders] [All Trips] [Calendar View]     │
│                                                                    │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ WEEKLY PLANNING (Pending Assignment)                         │  │
│ ├──────────────────────────────────────────────────────────────┤  │
│ │ PT Indofood - Week 47                      [6 trips] [Assign]│  │
│ │ PT Unilever - Week 47                      [5 trips] [Assign]│  │
│ │ PT Wings - Week 47                         [8 trips] [Assign]│  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ AD-HOC ORDERS (Today & Tomorrow)                             │  │
│ ├──────────────────────────────────────────────────────────────┤  │
│ │ ⚡ CV ABC - Tomorrow 06:00, Cikupa-Bandung    [Quick Assign] │  │
│ │ ⚡ PT XYZ - Today 14:00, Jakarta-Semarang     [Quick Assign] │  │
│ │ ⚡ Toko Maju - Tomorrow 08:00, Bandung-Garut  [Quick Assign] │  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ CALENDAR VIEW - Monday 18 Nov 2025                           │  │
│ ├──────────────────────────────────────────────────────────────┤  │
│ │ Window 4 (06:00-08:00)                                       │  │
│ │ ├─ [P] PT Indofood - Cikupa-Bandung (DRV001, VH001)          │  │
│ │ ├─ [P] PT Unilever - Cikupa-Bandung (DRV002, VH002)          │  │
│ │ └─ [A] CV ABC - Cikupa-Bandung (PENDING ASSIGNMENT)          │  │
│ │                                                              │  │
│ │ Window 5 (08:00-10:00)                                       │  │
│ │ ├─ [P] PT Indofood - Cikupa-Bandung (DRV003, VH003)          │  │
│ │ └─ [P] PT Wings - Jakarta-Surabaya (DRV004, VH004)           │  │
│ │                                                              │  │
│ │ Legend: [P] = From Planning, [A] = Ad-hoc Order              │  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│ STATISTICS:                                                        │
│ - Total trips this week: 45 (32 from planning, 13 ad-hoc)         │
│ - Assigned: 38 trips (84%)                                         │
│ - Pending: 7 trips (16%)                                           │
│ - Utilization: 76% (38/50 available truck-days)                    │
└────────────────────────────────────────────────────────────────────┘
```

---

## 8. BUSINESS RULES

### 8.1 Weekly Planning Rules

1. **Submission Deadline**: Jumat 17:00 untuk minggu depan
2. **Late Submission**: Diterima tapi **treated as ad-hoc** (no discount)
3. **Minimum Trips**: Client harus commit min 20-40 trips/month untuk dapat weekly planning privilege
4. **Cancellation**: 
   - > 48 jam sebelum trip: Free cancellation
   - 24-48 jam: Warning (3x warning = lose privilege)
   - < 24 jam: Penalty 50% dari trip rate
5. **Amendment**: Boleh edit planning sampai Senin 12:00 (sebelum execution week)

### 8.2 Ad-hoc Order Rules

1. **Minimum Notice**: 6 jam sebelum trip (jika < 6 jam, premium charge +20%)
2. **Availability**: First come, first served (jika tidak ada truck, reject)
3. **Pricing**: Spot rate (no discount)
4. **Payment**: TOP 7 hari atau COD (tergantung client credit rating)
5. **Cancellation**: 
   - > 6 jam sebelum trip: Free cancellation
   - < 6 jam: Penalty 30% dari trip rate

### 8.3 Priority Rules

```
Priority Order (jika konflik availability):

1. Weekly Planning - Contract Client ⭐⭐⭐ HIGHEST
   └─ Commit volume, dapat discount, predictable

2. Ad-hoc - Hybrid Client (contract + spot) ⭐⭐
   └─ Good customer, sometimes urgent

3. Ad-hoc - Spot Client ⭐
   └─ Pay premium, fill capacity gaps
```

---

## 9. REPORTING

### 9.1 Weekly Planning vs Ad-hoc Report

```sql
SELECT 
    DATEPART(YEAR, o.trip_date) as year,
    DATEPART(WEEK, o.trip_date) as week_number,
    
    -- From Weekly Planning
    COUNT(CASE WHEN o.order_source = 'WEEKLY_PLANNING' THEN 1 END) as trips_from_planning,
    SUM(CASE WHEN o.order_source = 'WEEKLY_PLANNING' THEN o.quoted_rate ELSE 0 END) as revenue_from_planning,
    
    -- From Ad-hoc
    COUNT(CASE WHEN o.order_source LIKE 'ADHOC%' THEN 1 END) as trips_from_adhoc,
    SUM(CASE WHEN o.order_source LIKE 'ADHOC%' THEN o.quoted_rate ELSE 0 END) as revenue_from_adhoc,
    
    -- Total
    COUNT(*) as total_trips,
    SUM(o.quoted_rate) as total_revenue,
    
    -- Percentage
    CAST(COUNT(CASE WHEN o.order_source = 'WEEKLY_PLANNING' THEN 1 END) AS FLOAT) / COUNT(*) * 100 as pct_planning,
    CAST(COUNT(CASE WHEN o.order_source LIKE 'ADHOC%' THEN 1 END) AS FLOAT) / COUNT(*) * 100 as pct_adhoc
    
FROM ms_order o
WHERE o.status IN ('CONFIRMED', 'DISPATCHED', 'COMPLETED')
  AND o.trip_date >= '2025-01-01'
GROUP BY DATEPART(YEAR, o.trip_date), DATEPART(WEEK, o.trip_date)
ORDER BY year DESC, week_number DESC;

Result:
| Year | Week | Planning Trips | Planning Revenue | Ad-hoc Trips | Ad-hoc Revenue | % Planning | % Ad-hoc |
|------|------|----------------|------------------|--------------|----------------|------------|----------|
| 2025 | 47   | 32             | Rp 38,400,000    | 13           | Rp 23,400,000  | 71%        | 29%      |
| 2025 | 46   | 35             | Rp 42,000,000    | 10           | Rp 18,000,000  | 78%        | 22%      |
| 2025 | 45   | 30             | Rp 36,000,000    | 15           | Rp 27,000,000  | 67%        | 33%      |
```

---

## 10. SUMMARY

### ✅ 2 Planning Modes:

1. **WEEKLY PLANNING** (Contract Client)
   - Submit Excel Jumat untuk minggu depan
   - Batch processing & assignment
   - Discounted rate
   - High volume, predictable
   - Database: `ms_planning_weekly` → `ms_planning_detail` → `ms_order` → `ms_dispatch`

2. **AD-HOC ORDER** (Spot Client)
   - Order via WA/Email/Phone kapan saja
   - Individual processing (1 by 1)
   - Spot rate (premium)
   - Low volume, unpredictable
   - Database: `ms_order` → `ms_dispatch`

3. **HYBRID** (Mixed Client)
   - Combine both: Weekly planning untuk base volume + Ad-hoc untuk urgent
   - Flexible pricing (contract rate untuk planning, spot rate untuk ad-hoc)

### ✅ Key Fields:
- `ms_client.planning_mode`: WEEKLY_ONLY, ADHOC_ONLY, HYBRID
- `ms_order.order_source`: WEEKLY_PLANNING, ADHOC_WA, ADHOC_EMAIL, ADHOC_PHONE, API
- `ms_order.is_spot_rate`: 0 = contract rate, 1 = spot rate

### ✅ Business Benefits:
- **Flexibility**: Support all client types (big contract & small spot)
- **Revenue optimization**: Contract client dapat discount tapi commit volume, Spot client bayar premium
- **Capacity utilization**: Ad-hoc order fill gaps between planned trips
- **Fair pricing**: Different rate untuk different commitment level

**Apakah konsep 2 mode planning (Weekly + Ad-hoc) ini sudah sesuai dengan realitas bisnis Anda?** 📅🚛

