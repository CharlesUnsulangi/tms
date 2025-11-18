# TMS - SHUTTLE DISPATCHER & DOCUMENT FLOW

**Created:** 18 November 2025  
**Purpose:** Detail table khusus untuk shuttle operation dengan document tracking (CO, DN, BTB)  
**Status:** Design Phase

---

## KONSEP: SHUTTLE DOCUMENT WORKFLOW

### Document Flow untuk Shuttle Operation:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SHUTTLE DOCUMENT LIFECYCLE                               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  JUMAT (Planning Phase)                                                     │
│  └─ Customer (AQUA) kirim CO Planning Excel                                 │
│     └─ CO berisi: SO Number, Route, Qty, Window, Pre-assigned truck/driver │
│                                                                             │
│  SABTU-MINGGU (Preparation)                                                 │
│  └─ TMS create Dispatch Order                                               │
│     └─ Generate SPJ (Surat Perintah Jalan)                                  │
│                                                                             │
│  SENIN PAGI (Before Departure) ⭐ SERAH TERIMA DOKUMEN                      │
│  ├─ Driver datang ke Tower/Pool                                             │
│  ├─ Dispatcher serahkan dokumen:                                            │
│  │  ├─ SPJ (Surat Perintah Jalan)                                          │
│  │  ├─ CO Customer (dari AQUA)                                             │
│  │  └─ Surat jalan kosong (untuk diisi)                                    │
│  ├─ Driver tanda tangan TERIMA dokumen                                      │
│  ├─ Catat waktu serah terima                                                │
│  └─ Status: DOKUMEN_DISERAHKAN                                              │
│                                                                             │
│  SENIN SIANG (At Origin - Loading) ⭐ DN (DELIVERY NOTE)                    │
│  ├─ Driver tiba di Plant Danone/AQUA                                        │
│  ├─ Tunjukkan SPJ + CO                                                      │
│  ├─ Loading barang                                                          │
│  ├─ Danone Staff issue DN (Delivery Note)                                   │
│  │  ├─ DN Number                                                            │
│  │  ├─ Qty actual loaded                                                    │
│  │  ├─ Batch number                                                         │
│  │  └─ Tanda tangan Danone staff                                            │
│  ├─ Driver foto DN                                                          │
│  └─ Status: DN_RECEIVED (barang sudah di-load)                              │
│                                                                             │
│  SENIN SORE (At Destination - Unloading) ⭐ BTB (BERITA TERIMA BARANG)     │
│  ├─ Driver tiba di DC/Customer                                              │
│  ├─ Tunjukkan DN dari Danone                                                │
│  ├─ Unloading barang                                                        │
│  ├─ Customer Staff verifikasi:                                              │
│  │  ├─ Qty sesuai DN?                                                       │
│  │  ├─ Kondisi barang OK?                                                   │
│  │  ├─ Batch number match?                                                  │
│  │  └─ Issue BTB (Berita Terima Barang)                                     │
│  ├─ BTB = Proof of Delivery (POD)                                           │
│  ├─ Driver foto BTB                                                         │
│  ├─ Customer tanda tangan di BTB                                            │
│  └─ Status: BTB_RECEIVED (barang sudah diterima customer)                   │
│                                                                             │
│  SENIN MALAM (Return to Pool) ⭐ RETURN DOKUMEN                             │
│  ├─ Driver kembali ke Tower/Pool                                            │
│  ├─ Serahkan dokumen ke Dispatcher:                                         │
│  │  ├─ DN original dari Danone (WAJIB)                                     │
│  │  ├─ BTB original dari Customer (WAJIB)                                  │
│  │  ├─ SPJ yang sudah dilengkapi                                            │
│  │  └─ Kwitansi BBM/toll (untuk reconcile uang jalan)                      │
│  ├─ Dispatcher verifikasi kelengkapan dokumen                               │
│  ├─ Dispatcher tanda tangan TERIMA dokumen                                  │
│  ├─ Catat waktu return dokumen                                              │
│  └─ Status: DOKUMEN_DIKEMBALIKAN                                            │
│                                                                             │
│  SELASA (Administration)                                                    │
│  ├─ Admin input data dari DN & BTB ke system                                │
│  ├─ Scan semua dokumen                                                      │
│  ├─ Archive fisik dokumen                                                   │
│  └─ Status: DOKUMEN_DIARSIP                                                 │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## TABLE STRUCTURE: tr_tms_dispatcher_shuttle

### Purpose: 
Detail table khusus untuk shuttle operation (one-to-one dengan tr_tms_dispatcher_main)

### Relationship:
```
tr_tms_dispatcher_main (1) ←→ (1) tr_tms_dispatcher_shuttle
```

```sql
CREATE TABLE tr_tms_dispatcher_shuttle (
    -- Primary Key (same as dispatch_id for 1-to-1)
    shuttle_id VARCHAR(20) PRIMARY KEY,
    dispatch_id VARCHAR(20) UNIQUE NOT NULL,  -- 1-to-1 relationship
    
    -- ============================================================
    -- SECTION 1: CO (CUSTOMER ORDER) INFORMATION
    -- ============================================================
    
    -- CO dari Customer (AQUA kirim setiap Jumat)
    co_number VARCHAR(50),  -- CO number dari customer
    co_date DATE,  -- Tanggal CO diterima
    co_received_by VARCHAR(20),  -- Staff yang terima CO
    co_file_path VARCHAR(500),  -- Path to CO PDF/Excel file
    
    -- Planning Reference (dari CO Planning yang di-import)
    planning_id VARCHAR(20),  -- Link to ms_planning_weekly
    planning_line_number INT,  -- Line number dalam CO Planning Excel
    
    -- Customer Order Details (from CO)
    customer_so_number VARCHAR(50),  -- SO number dari customer
    customer_po_number VARCHAR(50),  -- PO number dari customer
    customer_shipment_ref VARCHAR(50),  -- Shipment reference
    
    -- Route dari CO Planning
    route_id_planned VARCHAR(20),  -- Route sesuai CO
    route_id_actual VARCHAR(20),  -- Route actual (jika berubah)
    route_changed BIT DEFAULT 0,
    route_change_reason NVARCHAR(500),
    
    -- Truck & Driver Assignment (dari Planning)
    truck_id_planned VARCHAR(20),  -- Truck yang di-assign di CO Planning
    driver_id_planned VARCHAR(20),  -- Driver yang di-assign di CO Planning
    
    truck_id_actual VARCHAR(20),  -- Truck actual (jika berubah)
    driver_id_actual VARCHAR(20),  -- Driver actual (jika berubah)
    
    assignment_changed BIT DEFAULT 0,
    assignment_change_reason NVARCHAR(500),
    assignment_changed_by VARCHAR(20),
    assignment_changed_at DATETIME,
    
    -- ============================================================
    -- SECTION 2: SERAH TERIMA DOKUMEN (DEPARTURE)
    -- ============================================================
    
    -- Dokumen Diserahkan ke Driver (Senin Pagi di Tower/Pool)
    document_handover_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → READY → HANDED_OVER → CONFIRMED
    
    -- Waktu & Tempat Serah Terima
    handover_datetime DATETIME,  -- Waktu serah terima dokumen
    handover_location VARCHAR(100),  -- Tower/Pool location
    handover_by VARCHAR(20),  -- Dispatcher yang serahkan
    handover_by_name NVARCHAR(200),
    
    -- Dokumen yang Diserahkan
    doc_spj_given BIT DEFAULT 0,  -- SPJ diserahkan?
    doc_spj_number VARCHAR(50),  -- SPJ number
    
    doc_co_given BIT DEFAULT 0,  -- CO customer diserahkan?
    doc_surat_jalan_given BIT DEFAULT 0,  -- Surat jalan kosong diserahkan?
    
    other_documents_given NVARCHAR(500),  -- Dokumen lain yang diserahkan
    
    -- Driver Acknowledgement (Tanda Terima)
    driver_received_documents BIT DEFAULT 0,
    driver_signature_handover VARCHAR(500),  -- Path to signature image
    driver_received_datetime DATETIME,
    driver_notes_handover NVARCHAR(500),  -- Catatan driver saat terima dokumen
    
    -- Checklist Kelengkapan (sebelum berangkat)
    checklist_spj_complete BIT DEFAULT 0,
    checklist_co_complete BIT DEFAULT 0,
    checklist_vehicle_ok BIT DEFAULT 0,
    checklist_driver_ready BIT DEFAULT 0,
    checklist_fuel_sufficient BIT DEFAULT 0,
    
    checklist_verified_by VARCHAR(20),
    checklist_verified_at DATETIME,
    
    -- ============================================================
    -- SECTION 3: DN (DELIVERY NOTE) - AT ORIGIN/PLANT
    -- ============================================================
    
    -- DN dari Danone/AQUA (saat loading di plant)
    dn_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → AT_ORIGIN → DN_ISSUED → DN_PHOTO_UPLOADED → DN_VERIFIED
    
    -- DN Information
    dn_number VARCHAR(50),  -- DN number dari Danone
    dn_date DATE,  -- Tanggal DN
    dn_time TIME,  -- Jam DN issued
    
    -- Loading Details (dari DN)
    dn_plant_location_id VARCHAR(20),  -- Plant Danone/AQUA
    dn_plant_location_name NVARCHAR(200),
    
    dn_product_description NVARCHAR(500),  -- e.g., "AQUA 600ml"
    dn_qty_loaded DECIMAL(10,2),  -- Qty actual loaded (dari DN)
    dn_qty_planned DECIMAL(10,2),  -- Qty planned (dari CO)
    dn_qty_variance DECIMAL(10,2),  -- Variance (loaded - planned)
    dn_uom VARCHAR(10),  -- CTN, KG, PCS
    
    dn_batch_number VARCHAR(50),  -- Batch/lot number
    dn_production_date DATE,  -- Production date
    dn_expiry_date DATE,  -- Expiry date
    
    -- DN Issuer (Danone Staff)
    dn_issued_by_name NVARCHAR(200),  -- Nama staff Danone
    dn_issued_by_position VARCHAR(100),  -- Jabatan
    dn_issued_by_signature VARCHAR(500),  -- Path to signature (dari DN scan)
    
    -- Loading Timestamps
    loading_start_time DATETIME,  -- Mulai loading
    loading_end_time DATETIME,  -- Selesai loading
    loading_duration_minutes INT,  -- Duration
    
    -- DN Document Upload
    dn_photo_path VARCHAR(500),  -- Path to DN photo (driver upload)
    dn_photo_uploaded_at DATETIME,
    dn_photo_uploaded_by VARCHAR(20),  -- Driver ID
    
    dn_scan_path VARCHAR(500),  -- Path to DN scan (admin)
    dn_scan_uploaded_at DATETIME,
    dn_scan_uploaded_by VARCHAR(20),  -- Admin ID
    
    -- DN Verification
    dn_verified BIT DEFAULT 0,
    dn_verified_at DATETIME,
    dn_verified_by VARCHAR(20),  -- Supervisor/Admin
    dn_verification_notes NVARCHAR(500),
    
    -- Issues at Loading
    dn_has_discrepancy BIT DEFAULT 0,
    dn_discrepancy_description NVARCHAR(1000),
    dn_discrepancy_resolved BIT DEFAULT 0,
    
    -- ============================================================
    -- SECTION 4: BTB (BERITA TERIMA BARANG) - AT DESTINATION
    -- ============================================================
    
    -- BTB dari Customer/DC (saat unloading di tujuan)
    btb_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → AT_DESTINATION → BTB_ISSUED → BTB_PHOTO_UPLOADED → BTB_VERIFIED
    
    -- BTB Information
    btb_number VARCHAR(50),  -- BTB number dari customer
    btb_date DATE,  -- Tanggal BTB
    btb_time TIME,  -- Jam BTB issued
    
    -- Unloading Details (dari BTB)
    btb_destination_location_id VARCHAR(20),  -- DC/Customer location
    btb_destination_location_name NVARCHAR(200),
    
    btb_product_description NVARCHAR(500),
    btb_qty_delivered DECIMAL(10,2),  -- Qty actual delivered (dari BTB)
    btb_qty_loaded DECIMAL(10,2),  -- Qty loaded (dari DN)
    btb_qty_variance DECIMAL(10,2),  -- Variance (delivered - loaded)
    btb_qty_damaged DECIMAL(10,2),  -- Qty rusak/reject
    btb_qty_shortage DECIMAL(10,2),  -- Qty kurang
    btb_uom VARCHAR(10),
    
    -- Condition Check
    btb_goods_condition VARCHAR(20),  -- GOOD, DAMAGED, PARTIAL_DAMAGED
    btb_packaging_condition VARCHAR(20),  -- GOOD, DAMAGED
    btb_temperature_ok BIT DEFAULT 1,  -- For cold chain (jika applicable)
    btb_seal_intact BIT DEFAULT 1,  -- Seal masih utuh?
    
    -- BTB Receiver (Customer Staff)
    btb_receiver_name NVARCHAR(200),  -- Nama penerima barang
    btb_receiver_position VARCHAR(100),  -- Jabatan
    btb_receiver_id_number VARCHAR(50),  -- NIK/ID penerima
    btb_receiver_phone VARCHAR(20),
    btb_receiver_signature VARCHAR(500),  -- Path to signature
    
    -- Unloading Timestamps
    unloading_start_time DATETIME,
    unloading_end_time DATETIME,
    unloading_duration_minutes INT,
    
    -- Waiting Time (jika ada antrian)
    waiting_time_minutes INT,
    waiting_time_reason NVARCHAR(500),
    waiting_time_charge DECIMAL(15,2),  -- Charge for waiting time
    
    -- BTB Document Upload
    btb_photo_path VARCHAR(500),  -- Path to BTB photo (driver upload)
    btb_photo_uploaded_at DATETIME,
    btb_photo_uploaded_by VARCHAR(20),
    
    btb_scan_path VARCHAR(500),  -- Path to BTB scan (admin)
    btb_scan_uploaded_at DATETIME,
    btb_scan_uploaded_by VARCHAR(20),
    
    -- BTB Verification
    btb_verified BIT DEFAULT 0,
    btb_verified_at DATETIME,
    btb_verified_by VARCHAR(20),
    btb_verification_notes NVARCHAR(500),
    
    -- Issues at Delivery
    btb_has_issues BIT DEFAULT 0,
    btb_issue_type VARCHAR(50),  -- DAMAGE, SHORTAGE, DELAY, REJECTION
    btb_issue_description NVARCHAR(1000),
    btb_issue_photo_path VARCHAR(500),  -- Photo bukti issue
    btb_issue_resolved BIT DEFAULT 0,
    btb_issue_resolution NVARCHAR(1000),
    
    -- Rejection (jika barang ditolak)
    btb_rejected BIT DEFAULT 0,
    btb_rejection_reason NVARCHAR(1000),
    btb_rejection_qty DECIMAL(10,2),
    btb_rejection_action VARCHAR(50),  -- RETURN, DISPOSE, REWORK
    
    -- ============================================================
    -- SECTION 5: RETURN DOKUMEN (KEMBALI KE POOL)
    -- ============================================================
    
    -- Return Documents ke Dispatcher (Senin Malam)
    document_return_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → ON_THE_WAY → RETURNED → VERIFIED → ARCHIVED
    
    -- Waktu & Tempat Return
    return_datetime DATETIME,  -- Waktu return dokumen
    return_location VARCHAR(100),  -- Tower/Pool
    return_to VARCHAR(20),  -- Dispatcher yang terima
    return_to_name NVARCHAR(200),
    
    -- Dokumen yang Dikembalikan (CHECKLIST)
    doc_dn_returned BIT DEFAULT 0,  -- ⭐ DN original returned? (WAJIB)
    doc_dn_return_condition VARCHAR(20),  -- ORIGINAL, COPY, DAMAGED
    
    doc_btb_returned BIT DEFAULT 0,  -- ⭐ BTB original returned? (WAJIB)
    doc_btb_return_condition VARCHAR(20),
    
    doc_spj_returned BIT DEFAULT 0,  -- SPJ returned?
    doc_receipt_fuel_returned BIT DEFAULT 0,  -- Kwitansi BBM?
    doc_receipt_toll_returned BIT DEFAULT 0,  -- Kwitansi toll?
    doc_receipt_parking_returned BIT DEFAULT 0,  -- Kwitansi parkir?
    
    other_documents_returned NVARCHAR(500),
    
    -- Kelengkapan Dokumen (Verification)
    documents_complete BIT DEFAULT 0,  -- Semua dokumen lengkap?
    documents_incomplete_reason NVARCHAR(500),  -- Alasan jika tidak lengkap
    
    missing_documents NVARCHAR(500),  -- Dokumen yang hilang
    missing_document_penalty DECIMAL(15,2),  -- Penalty jika hilang
    
    -- Dispatcher Acknowledgement
    dispatcher_received_documents BIT DEFAULT 0,
    dispatcher_signature_return VARCHAR(500),
    dispatcher_received_datetime DATETIME,
    dispatcher_notes_return NVARCHAR(500),
    
    -- ============================================================
    -- SECTION 6: ADMINISTRATION & ARCHIVING
    -- ============================================================
    
    -- Admin Input Data (dari DN & BTB fisik)
    admin_data_entry_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → IN_PROGRESS → COMPLETED → VERIFIED
    
    admin_data_entered_by VARCHAR(20),
    admin_data_entered_at DATETIME,
    
    -- Scanning & Archiving
    all_documents_scanned BIT DEFAULT 0,
    documents_scanned_at DATETIME,
    documents_scanned_by VARCHAR(20),
    
    physical_archive_location VARCHAR(100),  -- e.g., "BOX-2025-11-W3-001"
    physical_archived_at DATETIME,
    physical_archived_by VARCHAR(20),
    
    digital_archive_folder VARCHAR(500),  -- Path to digital archive folder
    
    -- Document Retention
    retention_period_months INT DEFAULT 24,  -- Keep for 2 years
    scheduled_disposal_date DATE,  -- Scheduled for disposal
    
    -- ============================================================
    -- SECTION 7: COMPLIANCE & AUDIT
    -- ============================================================
    
    -- Compliance Checks
    co_dn_match BIT DEFAULT 0,  -- CO qty = DN qty?
    dn_btb_match BIT DEFAULT 0,  -- DN qty = BTB qty?
    co_dn_btb_all_match BIT DEFAULT 0,  -- All match?
    
    -- Variances
    total_variance_qty DECIMAL(10,2),  -- Total variance across chain
    variance_within_tolerance BIT DEFAULT 1,  -- Within acceptable range?
    variance_explanation NVARCHAR(1000),
    
    -- Document Completeness Score
    document_completeness_score DECIMAL(5,2),  -- 0-100%
    -- Score based on:
    -- - All docs returned?
    -- - All docs on time?
    -- - All docs in good condition?
    -- - All data verified?
    
    -- Audit Trail
    audit_status VARCHAR(20) DEFAULT 'PENDING',
    -- Status: PENDING → AUDITED → APPROVED → CLOSED
    
    audited_by VARCHAR(20),
    audited_at DATETIME,
    audit_findings NVARCHAR(1000),
    audit_approved BIT DEFAULT 0,
    
    -- ============================================================
    -- SECTION 8: PERFORMANCE METRICS
    -- ============================================================
    
    -- Document Processing Time
    co_to_dispatch_hours DECIMAL(5,2),  -- CO received → Dispatch created
    dispatch_to_handover_hours DECIMAL(5,2),  -- Dispatch → Doc handover
    handover_to_dn_hours DECIMAL(5,2),  -- Handover → DN received
    dn_to_btb_hours DECIMAL(5,2),  -- DN → BTB received
    btb_to_return_hours DECIMAL(5,2),  -- BTB → Doc returned
    return_to_archive_hours DECIMAL(5,2),  -- Return → Archived
    
    total_cycle_time_hours DECIMAL(5,2),  -- End-to-end time
    
    -- On-time Performance
    handover_on_time BIT DEFAULT 1,  -- Doc handover on schedule?
    dn_on_time BIT DEFAULT 1,  -- DN received on schedule?
    btb_on_time BIT DEFAULT 1,  -- BTB received on schedule?
    return_on_time BIT DEFAULT 1,  -- Doc return on schedule?
    
    overall_on_time BIT DEFAULT 1,  -- All on time?
    
    -- Driver Performance (based on document handling)
    driver_document_handling_score DECIMAL(5,2),  -- 0-100
    -- Based on:
    -- - Documents returned complete?
    -- - Documents in good condition?
    -- - Returned on time?
    -- - No lost documents?
    
    -- ============================================================
    -- SECTION 9: NOTES & SPECIAL HANDLING
    -- ============================================================
    
    special_instructions NVARCHAR(1000),  -- Special handling dari customer
    cold_chain_required BIT DEFAULT 0,  -- Requires temperature control?
    fragile_goods BIT DEFAULT 0,  -- Barang mudah pecah?
    hazmat BIT DEFAULT 0,  -- Hazardous materials?
    
    dispatcher_notes NVARCHAR(1000),
    driver_notes NVARCHAR(1000),
    admin_notes NVARCHAR(1000),
    
    -- ============================================================
    -- SECTION 10: AUDIT & TIMESTAMPS
    -- ============================================================
    
    created_at DATETIME DEFAULT GETDATE(),
    created_by VARCHAR(20),
    updated_at DATETIME,
    updated_by VARCHAR(20),
    
    -- FOREIGN KEYS
    FOREIGN KEY (dispatch_id) REFERENCES tr_tms_dispatcher_main(dispatch_id),
    FOREIGN KEY (planning_id) REFERENCES ms_planning_weekly(planning_id),
    FOREIGN KEY (route_id_planned) REFERENCES ms_route(route_id),
    FOREIGN KEY (route_id_actual) REFERENCES ms_route(route_id),
    FOREIGN KEY (truck_id_planned) REFERENCES ms_vehicle(vehicle_id),
    FOREIGN KEY (truck_id_actual) REFERENCES ms_vehicle(vehicle_id),
    FOREIGN KEY (driver_id_planned) REFERENCES ms_driver(driver_id),
    FOREIGN KEY (driver_id_actual) REFERENCES ms_driver(driver_id),
    FOREIGN KEY (dn_plant_location_id) REFERENCES ms_location(location_id),
    FOREIGN KEY (btb_destination_location_id) REFERENCES ms_location(location_id)
);

-- INDEXES
CREATE INDEX idx_shuttle_dispatch ON tr_tms_dispatcher_shuttle(dispatch_id);
CREATE INDEX idx_shuttle_planning ON tr_tms_dispatcher_shuttle(planning_id);
CREATE INDEX idx_shuttle_co_number ON tr_tms_dispatcher_shuttle(co_number);
CREATE INDEX idx_shuttle_dn_number ON tr_tms_dispatcher_shuttle(dn_number);
CREATE INDEX idx_shuttle_btb_number ON tr_tms_dispatcher_shuttle(btb_number);
CREATE INDEX idx_shuttle_driver ON tr_tms_dispatcher_shuttle(driver_id_actual, handover_datetime);
CREATE INDEX idx_shuttle_status ON tr_tms_dispatcher_shuttle(document_handover_status, dn_status, btb_status);
CREATE INDEX idx_shuttle_dates ON tr_tms_dispatcher_shuttle(handover_datetime, return_datetime);

-- UNIQUE constraint
CREATE UNIQUE INDEX idx_shuttle_dispatch_unique ON tr_tms_dispatcher_shuttle(dispatch_id);
```

---

## SAMPLE DATA FLOW: COMPLETE SHUTTLE CYCLE

```sql
-- ============================================================
-- STEP 1: CREATE SHUTTLE RECORD (linked to Dispatch)
-- ============================================================

INSERT INTO tr_tms_dispatcher_shuttle (
    shuttle_id, dispatch_id,
    co_number, co_date, planning_id,
    customer_so_number, customer_shipment_ref,
    route_id_planned, truck_id_planned, driver_id_planned,
    truck_id_actual, driver_id_actual,
    created_by
) VALUES (
    'SHT001', 'DSP001',
    'CO-AQUA-2025-W47-001', '2025-11-15', 'PLN001',
    'SO-25111400788', 'S25111400788',
    'ROU001', 'VEH001', 'DRV001',  -- Planned
    'VEH001', 'DRV001',  -- Actual (same as planned)
    'PLANNER01'
);

-- ============================================================
-- STEP 2: SERAH TERIMA DOKUMEN (Senin Pagi 05:00 di Tower)
-- ============================================================

UPDATE tr_tms_dispatcher_shuttle
SET 
    -- Status
    document_handover_status = 'HANDED_OVER',
    
    -- Handover details
    handover_datetime = '2025-11-18 05:00:00',
    handover_location = 'Tower Cikupa',
    handover_by = 'DISP001',
    handover_by_name = 'Pak Joko (Dispatcher)',
    
    -- Documents given
    doc_spj_given = 1,
    doc_spj_number = 'SPJ-001/TMS/XI/2025',
    doc_co_given = 1,
    doc_surat_jalan_given = 1,
    
    -- Driver received
    driver_received_documents = 1,
    driver_signature_handover = '/uploads/signatures/drv001_handover_20251118.jpg',
    driver_received_datetime = '2025-11-18 05:05:00',
    driver_notes_handover = 'Dokumen lengkap, siap berangkat',
    
    -- Checklist
    checklist_spj_complete = 1,
    checklist_co_complete = 1,
    checklist_vehicle_ok = 1,
    checklist_driver_ready = 1,
    checklist_fuel_sufficient = 1,
    checklist_verified_by = 'DISP001',
    checklist_verified_at = '2025-11-18 05:05:00',
    
    updated_by = 'DISP001',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- ============================================================
-- STEP 3: DN RECEIVED (Senin 07:30 di Plant Ciherang)
-- ============================================================

UPDATE tr_tms_dispatcher_shuttle
SET 
    -- Status
    dn_status = 'DN_ISSUED',
    
    -- DN details
    dn_number = 'DN-CHR-2025-11-18-12345',
    dn_date = '2025-11-18',
    dn_time = '07:30:00',
    
    -- Location
    dn_plant_location_id = 'LOC001',
    dn_plant_location_name = 'Plant Ciherang (9018)',
    
    -- Product & Qty
    dn_product_description = 'AQUA 600ml Carton',
    dn_qty_loaded = 2900,  -- Actual loaded
    dn_qty_planned = 2900,  -- From CO
    dn_qty_variance = 0,  -- No variance
    dn_uom = 'CTN',
    
    dn_batch_number = 'BATCH-2025-11-17-001',
    dn_production_date = '2025-11-17',
    dn_expiry_date = '2027-11-17',
    
    -- Issuer
    dn_issued_by_name = 'Budi Santoso (Plant Supervisor)',
    dn_issued_by_position = 'Warehouse Supervisor',
    
    -- Loading time
    loading_start_time = '2025-11-18 07:00:00',
    loading_end_time = '2025-11-18 07:30:00',
    loading_duration_minutes = 30,
    
    -- Photo upload (by driver via mobile app)
    dn_photo_path = '/uploads/dn/dn_sht001_20251118.jpg',
    dn_photo_uploaded_at = '2025-11-18 07:35:00',
    dn_photo_uploaded_by = 'DRV001',
    
    updated_by = 'DRV001',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- Verify DN (by supervisor)
UPDATE tr_tms_dispatcher_shuttle
SET 
    dn_status = 'DN_VERIFIED',
    dn_verified = 1,
    dn_verified_at = '2025-11-18 08:00:00',
    dn_verified_by = 'SUPER01',
    dn_verification_notes = 'DN verified, qty match with CO',
    
    updated_by = 'SUPER01',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- ============================================================
-- STEP 4: BTB RECEIVED (Senin 09:30 di DC Palapa)
-- ============================================================

UPDATE tr_tms_dispatcher_shuttle
SET 
    -- Status
    btb_status = 'BTB_ISSUED',
    
    -- BTB details
    btb_number = 'BTB-PAL-2025-11-18-567',
    btb_date = '2025-11-18',
    btb_time = '09:30:00',
    
    -- Location
    btb_destination_location_id = 'LOC005',
    btb_destination_location_name = 'DC Palapa (9025)',
    
    -- Product & Qty
    btb_product_description = 'AQUA 600ml Carton',
    btb_qty_delivered = 2900,  -- All delivered
    btb_qty_loaded = 2900,  -- From DN
    btb_qty_variance = 0,  -- No variance
    btb_qty_damaged = 0,  -- No damage
    btb_qty_shortage = 0,  -- No shortage
    btb_uom = 'CTN',
    
    -- Condition
    btb_goods_condition = 'GOOD',
    btb_packaging_condition = 'GOOD',
    btb_seal_intact = 1,
    
    -- Receiver
    btb_receiver_name = 'Siti Aminah',
    btb_receiver_position = 'Warehouse Coordinator',
    btb_receiver_id_number = '3201234567890123',
    btb_receiver_phone = '081234567890',
    
    -- Unloading time
    unloading_start_time = '2025-11-18 09:00:00',
    unloading_end_time = '2025-11-18 09:30:00',
    unloading_duration_minutes = 30,
    
    waiting_time_minutes = 15,  -- Ada antrian
    waiting_time_reason = 'Ada 2 truck di depan',
    
    -- Photo upload
    btb_photo_path = '/uploads/btb/btb_sht001_20251118.jpg',
    btb_photo_uploaded_at = '2025-11-18 09:35:00',
    btb_photo_uploaded_by = 'DRV001',
    
    updated_by = 'DRV001',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- Verify BTB
UPDATE tr_tms_dispatcher_shuttle
SET 
    btb_status = 'BTB_VERIFIED',
    btb_verified = 1,
    btb_verified_at = '2025-11-18 10:00:00',
    btb_verified_by = 'SUPER01',
    btb_verification_notes = 'BTB verified, all goods received in good condition',
    
    -- Compliance check
    co_dn_match = 1,  -- CO 2900 = DN 2900
    dn_btb_match = 1,  -- DN 2900 = BTB 2900
    co_dn_btb_all_match = 1,  -- All match!
    total_variance_qty = 0,
    variance_within_tolerance = 1,
    
    updated_by = 'SUPER01',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- ============================================================
-- STEP 5: RETURN DOKUMEN (Senin 18:00 kembali ke Tower)
-- ============================================================

UPDATE tr_tms_dispatcher_shuttle
SET 
    -- Status
    document_return_status = 'RETURNED',
    
    -- Return details
    return_datetime = '2025-11-18 18:00:00',
    return_location = 'Tower Cikupa',
    return_to = 'DISP001',
    return_to_name = 'Pak Joko (Dispatcher)',
    
    -- Documents returned (CHECKLIST)
    doc_dn_returned = 1,  -- ⭐ DN original (WAJIB)
    doc_dn_return_condition = 'ORIGINAL',
    
    doc_btb_returned = 1,  -- ⭐ BTB original (WAJIB)
    doc_btb_return_condition = 'ORIGINAL',
    
    doc_spj_returned = 1,
    doc_receipt_fuel_returned = 1,  -- Kwitansi BBM
    doc_receipt_toll_returned = 1,  -- Kwitansi toll
    doc_receipt_parking_returned = 1,  -- Kwitansi parkir
    
    -- Completeness
    documents_complete = 1,  -- ✅ All complete!
    
    -- Dispatcher acknowledgement
    dispatcher_received_documents = 1,
    dispatcher_signature_return = '/uploads/signatures/disp001_return_20251118.jpg',
    dispatcher_received_datetime = '2025-11-18 18:05:00',
    dispatcher_notes_return = 'Dokumen lengkap, kondisi baik',
    
    -- Performance
    btb_to_return_hours = 8.5,  -- BTB 09:30 → Return 18:00
    return_on_time = 1,
    
    updated_by = 'DISP001',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- ============================================================
-- STEP 6: ADMIN INPUT & ARCHIVING (Selasa)
-- ============================================================

-- Scan documents
UPDATE tr_tms_dispatcher_shuttle
SET 
    -- Scan DN & BTB original
    dn_scan_path = '/archives/2025/11/week3/dn_sht001.pdf',
    dn_scan_uploaded_at = '2025-11-19 09:00:00',
    dn_scan_uploaded_by = 'ADMIN01',
    
    btb_scan_path = '/archives/2025/11/week3/btb_sht001.pdf',
    btb_scan_uploaded_at = '2025-11-19 09:05:00',
    btb_scan_uploaded_by = 'ADMIN01',
    
    all_documents_scanned = 1,
    documents_scanned_at = '2025-11-19 09:10:00',
    documents_scanned_by = 'ADMIN01',
    
    updated_by = 'ADMIN01',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- Archive physical documents
UPDATE tr_tms_dispatcher_shuttle
SET 
    document_return_status = 'ARCHIVED',
    
    physical_archive_location = 'BOX-2025-11-W47-SHUTTLE-001',
    physical_archived_at = '2025-11-19 10:00:00',
    physical_archived_by = 'ADMIN01',
    
    digital_archive_folder = '/archives/2025/11/week47/shuttle/',
    
    retention_period_months = 24,
    scheduled_disposal_date = '2027-11-19',
    
    -- Calculate completeness score
    document_completeness_score = 100.00,  -- Perfect!
    
    -- Performance metrics
    co_to_dispatch_hours = 72,  -- Jumat → Senin
    dispatch_to_handover_hours = 1,  -- 04:00 → 05:00
    handover_to_dn_hours = 2.5,  -- 05:00 → 07:30
    dn_to_btb_hours = 2,  -- 07:30 → 09:30
    total_cycle_time_hours = 85,  -- Total
    
    handover_on_time = 1,
    dn_on_time = 1,
    btb_on_time = 1,
    return_on_time = 1,
    overall_on_time = 1,
    
    driver_document_handling_score = 100.00,  -- Perfect handling!
    
    updated_by = 'ADMIN01',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';

-- Final audit approval
UPDATE tr_tms_dispatcher_shuttle
SET 
    audit_status = 'APPROVED',
    audited_by = 'AUDITOR01',
    audited_at = '2025-11-19 14:00:00',
    audit_findings = 'All documents complete, on time, no discrepancies',
    audit_approved = 1,
    
    updated_by = 'AUDITOR01',
    updated_at = GETDATE()
WHERE shuttle_id = 'SHT001';
```

---

## REPORTING QUERIES

### 1. Document Completeness Report

```sql
SELECT 
    s.shuttle_id,
    d.dispatch_number,
    s.co_number,
    s.dn_number,
    s.btb_number,
    dr.driver_name,
    
    -- Document status
    s.doc_dn_returned,
    s.doc_btb_returned,
    s.documents_complete,
    s.document_completeness_score,
    
    -- Timeline
    s.handover_datetime,
    s.return_datetime,
    DATEDIFF(HOUR, s.handover_datetime, s.return_datetime) AS cycle_time_hours,
    
    -- Performance
    s.overall_on_time,
    s.driver_document_handling_score
    
FROM tr_tms_dispatcher_shuttle s
INNER JOIN tr_tms_dispatcher_main d ON s.dispatch_id = d.dispatch_id
INNER JOIN ms_driver dr ON s.driver_id_actual = dr.driver_id
WHERE s.handover_datetime >= '2025-11-18'
  AND s.handover_datetime < '2025-11-25'
ORDER BY s.handover_datetime DESC;
```

---

### 2. Missing Documents Alert

```sql
SELECT 
    s.shuttle_id,
    d.dispatch_number,
    s.co_number,
    dr.driver_name,
    s.return_datetime,
    
    -- Missing docs
    CASE WHEN s.doc_dn_returned = 0 THEN 'DN MISSING! ' ELSE '' END +
    CASE WHEN s.doc_btb_returned = 0 THEN 'BTB MISSING! ' ELSE '' END +
    s.missing_documents AS missing_documents_alert,
    
    s.missing_document_penalty,
    
    DATEDIFF(DAY, s.return_datetime, GETDATE()) AS days_overdue
    
FROM tr_tms_dispatcher_shuttle s
INNER JOIN tr_tms_dispatcher_main d ON s.dispatch_id = d.dispatch_id
INNER JOIN ms_driver dr ON s.driver_id_actual = dr.driver_id
WHERE s.documents_complete = 0
  AND s.return_datetime IS NOT NULL
ORDER BY s.return_datetime;
```

---

### 3. Qty Variance Analysis (CO vs DN vs BTB)

```sql
SELECT 
    s.shuttle_id,
    d.dispatch_number,
    s.co_number,
    
    -- Qty tracking
    s.dn_qty_planned AS co_qty,
    s.dn_qty_loaded AS dn_qty,
    s.btb_qty_delivered AS btb_qty,
    
    -- Variances
    s.dn_qty_variance AS dn_variance,
    s.btb_qty_variance AS btb_variance,
    s.total_variance_qty,
    
    -- Damage/shortage
    s.btb_qty_damaged,
    s.btb_qty_shortage,
    
    -- Match status
    s.co_dn_match,
    s.dn_btb_match,
    s.co_dn_btb_all_match,
    
    s.variance_explanation
    
FROM tr_tms_dispatcher_shuttle s
INNER JOIN tr_tms_dispatcher_main d ON s.dispatch_id = d.dispatch_id
WHERE s.co_dn_btb_all_match = 0  -- Show only discrepancies
ORDER BY ABS(s.total_variance_qty) DESC;
```

---

## ADVANTAGES OF THIS STRUCTURE

### ✅ 1. Complete Document Tracking
- Track CO → DN → BTB → Return complete lifecycle
- Photo upload untuk setiap dokumen
- Verification workflow

### ✅ 2. Serah Terima Documentation
- Tanda tangan digital dispatcher & driver
- Timestamp lengkap
- Checklist kelengkapan

### ✅ 3. Qty Reconciliation
- Compare CO vs DN vs BTB
- Auto-detect variances
- Track damage/shortage

### ✅ 4. Compliance & Audit
- Document completeness score
- Performance metrics
- Audit trail lengkap

### ✅ 5. Driver Performance Tracking
- Document handling score
- On-time performance
- Penalty untuk dokumen hilang

---

## NEXT STEPS

1. ✅ Review struktur table apakah sudah cover semua kebutuhan
2. Create migration file
3. Build mobile app untuk driver (photo upload DN/BTB)
4. Build web dashboard untuk dispatcher (document tracking)
5. Create report untuk document audit

Apakah struktur ini sudah sesuai? Ada yang perlu ditambah? 🚀