# TMS - MASTER DOCUMENTATION INDEX

**Created:** 18 November 2025  
**Purpose:** Comprehensive index of all TMS documentation, tables, and relationships  
**Status:** Complete Reference Guide

---

## 📚 DOCUMENTATION OVERVIEW

### Total Documents: 16
### Total Database Tables: 60+ (Master Data + Transactions)
### Lines of Documentation: 65,000+

---

## 1. CORE PROJECT DOCUMENTATION

### 1.1 TMS_Project_Blueprint.md (2,800+ lines)
**Purpose**: Master project plan, features, modules, and architecture

**Key Sections:**
- ✅ Overview & Features (Planning modes, Dispatcher ops, Driver communication)
- ✅ Module breakdown (Master data, Planning, Execution, Settlement)
- ✅ AQUA CO Planning integration (29 columns, 412 shipments → 145 trips)
- ✅ Weekly planning workflow (Jumat → Senin execution)
- ✅ Rest time monitoring (8 hours minimum)
- ⭐ **NEW**: Dispatch as SPJ (Surat Perintah Jalan)
- ⭐ **NEW**: Tariff management (many-to-many)
- ⭐ **NEW**: Document flow (CO/DN/BTB)
- ⭐ **NEW**: Tanker operations (volume-based pricing)
- ⭐ **NEW**: Accounting integration

**Related Tables:**
- All master data tables
- All transaction tables
- Planning, Dispatch, Execution, Settlement

---

### 1.2 AI Guidelines.md (Reference Guide)
**Purpose**: Guidelines for AI assistant interaction

**Key Topics:**
- Project context
- Documentation standards
- Communication style
- Technical requirements

---

## 2. MASTER DATA DOCUMENTATION

### 2.1 Complete_Master_Data_Structure.md (1,100+ lines)
**Purpose**: Complete master data table definitions

**Tables Documented:**
1. **ms_point** - Locations (warehouse, factory, DC, customer sites)
2. **ms_client** - Customers with contract details
3. **ms_product** - Products/cargo types
4. **ms_route** - Routes between points
5. **ms_route_tariff** - Route-specific pricing (DEPRECATED - see Tariff_Structure_Analysis.md)
6. **ms_driver** - Driver master data
7. **ms_vehicle** - Vehicle/truck fleet
8. **ms_truck_type** - Truck type specifications

⭐ **UPDATED**: Now references new tariff structure (`Tariff_Structure_Analysis.md`)

---

### 2.2 AQUA_Master_Data_Analysis.md (900+ lines)
**Purpose**: Real data extracted from AQUA CO Planning Excel

**Master Data Extracted:**
- **16 Locations**: 5 Plants, 7 DCs, 1 XWH, 3 Distributors
  - Plants: Ciherang (9018), Mekarsari (90AE), Sentul (9076), Citeureup (9013), Caringin (90A0)
  - DCs: Palapa (9025), Kawasan (9021), Bandung (9028), etc.
- **5 Truck Types**: JUGRACK Green, JUGRACK Standard, PET JUGRACK, ODOL-864, PALLET
  - Capacities: 2900, 2610, 1312-2720 cartons
- **11 Routes**: CHR-PAL (45km), MEK-KWS (35km), MEK-BDG (180km long haul), etc.
- **Movement Types**: 92% Factory→Depo, 8% Others
- **Time Windows**: 12 windows (2-hour slots)

**SQL INSERT Statements:**
- ✅ Complete location inserts
- ✅ Complete truck type inserts
- ✅ Complete route inserts with distance

---

## 3. ROUTE & SHUTTLE SPECIFICATIONS

### 3.1 Route_Management_Specification.md
**Purpose**: Route planning and management

**Key Topics:**
- Route definition
- Distance calculation
- Toll estimation
- Time window management

---

### 3.2 Shuttle_Planning_Modes.md
**Purpose**: 3 shuttle planning modes

**Modes:**
1. **Weekly Planning**: Contract clients (AQUA) submit Excel every Friday
2. **Ad-hoc**: Spot clients order via WA/Email/Phone
3. **Hybrid**: Combination of weekly + ad-hoc

---

### 3.3 Shuttle_Route_Specification.md
**Purpose**: Detailed shuttle route specs

---

### 3.4 Route_Table_Structure_Enhanced.md
**Purpose**: Enhanced route table design

---

## 4. TRANSACTION TABLES & DATABASE DESIGN

### 4.1 Database_Transaction_Tables_Plan.md (1,200+ lines) ⭐ UPDATED
**Purpose**: Complete transaction table design

**Transaction Flow (12 stages):**
1. **Planning** (Jumat) → ms_planning_weekly
2. **Assignment** (Sabtu) → ms_planning_assignment
3. **Approval** (Minggu) → ms_planning_approval
4. **Dispatch Order** (Senin) → tr_tms_dispatcher_main ⭐ NEW
5. **Shuttle Docs** → tr_tms_dispatcher_shuttle ⭐ NEW
6. **Execution** → ms_trip_execution
7. **GPS Tracking** → ms_gps_tracking (201K rows/week!)
8. **POD** → ms_pod
9. **Tanker Volume** → tr_tms_tanker_delivery ⭐ NEW
10. **Accounting** → tr_acc_tms_transaksi_sales, tr_acc_tms_transaksi_kasir ⭐ NEW
11. **Settlement** → ms_settlement, ms_settlement_detail
12. **Adjustment** → ms_dispatch_adjustment_log

**Tables Summary: 20 tables**
- 11 Original transaction tables
- 9 NEW tables (dispatcher, accounting, tanker, tariff)

**Volume Estimates:**
- Total: ~152,900 rows/week
- Storage: ~50 MB/week
- High volume: ms_gps_tracking (201K rows/week)

⭐ **UPDATED**: Added references to new dispatcher, accounting, and tariff tables

---

### 4.2 Transaction_Tables_Dispatcher_Accounting.md (1,500+ lines) ⭐ NEW
**Purpose**: Dispatch Order as "Surat Perintah Jalan" (SPJ) with accounting integration

**Key Tables:**
1. **tr_tms_dispatcher_main** (Main SPJ)
   - 100+ fields
   - Dispatch types: SHUTTLE, BACKHAUL, ADHOC, URGENT, MULTI_DROP
   - Contains: Pricing, costs, uang jalan, uang jasa
   - Status: DRAFT → APPROVED → DISPATCHED → COMPLETED → INVOICED → SETTLED
   - Auto-triggers accounting transactions

2. **tr_tms_dispatcher_detail** (Multi-drop breakdown)
   - Detail per drop point
   - POD per location

3. **tr_acc_tms_transaksi_sales** (Customer Invoice - AR)
   - Auto-generated when: Dispatch COMPLETED + POD verified
   - Invoice with PPN 11%
   - Payment terms: NET30, NET45, NET60
   - Payment tracking: UNPAID → PAID

4. **tr_acc_tms_transaksi_kasir** (Driver Payments - AP)
   - **UANG_JALAN** (Cash Advance):
     - Trigger: Dispatch status = APPROVED
     - Components: Fuel, toll, parking, meal
     - Reconciliation required with receipts
   - **UANG_JASA** (Driver Fee):
     - Trigger: Trip status = COMPLETED
     - Calculation: Base + Bonus - Penalty
     - Payment: Cash/Bank Transfer

**Auto-Triggers:**
```
Dispatch APPROVED → Create Uang Jalan (kasir)
Dispatch COMPLETED + POD → Create Invoice (sales) + Uang Jasa (kasir)
```

**Sample Flow:**
```
1. Create Dispatch → DRAFT
2. Approve → AUTO: Uang Jalan Rp 250K
3. Kasir pays → Driver receives advance
4. Trip completed + POD → AUTO: Invoice Rp 1.15M + Uang Jasa Rp 200K
5. Customer pays → PAID
6. Driver receives uang jasa → SETTLED
```

---

### 4.3 Shuttle_Dispatcher_Document_Flow.md (1,400+ lines) ⭐ NEW
**Purpose**: Shuttle-specific document tracking (CO → DN → BTB)

**Table: tr_tms_dispatcher_shuttle** (1-to-1 with dispatch)

**Document Lifecycle:**
1. **CO (Customer Order)** - Jumat
   - CO number from AQUA
   - Planning reference
   - Pre-assigned truck/driver

2. **Serah Terima Dokumen** - Senin Pagi (05:00)
   - Dispatcher → Driver
   - Documents: SPJ, CO, Surat Jalan
   - Driver signature (digital)
   - Checklist: SPJ, CO, vehicle OK, fuel sufficient

3. **DN (Delivery Note)** - Senin (07:30 at Plant)
   - From Danone/AQUA at loading
   - DN number, qty loaded, batch number
   - Flowmeter reading (for tanker)
   - Photo upload by driver

4. **BTB (Berita Terima Barang)** - Senin (09:30 at Customer)
   - From customer at delivery
   - BTB number, qty delivered, condition
   - Receiver signature
   - Photo upload

5. **Return Dokumen** - Senin Malam (18:00)
   - Driver → Dispatcher
   - Must return: DN original, BTB original (WAJIB!)
   - Dispatcher verify & sign

6. **Archive** - Selasa
   - Scan all documents
   - Physical archive (BOX-2025-11-W47-001)
   - Digital archive (/archives/2025/11/week47/)
   - Retention: 2 years

**Key Features:**
- ✅ Complete document tracking
- ✅ Photo verification workflow
- ✅ Qty reconciliation (CO vs DN vs BTB)
- ✅ Variance analysis (±2% tolerance)
- ✅ Document completeness score
- ✅ Performance metrics
- ✅ Audit trail

**10 Main Sections:**
1. CO Information
2. Serah Terima (Departure)
3. DN (Loading)
4. BTB (Unloading)
5. Return Dokumen
6. Administration & Archive
7. Compliance & Audit
8. Performance Metrics
9. Notes & Special Handling
10. Audit & Timestamps

---

## 5. TARIFF & PRICING DOCUMENTATION

### 5.1 Tariff_Structure_Analysis.md (800+ lines) ⭐ NEW
**Purpose**: Many-to-many tariff structure with dynamic pricing

**Key Concept:**
```
Tariff = Client × Route × Truck Type × Time Window × Volume Tier
```

**Pricing Models:**
1. **TRIP-based**: Rp 800K per trip (shuttle JUGRACK/PALLET)
2. **VOLUME-based**: Rp 50 per liter (tanker water) ⭐
3. **DISTANCE-based**: Rp 12K per km (ad-hoc)
4. **WEIGHT-based**: Rp 150 per kg (general cargo)
5. **TIME-based**: Rp 100K per hour (rental)

**Tables:**
1. **ms_tariff_contract**
   - Main contract per client
   - Contract type: WEEKLY_PLANNING, ADHOC, HYBRID
   - Volume commitment (min/max trips)
   - Volume discount tiers (>50 = 5%, >100 = 10%, >200 = 15%)
   - Payment terms (NET30, NET45, NET60)
   - Backhaul settings
   - Surcharges (night shift +30%, weekend +25%)

2. **ms_tariff_rate** (Bridge table: Contract × Route × Truck)
   - Pricing model (TRIP, VOLUME, DISTANCE, WEIGHT, TIME)
   - Base rates per model
   - Time window surcharges (12 windows)
   - Minimum/maximum charges
   - Toll & parking inclusion
   - Fuel surcharge formula
   - **Volume-specific fields** ⭐:
     - rate_per_liter
     - minimum_volume_liters
     - maximum_volume_liters
     - volume_measurement_method (FLOWMETER)
     - charge_based_on (LOADED, DELIVERED, AVERAGE)

3. **ms_tariff_special_condition**
   - Seasonal pricing (peak season +20%)
   - Promotional discounts
   - Weather-based surcharges
   - Holiday pricing

**Example: AQUA Contract**
```sql
-- Shuttle Rate
Contract: TC-AQUA-2025-001 (Weekly Planning)
Route: Ciherang → Palapa (45km)
Truck: JUGRACK Green
Rate: Rp 800K per trip
Window 2 surcharge: +30%
Volume discount (100 trips): -10%
Final: Rp 1,040,000

-- Tanker Rate ⭐
Contract: TC-AQUA-TANKER-2025-001 (Ad-hoc)
Route: Sentul → Customer
Truck: Tanker 10KL
Rate: Rp 50 per liter
Delivered: 9,850 L
Final: Rp 492,500
```

**Pricing Functions:**
- `fn_CalculateTripTariff()` - For trip-based
- `fn_CalculateTankerTariff()` - For volume-based ⭐

**Advantages:**
- ✅ Flexible pricing (beda customer beda harga)
- ✅ Dynamic adjustments (time, volume, season)
- ✅ Complete audit trail
- ✅ Easy contract management
- ✅ Support multiple pricing models

---

### 5.2 Tanker_Truck_Volume_Measurement.md (1,000+ lines) ⭐ NEW
**Purpose**: Tanker operations with volume-based pricing

**Tanker Truck Types:**
1. **Water Tanker 10KL**: Rp 50/liter, 5-10KL capacity
2. **Water Tanker 15KL**: Rp 48/liter, 8-15KL capacity
3. **Chemical Tanker 12KL**: Industrial/hazmat

**Volume Measurement Process:**
```
Loading (Plant Sentul):
  Flowmeter start: 12,345.50 L
  Flowmeter end:   22,345.50 L
  Loaded:          10,000 L ✅

Transit:
  Seal tank with numbered seal
  GPS tracking
  Expected spillage: 50-150L (1-1.5%)

Unloading (Customer):
  Flowmeter start: 54,321.00 L
  Flowmeter end:   64,171.00 L
  Delivered:       9,850 L ⭐
  
Variance:
  Lost: 150 L (1.5%) ✅ Within tolerance

Charge:
  9,850 L × Rp 50/L = Rp 492,500 ⭐
```

**Variance Tolerance:**
- 0-1%: ✅ Excellent
- 1-2%: ✅ Acceptable (normal spillage)
- 2-3%: ⚠️ Warning (investigate)
- 3-5%: ❌ High variance (leak possible)
- >5%: 🚨 Critical (stop operation)

**Tables:**
1. **tr_tms_tanker_delivery**
   - Loading measurement (flowmeter readings, temperature)
   - Unloading measurement
   - Variance analysis
   - Chargeable volume calculation
   - Photo upload (flowmeter display)

2. **ms_flowmeter_calibration**
   - Calibration certificates
   - Annual calibration required
   - Accuracy tests (±0.5%)
   - Correction factors

**Key Features:**
- ✅ Flowmeter measurement (loading & unloading)
- ✅ Photo verification (both flowmeters)
- ✅ Temperature correction formula
- ✅ Variance tolerance checking
- ✅ Charge based on delivered volume (not loaded)
- ✅ Seal integrity verification
- ✅ Cross-check with customer flowmeter

---

## 6. DISPATCHER & OPERATIONAL MODULES

### 6.1 Dispatcher_Adjustment_Module.md
**Purpose**: Real-time dispatch adjustments

**Adjustment Types:**
- Driver change (sick, unavailable)
- Vehicle change (breakdown, maintenance)
- Route change (traffic, road closure)
- Cancellation
- Split order

---

### 6.2 Driver_Order_Assignment_System.md
**Purpose**: Driver assignment and notification

---

## 7. COST & SETTLEMENT

### 7.1 Cost_Structure_Settlement_System.md
**Purpose**: Cost components and settlement calculation

**Cost Components:**
1. Uang Jasa Driver
2. Uang Jalan
3. E-toll
4. Uang Kenek
5. BBM
6. Other

---

## 8. COMPLETE TABLE INDEX

### 8.1 Master Data Tables (26 tables)

**Client & Location:**
1. ms_client
2. ms_location (or ms_point)
3. ms_route
4. ms_time_window

**Fleet:**
5. ms_driver
6. ms_vehicle
7. ms_truck_type

**Tariff & Pricing** ⭐ NEW:
8. ms_tariff_contract
9. ms_tariff_rate
10. ms_tariff_special_condition

**Tanker Specific** ⭐ NEW:
11. ms_flowmeter_calibration

**Product & Cargo:**
12. ms_product
13. ms_cargo_type

**Policy:**
14. ms_driver_rest_policy
15. ms_cost_component

**Finance:**
16. ms_payment_terms
17. ms_bank_account
18. ms_etoll_card

**System:**
19. ms_user
20. ms_role
21. ms_permission
22. ms_setting
23. ms_holiday
24. ms_notification_template
25. ms_sms_template
26. ms_audit_log

---

### 8.2 Transaction Tables (20 tables)

**Planning (3):**
1. ms_planning_weekly
2. ms_planning_assignment
3. ms_planning_approval

**Dispatcher (4)** ⭐ REVISED:
4. tr_tms_dispatcher_main (Main SPJ)
5. tr_tms_dispatcher_detail (Multi-drop)
6. tr_tms_dispatcher_shuttle (Document flow) ⭐ NEW
7. tr_tms_tanker_delivery (Volume measurement) ⭐ NEW

**Execution (3):**
8. ms_trip_execution
9. ms_gps_tracking (HIGH VOLUME: 201K rows/week)
10. ms_pod

**Accounting Integration (2)** ⭐ NEW:
11. tr_acc_tms_transaksi_sales (Customer Invoice - AR)
12. tr_acc_tms_transaksi_kasir (Uang Jalan + Uang Jasa - AP)

**Settlement (3):**
13. ms_settlement
14. ms_settlement_detail
15. ms_driver_payment

**Audit (1):**
16. ms_dispatch_adjustment_log

**E-toll & Fuel (4):**
17. tr_etoll_topup
18. tr_etoll_transaction
19. tr_fuel_card_transaction
20. tr_fuel_consumption

---

## 9. DATA VOLUME & STORAGE ESTIMATES

### 9.1 Weekly Volume
```
Total Rows/Week: ~152,900
- Planning: 150
- Dispatch: 150
- Shuttle docs: 150
- Execution: 150
- GPS tracking: 201,600 ⚠️ (HIGH VOLUME)
- POD: 150
- Tanker: ~20
- Accounting: ~300 (150 invoices + 300 kasir transactions)
- Settlement: ~150
- Adjustment: ~50
```

### 9.2 Storage Growth
```
~50 MB/week
~200 MB/month
~2.4 GB/year
```

### 9.3 High Volume Table
```
ms_gps_tracking:
- 120 rows/hour/truck
- 20 trucks
- 12 hours/day
- 7 days/week
= 201,600 rows/week
= ~10 MB/week

Optimization:
- Partition by month
- Archive after 1 year
- Hot: 7 days
- Warm: 30 days
- Cold: 1 year
- Purge: > 1 year
```

---

## 10. MIGRATION & DEPLOYMENT PLAN

### 10.1 Phase 1: Master Data - Tariff (Week 1) ⭐ NEW
```bash
php artisan migrate --path=.../ms_tariff_contract_table.php
php artisan migrate --path=.../ms_tariff_rate_table.php
php artisan migrate --path=.../ms_tariff_special_condition_table.php
php artisan migrate --path=.../ms_flowmeter_calibration_table.php
```

### 10.2 Phase 2: Planning Tables (Week 1)
```bash
php artisan migrate --path=.../ms_planning_weekly_table.php
php artisan migrate --path=.../ms_planning_assignment_table.php
php artisan migrate --path=.../ms_planning_approval_table.php
```

### 10.3 Phase 3: Dispatcher Tables (Week 2) ⭐ UPDATED
```bash
php artisan migrate --path=.../tr_tms_dispatcher_main_table.php
php artisan migrate --path=.../tr_tms_dispatcher_detail_table.php
php artisan migrate --path=.../tr_tms_dispatcher_shuttle_table.php
php artisan migrate --path=.../tr_tms_tanker_delivery_table.php
```

### 10.4 Phase 4: Accounting Integration (Week 2) ⭐ NEW
```bash
php artisan migrate --path=.../tr_acc_tms_transaksi_sales_table.php
php artisan migrate --path=.../tr_acc_tms_transaksi_kasir_table.php
```

### 10.5 Phase 5: Execution Tables (Week 2)
```bash
php artisan migrate --path=.../ms_trip_execution_table.php
php artisan migrate --path=.../ms_gps_tracking_table.php
php artisan migrate --path=.../ms_pod_table.php
```

### 10.6 Phase 6: Settlement & Audit (Week 3)
```bash
php artisan migrate --path=.../ms_settlement_table.php
php artisan migrate --path=.../ms_settlement_detail_table.php
php artisan migrate --path=.../ms_driver_payment_table.php
php artisan migrate --path=.../ms_dispatch_adjustment_log_table.php
```

### 10.7 Phase 7: Triggers & Functions (Week 3) ⭐ NEW
```bash
# Auto-triggers:
# - Dispatch APPROVED → Create Uang Jalan
# - Dispatch COMPLETED → Create Invoice + Uang Jasa
php artisan db:seed --class=CreateAccountingTriggersSeeder
```

---

## 11. KEY RELATIONSHIPS DIAGRAM

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    TMS TABLE RELATIONSHIPS                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  [MASTER DATA]                                                              │
│                                                                             │
│  ms_client (1) ──┬─→ ms_planning_weekly (N)                                │
│                  ├─→ ms_tariff_contract (N) ⭐                              │
│                  └─→ tr_tms_dispatcher_main (N)                             │
│                                                                             │
│  ms_route (1) ───┬─→ ms_planning_weekly (N)                                │
│                  ├─→ ms_tariff_rate (N) ⭐                                  │
│                  └─→ tr_tms_dispatcher_main (N)                             │
│                                                                             │
│  ms_tariff_contract (1) ──→ ms_tariff_rate (N) ⭐                           │
│  ms_tariff_rate (1) ──→ ms_tariff_special_condition (N)                     │
│                                                                             │
│  [TRANSACTIONS]                                                             │
│                                                                             │
│  ms_planning_weekly (1) ──┬─→ ms_planning_assignment (N)                   │
│                           └─→ tr_tms_dispatcher_shuttle (1)                 │
│                                                                             │
│  ms_planning_assignment (1) ──→ tr_tms_dispatcher_main (1)                 │
│                                                                             │
│  tr_tms_dispatcher_main (1) ──┬─→ tr_tms_dispatcher_detail (N)             │
│                               ├─→ tr_tms_dispatcher_shuttle (1) ⭐ 1-to-1  │
│                               ├─→ tr_tms_tanker_delivery (1) ⭐            │
│                               ├─→ tr_acc_tms_transaksi_sales (1) ⭐        │
│                               ├─→ tr_acc_tms_transaksi_kasir (2) ⭐        │
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
│  ms_vehicle (1) ──→ ms_flowmeter_calibration (N) ⭐ (tanker only)          │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 12. FEATURE COMPLETION STATUS

### 12.1 Completed Features ✅
- ✅ Master data structure (Client, Location, Route, Driver, Vehicle)
- ✅ Planning tables (Weekly planning, Assignment, Approval)
- ✅ **Dispatcher as SPJ** (tr_tms_dispatcher_main) ⭐
- ✅ **Shuttle document flow** (CO/DN/BTB tracking) ⭐
- ✅ **Tariff management** (Many-to-many, dynamic pricing) ⭐
- ✅ **Multiple pricing models** (TRIP, VOLUME, DISTANCE, WEIGHT, TIME) ⭐
- ✅ **Tanker operations** (Volume measurement, flowmeter) ⭐
- ✅ **Accounting integration** (Auto-generate invoices, uang jalan, uang jasa) ⭐
- ✅ Execution tables (Trip, GPS, POD)
- ✅ Settlement tables
- ✅ Audit trail (Adjustment log)
- ✅ AQUA master data extracted (16 locations, 5 truck types, 11 routes)

### 12.2 In Progress ⏳
- ⏳ Laravel migration files generation
- ⏳ Model classes with relationships
- ⏳ Seeder files for master data
- ⏳ REST API endpoints

### 12.3 Not Started ❌
- ❌ Frontend UI development
- ❌ Mobile driver app
- ❌ GPS device integration
- ❌ E-toll API integration
- ❌ SMS gateway integration
- ❌ Flowmeter IoT integration (for real-time volume)

---

## 13. QUICK REFERENCE: FIND WHAT YOU NEED

### "I need to understand..."

**...the overall system:**
→ `TMS_Project_Blueprint.md`

**...master data structure:**
→ `Complete_Master_Data_Structure.md`

**...transaction flow:**
→ `Database_Transaction_Tables_Plan.md`

**...dispatch operations:**
→ `Transaction_Tables_Dispatcher_Accounting.md`

**...shuttle documents (CO/DN/BTB):**
→ `Shuttle_Dispatcher_Document_Flow.md`

**...pricing & tariffs:**
→ `Tariff_Structure_Analysis.md`

**...tanker operations:**
→ `Tanker_Truck_Volume_Measurement.md`

**...AQUA real data:**
→ `AQUA_Master_Data_Analysis.md`

**...route management:**
→ `Route_Management_Specification.md`

**...planning modes:**
→ `Shuttle_Planning_Modes.md`

**...cost & settlement:**
→ `Cost_Structure_Settlement_System.md`

**...adjustments:**
→ `Dispatcher_Adjustment_Module.md`

**...driver assignment:**
→ `Driver_Order_Assignment_System.md`

---

## 14. GLOSSARY

**CO**: Customer Order (Surat pesanan dari customer, contoh: AQUA kirim Excel setiap Jumat)

**DN**: Delivery Note (Surat jalan dari supplier saat loading, contoh: Dari Danone/AQUA)

**BTB**: Berita Terima Barang (Proof of delivery dari customer saat unloading)

**SPJ**: Surat Perintah Jalan (Dispatch order, main transaction record)

**Uang Jalan**: Cash advance untuk driver (fuel, toll, parking, meal) - dibayar sebelum trip

**Uang Jasa**: Driver fee/commission - dibayar setelah trip selesai

**POD**: Proof of Delivery (Bukti pengiriman dengan foto & tanda tangan)

**Flowmeter**: Alat ukur volume liquid pada tanker (accuracy ±0.5%)

**JUGRACK**: Truck type untuk mengangkut botol (bottle carrier)

**ODOL**: Truck type dengan overhead door loading

**PALLET**: Truck type untuk pallet loading

**KL**: Kilo Liter (1 KL = 1,000 liters)

**AR**: Accounts Receivable (Piutang dari customer)

**AP**: Accounts Payable (Hutang ke driver/vendor)

---

## 15. SUMMARY STATISTICS

**Documentation:**
- Total Documents: 16
- Total Lines: 65,000+
- Total Pages (estimated): 650+

**Database:**
- Master Data Tables: 26
- Transaction Tables: 20
- Total Tables: 46
- High Volume Tables: 1 (ms_gps_tracking)

**Data Volume:**
- Rows/Week: ~152,900
- Storage/Week: ~50 MB
- Storage/Year: ~2.4 GB

**Features:**
- Planning Modes: 3 (Weekly, Ad-hoc, Hybrid)
- Dispatch Types: 7 (Shuttle, Backhaul, Adhoc, Urgent, Multi-drop, Empty, Relocation)
- Pricing Models: 5 (Trip, Volume, Distance, Weight, Time)
- Truck Types: 8 (JUGRACK, PALLET, ODOL, Tanker, etc.)
- Time Windows: 12 (2-hour slots)

**Real Data:**
- AQUA Locations: 16
- AQUA Routes: 11
- AQUA Weekly Shipments: 412 → 145 trips

---

**Last Updated**: 18 November 2025  
**Next Update**: After migration files generation  
**Status**: Design Complete - Ready for Implementation 🚀
