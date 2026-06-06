# InfoIndi API Data Shape Documentation

Dokumen ini merangkum bentuk struktur (shape) JSON yang akan dikembalikan oleh backend untuk berbagai resource. Frontend dapat menggunakan ini sebagai referensi untuk membuat *interface* atau *type* di TypeScript.

---

## 1. Format Response Dasar (Global)

Backend menggunakan standar response JSON untuk semua endpoint. Semua *success* dan *error* memiliki shape yang seragam.

### A. Success Response

```json
{
  "status": "success",
  "message": "Pesan sukses (opsional)",
  "data": { ... } // Berisi objek atau array tergantung endpoint
}
```

*Catatan: Terkadang untuk response paginasi standar Laravel (tanpa resource custom), property pagination akan disertakan di luar "data" atau dalam format spesifik ResourceCollection Laravel.*

### B. Error Response

```json
{
  "status": "error",
  "message": "Pesan error umum",
  "data": { 
    // Jika ada error validasi, ini akan berisi field dan array pesan error
    "field_name": ["Error detail 1", "Error detail 2"]
  }
}
```

---

## 2. Resource Shapes (Model Types)

Berikut adalah struktur object utama yang sering dikembalikan di dalam properti `"data"`.

### A. User Object

Bentuk object user yang dikembalikan oleh endpoint seperti `/api/auth/me`, `/api/users/{id}`, dll.

```json
{
  "id": "e4b51...uuid...",
  "name": "John Doe",
  "email": "johndoe@example.com",
  "role": "user", // "user" atau "admin"
  "is_active": true, // false jika akun di-ban
  "created_at": "2026-05-15 11:00:00"
}
```

### B. Post Object

Shape ini dikembalikan saat mengambil list post (`/api/posts`) atau detail post (`/api/posts/{id}`).

```json
{
  "id": "123e4567-e89b-12d3-a456-426614174000",
  "post_name": "Judul Postingan Saya",
  "description": "Isi deskripsi lengkap dari postingan.",
  "banner_url": "http://localhost:8000/storage/posts/banners/abc.jpg", // null jika tidak ada
  "view_count": 45,
  "likes_count": 12,
  "is_liked": true, // true jika user yang sedang login sudah like post ini
  "created_at": "2026-05-15 11:00:00",
  
  // Relasi (Tergantung dari eager loading, umumnya ada)
  "category": {
    "id": "890e4567-e89b-12d3-a456-426614174000",
    "name": "Teknologi",
    "slug": "teknologi"
  },
  "user": {
    "id": "e4b51...uuid...",
    "name": "John Doe",
    "email": "johndoe@example.com",
    "role": "user"
  },
  "images": [ // Additional images array
    {
      "id": "456e4567-e89b-12d3-a456-426614174000",
      "image_url": "http://localhost:8000/storage/posts/images/xyz.jpg",
      "post_id": "123e4567-e89b-12d3-a456-426614174000",
      "created_at": "2026-05-15 11:00:00"
    }
  ]
}
```

### C. Report Object

Diperuntukkan untuk halaman admin report (`/api/admin/reports`).

```json
{
  "id": "999e4567-e89b-12d3-a456-426614174000",
  "reason": "spam",
  "additional_info": "Ini adalah spam, promosi judi online",
  "status": "pending", // "pending", "reviewed", "resolved", "dismissed"
  "admin_notes": null, // Catatan dari admin setelah review
  "created_at": "2026-05-15 11:00:00",
  "user": { ... }, // (User Resource) Pelapor
  "post": { ... }  // (Post Resource) Post yang dilaporkan
}
```

---

## 3. Specific Endpoint Responses

### A. Login (`POST /api/auth/login`)

```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": "e4b51...uuid...",
      "name": "John Doe",
      "email": "johndoe@example.com",
      "role": "user",
      "is_active": true
    }
  }
}
```

### B. Toggle Like (`POST /api/posts/{id}/like`)

```json
{
  "status": "success",
  "message": "Post liked successfully", // atau "Post unliked successfully"
  "data": {
    "is_liked": true,
    "likes_count": 13
  }
}
```

### C. Admin Dashboard (`GET /api/admin/dashboard`)

```json
{
  "status": "success",
  "message": "Dashboard data retrieved successfully",
  "data": {
    "counts": {
      "total_users": 150,
      "active_users": 145,
      "banned_users": 5,
      "total_posts": 320,
      "pending_reports": 12
    },
    "user_growth": [
      {
        "month": "2026-01",
        "new_users": 15
      },
      // ... 12 bulan terakhir
    ],
    "user_activity": [
      {
        "month": "2026-01",
        "signups": 15,
        "active_posters": 8
      }
      // ... 12 bulan terakhir
    ],
    "top_users": [
      {
        "user_id": "e4b51...uuid...",
        "name": "John Doe",
        "email": "johndoe@example.com",
        "total_posts": 45,
        "total_likes_received": 1050
      }
      // ... top 10 users
    ]
  }
}
```

### D. Profil User + Statistik (`GET /api/users/{id}/profile`)

Jika melihat profil, biasanya disertai statistik *follower* (jika sistem follow diimplementasikan sepenuhnya) dan data post dari user.

```json
{
  "status": "success",
  "message": "Profile retrieved successfully",
  "data": {
    "user": { ... }, // Data dasar user
    "statistics": {
      "total_posts": 10,
      "followers_count": 25,
      "following_count": 30
    },
    "recent_posts": [
      // Array of Post Objects (maksimal/paginasi terbaru)
    ]
  }
}
```
