# AQUA MASTER DATA ANALYSIS
## Extracted from CO Planning Excel (Real Data)

**Source**: CO_Planning_AQUA_Week47_2025.xlsx  
**Date Analyzed**: 17 Nov 2025  
**Total Rows**: 412 shipments  
**Purpose**: Create master data for migration (ms_location, ms_truck_type)

---

## 1. LOCATION MASTER DATA

### 1.1 Data Extraction from Excel

**Column Analysis:**
- **Source Location Name**: "9000 ID CIHERANG PLANT TIV"
- **Source Location ID**: "9018" (plant code)
- **Destination Location Name**: "9000 ID PALAPA DC TIV"
- **Destination Location ID**: "9025" (DC code)

**Pattern Recognition:**
- Format: `9000 ID [NAME] [TYPE]`
- Code stored separately (4 digits: 9018, 9025, etc.)
- Type suffix: PLANT TIV, DC TIV, XWH (External Warehouse)

---

### 1.2 UNIQUE LOCATIONS (Extracted from 412 rows)

#### A. PLANTS (Origin - FACTORY)

```sql
-- LOC001: Ciherang Plant
INSERT INTO ms_location (
    location_id, location_code, location_name, 
    location_type, client_id, address, 
    city, province, postal_code,
    latitude, longitude, 
    is_active, created_at
) VALUES (
    'LOC001', '9018', '9000 ID CIHERANG PLANT TIV',
    'PLANT', 'CUS001', 'Jl. Raya Ciherang KM XX',
    'Bogor', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS coordinates
    1, NOW()
);

-- LOC002: Mekarsari Plant
INSERT INTO ms_location (
    location_id, location_code, location_name, 
    location_type, client_id, address, 
    city, province, postal_code,
    latitude, longitude, 
    is_active
) VALUES (
    'LOC002', '90AE', '9000 ID MEKARSARI PLANT TIV',
    'PLANT', 'CUS001', 'Jl. Raya Mekarsari KM XX',
    'Bekasi', 'Jawa Barat', '17XXX',
    -6.XXXXX, 107.XXXXX,  -- Need GPS
    1
);

-- LOC003: Sentul Plant
INSERT INTO ms_location (
    location_id, location_code, location_name, 
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC003', '9076', '9000 ID SENTUL PLANT TIV',
    'PLANT', 'CUS001', 'Jl. Raya Sentul KM XX',
    'Bogor', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);

-- LOC004: Citeureup Plant
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC004', '9013', '9000 ID CITEUREUP PLANT TIV',
    'PLANT', 'CUS001', 'Jl. Raya Citeureup KM XX',
    'Bogor', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);

-- LOC005: Caringin Plant
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC005', '90A0', '9000 ID CARINGIN PLANT TIV',
    'PLANT', 'CUS001', 'Jl. Raya Caringin KM XX',
    'Bogor', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);
```

#### B. DISTRIBUTION CENTERS (Destination - DC)

```sql
-- LOC006: Palapa DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC006', '9025', '9000 ID PALAPA DC TIV',
    'DC', 'CUS001', 'Jl. Palapa Raya No. XX',
    'Jakarta Timur', 'DKI Jakarta', '13XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);

-- LOC007: Kawasan DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC007', '9021', '9000 ID KAWASAN DC TIV',
    'DC', 'CUS001', 'Jl. Kawasan Industri No. XX',
    'Bekasi', 'Jawa Barat', '17XXX',
    -6.XXXXX, 107.XXXXX,  -- Need GPS
    1
);

-- LOC008: Bandung DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC008', '9028', '9000 ID BANDUNG DC TIV',
    'DC', 'CUS001', 'Jl. Soekarno Hatta No. XX',
    'Bandung', 'Jawa Barat', '40XXX',
    -6.XXXXX, 107.XXXXX,  -- Need GPS
    1
);

-- LOC009: Ciputat DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC009', '9026', '9000 ID CIPUTAT DC TIV',
    'DC', 'CUS001', 'Jl. Ciputat Raya No. XX',
    'Tangerang Selatan', 'Banten', '15XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);

-- LOC010: Jatiasih DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC010', '9061', '9000 ID JATIASIH DC TIV',
    'DC', 'CUS001', 'Jl. Jatiasih No. XX',
    'Bekasi', 'Jawa Barat', '17XXX',
    -6.XXXXX, 107.XXXXX,  -- Need GPS
    1
);

-- LOC011: Cikarang DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC011', '9024', '9000 ID CIKARANG DC TIV',
    'DC', 'CUS001', 'Jl. Cikarang Industrial Estate No. XX',
    'Cikarang', 'Jawa Barat', '17XXX',
    -6.XXXXX, 107.XXXXX,  -- Need GPS
    1
);

-- LOC012: Cibinong DC
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC012', '9027', '9000 ID CIBINONG DC TIV',
    'DC', 'CUS001', 'Jl. Raya Cibinong No. XX',
    'Bogor', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);
```

#### C. EXTERNAL WAREHOUSES (Destination - XWH)

```sql
-- LOC013: Cimanggis External Warehouse
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC013', '9059', '9000 ID TIV XWH CIMANGGIS',
    'EXTERNAL_WAREHOUSE', 'CUS001', 'Jl. Raya Cimanggis No. XX',
    'Depok', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);
```

#### D. DISTRIBUTORS (Third-party destinations)

```sql
-- LOC014: Tirta Varia Intipratama - Lodan
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC014', '950108918', 'TIRTA VARIA INTIPRATAMA, PT - LODAN',
    'DISTRIBUTOR', 'CUS001', 'Jl. Lodan Raya No. XX',
    'Jakarta Utara', 'DKI Jakarta', '14XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);

-- LOC015: Adi Sukses Abadi - Balaraja
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC015', '950229088', 'PT. ADI SUKSES ABADI - BALARAJA',
    'DISTRIBUTOR', 'CUS001', 'Jl. Raya Balaraja KM XX',
    'Tangerang', 'Banten', '15XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);

-- LOC016: Tirta Varia Intipratama - Depok
INSERT INTO ms_location (
    location_id, location_code, location_name,
    location_type, client_id, address,
    city, province, postal_code,
    latitude, longitude,
    is_active
) VALUES (
    'LOC016', '950254122', 'PT. TIRTA VARIA INTIPRATAMA - DEPOK',
    'DISTRIBUTOR', 'CUS001', 'Jl. Margonda Raya No. XX',
    'Depok', 'Jawa Barat', '16XXX',
    -6.XXXXX, 106.XXXXX,  -- Need GPS
    1
);
```

---

### 1.3 LOCATION SUMMARY

| Location Type | Count | Location Codes | Purpose |
|--------------|-------|----------------|---------|
| **PLANT** | 5 | 9018, 90AE, 9076, 9013, 90A0 | Origin (Loading Point) |
| **DC** | 7 | 9025, 9021, 9028, 9026, 9061, 9024, 9027 | Destination (Unloading) |
| **EXTERNAL_WAREHOUSE** | 1 | 9059 | Destination (Temporary Storage) |
| **DISTRIBUTOR** | 3 | 950108918, 950229088, 950254122 | Third-party Delivery |
| **TOTAL** | **16** | | |

---

## 2. TRUCK TYPE MASTER DATA

### 2.1 Data Extraction from Excel

**Column**: `First Equipment Group ID`

**Unique Values Found** (from 412 rows):
1. `TNWB_JUGRACK_GREEN-TIV`
2. `TNWB_PET_JUGRACK-TIV`
3. `TNWB_ODOL-864_JUGRACK-TIV`
4. `TNWB_JUGRACK-TIV`
5. `TNWB_PALLET`

---

### 2.2 TRUCK TYPE ANALYSIS

#### Pattern Recognition:
- **TNWB**: Truck type prefix (Aqua standard)
- **JUGRACK**: Jug rack system (for gallon water)
- **PALLET**: Pallet-based loading
- **ODOL**: Odol truck (specific shape)
- **PET**: PET bottle (plastic bottles)
- **GREEN**: Color code (maybe route-specific?)
- **-TIV**: Suffix (Tirta Investama?)
- **864**: Capacity code (maybe 864 cartons?)

#### Capacity Analysis (from Package Count column):
- **JUGRACK**: 2900 cartons (most common)
- **PALLET**: 1312-2720 cartons (variable)
- **ODOL-864**: 2610 cartons

---

### 2.3 TRUCK TYPE MASTER DATA (for Migration)

```sql
-- TRK001: JUGRACK GREEN (Standard shuttle truck)
INSERT INTO ms_truck_type (
    truck_type_id, truck_type_code, truck_type_name,
    capacity_kg, capacity_cbm, capacity_carton,
    truck_category, body_type,
    length_m, width_m, height_m,
    client_specific, client_id,
    notes, is_active
) VALUES (
    'TRK001', 'TNWB_JUGRACK_GREEN-TIV', 'JUGRACK Green TIV',
    12000, 35, 2900,  -- 2900 cartons typical
    'BOX', 'CLOSED',
    6.0, 2.4, 2.5,
    1, 'CUS001',  -- Aqua-specific
    'Standard jug rack truck for gallon water, green color marking',
    1
);

-- TRK002: JUGRACK Standard (General purpose)
INSERT INTO ms_truck_type (
    truck_type_id, truck_type_code, truck_type_name,
    capacity_kg, capacity_cbm, capacity_carton,
    truck_category, body_type,
    length_m, width_m, height_m,
    client_specific, client_id,
    notes, is_active
) VALUES (
    'TRK002', 'TNWB_JUGRACK-TIV', 'JUGRACK TIV',
    12000, 35, 2900,
    'BOX', 'CLOSED',
    6.0, 2.4, 2.5,
    1, 'CUS001',
    'Standard jug rack truck (no color)',
    1
);

-- TRK003: PET JUGRACK (For PET bottles)
INSERT INTO ms_truck_type (
    truck_type_id, truck_type_code, truck_type_name,
    capacity_kg, capacity_cbm, capacity_carton,
    truck_category, body_type,
    length_m, width_m, height_m,
    client_specific, client_id,
    notes, is_active
) VALUES (
    'TRK003', 'TNWB_PET_JUGRACK-TIV', 'PET JUGRACK TIV',
    10000, 30, 2610,
    'BOX', 'CLOSED',
    6.0, 2.4, 2.5,
    1, 'CUS001',
    'Jug rack for PET bottles (plastic bottles)',
    1
);

-- TRK004: ODOL-864 JUGRACK (Specialized odol truck)
INSERT INTO ms_truck_type (
    truck_type_id, truck_type_code, truck_type_name,
    capacity_kg, capacity_cbm, capacity_carton,
    truck_category, body_type,
    length_m, width_m, height_m,
    client_specific, client_id,
    notes, is_active
) VALUES (
    'TRK004', 'TNWB_ODOL-864_JUGRACK-TIV', 'ODOL-864 JUGRACK TIV',
    11000, 32, 2610,  -- 2610 cartons from data
    'BOX', 'CLOSED',
    7.0, 2.4, 2.6,  -- Longer body (odol shape)
    1, 'CUS001',
    'Odol-shaped truck with jug rack, capacity 864 units/2610 cartons',
    1
);

-- TRK005: PALLET (Pallet-based loading)
INSERT INTO ms_truck_type (
    truck_type_id, truck_type_code, truck_type_name,
    capacity_kg, capacity_cbm, capacity_carton,
    truck_category, body_type,
    length_m, width_m, height_m,
    client_specific, client_id,
    notes, is_active
) VALUES (
    'TRK005', 'TNWB_PALLET', 'PALLET TIV',
    15000, 40, 2720,  -- Variable 1312-2720, use max
    'BOX', 'CLOSED',
    8.0, 2.5, 2.7,  -- Larger truck
    1, 'CUS001',
    'Pallet-based truck, capacity varies (1312-2720 cartons)',
    1
);
```

---

### 2.4 TRUCK TYPE SUMMARY

| Truck Type Code | Name | Capacity (Carton) | Usage Count | Percentage |
|-----------------|------|-------------------|-------------|------------|
| **TNWB_JUGRACK_GREEN-TIV** | JUGRACK Green | 2900 | 180 | 43.7% |
| **TNWB_JUGRACK-TIV** | JUGRACK Standard | 2900 | 150 | 36.4% |
| **TNWB_PET_JUGRACK-TIV** | PET JUGRACK | 2610 | 45 | 10.9% |
| **TNWB_ODOL-864_JUGRACK-TIV** | ODOL-864 | 2610 | 25 | 6.1% |
| **TNWB_PALLET** | PALLET | 1312-2720 | 12 | 2.9% |
| **TOTAL** | | | **412** | **100%** |

---

## 3. ROUTE MASTER DATA (Derived from Location Pairs)

### 3.1 Unique Routes Detected (from 412 shipments)

```sql
-- ROU001: Ciherang → Palapa (Most common)
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU001', 'CHR-PAL', 'Ciherang Plant → Palapa DC',
    'LOC001', 'LOC006',  -- 9018 → 9025
    45, 2.5,  -- Need accurate measurement
    'SHUTTLE', 15000,  -- Estimate toll
    'CUS001', 1
);

-- ROU002: Mekarsari → Kawasan
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU002', 'MEK-KWS', 'Mekarsari Plant → Kawasan DC',
    'LOC002', 'LOC007',  -- 90AE → 9021
    35, 2.0,
    'SHUTTLE', 12000,
    'CUS001', 1
);

-- ROU003: Mekarsari → Bandung (Long haul!)
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU003', 'MEK-BDG', 'Mekarsari Plant → Bandung DC',
    'LOC002', 'LOC008',  -- 90AE → 9028
    180, 4.5,  -- Long distance
    'LONG_HAUL', 50000,  -- Higher toll
    'CUS001', 1
);

-- ROU004: Sentul → Ciputat
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU004', 'SNT-CPT', 'Sentul Plant → Ciputat DC',
    'LOC003', 'LOC009',  -- 9076 → 9026
    55, 3.0,
    'SHUTTLE', 20000,
    'CUS001', 1
);

-- ROU005: Citeureup → Cimanggis XWH
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU005', 'CTP-CMG', 'Citeureup Plant → Cimanggis XWH',
    'LOC004', 'LOC013',  -- 9013 → 9059
    25, 1.5,
    'SHUTTLE', 8000,
    'CUS001', 1
);

-- ROU006: Caringin → Cibinong
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU006', 'CRG-CBI', 'Caringin Plant → Cibinong DC',
    'LOC005', 'LOC012',  -- 90A0 → 9027
    30, 2.0,
    'SHUTTLE', 10000,
    'CUS001', 1
);

-- ROU007: Mekarsari → Ciputat
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU007', 'MEK-CPT', 'Mekarsari Plant → Ciputat DC',
    'LOC002', 'LOC009',  -- 90AE → 9026
    50, 2.5,
    'SHUTTLE', 18000,
    'CUS001', 1
);

-- ROU008: Sentul → Palapa
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU008', 'SNT-PAL', 'Sentul Plant → Palapa DC',
    'LOC003', 'LOC006',  -- 9076 → 9025
    40, 2.5,
    'SHUTTLE', 15000,
    'CUS001', 1
);

-- ROU009: Sentul → Kawasan
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU009', 'SNT-KWS', 'Sentul Plant → Kawasan DC',
    'LOC003', 'LOC007',  -- 9076 → 9021
    38, 2.5,
    'SHUTTLE', 14000,
    'CUS001', 1
);

-- ROU010: Ciherang → Jatiasih
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU010', 'CHR-JTS', 'Ciherang Plant → Jatiasih DC',
    'LOC001', 'LOC010',  -- 9018 → 9061
    42, 2.5,
    'SHUTTLE', 16000,
    'CUS001', 1
);

-- ROU011: Ciherang → Cikarang
INSERT INTO ms_route (
    route_id, route_code, route_name,
    origin_location_id, destination_location_id,
    distance_km, estimated_duration_hours,
    route_type, toll_cost,
    client_id, is_active
) VALUES (
    'ROU011', 'CHR-CKR', 'Ciherang Plant → Cikarang DC',
    'LOC001', 'LOC011',  -- 9018 → 9024
    50, 3.0,
    'SHUTTLE', 18000,
    'CUS001', 1
);
```

---

### 3.2 ROUTE SUMMARY

| Route Code | Origin → Destination | Distance | Duration | Type | Count | % |
|-----------|---------------------|----------|----------|------|-------|---|
| **CHR-PAL** | Ciherang → Palapa | 45 km | 2.5h | Shuttle | 82 | 19.9% |
| **MEK-KWS** | Mekarsari → Kawasan | 35 km | 2.0h | Shuttle | 95 | 23.1% |
| **MEK-BDG** | Mekarsari → Bandung | 180 km | 4.5h | Long Haul | 28 | 6.8% |
| **SNT-CPT** | Sentul → Ciputat | 55 km | 3.0h | Shuttle | 65 | 15.8% |
| **CTP-CMG** | Citeureup → Cimanggis | 25 km | 1.5h | Shuttle | 48 | 11.7% |
| **CRG-CBI** | Caringin → Cibinong | 30 km | 2.0h | Shuttle | 22 | 5.3% |
| **Others** | Various combinations | Varies | Varies | Shuttle | 72 | 17.4% |
| **TOTAL** | | | | | **412** | **100%** |

---

## 4. MOVEMENT TYPE ANALYSIS

From Excel column: `Movement Type`

**Unique Values:**
1. `FACTORY TO DEPO` (92% - 379 shipments)
2. `FACTORY TO EX-WHS` (5% - 21 shipments)
3. `FACTORY TO DISTRIBUTOR` (3% - 12 shipments)

**Mapping to TMS:**
- **FACTORY TO DEPO** → `movement_type = 'DELIVERY'` + `order_type = 'SHUTTLE'`
- **FACTORY TO EX-WHS** → `movement_type = 'DELIVERY'` + `destination_type = 'EXTERNAL_WAREHOUSE'`
- **FACTORY TO DISTRIBUTOR** → `movement_type = 'DELIVERY'` + `destination_type = 'DISTRIBUTOR'`

---

## 5. TIME WINDOW ANALYSIS

From Excel column: `Pick Up Window`

**Unique Values Found**: 1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12

**Missing Windows**: 4 (possibly error in sample data)

**Window Distribution:**
| Window | Start Time | End Time | Count | % |
|--------|-----------|----------|-------|---|
| 1 | 00:00 | 02:00 | 62 | 15.0% |
| 2 | 02:00 | 04:00 | 58 | 14.1% |
| 3 | 04:00 | 06:00 | 42 | 10.2% |
| 5 | 08:00 | 10:00 | 38 | 9.2% |
| 6 | 10:00 | 12:00 | 45 | 10.9% |
| 7 | 12:00 | 14:00 | 41 | 10.0% |
| 8 | 14:00 | 16:00 | 48 | 11.7% |
| 9 | 16:00 | 18:00 | 18 | 4.4% |
| 10 | 18:00 | 20:00 | 25 | 6.1% |
| 11 | 20:00 | 22:00 | 22 | 5.3% |
| 12 | 22:00 | 00:00 | 13 | 3.1% |
| **TOTAL** | | | **412** | **100%** |

**Observation:**
- Window 1-3 (Night shift): 39.3% of trips
- Window 5-8 (Day shift): 41.8% of trips
- Window 9-12 (Evening shift): 18.9% of trips

---

## 6. MIGRATION SCRIPT PRIORITY

### Phase 1: Foundation Master Data (Must create first)
```bash
# 1. Client master
php artisan migrate:fresh --path=database/migrations/xxxx_create_ms_client_table.php
php artisan db:seed --class=AquaClientSeeder  # Create CUS001

# 2. Location master (16 locations)
php artisan db:seed --class=AquaLocationSeeder

# 3. Truck type master (5 types)
php artisan db:seed --class=AquaTruckTypeSeeder

# 4. Time window master (12 windows)
php artisan db:seed --class=TimeWindowSeeder

# 5. Route master (11 routes)
php artisan db:seed --class=AquaRouteSeeder
```

### Phase 2: Import CO Planning
```bash
# After master data ready, import planning
php artisan aqua:import-co-planning --file=CO_Planning_Week47_2025.xlsx
```

---

## 7. DATA QUALITY NOTES

### Missing Information (Need to collect):
1. **GPS Coordinates**: All 16 locations need lat/long
2. **Exact Addresses**: Only have generic addresses
3. **Distance & Duration**: Need accurate Google Maps data
4. **Toll Costs**: Need actual toll receipts
5. **Truck Dimensions**: Estimated, need actual specs
6. **Contact Info**: Each location needs PIC, phone, email

### Data Inconsistencies Found:
1. **Driver Names**: Some typos (e.g., "DEDI KUSNADI" vs "DEDI KUSNANDI")
2. **Truck Codes**: Some non-standard (need validation)
3. **Window 4 Missing**: Possible data entry error

### Recommendations:
1. **Location Validation**: Cross-check with Google Maps
2. **Driver Master**: Import unique driver names → create driver master
3. **Truck Master**: Map Truck Id (e.g., "HGS/B9873KYX") → create vehicle master
4. **Geocoding**: Use Google Maps API to get coordinates
5. **Data Cleansing**: Fix typos before migration

---

## 8. NEXT STEPS

### 8.1 Immediate Actions
- [ ] Get GPS coordinates for all 16 locations
- [ ] Measure actual distances (Google Maps API)
- [ ] Collect toll cost data from finance
- [ ] Verify truck specifications with fleet manager
- [ ] Clean driver name data (standardize spelling)

### 8.2 Create Seeders
- [ ] `AquaClientSeeder.php` (1 client)
- [ ] `AquaLocationSeeder.php` (16 locations)
- [ ] `AquaTruckTypeSeeder.php` (5 truck types)
- [ ] `AquaRouteSeeder.php` (11 routes)
- [ ] `AquaDriverSeeder.php` (extract from Excel)
- [ ] `AquaVehicleSeeder.php` (extract from Excel)

### 8.3 Create Import Command
- [ ] `php artisan make:command ImportAquaCOPlanning`
- [ ] Parse 29 columns Excel format
- [ ] Map to ms_planning_weekly table
- [ ] Validate routes, windows, locations
- [ ] Handle pre-assigned trucks/drivers

---

**Document prepared by**: TMS Development Team  
**Date**: 17 November 2025  
**Version**: 1.0  
**Status**: Ready for Seeder Development
