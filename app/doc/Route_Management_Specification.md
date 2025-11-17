# TMS - Route Management Module (Detail Specification)

**Tanggal:** 17 November 2025  
**Modul:** Route Management  
**Status:** Design Phase

---

## 1. OVERVIEW ROUTE MANAGEMENT

### 1.1 Definisi
Route Management adalah modul untuk mengelola rute pengiriman dari origin ke destination, termasuk:
- Master route definition (A to B)
- Route characteristics (jarak, waktu, tipe jalan)
- Route costing (tarif, toll, BBM)
- Route optimization & planning
- Route history & analytics

### 1.2 Pentingnya Route Management
1. **Akurasi Cost Calculation** - basis untuk hitung uang jalan & tarif
2. **Planning Efficiency** - route yang terdata bisa auto-assign
3. **Driver Familiarity** - track driver mana yang familiar dengan route tertentu
4. **Performance Monitoring** - compare estimated vs actual time/cost
5. **Safety & Compliance** - route restrictions, avoid areas

### 1.3 Jenis Route
1. **Shuttle Route** - Point to point (A → B → A)
   - Origin dan destination tetap
   - Bisa round-trip atau one-way
   
2. **Multi-Point Route (FMCG)** - Sequential delivery (A → B → C → D → A)
   - Origin tetap, multiple destinations
   - Sequence bisa dioptimasi
   
3. **Ad-hoc Route** - Custom/temporary route
   - Tidak tersimpan sebagai master
   - Untuk kebutuhan khusus

---

## 2. DATABASE STRUCTURE

### 2.1 Master Route
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
    distance_km DECIMAL(10,2) NOT NULL, -- jarak total
    estimated_time_hours DECIMAL(5,2), -- waktu tempuh normal
    estimated_time_min DECIMAL(5,2), -- waktu tempuh tercepat
    estimated_time_max DECIMAL(5,2), -- waktu tempuh terlamban (macet)
    
    -- Road Characteristics
    road_type VARCHAR(50), -- TOLL, HIGHWAY, CITY, MIXED
    road_condition VARCHAR(20), -- EXCELLENT, GOOD, FAIR, POOR
    traffic_level VARCHAR(20), -- LOW, MEDIUM, HIGH, VERY_HIGH
    
    -- Route Restrictions
    max_weight_ton DECIMAL(10,2), -- batas berat kendaraan
    max_height_meter DECIMAL(5,2), -- batas tinggi kendaraan
    max_width_meter DECIMAL(5,2), -- batas lebar kendaraan
    restricted_times VARCHAR(200), -- jam-jam yang tidak boleh lewat (JSON)
    restricted_days VARCHAR(100), -- hari yang tidak boleh lewat (JSON)
    
    -- Cost Components
    base_toll_fee DECIMAL(15,2), -- tol dasar
    estimated_fuel_cost DECIMAL(15,2), -- estimasi BBM
    other_cost DECIMAL(15,2), -- parkir, retribusi, dll
    
    -- Alternative Routes
    has_alternative BIT DEFAULT 0,
    alternative_route_id VARCHAR(50), -- link ke route alternatif
    
    -- Metadata
    is_active BIT DEFAULT 1,
    is_favorite BIT DEFAULT 0, -- route yang sering dipakai
    usage_count INT DEFAULT 0, -- berapa kali route ini dipakai
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

### 2.2 Route Waypoints (untuk Multi-Point)
```sql
CREATE TABLE ms_route_waypoint (
    waypoint_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    sequence_no INT NOT NULL, -- urutan stop
    
    location_id VARCHAR(50),
    location_name VARCHAR(100) NOT NULL,
    location_address TEXT,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    
    -- Estimated Time from Previous Point
    distance_from_prev_km DECIMAL(10,2),
    time_from_prev_hours DECIMAL(5,2),
    
    -- Stop Duration
    estimated_stop_duration_minutes INT, -- lama bongkar/muat
    
    -- Waypoint Type
    waypoint_type VARCHAR(20), -- PICKUP, DELIVERY, CHECKPOINT, REST_AREA
    
    -- Restrictions at This Point
    time_window_start TIME, -- boleh sampai mulai jam berapa
    time_window_end TIME, -- boleh sampai sampai jam berapa
    
    notes TEXT,
    is_mandatory BIT DEFAULT 1, -- harus lewat atau bisa skip?
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (location_id) REFERENCES ms_location(location_id),
    
    UNIQUE(route_id, sequence_no),
    INDEX idx_route_sequence (route_id, sequence_no)
)
```

### 2.3 Route Segments (Detail per Segment Jalan)
```sql
CREATE TABLE ms_route_segment (
    segment_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    segment_no INT NOT NULL,
    
    from_point VARCHAR(100), -- nama titik awal segment
    to_point VARCHAR(100), -- nama titik akhir segment
    
    distance_km DECIMAL(10,2),
    estimated_time_minutes INT,
    
    segment_type VARCHAR(20), -- TOLL, HIGHWAY, CITY_ROAD, RURAL
    toll_fee DECIMAL(15,2), -- tol untuk segment ini
    
    -- Polyline for Map Display
    polyline TEXT, -- encoded polyline (Google format)
    
    notes TEXT,
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    INDEX idx_route_segment (route_id, segment_no)
)
```

### 2.4 Route History (Tracking Actual Performance)
```sql
CREATE TABLE ms_route_history (
    history_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    dispatch_id VARCHAR(50),
    
    driver_id VARCHAR(50),
    vehicle_id VARCHAR(50),
    
    -- Actual Performance
    actual_distance_km DECIMAL(10,2),
    actual_time_hours DECIMAL(5,2),
    actual_fuel_liter DECIMAL(10,2),
    actual_fuel_cost DECIMAL(15,2),
    actual_toll_fee DECIMAL(15,2),
    
    -- Comparison
    distance_variance DECIMAL(10,2), -- selisih vs estimated
    time_variance DECIMAL(5,2), -- selisih vs estimated
    cost_variance DECIMAL(15,2), -- selisih vs estimated
    
    -- Conditions
    weather VARCHAR(20), -- CLEAR, RAIN, FOG
    traffic_condition VARCHAR(20), -- SMOOTH, NORMAL, JAM, HEAVY_JAM
    
    -- Rating & Feedback
    driver_rating INT, -- 1-5, bagaimana driver rate route ini
    driver_notes TEXT,
    
    trip_date DATE,
    departure_time DATETIME,
    arrival_time DATETIME,
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id),
    
    INDEX idx_route_date (route_id, trip_date)
)
```

### 2.5 Route Driver Familiarity (Driver yang Familiar dengan Route)
```sql
CREATE TABLE ms_route_driver_familiarity (
    familiarity_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    driver_id VARCHAR(50) NOT NULL,
    
    -- Statistics
    total_trips INT DEFAULT 0,
    successful_trips INT DEFAULT 0,
    failed_trips INT DEFAULT 0,
    
    -- Performance
    avg_time_hours DECIMAL(5,2),
    best_time_hours DECIMAL(5,2),
    worst_time_hours DECIMAL(5,2),
    
    avg_rating DECIMAL(3,2), -- rata-rata rating driver untuk route ini
    
    -- Familiarity Level
    familiarity_level VARCHAR(20), -- EXPERT, EXPERIENCED, MODERATE, BEGINNER, NEW
    
    last_trip_date DATE,
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    
    UNIQUE(route_id, driver_id),
    INDEX idx_route_familiarity (route_id, familiarity_level)
)
```

### 2.6 Route Tariff (Harga per Route per Service Type)
```sql
CREATE TABLE ms_route_tariff (
    tariff_id VARCHAR(50) PRIMARY KEY,
    route_id VARCHAR(50) NOT NULL,
    
    -- Service Type
    service_type VARCHAR(20), -- SHUTTLE, MULTIPOINT, EXPRESS, REGULAR
    vehicle_type VARCHAR(20), -- TRUCK, TRONTON, FUSO, PICKUP, etc
    
    -- Pricing
    base_rate DECIMAL(15,2), -- tarif dasar
    rate_per_km DECIMAL(10,2), -- tarif per km tambahan
    rate_per_ton DECIMAL(10,2), -- tarif per ton muatan
    
    -- Driver Compensation
    uang_jalan DECIMAL(15,2), -- uang jalan untuk route ini
    uang_jasa_min DECIMAL(15,2), -- uang jasa minimal
    uang_jasa_max DECIMAL(15,2), -- uang jasa maksimal
    
    -- Additional Charges
    toll_reimbursement DECIMAL(15,2), -- ganti tol
    fuel_reimbursement DECIMAL(15,2), -- ganti BBM
    other_reimbursement DECIMAL(15,2), -- ganti parkir, dll
    
    -- Validity
    effective_date DATE,
    expired_date DATE,
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    INDEX idx_route_tariff_active (route_id, is_active, effective_date)
)
```

---

## 3. ROUTE FEATURES & FUNCTIONALITY

### 3.1 Route CRUD Operations

#### A. Create Route
**Manual Entry:**
```
Input Fields:
- Route Code (auto-generate atau manual)
- Route Name (descriptive)
- Route Type (Shuttle/Multi-Point/Ad-hoc)
- Origin (select from Location master atau custom)
- Destination (select from Location master atau custom)
- Distance (manual input atau calculate dari Maps API)
- Estimated Time (manual atau auto calculate)
- Road Type, Road Condition, Traffic Level
- Restrictions (weight, height, time, day)
- Cost Components (toll, fuel, other)
- Notes
```

**Auto-Create dari Maps API:**
```
1. User pilih Origin & Destination dari map
2. System call Google Maps Directions API
3. Get:
   - Distance (km)
   - Duration (hours)
   - Polyline (for map display)
   - Route steps (segments)
4. Auto populate form
5. User review & adjust
6. Save route
```

#### B. Update Route
- Edit semua field (kecuali route_id)
- Update tariff
- Update restrictions
- Add waypoints (untuk multi-point)
- Mark as inactive (soft delete)

#### C. Read/View Route
**List View:**
- Table dengan columns: Code, Name, Origin, Destination, Distance, Est. Time, Status
- Filter: Route Type, Active/Inactive, Origin, Destination
- Search: by code, name, origin, destination
- Sort: by code, distance, usage_count, last_used

**Detail View:**
- Map visualization (origin, destination, route path)
- Route info (distance, time, cost)
- Waypoints (jika multi-point)
- Restrictions
- Tariff history
- Usage statistics
- Driver familiarity list
- Historical performance chart

#### D. Delete Route
- Soft delete (set is_active = 0)
- Cannot delete if ada dispatch aktif yang pakai route ini
- Cascade update: inactivate related tariffs

---

### 3.2 Route Optimization

#### A. Best Route Suggestion
**Scenario:** User input origin & destination baru
```
1. Check apakah route sudah ada di master
   - Jika ada, suggest route existing
   
2. Jika belum ada, system suggest:
   - Call Maps API untuk 3 alternative routes:
     * Fastest route (minimize time)
     * Shortest route (minimize distance)
     * Avoid toll route
   
3. Display comparison:
   | Route | Distance | Time | Toll Fee | Est. Cost |
   |-------|----------|------|----------|-----------|
   | Fast  | 120 km   | 2h   | Rp 50k   | Rp 500k   |
   | Short | 110 km   | 2.5h | Rp 40k   | Rp 480k   |
   | No Toll| 130 km  | 3h   | Rp 0     | Rp 450k   |
   
4. User pilih & save as new route
```

#### B. Multi-Point Route Optimization
**Scenario:** FMCG dengan 10 delivery points
```
Problem: Optimize sequence untuk minimize total distance/time

Algorithm Options:
1. Nearest Neighbor (simple, fast)
   - Start from origin
   - Next = closest unvisited point
   - Repeat until all visited
   
2. Genetic Algorithm (better result, slower)
   - Generate random sequences
   - Evaluate fitness (total distance)
   - Breed & mutate
   - Select best sequence
   
3. Google Maps Optimization API
   - Send all waypoints
   - Get optimized order
   
Output:
- Optimized sequence
- Total distance saved vs original
- Total time saved
- Map visualization
```

#### C. Driver-Route Matching
**Scenario:** Auto-assign driver untuk route tertentu
```
Criteria:
1. Familiarity level (EXPERT > EXPERIENCED > MODERATE)
2. Success rate (successful_trips / total_trips)
3. Average performance (avg_time vs estimated_time)
4. Availability (tidak ada dispatch bentrok)
5. Last trip on this route (rotate untuk fairness)

Scoring Formula:
score = (familiarity_weight * familiarity_score) +
        (success_weight * success_rate) +
        (performance_weight * performance_score) +
        (availability_weight * availability_score) +
        (rotation_weight * days_since_last_trip)

Output: Top 5 recommended drivers untuk route ini
```

---

### 3.3 Route Analytics & Reporting

#### A. Route Performance Dashboard
```
Metrics per Route:
- Total usage (berapa kali dipakai)
- Average actual time vs estimated time
- Average actual cost vs estimated cost
- Success rate (on-time delivery)
- Driver satisfaction rating
- Most used by which drivers
- Peak usage hours/days

Visualization:
- Line chart: Actual time trend over months
- Bar chart: Top 10 most used routes
- Heatmap: Route usage by day of week & time window
- Map: Popular routes overlay
```

#### B. Route Comparison Report
```
Compare 2+ routes:
| Metric            | Route A   | Route B   | Route C   |
|-------------------|-----------|-----------|-----------|
| Distance          | 120 km    | 115 km    | 130 km    |
| Est. Time         | 2h        | 2.5h      | 2.2h      |
| Avg Actual Time   | 2.1h      | 2.8h      | 2.3h      |
| Variance          | +5%       | +12%      | +4.5%     |
| Total Trips       | 150       | 80        | 120       |
| Success Rate      | 95%       | 88%       | 92%       |
| Avg Cost          | Rp 500k   | Rp 480k   | Rp 510k   |
| Recommendation    | ⭐⭐⭐⭐⭐   | ⭐⭐⭐      | ⭐⭐⭐⭐    |
```

#### C. Route Profitability Analysis
```
Revenue vs Cost per Route:
- Total revenue (dari tariff)
- Total cost (uang jalan + uang jasa + toll + fuel)
- Gross profit
- Profit margin
- ROI per trip

Trend Analysis:
- Profit trend over time
- Seasonal patterns
- Cost increase factors
```

---

### 3.4 Route Planning Tools

#### A. Route Planner (Weekly Planning Integration)
```
Scenario: Planning 100 trips untuk minggu depan

Steps:
1. System group trips by route
   - Jakarta-Bandung: 30 trips
   - Jakarta-Surabaya: 20 trips
   - Surabaya-Malang: 25 trips
   - etc.

2. For each route group:
   - Calculate truck & driver needed
   - Check availability
   - Suggest optimal schedule (window assignment)
   
3. Display planning matrix:
   | Route         | Mon | Tue | Wed | Thu | Fri | Sat | Sun | Total |
   |---------------|-----|-----|-----|-----|-----|-----|-----|-------|
   | JKT-BDG       | 5   | 4   | 6   | 5   | 6   | 3   | 1   | 30    |
   | JKT-SBY       | 3   | 3   | 2   | 4   | 3   | 3   | 2   | 20    |
   
4. Auto-assign:
   - Driver with highest familiarity
   - Truck available & suitable
   - Distribute evenly (avoid overload 1 driver)
```

#### B. Route Grouping (untuk Cost Efficiency)
```
Scenario: Ada 5 trips di area yang sama

System suggest grouping:
- Trip 1: Jakarta → Bekasi (10km)
- Trip 2: Jakarta → Depok (15km)
- Trip 3: Jakarta → Tangerang (20km)
- Trip 4: Jakarta → Bogor (40km)
- Trip 5: Jakarta → Cibubur (25km)

Option A: 5 trips terpisah
- Total distance: 110 km
- Total cost: Rp 2,500k
- 5 trucks needed

Option B: 2 grouped trips
- Group 1: Jakarta → Bekasi → Cibubur → Depok → Jakarta (70km)
- Group 2: Jakarta → Tangerang → Bogor → Jakarta (100km)
- Total distance: 170 km (increase 55%)
- Total cost: Rp 1,800k (save 28%)
- 2 trucks needed (save 3 trucks)

User decide: efficiency vs speed
```

---

### 3.5 Route Import/Export

#### A. Import Route dari Excel
```
Template Excel:
| Route Code | Route Name    | Origin   | Destination | Distance | Time | Toll | Notes |
|------------|---------------|----------|-------------|----------|------|------|-------|
| JKT-BDG-01 | Jakarta-Bandung| Jakarta  | Bandung     | 150      | 3    | 65k  | Via tol|
| JKT-SBY-01 | Jakarta-Surabaya| Jakarta | Surabaya    | 750      | 12   | 250k | -     |

Validation:
- Route code unique
- Origin & destination exist in location master (atau create on-the-fly)
- Distance & time numeric
- Toll fee numeric

Process:
1. Upload Excel
2. Preview & validate
3. Mark errors (highlight row)
4. User fix atau skip
5. Confirm import
6. Show summary (X success, Y failed)
```

#### B. Export Route to Excel/PDF
```
Export options:
- All routes atau filtered
- Include tariff atau tidak
- Include statistics atau tidak
- Include map image atau tidak

Format:
- Excel: untuk edit & re-import
- PDF: untuk dokumentasi/sharing
- CSV: untuk integration
```

---

### 3.6 Route Restrictions & Compliance

#### A. Time Restrictions
```
Example:
Route: Jakarta - Bekasi
Restricted Times: 07:00-09:00, 17:00-19:00 (rush hour)

System behavior:
1. Saat planning, warning jika dispatch di jam terlarang
2. Auto suggest alternative time window
3. Allow override dengan approval manager
4. Log violation untuk audit
```

#### B. Weight/Dimension Restrictions
```
Example:
Route: Via Jembatan Semanggi
Max Weight: 10 ton
Max Height: 3.5 meter

System behavior:
1. Check truck spec vs route restriction
2. Block assignment jika exceed limit
3. Suggest alternative route (tanpa batasan)
4. Show map overlay (restricted zones)
```

#### C. Day Restrictions
```
Example:
Route: Tol Dalam Kota Jakarta
Restricted Days: Weekends (for trucks)

System behavior:
1. Check dispatch date vs restriction
2. Warning/block if violated
3. Suggest reschedule
```

---

## 4. UI/UX DESIGN

### 4.1 Route List Page
```
┌─────────────────────────────────────────────────────────────────┐
│ Master Route                                        [+ Add Route]│
├─────────────────────────────────────────────────────────────────┤
│ Filter: [Route Type ▼] [Origin ▼] [Destination ▼] [Status ▼] [Search...]
├─────────────────────────────────────────────────────────────────┤
│ Code       │ Route Name        │ Origin   │ Dest     │ Dist  │ Action │
├────────────┼──────────────────┼──────────┼─────────┼───────┼────────┤
│ JKT-BDG-01 │ Jakarta-Bandung   │ Jakarta  │ Bandung  │ 150km │ [View] │
│            │ (Via Tol)         │          │          │       │ [Edit] │
│            │ ⭐⭐⭐⭐⭐ (150 trips)│          │          │       │ [Del]  │
├────────────┼──────────────────┼──────────┼─────────┼───────┼────────┤
│ JKT-SBY-01 │ Jakarta-Surabaya  │ Jakarta  │ Surabaya │ 750km │ [View] │
│            │ (Pantura)         │          │          │       │ [Edit] │
│            │ ⭐⭐⭐⭐ (80 trips)  │          │          │       │ [Del]  │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Route Detail Page
```
┌─────────────────────────────────────────────────────────────────┐
│ Route: JKT-BDG-01 - Jakarta Bandung                     [Edit]  │
├─────────────────────────────────────────────────────────────────┤
│ ┌─────────────────────┐  ┌────────────────────────────────────┐ │
│ │     MAP VIEW        │  │ Route Information                  │ │
│ │                     │  │ Code: JKT-BDG-01                   │ │
│ │  [Origin Pin]       │  │ Type: Shuttle                      │ │
│ │      |              │  │ Distance: 150 km                   │ │
│ │      |              │  │ Est. Time: 3 hours                 │ │
│ │      v              │  │ Road Type: Toll Highway            │ │
│ │  [Dest Pin]         │  │ Traffic: Medium                    │ │
│ │                     │  │ Toll Fee: Rp 65,000                │ │
│ └─────────────────────┘  │ Status: Active ✅                   │ │
│                          └────────────────────────────────────┘ │
├─────────────────────────────────────────────────────────────────┤
│ [Tab: Info] [Tab: Tariff] [Tab: Drivers] [Tab: History] [Tab: Stats] │
├─────────────────────────────────────────────────────────────────┤
│ Restrictions:                                                    │
│ ⚠ Max Weight: 15 ton                                            │
│ ⚠ Restricted Time: 07:00-09:00 (rush hour)                      │
├─────────────────────────────────────────────────────────────────┤
│ Tariff (Active):                                                 │
│ Base Rate: Rp 500,000                                           │
│ Uang Jalan: Rp 200,000                                          │
│ Uang Jasa: Rp 150,000 - Rp 200,000                              │
├─────────────────────────────────────────────────────────────────┤
│ Familiar Drivers (Top 5):                                        │
│ 1. Driver A - EXPERT (50 trips, 96% success) ⭐⭐⭐⭐⭐             │
│ 2. Driver B - EXPERIENCED (30 trips, 93% success) ⭐⭐⭐⭐          │
│ 3. Driver C - MODERATE (15 trips, 90% success) ⭐⭐⭐             │
└─────────────────────────────────────────────────────────────────┘
```

### 4.3 Route Create/Edit Form
```
┌─────────────────────────────────────────────────────────────────┐
│ Add New Route                                                    │
├─────────────────────────────────────────────────────────────────┤
│ Route Code: [Auto-generate ✓] [__________] or manual            │
│ Route Name: [_____________________________________]              │
│ Route Type: [⦿ Shuttle  ○ Multi-Point  ○ Ad-hoc]               │
├─────────────────────────────────────────────────────────────────┤
│ Origin:                                                          │
│   [Select from Location ▼] or [📍 Pick from Map]                │
│   Location: Jakarta                                              │
│   Address: Jl. Sudirman No. 1                                   │
│   Lat/Lng: -6.2088, 106.8456                                    │
├─────────────────────────────────────────────────────────────────┤
│ Destination:                                                     │
│   [Select from Location ▼] or [📍 Pick from Map]                │
│   Location: Bandung                                              │
│   Address: Jl. Asia Afrika No. 100                              │
│   Lat/Lng: -6.9175, 107.6191                                    │
├─────────────────────────────────────────────────────────────────┤
│ Route Characteristics:                                           │
│   Distance (km): [150.5] [🔄 Calculate from Maps]               │
│   Est. Time (hours): [3.0]                                      │
│   Road Type: [Toll Highway ▼]                                   │
│   Road Condition: [Excellent ▼]                                  │
│   Traffic Level: [Medium ▼]                                      │
├─────────────────────────────────────────────────────────────────┤
│ Cost Components:                                                 │
│   Toll Fee: Rp [65,000]                                         │
│   Est. Fuel Cost: Rp [300,000]                                  │
│   Other Cost: Rp [20,000]                                       │
├─────────────────────────────────────────────────────────────────┤
│ Restrictions (Optional):                                         │
│   Max Weight (ton): [15]                                        │
│   Max Height (m): [4.0]                                         │
│   Max Width (m): [2.5]                                          │
│   Restricted Times: [+ Add Time Range]                          │
│   Restricted Days: [☐ Mon ☐ Tue ☐ Wed ☐ Thu ☐ Fri ☑ Sat ☑ Sun]│
├─────────────────────────────────────────────────────────────────┤
│ Notes: [_______________________________________________]         │
├─────────────────────────────────────────────────────────────────┤
│                        [Cancel]  [Save Route]                    │
└─────────────────────────────────────────────────────────────────┘
```

---

## 5. INTEGRATION POINTS

### 5.1 Maps API Integration
```php
// Google Maps Directions API
function getRouteFromMaps($origin, $destination) {
    $apiKey = env('GOOGLE_MAPS_API_KEY');
    $url = "https://maps.googleapis.com/maps/api/directions/json?".
           "origin=" . urlencode($origin) .
           "&destination=" . urlencode($destination) .
           "&alternatives=true" .
           "&key=" . $apiKey;
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data['status'] == 'OK') {
        $route = $data['routes'][0];
        $leg = $route['legs'][0];
        
        return [
            'distance_km' => $leg['distance']['value'] / 1000,
            'duration_hours' => $leg['duration']['value'] / 3600,
            'polyline' => $route['overview_polyline']['points'],
            'start_address' => $leg['start_address'],
            'end_address' => $leg['end_address'],
            'steps' => $leg['steps']
        ];
    }
    
    return null;
}
```

### 5.2 Planning Module Integration
```php
// Auto-assign route ke planning trip
function assignRouteToPlanning($planningId) {
    $planning = MsPlanningWeekly::find($planningId);
    
    // Find matching route
    $route = MsRoute::where('origin_name', $planning->origin)
                    ->where('destination_name', $planning->destination)
                    ->where('is_active', 1)
                    ->first();
    
    if (!$route) {
        // Route not found, suggest create new or select alternative
        $alternatives = findAlternativeRoutes($planning->origin, $planning->destination);
        return ['route' => null, 'alternatives' => $alternatives];
    }
    
    // Update planning dengan route_id
    $planning->route_id = $route->route_id;
    $planning->save();
    
    // Update route usage stats
    $route->increment('usage_count');
    $route->last_used = now();
    $route->save();
    
    return ['route' => $route];
}
```

### 5.3 Cost Calculation Integration
```php
// Calculate cost based on route
function calculateRouteCost($routeId, $weight = 0, $serviceType = 'SHUTTLE') {
    $route = MsRoute::find($routeId);
    $tariff = MsRouteTariff::where('route_id', $routeId)
                           ->where('service_type', $serviceType)
                           ->where('is_active', 1)
                           ->where('effective_date', '<=', now())
                           ->where(function($q) {
                               $q->whereNull('expired_date')
                                 ->orWhere('expired_date', '>=', now());
                           })
                           ->first();
    
    if (!$tariff) {
        // Use default calculation
        $baseCost = $route->distance_km * 3000; // Rp 3k per km default
        $tollCost = $route->base_toll_fee;
        $fuelCost = $route->estimated_fuel_cost;
        
        return [
            'base_cost' => $baseCost,
            'toll_cost' => $tollCost,
            'fuel_cost' => $fuelCost,
            'total_cost' => $baseCost + $tollCost + $fuelCost,
            'uang_jalan' => $baseCost * 0.4, // 40% untuk uang jalan
            'uang_jasa' => $baseCost * 0.3,  // 30% untuk uang jasa
        ];
    }
    
    // Calculate using tariff
    $baseCost = $tariff->base_rate;
    $kmCost = $route->distance_km * $tariff->rate_per_km;
    $weightCost = $weight * $tariff->rate_per_ton;
    $totalCost = $baseCost + $kmCost + $weightCost;
    
    return [
        'base_cost' => $baseCost,
        'km_cost' => $kmCost,
        'weight_cost' => $weightCost,
        'total_cost' => $totalCost,
        'uang_jalan' => $tariff->uang_jalan,
        'uang_jasa' => $tariff->uang_jasa_min, // or calculate based on performance
        'toll' => $tariff->toll_reimbursement,
        'fuel' => $tariff->fuel_reimbursement,
    ];
}
```

---

## 6. BUSINESS RULES

### 6.1 Route Validation Rules
1. **Route Code harus unique** - tidak boleh duplicate
2. **Origin dan Destination tidak boleh sama** - kecuali round-trip
3. **Distance harus > 0** - tidak boleh 0 atau negatif
4. **Estimated time harus realistis** - min time < normal time < max time
5. **Tariff effective date tidak boleh overlap** - untuk route & service type yang sama
6. **Restrictions harus konsisten** - time window start < end
7. **Cannot delete route yang masih dipakai** - jika ada dispatch aktif

### 6.2 Route Assignment Rules (Auto Scheduling)
1. **Driver priority:**
   - Familiarity level: EXPERT > EXPERIENCED > MODERATE
   - Success rate > 90% preferred
   - Last trip on this route > 7 days ago (untuk fairness)
   
2. **Truck priority:**
   - Capacity match dengan muatan
   - Tidak exceed route restrictions (weight, height, width)
   - Fuel efficient untuk route jarak jauh
   
3. **Time window compatibility:**
   - Route restricted time tidak bentrok dengan dispatch time
   - Buffer time untuk driver (min 2 jam antar trip)

### 6.3 Route Update Rules
1. **Jika route sudah dipakai, perubahan hanya affect future dispatch**
2. **Historical dispatch tetap pakai data route saat itu** (snapshot)
3. **Perubahan signifikan (>20% distance/time) require approval**
4. **Tariff changes require approval manager**

---

## 7. PERFORMANCE OPTIMIZATION

### 7.1 Database Indexing
```sql
-- Critical indexes untuk query performance
CREATE INDEX idx_route_origin_dest ON ms_route(origin_name, destination_name, is_active);
CREATE INDEX idx_route_code ON ms_route(route_code);
CREATE INDEX idx_route_usage ON ms_route(usage_count DESC, last_used DESC);
CREATE INDEX idx_route_favorite ON ms_route(is_favorite, is_active);

CREATE INDEX idx_waypoint_route_seq ON ms_route_waypoint(route_id, sequence_no);
CREATE INDEX idx_history_route_date ON ms_route_history(route_id, trip_date DESC);
CREATE INDEX idx_familiarity_route_driver ON ms_route_driver_familiarity(route_id, driver_id, familiarity_level);
CREATE INDEX idx_tariff_route_active ON ms_route_tariff(route_id, is_active, effective_date);
```

### 7.2 Caching Strategy
```php
// Cache route data yang sering diakses
Cache::remember('route:' . $routeId, 3600, function() use ($routeId) {
    return MsRoute::with(['waypoints', 'activeTariff', 'familiarDrivers'])
                  ->find($routeId);
});

// Cache route list untuk planning
Cache::remember('routes:active:shuttle', 1800, function() {
    return MsRoute::where('route_type', 'SHUTTLE')
                  ->where('is_active', 1)
                  ->orderBy('usage_count', 'DESC')
                  ->get();
});

// Invalidate cache on update
function updateRoute($routeId, $data) {
    $route = MsRoute::find($routeId);
    $route->update($data);
    
    Cache::forget('route:' . $routeId);
    Cache::forget('routes:active:shuttle');
    Cache::forget('routes:active:multipoint');
}
```

### 7.3 Query Optimization
```php
// Use eager loading untuk reduce N+1 queries
$routes = MsRoute::with([
    'originLocation',
    'destinationLocation',
    'activeTariff',
    'familiarDrivers' => function($query) {
        $query->where('familiarity_level', 'EXPERT')
              ->limit(5);
    }
])->where('is_active', 1)->get();

// Pagination untuk large dataset
$routes = MsRoute::where('is_active', 1)
                 ->orderBy('last_used', 'DESC')
                 ->paginate(50);
```

---

## 8. TESTING SCENARIOS

### 8.1 Unit Tests
```php
// Test route creation
testCreateRoute()
testRouteCodeUnique()
testOriginDestinationDifferent()
testDistancePositive()
testTimeEstimationValid()

// Test route calculation
testCalculateRouteFromMaps()
testCalculateRouteCost()
testCalculateDriverFamiliarity()

// Test route assignment
testAutoAssignBestDriver()
testCheckRouteRestrictions()
testValidateTimeWindow()
```

### 8.2 Integration Tests
```php
// Test dengan Planning module
testAssignRouteToPlanningTrip()
testUpdatePlanningWhenRouteChanged()

// Test dengan Dispatch module
testCreateDispatchFromRoute()
testUpdateRouteHistoryAfterTrip()

// Test dengan Tariff module
testApplyCorrectTariffToRoute()
testTariffEffectiveDateValidation()
```

### 8.3 Performance Tests
```php
// Load testing
test1000RoutesLoadTime()
testRouteSearchPerformance()
testMapAPIResponseTime()
testCacheEffectiveness()
```

---

## 9. MIGRATION & SEEDING

### 9.1 Sample Seeder
```php
// RouteSeeder.php
class RouteSeeder extends Seeder
{
    public function run()
    {
        // Sample Shuttle Routes
        MsRoute::create([
            'route_id' => Str::uuid(),
            'route_code' => 'JKT-BDG-01',
            'route_name' => 'Jakarta - Bandung (Tol Cipularang)',
            'route_type' => 'SHUTTLE',
            'origin_name' => 'Jakarta',
            'origin_latitude' => -6.2088,
            'origin_longitude' => 106.8456,
            'destination_name' => 'Bandung',
            'destination_latitude' => -6.9175,
            'destination_longitude' => 107.6191,
            'distance_km' => 150.5,
            'estimated_time_hours' => 3.0,
            'road_type' => 'TOLL',
            'road_condition' => 'EXCELLENT',
            'traffic_level' => 'MEDIUM',
            'base_toll_fee' => 65000,
            'estimated_fuel_cost' => 300000,
            'other_cost' => 20000,
            'is_active' => 1,
        ]);
        
        // More sample routes...
    }
}
```

---

## 10. NEXT STEPS

### Immediate Actions:
1. **✅ Review & approve specification ini**
2. **✅ Tentukan Maps API provider** (Google Maps atau HERE atau OpenStreetMap?)
3. **✅ Prepare sample route data** (min 10-20 routes untuk testing)
4. **🚀 Start development:**
   - Create database tables
   - Build Route CRUD
   - Integrate Maps API
   - Build route optimization logic

### Questions to Answer:
1. **Maps API:** Google Maps (berbayar, akurat) atau OpenStreetMap (gratis, less accurate)?
2. **Route optimization priority:** Speed vs Cost vs Distance?
3. **Driver familiarity threshold:** Berapa trips minimum untuk status EXPERT?
4. **Route approval workflow:** Perlu approval manager untuk create/edit route?
5. **Historical data retention:** Keep route history berapa lama?

---

**Status:** ⏳ Awaiting Review & Approval  
**Ready to Implement:** YES - semua spec sudah detail dan actionable

Apakah ada yang perlu ditambahkan atau direvisi untuk Route Management ini?
