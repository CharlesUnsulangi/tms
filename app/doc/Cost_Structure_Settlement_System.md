# TMS - Cost Structure & Settlement System
## (Uang Jasa Driver, Uang Jalan, Uang Kenek, BBM Operator)

**Tanggal:** 17 November 2025  
**Status:** Design Phase - Cost Breakdown Structure  

---

## 1. REALITAS COST STRUCTURE

### 1.1 Konsep Bisnis yang BENAR ✅

```
BIAYA PERJALANAN TRUCK terdiri dari:

1. UANG JASA DRIVER (Driver Compensation)
   - Upah/fee untuk driver
   - Diterima oleh: DRIVER
   - Dibayar saat: Settlement (weekly/bi-weekly)
   - Contoh: Rp 300,000 per trip

2. UANG JALAN (Trip Expenses)
   - Biaya operasional di jalan: parkir, retribusi, makan, toll BACKUP (jika e-toll habis)
   - Diterima oleh: DRIVER (as cash advance before trip)
   - Digunakan untuk: bayar parkir, retribusi, makan, dsb
   - Harus dipertanggungjawabkan dengan nota/bukti
   - NOTE: TOLL sudah pakai E-TOLL CARD, jadi uang jalan hanya untuk backup
   - Contoh: Parkir Rp 20k + Retribusi Rp 15k + Makan Rp 50k = Rp 85k (tanpa toll)

3. E-TOLL (Managed by E-Toll Operator) ⭐ NEW
   - Dikelola oleh: OPERATOR E-TOLL
   - BUKAN diberikan ke driver dalam bentuk uang
   - Sistem: E-TOLL CARD (Brizzi, Flazz, e-Toll, TapCash)
   - Driver tap e-toll card di gerbang tol
   - Auto-deduct dari saldo e-toll card
   - Auto top-up jika saldo < minimum (Rp 50k)
   - Tracking: ms_etoll_transaction
   - Contoh: Toll Cipularang Rp 65k (auto-deduct dari e-toll card)

4. UANG JASA KENEK/HELPER (Helper Compensation)
   - Upah untuk kenek/pembantu driver
   - Diterima oleh: KENEK
   - Dibayar saat: Settlement (bisa berbeda dengan driver)
   - Contoh: Rp 100,000 per trip

5. BBM (FUEL - dikelola OPERATOR BBM)
   - Dikelola oleh: OPERATOR BBM / Departemen BBM
   - BUKAN diberikan ke driver dalam bentuk uang
   - Sistem: 
     a. Driver isi di SPBU rekanan (pakai fuel card)
     b. Atau driver isi dulu, klaim nota ke operator BBM
   - Tracking: Per liter, per kilometer
   - Contoh: 150 km ÷ 4 km/liter × Rp 9,500 = Rp 356,250

6. BIAYA LAIN-LAIN
   - Bongkar muat (loading/unloading) - jika paid by vendor
   - Permit (untuk dangerous goods)
   - Escort (security)
   - dll
```

### 1.2 Flow Pembayaran

```
SEBELUM TRIP:
├─ Driver terima: UANG JALAN (cash advance)
│  └─ Rp 85,000 untuk parkir, retribusi, makan (TANPA toll, karena pakai e-toll)
│
├─ Driver dapat: E-TOLL CARD ⭐
│  └─ Saldo sudah terisi (auto top-up jika < Rp 50k)
│  └─ Untuk bayar toll dengan tap di gerbang tol
│
├─ Driver dapat: FUEL CARD atau VOUCHER BBM
│  └─ Untuk isi BBM di SPBU rekanan
│
└─ Kenek dapat: (optional, bisa settlement akhir)

SETELAH TRIP (Settlement):
├─ Driver bayar:
│  ├─ UANG JASA DRIVER (Rp 300k)
│  └─ Kelebihan UANG JALAN (jika ada sisa)
│
├─ Kenek bayar:
│  └─ UANG JASA KENEK (Rp 100k)
│
├─ E-Toll Operator proses: ⭐
│  ├─ Rekap transaksi toll (dari e-toll card system)
│  ├─ Verifikasi vs estimasi
│  ├─ Cek apakah perlu top-up
│  └─ Catat selisih (jika over/under estimation)
│
├─ Operator BBM proses:
│  ├─ Rekap konsumsi BBM (dari fuel card/nota)
│  ├─ Verifikasi vs estimasi
│  └─ Catat selisih (jika over/under consumption)
│
└─ Finance catat:
   └─ Total cost per trip untuk costing analysis
```

---

## 2. DATABASE STRUCTURE (UPDATED)

### 2.1 ms_fuel_operator (Master Operator BBM)
```sql
CREATE TABLE ms_fuel_operator (
    fuel_operator_id VARCHAR(50) PRIMARY KEY,
    operator_name VARCHAR(100) NOT NULL,
    operator_code VARCHAR(20) UNIQUE NOT NULL,
    
    -- Contact
    pic_name VARCHAR(100),
    pic_phone VARCHAR(20),
    pic_email VARCHAR(100),
    
    -- Fuel Card Type
    fuel_card_type VARCHAR(50), -- PERTAMINA_CARD, SHELL_CARD, VOUCHER, NOTA_REIMBURSEMENT
    
    is_active BIT DEFAULT 1,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME
)

-- Sample Data
INSERT INTO ms_fuel_operator VALUES
('FO001', 'Departemen BBM Internal', 'BBM-INT', 'Agus Fuel Manager', '081234567890', 'agus.fuel@company.com', 'PERTAMINA_CARD', 1, GETDATE(), NULL),
('FO002', 'Operator BBM Eksternal', 'BBM-EXT', 'Budi Fuel Vendor', '082345678901', 'budi@fuelvendor.com', 'NOTA_REIMBURSEMENT', 1, GETDATE(), NULL);
```

### 2.1A ms_etoll_operator (Master Operator E-Toll) ⭐ NEW
```sql
CREATE TABLE ms_etoll_operator (
    etoll_operator_id VARCHAR(50) PRIMARY KEY,
    operator_name VARCHAR(100) NOT NULL,
    operator_code VARCHAR(20) UNIQUE NOT NULL,
    
    -- Contact
    pic_name VARCHAR(100),
    pic_phone VARCHAR(20),
    pic_email VARCHAR(100),
    
    -- E-Toll Provider
    etoll_provider VARCHAR(50), -- BRI_BRIZZI, MANDIRI_ETOLL, BCA_FLAZZ, BNI_TAPCASH
    
    -- API Integration (for auto top-up & monitoring)
    has_api_integration BIT DEFAULT 0,
    api_endpoint VARCHAR(500),
    api_key VARCHAR(200),
    
    is_active BIT DEFAULT 1,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME
)

-- Sample Data
INSERT INTO ms_etoll_operator VALUES
('ETO001', 'Departemen E-Toll Internal', 'ETOLL-INT', 'Siti E-Toll Manager', '081234567891', 'siti.etoll@company.com', 'BRI_BRIZZI', 1, 'https://api.bri.co.id/brizzi', 'API_KEY_XXXX', 1, GETDATE(), NULL),
('ETO002', 'Operator E-Toll BCA', 'ETOLL-BCA', 'Andi BCA', '082345678902', 'andi@bca.com', 'BCA_FLAZZ', 0, NULL, NULL, 1, GETDATE(), NULL);
```

### 2.2 ms_fuel_card (Kartu BBM untuk Driver/Truck)
```sql
CREATE TABLE ms_fuel_card (
    fuel_card_id VARCHAR(50) PRIMARY KEY,
    card_number VARCHAR(50) UNIQUE NOT NULL,
    card_type VARCHAR(50), -- PERTAMINA, SHELL, VIVO, etc
    
    -- Assignment
    assigned_to_driver_id VARCHAR(50), -- bisa assign ke driver tetap
    assigned_to_vehicle_id VARCHAR(50), -- atau ke truck tetap
    
    -- Card Status
    status VARCHAR(20) DEFAULT 'ACTIVE', -- ACTIVE, SUSPENDED, LOST, EXPIRED
    
    -- Limit
    daily_limit_liter DECIMAL(10,2),
    monthly_limit_liter DECIMAL(10,2),
    current_month_usage DECIMAL(10,2) DEFAULT 0,
    
    -- Validity
    issued_date DATE,
    expired_date DATE,
    
    fuel_operator_id VARCHAR(50),
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (assigned_to_driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (assigned_to_vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (fuel_operator_id) REFERENCES ms_fuel_operator(fuel_operator_id)
)
```

### 2.2A ms_etoll_card (E-Toll Card untuk Driver/Truck) ⭐ NEW
```sql
CREATE TABLE ms_etoll_card (
    etoll_card_id VARCHAR(50) PRIMARY KEY,
    card_number VARCHAR(50) UNIQUE NOT NULL,
    card_type VARCHAR(50), -- BRI_BRIZZI, MANDIRI_ETOLL, BCA_FLAZZ, BNI_TAPCASH
    
    -- Assignment
    assigned_to_driver_id VARCHAR(50), -- bisa assign ke driver tetap
    assigned_to_vehicle_id VARCHAR(50), -- atau ke truck tetap
    
    -- Card Status
    status VARCHAR(20) DEFAULT 'ACTIVE', -- ACTIVE, SUSPENDED, LOST, EXPIRED, BLOCKED
    
    -- Balance & Limit
    current_balance DECIMAL(15,2) DEFAULT 0, -- saldo saat ini
    minimum_balance DECIMAL(15,2) DEFAULT 50000, -- min balance sebelum top-up
    auto_topup_enabled BIT DEFAULT 1, -- auto top-up jika saldo < minimum
    auto_topup_amount DECIMAL(15,2) DEFAULT 500000, -- nominal top-up
    
    daily_limit DECIMAL(15,2), -- limit pemakaian per hari
    monthly_limit DECIMAL(15,2), -- limit pemakaian per bulan
    current_month_usage DECIMAL(15,2) DEFAULT 0,
    
    -- Validity
    issued_date DATE,
    expired_date DATE,
    
    -- Last Transaction
    last_transaction_date DATETIME,
    last_transaction_amount DECIMAL(15,2),
    last_transaction_location VARCHAR(200),
    
    etoll_operator_id VARCHAR(50),
    
    is_active BIT DEFAULT 1,
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME,
    
    FOREIGN KEY (assigned_to_driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (assigned_to_vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (etoll_operator_id) REFERENCES ms_etoll_operator(etoll_operator_id),
    
    INDEX idx_card_number (card_number),
    INDEX idx_assigned_driver (assigned_to_driver_id),
    INDEX idx_assigned_vehicle (assigned_to_vehicle_id),
    INDEX idx_status (status)
)

-- Sample Data
INSERT INTO ms_etoll_card VALUES
('ETC001', '6220-1234-5678-9012', 'BRI_BRIZZI', 'DRV001', 'VH001', 
'ACTIVE', 250000, 50000, 1, 500000, 
1000000, 5000000, 0, 
'2025-01-01', '2027-01-01', 
'2025-11-17 06:45:00', 65000, 'Gerbang Tol Cipularang',
'ETO001', 1, 'E-Toll untuk Driver Budi - Truck B1234XYZ', GETDATE(), NULL);
```

### 2.3 ms_client_contract_tariff (UPDATED - Pemisahan Cost Components)
```sql
CREATE TABLE ms_client_contract_tariff (
    contract_tariff_id VARCHAR(50) PRIMARY KEY,
    
    -- Client & Contract
    client_id VARCHAR(50) NOT NULL,
    contract_number VARCHAR(50),
    
    -- Route, Truck, Product
    route_id VARCHAR(50) NOT NULL,
    truck_type_id VARCHAR(50) NOT NULL,
    product_id VARCHAR(50),
    service_type VARCHAR(20),
    
    -- PRICING (Revenue dari Client)
    client_rate DECIMAL(15,2) NOT NULL,
    rate_per_km DECIMAL(10,2),
    rate_per_ton DECIMAL(10,2),
    rate_per_trip DECIMAL(15,2),
    minimum_charge DECIMAL(15,2),
    maximum_charge DECIMAL(15,2),
    discount_percentage DECIMAL(5,2),
    minimum_monthly_trips INT,
    
    -- ========================================
    -- COST COMPONENTS (Vendor Expenses) ⭐⭐⭐
    -- ========================================
    
    -- 1. UANG JASA DRIVER (Driver Compensation)
    uang_jasa_driver_base DECIMAL(15,2), -- base fee per trip
    uang_jasa_driver_per_km DECIMAL(10,2), -- bonus per km (optional)
    uang_jasa_driver_per_ton DECIMAL(10,2), -- bonus per ton (optional)
    uang_jasa_driver_ontime_bonus DECIMAL(15,2), -- bonus jika on-time
    uang_jasa_driver_fullload_bonus DECIMAL(15,2), -- bonus jika full load
    
    -- 2. UANG JALAN (Trip Expenses - Cash Advance to Driver)
    uang_jalan_toll DECIMAL(15,2), -- estimasi toll (BACKUP jika e-toll tidak cukup)
    uang_jalan_parking DECIMAL(15,2), -- estimasi parkir
    uang_jalan_retribusi DECIMAL(15,2), -- retribusi, timbang, dll
    uang_jalan_meal DECIMAL(15,2), -- uang makan driver di jalan (optional)
    uang_jalan_other DECIMAL(15,2), -- lain-lain
    uang_jalan_total AS ( -- computed column
        ISNULL(uang_jalan_toll, 0) + 
        ISNULL(uang_jalan_parking, 0) + 
        ISNULL(uang_jalan_retribusi, 0) + 
        ISNULL(uang_jalan_meal, 0) + 
        ISNULL(uang_jalan_other, 0)
    ) PERSISTED,
    
    -- 2A. E-TOLL (Managed by E-Toll Operator) ⭐ NEW
    etoll_estimated_cost DECIMAL(15,2), -- estimasi biaya toll per trip
    etoll_payment_method VARCHAR(50), -- ETOLL_CARD, CASH (backup), REIMBURSEMENT
    etoll_route_code VARCHAR(50), -- kode rute toll (misal: CIPULARANG, JORR, etc)
    
    -- 3. UANG JASA KENEK (Helper Compensation)
    uang_jasa_kenek_base DECIMAL(15,2), -- base fee per trip
    uang_jasa_kenek_per_ton DECIMAL(10,2), -- bonus per ton (optional)
    requires_kenek BIT DEFAULT 0, -- apakah trip ini perlu kenek?
    
    -- 4. BBM (Fuel - Managed by Fuel Operator)
    bbm_estimated_liter DECIMAL(10,2), -- estimasi liter per trip
    bbm_estimated_cost DECIMAL(15,2), -- estimasi biaya (liter × harga)
    bbm_payment_method VARCHAR(50), -- FUEL_CARD, VOUCHER, REIMBURSEMENT
    bbm_spbu_partner VARCHAR(100), -- SPBU rekanan (Pertamina, Shell, dll)
    
    -- 5. BIAYA LAIN-LAIN
    loading_cost DECIMAL(15,2), -- biaya bongkar muat
    loading_paid_by VARCHAR(20), -- CLIENT, VENDOR, SHARED
    unloading_cost DECIMAL(15,2),
    unloading_paid_by VARCHAR(20),
    
    permit_cost DECIMAL(15,2), -- permit untuk dangerous goods
    escort_cost DECIMAL(15,2), -- security escort
    insurance_cost DECIMAL(15,2), -- asuransi barang
    temperature_control_cost DECIMAL(15,2), -- cooler operation
    
    -- 6. DETENTION (Waiting Time Charge)
    free_waiting_time_minutes INT DEFAULT 60,
    detention_charge_per_hour DECIMAL(15,2),
    
    -- Validity
    effective_date DATE NOT NULL,
    expired_date DATE,
    payment_term_days INT,
    
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
    FOREIGN KEY (product_id) REFERENCES ms_product(product_id)
)
```

### 2.4 ms_dispatch (Trip Execution Record) - UPDATED
```sql
CREATE TABLE ms_dispatch (
    dispatch_id VARCHAR(50) PRIMARY KEY,
    dispatch_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Order & Route
    order_id VARCHAR(50),
    route_id VARCHAR(50) NOT NULL,
    
    -- Assignment
    driver_id VARCHAR(50) NOT NULL,
    vehicle_id VARCHAR(50) NOT NULL,
    helper_id VARCHAR(50), -- kenek (optional)
    
    -- Client & Product
    client_id VARCHAR(50) NOT NULL,
    product_id VARCHAR(50),
    
    -- Load Details
    weight_ton DECIMAL(10,2),
    volume_cbm DECIMAL(10,2),
    quantity INT,
    
    -- Schedule
    planned_departure DATETIME,
    planned_arrival DATETIME,
    actual_departure DATETIME,
    actual_arrival DATETIME,
    
    -- Status
    status VARCHAR(20), -- PLANNED, DEPARTED, IN_TRANSIT, ARRIVED, COMPLETED, CANCELLED
    
    -- ========================================
    -- COST TRACKING (Actual vs Estimated) ⭐
    -- ========================================
    
    -- Tariff yang digunakan
    contract_tariff_id VARCHAR(50), -- link ke ms_client_contract_tariff
    
    -- REVENUE (dari client)
    revenue_amount DECIMAL(15,2), -- actual revenue (bisa beda dari tariff jika ada adjustment)
    
    -- COSTS - UANG JASA DRIVER
    cost_uang_jasa_driver_base DECIMAL(15,2),
    cost_uang_jasa_driver_bonus DECIMAL(15,2), -- total bonus (km + ton + ontime + fullload)
    cost_uang_jasa_driver_total AS (
        ISNULL(cost_uang_jasa_driver_base, 0) + ISNULL(cost_uang_jasa_driver_bonus, 0)
    ) PERSISTED,
    
    -- COSTS - UANG JALAN (Cash Advance to Driver)
    cost_uang_jalan_advanced DECIMAL(15,2), -- total uang jalan yang diberikan ke driver sebelum berangkat
    cost_uang_jalan_actual DECIMAL(15,2), -- actual expenses (dari nota/bukti)
    cost_uang_jalan_variance AS (
        ISNULL(cost_uang_jalan_advanced, 0) - ISNULL(cost_uang_jalan_actual, 0)
    ) PERSISTED, -- selisih (positif = sisa, negatif = kurang)
    
    -- COSTS - E-TOLL (Managed by E-Toll Operator) ⭐ NEW
    etoll_estimated_cost DECIMAL(15,2), -- dari tariff
    etoll_actual_cost DECIMAL(15,2), -- actual cost (dari e-toll card transaction)
    etoll_variance AS (
        ISNULL(etoll_estimated_cost, 0) - ISNULL(etoll_actual_cost, 0)
    ) PERSISTED, -- variance
    etoll_card_id VARCHAR(50), -- e-toll card yang digunakan
    etoll_payment_method VARCHAR(50), -- ETOLL_CARD, CASH (backup)
    
    -- COSTS - UANG JASA KENEK
    cost_uang_jasa_kenek DECIMAL(15,2),
    
    -- COSTS - BBM (Managed by Fuel Operator)
    bbm_estimated_liter DECIMAL(10,2), -- dari tariff
    bbm_actual_liter DECIMAL(10,2), -- actual consumption (dari kartu BBM / nota)
    bbm_actual_cost DECIMAL(15,2), -- actual cost
    bbm_variance_liter AS (
        ISNULL(bbm_estimated_liter, 0) - ISNULL(bbm_actual_liter, 0)
    ) PERSISTED, -- variance (negatif = boros, positif = irit)
    bbm_fuel_card_id VARCHAR(50), -- kartu BBM yang digunakan
    bbm_payment_method VARCHAR(50), -- FUEL_CARD, VOUCHER, REIMBURSEMENT
    
    -- COSTS - OTHER
    cost_loading DECIMAL(15,2),
    cost_unloading DECIMAL(15,2),
    cost_permit DECIMAL(15,2),
    cost_escort DECIMAL(15,2),
    cost_insurance DECIMAL(15,2),
    cost_temperature_control DECIMAL(15,2),
    cost_detention DECIMAL(15,2), -- detention charge jika ada waiting time
    cost_other DECIMAL(15,2),
    
    -- TOTAL COST
    total_cost AS (
        ISNULL(cost_uang_jasa_driver_total, 0) +
        ISNULL(cost_uang_jalan_actual, 0) +
        ISNULL(etoll_actual_cost, 0) + -- ⭐ E-TOLL
        ISNULL(cost_uang_jasa_kenek, 0) +
        ISNULL(bbm_actual_cost, 0) +
        ISNULL(cost_loading, 0) +
        ISNULL(cost_unloading, 0) +
        ISNULL(cost_permit, 0) +
        ISNULL(cost_escort, 0) +
        ISNULL(cost_insurance, 0) +
        ISNULL(cost_temperature_control, 0) +
        ISNULL(cost_detention, 0) +
        ISNULL(cost_other, 0)
    ) PERSISTED,
    
    -- MARGIN
    margin AS (
        ISNULL(revenue_amount, 0) - (
            ISNULL(cost_uang_jasa_driver_total, 0) +
            ISNULL(cost_uang_jalan_actual, 0) +
            ISNULL(etoll_actual_cost, 0) + -- ⭐ E-TOLL
            ISNULL(cost_uang_jasa_kenek, 0) +
            ISNULL(bbm_actual_cost, 0) +
            ISNULL(cost_loading, 0) +
            ISNULL(cost_unloading, 0) +
            ISNULL(cost_permit, 0) +
            ISNULL(cost_escort, 0) +
            ISNULL(cost_insurance, 0) +
            ISNULL(cost_temperature_control, 0) +
            ISNULL(cost_detention, 0) +
            ISNULL(cost_other, 0)
        )
    ) PERSISTED,
    
    -- Settlement Status
    is_settled BIT DEFAULT 0,
    settlement_date DATE,
    settlement_id VARCHAR(50),
    
    notes TEXT,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (order_id) REFERENCES ms_order(order_id),
    FOREIGN KEY (route_id) REFERENCES ms_route(route_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (helper_id) REFERENCES ms_helper(helper_id),
    FOREIGN KEY (client_id) REFERENCES ms_client(client_id),
    FOREIGN KEY (product_id) REFERENCES ms_product(product_id),
    FOREIGN KEY (contract_tariff_id) REFERENCES ms_client_contract_tariff(contract_tariff_id),
    FOREIGN KEY (bbm_fuel_card_id) REFERENCES ms_fuel_card(fuel_card_id),
    FOREIGN KEY (etoll_card_id) REFERENCES ms_etoll_card(etoll_card_id) -- ⭐ NEW
)
```

### 2.5 ms_etoll_transaction (E-Toll Transaction Record) ⭐ NEW
```sql
CREATE TABLE ms_etoll_transaction (
    etoll_transaction_id VARCHAR(50) PRIMARY KEY,
    
    -- Trip Reference
    dispatch_id VARCHAR(50), -- bisa NULL jika top-up manual
    etoll_card_id VARCHAR(50) NOT NULL,
    vehicle_id VARCHAR(50),
    driver_id VARCHAR(50),
    
    -- Transaction Type
    transaction_type VARCHAR(50) NOT NULL, -- TOLL_PAYMENT, TOP_UP, REFUND, ADJUSTMENT
    
    -- Transaction Details
    transaction_date DATETIME NOT NULL,
    transaction_amount DECIMAL(15,2) NOT NULL,
    
    -- Toll Details (for TOLL_PAYMENT)
    toll_gate_name VARCHAR(100), -- "Gerbang Tol Cipularang", "Gerbang Tol Cikampek"
    toll_gate_code VARCHAR(50), -- "GT-CIPULARANG-001"
    toll_entrance VARCHAR(100), -- "Cikupa"
    toll_exit VARCHAR(100), -- "Padalarang"
    toll_distance_km DECIMAL(10,2),
    toll_class VARCHAR(20), -- GOLONGAN_1, GOLONGAN_2, GOLONGAN_3, dst
    
    -- Balance Before & After
    balance_before DECIMAL(15,2),
    balance_after DECIMAL(15,2),
    
    -- Payment Method (for TOP_UP)
    payment_method VARCHAR(50), -- BANK_TRANSFER, CASH, AUTO_TOPUP, VIRTUAL_ACCOUNT
    payment_reference VARCHAR(100), -- nomor transfer / VA number
    
    -- Verification
    is_verified BIT DEFAULT 0,
    verified_by VARCHAR(50), -- e-toll operator
    verified_at DATETIME,
    
    etoll_operator_id VARCHAR(50),
    
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (etoll_card_id) REFERENCES ms_etoll_card(etoll_card_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (etoll_operator_id) REFERENCES ms_etoll_operator(etoll_operator_id),
    
    INDEX idx_dispatch (dispatch_id),
    INDEX idx_card (etoll_card_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_transaction_type (transaction_type)
)

-- Example 1: Top-up e-toll card
INSERT INTO ms_etoll_transaction VALUES
('ETT001', NULL, 'ETC001', 'VH001', 'DRV001',
'TOP_UP', '2025-11-17 05:00:00', 500000,
NULL, NULL, NULL, NULL, NULL, NULL,
250000, 750000, -- balance: 250k -> 750k
'AUTO_TOPUP', 'AUTOTOPUP-20251117-001',
1, 'etoll_operator_siti', GETDATE(),
'ETO001', 'Auto top-up triggered (balance < 50k)', GETDATE());

-- Example 2: Toll payment during trip
INSERT INTO ms_etoll_transaction VALUES
('ETT002', 'DSP001', 'ETC001', 'VH001', 'DRV001',
'TOLL_PAYMENT', '2025-11-17 06:45:00', 65000,
'Gerbang Tol Cipularang', 'GT-CIPULARANG-001', 'Cikupa', 'Padalarang', 120, 'GOLONGAN_2',
750000, 685000, -- balance: 750k -> 685k
NULL, NULL,
1, 'etoll_operator_siti', GETDATE(),
'ETO001', 'Toll payment for dispatch DSP001', GETDATE());
```

### 2.6 ms_dispatch_uang_jalan_detail (Breakdown Uang Jalan - Accountability)
```sql
CREATE TABLE ms_dispatch_uang_jalan_detail (
    uang_jalan_detail_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50) NOT NULL,
    
    -- Expense Type
    expense_type VARCHAR(50) NOT NULL, -- TOLL_BACKUP, PARKING, RETRIBUSI, MEAL, OTHER
    expense_description VARCHAR(200),
    
    -- Amount
    estimated_amount DECIMAL(15,2), -- estimasi
    actual_amount DECIMAL(15,2), -- actual (dari nota)
    
    -- Proof
    receipt_number VARCHAR(50), -- nomor nota/kwitansi
    receipt_photo_url VARCHAR(500), -- foto nota (jika ada)
    
    -- Verification
    is_verified BIT DEFAULT 0,
    verified_by VARCHAR(50),
    verified_at DATETIME,
    
    notes TEXT,
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id)
)

-- Example: Uang jalan breakdown untuk dispatch DSP001
-- NOTE: TOLL sudah pakai E-TOLL CARD, jadi uang jalan hanya untuk backup (jika e-toll saldo habis)
INSERT INTO ms_dispatch_uang_jalan_detail VALUES
('UJD001', 'DSP001', 'TOLL_BACKUP', 'Toll backup (jika e-toll habis)', 0, 0, NULL, NULL, 1, 'supervisor', GETDATE(), 'E-toll mencukupi, tidak perlu backup', GETDATE()),
('UJD002', 'DSP001', 'PARKING', 'Parkir DC Bandung', 20000, 15000, 'PARK-001', NULL, 1, 'supervisor', GETDATE(), 'Lebih murah Rp 5k', GETDATE()),
('UJD003', 'DSP001', 'RETRIBUSI', 'Retribusi jalan kabupaten', 15000, 15000, NULL, NULL, 0, NULL, NULL, 'Belum ada bukti', GETDATE()),
('UJD004', 'DSP001', 'MEAL', 'Uang makan driver di jalan', 50000, 50000, NULL, NULL, 1, 'supervisor', GETDATE(), NULL, GETDATE());

-- Total advanced: Rp 85k (tanpa toll karena pakai e-toll)
-- Total actual: Rp 80k
-- Sisa (harus dikembalikan driver): Rp 5k
```

### 2.6 ms_fuel_consumption (BBM Consumption Record - Managed by Fuel Operator)
```sql
CREATE TABLE ms_fuel_consumption (
    fuel_consumption_id VARCHAR(50) PRIMARY KEY,
    
    -- Trip Reference
    dispatch_id VARCHAR(50) NOT NULL,
    vehicle_id VARCHAR(50) NOT NULL,
    driver_id VARCHAR(50) NOT NULL,
    
    -- Fuel Card
    fuel_card_id VARCHAR(50),
    
    -- Refueling Details
    refuel_date DATETIME NOT NULL,
    spbu_name VARCHAR(100), -- SPBU Pertamina Cikupa, Shell Bandung, dll
    spbu_location VARCHAR(200),
    
    -- Quantity
    liter DECIMAL(10,2) NOT NULL,
    price_per_liter DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(15,2) NOT NULL,
    
    -- Odometer (jika ada)
    odometer_before INT,
    odometer_after INT,
    distance_km AS (ISNULL(odometer_after, 0) - ISNULL(odometer_before, 0)) PERSISTED,
    fuel_efficiency AS (
        CASE 
            WHEN ISNULL(odometer_after, 0) - ISNULL(odometer_before, 0) > 0 
                 AND ISNULL(liter, 0) > 0
            THEN (ISNULL(odometer_after, 0) - ISNULL(odometer_before, 0)) / ISNULL(liter, 0)
            ELSE NULL
        END
    ) PERSISTED, -- km/liter
    
    -- Payment Method
    payment_method VARCHAR(50), -- FUEL_CARD, VOUCHER, CASH_REIMBURSEMENT
    
    -- Receipt
    receipt_number VARCHAR(50),
    receipt_photo_url VARCHAR(500),
    
    -- Verification by Fuel Operator
    is_verified BIT DEFAULT 0,
    verified_by VARCHAR(50), -- fuel operator
    verified_at DATETIME,
    variance_note TEXT, -- catatan jika ada variance (boros/irit)
    
    fuel_operator_id VARCHAR(50),
    
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (vehicle_id) REFERENCES ms_vehicle(id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (fuel_card_id) REFERENCES ms_fuel_card(fuel_card_id),
    FOREIGN KEY (fuel_operator_id) REFERENCES ms_fuel_operator(fuel_operator_id),
    
    INDEX idx_dispatch (dispatch_id),
    INDEX idx_vehicle (vehicle_id),
    INDEX idx_refuel_date (refuel_date)
)

-- Example: BBM consumption untuk dispatch DSP001
INSERT INTO ms_fuel_consumption VALUES
('FC001', 'DSP001', 'VH001', 'DRV001', 'FCARD001', 
'2025-11-17 06:30:00', 'SPBU Pertamina Cikupa', 'Jl. Raya Cikupa KM 5',
40.5, 9500, 384750, -- 40.5 liter × Rp 9,500
12000, 12150, 150, 3.70, -- odometer: 12000 -> 12150 (150 km), efficiency 3.70 km/liter
'FUEL_CARD', 'FUEL-20251117-001', '/uploads/receipts/fuel001.jpg',
1, 'fuel_operator_agus', GETDATE(), 'Normal consumption', 'FO001',
GETDATE());

-- Estimated: 150 km ÷ 4 km/liter = 37.5 liter
-- Actual: 40.5 liter
-- Variance: -3 liter (BOROS 8%)
```

### 2.7 ms_settlement_driver (Driver Settlement/Payroll)
```sql
CREATE TABLE ms_settlement_driver (
    settlement_id VARCHAR(50) PRIMARY KEY,
    settlement_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Driver
    driver_id VARCHAR(50) NOT NULL,
    
    -- Period
    settlement_period_start DATE NOT NULL,
    settlement_period_end DATE NOT NULL,
    
    -- Summary
    total_trips INT,
    total_km DECIMAL(10,2),
    total_ton DECIMAL(10,2),
    
    -- ========================================
    -- INCOME ⭐
    -- ========================================
    total_uang_jasa_base DECIMAL(15,2), -- sum of base fee
    total_uang_jasa_bonus DECIMAL(15,2), -- sum of bonus (km, ton, ontime, fullload)
    total_uang_jasa_driver AS (
        ISNULL(total_uang_jasa_base, 0) + ISNULL(total_uang_jasa_bonus, 0)
    ) PERSISTED,
    
    -- ========================================
    -- UANG JALAN RECONCILIATION ⭐
    -- ========================================
    total_uang_jalan_advanced DECIMAL(15,2), -- total cash advance yang diberikan
    total_uang_jalan_actual DECIMAL(15,2), -- total expenses (dari nota)
    total_uang_jalan_variance AS (
        ISNULL(total_uang_jalan_advanced, 0) - ISNULL(total_uang_jalan_actual, 0)
    ) PERSISTED, -- selisih
    
    -- Jika variance > 0: driver harus kembalikan sisa
    -- Jika variance < 0: company harus bayar kekurangan (driver keluar uang sendiri)
    
    -- ========================================
    -- DEDUCTIONS (Potongan)
    -- ========================================
    deduction_damage DECIMAL(15,2), -- potongan jika ada kerusakan
    deduction_lost_goods DECIMAL(15,2), -- potongan jika barang hilang
    deduction_violation DECIMAL(15,2), -- potongan jika ada pelanggaran
    deduction_cash_advance DECIMAL(15,2), -- potongan kasbon
    deduction_other DECIMAL(15,2),
    total_deduction AS (
        ISNULL(deduction_damage, 0) +
        ISNULL(deduction_lost_goods, 0) +
        ISNULL(deduction_violation, 0) +
        ISNULL(deduction_cash_advance, 0) +
        ISNULL(deduction_other, 0)
    ) PERSISTED,
    
    -- ========================================
    -- NET PAYMENT ⭐⭐⭐
    -- ========================================
    net_payment AS (
        ISNULL(total_uang_jasa_driver, 0) + 
        ISNULL(total_uang_jalan_variance, 0) - -- jika positif (sisa) dikurangi dari payment, jika negatif (kurang) ditambahkan
        ISNULL(total_deduction, 0)
    ) PERSISTED,
    
    -- Payment Status
    status VARCHAR(20) DEFAULT 'DRAFT', -- DRAFT, APPROVED, PAID, CANCELLED
    payment_method VARCHAR(50), -- BANK_TRANSFER, CASH
    payment_date DATE,
    payment_reference VARCHAR(100), -- nomor transfer / bukti bayar
    
    notes TEXT,
    approved_by VARCHAR(50),
    approved_at DATETIME,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id)
)

-- Example: Settlement driver untuk periode 1-15 Nov 2025
INSERT INTO ms_settlement_driver VALUES
('SETD001', 'SETD-2025-11-001', 'DRV001', 
'2025-11-01', '2025-11-15',
10, 1500, 120, -- 10 trips, 1500 km, 120 ton
3000000, 850000, NULL, -- base Rp 3jt, bonus Rp 850k, total Rp 3,850k
1000000, 980000, NULL, -- uang jalan advanced Rp 1jt, actual Rp 980k, sisa Rp 20k (driver harus kembalikan)
0, 0, 0, 100000, 50000, NULL, -- potongan kasbon Rp 100k, other Rp 50k
NULL, -- net payment = 3,850k + (-20k) - 150k = Rp 3,680k
'APPROVED', 'BANK_TRANSFER', '2025-11-17', 'TRF-20251117-001',
'Settlement 1-15 Nov 2025', 'finance_manager', GETDATE(), 'admin', GETDATE(), NULL, NULL);
```

### 2.8 ms_settlement_helper (Helper/Kenek Settlement)
```sql
CREATE TABLE ms_settlement_helper (
    settlement_id VARCHAR(50) PRIMARY KEY,
    settlement_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Helper
    helper_id VARCHAR(50) NOT NULL,
    
    -- Period
    settlement_period_start DATE NOT NULL,
    settlement_period_end DATE NOT NULL,
    
    -- Summary
    total_trips INT,
    total_ton DECIMAL(10,2),
    
    -- INCOME
    total_uang_jasa_kenek_base DECIMAL(15,2),
    total_uang_jasa_kenek_bonus DECIMAL(15,2),
    total_uang_jasa_kenek AS (
        ISNULL(total_uang_jasa_kenek_base, 0) + ISNULL(total_uang_jasa_kenek_bonus, 0)
    ) PERSISTED,
    
    -- DEDUCTIONS
    deduction_damage DECIMAL(15,2),
    deduction_violation DECIMAL(15,2),
    deduction_cash_advance DECIMAL(15,2),
    deduction_other DECIMAL(15,2),
    total_deduction AS (
        ISNULL(deduction_damage, 0) +
        ISNULL(deduction_violation, 0) +
        ISNULL(deduction_cash_advance, 0) +
        ISNULL(deduction_other, 0)
    ) PERSISTED,
    
    -- NET PAYMENT
    net_payment AS (
        ISNULL(total_uang_jasa_kenek, 0) - ISNULL(total_deduction, 0)
    ) PERSISTED,
    
    status VARCHAR(20) DEFAULT 'DRAFT',
    payment_method VARCHAR(50),
    payment_date DATE,
    payment_reference VARCHAR(100),
    
    notes TEXT,
    approved_by VARCHAR(50),
    approved_at DATETIME,
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT GETDATE(),
    updated_by VARCHAR(50),
    updated_at DATETIME,
    
    FOREIGN KEY (helper_id) REFERENCES ms_helper(helper_id)
)
```

---

## 3. COST BREAKDOWN EXAMPLE

### Trip: Jakarta - Bandung (Tronton, PT Indofood, Mie Goreng)

```
┌─────────────────────────────────────────────────────────────┐
│ REVENUE (dari Client)                                       │
├─────────────────────────────────────────────────────────────┤
│ Client Rate (PT Indofood - Contract)    Rp    1,200,000    │
│ Discount 15% (volume 40+ trips/month)   Rp     (180,000)   │
│ TOTAL REVENUE                            Rp    1,020,000    │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ COSTS (Vendor Expenses)                                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ 1. UANG JASA DRIVER (Driver Compensation)                  │
│    ├─ Base fee                           Rp      300,000   │
│    ├─ Bonus per ton (15 ton × Rp 25k)    Rp      375,000   │
│    ├─ On-time bonus                      Rp      100,000   │
│    └─ SUBTOTAL                           Rp      775,000   │
│                                                             │
│ 2. UANG JALAN (Trip Expenses)                              │
│    ├─ Toll Cipularang                    Rp       65,000   │
│    ├─ Parking DC Bandung                 Rp       20,000   │
│    ├─ Retribusi                          Rp       15,000   │
│    ├─ Uang makan driver                  Rp       50,000   │
│    └─ SUBTOTAL                           Rp      150,000   │
│                                                             │
│ 3. UANG JASA KENEK (Helper Compensation)                   │
│    ├─ Base fee                           Rp      100,000   │
│    ├─ Bonus per ton (15 ton × Rp 10k)    Rp      150,000   │
│    └─ SUBTOTAL                           Rp      250,000   │
│                                                             │
│ 4. BBM (Fuel - Managed by Fuel Operator)                   │
│    ├─ Distance: 150 km (one way)                           │
│    ├─ Consumption: 150 ÷ 4 = 37.5 liter                    │
│    ├─ Price: Rp 9,500/liter                                │
│    └─ SUBTOTAL                           Rp      356,250   │
│                                                             │
│ 5. BIAYA LAIN                                              │
│    ├─ Loading (paid by client)           Rp            0   │
│    ├─ Unloading (paid by client)         Rp            0   │
│    ├─ Insurance                          Rp       15,000   │
│    └─ SUBTOTAL                           Rp       15,000   │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│ TOTAL COST                               Rp    1,546,250   │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ MARGIN ANALYSIS                                             │
├─────────────────────────────────────────────────────────────┤
│ Revenue                                  Rp    1,020,000    │
│ Cost                                     Rp    1,546,250    │
│ MARGIN                                   Rp     (526,250)   │
│ MARGIN %                                        -51.59%     │
│                                                             │
│ STATUS: LOSS ❌                                             │
│ NOTE: Perlu BACKHAUL untuk profitable!                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. SETTLEMENT WORKFLOW

### 4.1 Flow Sebelum Trip (Cash Advance)

```
┌─────────────────────────────────────────────────────┐
│ DISPATCHER CREATE DISPATCH                          │
│ - Assign driver, truck, route                       │
│ - System calculate dari tariff:                     │
│   * Uang jasa driver                                │
│   * Uang jalan                                      │
│   * Uang jasa kenek                                 │
│   * BBM estimation                                  │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ FINANCE PREPARE CASH ADVANCE                        │
│ - Kasir siapkan UANG JALAN (cash)                   │
│   Contoh: Rp 85,000 (TANPA toll, pakai e-toll) ⭐   │
│ - Kasir catat: "Advanced to Driver DRV001"          │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ E-TOLL OPERATOR PREPARE E-TOLL CARD ⭐ NEW          │
│ - E-toll operator cek saldo e-toll card driver      │
│ - Jika saldo < Rp 50k: Auto top-up Rp 500k          │
│ - Estimasi toll: Rp 65k untuk trip ini              │
│ - Saldo cukup: Driver siap berangkat                │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ FUEL OPERATOR PREPARE FUEL CARD/VOUCHER             │
│ - Fuel operator cek fuel card driver                │
│ - Atau berikan voucher BBM                          │
│ - Estimasi: 37.5 liter untuk trip ini               │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ DRIVER RECEIVE BEFORE DEPARTURE                     │
│ - Uang jalan: Rp 85,000 (CASH, tanpa toll) ⭐       │
│ - E-toll card: ETC001 (saldo Rp 750k) ⭐            │
│ - Fuel card: FCARD001                               │
│ - Driver sign acknowledgement                       │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
             🚚 TRIP START
```

### 4.2 Flow Selama Trip (Expenses Tracking)

```
🚚 DRIVER ON THE ROAD

├─ 06:30 - Isi BBM di SPBU Pertamina Cikupa
│  └─ Swipe fuel card: 40.5 liter, Rp 384,750
│     (Auto recorded oleh sistem fuel operator)
│
├─ 06:45 - Lewat Gerbang Tol Cipularang ⭐ NEW
│  └─ Tap e-toll card: Rp 65,000 (auto-deduct)
│     (Auto recorded oleh e-toll system)
│     Saldo: Rp 750k → Rp 685k
│
├─ 10:30 - Sampai DC Bandung
│  └─ Parkir: Rp 20,000 (dari uang jalan)
│     Driver simpan nota
│
├─ 11:00 - Bongkar muatan
│  └─ Loading cost paid by client (free)
│
└─ 12:00 - Makan siang driver
   └─ Rp 50,000 (dari uang jalan)
      Driver simpan nota (optional)
```

### 4.3 Flow Setelah Trip (Settlement)

```
┌─────────────────────────────────────────────────────┐
│ DRIVER RETURN & SUBMIT ACCOUNTABILITY               │
│ - Submit semua nota/kwitansi:                       │
│   * Parkir: Rp 20,000 ✅                            │
│   * Retribusi: Rp 15,000 ✅                         │
│   * Makan: Rp 50,000 (no receipt - lump sum)        │
│   * Toll: TIDAK PERLU (pakai e-toll card) ⭐        │
│ - Total actual expenses: Rp 85,000                  │
│ - Sisa uang jalan: Rp 0 (pas)                       │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ SUPERVISOR VERIFY UANG JALAN                        │
│ - Cek semua nota                                    │
│ - Input ke system: ms_dispatch_uang_jalan_detail    │
│ - Approve jika sesuai                               │
│ - Jika ada sisa -> driver kembalikan                │
│ - Jika kurang -> company bayar kekurangan           │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ E-TOLL OPERATOR VERIFY TOLL TRANSACTION ⭐ NEW      │
│ - Cek data dari e-toll card system                  │
│ - Input ke: ms_etoll_transaction                    │
│ - Actual: Rp 65,000 vs Estimated: Rp 65,000         │
│ - Variance: Rp 0 (sesuai estimasi) ✅               │
│ - Update saldo e-toll card: Rp 685k                 │
│ - Note: "Toll payment untuk dispatch DSP001"        │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ FUEL OPERATOR VERIFY BBM CONSUMPTION                │
│ - Cek data dari fuel card system                    │
│ - Input ke: ms_fuel_consumption                     │
│ - Actual: 40.5 liter vs Estimated: 37.5 liter       │
│ - Variance: -3 liter (BOROS 8%)                     │
│ - Note: "Sedikit boros, masih dalam toleransi"      │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│ FINANCE PROCESS SETTLEMENT (Weekly/Bi-weekly)       │
│                                                     │
│ Settlement Period: 1-15 Nov 2025                    │
│ Driver: DRV001 (Budi Santoso)                       │
│ Total trips: 10 trips                               │
│                                                     │
│ INCOME:                                             │
│ - Uang jasa base: Rp 3,000,000                      │
│ - Uang jasa bonus: Rp 850,000                       │
│ - TOTAL: Rp 3,850,000                               │
│                                                     │
│ UANG JALAN RECONCILIATION:                          │
│ - Advanced: Rp 1,000,000                            │
│ - Actual: Rp 980,000                                │
│ - Sisa (driver kembalikan): Rp 20,000               │
│                                                     │
│ DEDUCTIONS:                                         │
│ - Kasbon: Rp 100,000                                │
│ - Other: Rp 50,000                                  │
│ - TOTAL: Rp 150,000                                 │
│                                                     │
│ NET PAYMENT:                                        │
│ Rp 3,850,000 - Rp 20,000 - Rp 150,000               │
│ = Rp 3,680,000                                      │
│                                                     │
│ Transfer to: Bank Mandiri 1234567890                │
│ Payment date: 17 Nov 2025                           │
└─────────────────────────────────────────────────────┘
```

---

## 5. REPORT EXAMPLES

### 5.1 Trip Cost Breakdown Report
```sql
SELECT 
    d.dispatch_number,
    d.actual_departure,
    dr.nama as driver_name,
    v.no_polisi,
    r.route_name,
    c.client_name,
    
    -- Revenue
    d.revenue_amount,
    
    -- Costs
    d.cost_uang_jasa_driver_total as driver_fee,
    d.cost_uang_jalan_actual as trip_expenses,
    d.cost_uang_jasa_kenek as helper_fee,
    d.bbm_actual_cost as fuel_cost,
    (d.cost_loading + d.cost_unloading + d.cost_permit + 
     d.cost_escort + d.cost_insurance + d.cost_temperature_control + 
     d.cost_detention + d.cost_other) as other_costs,
    
    d.total_cost,
    d.margin,
    (d.margin / NULLIF(d.revenue_amount, 0) * 100) as margin_percentage
    
FROM ms_dispatch d
JOIN ms_tms_driver dr ON d.driver_id = dr.ms_tms_driver_id
JOIN ms_vehicle v ON d.vehicle_id = v.id
JOIN ms_route r ON d.route_id = r.route_id
JOIN ms_client c ON d.client_id = c.client_id
WHERE d.status = 'COMPLETED'
  AND d.actual_departure BETWEEN '2025-11-01' AND '2025-11-30'
ORDER BY d.actual_departure DESC;
```

### 5.2 BBM Efficiency Report (Managed by Fuel Operator)
```sql
SELECT 
    v.no_polisi,
    tt.truck_type_name,
    dr.nama as driver_name,
    
    COUNT(fc.fuel_consumption_id) as total_refuels,
    SUM(fc.liter) as total_liter,
    SUM(fc.total_cost) as total_bbm_cost,
    SUM(fc.distance_km) as total_km,
    
    -- Average efficiency
    AVG(fc.fuel_efficiency) as avg_km_per_liter,
    
    -- Cost per km
    SUM(fc.total_cost) / NULLIF(SUM(fc.distance_km), 0) as cost_per_km
    
FROM ms_fuel_consumption fc
JOIN ms_vehicle v ON fc.vehicle_id = v.id
JOIN ms_truck_type tt ON v.truck_type_id = tt.truck_type_id
JOIN ms_tms_driver dr ON fc.driver_id = dr.ms_tms_driver_id
WHERE fc.refuel_date BETWEEN '2025-11-01' AND '2025-11-30'
  AND fc.is_verified = 1
GROUP BY v.no_polisi, tt.truck_type_name, dr.nama
ORDER BY avg_km_per_liter DESC;
```

### 5.3 Uang Jalan Variance Report
```sql
SELECT 
    d.dispatch_number,
    dr.nama as driver_name,
    r.route_name,
    d.actual_departure,
    
    d.cost_uang_jalan_advanced as advanced,
    d.cost_uang_jalan_actual as actual,
    d.cost_uang_jalan_variance as variance,
    
    CASE 
        WHEN d.cost_uang_jalan_variance > 0 THEN 'SISA (Driver kembalikan)'
        WHEN d.cost_uang_jalan_variance < 0 THEN 'KURANG (Company bayar)'
        ELSE 'PAS'
    END as status,
    
    -- Breakdown
    (SELECT STRING_AGG(expense_type + ': Rp ' + CAST(actual_amount AS VARCHAR), ', ')
     FROM ms_dispatch_uang_jalan_detail 
     WHERE dispatch_id = d.dispatch_id) as breakdown
    
FROM ms_dispatch d
JOIN ms_tms_driver dr ON d.driver_id = dr.ms_tms_driver_id
JOIN ms_route r ON d.route_id = r.route_id
WHERE d.status = 'COMPLETED'
  AND d.actual_departure BETWEEN '2025-11-01' AND '2025-11-30'
  AND ABS(d.cost_uang_jalan_variance) > 0
ORDER BY ABS(d.cost_uang_jalan_variance) DESC;
```

### 5.4 Driver Settlement Summary
```sql
SELECT 
    sd.settlement_number,
    dr.nama as driver_name,
    sd.settlement_period_start,
    sd.settlement_period_end,
    sd.total_trips,
    
    -- Income
    sd.total_uang_jasa_driver as gross_income,
    
    -- Uang jalan reconciliation
    sd.total_uang_jalan_variance as uang_jalan_adj,
    
    -- Deductions
    sd.total_deduction,
    
    -- Net payment
    sd.net_payment,
    
    sd.payment_method,
    sd.payment_date,
    sd.status
    
FROM ms_settlement_driver sd
JOIN ms_tms_driver dr ON sd.driver_id = dr.ms_tms_driver_id
WHERE sd.settlement_period_start >= '2025-11-01'
ORDER BY sd.settlement_period_start DESC;
```

---

## 6. SUMMARY

### ✅ Cost Components yang BENAR:

1. **UANG JASA DRIVER** (Driver Compensation)
   - Base fee per trip
   - Bonus (per km, per ton, on-time, full load)
   - Dibayar saat settlement (weekly/bi-weekly)
   - Diterima oleh: DRIVER

2. **UANG JALAN** (Trip Expenses)
   - Cash advance SEBELUM trip
   - Untuk: parkir, retribusi, makan (TANPA toll karena pakai e-toll) ⭐
   - Harus dipertanggungjawabkan dengan NOTA
   - Sisa dikembalikan, kurang dibayar company
   - Tracking: ms_dispatch_uang_jalan_detail

3. **E-TOLL** (Managed by E-Toll Operator) ⭐ NEW
   - E-toll card (Brizzi, Flazz, e-Toll, TapCash)
   - Auto-deduct saat tap di gerbang tol
   - Auto top-up jika saldo < minimum (Rp 50k)
   - Tracking: ms_etoll_transaction
   - Monitoring saldo & usage per card
   - Diterima oleh: TIDAK ADA (auto-deduct dari card)

4. **UANG JASA KENEK** (Helper Compensation)
   - Base fee per trip
   - Bonus (per ton)
   - Dibayar saat settlement
   - Diterima oleh: KENEK

5. **BBM (Fuel)** ⭐ SPECIAL
   - BUKAN diberikan ke driver dalam bentuk uang
   - Dikelola oleh: OPERATOR BBM
   - Sistem: Fuel card / Voucher / Reimbursement nota
   - Tracking: ms_fuel_consumption
   - Variance monitoring (boros/irit)

6. **BIAYA LAIN-LAIN**
   - Loading/unloading (tergantung siapa yang bayar)
   - Permit, escort, insurance, temperature control
   - Detention charge

### ✅ Settlement Flow:
1. Sebelum trip: Driver terima uang jalan (Rp 85k, tanpa toll) + e-toll card (saldo cukup) + fuel card ⭐
2. Selama trip: Driver pakai uang jalan (parkir, makan) + tap e-toll + swipe fuel card, simpan nota ⭐
3. Setelah trip: Driver submit accountability (nota untuk parkir, makan)
4. Supervisor verify uang jalan expenses
5. E-toll operator verify toll transactions (auto-recorded) ⭐
6. Fuel operator verify BBM consumption
7. Finance process settlement (weekly/bi-weekly)
8. Driver terima net payment (uang jasa - deduction ± uang jalan variance)

**Apakah struktur cost & settlement dengan E-TOLL ini sudah sesuai dengan business process Anda?** 💰🚛💳

