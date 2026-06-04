# Frontend Integration Guide (InfoIndi)

Dokumen ini ditujukan untuk **Frontend Agent/Developer** sebagai panduan integrasi API InfoIndi. Backend menggunakan Laravel 11 dengan JWT Authentication, dan Frontend menggunakan **Axios**, **TanStack Query (React Query)**, dan **Zustand**.

---

## 1. Global Setup & Konfigurasi

### A. State Management (Zustand)
Gunakan Zustand untuk mengelola state autentikasi secara global.
- **State yang perlu disimpan:** `token`, `user` (data profil & role), `isAuthenticated`.
- **Aksi (Actions):** `login(token, user)`, `logout()`, `updateUser(data)`.
- **Persistensi:** Gunakan middleware `persist` dari Zustand untuk menyimpan `token` dan `user` di `localStorage` agar sesi bertahan setelah refresh.

### B. HTTP Client (Axios Interceptors)
Buat instance Axios khusus (`apiClient`) dengan opsi `withCredentials: true` dan interceptor:
- **withCredentials:** Aktifkan opsi `withCredentials: true` secara global pada Axios instance (Backend CORS sudah dikonfigurasi dengan `'supports_credentials' => true` dan mengizinkan origin `http://localhost:5173`).
- **Request Interceptor:** Ambil JWT `token` dari Zustand store dan sisipkan ke header: `Authorization: Bearer <token>`.
- **Response Interceptor:** 
  - Tangani error global. 
  - Jika menerima **HTTP 401 (Unauthorized)**, otomatis panggil aksi `logout()` dari Zustand dan arahkan ke halaman Login.
  - Jika menerima **HTTP 403 (Forbidden)**, ini menandakan `UnauthorizedActionException` (bukan admin) atau `UserBannedException` (akun di-ban). Jika response message mengindikasikan banned, paksa logout dan tampilkan pesan "Akun Anda ditangguhkan".

### C. Data Fetching (TanStack Query)
- Gunakan `useQuery` untuk semua operasi GET (fetching list, detail).
- Gunakan `useMutation` untuk operasi POST, PUT, DELETE.
- Manfaatkan fitur **Invalidation** (`queryClient.invalidateQueries`) setelah mutasi berhasil untuk me-refresh data secara otomatis (misal: setelah mutasi `likePost`, invalidate query `['posts']` atau `['post', id]`).

---

## 2. API Endpoints & Panduan Penggunaan

Semua endpoint memiliki prefix `/api/`.

### Autentikasi (Public & Protected)
- `POST /api/auth/register` - Daftar user baru.
- `POST /api/auth/login` - Login. **Penting:** Response akan berisi `token` dan data user. Simpan ini ke Zustand.
- `GET /api/auth/me` - (Protected) Ambil data user yang sedang login.
- `POST /api/auth/logout` - (Protected) Invalidate JWT token.

### Fitur Publik (Tidak Membutuhkan Login)
- `GET /api/posts` - Fetch list posts. Mendukung query params: `?search=xyz`, `?category_id=...`, `?user_id=...`. Gunakan `useQuery` dengan *pagination*. (Public route, opsional mengirim `Authorization` header jika ingin mendeteksi status `is_liked` user).
- `GET /api/posts/{id}` - Fetch detail post. (Public route, opsional mengirim `Authorization` header).

### Fitur User Terproteksi (Membutuhkan Login)
*Header wajib: `Authorization: Bearer <token>`*

#### Posts (CRUD & Actions)
- `POST /api/posts` - Buat post baru. 
  - **⚠️ PERHATIAN (Multipart):** Endpoint ini menerima file upload. Anda **WAJIB** mengirim data menggunakan `FormData`, BUKAN JSON.
  - Field form: `post_name`, `description`, `category_id`, `banner_image` (File), `images[]` (Array of Files).
- `PUT /api/posts/{id}` - Update post. (Bisa menggunakan `FormData` atau JSON jika tidak ada update gambar. Method `PUT` dengan `FormData` di Laravel kadang bermasalah, jika gagal gunakan `POST` dan tambahkan `_method=PUT` di FormData).
- `DELETE /api/posts/{id}` - Hapus post.
- `DELETE /api/posts/{post_id}/images/{image_id}` - Hapus satu gambar spesifik (additional image).

#### Interaksi Post (Like & Report)
- `POST /api/posts/{id}/like` - Toggle like/unlike. (Gunakan `useMutation`, response berisi `is_liked` dan `likes_count`. Update cache TanStack Query secara optimis).
- `GET /api/posts/{id}/likes` - Lihat daftar user yang menyukai post.
- `POST /api/posts/{id}/report` - Report post. Body: `{ reason: "spam/inappropriate/dll", additional_info: "..." }`.

#### Users & Follow
- `GET /api/users/{id}/profile` - Lihat profil user.
- `POST /api/users/{id}/follow/{target_id}` - Follow user.
- `POST /api/users/{id}/unfollow/{target_id}` - Unfollow user.
- `PUT /api/users/{id}/profile` - Update profil sendiri.

---

## 3. Fitur Admin (Membutuhkan Role 'admin')

Rute-rute ini akan mengembalikan **403 Forbidden** jika dipanggil oleh user biasa. Front-end harus menyembunyikan menu admin jika `user.role !== 'admin'`.

#### Dashboard & Analytics
- `GET /api/admin/dashboard` - Mengambil semua data statistik.
  - *Response:* `counts` (summary angka), `user_growth` (grafik bulan), `user_activity` (pendaftar vs pembuat post), `top_users` (leaderboard). Cocok diikat dengan *charting library* (misal Recharts/Chart.js).

#### User Management
- `GET /api/admin/users` - List semua user (untuk tabel admin). Mendukung pagination dan filter.
- `GET /api/admin/users/{id}` - Detail komprehensif user beserta statistiknya.
- `POST /api/admin/users/{id}/ban` - Suspend akun user (is_active menjadi false).
- `POST /api/admin/users/{id}/unban` - Aktifkan kembali akun user.

#### Report Management
- `GET /api/admin/reports` - List semua report dari user.
- `PUT /api/admin/reports/{id}` - Update status penyelesaian report. Body: `{ status: "reviewed/resolved/dismissed", admin_notes: "..." }`.

---

## 4. Format Error Response Standar

Backend ini menggunakan struktur response yang seragam dari trait `ApiResponse`. Format error umumnya seperti ini:

```json
{
  "status": "error",
  "message": "Validation Error",
  "data": {
    "post_name": ["Post name is required."],
    "banner_image": ["Banner must be a file of type: jpeg, png."]
  }
}
```
**Tugas Frontend:**
Tangkap error di Axios interceptor atau blok `.catch()` / `onError` di TanStack Query, lalu ekstrak field `message` dan `data` untuk ditampilkan ke komponen UI (seperti Toast Notification atau pesan error di bawah input form).
