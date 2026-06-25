# Job Tracking App - Backend (API)

Repository ini berisi *source code* backend untuk aplikasi Job Tracking (manajemen status lamaran kerja). Di sini saya menggunakan Laravel untuk menyediakan RESTful API yang nantinya dikonsumsi oleh aplikasi Frontend.

* **Frontend Repository:** (https://github.com/mahendraloka/job-tracking-client)

## Fitur API
* **Autentikasi:** Registrasi dan Login user (menggunakan Laravel Sanctum).
* **Job Management (CRUD):** Endpoint untuk menambah, melihat, mengupdate, dan menghapus data lamaran (Nama Perusahaan, Posisi, Status, Tanggal Apply, Catatan).
* **Dashboard Stats:** Mengirimkan data ringkasan (misal: jumlah lamaran yang *pending*, *interview*, atau *rejected*).

## Tech Stack
* Laravel (RESTful API)
* MySQL (Database)
