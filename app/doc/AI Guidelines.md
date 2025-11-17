# AI Development Guidelines - AccAdmin Accounting System

## 🎯 Tujuan
Panduan ini memastikan pengembangan aplikasi TMS yang aman, stabil, bebas error, dan mudah dimaintain oleh AI atau developer.

---

## � ATURAN KRITIS - TIDAK BOLEH DILANGGAR

### ⛔ DILARANG KERAS (FORBIDDEN ACTIONS)

**AI TIDAK BOLEH DAN TIDAK AKAN PERNAH:**

1. **❌ MENGHAPUS DATABASE**
   - Tidak boleh menjalankan `DROP DATABASE`
   - Tidak boleh menjalankan `DELETE DATABASE`
   - Tidak boleh menghapus connection database di `.env`

2. **❌ MENGHAPUS TABEL**
   - Tidak boleh menjalankan `DROP TABLE`
   - Tidak boleh menjalankan `TRUNCATE TABLE`
   - Tidak boleh membuat migration dengan `Schema::drop()`
   - Tidak boleh menjalankan migration yang drop tabel tanpa persetujuan eksplisit

3. **❌ MENGHAPUS DATA (MASS DELETE)**
   - Tidak boleh menjalankan `DELETE FROM table` tanpa WHERE clause
   - Tidak boleh menjalankan `Model::truncate()`
   - Tidak boleh menjalankan `DB::table()->delete()` tanpa where
   - Tidak boleh menghapus data historis/transaksi
   - **GUNAKAN SOFT DELETE** (rec_status = 'D') sebagai gantinya

4. **❌ MENGUBAH STRUKTUR PRODUCTION DATABASE**
   - Tidak boleh ALTER TABLE di production tanpa backup
   - Tidak boleh menghapus kolom yang sudah ada data
   - Tidak boleh mengubah primary key atau foreign key yang sudah digunakan

**✅ YANG BOLEH DILAKUKAN:**
- Membuat file migration baru (tanpa menjalankan)
- Menghapus file model/controller/view (bukan database)
- Soft delete dengan `rec_status = 'D'`
- Membuat backup sebelum perubahan struktur
- Menambah kolom baru (tanpa menghapus yang lama)

**JIKA USER MEMINTA HAPUS DATABASE/TABEL:**
```
Response AI: "Untuk keamanan data, saya tidak dapat menghapus database atau tabel. 
Jika Anda ingin:
1. Hapus DATA: Saya bisa gunakan soft delete (rec_status='D')
2. Hapus MODEL: Saya bisa hapus file PHP di app/Models
3. Hapus STRUKTUR: Silakan backup manual dulu, lalu konfirmasi eksplisit

Apakah Anda ingin melakukan salah satu dari opsi di atas?"
```

---

## �📋 PRINSIP UTAMA

### 1. KEAMANAN MAKSIMAL (Security First)
- **NEVER** hardcode credentials, API keys, atau secrets
- Selalu gunakan environment variables (.env)
- Validasi SEMUA input dari user tanpa kecuali
- Gunakan prepared statements untuk database queries
- Implementasi CSRF protection (default Laravel)
- Sanitize output untuk mencegah XSS
- Implementasi rate limiting untuk API
- Gunakan HTTPS di production

### 2. VALIDASI DATA KETAT
```php
// ✅ BENAR - Validasi lengkap
public function store(Request $request)
{
    $validated = $request->validate([
        'amount' => 'required|numeric|min:0|max:999999999.99',
        'date' => 'required|date|before_or_equal:today',
        'description' => 'required|string|max:500',
        'account_id' => 'required|exists:accounts,id'
    ]);
    
    // Lanjutkan proses...
}

// ❌ SALAH - Tanpa validasi
public function store(Request $request)
{
    Account::create($request->all()); // BERBAHAYA!
}
```

### 3. ERROR HANDLING KOMPREHENSIF
```php
// ✅ BENAR - Try-catch dengan logging
public function processTransaction(Request $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validate([...]);
        
        $transaction = Transaction::create($validated);
        $this->updateAccountBalance($transaction);
        
        DB::commit();
        
        Log::info('Transaction processed', ['transaction_id' => $transaction->id]);
        return response()->json(['success' => true, 'data' => $transaction]);
        
    } catch (ValidationException $e) {
        DB::rollBack();
        Log::warning('Validation failed', ['errors' => $e->errors()]);
        return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Transaction failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
    }
}
```

---

## 🗄️ DATABASE BEST PRACTICES

### Migration Rules
```php
// ✅ BENAR - Lengkap dengan indexes dan constraints
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('account_id')->constrained()->onDelete('restrict');
    $table->decimal('amount', 15, 2); // Presisi untuk uang
    $table->enum('type', ['debit', 'credit']);
    $table->date('transaction_date');
    $table->string('description', 500);
    $table->string('reference_no', 50)->nullable()->unique();
    $table->timestamps();
    $table->softDeletes(); // Soft delete untuk audit trail
    
    // Indexes untuk performa
    $table->index('transaction_date');
    $table->index('account_id');
    $table->index(['account_id', 'transaction_date']);
});
```

### Model Protection
```php
// ✅ BENAR - Protected model
class Transaction extends Model
{
    use SoftDeletes;
    
    // Whitelist fields yang boleh di-fill
    protected $fillable = [
        'account_id',
        'amount',
        'type',
        'transaction_date',
        'description',
        'reference_no'
    ];
    
    // Blacklist fields yang tidak boleh di-fill
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    // Cast tipe data
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];
    
    // Validation rules
    public static function rules()
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01|max:999999999.99',
            'type' => 'required|in:debit,credit',
            'transaction_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:500',
            'reference_no' => 'nullable|string|max:50|unique:transactions,reference_no',
        ];
    }
}
```

---

## 🔐 AUTHENTICATION & AUTHORIZATION

### Middleware Protection
```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('accounting')->group(function () {
        
        // Admin only routes
        Route::middleware('can:manage-accounts')->group(function () {
            Route::resource('accounts', AccountController::class);
        });
        
        // Accountant routes
        Route::middleware('can:create-transactions')->group(function () {
            Route::resource('transactions', TransactionController::class);
        });
        
        // View only routes
        Route::middleware('can:view-reports')->group(function () {
            Route::get('reports', [ReportController::class, 'index']);
        });
    });
});
```

### Policy Implementation
```php
// app/Policies/TransactionPolicy.php
class TransactionPolicy
{
    public function update(User $user, Transaction $transaction)
    {
        // Hanya creator atau admin yang bisa update
        return $user->id === $transaction->created_by 
            || $user->hasRole('admin');
    }
    
    public function delete(User $user, Transaction $transaction)
    {
        // Tidak boleh delete transaksi yang sudah di-close
        if ($transaction->is_closed) {
            return false;
        }
        
        return $user->hasRole('admin');
    }
}
```

---

## 💾 DATA INTEGRITY UNTUK ACCOUNTING

### Atomic Transactions
```php
// ✅ BENAR - Gunakan database transactions untuk operasi multi-step
public function createJournalEntry(array $data)
{
    DB::beginTransaction();
    try {
        // 1. Create journal entry header
        $journal = JournalEntry::create([
            'date' => $data['date'],
            'description' => $data['description'],
            'reference' => $this->generateReference(),
        ]);
        
        $totalDebit = 0;
        $totalCredit = 0;
        
        // 2. Create journal entry lines
        foreach ($data['lines'] as $line) {
            JournalEntryLine::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $line['account_id'],
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
            ]);
            
            $totalDebit += $line['debit'] ?? 0;
            $totalCredit += $line['credit'] ?? 0;
        }
        
        // 3. Validasi balanced entry
        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \Exception('Journal entry must be balanced');
        }
        
        // 4. Update account balances
        $this->updateAccountBalances($journal);
        
        DB::commit();
        return $journal;
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Journal entry creation failed', [
            'error' => $e->getMessage(),
            'data' => $data
        ]);
        throw $e;
    }
}
```

### Data Validation untuk Accounting
```php
// Custom validation rules
class BalancedJournalRule implements Rule
{
    public function passes($attribute, $value)
    {
        $totalDebit = collect($value)->sum('debit');
        $totalCredit = collect($value)->sum('credit');
        
        return abs($totalDebit - $totalCredit) < 0.01; // Toleransi 1 sen
    }
    
    public function message()
    {
        return 'Journal entry must be balanced (debit = credit).';
    }
}

// Usage
$request->validate([
    'lines' => ['required', 'array', 'min:2', new BalancedJournalRule],
    'lines.*.account_id' => 'required|exists:accounts,id',
    'lines.*.debit' => 'required|numeric|min:0',
    'lines.*.credit' => 'required|numeric|min:0',
]);
```

---

## 🧪 TESTING REQUIREMENTS

### Feature Tests (WAJIB)
```php
// tests/Feature/TransactionTest.php
class TransactionTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_creates_transaction_with_valid_data()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        
        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'account_id' => $account->id,
            'amount' => 100.00,
            'type' => 'debit',
            'transaction_date' => now()->toDateString(),
            'description' => 'Test transaction',
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'amount' => 100.00,
        ]);
    }
    
    /** @test */
    public function it_prevents_negative_amounts()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create();
        
        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'account_id' => $account->id,
            'amount' => -100.00, // Invalid
            'type' => 'debit',
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);
    }
    
    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->postJson('/api/transactions', []);
        $response->assertStatus(401);
    }
}
```

---

## 📝 LOGGING & MONITORING

### Comprehensive Logging
```php
// app/Services/AuditLogger.php
class AuditLogger
{
    public static function logTransaction(string $action, Transaction $transaction, ?User $user = null)
    {
        Log::channel('audit')->info('Transaction action', [
            'action' => $action, // create, update, delete
            'transaction_id' => $transaction->id,
            'user_id' => $user?->id ?? auth()->id(),
            'user_email' => $user?->email ?? auth()->user()?->email,
            'amount' => $transaction->amount,
            'account_id' => $transaction->account_id,
            'timestamp' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    public static function logFailedAction(string $action, array $data, \Exception $e)
    {
        Log::channel('audit')->error('Action failed', [
            'action' => $action,
            'data' => $data,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
```

### Configure Logging Channels
```php
// config/logging.php
'channels' => [
    'audit' => [
        'driver' => 'daily',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
        'days' => 90, // Keep 90 days untuk audit
    ],
    
    'transactions' => [
        'driver' => 'daily',
        'path' => storage_path('logs/transactions.log'),
        'level' => 'info',
        'days' => 365, // Keep 1 year untuk transactions
    ],
],
```

---

## 🚀 PERFORMANCE OPTIMIZATION

### Query Optimization
```php
// ✅ BENAR - Eager loading
public function getAccountWithTransactions($accountId)
{
    return Account::with(['transactions' => function ($query) {
        $query->orderBy('transaction_date', 'desc')
              ->limit(100);
    }])->findOrFail($accountId);
}

// ❌ SALAH - N+1 problem
public function index()
{
    $accounts = Account::all();
    foreach ($accounts as $account) {
        $account->transactions; // N+1 query!
    }
}
```

### Caching Strategy
```php
// Cache untuk data yang jarang berubah
public function getChartOfAccounts()
{
    return Cache::remember('chart_of_accounts', 3600, function () {
        return Account::with('parent')
            ->orderBy('code')
            ->get();
    });
}

// Clear cache saat data berubah
public function store(Request $request)
{
    $account = Account::create($validated);
    Cache::forget('chart_of_accounts');
    return $account;
}
```

### Database Indexes
```php
// Migration untuk performa
Schema::table('transactions', function (Blueprint $table) {
    // Composite index untuk queries yang sering
    $table->index(['account_id', 'transaction_date']);
    $table->index(['type', 'transaction_date']);
    
    // Full-text search index
    $table->fullText(['description', 'reference_no']);
});
```

---

## 🛡️ INPUT VALIDATION PATTERNS

### Request Classes
```php
// app/Http/Requests/StoreTransactionRequest.php
class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Transaction::class);
    }
    
    public function rules()
    {
        return [
            'account_id' => [
                'required',
                'exists:accounts,id',
                Rule::exists('accounts', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/', // Max 2 decimal places
            ],
            'type' => 'required|in:debit,credit',
            'transaction_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subYear()->toDateString(),
            ],
            'description' => 'required|string|min:5|max:500',
            'reference_no' => 'nullable|string|max:50|unique:transactions,reference_no',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ];
    }
    
    public function messages()
    {
        return [
            'amount.regex' => 'Amount must have maximum 2 decimal places.',
            'transaction_date.after_or_equal' => 'Transaction date cannot be more than 1 year in the past.',
        ];
    }
    
    protected function prepareForValidation()
    {
        // Sanitize input
        $this->merge([
            'description' => strip_tags($this->description),
            'amount' => (float) $this->amount,
        ]);
    }
}
```

---

## 🔄 API RESPONSE STANDARDS

### Consistent Response Format
```php
// app/Traits/ApiResponse.php
trait ApiResponse
{
    protected function successResponse($data = null, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }
    
    protected function errorResponse($message, $errors = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }
    
    protected function validationErrorResponse($errors)
    {
        return $this->errorResponse(
            'Validation failed',
            $errors,
            422
        );
    }
}

// Usage in controller
class TransactionController extends Controller
{
    use ApiResponse;
    
    public function store(StoreTransactionRequest $request)
    {
        try {
            $transaction = Transaction::create($request->validated());
            return $this->successResponse(
                $transaction,
                'Transaction created successfully',
                201
            );
        } catch (\Exception $e) {
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to create transaction', null, 500);
        }
    }
}
```

---

## 🔧 CONFIGURATION MANAGEMENT

### Environment Variables
```env
# .env.example - Template untuk developer baru

# App
APP_NAME=AccAdmin
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=accadmin
DB_USERNAME=root
DB_PASSWORD=

# Security
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Rate Limiting
RATE_LIMIT_PER_MINUTE=60
RATE_LIMIT_LOGIN_PER_MINUTE=5

# Backup
BACKUP_ENABLED=true
BACKUP_SCHEDULE=daily
BACKUP_RETENTION_DAYS=30

# Email
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

# Accounting Settings
FISCAL_YEAR_START_MONTH=1
DEFAULT_CURRENCY=IDR
DECIMAL_PLACES=2
```

---

## 📚 DOCUMENTATION REQUIREMENTS

### Code Documentation
```php
/**
 * Process journal entry and update account balances
 * 
 * This method handles the complete journal entry process including:
 * - Validation of balanced entries (debit = credit)
 * - Creating journal entry header and lines
 * - Updating affected account balances
 * - Logging for audit trail
 *
 * @param array $data Journal entry data containing date, description, and lines
 * @return JournalEntry The created journal entry
 * @throws \Exception When journal entry is not balanced
 * @throws \Illuminate\Database\QueryException When database operation fails
 * 
 * @example
 * $data = [
 *     'date' => '2025-11-06',
 *     'description' => 'Payment received',
 *     'lines' => [
 *         ['account_id' => 1, 'debit' => 1000, 'credit' => 0],
 *         ['account_id' => 2, 'debit' => 0, 'credit' => 1000],
 *     ]
 * ];
 * $journal = $service->createJournalEntry($data);
 */
public function createJournalEntry(array $data): JournalEntry
{
    // Implementation...
}
```

---

## ⚠️ COMMON PITFALLS TO AVOID

### ❌ NEVER DO THIS
```php
// 1. Mass assignment tanpa protection
Account::create($request->all()); // BAHAYA!

// 2. Raw queries tanpa binding
DB::select("SELECT * FROM accounts WHERE id = " . $id); // SQL INJECTION!

// 3. Hardcoded credentials
$conn = mysqli_connect('localhost', 'root', 'password123'); // SALAH!

// 4. Tanpa error handling
public function delete($id) {
    Account::find($id)->delete(); // Crash jika tidak ada!
}

// 5. Tanpa transaction untuk operasi multi-step
public function transfer($from, $to, $amount) {
    Account::find($from)->decrement('balance', $amount); // Bisa inconsistent
    Account::find($to)->increment('balance', $amount);   // jika yang kedua fail!
}

// 6. N+1 Query Problem
foreach (Account::all() as $account) {
    echo $account->transactions->count(); // Query di setiap loop!
}

// 7. Expose sensitive data
return User::all(); // Include password hash, tokens, etc!

// 8. Tanpa authorization check
public function update($id) {
    Transaction::find($id)->update([...]); // Siapa saja bisa update!
}
```

### ✅ CORRECT WAY
```php
// 1. Protected mass assignment
Account::create($request->validated());

// 2. Prepared statements (automatic dengan Eloquent)
Account::where('id', $id)->first();

// 3. Environment variables
config('database.connections.mysql.password')

// 4. Proper error handling
public function delete($id) {
    $account = Account::findOrFail($id);
    if ($account->transactions()->exists()) {
        throw new \Exception('Cannot delete account with transactions');
    }
    $account->delete();
}

// 5. Database transactions
DB::transaction(function () use ($from, $to, $amount) {
    Account::findOrFail($from)->decrement('balance', $amount);
    Account::findOrFail($to)->increment('balance', $amount);
});

// 6. Eager loading
Account::with('transactions')->get();

// 7. Resource classes
return AccountResource::collection(Account::all());

// 8. Authorization
public function update(Request $request, $id) {
    $transaction = Transaction::findOrFail($id);
    $this->authorize('update', $transaction);
    $transaction->update($request->validated());
}
```

---

## 🎯 CHECKLIST SEBELUM COMMIT

### Pre-Commit Checklist
- [ ] Semua input ter-validasi dengan benar
- [ ] Error handling sudah diimplementasi
- [ ] Database transactions digunakan untuk operasi multi-step
- [ ] Tidak ada hardcoded credentials atau secrets
- [ ] Query sudah dioptimasi (no N+1)
- [ ] Authorization checks sudah ditambahkan
- [ ] Logging untuk audit trail
- [ ] Tests sudah dibuat dan passing
- [ ] Code sudah didokumentasi
- [ ] Tidak ada console.log atau dd() yang tertinggal
- [ ] Migration bisa di-rollback
- [ ] Response format konsisten
- [ ] Cache strategy sudah dipertimbangkan

### Pre-Deploy Checklist
- [ ] APP_DEBUG=false di production
- [ ] Database backup sudah disetup
- [ ] Error monitoring sudah dikonfigurasi
- [ ] Rate limiting sudah aktif
- [ ] HTTPS enforced
- [ ] Environment variables sudah dicek
- [ ] Migration tested di staging
- [ ] Rollback plan sudah siap
- [ ] Performance testing sudah dilakukan
- [ ] Security audit sudah dilakukan

---

## 🆘 TROUBLESHOOTING GUIDE

### Common Issues

#### 1. Transaction Imbalance
```php
// Problem: Debit ≠ Credit
// Solution: Add validation
if (abs($totalDebit - $totalCredit) > 0.01) {
    throw new \Exception("Unbalanced entry: Debit=$totalDebit, Credit=$totalCredit");
}
```

#### 2. Floating Point Precision
```php
// Problem: 0.1 + 0.2 !== 0.3 in floating point
// Solution: Use bcmath or round properly
$total = round($amount1 + $amount2, 2);
// Or use string math for absolute precision
$total = bcadd((string)$amount1, (string)$amount2, 2);
```

#### 3. Concurrent Transactions
```php
// Problem: Race condition pada balance update
// Solution: Use pessimistic locking
DB::transaction(function () use ($accountId, $amount) {
    $account = Account::where('id', $accountId)
        ->lockForUpdate()
        ->first();
    $account->balance += $amount;
    $account->save();
});
```

#### 4. Memory Issues with Large Datasets
```php
// Problem: Loading ribuan records
// Solution: Use chunking
Account::chunk(100, function ($accounts) {
    foreach ($accounts as $account) {
        // Process...
    }
});
```

---

## 📖 RESOURCES

### Laravel Documentation
- Security: https://laravel.com/docs/security
- Validation: https://laravel.com/docs/validation
- Database: https://laravel.com/docs/database
- Testing: https://laravel.com/docs/testing

### Accounting Best Practices
- Double-entry bookkeeping principles
- GAAP compliance
- Audit trail requirements
- Financial reporting standards

### Tools
- Laravel Debugbar (development)
- Laravel Telescope (monitoring)
- PHPStan (static analysis)
- PHP CS Fixer (code style)

---

## 🔄 MAINTENANCE

### Regular Tasks
- **Daily**: Monitor logs untuk errors
- **Weekly**: Review failed transactions
- **Monthly**: Database optimization dan backup verification
- **Quarterly**: Security audit dan dependency updates

### Database Maintenance
```php
// Optimize tables
DB::statement('OPTIMIZE TABLE transactions, accounts, journal_entries');

// Analyze slow queries
DB::enableQueryLog();
// ... run operations
dd(DB::getQueryLog());
```

---

## ✅ SUMMARY

### Golden Rules
1. **Security First**: Validasi semua input, never trust user data
2. **Fail Safe**: Gunakan transactions, proper error handling
3. **Audit Everything**: Log semua perubahan finansial
4. **Test Everything**: Unit tests + Feature tests
5. **Document Everything**: Code comments + API docs
6. **Performance Matters**: Optimize queries, use caching
7. **Consistency**: Follow coding standards
8. **Backup Always**: Regular backups + test restore

### Remember
> "Better to prevent than to cure. Better to log than to wonder. Better to test than to hope."

Aplikasi accounting tidak boleh ada error karena berhubungan dengan uang dan keputusan bisnis. Follow guideline ini dengan ketat!

---

**Last Updated**: November 6, 2025
**Version**: 1.0
**Maintainer**: AccAdmin Development Team
