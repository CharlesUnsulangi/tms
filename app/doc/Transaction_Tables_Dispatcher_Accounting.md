# TMS - TRANSACTION TABLES: DISPATCH & ACCOUNTING INTEGRATION

**Created:** 18 November 2025  
**Purpose:** Revised transaction structure dengan Dispatch Order sebagai "Surat Perintah Jalan" yang terintegrasi dengan Accounting  
**Status:** Design Phase - REVISED

---

## KONSEP UTAMA: DISPATCH ORDER SEBAGAI "SURAT PERINTAH JALAN"

### Business Flow:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                  DISPATCH ORDER SEBAGAI PUSAT TRANSAKSI                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│   1. PLANNING                                                               │
│      └─ ms_planning_weekly (CO Planning dari client)                        │
│                                                                             │
│   2. DISPATCH ORDER (Surat Perintah Jalan) ⭐ MAIN TRANSACTION              │
│      └─ tr_tms_dispatcher_main                                              │
│         ├─ Tipe: SHUTTLE, BACKHAUL, ADHOC, URGENT, MULTI_DROP              │
│         ├─ Status: DRAFT → APPROVED → DISPATCHED → COMPLETED               │
│         ├─ Berisi: Route, Driver, Vehicle, Schedule, Cost, Revenue         │
│         └─ Link ke:                                                        │
│            ├─ tr_tms_dispatcher_detail (breakdown per DO)                  │
│            ├─ tr_acc_tms_transaksi_sales (Invoice ke Customer) 💰         │
│            └─ tr_acc_tms_transaksi_kasir (Uang Jalan + Uang Jasa) 💵      │
│                                                                             │
│   3. EXECUTION                                                              │
│      ├─ tr_tms_trip_execution (Actual trip)                                │
│      ├─ tr_tms_gps_tracking (Real-time GPS)                                │
│      └─ tr_tms_pod (Proof of Delivery)                                     │
│                                                                             │
│   4. ACCOUNTING (Auto-generated from Dispatch)                             │
│      ├─ tr_acc_tms_transaksi_sales (AR - Piutang Customer)                 │
│      │   └─ Trigger: Dispatch status = COMPLETED + POD uploaded            │
│      └─ tr_acc_tms_transaksi_kasir (AP - Uang Jalan & Jasa Driver)         │
│          ├─ Uang Jalan: Trigger saat Dispatch status = APPROVED            │
│          └─ Uang Jasa: Trigger setelah Trip status = COMPLETED             │
│                                                                             │
│   5. SETTLEMENT                                                             │
│      └─ tr_tms_settlement (Closing & Reconciliation)                        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## STRUKTUR TABEL TRANSAKSI (REVISED)

### 1. tr_tms_dispatcher_main ⭐ (SURAT PERINTAH JALAN)

**Purpose**: Main dispatch order - "Surat Perintah Jalan" yang menjadi pusat transaksi

**Lifecycle**: 
```
DRAFT → REVIEW → APPROVED → DISPATCHED → IN_PROGRESS → POD_UPLOADED → COMPLETED → INVOICED → SETTLED
```

```sql
CREATE TABLE tr_tms_dispatcher_main (
    -- Primary Key
    dispatch_id VARCHAR(20) PRIMARY KEY,  -- DSP-2025-11-18-001
    dispatch_number VARCHAR(50) UNIQUE NOT NULL,  -- SPJ-001/TMS/XI/2025
    
    -- Dispatch Type (Tipe Surat Perintah)
    dispatch_type VARCHAR(20) NOT NULL,
    -- Types:
    --   SHUTTLE: Regular weekly shuttle
    --   BACKHAUL: Return trip dengan muatan
    --   ADHOC: Ad-hoc / spot order
    --   URGENT: Urgent delivery (same day)
    --   MULTI_DROP: Multiple drop points
    --   EMPTY_RETURN: Pulang kosong
    --   POOL_RELOCATION: Relokasi truck antar pool
    
    dispatch_category VARCHAR(20),  -- PRIMARY, SECONDARY (if part of multi-trip)
    parent_dispatch_id VARCHAR(20),  -- For backhaul (linked to main shuttle)
    
    -- Source Planning
    planning_id VARCHAR(20),  -- Link to ms_planning_weekly (if from CO Planning)
    planning_ref_no VARCHAR(50),  -- Original SO/Shipment number
    
    -- Client & Order Info
    client_id VARCHAR(20) NOT NULL,
    client_po_number VARCHAR(50),  -- PO from customer
    client_so_number VARCHAR(50),  -- SO from customer
    
    -- Route & Location
    route_id VARCHAR(20) NOT NULL,
    origin_location_id VARCHAR(20) NOT NULL,
    destination_location_id VARCHAR(20) NOT NULL,
    
    -- Cargo Details
    cargo_description NVARCHAR(500),  -- e.g., "AQUA 600ml - 2900 cartons"
    qty_planned DECIMAL(10,2),  -- 2900 cartons
    qty_actual_loaded DECIMAL(10,2),  -- Actual loaded (may differ)
    qty_actual_delivered DECIMAL(10,2),  -- Actual delivered
    qty_damaged DECIMAL(10,2),  -- Damaged during transport
    uom VARCHAR(10),  -- CTN, KG, M3
    
    estimated_weight_kg DECIMAL(10,2),
    estimated_volume_m3 DECIMAL(10,2),
    
    -- Vehicle & Driver Assignment
    vehicle_id VARCHAR(20) NOT NULL,
    truck_type_id VARCHAR(20) NOT NULL,
    driver_id VARCHAR(20) NOT NULL,
    co_driver_id VARCHAR(20),  -- For long haul
    
    -- Scheduling
    scheduled_date DATE NOT NULL,
    scheduled_departure_time TIME,
    scheduled_arrival_time TIME,
    time_window_id VARCHAR(20),  -- Link to ms_time_window
    
    -- Route Details
    estimated_distance_km DECIMAL(8,2),
    estimated_duration_hours DECIMAL(5,2),
    route_notes NVARCHAR(500),
    
    -- PRICING & REVENUE (From Tariff Contract) 💰
    tariff_contract_id VARCHAR(20),  -- Link to ms_tariff_contract
    tariff_rate_id VARCHAR(20),  -- Link to ms_tariff_rate
    
    -- Revenue Calculation
    base_rate DECIMAL(15,2),  -- Base tariff
    surcharge_night_shift DECIMAL(15,2),  -- Night shift surcharge
    surcharge_weekend DECIMAL(15,2),  -- Weekend surcharge
    surcharge_urgent DECIMAL(15,2),  -- Urgent surcharge
    surcharge_waiting_time DECIMAL(15,2),  -- Waiting time charge
    surcharge_detention DECIMAL(15,2),  -- Detention charge
    discount_volume DECIMAL(15,2),  -- Volume discount
    discount_early_payment DECIMAL(15,2),  -- Early payment discount
    
    gross_revenue DECIMAL(15,2),  -- Total revenue before discount
    net_revenue DECIMAL(15,2),  -- After all discounts
    revenue_currency VARCHAR(3) DEFAULT 'IDR',
    
    -- COST ESTIMATION 💸
    estimated_fuel_cost DECIMAL(15,2),
    estimated_toll_cost DECIMAL(15,2),
    estimated_parking_cost DECIMAL(15,2),
    estimated_loading_cost DECIMAL(15,2),
    estimated_driver_salary DECIMAL(15,2),
    estimated_maintenance_cost DECIMAL(15,2),
    estimated_overhead_cost DECIMAL(15,2),
    
    total_estimated_cost DECIMAL(15,2),
    estimated_gross_margin DECIMAL(15,2),  -- Revenue - Cost
    estimated_margin_percent DECIMAL(5,2),  -- (Margin/Revenue) * 100
    
    -- UANG JALAN (Cash Advance for Driver) 💵
    uang_jalan_amount DECIMAL(15,2),  -- Total cash advance
    uang_jalan_fuel DECIMAL(15,2),  -- For fuel
    uang_jalan_toll DECIMAL(15,2),  -- For toll
    uang_jalan_parking DECIMAL(15,2),  -- For parking
    uang_jalan_meal DECIMAL(15,2),  -- For meals
    uang_jalan_other DECIMAL(15,2),  -- Other expenses
    
    uang_jalan_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → APPROVED → DISBURSED → ACCOUNTED
    uang_jalan_disbursed_at DATETIME,
    uang_jalan_disbursed_by VARCHAR(20),
    
    -- UANG JASA (Driver Fee/Commission) 💰
    uang_jasa_type VARCHAR(20),  -- PER_TRIP, PER_KM, PERCENTAGE
    uang_jasa_base_amount DECIMAL(15,2),  -- Base driver fee
    uang_jasa_bonus_ontime DECIMAL(15,2),  -- On-time bonus
    uang_jasa_bonus_safety DECIMAL(15,2),  -- Safety bonus
    uang_jasa_penalty_late DECIMAL(15,2),  -- Late penalty
    uang_jasa_penalty_damage DECIMAL(15,2),  -- Damage penalty
    
    total_uang_jasa DECIMAL(15,2),  -- Net driver fee
    uang_jasa_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → CALCULATED → APPROVED → PAID
    uang_jasa_paid_at DATETIME,
    uang_jasa_paid_by VARCHAR(20),
    
    -- STATUS WORKFLOW
    status VARCHAR(20) DEFAULT 'DRAFT',
    -- Status flow:
    --   DRAFT: Initial creation
    --   REVIEW: Pending dispatcher review
    --   APPROVED: Approved by dispatcher/manager
    --   DISPATCHED: Sent to driver (notification sent)
    --   CONFIRMED: Driver confirmed
    --   IN_PROGRESS: Trip started
    --   POD_UPLOADED: POD uploaded by driver
    --   COMPLETED: Trip completed
    --   INVOICED: Invoice created for customer
    --   SETTLED: Payment settled
    --   CANCELLED: Cancelled
    --   REJECTED: Rejected
    
    status_changed_at DATETIME,
    status_changed_by VARCHAR(20),
    status_reason NVARCHAR(500),
    
    -- APPROVAL WORKFLOW
    requires_approval BIT DEFAULT 1,
    approval_level VARCHAR(20),  -- DISPATCHER, MANAGER, DIRECTOR
    approved_at DATETIME,
    approved_by VARCHAR(20),
    approval_notes NVARCHAR(500),
    
    rejected_at DATETIME,
    rejected_by VARCHAR(20),
    rejection_reason NVARCHAR(500),
    
    -- NOTIFICATION TO DRIVER
    notification_sent BIT DEFAULT 0,
    notification_sent_at DATETIME,
    notification_method VARCHAR(20),  -- APP, SMS, WA, EMAIL
    notification_read BIT DEFAULT 0,
    notification_read_at DATETIME,
    
    driver_confirmed BIT DEFAULT 0,
    driver_confirmed_at DATETIME,
    driver_confirmation_notes NVARCHAR(500),
    
    -- ACTUAL EXECUTION (Filled after trip)
    actual_departure_time DATETIME,
    actual_arrival_time DATETIME,
    actual_distance_km DECIMAL(8,2),
    actual_duration_hours DECIMAL(5,2),
    
    actual_fuel_consumed_liters DECIMAL(8,2),
    actual_fuel_cost DECIMAL(15,2),
    actual_toll_cost DECIMAL(15,2),
    actual_parking_cost DECIMAL(15,2),
    
    -- PERFORMANCE METRICS
    on_time_departure BIT,
    on_time_arrival BIT,
    variance_distance_km DECIMAL(8,2),  -- Actual - Planned
    variance_duration_hours DECIMAL(5,2),
    
    has_issues BIT DEFAULT 0,
    issue_description NVARCHAR(1000),
    is_delayed BIT DEFAULT 0,
    delay_reason NVARCHAR(500),
    
    -- GPS SUMMARY
    total_gps_points INT,
    speeding_violations_count INT,
    route_deviation_count INT,
    harsh_braking_count INT,
    harsh_acceleration_count INT,
    
    -- POD INFO
    pod_id VARCHAR(20),  -- Link to tr_tms_pod
    pod_uploaded_at DATETIME,
    pod_verified_at DATETIME,
    pod_verified_by VARCHAR(20),
    
    -- ACCOUNTING INTEGRATION LINKS 💰
    sales_invoice_id VARCHAR(20),  -- Link to tr_acc_tms_transaksi_sales
    sales_invoice_generated_at DATETIME,
    
    kasir_uang_jalan_id VARCHAR(20),  -- Link to tr_acc_tms_transaksi_kasir (advance)
    kasir_uang_jasa_id VARCHAR(20),  -- Link to tr_acc_tms_transaksi_kasir (payment)
    
    -- SETTLEMENT LINK
    settlement_id VARCHAR(20),  -- Link to tr_tms_settlement
    settlement_status VARCHAR(20),  -- PENDING, INCLUDED, VERIFIED, PAID
    
    -- SPECIAL FLAGS
    is_urgent BIT DEFAULT 0,
    is_backhaul BIT DEFAULT 0,
    is_multi_drop BIT DEFAULT 0,
    is_night_shift BIT DEFAULT 0,
    is_weekend BIT DEFAULT 0,
    is_long_haul BIT DEFAULT 0,  -- >150km
    
    requires_special_handling BIT DEFAULT 0,
    special_handling_notes NVARCHAR(500),
    
    -- CANCELLATION (if cancelled)
    cancelled_at DATETIME,
    cancelled_by VARCHAR(20),
    cancellation_reason NVARCHAR(500),
    cancellation_penalty DECIMAL(15,2),
    
    -- TAGS & CATEGORIZATION
    tags NVARCHAR(200),  -- e.g., "AQUA,PRIORITY,COLD_CHAIN"
    priority_level VARCHAR(20) DEFAULT 'NORMAL',  -- LOW, NORMAL, HIGH, URGENT
    
    -- NOTES & INSTRUCTIONS
    dispatcher_notes NVARCHAR(1000),
    driver_instructions NVARCHAR(1000),
    customer_notes NVARCHAR(1000),
    
    -- AUDIT TRAIL
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(20),
    updated_at DATETIME,
    updated_by VARCHAR(20),
    
    -- VERSION CONTROL (for audit)
    version INT DEFAULT 1,  -- Increment on major changes
    
    -- FOREIGN KEYS
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (origin_location_id) REFERENCES ms_location(location_id),
    FOREIGN KEY (destination_location_id) REFERENCES ms_location(location_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(vehicle_id),
    FOREIGN KEY (truck_type_id) REFERENCES ms_truck_type(truck_type_id),
    FOREIGN KEY (driver_id) REFERENCES ms_driver(driver_id),
    FOREIGN KEY (co_driver_id) REFERENCES ms_driver(driver_id),
    FOREIGN KEY (time_window_id) REFERENCES ms_time_window(window_id),
    FOREIGN KEY (tariff_contract_id) REFERENCES ms_tariff_contract(contract_id),
    FOREIGN KEY (tariff_rate_id) REFERENCES ms_tariff_rate(rate_id),
    FOREIGN KEY (planning_id) REFERENCES ms_planning_weekly(planning_id),
    FOREIGN KEY (parent_dispatch_id) REFERENCES tr_tms_dispatcher_main(dispatch_id)
);

-- INDEXES for Performance
CREATE INDEX idx_dispatch_client ON tr_tms_dispatcher_main(client_id, scheduled_date);
CREATE INDEX idx_dispatch_driver ON tr_tms_dispatcher_main(driver_id, scheduled_date);
CREATE INDEX idx_dispatch_vehicle ON tr_tms_dispatcher_main(vehicle_id, scheduled_date);
CREATE INDEX idx_dispatch_route ON tr_tms_dispatcher_main(route_id, scheduled_date);
CREATE INDEX idx_dispatch_status ON tr_tms_dispatcher_main(status, scheduled_date);
CREATE INDEX idx_dispatch_date ON tr_tms_dispatcher_main(scheduled_date, dispatch_type);
CREATE INDEX idx_dispatch_settlement ON tr_tms_dispatcher_main(settlement_id, settlement_status);
CREATE INDEX idx_dispatch_planning ON tr_tms_dispatcher_main(planning_id);

-- UNIQUE constraint for dispatch number
CREATE UNIQUE INDEX idx_dispatch_number ON tr_tms_dispatcher_main(dispatch_number);
```

---

### 2. tr_tms_dispatcher_detail (Detail Breakdown per DO)

**Purpose**: Detail breakdown untuk multi-drop atau complex dispatch

```sql
CREATE TABLE tr_tms_dispatcher_detail (
    -- Primary Key
    detail_id VARCHAR(20) PRIMARY KEY,
    dispatch_id VARCHAR(20) NOT NULL,
    
    -- Sequence
    sequence_no INT,  -- 1, 2, 3 for multi-drop
    
    -- Drop Location
    drop_location_id VARCHAR(20),
    drop_contact_person NVARCHAR(200),
    drop_contact_phone VARCHAR(20),
    
    -- Cargo per Drop
    cargo_description NVARCHAR(500),
    qty_planned DECIMAL(10,2),
    qty_delivered DECIMAL(10,2),
    qty_damaged DECIMAL(10,2),
    uom VARCHAR(10),
    
    -- Timing per Drop
    scheduled_arrival_time TIME,
    actual_arrival_time DATETIME,
    unloading_start_time DATETIME,
    unloading_end_time DATETIME,
    departure_time DATETIME,
    
    -- POD per Drop
    pod_photo_path VARCHAR(500),
    receiver_name NVARCHAR(200),
    receiver_signature_image VARCHAR(500),
    delivery_notes NVARCHAR(500),
    
    -- Status
    status VARCHAR(20) DEFAULT 'PENDING',
    -- PENDING → IN_PROGRESS → COMPLETED → VERIFIED
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (dispatch_id) REFERENCES tr_tms_dispatcher_main(dispatch_id),
    FOREIGN KEY (drop_location_id) REFERENCES ms_location(location_id)
);

CREATE INDEX idx_detail_dispatch ON tr_tms_dispatcher_detail(dispatch_id, sequence_no);
```

---

### 3. tr_acc_tms_transaksi_sales (Invoice ke Customer) 💰

**Purpose**: Auto-generate invoice untuk customer setelah trip completed

**Trigger**: Dispatch status = COMPLETED + POD verified

```sql
CREATE TABLE tr_acc_tms_transaksi_sales (
    -- Primary Key
    sales_id VARCHAR(20) PRIMARY KEY,  -- SLS-2025-11-001
    invoice_number VARCHAR(50) UNIQUE NOT NULL,  -- INV-TMS-2025-11-001
    
    -- Link to Dispatch
    dispatch_id VARCHAR(20) NOT NULL,
    
    -- Customer Info
    client_id VARCHAR(20) NOT NULL,
    client_name NVARCHAR(200),
    client_npwp VARCHAR(30),
    client_address NVARCHAR(500),
    
    -- Invoice Info
    invoice_date DATE NOT NULL,
    invoice_due_date DATE,  -- Based on payment terms (NET30, etc.)
    
    payment_terms VARCHAR(20),  -- NET30, NET45, NET60, COD
    credit_limit DECIMAL(15,2),
    
    -- Invoice Items (from Dispatch)
    description NVARCHAR(1000),  -- Service description
    route_description NVARCHAR(200),  -- e.g., "Ciherang → Palapa"
    
    -- Pricing Detail
    base_rate DECIMAL(15,2),
    qty DECIMAL(10,2) DEFAULT 1,  -- Usually 1 trip
    uom VARCHAR(10) DEFAULT 'TRIP',
    
    -- Add-ons & Surcharges
    surcharge_night_shift DECIMAL(15,2),
    surcharge_weekend DECIMAL(15,2),
    surcharge_urgent DECIMAL(15,2),
    surcharge_waiting_time DECIMAL(15,2),
    surcharge_detention DECIMAL(15,2),
    surcharge_other DECIMAL(15,2),
    surcharge_other_description NVARCHAR(200),
    
    total_surcharge DECIMAL(15,2),
    
    -- Discounts
    discount_volume DECIMAL(15,2),
    discount_early_payment DECIMAL(15,2),
    discount_other DECIMAL(15,2),
    discount_other_description NVARCHAR(200),
    
    total_discount DECIMAL(15,2),
    
    -- Subtotal & Tax
    subtotal DECIMAL(15,2),  -- Base + Surcharges - Discounts
    
    tax_rate DECIMAL(5,2) DEFAULT 11.00,  -- PPN 11%
    tax_amount DECIMAL(15,2),
    
    -- Total
    total_amount DECIMAL(15,2),  -- Subtotal + Tax
    currency VARCHAR(3) DEFAULT 'IDR',
    
    -- Payment Status
    payment_status VARCHAR(20) DEFAULT 'UNPAID',
    -- Status: UNPAID → PARTIAL → PAID → OVERDUE
    
    paid_amount DECIMAL(15,2) DEFAULT 0,
    remaining_amount DECIMAL(15,2),
    
    -- Payment Info
    paid_at DATETIME,
    payment_method VARCHAR(20),  -- BANK_TRANSFER, CASH, GIRO, CREDIT_CARD
    payment_reference VARCHAR(100),  -- Bank transfer ref, giro number, etc.
    
    bank_name VARCHAR(100),
    bank_account_number VARCHAR(50),
    
    -- Accounting Link
    journal_entry_id VARCHAR(20),  -- Link to general ledger
    ar_account_id VARCHAR(20),  -- Accounts Receivable account
    revenue_account_id VARCHAR(20),  -- Revenue account
    
    -- Status & Workflow
    status VARCHAR(20) DEFAULT 'DRAFT',
    -- Status: DRAFT → PENDING_APPROVAL → APPROVED → SENT → PAID → CANCELLED
    
    approved_at DATETIME,
    approved_by VARCHAR(20),
    
    sent_to_customer_at DATETIME,
    sent_via VARCHAR(20),  -- EMAIL, WA, COURIER
    
    -- Overdue Handling
    is_overdue BIT DEFAULT 0,
    overdue_days INT,
    overdue_penalty DECIMAL(15,2),
    
    -- Collection Notes
    collection_notes NVARCHAR(1000),
    last_follow_up_date DATE,
    next_follow_up_date DATE,
    
    -- Cancellation (if cancelled)
    cancelled_at DATETIME,
    cancelled_by VARCHAR(20),
    cancellation_reason NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(20),
    updated_at DATETIME,
    updated_by VARCHAR(20),
    
    FOREIGN KEY (dispatch_id) REFERENCES tr_tms_dispatcher_main(dispatch_id),
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id)
);

CREATE INDEX idx_sales_dispatch ON tr_acc_tms_transaksi_sales(dispatch_id);
CREATE INDEX idx_sales_client ON tr_acc_tms_transaksi_sales(client_id, invoice_date);
CREATE INDEX idx_sales_status ON tr_acc_tms_transaksi_sales(payment_status, invoice_due_date);
CREATE INDEX idx_sales_date ON tr_acc_tms_transaksi_sales(invoice_date);
CREATE UNIQUE INDEX idx_sales_invoice_number ON tr_acc_tms_transaksi_sales(invoice_number);
```

---

### 4. tr_acc_tms_transaksi_kasir (Uang Jalan & Uang Jasa) 💵

**Purpose**: Record cash transactions untuk driver (advance & payment)

**Two Types:**
1. **Uang Jalan** (Cash Advance) - Trigger: Dispatch status = APPROVED
2. **Uang Jasa** (Driver Fee) - Trigger: Trip status = COMPLETED

```sql
CREATE TABLE tr_acc_tms_transaksi_kasir (
    -- Primary Key
    kasir_id VARCHAR(20) PRIMARY KEY,  -- KSR-2025-11-001
    transaction_number VARCHAR(50) UNIQUE NOT NULL,  -- KSR-TMS-2025-11-001
    
    -- Link to Dispatch
    dispatch_id VARCHAR(20) NOT NULL,
    
    -- Transaction Type
    transaction_type VARCHAR(20) NOT NULL,
    -- Types:
    --   UANG_JALAN: Cash advance for trip expenses
    --   UANG_JASA: Driver fee/commission after trip
    --   REIMBURSEMENT: Expense reimbursement
    --   DEDUCTION: Deduction from driver
    --   PENALTY: Penalty fee
    --   BONUS: Bonus payment
    
    -- Driver Info
    driver_id VARCHAR(20) NOT NULL,
    driver_name NVARCHAR(200),
    driver_account_number VARCHAR(50),  -- For bank transfer
    driver_bank_name VARCHAR(100),
    
    -- Transaction Date
    transaction_date DATE NOT NULL,
    transaction_time TIME DEFAULT CONVERT(TIME, GETDATE()),
    
    -- UANG JALAN Breakdown (if type = UANG_JALAN)
    uang_jalan_fuel DECIMAL(15,2),
    uang_jalan_toll DECIMAL(15,2),
    uang_jalan_parking DECIMAL(15,2),
    uang_jalan_meal DECIMAL(15,2),
    uang_jalan_lodging DECIMAL(15,2),  -- If overnight
    uang_jalan_other DECIMAL(15,2),
    uang_jalan_other_description NVARCHAR(200),
    
    subtotal_uang_jalan DECIMAL(15,2),
    
    -- UANG JASA Breakdown (if type = UANG_JASA)
    uang_jasa_base DECIMAL(15,2),  -- Base driver fee
    uang_jasa_per_km DECIMAL(15,2),  -- Per kilometer rate
    uang_jasa_bonus_ontime DECIMAL(15,2),
    uang_jasa_bonus_safety DECIMAL(15,2),
    uang_jasa_bonus_performance DECIMAL(15,2),
    uang_jasa_overtime DECIMAL(15,2),
    
    -- Deductions
    uang_jasa_penalty_late DECIMAL(15,2),
    uang_jasa_penalty_damage DECIMAL(15,2),
    uang_jasa_penalty_speeding DECIMAL(15,2),
    uang_jasa_deduction_advance DECIMAL(15,2),  -- Deduct previous advance if not accounted
    uang_jasa_deduction_other DECIMAL(15,2),
    uang_jasa_deduction_description NVARCHAR(200),
    
    subtotal_uang_jasa DECIMAL(15,2),
    
    -- TOTAL AMOUNT
    gross_amount DECIMAL(15,2),  -- Before deductions
    total_deduction DECIMAL(15,2),
    net_amount DECIMAL(15,2),  -- Amount to pay/receive
    currency VARCHAR(3) DEFAULT 'IDR',
    
    -- Payment Method
    payment_method VARCHAR(20),
    -- Methods: CASH, BANK_TRANSFER, E_WALLET, GIRO
    
    payment_reference VARCHAR(100),  -- Transfer ref, e-wallet trans ID
    
    -- Bank Transfer Info
    bank_name VARCHAR(100),
    bank_account_number VARCHAR(50),
    transfer_date DATE,
    transfer_proof_image VARCHAR(500),
    
    -- Cash Info
    cashier_name VARCHAR(100),
    cashier_id_internal VARCHAR(20),
    cash_receipt_number VARCHAR(50),
    
    -- Accounting Link
    journal_entry_id VARCHAR(20),  -- Link to general ledger
    cash_account_id VARCHAR(20),  -- Cash/bank account
    expense_account_id VARCHAR(20),  -- Expense account (for uang jalan)
    payable_account_id VARCHAR(20),  -- Payable account (for uang jasa)
    
    -- Status
    status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → APPROVED → DISBURSED → ACCOUNTED → RECONCILED
    
    approved_at DATETIME,
    approved_by VARCHAR(20),
    
    disbursed_at DATETIME,
    disbursed_by VARCHAR(20),
    
    -- Driver Acknowledgement
    received_by_driver BIT DEFAULT 0,
    driver_signature_image VARCHAR(500),
    driver_received_at DATETIME,
    
    -- Reconciliation (untuk Uang Jalan)
    is_reconciled BIT DEFAULT 0,
    reconciled_at DATETIME,
    reconciled_by VARCHAR(20),
    
    actual_fuel_expense DECIMAL(15,2),
    actual_toll_expense DECIMAL(15,2),
    actual_parking_expense DECIMAL(15,2),
    actual_other_expense DECIMAL(15,2),
    
    variance_amount DECIMAL(15,2),  -- Advance - Actual (return or shortage)
    variance_notes NVARCHAR(500),
    
    -- Supporting Documents
    fuel_receipt_images NVARCHAR(MAX),  -- JSON array of image paths
    toll_receipt_images NVARCHAR(MAX),
    other_receipt_images NVARCHAR(MAX),
    
    -- Notes
    notes NVARCHAR(1000),
    internal_memo NVARCHAR(500),
    
    -- Cancellation
    cancelled_at DATETIME,
    cancelled_by VARCHAR(20),
    cancellation_reason NVARCHAR(500),
    
    -- Audit
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(20),
    updated_at DATETIME,
    updated_by VARCHAR(20),
    
    FOREIGN KEY (dispatch_id) REFERENCES tr_tms_dispatcher_main(dispatch_id),
    FOREIGN KEY (driver_id) REFERENCES ms_driver(driver_id)
);

CREATE INDEX idx_kasir_dispatch ON tr_acc_tms_transaksi_kasir(dispatch_id);
CREATE INDEX idx_kasir_driver ON tr_acc_tms_transaksi_kasir(driver_id, transaction_date);
CREATE INDEX idx_kasir_type ON tr_acc_tms_transaksi_kasir(transaction_type, status);
CREATE INDEX idx_kasir_date ON tr_acc_tms_transaksi_kasir(transaction_date);
CREATE INDEX idx_kasir_status ON tr_acc_tms_transaksi_kasir(status);
CREATE UNIQUE INDEX idx_kasir_transaction_number ON tr_acc_tms_transaksi_kasir(transaction_number);
```

---

## AUTO-TRIGGER LOGIC (Integration Flow)

### Trigger 1: Generate Uang Jalan (when Dispatch APPROVED)

```sql
CREATE TRIGGER trg_generate_uang_jalan
ON tr_tms_dispatcher_main
AFTER UPDATE
AS
BEGIN
    -- Only trigger when status changes to APPROVED
    IF UPDATE(status)
    BEGIN
        INSERT INTO tr_acc_tms_transaksi_kasir (
            kasir_id,
            transaction_number,
            dispatch_id,
            transaction_type,
            driver_id,
            driver_name,
            transaction_date,
            uang_jalan_fuel,
            uang_jalan_toll,
            uang_jalan_parking,
            uang_jalan_meal,
            uang_jalan_other,
            gross_amount,
            net_amount,
            status,
            created_by
        )
        SELECT
            'KSR' + FORMAT(GETDATE(), 'yyyyMMddHHmmss'),
            'UJ-' + i.dispatch_number,
            i.dispatch_id,
            'UANG_JALAN',
            i.driver_id,
            d.driver_name,
            GETDATE(),
            i.uang_jalan_fuel,
            i.uang_jalan_toll,
            i.uang_jalan_parking,
            i.uang_jalan_meal,
            i.uang_jalan_other,
            i.uang_jalan_amount,
            i.uang_jalan_amount,
            'PENDING',
            i.updated_by
        FROM inserted i
        INNER JOIN deleted old ON i.dispatch_id = old.dispatch_id
        INNER JOIN ms_driver d ON i.driver_id = d.driver_id
        WHERE i.status = 'APPROVED'
          AND old.status != 'APPROVED'
          AND i.uang_jalan_amount > 0;
        
        -- Update dispatch with kasir link
        UPDATE tr_tms_dispatcher_main
        SET kasir_uang_jalan_id = 'KSR' + FORMAT(GETDATE(), 'yyyyMMddHHmmss')
        FROM tr_tms_dispatcher_main t
        INNER JOIN inserted i ON t.dispatch_id = i.dispatch_id
        WHERE i.status = 'APPROVED';
    END
END;
```

---

### Trigger 2: Generate Invoice (when Dispatch COMPLETED + POD verified)

```sql
CREATE TRIGGER trg_generate_sales_invoice
ON tr_tms_dispatcher_main
AFTER UPDATE
AS
BEGIN
    IF UPDATE(status) OR UPDATE(pod_verified_at)
    BEGIN
        INSERT INTO tr_acc_tms_transaksi_sales (
            sales_id,
            invoice_number,
            dispatch_id,
            client_id,
            client_name,
            invoice_date,
            invoice_due_date,
            payment_terms,
            description,
            route_description,
            base_rate,
            surcharge_night_shift,
            surcharge_weekend,
            surcharge_urgent,
            discount_volume,
            subtotal,
            tax_rate,
            tax_amount,
            total_amount,
            status,
            created_by
        )
        SELECT
            'SLS' + FORMAT(GETDATE(), 'yyyyMMddHHmmss'),
            'INV-' + i.dispatch_number,
            i.dispatch_id,
            i.client_id,
            c.client_name,
            GETDATE(),
            DATEADD(DAY, 
                CASE c.payment_terms 
                    WHEN 'NET30' THEN 30
                    WHEN 'NET45' THEN 45
                    WHEN 'NET60' THEN 60
                    ELSE 7
                END, GETDATE()),
            c.payment_terms,
            'Transportation Service: ' + i.cargo_description,
            r.route_name,
            i.base_rate,
            i.surcharge_night_shift,
            i.surcharge_weekend,
            i.surcharge_urgent,
            i.discount_volume,
            i.net_revenue,  -- Subtotal
            11.00,  -- PPN 11%
            i.net_revenue * 0.11,
            i.net_revenue * 1.11,  -- Total with tax
            'APPROVED',
            i.updated_by
        FROM inserted i
        INNER JOIN deleted old ON i.dispatch_id = old.dispatch_id
        INNER JOIN ms_client c ON i.client_id = c.client_id
        INNER JOIN ms_route r ON i.route_id = r.route_id
        WHERE i.status = 'COMPLETED'
          AND old.status != 'COMPLETED'
          AND i.pod_verified_at IS NOT NULL
          AND i.net_revenue > 0;
        
        -- Update dispatch
        UPDATE tr_tms_dispatcher_main
        SET sales_invoice_id = 'SLS' + FORMAT(GETDATE(), 'yyyyMMddHHmmss'),
            sales_invoice_generated_at = GETDATE(),
            status = 'INVOICED'
        FROM tr_tms_dispatcher_main t
        INNER JOIN inserted i ON t.dispatch_id = i.dispatch_id
        WHERE i.status = 'COMPLETED' AND i.pod_verified_at IS NOT NULL;
    END
END;
```

---

### Trigger 3: Generate Uang Jasa (when Trip COMPLETED)

```sql
CREATE TRIGGER trg_generate_uang_jasa
ON tr_tms_dispatcher_main
AFTER UPDATE
AS
BEGIN
    IF UPDATE(status)
    BEGIN
        INSERT INTO tr_acc_tms_transaksi_kasir (
            kasir_id,
            transaction_number,
            dispatch_id,
            transaction_type,
            driver_id,
            driver_name,
            transaction_date,
            uang_jasa_base,
            uang_jasa_bonus_ontime,
            uang_jasa_bonus_safety,
            uang_jasa_penalty_late,
            uang_jasa_penalty_damage,
            gross_amount,
            total_deduction,
            net_amount,
            status,
            created_by
        )
        SELECT
            'KSR' + FORMAT(GETDATE(), 'yyyyMMddHHmmss'),
            'UJS-' + i.dispatch_number,
            i.dispatch_id,
            'UANG_JASA',
            i.driver_id,
            d.driver_name,
            GETDATE(),
            i.uang_jasa_base_amount,
            i.uang_jasa_bonus_ontime,
            i.uang_jasa_bonus_safety,
            i.uang_jasa_penalty_late,
            i.uang_jasa_penalty_damage,
            i.uang_jasa_base_amount + i.uang_jasa_bonus_ontime + i.uang_jasa_bonus_safety,
            i.uang_jasa_penalty_late + i.uang_jasa_penalty_damage,
            i.total_uang_jasa,
            'PENDING',
            i.updated_by
        FROM inserted i
        INNER JOIN deleted old ON i.dispatch_id = old.dispatch_id
        INNER JOIN ms_driver d ON i.driver_id = d.driver_id
        WHERE i.status = 'COMPLETED'
          AND old.status != 'COMPLETED';
        
        -- Update dispatch
        UPDATE tr_tms_dispatcher_main
        SET kasir_uang_jasa_id = 'KSR' + FORMAT(GETDATE(), 'yyyyMMddHHmmss'),
            uang_jasa_status = 'CALCULATED'
        FROM tr_tms_dispatcher_main t
        INNER JOIN inserted i ON t.dispatch_id = i.dispatch_id
        WHERE i.status = 'COMPLETED';
    END
END;
```

---

## SAMPLE DATA FLOW

### Example: Complete Dispatch Flow from Planning to Settlement

```sql
-- Step 1: Create Dispatch Order (Surat Perintah Jalan)
INSERT INTO tr_tms_dispatcher_main (
    dispatch_id, dispatch_number, dispatch_type,
    planning_id, client_id, route_id,
    origin_location_id, destination_location_id,
    vehicle_id, truck_type_id, driver_id,
    scheduled_date, scheduled_departure_time,
    cargo_description, qty_planned, uom,
    base_rate, net_revenue,
    uang_jalan_amount, uang_jalan_fuel, uang_jalan_toll,
    uang_jasa_base_amount,
    status, created_by
) VALUES (
    'DSP001', 'SPJ-001/TMS/XI/2025', 'SHUTTLE',
    'PLN001', 'CUS001', 'ROU001',  -- AQUA, Ciherang-Palapa
    'LOC001', 'LOC005',  -- Ciherang → Palapa
    'VEH001', 'TRK001', 'DRV001',  -- JUGRACK, Driver Budi
    '2025-11-18', '02:00:00',
    'AQUA 600ml - 2900 cartons', 2900, 'CTN',
    800000.00, 1040000.00,  -- After night surcharge & discount
    250000.00, 150000.00, 50000.00,  -- Uang jalan: total, fuel, toll
    200000.00,  -- Uang jasa base
    'DRAFT', 'DISPATCHER01'
);

-- Step 2: Approve Dispatch → AUTO-TRIGGER Uang Jalan
UPDATE tr_tms_dispatcher_main
SET status = 'APPROVED',
    approved_at = GETDATE(),
    approved_by = 'MANAGER01',
    uang_jalan_status = 'APPROVED'
WHERE dispatch_id = 'DSP001';

-- Trigger will auto-create in tr_acc_tms_transaksi_kasir:
-- → kasir_id = KSR20251118001
-- → transaction_type = 'UANG_JALAN'
-- → net_amount = 250,000

-- Step 3: Disburse Uang Jalan (Kasir pays driver)
UPDATE tr_acc_tms_transaksi_kasir
SET status = 'DISBURSED',
    disbursed_at = GETDATE(),
    disbursed_by = 'KASIR01',
    payment_method = 'CASH',
    received_by_driver = 1,
    driver_received_at = GETDATE()
WHERE kasir_id = 'KSR20251118001';

-- Step 4: Dispatch to Driver
UPDATE tr_tms_dispatcher_main
SET status = 'DISPATCHED',
    notification_sent = 1,
    notification_sent_at = GETDATE(),
    notification_method = 'APP'
WHERE dispatch_id = 'DSP001';

-- Step 5: Driver Confirms
UPDATE tr_tms_dispatcher_main
SET driver_confirmed = 1,
    driver_confirmed_at = GETDATE(),
    status = 'CONFIRMED'
WHERE dispatch_id = 'DSP001';

-- Step 6: Trip Starts
UPDATE tr_tms_dispatcher_main
SET status = 'IN_PROGRESS',
    actual_departure_time = GETDATE()
WHERE dispatch_id = 'DSP001';

-- Step 7: Trip Completed + POD Uploaded
UPDATE tr_tms_dispatcher_main
SET status = 'COMPLETED',
    actual_arrival_time = GETDATE(),
    actual_distance_km = 47.5,
    actual_fuel_consumed_liters = 18.5,
    on_time_departure = 1,
    on_time_arrival = 1,
    pod_id = 'POD001',
    pod_uploaded_at = GETDATE(),
    pod_verified_at = GETDATE(),
    pod_verified_by = 'SUPERVISOR01'
WHERE dispatch_id = 'DSP001';

-- Trigger will auto-create 2 records:
-- 1. tr_acc_tms_transaksi_sales (Invoice to AQUA)
--    → invoice_number = INV-SPJ-001/TMS/XI/2025
--    → total_amount = 1,154,400 (with PPN 11%)
-- 2. tr_acc_tms_transaksi_kasir (Uang Jasa)
--    → kasir_id = KSR20251118002
--    → transaction_type = 'UANG_JASA'
--    → net_amount = 200,000

-- Step 8: Approve & Pay Uang Jasa
UPDATE tr_acc_tms_transaksi_kasir
SET status = 'APPROVED',
    approved_at = GETDATE(),
    approved_by = 'MANAGER01'
WHERE kasir_id = 'KSR20251118002';

UPDATE tr_acc_tms_transaksi_kasir
SET status = 'DISBURSED',
    disbursed_at = GETDATE(),
    payment_method = 'BANK_TRANSFER',
    bank_name = 'BCA',
    bank_account_number = '1234567890',
    transfer_date = GETDATE(),
    received_by_driver = 1
WHERE kasir_id = 'KSR20251118002';

-- Step 9: Customer Pays Invoice (later)
UPDATE tr_acc_tms_transaksi_sales
SET payment_status = 'PAID',
    paid_amount = 1154400.00,
    remaining_amount = 0,
    paid_at = GETDATE(),
    payment_method = 'BANK_TRANSFER',
    payment_reference = 'TRF-AQUA-20251125'
WHERE sales_id = 'SLS20251118001';

-- Step 10: Include in Weekly Settlement
UPDATE tr_tms_dispatcher_main
SET settlement_id = 'SETT-W47-2025',
    settlement_status = 'INCLUDED',
    status = 'SETTLED'
WHERE dispatch_id = 'DSP001';
```

---

## ADVANTAGES OF THIS STRUCTURE

### ✅ 1. Single Source of Truth
- **tr_tms_dispatcher_main** adalah pusat transaksi
- Semua accounting auto-generated dari dispatch
- No manual entry → reduce errors

### ✅ 2. Complete Audit Trail
- Every status change tracked with who/when/why
- Link antara Dispatch → Invoice → Payment
- Easy to trace money flow

### ✅ 3. Auto-Integration
- Dispatch APPROVED → Auto create Uang Jalan
- Dispatch COMPLETED → Auto create Invoice + Uang Jasa
- No manual trigger needed

### ✅ 4. Financial Control
- Uang jalan tracked & reconciled
- Invoice generated only after POD verified
- Driver payment linked to performance

### ✅ 5. Flexible Dispatch Types
- Support SHUTTLE, BACKHAUL, ADHOC, URGENT, MULTI_DROP
- Each type can have different pricing

---

## NEXT STEPS

1. ✅ Review structure apakah sudah sesuai kebutuhan
2. Create migration files untuk 4 tables:
   - tr_tms_dispatcher_main
   - tr_tms_dispatcher_detail
   - tr_acc_tms_transaksi_sales
   - tr_acc_tms_transaksi_kasir
3. Create triggers untuk auto-generate accounting records
4. Build API endpoints untuk dispatch workflow
5. Create UI untuk dispatcher dashboard

---

**Document Status**: Ready for Review  
**Next Phase**: Migration Development  
**Integration**: TMS ↔ Accounting (Seamless) 🚀
