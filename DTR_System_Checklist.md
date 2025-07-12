
# ✅ DTR System Setup Checklist (Full Stack Laravel)

## 🗃️ DTR Model & Migration

- [ ] Create model and migration:  
  `php artisan make:model DTR -m`
- [ ] Add columns to migration:
  ```php
  Schema::create('dtrs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->date('date');
      $table->timestamp('time_in')->nullable();
      $table->timestamp('time_out')->nullable();
      $table->timestamps();
  });
  ```
- [ ] Run migration:  
  `php artisan migrate`

---

## 📇 Add QR Code Field to Users

- [ ] Add `qr_code` column to users table:
  ```bash
  php artisan make:migration add_qr_code_to_users_table
  ```
  In migration file:
  ```php
  $table->string('qr_code')->nullable()->unique();
  ```
- [ ] Run migration:  
  `php artisan migrate`

---

## 📥 QR Code Scanning Frontend

- [ ] Add HTML5 QR Code library to Blade view:
  ```html
  <script src="https://unpkg.com/html5-qrcode"></script>
  ```
- [ ] Create scan view (e.g. `resources/views/dtr/scan.blade.php`) with:
  - A `div` for camera
  - Script to send scan result via `fetch()` to `/dtr/scan`

---

## 📂 Controller Setup

- [ ] Create `DTRController`:  
  `php artisan make:controller DTRController`
- [ ] Add route to `web.php`:
  ```php
  Route::middleware(['auth'])->group(function () {
      Route::get('/dtr', [DTRController::class, 'index'])->name('dtr.index');
      Route::post('/dtr/scan', [DTRController::class, 'scan']);
      Route::get('/dtr/export-pdf', [DTRController::class, 'exportPDF'])->name('dtr.export-pdf');
  });
  ```
- [ ] Add logic to `scan()`:
  - Check QR
  - Log time in or time out
  - Return message

---

## 🖨️ PDF Export Feature

- [ ] Install DOMPDF:
  ```bash
  composer require barryvdh/laravel-dompdf
  ```
- [ ] Create Blade template:  
  `resources/views/dtr/pdf.blade.php`
- [ ] Add `exportPDF()` method in controller:
  - Validate date range
  - Get logs
  - Generate and download PDF

---

## 📅 Add Export Form (Frontend)

- [ ] Add form on DTR page:
  ```blade
  <form action="{{ route('dtr.export-pdf') }}" method="GET" target="_blank">
      <input type="date" name="from" required>
      <input type="date" name="to" required>
      <button type="submit">Download DTR PDF</button>
  </form>
  ```

---

## 🎁 Optional Enhancements

- [ ] Generate QR Codes for each user (use `SimpleQRCode` or external tool)
- [ ] Add admin view for managing DTRs
- [ ] Add filters for logs (by user, date, etc.)
- [ ] Add late checker or absence calculator
- [ ] Implement face/selfie capture with webcam
- [ ] Send notifications for unlogged attendance

---
