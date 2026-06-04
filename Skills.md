# InfoIndi Backend — Architecture & Skills Reference

> **Tujuan dokumen ini:** Menjadi panduan lengkap bagi AI model (atau developer) untuk memahami arsitektur backend yang sudah ada, sehingga bisa membuat modul-modul baru secara konsisten tanpa melanggar konvensi yang sudah ditetapkan.

---

## 1. Tech Stack

| Komponen        | Teknologi                        |
|-----------------|----------------------------------|
| Framework       | Laravel 12                       |
| PHP Version     | ^8.2                             |
| Auth            | JWT (`tymon/jwt-auth` ^2.3)      |
| Database        | SQLite (development)             |
| API Format      | JSON REST API                    |
| Guard Default   | `api` (JWT driver)               |

---

## 2. Struktur Direktori Project

```
infoindi-be/
├── app/
│   ├── Exceptions/          # Custom exception & centralized handler
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/         # Semua API controller ada di sini
│   │   ├── Requests/        # Form Request dikelompokkan per modul
│   │   │   ├── Auth/
│   │   │   ├── Category/
│   │   │   ├── Post/
│   │   │   └── User/
│   │   └── Resources/       # API Resource (response transformer)
│   ├── Models/              # Eloquent models
│   ├── Providers/
│   ├── Services/            # Business logic layer
│   └── Traits/              # Shared traits (ApiResponse, HasQuery)
├── bootstrap/
│   └── app.php              # Application bootstrap & exception wiring
├── config/
│   ├── auth.php             # Guard config (api = jwt)
│   ├── cors.php             # CORS settings
│   └── jwt.php              # JWT configuration
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
└── routes/
    └── api.php              # Semua API route definition
```

---

## 3. Arsitektur Layer (Request → Response Flow)

```
Request → Route → Middleware (jwt.auth) → Controller → Service → Model → Database
                                              ↓
                                    FormRequest (validation)
                                              ↓
                                    Resource (response formatting)
```

### Penjelasan tiap layer:

1. **Route** (`routes/api.php`) — Mendefinisikan endpoint dan menghubungkan ke controller.
2. **Middleware** — `jwt.auth` untuk proteksi route, didaftarkan langsung di route group.
3. **Controller** — Tipis (thin controller), hanya menerima request, memanggil service, dan mengembalikan response menggunakan method dari `ApiResponse` trait.
4. **FormRequest** — Validasi input dipisahkan ke class tersendiri per action (Store/Update).
5. **Service** — Business logic ada di sini. Controller TIDAK boleh berisi logic bisnis.
6. **Model** — Representasi tabel database. Berisi `$fillable`, `$hidden`, `casts()`, dan definisi relationship.
7. **Resource** — Transformer untuk format response JSON. Mengontrol field apa saja yang dikirim ke client.

---

## 4. Konvensi Penamaan

### File & Class Naming

| Jenis              | Format                              | Contoh                              |
|---------------------|-------------------------------------|--------------------------------------|
| Model               | Singular PascalCase                 | `Category`, `Post`, `User`           |
| Controller          | `{Model}Controller`                 | `CategoryController`                 |
| Service             | `{Model}Service`                    | `CategoryService`, `UserService`     |
| FormRequest (store) | `Store{Model}Request`               | `StoreCategoryRequest`               |
| FormRequest (update)| `Update{Model}Request`              | `UpdateCategoryRequest`              |
| Resource            | `{Model}Resource`                   | `CategoryResource`, `PostResource`   |
| Migration           | `create_{table_name}_table`         | `create_categories_table`            |
| Seeder              | `{Model}Seeder`                     | `CategorySeeder`                     |
| Exception (custom)  | `{DeskripsiKondisi}Exception`       | `UserAlreadyExistException`          |

### Namespace Convention

| Jenis            | Namespace                                    |
|-------------------|----------------------------------------------|
| Controller (API)  | `App\Http\Controllers\Api`                   |
| FormRequest       | `App\Http\Requests\{ModuleName}`             |
| Resource          | `App\Http\Resources`                         |
| Service           | `App\Services`                               |
| Model             | `App\Models`                                 |
| Trait             | `App\Traits`                                 |
| Exception         | `App\Exceptions`                             |

### Directory untuk FormRequest

FormRequest dikelompokkan ke folder berdasarkan **nama modul**:
```
app/Http/Requests/
├── Auth/
│   ├── LoginRequest.php
│   └── RegisterRequest.php
├── Category/
│   ├── StoreCategoryRequest.php
│   └── UpdateCategoryRequest.php
├── Post/
│   └── StorePostRequest.php
└── User/
    ├── StoreProfileRequest.php
    └── UpdateProfileRequest.php
```

---

## 5. Standar API Response

Semua response menggunakan `ApiResponse` trait yang di-use di `BaseApiController`.

### Trait: `App\Traits\ApiResponse`

```php
// Response sukses (HTTP 200)
$this->success($data, 'Pesan sukses.');

// Response created (HTTP 201)
$this->created($data, 'Resource created successfully.');

// Response dengan pagination
$this->paginated($paginator, ResourceClass::class, 'Data fetched.');

// Response error
$this->error('Error message', 400, $optionalData);
```

### Format Response JSON

**Sukses:**
```json
{
  "status": true,
  "message": "Success",
  "data": { ... }
}
```

**Sukses dengan Pagination:**
```json
{
  "status": true,
  "message": "Success",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "last_page": 5,
    "total": 75,
    "from": 1,
    "to": 15
  }
}
```

**Error:**
```json
{
  "status": false,
  "message": "Error message"
}
```

**Validation Error:**
```json
{
  "status": false,
  "message": "Validation Error",
  "data": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

---

## 6. Controller Pattern

### BaseApiController

Semua API controller **WAJIB** extend `BaseApiController`, bukan `Controller` langsung.

```php
// app/Http/Controllers/Api/BaseApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class BaseApiController extends Controller
{
    use ApiResponse;
}
```

### Contoh Controller (Referensi: CategoryController)

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends BaseApiController
{
    private CategoryService $categoryService;

    // Inject service via constructor
    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }

    // INDEX: List dengan pagination
    public function index(Request $request)
    {
        $categories = $this->categoryService->getCategories($request);
        return $this->paginated(
            $categories,
            CategoryResource::class,
            'Categories fetched successfully.');
    }

    // STORE: Gunakan FormRequest untuk validasi
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        return $this->created($category, 'Category created successfully.');
    }

    // UPDATE: Route model binding + FormRequest
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());
        return $this->success($category, 'Category updated successfully.');
    }

    // DESTROY: Route model binding
    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return $this->success(null, 'Category deleted successfully.');
    }
}
```

### Aturan Controller:
1. **Selalu extend** `BaseApiController`
2. **Inject Service** melalui constructor (dependency injection)
3. **Jangan taruh business logic** di controller — delegasikan ke Service
4. **Gunakan FormRequest** untuk validasi, bukan manual `$request->validate()`
5. **Gunakan Route Model Binding** untuk parameter model (`Category $category`)
6. **Gunakan API Resource** untuk formatting response list/paginated
7. **Pesan response** selalu dalam bahasa Inggris, format: `"{Resource} {action} successfully."`

---

## 7. Service Pattern

### Trait: `App\Traits\HasQuery`

Service yang perlu fitur pencarian/filter/sorting/pagination, **use `HasQuery` trait**.

```php
trait HasQuery
{
    // Pencarian multi-kolom dengan LIKE
    public function applySearch(?Builder $query, ?string $search, array $columns): Builder

    // Sorting berdasarkan query param sort_by & sort_order
    public function applySort(Builder $query, Request $request, string $defaultSort = 'created_at', string $defaultOrder = 'desc'): Builder

    // Filter exact match berdasarkan query param
    public function applyFilter(Builder $query, Request $request, array $filters): Builder

    // Pagination berdasarkan query param per_page (default: 15)
    public function paginate(Builder $query, Request $request, int $defaultPerPage = 15): LengthAwarePaginator
}
```

### Contoh Service (Referensi: CategoryService)

```php
namespace App\Services;

use App\Models\Category;
use App\Traits\HasQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CategoryService
{
    use HasQuery;

    public function getCategories(Request $request): LengthAwarePaginator
    {
        $query = Category::query();
        $query = $this->applySearch($query, $request->get('search'), ['category_name']);
        return $this->paginate($query, $request);
    }

    public function findById($id)
    {
        return Category::find($id);
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category)
    {
        $category->delete();
        return true;
    }
}
```

### Aturan Service:
1. **Satu service per model/domain**
2. **Use `HasQuery`** jika butuh search, filter, sort, atau pagination
3. Method `getXxx(Request $request)` untuk list data — return `LengthAwarePaginator`
4. Method `createXxx(array $data)` — terima array dari `$request->validated()`
5. Method `update(Model $model, array $data)` — terima model instance + data
6. Method `delete(Model $model)` — terima model instance
7. Method `findById`, `findBySlug`, dll untuk query spesifik

---

## 8. Model Pattern

### Contoh Model (Referensi: Post)

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'post_name',
        'description',
        'banner_url',
        'view_count'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Aturan Model:
1. Selalu definisikan `$fillable` (BUKAN `$guarded`)
2. Gunakan `HasFactory` trait
3. Definisikan semua relationship sebagai method
4. Relationship naming: singular untuk belongsTo/hasOne, plural untuk hasMany/belongsToMany
5. Foreign key convention: `{model}_id` (contoh: `user_id`, `category_id`)

### Relationship yang Sudah Ada

```
User ──hasOne──▶ Profile
User ──hasMany──▶ Post
User ──belongsToMany──▶ User (followers via user_follow_user)
User ──belongsToMany──▶ User (following via user_follow_user)
Post ──belongsTo──▶ User
Post ──belongsTo──▶ Category
Category ──hasMany──▶ Post
Profile ──belongsTo──▶ User
```

---

## 9. Database Schema

### Tabel: `users`
| Kolom              | Tipe          | Constraint              |
|--------------------|---------------|-------------------------|
| id                 | bigint (auto) | PK                      |
| name               | string        | required                |
| email              | string        | unique                  |
| email_verified_at  | timestamp     | nullable                |
| password           | string        | required                |
| role               | enum          | 'user' \| 'admin', default: 'user' |
| remember_token     | string        | nullable                |
| timestamps         | -             | created_at, updated_at  |

### Tabel: `profiles`
| Kolom          | Tipe    | Constraint                                  |
|----------------|---------|---------------------------------------------|
| id             | bigint  | PK                                          |
| user_id        | FK      | constrained → users, onDelete cascade       |
| avatar         | string  | nullable                                    |
| phone          | string  | nullable                                    |
| website_url    | string  | nullable                                    |
| instagram_url  | string  | nullable                                    |
| bio            | text    | nullable                                    |
| is_mahasiswa   | boolean | default: true                               |
| timestamps     | -       | created_at, updated_at                      |

### Tabel: `categories`
| Kolom          | Tipe    | Constraint |
|----------------|---------|------------|
| id             | bigint  | PK         |
| category_name  | string  | unique     |
| slug           | string  | unique     |
| timestamps     | -       |            |

### Tabel: `posts`
| Kolom        | Tipe    | Constraint                              |
|--------------|---------|------------------------------------------|
| id           | bigint  | PK                                       |
| category_id  | FK      | constrained → categories, onDelete cascade |
| user_id      | FK      | constrained → users, onDelete cascade    |
| post_name    | string  | required                                 |
| description  | text    | required                                 |
| banner_url   | text    | required                                 |
| view_count   | integer | default: 0                               |
| timestamps   | -       |                                          |

### Tabel: `user_follow_user` (pivot)
| Kolom       | Tipe   | Constraint                             |
|-------------|--------|----------------------------------------|
| id          | bigint | PK                                     |
| follower_id | FK     | constrained → users, onDelete cascade  |
| followed_id | FK     | constrained → users, onDelete cascade  |
| timestamps  | -      |                                        |

---

## 10. FormRequest Pattern

### Contoh Store Request

```php
namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;  // Selalu true, auth ditangani middleware
    }

    public function rules(): array
    {
        return [
            'category_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => 'Category name is required.',
            // ... custom messages
        ];
    }
}
```

### Contoh Update Request (dengan exclude current ID)

```php
public function rules(): array
{
    return [
        'category_name' => 'string|max:255|unique:categories,category_name,' . $this->route('category')->id,
        'slug' => 'string|max:255|unique:categories,slug,' . $this->route('category')->id,
    ];
}
```

### Aturan FormRequest:
1. `authorize()` selalu return `true` — auth ditangani oleh middleware, bukan FormRequest
2. Store request: field wajib pakai `required`
3. Update request: field TIDAK pakai `required` (partial update diperbolehkan)
4. Update request: unique rule harus **exclude current ID** dengan pattern `unique:table,column,{id}`
5. Custom `messages()` method opsional tapi direkomendasikan
6. File disimpan di `app/Http/Requests/{ModuleName}/`

---

## 11. API Resource Pattern

### Contoh Resource

```php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_name' => $this->category_name,
            'slug' => $this->slug,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
```

### Resource dengan Nested Relation

```php
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            'role' => $this->role,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'followers_count' => $this->followers()->count(),
            'following_count' => $this->following()->count(),
        ];
    }
}
```

### Aturan Resource:
1. Selalu format `created_at` dengan `->toDateTimeString()`
2. Gunakan `$this->whenLoaded('relation')` untuk relasi opsional
3. Buat Resource class terpisah untuk nested objects
4. JANGAN expose field sensitif (password, token, dll)

---

## 12. Exception Handling

### Centralized Handler: `ApiExceptionHandler`

Didaftarkan di `bootstrap/app.php`, hanya aktif untuk request ke `api/*`:

```php
// bootstrap/app.php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (Throwable $e, $request) {
        if ($request->is('api/*')) {
            return (new ApiExceptionHandler())->handle($e, $request);
        }
    });
})
```

### Exception yang Ditangani:

| Exception                    | HTTP Code | Message              |
|------------------------------|-----------|----------------------|
| `ValidationException`        | 422       | Validation Error     |
| `AuthenticationException`    | 401       | Unauthenticated      |
| `UserAlreadyExistException`  | 409       | User already exist   |
| `InvalidCredentialsException`| 401       | Invalid credentials  |
| `ModelNotFoundException`     | 404       | Resource not found   |
| `NotFoundHttpException`      | 404       | Route not found      |
| `HttpExceptionInterface`     | varies    | Http Error           |
| Lainnya                      | 500       | Internal Server Error|

### Cara Buat Custom Exception Baru

```php
// app/Exceptions/NamaException.php
namespace App\Exceptions;

class NamaException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Pesan error.', 4xx);
    }
}
```

Lalu tambahkan handler-nya di `ApiExceptionHandler::handle()`:
```php
if ($e instanceof NamaException) {
    return $this->error($e->getMessage(), 4xx);
}
```

---

## 13. Routing Pattern

### Struktur Route (`routes/api.php`)

```php
// Public routes (tanpa auth)
Route::get('/health', [HealthController::class, 'index']);

// Auth routes (sebagian public, sebagian protected)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('jwt.auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Protected routes (semua butuh JWT)
Route::middleware('jwt.auth')->group(function () {

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        // ... route lainnya
    });

    // Untuk CRUD standar, gunakan Route::resource
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});
```

### Aturan Routing:
1. Semua route protected di-wrap dalam `Route::middleware('jwt.auth')`
2. Gunakan `Route::prefix()` untuk grouping berdasarkan resource
3. Gunakan `Route::resource()` untuk CRUD standar, dengan `->only()` untuk membatasi
4. Gunakan **Route Model Binding** (`{model}`) untuk parameter
5. URL menggunakan **plural noun** (contoh: `/users`, `/categories`)

---

## 14. Autentikasi (JWT)

### Konfigurasi
- Guard default: `api` (driver: `jwt`) — lihat `config/auth.php`
- Package: `tymon/jwt-auth` ^2.3
- Middleware: `jwt.auth`

### Penggunaan di Code
```php
// Login — generate token
$token = auth('api')->attempt($credentials);

// Ambil user yang sedang login
$user = auth('api')->user();

// Logout
auth('api')->logout();

// Login manual (setelah register)
$token = auth('api')->login($user);
```

### Auth Response Format
```json
{
  "status": true,
  "message": "User logged in successfully.",
  "data": {
    "user": { ... },
    "access_token": "eyJ..."
  }
}
```

---

## 15. CORS Configuration

```php
// config/cors.php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:5173'],
'allowed_headers' => ['*'],
```

Frontend dev server berjalan di `http://localhost:5173` (Vite default).

---

## 16. Query Parameters yang Didukung (via HasQuery Trait)

| Parameter   | Fungsi                             | Contoh                        |
|-------------|-------------------------------------|-------------------------------|
| `search`    | Pencarian LIKE di kolom tertentu   | `?search=john`                |
| `sort_by`   | Kolom untuk sorting                | `?sort_by=name`               |
| `sort_order`| Arah sorting (asc/desc)            | `?sort_order=asc`             |
| `per_page`  | Jumlah item per halaman            | `?per_page=10`                |
| `{filter}`  | Exact match filter                 | `?is_mahasiswa=1`             |

---

## 17. Checklist Membuat Modul Baru

Ketika ingin membuat modul CRUD baru (contoh: modul `Event`), ikuti langkah berikut:

### Step 1: Migration
```bash
php artisan make:migration create_events_table
```
- Definisikan kolom, foreign key, dan constraint
- Jalankan `php artisan migrate`

### Step 2: Model
Buat `app/Models/Event.php`:
- Definisikan `$fillable`
- Tambahkan `HasFactory` trait
- Definisikan relationships

### Step 3: Service
Buat `app/Services/EventService.php`:
- `use HasQuery` jika butuh search/filter/pagination
- Method: `getEvents()`, `createEvent()`, `update()`, `delete()`, `findById()`, dll

### Step 4: FormRequest
Buat folder `app/Http/Requests/Event/`:
- `StoreEventRequest.php` — rules dengan `required`
- `UpdateEventRequest.php` — rules tanpa `required`, unique exclude current ID

### Step 5: Resource
Buat `app/Http/Resources/EventResource.php`:
- Format `created_at` dengan `->toDateTimeString()`
- Gunakan `whenLoaded()` untuk relasi

### Step 6: Controller
Buat `app/Http/Controllers/Api/EventController.php`:
- Extend `BaseApiController`
- Inject `EventService` via constructor
- Method: `index()`, `store()`, `update()`, `destroy()`, `show()`

### Step 7: Routes
Tambahkan di `routes/api.php` dalam group `jwt.auth`:
```php
Route::resource('events', EventController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy']);
```

### Step 8: Seeder (opsional)
Buat `database/seeders/EventSeeder.php` dan daftarkan di `DatabaseSeeder.php`.

---

## 18. Modul yang Sudah Ada

### ✅ Auth Module
- Register, Login, Logout, Me (current user)
- JWT-based authentication
- Custom exceptions: `UserAlreadyExistException`, `InvalidCredentialsException`

### ✅ User Module
- CRUD user
- Profile management (one-to-one)
- Follow/Unfollow system (many-to-many self-referencing)
- Followers & Following list
- Search by name/email, filter by is_mahasiswa

### ✅ Category Module
- Full CRUD (index, store, update, destroy)
- Search by category_name
- Pagination support

### 🔲 Post Module (Skeleton Only)
- Model & migration sudah ada
- Controller (`PostController`) masih kosong
- `StorePostRequest` sudah ada
- Resource (`PostResource`) sudah ada
- **Belum ada**: Service, route registration, implementasi controller methods

---

## 19. Anti-Pattern yang Harus Dihindari

1. ❌ **Jangan taruh business logic di Controller** — selalu di Service
2. ❌ **Jangan validasi manual di Controller** — selalu pakai FormRequest
3. ❌ **Jangan return response tanpa `ApiResponse` trait** — pakai `$this->success()`, `$this->error()`, dll
4. ❌ **Jangan extend `Controller` langsung** — extend `BaseApiController`
5. ❌ **Jangan pakai `$guarded`** — selalu definisikan `$fillable` di Model
6. ❌ **Jangan hardcode status code di response** — gunakan method yang sesuai (`success()` = 200, `created()` = 201)
7. ❌ **Jangan query database di Controller** — delegasikan ke Service
8. ❌ **Jangan buat custom exception tanpa mendaftarkannya** di `ApiExceptionHandler`
