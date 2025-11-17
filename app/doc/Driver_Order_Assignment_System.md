# TMS - Driver Order Assignment System
## (How to Give Orders to Drivers - Complete Flow)

**Tanggal:** 17 November 2025  
**Status:** Design Phase - Driver Communication & Assignment  

---

## 1. REALITAS BISNIS - DRIVER COMMUNICATION

### 1.1 Current State (Manual/Traditional)

```
CARA LAMA (Manual):

1. Dispatcher telepon driver (Jam 17:00 untuk besok):
   "Pak Budi, besok jam 6 pagi ambil barang di Cikupa 
    kirim ke Bandung ya. Truck B-1234-AB. 
    Kenek Andi ikut. Jangan lupa."

2. Driver catet di buku/HP (kadang lupa, kadang salah catat):
   "Besok pkl 6, Cikupa-Bandung, B-1234-AB"
   ❌ Lupa alamat detail Cikupa dimana
   ❌ Lupa contact person siapa
   ❌ Lupa barang apa yang diangkut
   ❌ Lupa jam berapa harus sampai

3. Pagi hari driver datang (kadang telat, kadang salah truck)
   
4. Dispatcher kasih uang jalan cash + nota BBM + e-toll card

5. Driver berangkat (kadang salah jalan, telepon-telepon)

MASALAH:
❌ Komunikasi tidak tercatat (no audit trail)
❌ Detail order tidak lengkap
❌ Driver sering lupa atau salah
❌ Tidak ada konfirmasi terima order
❌ Tidak ada tracking real-time
❌ Settlement ribet (nota hilang, lupa berapa dapat uang jalan)
```

### 1.2 Target State (Digital/TMS)

```
CARA BARU (TMS Digital):

1. Dispatcher assign di system (Jumat 17:00 untuk minggu depan):
   ✅ All details complete: Route, time, load, truck, helper
   ✅ System calculate: Uang jalan, estimated cost, tariff
   ✅ Auto-send notification ke driver (SMS/WA/App)

2. Driver terima notifikasi di HP:
   ✅ Complete info: Alamat GPS, contact person, product detail
   ✅ Route map (Google Maps integration)
   ✅ Estimated time, distance, toll
   ✅ Uang jasa driver (settlement preview)

3. Driver CONFIRM atau REJECT:
   ✅ Confirm: "Siap pak, besok jam 6"
   ✅ Reject: "Maaf pak, ada keperluan keluarga" (system cari pengganti)

4. Pagi hari CHECK-IN digital:
   ✅ Driver tap di app: "Saya sudah di kantor"
   ✅ System verify: Location, truck, helper
   ✅ Virtual uang jalan (recorded, no cash yet - ambil nanti)

5. START TRIP:
   ✅ GPS tracking otomatis
   ✅ Real-time monitoring
   ✅ Auto-update ETA

BENEFIT:
✅ Complete digital record
✅ Zero miscommunication
✅ Driver accountability (confirm order)
✅ Real-time tracking
✅ Automated settlement (semua tercatat)
```

---

## 2. DRIVER ASSIGNMENT CHANNELS

### 2.1 Multi-Channel Communication

| Channel | Use Case | Pros | Cons | Priority |
|---------|----------|------|------|----------|
| **Mobile App** | Primary (driver punya smartphone) | Complete info, GPS, real-time | Need smartphone, internet | 🥇 HIGH |
| **SMS** | Backup (driver no smartphone/no internet) | Universal, no internet needed | Limited info (160 char) | 🥈 MEDIUM |
| **WhatsApp** | Semi-formal (common in Indonesia) | Rich media, two-way chat | Need WA Business API | 🥉 MEDIUM |
| **Phone Call** | Urgent/Emergency | Immediate, personal | No record, time-consuming | ⚠️ EMERGENCY |
| **Email** | Formal confirmation | Complete detail, attachment | Driver jarang cek email | 🔽 LOW |

### 2.2 Recommended Strategy

```
PRIMARY: Mobile App (Driver App - Android/iOS)
├─ 80% driver adoption target
├─ Complete feature: Order detail, GPS tracking, check-in, settlement
└─ Offline mode available (sync when online)

BACKUP: SMS (for driver without smartphone atau offline)
├─ 20% driver (older driver, basic phone)
├─ Limited info: "Besok 06:00, Cikupa-Bandung, B-1234-AB, call dispatcher for detail"
└─ Manual tracking (driver call/WA progress)

EMERGENCY: Phone Call (adjustment, urgent issue)
├─ Real-time problem solving
└─ Record call summary in system manually
```

---

## 3. DRIVER MOBILE APP - ORDER FLOW

### 3.1 App Home Screen

```
┌─────────────────────────────────────────────────────┐
│ 🚛 TMS Driver - Home                    [☰]  [🔔3] │
├─────────────────────────────────────────────────────┤
│                                                     │
│ 👤 Budi Santoso (DRV001)                            │
│ 📱 0812-3456-7890                                   │
│ 🚗 License: SIM B2 (Valid until: 12/2027)           │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 📋 MY ORDERS                                        │
│                                                     │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🆕 NEW ORDER (Pending Confirmation)             │ │
│ │                                                 │ │
│ │ Tomorrow, 18 Nov 2025                           │ │
│ │ 🕐 06:00 - 08:00 (Window 4)                     │ │
│ │                                                 │ │
│ │ 📍 Cikupa → Bandung (120 km)                    │ │
│ │ 🚚 Truck: B-1234-AB (Tronton 18t)               │ │
│ │ 👷 Helper: Andi (HLP001)                        │ │
│ │ 📦 Load: Mie Instan 15 ton                      │ │
│ │                                                 │ │
│ │ 💰 Uang Jasa: Rp 200,000                        │ │
│ │ 💵 Uang Jalan: Rp 85,000 (cash advance)         │ │
│ │                                                 │ │
│ │ [VIEW DETAIL]  [CONFIRM]  [REJECT]              │ │
│ └─────────────────────────────────────────────────┘ │
│                                                     │
│ ┌─────────────────────────────────────────────────┐ │
│ │ ✅ CONFIRMED ORDER                              │ │
│ │                                                 │ │
│ │ Today, 18 Nov 2025                              │ │
│ │ 🕐 14:00 - 16:00 (Window 8)                     │ │
│ │                                                 │ │
│ │ 📍 Bandung → Cikupa (120 km, BACKHAUL)          │ │
│ │ 🚚 Truck: B-1234-AB                             │ │
│ │ 📦 Load: Empty (return trip)                    │ │
│ │                                                 │ │
│ │ 💰 Uang Jasa: Rp 100,000                        │ │
│ │                                                 │ │
│ │ [START TRIP]  [VIEW DETAIL]                     │ │
│ └─────────────────────────────────────────────────┘ │
│                                                     │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🚛 ON GOING TRIP                                │ │
│ │                                                 │ │
│ │ Jakarta → Surabaya                              │ │
│ │ Status: ON ROUTE (320 km / 780 km)              │ │
│ │ ETA: 18:30 (2 hours 15 min)                     │ │
│ │                                                 │ │
│ │ [TRACK]  [REPORT ISSUE]  [COMPLETE]             │ │
│ └─────────────────────────────────────────────────┘ │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 📊 This Month Performance:                          │
│ - Trips completed: 18 / 20 target                   │
│ - On-time delivery: 94%                             │
│ - Rating: ⭐⭐⭐⭐⭐ 4.8/5.0                          │
│ - Estimated earning: Rp 5,200,000                   │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### 3.2 Order Detail Screen

```
┌─────────────────────────────────────────────────────┐
│ ← Order Detail                          [Share] [?] │
├─────────────────────────────────────────────────────┤
│                                                     │
│ ORDER #DP-2025-11-18-001                            │
│ Status: 🆕 PENDING YOUR CONFIRMATION                │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 📅 SCHEDULE                                         │
│ Date: Monday, 18 November 2025                      │
│ Time Window: 06:00 - 08:00 (Window 4)               │
│ Departure: 06:00 (be ready at 05:45)                │
│ Estimated Arrival: 09:15 (3h 15min)                 │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 📍 ROUTE DETAIL                                     │
│                                                     │
│ ORIGIN (Loading Point)                              │
│ PT Indofood Factory - Cikupa                        │
│ Jl. Raya Serang KM 75, Cikupa, Tangerang            │
│ GPS: -6.234567, 106.512345                          │
│ [OPEN IN MAPS] [CALL: 021-5551234]                  │
│                                                     │
│ Contact Person: Pak Joko (Warehouse)                │
│ Phone: 0812-3333-4444                               │
│ Operational Hours: 06:00 - 22:00                    │
│ Notes: "Masuk dari gate 2, lapor security"          │
│                                                     │
│ ─────────────────────────────────────────────────   │
│                                                     │
│ DESTINATION (Unloading Point)                       │
│ PT Indofood DC - Bandung                            │
│ Jl. Soekarno Hatta No. 456, Bandung                 │
│ GPS: -6.912345, 107.612345                          │
│ [OPEN IN MAPS] [CALL: 022-7771234]                  │
│                                                     │
│ Contact Person: Ibu Siti (Receiving)                │
│ Phone: 0813-5555-6666                               │
│ Operational Hours: 08:00 - 18:00                    │
│ Notes: "Lapor ke gate utama, tunggu antrian"        │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 🗺️ ROUTE MAP                                        │
│ ┌─────────────────────────────────────────────────┐ │
│ │                                                 │ │
│ │    [A] Cikupa                                   │ │
│ │      │                                          │ │
│ │      │ ═══ Tol Cipularang ═══                  │ │
│ │      │ Distance: 120 km                        │ │
│ │      │ Duration: 2h 30min (normal traffic)     │ │
│ │      │ Toll: Rp 65,000 (e-toll auto)           │ │
│ │      │                                          │ │
│ │    [B] Bandung                                  │ │
│ │                                                 │ │
│ └─────────────────────────────────────────────────┘ │
│ [VIEW FULL MAP] [NAVIGATION]                        │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 📦 CARGO DETAIL                                     │
│ Product: Mie Goreng Instan (Indomie)                │
│ Quantity: 15,000 kg (15 ton)                        │
│ Volume: 30 CBM                                      │
│ Packaging: Karton (fragile ⚠️)                      │
│                                                     │
│ Special Instructions:                               │
│ - Handle with care (produk mudah pecah)             │
│ - Tidak boleh terkena hujan                         │
│ - Gunakan terpal jika cuaca buruk                   │
│ - Stack maksimal 10 karton tinggi                   │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 🚚 VEHICLE & TEAM                                   │
│ Truck: B-1234-AB (Tronton 18 ton)                   │
│ Fuel Card: BBM-CARD-001 (Balance: Rp 500k)          │
│ E-Toll Card: ETOLL-CARD-001 (Balance: Rp 350k)      │
│                                                     │
│ Helper: Andi (HLP001)                               │
│ Phone: 0815-7777-8888                               │
│ [CALL HELPER]                                       │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 💰 PAYMENT & SETTLEMENT                             │
│                                                     │
│ Uang Jasa Driver (Your Fee):                        │
│ - Base Fee: Rp 200,000                              │
│ - Bonus (on-time): Rp 20,000 (potential)            │
│ - Total Potential: Rp 220,000                       │
│                                                     │
│ Uang Jalan (Cash Advance):                          │
│ - Parkir: Rp 20,000                                 │
│ - Retribusi: Rp 15,000                              │
│ - Makan: Rp 50,000                                  │
│ - Total: Rp 85,000 (collect before trip)            │
│ ⚠️ Keep all receipts for settlement!                │
│                                                     │
│ E-toll: Auto-deduct from card (±Rp 65,000)          │
│ Fuel: Swipe fuel card (no cash)                     │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ 📋 DOCUMENTS REQUIRED                               │
│ ☑️ SIM (your license)                               │
│ ☑️ STNK (vehicle registration)                      │
│ ☑️ KIR (vehicle inspection certificate)             │
│ ☑️ Surat Jalan (delivery order - printed)           │
│                                                     │
│ [DOWNLOAD SURAT JALAN PDF]                          │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│                                                     │
│ ⏱️ RESPOND BY: 17 Nov 2025, 20:00                   │
│ (12 hours remaining)                                │
│                                                     │
│ ┌───────────────────┐  ┌───────────────────────┐   │
│ │   ✅ CONFIRM      │  │   ❌ REJECT           │   │
│ │   (I will take    │  │   (Cannot take this   │   │
│ │    this order)    │  │    order)             │   │
│ └───────────────────┘  └───────────────────────┘   │
│                                                     │
│ If reject, please select reason:                    │
│ [ ] Already have other trip                         │
│ [ ] Health issue / sick                             │
│ [ ] Vehicle problem                                 │
│ [ ] Personal / family matter                        │
│ [ ] Route too far                                   │
│ [ ] Other (specify)                                 │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### 3.3 Driver Actions Flow

```
FLOW 1: DRIVER CONFIRM ORDER
┌─────────────────────────────────────────────────────┐
│ 1. Driver tap [CONFIRM]                             │
├─────────────────────────────────────────────────────┤
│ System:                                             │
│ - Update order status: PENDING → CONFIRMED          │
│ - Send notification to dispatcher: "Budi confirmed" │
│ - Lock driver schedule (mark as busy)               │
│ - Add to driver's active orders                     │
│ - Send confirmation receipt to driver               │
│                                                     │
│ Driver sees:                                        │
│ ✅ "Order confirmed! See you tomorrow at 06:00"     │
│ [ADD TO CALENDAR] [SET REMINDER]                    │
└─────────────────────────────────────────────────────┘

FLOW 2: DRIVER REJECT ORDER
┌─────────────────────────────────────────────────────┐
│ 1. Driver tap [REJECT]                              │
│ 2. Select reason: "Health issue / sick"             │
│ 3. Optional: Add note "Demam, ke dokter besok"      │
├─────────────────────────────────────────────────────┤
│ System:                                             │
│ - Update order status: PENDING → REJECTED_BY_DRIVER │
│ - Send urgent notification to dispatcher            │
│ - Auto-suggest replacement drivers                  │
│ - Mark original driver as unavailable (if sick)     │
│                                                     │
│ Dispatcher sees:                                    │
│ ⚠️ "URGENT: Budi rejected DP-001 (sick)"            │
│ Suggested replacement:                              │
│ - Joko (DRV002) - Available, EXPERT on this route   │
│ - Siti (DRV003) - Available, COMPETENT              │
│ [ASSIGN TO JOKO] [ASSIGN TO SITI] [FIND OTHER]      │
└─────────────────────────────────────────────────────┘

FLOW 3: DRIVER NO RESPONSE (Timeout)
┌─────────────────────────────────────────────────────┐
│ Dispatcher assign order: 17 Nov 2025, 08:00         │
│ Response deadline: 17 Nov 2025, 20:00 (12 hours)    │
│                                                     │
│ 18:00 - System send reminder:                       │
│ "⏰ Reminder: Please confirm order DP-001           │
│  Deadline: 20:00 (2 hours left)"                    │
│                                                     │
│ 20:00 - Still no response:                          │
│ - Auto-escalate to dispatcher                       │
│ - Call driver (phone call)                          │
│ - If still no response → treat as REJECTED          │
│ - Find replacement driver                           │
│                                                     │
│ ⚠️ Mark driver: "Unresponsive" (impact rating)      │
└─────────────────────────────────────────────────────┘
```

---

## 4. DRIVER CHECK-IN & START TRIP

### 4.1 Morning Check-In Flow

```
┌─────────────────────────────────────────────────────┐
│ DRIVER ARRIVES AT DEPOT (05:45)                     │
├─────────────────────────────────────────────────────┤
│                                                     │
│ App Screen:                                         │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🏢 CHECK-IN                                     │ │
│ │                                                 │ │
│ │ Today's Order: DP-2025-11-18-001                │ │
│ │ Departure: 06:00 (15 minutes from now)          │ │
│ │                                                 │ │
│ │ ✅ STEP 1: Verify Location                     │ │
│ │ Current: Depot TMS (-6.234, 106.512) ✅         │ │
│ │ Status: Inside geofence (radius 100m)           │ │
│ │                                                 │ │
│ │ ✅ STEP 2: Verify Vehicle                      │ │
│ │ Assigned Truck: B-1234-AB                       │ │
│ │ [SCAN QR CODE] or [ENTER MANUALLY]              │ │
│ │ → Driver scan QR on truck windshield            │ │
│ │ Matched ✅ B-1234-AB (Tronton 18t)              │ │
│ │                                                 │ │
│ │ ⚠️ STEP 3: Pre-Trip Inspection                 │ │
│ │ Please check and confirm:                       │ │
│ │ ☑️ Tire pressure OK                             │ │
│ │ ☑️ Brake system OK                              │ │
│ │ ☑️ Lights working                               │ │
│ │ ☑️ Fuel level sufficient (≥50%)                 │ │
│ │ ☑️ Documents complete (SIM, STNK, KIR)          │ │
│ │                                                 │ │
│ │ Any issue? [REPORT PROBLEM]                     │ │
│ │                                                 │ │
│ │ ✅ STEP 4: Verify Helper                       │ │
│ │ Assigned Helper: Andi (HLP001)                  │ │
│ │ Status: ✅ Present                              │ │
│ │ [CONFIRM] or [REPORT ABSENT]                    │ │
│ │                                                 │ │
│ │ ✅ STEP 5: Collect Cash Advance                │ │
│ │ Uang Jalan: Rp 85,000                           │ │
│ │ Received from: [Cashier/Dispatcher name]        │ │
│ │ [CONFIRM RECEIVED]                              │ │
│ │ ⚠️ Photo of cash for verification (optional)    │ │
│ │                                                 │ │
│ │ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │ │
│ │                                                 │ │
│ │ All checks complete!                            │ │
│ │                                                 │ │
│ │ ┌─────────────────────────────────────────────┐ │ │
│ │ │  ✅ CHECK-IN COMPLETE                       │ │ │
│ │ │  Ready to depart at 06:00                   │ │ │
│ │ └─────────────────────────────────────────────┘ │ │
│ │                                                 │ │
│ └─────────────────────────────────────────────────┘ │
│                                                     │
│ System Records:                                     │
│ - Check-in time: 05:45 (15 min early ✅)            │
│ - Location: Depot (verified by GPS)                 │
│ - Vehicle: B-1234-AB (scanned)                      │
│ - Pre-trip inspection: All OK                       │
│ - Helper: Present                                   │
│ - Cash advance: Confirmed Rp 85,000                 │
│                                                     │
│ Dispatcher Dashboard shows:                         │
│ ✅ Budi checked in (DP-001) - Ready to depart       │
└─────────────────────────────────────────────────────┘
```

### 4.2 Start Trip

```
┌─────────────────────────────────────────────────────┐
│ START TRIP (06:00)                                  │
├─────────────────────────────────────────────────────┤
│                                                     │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🚛 READY TO START TRIP                          │ │
│ │                                                 │ │
│ │ Current Time: 06:00                             │ │
│ │ Departure Window: 06:00 - 08:00 ✅              │ │
│ │                                                 │ │
│ │ Next Destination:                               │ │
│ │ 📍 PT Indofood Factory - Cikupa                 │ │
│ │ Distance: 12 km (20 min)                        │ │
│ │ ETA: 06:20                                      │ │
│ │                                                 │ │
│ │ [NAVIGATE] [START TRIP]                         │ │
│ │                                                 │ │
│ └─────────────────────────────────────────────────┘ │
│                                                     │
│ Driver taps [START TRIP]                            │
│                                                     │
│ System:                                             │
│ - Start GPS tracking (every 30 seconds)             │
│ - Update order status: CONFIRMED → IN_PROGRESS      │
│ - Record actual departure time: 06:00               │
│ - Start trip timer                                  │
│ - Enable route navigation                           │
│ - Send notification: "Budi started DP-001"          │
│                                                     │
│ ┌─────────────────────────────────────────────────┐ │
│ │ 🚛 TRIP IN PROGRESS                             │ │
│ │                                                 │ │
│ │ Current Location: Jl. Raya Serang KM 12         │ │
│ │ Speed: 45 km/h                                  │ │
│ │ Next: Arrive at loading point (8 min)           │ │
│ │                                                 │ │
│ │ [MAP VIEW]  [REPORT ISSUE]  [CALL DISPATCH]     │ │
│ │                                                 │ │
│ │ Trip Progress:                                  │ │
│ │ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │ │
│ │ ├─ ✅ Check-in (05:45)                          │ │
│ │ ├─ ✅ Depart depot (06:00)                      │ │
│ │ ├─ 🚛 En route to loading (06:08, current)      │ │
│ │ ├─ ⏳ Arrive loading (ETA 06:20)                │ │
│ │ ├─ ⏳ Loading process                           │ │
│ │ ├─ ⏳ Depart to destination                     │ │
│ │ └─ ⏳ Arrive destination (ETA 09:15)            │ │
│ │                                                 │ │
│ └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

---

## 5. SMS FALLBACK (No Smartphone)

### 5.1 SMS Format (160 Characters Max)

```
SMS TO DRIVER (Assignment):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
From: TMS Dispatcher
To: 0812-3456-7890 (Budi)

[TMS] ORDER BESOK 18/11 JAM 06:00
CIKUPA-BANDUNG 120KM
TRUCK B-1234-AB KENEK ANDI
UANG JASA 200RB UJ 85RB
REPLY: OK atau TOLAK
INFO: 021-55512345
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Driver Reply SMS:
"OK" → System auto-confirm order
"TOLAK" → System notify dispatcher, find replacement

If no reply after 4 hours:
Send reminder SMS:
"REMINDER: Konfirmasi order 18/11 JAM 06:00 
CIKUPA-BANDUNG. REPLY: OK/TOLAK"

If still no reply:
Phone call from dispatcher
```

### 5.2 SMS Notification (Trip Updates)

```
SMS SEQUENCE:

1. Assignment (D-1, 17:00):
   [TMS] ORDER BESOK 18/11 06:00 CIKUPA-BANDUNG
   TRUCK B-1234-AB. REPLY: OK/TOLAK

2. Driver confirm:
   Driver reply: "OK"
   
   System auto-reply:
   [TMS] TERIMA KASIH. BESOK 06:00 DATANG KE DEPOT.
   TRUCK B-1234-AB KENEK ANDI. INFO: 021-55512345

3. Reminder (D-Day, 05:00):
   [TMS] REMINDER: JAM 06:00 CIKUPA-BANDUNG
   TRUCK B-1234-AB. JANGAN LUPA!

4. Manual check-in (via SMS):
   Driver SMS: "MULAI DP-001"
   
   System reply:
   [TMS] TRIP STARTED. HATI-HATI DI JALAN.
   TUJUAN: PT INDOFOOD BANDUNG 022-7771234

5. Adjustment (if any):
   [TMS] PERUBAHAN: TUJUAN GANTI TASIKMALAYA
   CALL DISPATCHER: 021-55512345 URGENT!

6. Completion reminder:
   [TMS] SUDAH SAMPAI? REPLY: SELESAI
   ATAU TELEPON 021-55512345
```

---

## 6. WHATSAPP BUSINESS API (Semi-Formal)

### 6.1 WhatsApp Message Template

```
┌─────────────────────────────────────────────────────┐
│ TMS Dispatcher                                      │
│ Online                                              │
├─────────────────────────────────────────────────────┤
│                                                     │
│ 🚛 *ORDER BARU UNTUK ANDA*                          │
│                                                     │
│ Halo Pak Budi,                                      │
│ Ada order baru untuk besok:                         │
│                                                     │
│ 📅 *Tanggal:* Senin, 18 November 2025               │
│ 🕐 *Waktu:* 06:00 - 08:00                           │
│                                                     │
│ 📍 *Route:*                                         │
│ Cikupa → Bandung (120 km, 2.5 jam)                  │
│                                                     │
│ 🚚 *Truck:* B-1234-AB (Tronton 18t)                 │
│ 👷 *Kenek:* Andi (0815-7777-8888)                   │
│ 📦 *Barang:* Mie Instan 15 ton                      │
│                                                     │
│ 💰 *Uang Jasa:* Rp 200,000                          │
│ 💵 *Uang Jalan:* Rp 85,000 (ambil di kasir)         │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │
│                                                     │
│ 📍 *Detail Alamat:*                                 │
│                                                     │
│ *Loading:*                                          │
│ PT Indofood Factory - Cikupa                        │
│ Jl. Raya Serang KM 75                               │
│ CP: Pak Joko (0812-3333-4444)                       │
│ https://goo.gl/maps/xxxxx                           │
│                                                     │
│ *Unloading:*                                        │
│ PT Indofood DC - Bandung                            │
│ Jl. Soekarno Hatta No. 456                          │
│ CP: Bu Siti (0813-5555-6666)                        │
│ https://goo.gl/maps/yyyyy                           │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │
│                                                     │
│ ⚠️ *Catatan Penting:*                               │
│ - Barang fragile, hati-hati saat muat/bongkar       │
│ - Gunakan terpal jika hujan                         │
│ - Lapor ke security gate 2 saat loading             │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │
│                                                     │
│ 📄 *Surat Jalan (PDF):*                             │
│ 📎 DP-2025-11-18-001.pdf (123 KB)                   │
│ [Download]                                          │
│                                                     │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━   │
│                                                     │
│ *Silakan konfirmasi sebelum jam 20:00:*            │
│                                                     │
│ ┌────────────┐  ┌────────────┐                     │
│ │ ✅ TERIMA  │  │ ❌ TOLAK   │                     │
│ └────────────┘  └────────────┘                     │
│                                                     │
│ atau reply:                                         │
│ "OK" = Terima order                                 │
│ "TOLAK" = Tidak bisa                                │
│                                                     │
│ Terima kasih! 🙏                                    │
│                                                     │
│                                          Sent 17:05 │
└─────────────────────────────────────────────────────┘
```

### 6.2 Interactive Buttons (WA Business API)

```
WhatsApp Business API features:

1. Quick Reply Buttons:
   ┌─────────────────────────────────────┐
   │ Silakan pilih:                      │
   │                                     │
   │ [✅ Terima Order]                   │
   │ [❌ Tolak Order]                    │
   │ [ℹ️ Lihat Detail]                   │
   │ [📞 Hubungi Dispatcher]              │
   └─────────────────────────────────────┘

2. List Menu:
   Driver tap → Show list:
   - View Order Detail
   - Download Surat Jalan
   - View Route Map
   - Call Loading Point
   - Call Unloading Point
   - Reject Order (with reason)

3. Multimedia:
   - PDF attachment (Surat Jalan)
   - Image (Route map)
   - Location pin (Google Maps link)
   - Voice note (special instructions)

4. Two-way Chat:
   Driver can ask questions:
   "Pak, jam berapa harus sampai Bandung?"
   
   Dispatcher/Bot reply:
   "Target jam 09:15, maksimal 10:00 agar tetap on-time"
```

---

## 7. DATABASE DESIGN - ORDER ASSIGNMENT

### 7.1 ms_dispatch Enhancement (Add Communication Fields)

```sql
ALTER TABLE ms_dispatch ADD COLUMN notification_sent BIT DEFAULT 0;
ALTER TABLE ms_dispatch ADD COLUMN notification_sent_at DATETIME;
ALTER TABLE ms_dispatch ADD COLUMN notification_channel VARCHAR(50); -- APP, SMS, WHATSAPP, EMAIL, PHONE
ALTER TABLE ms_dispatch ADD COLUMN notification_status VARCHAR(50); -- SENT, DELIVERED, READ, FAILED

ALTER TABLE ms_dispatch ADD COLUMN driver_response VARCHAR(20); -- PENDING, CONFIRMED, REJECTED, NO_RESPONSE
ALTER TABLE ms_dispatch ADD COLUMN driver_responded_at DATETIME;
ALTER TABLE ms_dispatch ADD COLUMN driver_rejection_reason VARCHAR(500);

ALTER TABLE ms_dispatch ADD COLUMN response_deadline DATETIME; -- deadline untuk driver konfirmasi
ALTER TABLE ms_dispatch ADD COLUMN reminder_sent BIT DEFAULT 0;
ALTER TABLE ms_dispatch ADD COLUMN reminder_sent_at DATETIME;

ALTER TABLE ms_dispatch ADD COLUMN check_in_time DATETIME; -- driver check-in di depot
ALTER TABLE ms_dispatch ADD COLUMN check_in_location_lat DECIMAL(10,8);
ALTER TABLE ms_dispatch ADD COLUMN check_in_location_lng DECIMAL(11,8);
ALTER TABLE ms_dispatch ADD COLUMN pre_trip_inspection_status VARCHAR(20); -- OK, ISSUE_REPORTED
ALTER TABLE ms_dispatch ADD COLUMN cash_advance_received BIT DEFAULT 0;
ALTER TABLE ms_dispatch ADD COLUMN cash_advance_received_at DATETIME;
```

### 7.2 ms_driver_notification

```sql
CREATE TABLE ms_driver_notification (
    notification_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50) NOT NULL,
    driver_id VARCHAR(50) NOT NULL,
    
    -- Notification Detail
    notification_type VARCHAR(50), -- ORDER_ASSIGNMENT, ORDER_REMINDER, ORDER_ADJUSTMENT, TRIP_ALERT
    notification_channel VARCHAR(50), -- APP_PUSH, SMS, WHATSAPP, EMAIL, PHONE_CALL
    
    -- Message
    subject VARCHAR(200),
    message_text TEXT,
    message_html TEXT, -- for email/rich format
    
    -- Attachments
    has_attachment BIT DEFAULT 0,
    attachment_url VARCHAR(500), -- PDF surat jalan, route map, etc.
    
    -- Status Tracking
    sent_at DATETIME,
    delivered_at DATETIME, -- when message reaches device
    read_at DATETIME, -- when driver opens/reads
    responded_at DATETIME, -- when driver takes action
    
    status VARCHAR(20), -- PENDING, SENT, DELIVERED, READ, RESPONDED, FAILED
    failure_reason TEXT,
    
    -- Response
    driver_response VARCHAR(20), -- CONFIRMED, REJECTED
    driver_response_note TEXT,
    
    -- Retry Logic
    retry_count INT DEFAULT 0,
    max_retry INT DEFAULT 3,
    next_retry_at DATETIME,
    
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    
    INDEX idx_dispatch (dispatch_id),
    INDEX idx_driver (driver_id),
    INDEX idx_status (status)
)

-- Sample Data
INSERT INTO ms_driver_notification VALUES
('NOTIF001', 'DP-2025-11-18-001', 'DRV001',
'ORDER_ASSIGNMENT', 'APP_PUSH',
'Order Baru Untuk Besok', 
'Order baru: Cikupa-Bandung, 18 Nov 06:00, Truck B-1234-AB, Uang Jasa Rp 200k',
'<html>...</html>',
1, 'https://tms.com/pdf/surat-jalan-DP-001.pdf',
'2025-11-17 17:05:00', '2025-11-17 17:05:23', '2025-11-17 17:08:15', '2025-11-17 17:10:00',
'RESPONDED', NULL,
'CONFIRMED', NULL,
0, 3, NULL,
GETDATE());

INSERT INTO ms_driver_notification VALUES
('NOTIF002', 'DP-2025-11-18-001', 'DRV001',
'ORDER_REMINDER', 'SMS',
NULL,
'[TMS] REMINDER: BESOK 06:00 CIKUPA-BANDUNG TRUCK B-1234-AB',
NULL,
0, NULL,
'2025-11-18 05:00:00', '2025-11-18 05:00:15', NULL, NULL,
'DELIVERED', NULL,
NULL, NULL,
0, 3, NULL,
GETDATE());
```

### 7.3 ms_driver_check_in

```sql
CREATE TABLE ms_driver_check_in (
    check_in_id VARCHAR(50) PRIMARY KEY,
    dispatch_id VARCHAR(50) NOT NULL,
    driver_id VARCHAR(50) NOT NULL,
    
    -- Check-in Info
    check_in_time DATETIME NOT NULL,
    check_in_location_lat DECIMAL(10,8),
    check_in_location_lng DECIMAL(11,8),
    check_in_method VARCHAR(50), -- APP, MANUAL (dispatcher input), SMS
    
    -- Location Verification
    geofence_passed BIT DEFAULT 0, -- driver dalam radius depot?
    distance_from_depot_meters INT,
    
    -- Vehicle Verification
    assigned_vehicle_id VARCHAR(50),
    verified_vehicle_id VARCHAR(50), -- from QR scan
    vehicle_match BIT DEFAULT 0,
    
    -- Pre-trip Inspection
    inspection_tire_ok BIT DEFAULT 0,
    inspection_brake_ok BIT DEFAULT 0,
    inspection_light_ok BIT DEFAULT 0,
    inspection_fuel_ok BIT DEFAULT 0,
    inspection_document_ok BIT DEFAULT 0,
    inspection_status VARCHAR(20), -- ALL_OK, ISSUE_REPORTED
    inspection_notes TEXT,
    
    -- Helper Verification
    assigned_helper_id VARCHAR(50),
    helper_present BIT DEFAULT 0,
    helper_check_in_time DATETIME,
    
    -- Cash Advance
    cash_advance_amount DECIMAL(15,2),
    cash_received BIT DEFAULT 0,
    cash_received_time DATETIME,
    cashier_name VARCHAR(100),
    
    -- Photo Evidence (optional)
    photo_cash_url VARCHAR(500),
    photo_vehicle_url VARCHAR(500),
    
    created_at DATETIME DEFAULT GETDATE(),
    
    FOREIGN KEY (dispatch_id) REFERENCES ms_dispatch(dispatch_id),
    FOREIGN KEY (driver_id) REFERENCES ms_tms_driver(ms_tms_driver_id),
    FOREIGN KEY (assigned_vehicle_id) REFERENCES ms_vehicle(id),
    
    INDEX idx_dispatch (dispatch_id),
    INDEX idx_check_in_time (check_in_time)
)

-- Sample Data
INSERT INTO ms_driver_check_in VALUES
('CHECKIN001', 'DP-2025-11-18-001', 'DRV001',
'2025-11-18 05:45:00', -6.234567, 106.512345, 'APP',
1, 45, -- within 45 meters from depot center
'VH001', 'VH001', 1, -- vehicle match
1, 1, 1, 1, 1, 'ALL_OK', NULL,
'HLP001', 1, '2025-11-18 05:43:00',
85000, 1, '2025-11-18 05:40:00', 'Siti Kasir',
'https://tms.com/photos/cash_CHECKIN001.jpg',
'https://tms.com/photos/vehicle_CHECKIN001.jpg',
GETDATE());
```

---

## 8. NOTIFICATION LOGIC & WORKFLOW

### 8.1 Notification Sequence

```sql
-- Step 1: Dispatcher Assign Order (D-1, 17:00)
INSERT INTO ms_dispatch VALUES (...);

-- Step 2: Auto-send notification
EXEC sp_send_driver_notification 'DP-2025-11-18-001';

-- Procedure logic:
CREATE PROCEDURE sp_send_driver_notification
    @dispatch_id VARCHAR(50)
AS
BEGIN
    DECLARE @driver_id VARCHAR(50);
    DECLARE @driver_phone VARCHAR(20);
    DECLARE @driver_has_app BIT;
    
    -- Get driver info
    SELECT 
        @driver_id = driver_id,
        @driver_phone = driver_phone,
        @driver_has_app = has_mobile_app
    FROM ms_dispatch d
    JOIN ms_tms_driver dr ON d.driver_id = dr.ms_tms_driver_id
    WHERE d.dispatch_id = @dispatch_id;
    
    -- Determine channel
    IF @driver_has_app = 1 THEN
        -- Send APP push notification
        INSERT INTO ms_driver_notification VALUES
        ('NOTIF-' + NEWID(), @dispatch_id, @driver_id,
         'ORDER_ASSIGNMENT', 'APP_PUSH',
         'Order Baru Untuk Besok',
         'Order baru: [route], [date], [time], Uang Jasa Rp [amount]',
         ..., GETDATE(), NULL, NULL, NULL,
         'SENT', NULL, NULL, NULL,
         0, 3, NULL, GETDATE());
         
        -- Also send SMS as backup
        EXEC sp_send_sms @driver_phone, '[TMS] ORDER BESOK...';
    ELSE
        -- Send SMS only
        EXEC sp_send_sms @driver_phone, '[TMS] ORDER BESOK...';
    END IF
    
    -- Set response deadline (12 hours)
    UPDATE ms_dispatch
    SET response_deadline = DATEADD(HOUR, 12, GETDATE())
    WHERE dispatch_id = @dispatch_id;
END
```

### 8.2 Reminder Logic

```sql
-- Scheduled Job (run every hour)
CREATE PROCEDURE sp_send_order_reminders
AS
BEGIN
    -- Find orders dengan deadline < 2 hours dan belum di-remind
    DECLARE @dispatch_id VARCHAR(50);
    
    DECLARE cur CURSOR FOR
    SELECT dispatch_id
    FROM ms_dispatch
    WHERE driver_response = 'PENDING'
      AND response_deadline < DATEADD(HOUR, 2, GETDATE())
      AND reminder_sent = 0;
    
    OPEN cur;
    FETCH NEXT FROM cur INTO @dispatch_id;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Send reminder
        EXEC sp_send_driver_notification_reminder @dispatch_id;
        
        -- Mark as reminded
        UPDATE ms_dispatch
        SET reminder_sent = 1,
            reminder_sent_at = GETDATE()
        WHERE dispatch_id = @dispatch_id;
        
        FETCH NEXT FROM cur INTO @dispatch_id;
    END
    
    CLOSE cur;
    DEALLOCATE cur;
END
```

### 8.3 Timeout & Escalation

```sql
-- Scheduled Job (run every 30 minutes)
CREATE PROCEDURE sp_handle_order_timeout
AS
BEGIN
    -- Find orders yang deadline lewat, no response
    UPDATE ms_dispatch
    SET driver_response = 'NO_RESPONSE',
        status = 'PENDING_REASSIGNMENT'
    WHERE driver_response = 'PENDING'
      AND response_deadline < GETDATE();
    
    -- Notify dispatcher (urgent)
    INSERT INTO notifications VALUES
    ('URGENT: Driver ' + driver_name + ' tidak respond untuk order ' + dispatch_id);
    
    -- Auto-suggest replacement drivers
    EXEC sp_suggest_replacement_driver @dispatch_id;
END
```

---

## 9. DISPATCHER DASHBOARD - ORDER TRACKING

### 9.1 Assignment Status Board

```
┌────────────────────────────────────────────────────────────────────┐
│ ORDER ASSIGNMENT TRACKING - Week 47                                │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│ [Pending Response: 5] [Confirmed: 32] [Rejected: 2] [Timeout: 1]  │
│                                                                    │
│ ⏳ PENDING DRIVER RESPONSE (Action Required)                       │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ DP-002  │ 19/11 08:00  │ Joko (DRV002)  │ Sent 2h ago  │ ⚠️  │  │
│ │         │ Jkt-Sby      │ SMS + App      │ Deadline: 6h │     │  │
│ │         │ [RESEND] [CALL DRIVER] [REASSIGN]               │  │
│ ├──────────────────────────────────────────────────────────────┤  │
│ │ DP-005  │ 20/11 06:00  │ Siti (DRV003)  │ Sent 4h ago  │ 🟡  │  │
│ │         │ Cikupa-Bdg   │ App (Read)     │ Deadline: 8h │     │  │
│ │         │ Status: READ but not responded               │  │
│ │         │ [SEND REMINDER] [CALL DRIVER]                    │  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│ ✅ CONFIRMED ORDERS (Ready to Execute)                             │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ DP-001  │ 18/11 06:00  │ Budi (DRV001)  │ ✅ Confirmed  │     │  │
│ │         │ Cikupa-Bdg   │ 2 hours ago    │               │     │  │
│ │         │ [VIEW] [ADJUST] [CANCEL]                         │  │
│ ├──────────────────────────────────────────────────────────────┤  │
│ │ DP-003  │ 18/11 14:00  │ Agus (DRV004)  │ ✅ Confirmed  │     │  │
│ │         │ Bdg-Cikupa   │ 1 hour ago     │               │     │  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│ ❌ REJECTED ORDERS (Need Reassignment)                             │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ DP-004  │ 19/11 10:00  │ Wati (DRV005)  │ ❌ Rejected   │ 🔴  │  │
│ │         │ Jkt-Bdg      │ Reason: Sick   │               │     │  │
│ │         │ [FIND REPLACEMENT] [CANCEL ORDER]                │  │
│ │         │ Suggested: Tono (DRV006), Eko (DRV007)           │  │
│ └──────────────────────────────────────────────────────────────┘  │
│                                                                    │
│ ⏰ TIMEOUT (No Response - Action Required!)                        │
│ ┌──────────────────────────────────────────────────────────────┐  │
│ │ DP-006  │ 21/11 06:00  │ Budi (DRV008)  │ ⏰ No Response│ 🔴  │  │
│ │         │ Sby-Jkt      │ Sent 14h ago   │ OVERDUE 2h   │     │  │
│ │         │ [CALL NOW!] [REASSIGN URGENT]                    │  │
│ └──────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────┘
```

---

## 10. SUMMARY & RECOMMENDATIONS

### ✅ **PRIMARY METHOD: Mobile App** (Target 80% adoption)

**Features:**
- Complete order detail (route, load, team, payment)
- GPS route map & navigation
- Two-way confirmation (confirm/reject dengan reason)
- Real-time check-in & pre-trip inspection
- GPS tracking during trip
- Digital settlement (preview earning, track expenses)
- Offline mode (sync when online)

**Implementation:**
- Android app (min version 7.0)
- iOS app (min version 13.0)
- Push notification
- QR code scanning (vehicle verification)
- Camera (photo evidence)

---

### ✅ **BACKUP METHOD: SMS** (20% fallback)

**Use for:**
- Driver tanpa smartphone
- Driver di area no internet
- Emergency notification
- Reminder

**Format:**
- 160 char max
- Clear & concise
- Reply: OK/TOLAK
- Include dispatcher phone for detail

---

### ✅ **NOTIFICATION SEQUENCE:**

```
D-1, 17:00: Assignment sent (App + SMS)
          └─> Driver respond: CONFIRM/REJECT/NO_RESPONSE
              
If no response after 6 hours:
D-1, 23:00: Send reminder (App + SMS)

If no response after 12 hours:
D-Day, 05:00: Auto-escalate + Phone call
            └─> If still no response: TIMEOUT → Reassign

D-Day, 05:00: Morning reminder (for confirmed orders)
D-Day, 05:45: Driver check-in (App or manual)
D-Day, 06:00: Driver start trip
```

---

### ✅ **KEY DATABASE TABLES:**

1. **ms_dispatch** (enhanced): notification fields, driver response, check-in
2. **ms_driver_notification**: Complete notification log
3. **ms_driver_check_in**: Digital check-in record

---

### ✅ **BUSINESS RULES:**

- Response deadline: 12 hours from assignment
- Reminder: 2 hours before deadline
- Timeout action: Auto-escalate + reassign
- Check-in window: 15-30 min before departure
- Geofence radius: 100m from depot
- Pre-trip inspection: MANDATORY

**Apakah konsep driver order assignment ini sudah sesuai? Perlu penambahan untuk fitur lain?** 📱🚛

