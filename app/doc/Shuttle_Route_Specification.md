# TMS - Shuttle Route Management (Detail Specification)

**Tanggal:** 17 November 2025  
**Modul:** Shuttle Route Management  
**Status:** Design Phase

---

## 1. DEFINISI SHUTTLE ROUTE

### 1.1 Apa itu Shuttle Route?
**Shuttle Route** adalah rute pengiriman bolak-balik (round-trip) antara dua titik tetap (Origin ↔ Destination) yang dilakukan secara berulang dan terjadwal.

**Karakteristik:**
- **Origin & Destination tetap** (contoh: Jakarta ↔ Bandung)
- **Berulang/recurring** (harian, mingguan, atau sesuai jadwal)
- **Round-trip atau one-way** (bisa pulang pergi atau sekali jalan saja)
- **Time window based** (dijadwalkan per window 2 jam)
- **Driver & truck assigned** (biasanya driver/truck tertentu yang familiar)

### 1.2 Perbedaan Shuttle vs Multi-Point vs Ad-hoc

| Aspek | Shuttle | Multi-Point (FMCG) | Ad-hoc |
|-------|---------|-------------------|--------|
| **Route Pattern** | A ↔ B (fixed) | A → B → C → D → A (variable) | A → X (random) |
| **Frequency** | Scheduled/recurring | Per order basis | One-time |
| **Planning** | Weekly planning | Per order grouping | Immediate |
| **Stops** | 2 points only | Multiple points (3-20+) | Variable |
| **Optimization** | Minimize idle time | Minimize total distance | Speed priority |
| **Example** | Jakarta-Bandung daily shuttle | FMCG distribution to 10 stores | Emergency delivery |

---

## 2. SHUTTLE ROUTE BUSINESS MODEL

### 2.1 Use Cases Shuttle Route

#### A. Contract-based Shuttle (Kontrak Reguler)
```
Scenario: PT ABC memiliki pabrik di Jakarta dan warehouse di Bandung
- Kebutuhan: Kirim material setiap hari Senin-Jumat
- Volume: 2-3 trip per hari
- Time window: Window 4 (06:00-08:00) dan Window 8 (14:00-16:00)
- Truck dedicated: 2 unit truck assigned khusus PT ABC
- Driver dedicated: 4 driver rotation

Planning:
- Week planning: 10 trips/week (Mon-Fri, 2 trips/day)
- Auto-assign driver & truck yang familiar
- Pre-calculated cost (kontrak bulanan)
```

#### B. Spot-based Shuttle (Shuttle Spot/Adhoc)
```
Scenario: PT XYZ butuh shuttle untuk project temporary (2 minggu)
- Kebutuhan: 5 trips dalam 2 minggu (tidak tentu hari/jam)
- Volume: 1-2 trip per hari (random)
- Time window: Flexible (tergantung ketersediaan)
- Truck: Available truck (tidak dedicated)
- Driver: Available driver (tidak dedicated)

Planning:
- Ad-hoc planning: per trip basis
- Find available driver & truck
- Variable pricing (spot rate)
```

#### C. Mixed Shuttle (Contract + Spot)
```
Scenario: PT DEF punya contract 20 trips/month, tapi kadang butuh extra
- Contract base: 20 trips guaranteed per month
- Spot: 5-10 trips additional per month (flexible)
- Pricing: Contract rate + spot rate premium

Planning:
- Contract trips: planned weekly
- Spot trips: ad-hoc insertion
```

---

## 3. SHUTTLE ROUTE PLANNING WORKFLOW

### 3.1 Weekly Planning Process (Contract Shuttle)

#### Step 1: Client Submit Planning Request
```
Client send Excel file (weekly planning):

| Date       | Window | Origin  | Destination | Qty (ton) | Notes |
|------------|--------|---------|-------------|-----------|-------|
| 2025-11-18 | 4      | Jakarta | Bandung     | 12        | -     |
| 2025-11-18 | 8      | Jakarta | Bandung     | 10        | -     |
| 2025-11-19 | 4      | Jakarta | Bandung     | 15        | -     |
| 2025-11-19 | 8      | Jakarta | Bandung     | 12        | -     |
| 2025-11-20 | 4      | Jakarta | Bandung     | 14        | Urgent|
| ... (total 10 trips untuk week ini)
```

#### Step 2: System Import & Validation
```
Validation checks:
✅ Route exist? (Jakarta-Bandung already in ms_route)
✅ Date dalam range week yang dipilih?
✅ Window valid (1-12)?
✅ Qty feasible (tidak exceed max truck capacity)?
✅ No conflict dengan planning existing?

Result:
- 10 trips imported ke ms_planning_weekly
- Status: Draft
- Route_id auto-assigned: JKT-BDG-01
```

#### Step 3: Auto Scheduling
```
System analyze:
1. Route: JKT-BDG-01
   - Distance: 150 km
   - Estimated time: 3 hours one-way
   - Round-trip time: 6 hours total
   
2. Window constraints:
   - Window 4 (06:00-08:00) → Depart 06:00, arrive 09:00, return 12:00
   - Window 8 (14:00-16:00) → Depart 14:00, arrive 17:00, return 20:00
   
3. Driver & Truck availability:
   - Check ms_availability for Window 4 & 8 per day
   - Find drivers dengan familiarity EXPERT/EXPERIENCED untuk route JKT-BDG
   
4. Assignment logic:
   - Day 1 (Mon):
     * Trip 1 (Window 4): Driver A + Truck 1
     * Trip 2 (Window 8): Driver B + Truck 2
   - Day 2 (Tue):
     * Trip 1 (Window 4): Driver C + Truck 1 (truck kembali, ready pagi)
     * Trip 2 (Window 8): Driver D + Truck 2
   - ... rotation untuk fairness
```

#### Step 4: Dispatcher Review & Manual Adjustment
```
Dashboard tampilkan:

Weekly Planning: Jakarta-Bandung Shuttle (Week 47/2025)
┌──────────┬─────────┬─────────┬─────────┬─────────┬─────────┐
│          │ Mon     │ Tue     │ Wed     │ Thu     │ Fri     │
├──────────┼─────────┼─────────┼─────────┼─────────┼─────────┤
│ Window 4 │ Driver A│ Driver C│ Driver A│ Driver C│ Driver A│
│ (06-08)  │ Truck 1 │ Truck 1 │ Truck 1 │ Truck 1 │ Truck 1 │
│          │ 12 ton  │ 15 ton  │ 14 ton  │ 13 ton  │ 12 ton  │
├──────────┼─────────┼─────────┼─────────┼─────────┼─────────┤
│ Window 8 │ Driver B│ Driver D│ Driver B│ Driver D│ Driver B│
│ (14-16)  │ Truck 2 │ Truck 2 │ Truck 2 │ Truck 2 │ Truck 2 │
│          │ 10 ton  │ 12 ton  │ 11 ton  │ 14 ton  │ 13 ton  │
└──────────┴─────────┴─────────┴─────────┴─────────┴─────────┘

Actions available:
- [Swap] driver/truck
- [Change] window
- [Add] extra trip
- [Remove] trip
- [Mark] external rental (jika truck internal tidak cukup)
```

#### Step 5: Approval & Dispatch Generation
```
Manager review & approve:
✅ Resource allocation OK
✅ Cost calculation match budget
✅ No overtime issues
✅ Compliance with customer contract

System generate:
- 10 dispatch orders (ms_dispatch)
- Update ms_availability (mark booked)
- Calculate cost per trip:
  * Uang jalan: Rp 400,000 per trip
  * Uang jasa: Rp 300,000 per trip
  * Toll: Rp 65,000 per trip
  * Fuel: Rp 350,000 per trip
  * Total cost: Rp 1,115,000 per trip
  * Revenue (tariff): Rp 1,500,000 per trip
  * Margin: Rp 385,000 per trip (25.7%)

Export planning:
- PDF untuk driver (weekly schedule masing-masing)
- Excel untuk management review
```

---

### 3.2 Daily Execution Process

#### Morning (Window 4: 06:00-08:00)
```
06:00 - Driver A check-in via mobile app
        - GPS position: Jakarta origin
        - Status: Departed
        - Load: 12 ton confirmed
        
06:05 - GPS tracking active (update every 1 min)
        
09:15 - Arrival notification (GPS geofence detected)
        - Location: Bandung destination
        - Status: Arrived
        - Unload & POD capture (photo + signature)
        
09:45 - Return departure
        - Status: Returning
        
12:30 - Arrival back to Jakarta
        - Status: Completed
        - Trip summary:
          * Actual distance: 152 km (vs est. 150 km)
          * Actual time: 3.25 hours (vs est. 3 hours)
          * Variance: +2 km, +0.25 hours
          * Reason: Light traffic, smooth trip
```

#### Afternoon (Window 8: 14:00-16:00)
```
14:00 - Driver B check-in
        - GPS position: Jakarta origin
        - Status: Departed
        - Load: 10 ton confirmed
        
17:30 - Arrival notification (delayed 30 min)
        - Location: Bandung destination
        - Status: Arrived (Late)
        - Alert sent to dispatcher: "Trip delayed 30 min"
        - Reason: Heavy traffic at Cipularang
        
18:00 - Return departure
        
21:00 - Arrival back to Jakarta
        - Status: Completed
        - Trip summary:
          * Actual time: 3.5 hours (vs est. 3 hours)
          * Variance: +0.5 hours
          * Driver notes: "Heavy rain + traffic jam"
          * System update ms_route_history
```

---

## 4. SHUTTLE ROUTE FEATURES (Specific)

### 4.1 Round-Trip Configuration
```sql
-- Tambahan field di ms_route untuk shuttle
ALTER TABLE ms_route ADD COLUMN is_roundtrip BIT DEFAULT 1;
ALTER TABLE ms_route ADD COLUMN roundtrip_time_hours DECIMAL(5,2);
ALTER TABLE ms_route ADD COLUMN rest_time_minutes INT; -- istirahat driver sebelum return

Example:
Route: Jakarta-Bandung Shuttle
- is_roundtrip: 1 (YES)
- distance_km: 150 (one-way)
- estimated_time_hours: 3.0 (one-way)
- roundtrip_time_hours: 6.5 (3 + 3 + 0.5 rest)
- rest_time_minutes: 30

Implication:
- Window 4 (06:00-08:00) departure → Expected return 12:30
- Window 8 (14:00-16:00) departure → Expected return 20:30
- Cannot assign same truck to Window 4 & Window 8 (tidak cukup waktu)
```

### 4.2 Backhaul Management (Muatan Balik)
```
Scenario: Trip Jakarta → Bandung kosong return, boros biaya

Backhaul opportunity:
- Origin trip: Jakarta → Bandung (12 ton, PT ABC)
- Return trip: Bandung → Jakarta (10 ton, PT XYZ) ← NEW OPPORTUNITY

System features:
1. Backhaul matching:
   - Cari order yang origin = current destination
   - Timeframe match dengan return window
   - Capacity match dengan truck available

2. Cost optimization:
   - Original cost (no backhaul): Rp 1,500,000
   - Backhaul revenue: Rp 1,200,000
   - Net cost: Rp 300,000
   - Savings: 80%!

3. Planning integration:
   - Mark trip as "Backhaul available"
   - Auto-suggest matching orders
   - One dispatch untuk 2 orders (origin + return)
```

Database:
```sql
CREATE TABLE ms_dispatch_backhaul (
    backhaul_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50), -- dispatch utama
    order_id VARCHAR(50), -- order untuk backhaul
    origin VARCHAR(100), -- origin backhaul (= destination utama)
    destination VARCHAR(100), -- destination backhaul (= origin utama)
    qty DECIMAL(10,2),
    revenue DECIMAL(15,2),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (order_id) REFERENCES ms_order(order_id)
)
```

### 4.3 Shuttle Frequency Management
```
Customer contract variations:
1. Daily shuttle (Mon-Fri)
   - 5 days x 2 trips = 10 trips/week
   - Dedicated truck & driver
   
2. Bi-weekly shuttle (Mon, Wed, Fri)
   - 3 days x 2 trips = 6 trips/week
   - Shared truck (alternating with other shuttle)
   
3. Weekly shuttle (every Monday)
   - 1 day x 1 trip = 1 trip/week
   - Shared truck & driver
   
4. Ad-hoc shuttle (on-demand)
   - Variable trips/week
   - Non-dedicated resources

Configuration in ms_route_tariff:
```sql
ALTER TABLE ms_route_tariff ADD COLUMN frequency_type VARCHAR(20); 
-- DAILY, BIWEEKLY, WEEKLY, ADHOC

ALTER TABLE ms_route_tariff ADD COLUMN minimum_trips INT; 
-- contract minimum trips per month

ALTER TABLE ms_route_tariff ADD COLUMN discounted_rate DECIMAL(15,2);
-- rate untuk contract vs spot

Example:
- Daily shuttle contract: 40 trips/month minimum, rate Rp 1,200,000/trip
- Spot shuttle: No minimum, rate Rp 1,500,000/trip (25% premium)
```

### 4.4 Shuttle Performance Tracking
```
KPI per Shuttle Route:

1. On-Time Performance (OTP)
   - Target: 95% trips on-time (variance < 30 min)
   - Actual: 88% (8 late trips dari 50 total trips bulan ini)
   - Action: Investigate delay causes

2. Cost Variance
   - Estimated cost/trip: Rp 1,115,000
   - Actual avg cost/trip: Rp 1,180,000
   - Variance: +5.8%
   - Reason: Fuel price increase + toll adjustment

3. Truck Utilization
   - Truck 1: 22 trips/month (dedicated Jakarta-Bandung)
   - Capacity: 15 ton, avg load: 12.5 ton
   - Utilization: 83% (good)
   - Backhaul rate: 60% (12 trips ada backhaul)

4. Driver Performance
   - Driver A: 15 trips, OTP 93%, avg rating 4.5/5
   - Driver B: 10 trips, OTP 90%, avg rating 4.2/5
   - Driver C: 12 trips, OTP 95%, avg rating 4.7/5 ⭐ BEST
   - Driver D: 13 trips, OTP 85%, avg rating 4.0/5 ⚠ Need coaching

Dashboard view:
┌────────────────────────────────────────────────────────┐
│ Shuttle Route: Jakarta-Bandung (Month: Nov 2025)       │
├────────────────────────────────────────────────────────┤
│ Total Trips: 50                                         │
│ On-Time: 44 (88%) ⚠ Below target (95%)                 │
│ Late: 6 (12%)                                           │
│ Cancelled: 0 (0%)                                       │
├────────────────────────────────────────────────────────┤
│ Revenue: Rp 75,000,000                                  │
│ Cost: Rp 59,000,000                                     │
│ Margin: Rp 16,000,000 (21.3%)                           │
├────────────────────────────────────────────────────────┤
│ Top Delay Reasons:                                      │
│ 1. Traffic jam (4 times)                                │
│ 2. Weather (rain) (2 times)                             │
│ 3. Truck breakdown (0 times)                            │
├────────────────────────────────────────────────────────┤
│ [View Detail] [Export Report] [Action Plan]            │
└────────────────────────────────────────────────────────┘
```

---

## 5. SHUTTLE ROUTE COSTING

### 5.1 Cost Structure per Trip
```
Route: Jakarta-Bandung Shuttle (One-way)

Direct Costs:
1. Fuel: 
   - Distance: 150 km
   - Consumption: 4 km/liter (loaded truck)
   - Fuel needed: 150/4 = 37.5 liter
   - Price: Rp 9,500/liter
   - Total: 37.5 x 9,500 = Rp 356,250
   
2. Toll:
   - Cipularang toll: Rp 65,000
   
3. Driver wage (uang jalan):
   - Base: Rp 300,000
   - Distance bonus: 150 km x Rp 500 = Rp 75,000
   - Total: Rp 375,000
   
4. Driver fee (uang jasa):
   - Base: Rp 250,000
   - Performance bonus: Rp 50,000 (if on-time)
   - Total: Rp 300,000
   
5. Other costs:
   - Parkir: Rp 20,000
   - Retribusi: Rp 10,000
   - Total: Rp 30,000

Total Direct Cost: Rp 1,126,250

Indirect Costs (allocated):
6. Truck depreciation: Rp 50,000/trip
7. Maintenance reserve: Rp 30,000/trip
8. Insurance: Rp 20,000/trip
9. Overhead: Rp 40,000/trip

Total Indirect Cost: Rp 140,000

TOTAL COST per trip: Rp 1,266,250
```

### 5.2 Round-Trip Costing
```
Scenario 1: No backhaul (return empty)
- Origin trip cost: Rp 1,266,250
- Return trip cost: Rp 1,126,250 (no muatan, same cost)
- Total cost: Rp 2,392,500
- Revenue: Rp 1,500,000 (only origin trip)
- LOSS: Rp 892,500 ❌

Scenario 2: With backhaul (return loaded)
- Origin trip cost: Rp 1,266,250
- Return trip cost: Rp 1,126,250
- Total cost: Rp 2,392,500
- Revenue origin: Rp 1,500,000
- Revenue backhaul: Rp 1,200,000
- Total revenue: Rp 2,700,000
- PROFIT: Rp 307,500 ✅ (12.8% margin)

Conclusion: Backhaul sangat penting untuk shuttle profitability!
```

### 5.3 Contract vs Spot Pricing
```
Contract Shuttle (guaranteed volume):
- Minimum: 40 trips/month
- Rate: Rp 1,200,000/trip (discounted)
- Monthly revenue: Rp 48,000,000
- Cost: Rp 40,000,000 (truck dedicated, efficiency)
- Margin: Rp 8,000,000 (16.7%)
- Stability: High (predictable cash flow)

Spot Shuttle (ad-hoc):
- Volume: 10 trips/month (variable)
- Rate: Rp 1,500,000/trip (premium)
- Monthly revenue: Rp 15,000,000
- Cost: Rp 12,000,000 (less efficient, no dedicated resource)
- Margin: Rp 3,000,000 (20%)
- Stability: Low (unpredictable)

Mixed Strategy (optimal):
- Contract: 40 trips @ Rp 1,200,000 = Rp 48M
- Spot: 10 trips @ Rp 1,500,000 = Rp 15M
- Total revenue: Rp 63M
- Total cost: Rp 52M
- Total margin: Rp 11M (17.5%)
- Balance: Stability + upside potential
```

---

## 6. SHUTTLE ROUTE SCHEDULING RULES

### 6.1 Time Window Constraints
```
Rule 1: Round-trip time must fit within operational hours
- Max operational: 16 hours/day (06:00-22:00)
- Round-trip: 6.5 hours (Jakarta-Bandung-Jakarta)
- Buffer: 1 hour (loading/unloading)
- Total: 7.5 hours needed

Valid windows untuk one round-trip:
✅ Window 4 (06:00) → Return by 13:30 (OK)
✅ Window 5 (08:00) → Return by 15:30 (OK)
✅ Window 6 (10:00) → Return by 17:30 (OK)
✅ Window 7 (12:00) → Return by 19:30 (OK)
✅ Window 8 (14:00) → Return by 21:30 (OK)
❌ Window 9 (16:00) → Return by 23:30 (EXCEED operational hours!)

Rule 2: Same truck cannot do 2 round-trips dalam sehari
- Trip 1: Window 4 (06:00-13:30) = 7.5 hours
- Trip 2: Window 8 (14:00-21:30) = 7.5 hours
- Total: 15 hours (possible tapi driver kecapekan)
- ⚠ Need 2 different drivers (shift rotation)

Rule 3: Driver rest period (compliance)
- Max driving: 8 hours/day
- Min rest: 8 hours between shifts
- Driver A: Trip Window 4 Mon (06:00-13:30)
  → Earliest next trip: Tue Window 4 (22 hours rest) ✅
  → Cannot: Mon Window 8 (only 0.5 hour rest) ❌
```

### 6.2 Resource Allocation Rules
```
Rule 1: Dedicated vs Shared Resources

Dedicated (untuk high-frequency shuttle):
- Truck: Assigned 100% ke satu route
- Driver: Pool of 3-4 drivers rotation untuk satu route
- Advantage: Familiar, efficient, high OTP
- Disadvantage: Underutilization jika demand turun

Shared (untuk low-frequency shuttle):
- Truck: Multi-route, scheduled flexibly
- Driver: Multi-route, assigned per trip
- Advantage: High utilization, flexible
- Disadvantage: Less familiar, lower OTP

Criteria untuk dedicated resource:
- Frequency ≥ 20 trips/month
- Contract duration ≥ 6 months
- Customer priority: HIGH
- Route profitability: >15% margin

Rule 2: Driver Rotation (untuk fairness)
- Driver A: 40% trips
- Driver B: 30% trips
- Driver C: 20% trips
- Driver D: 10% trips (backup)

Rotation pattern (2-week cycle):
Week 1: A-B-A-C-A-B-A-C-A-D
Week 2: B-A-C-A-B-A-D-A-C-A

Ensure:
- No driver monopoli (max 50%)
- All drivers maintain familiarity (min 10% trips)
- Backup driver always available
```

### 6.3 Conflict Resolution Rules
```
Scenario: 2 planning untuk route yang sama, window yang sama

Planning A: Jakarta-Bandung, Mon Window 4, 12 ton
Planning B: Jakarta-Bandung, Mon Window 4, 10 ton

Conflict: Only 1 truck available

Resolution priority:
1. Customer priority (contract > spot)
2. Booking time (first come first serve)
3. Profitability (higher margin wins)
4. Alternative window (suggest reschedule)
5. External rental (cost increase, customer pays)

System suggest:
Option 1: Planning A → Internal truck (priority: contract)
          Planning B → Reschedule to Window 5 (2 jam later)
          
Option 2: Planning A → Internal truck
          Planning B → External rental (premium Rp 300k)
          
Option 3: Combine trips (jika customer agree)
          - One truck, 2 trips (Window 4 + Window 5)
          - Total: 22 ton (might exceed capacity!)
          - ❌ Not feasible (truck max 20 ton)
```

---

## 7. SHUTTLE ROUTE OPTIMIZATION

### 7.1 Route Selection Optimization
```
Scenario: Jakarta → Bandung ada 3 alternative routes

Route A: Via Tol Cipularang (fastest)
- Distance: 150 km
- Time: 3 hours
- Toll: Rp 65,000
- Fuel: Rp 356,000
- Total cost: Rp 421,000
- Suitable for: Time-sensitive, premium service

Route B: Via Puncak (scenic)
- Distance: 180 km
- Time: 5 hours
- Toll: Rp 0
- Fuel: Rp 427,000
- Total cost: Rp 427,000
- Suitable for: No rush, lower budget (tapi lebih lama)

Route C: Via Purwakarta-Subang (mixed)
- Distance: 165 km
- Time: 4 hours
- Toll: Rp 40,000
- Fuel: Rp 391,000
- Total cost: Rp 431,000
- Suitable for: Balanced (medium time, medium cost)

Auto-recommendation:
Default: Route A (fastest, standard shuttle route)
Alternative: Route C (jika customer budget constrained)
Not recommend: Route B (too slow untuk shuttle purpose)
```

### 7.2 Load Optimization (Multiple Small Orders)
```
Scenario: 5 orders Jakarta-Bandung, same day, same window

Order 1: 3 ton
Order 2: 4 ton
Order 3: 5 ton
Order 4: 6 ton
Order 5: 2 ton
Total: 20 ton

Truck capacity: 15 ton max

Option 1: 2 trips
- Trip 1: Order 1+2+3 = 12 ton (Truck A)
- Trip 2: Order 4+5 = 8 ton (Truck B)
- Cost: 2 x Rp 1,200,000 = Rp 2,400,000
- Efficiency: 67% (12/15 + 8/15 = 133% / 2 trucks)

Option 2: 2 trips (optimized)
- Trip 1: Order 2+3+4 = 15 ton (Truck A) ✅ FULL
- Trip 2: Order 1+5 = 5 ton (Truck B)
- Cost: Rp 2,400,000 (same)
- Efficiency: 67% (15/15 + 5/15 = 133% / 2 trucks)
- Better: Trip 1 full capacity (better fuel efficiency)

Option 3: Suggest delay (jika flexible)
- Delay Order 5 ke window berikutnya
- Trip 1: Order 2+3+4 = 15 ton (Truck A) ✅ FULL
- Trip 2 (next window): Order 1+5 + new orders = 10+ ton
- Efficiency: Better (wait untuk accumulate lebih banyak)
```

### 7.3 Backhaul Matching Algorithm
```
System logic untuk find backhaul opportunities:

Step 1: Identify shuttle dispatch tanpa backhaul
- Dispatch D001: Jakarta → Bandung, Mon 06:00
- Expected return: Mon 12:30
- Current status: No backhaul

Step 2: Search matching orders
Query:
SELECT * FROM ms_order
WHERE origin_name = 'Bandung' -- destination shuttle jadi origin backhaul
  AND destination_name = 'Jakarta' -- origin shuttle jadi dest backhaul
  AND delivery_date = '2025-11-18' -- same day
  AND time_window >= 10 -- setelah arrival (09:30)
  AND time_window <= 12 -- sebelum return window
  AND qty <= 15 -- capacity match
  AND status = 'PENDING'

Result:
- Order O123: Bandung → Jakarta, Mon, 10 ton, Window 10 (pickup 10:00-12:00)

Step 3: Calculate backhaul economics
- Additional cost: Rp 100,000 (pickup + loading time)
- Revenue: Rp 1,200,000
- Net benefit: Rp 1,100,000
- Decision: ✅ ACCEPT (highly profitable)

Step 4: Auto-assign atau suggest to dispatcher
- Auto-assign if: margin > 50% AND no conflict
- Suggest if: need approval (special customer, tight schedule)
```

---

## 8. SHUTTLE ROUTE MONITORING

### 8.1 Real-Time Dashboard
```
┌────────────────────────────────────────────────────────────────┐
│ SHUTTLE MONITORING - Jakarta Bandung Route (Today: Mon)        │
├────────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────┐   │
│ │              MAP VIEW (Real-time GPS)                     │   │
│ │                                                           │   │
│ │  [Origin: Jakarta] ─────────────────→ [Dest: Bandung]    │   │
│ │                                                           │   │
│ │  🚛 Truck 1 (70% progress) Driver A - On track          │   │
│ │     └─ ETA: 09:15 (on-time)                              │   │
│ │                                                           │   │
│ │  🚛 Truck 2 (returning) Driver B - Delayed 20 min       │   │
│ │     └─ ETA: 13:00 (late)                                 │   │
│ │                                                           │   │
│ └──────────────────────────────────────────────────────────┘   │
├────────────────────────────────────────────────────────────────┤
│ TODAY'S TRIPS:                                                  │
│                                                                 │
│ Window 4 (06:00-08:00):                                         │
│ ✅ Trip #1 - Driver A, Truck 1, 12 ton - Departed 06:05        │
│    Status: In Transit (70%) - ETA 09:15 ✅ On-time             │
│                                                                 │
│ Window 8 (14:00-16:00):                                         │
│ 🔜 Trip #2 - Driver C, Truck 3, 14 ton - Scheduled 14:00       │
│    Status: Preparing                                            │
│                                                                 │
│ ALERTS: ⚠️ 1 active                                             │
│ • Truck 2 delayed 20 min due to traffic (Driver B)              │
│   Action: Notify customer, update ETA                           │
└────────────────────────────────────────────────────────────────┘
```

### 8.2 Exception Handling
```
Exception 1: Driver sick/emergency
- Trigger: Driver A report sick, 2 hours before departure
- Impact: Trip #1 (Window 4) tidak ada driver
- System action:
  1. Find replacement driver (query available + familiar)
  2. Suggest: Driver D (backup, moderate familiarity)
  3. Auto-notify: Driver D via SMS/app
  4. Update dispatch: assign Driver D
  5. Notify customer: driver change (if required)
  
Exception 2: Truck breakdown
- Trigger: Truck 1 breakdown di route (jam 08:00)
- Impact: Cannot complete trip, delay delivery
- System action:
  1. Alert dispatcher (high priority)
  2. Find nearest rescue truck
  3. Options:
     a. Tow truck + transfer muatan → delay 3 hours
     b. Send replacement truck dari Jakarta → delay 6 hours
     c. External rental → extra cost Rp 500k
  4. Decision: Dispatcher choose option C (customer critical)
  5. Update dispatch: new truck assigned
  6. Track actual cost variance
  
Exception 3: Weather/disaster
- Trigger: Heavy rain + flood warning di Cipularang (06:00)
- Impact: Route tidak aman, estimated delay 2-4 hours
- System action:
  1. Alert all drivers departing Window 4-6
  2. Suggest alternative:
     a. Delay departure 2 hours (wait weather clear)
     b. Alternative route (Via Puncak +2 hours, no toll)
  3. Customer notification: delay risk
  4. Monitor weather update real-time
  5. Decision when safe: proceed or postpone
```

---

## 9. SHUTTLE ROUTE REPORTING

### 9.1 Daily Report
```
SHUTTLE ROUTE DAILY REPORT
Route: Jakarta - Bandung
Date: Monday, 18 November 2025

SUMMARY:
- Planned trips: 2
- Completed: 1
- In progress: 1
- Cancelled: 0

PERFORMANCE:
- On-time trips: 1 (100% so far)
- Late trips: 0
- Avg delay: 0 minutes

COST:
- Estimated cost: Rp 2,400,000 (2 trips)
- Actual cost (partial): Rp 1,150,000 (Trip #1 done)
- Variance: -Rp 50,000 (-4.2%) ✅ Under budget

ISSUES:
- None

TRIP DETAILS:
┌──────┬────────┬────────┬──────┬─────────┬─────────┬────────┐
│ Trip │ Window │ Driver │ Truck│ Depart  │ Arrive  │ Status │
├──────┼────────┼────────┼──────┼─────────┼─────────┼────────┤
│ #1   │ 4      │ A      │ 1    │ 06:05   │ 09:10   │ ✅ Done│
│ #2   │ 8      │ C      │ 3    │ -       │ -       │ 🔜 Sched│
└──────┴────────┴────────┴──────┴─────────┴─────────┴────────┘
```

### 9.2 Weekly Report
```
SHUTTLE ROUTE WEEKLY REPORT
Route: Jakarta - Bandung
Week: 47/2025 (Nov 18-24)

SUMMARY:
- Planned trips: 10 (Mon-Fri, 2/day)
- Completed: 10 (100%)
- Cancelled: 0
- On-time: 9 (90%)
- Late: 1 (10%)

DRIVERS PERFORMANCE:
┌────────┬───────┬─────────┬──────────┬────────┐
│ Driver │ Trips │ On-time │ Avg Time │ Rating │
├────────┼───────┼─────────┼──────────┼────────┤
│ A      │ 4     │ 100%    │ 3.1h     │ ⭐⭐⭐⭐⭐ │
│ B      │ 3     │ 67%     │ 3.4h     │ ⭐⭐⭐   │
│ C      │ 3     │ 100%    │ 2.9h     │ ⭐⭐⭐⭐⭐ │
└────────┴───────┴─────────┴──────────┴────────┘

COST ANALYSIS:
- Total revenue: Rp 15,000,000
- Total cost: Rp 12,100,000
- Margin: Rp 2,900,000 (19.3%)
- Target margin: 20%
- Variance: -0.7% ⚠️ Slightly below target

BACKHAUL PERFORMANCE:
- Backhaul opportunities: 10 (100%)
- Backhaul secured: 6 (60%)
- Backhaul revenue: Rp 7,200,000
- Backhaul margin: Rp 1,500,000
- Impact on profitability: +10.5% ✅

ISSUES & ACTIONS:
1. Driver B performance below par (67% OTP)
   → Action: Coaching session scheduled
2. Fuel cost variance +8% vs estimate
   → Action: Review fuel efficiency, check truck maintenance
3. 4 missed backhaul opportunities
   → Action: Improve backhaul matching algorithm, sales outreach
```

### 9.3 Monthly Report (Executive Summary)
```
SHUTTLE ROUTE MONTHLY EXECUTIVE SUMMARY
Route: Jakarta - Bandung
Month: November 2025

KEY METRICS:
┌──────────────────────┬──────────┬─────────┬──────────┐
│ Metric               │ Actual   │ Target  │ Status   │
├──────────────────────┼──────────┼─────────┼──────────┤
│ Total Trips          │ 50       │ 48      │ ✅ +4%   │
│ On-Time %            │ 88%      │ 95%     │ ⚠️ -7%   │
│ Revenue              │ 75M      │ 72M     │ ✅ +4%   │
│ Cost                 │ 59M      │ 56M     │ ⚠️ +5%   │
│ Margin %             │ 21.3%    │ 22%     │ ⚠️ -0.7% │
│ Backhaul Rate        │ 60%      │ 70%     │ ⚠️ -10%  │
│ Customer Satisfaction│ 4.2/5    │ 4.5/5   │ ⚠️ -0.3  │
└──────────────────────┴──────────┴─────────┴──────────┘

TOP PERFORMERS:
🥇 Driver C - 15 trips, 95% OTP, 4.7/5 rating
🥈 Driver A - 15 trips, 93% OTP, 4.5/5 rating
🥉 Driver D - 13 trips, 92% OTP, 4.3/5 rating

NEEDS IMPROVEMENT:
⚠️ Driver B - 10 trips, 90% OTP, 4.2/5 rating (below threshold)

INSIGHTS:
• Traffic pattern analysis: 
  - Peak delays: Window 7-9 (12:00-18:00)
  - Best windows: Window 4-5 (06:00-10:00)
  - Recommendation: Shift more trips to morning windows

• Cost drivers:
  - Fuel: +8% vs budget (price increase)
  - Toll: stable
  - Uang jalan: stable
  - Action: Renegotiate tariff dengan customer (+5%)

• Growth opportunities:
  - Backhaul: 40% trips masih empty return
  - Potential revenue: +Rp 12M/month
  - Action: Sales team focus backhaul sales

RECOMMENDATIONS:
1. Add 1 more dedicated truck (demand growing)
2. Driver training program (improve OTP)
3. Backhaul sales campaign
4. Tariff adjustment discussion with customer (+5%)
5. Route optimization study (avoid peak hour delays)
```

---

## 10. IMPLEMENTATION CHECKLIST

### Phase 1: Database & Basic CRUD ✅
- [ ] Create/update ms_route table (add shuttle-specific fields)
- [ ] Create ms_dispatch_backhaul table
- [ ] Route CRUD (create, read, update, delete)
- [ ] Shuttle route type flag & validation

### Phase 2: Planning Integration ✅
- [ ] Excel import for shuttle planning
- [ ] Weekly planning dashboard (shuttle view)
- [ ] Auto-assign route ke planning trip
- [ ] Conflict detection & resolution

### Phase 3: Scheduling & Assignment ⏳
- [ ] Round-trip time calculation
- [ ] Driver rotation logic
- [ ] Resource availability check
- [ ] Time window validation

### Phase 4: Backhaul Management ⏳
- [ ] Backhaul opportunity detection
- [ ] Backhaul matching algorithm
- [ ] Cost-benefit analysis
- [ ] Auto-suggest backhaul to dispatcher

### Phase 5: Monitoring & Tracking ⏳
- [ ] Real-time shuttle dashboard
- [ ] GPS tracking integration
- [ ] Exception alerts (delay, breakdown)
- [ ] ETA calculation & update

### Phase 6: Reporting & Analytics ⏳
- [ ] Daily shuttle report
- [ ] Weekly performance report
- [ ] Monthly executive summary
- [ ] Driver performance scorecard

### Phase 7: Optimization ⏳
- [ ] Route selection optimization
- [ ] Load consolidation algorithm
- [ ] Backhaul revenue maximization
- [ ] Time window recommendation

---

## 11. SUCCESS METRICS

### KPIs untuk Shuttle Route:
1. **On-Time Performance (OTP):** ≥95%
2. **Backhaul Rate:** ≥70%
3. **Cost Variance:** ≤5%
4. **Profit Margin:** ≥20%
5. **Truck Utilization:** ≥80%
6. **Driver Satisfaction:** ≥4.5/5
7. **Customer Satisfaction:** ≥4.5/5

---

**Apakah penjelasan Shuttle Route ini sudah detail dan clear? Ada aspek lain yang perlu dijelaskan lebih dalam?** 🚀
