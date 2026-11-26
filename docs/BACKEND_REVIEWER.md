# Attendance Management System — Backend Reviewer

> **Para kanino:** sa magpe-present ng system (John Aliandro T. Plaza).
> **Paano gamitin sa VS Code:** buksan ang file na ito, pindutin ang **`Ctrl + Shift + V`** para sa magandang preview.
> Ang mga link (hal. [`routes/web.php`](../routes/web.php)) ay **Ctrl + Click** para buksan mismo ang file.
> Para mabilis maghanap ng file: **`Ctrl + P`** tapos i-type ang pangalan (hal. `AttendanceController`).

---

## Laman

1. [Paano tumatakbo ang isang request (MVC)](#1-paano-tumatakbo-ang-isang-request-mvc)
2. [Mapa ng mga folder at file](#2-mapa-ng-mga-folder-at-file)
3. [Routes — `routes/web.php`](#3-routes--routeswebphp)
4. [Controllers — isa-isa](#4-controllers--isa-isa)
5. [Models](#5-models)
6. [Database — migrations](#6-database--migrations)
7. [Seeders (sample data)](#7-seeders-sample-data)
8. [Config at providers na binago](#8-config-at-providers-na-binago)
9. [Security — paano protektado ang system](#9-security--paano-protektado-ang-system)
10. [Mga tanong sa defense at sagot](#10-mga-tanong-sa-defense-at-sagot)
11. [1-minute explanation (script)](#11-1-minute-explanation-script)

---

## 1. Paano tumatakbo ang isang request (MVC)

Ang system ay **Laravel 8** (PHP framework) na gumagamit ng **MVC**:

| Letter | Ibig sabihin | Saan sa code | Trabaho |
| --- | --- | --- | --- |
| **M** | Model | `app/Models/` | Kumakatawan sa isang table; dito kumukuha/nagse-save ng data (Eloquent) |
| **V** | View | `resources/views/` | Ang HTML page na nakikita ng user (Blade) |
| **C** | Controller | `app/Http/Controllers/` | Ang "utak": tumatanggap ng request, nagva-validate, gumagamit ng model, nagbabalik ng view |

**Halimbawa: pag-save ng Take Attendance**

```
Browser  ──POST /attendance/take──►  routes/web.php
                                        │  (middleware: auth + CSRF)
                                        ▼
                         AttendanceController@saveTake
                           1. $request->validate([...])
                           2. DB::transaction( Attendance::updateOrCreate(...) )
                                        │
                                        ▼
                         Model: Attendance  ──SQL──►  MySQL table `attendances`
                                        │
                                        ▼
                         redirect() + "Attendance saved..."  ──►  Browser
```

**Sa madaling salita:** route → controller → model → database → balik sa view.

---

## 2. Mapa ng mga folder at file

Ito lang ang mga file na **kami ang gumawa o nagbago**. Ang iba ay default ng Laravel.

```
BSIS4B-DEV-APP-PLAZA/
├── routes/
│   └── web.php                         ← lahat ng URL ng system
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          ← login / logout
│   │   ├── DashboardController.php     ← dashboard numbers at charts
│   │   ├── StudentController.php       ← Students CRUD
│   │   ├── AttendanceController.php    ← Records CRUD + Take Attendance
│   │   ├── ReportController.php        ← report + CSV export
│   │   └── PostController.php          ← Posts CRUD (unang activity)
│   ├── Models/
│   │   ├── Student.php
│   │   ├── Attendance.php
│   │   ├── User.php
│   │   └── Post.php
│   └── Providers/
│       ├── AppServiceProvider.php      ← Bootstrap pagination
│       └── RouteServiceProvider.php    ← HOME = '/'
├── database/
│   ├── migrations/                     ← table definitions
│   └── seeders/                        ← sample data + demo account
├── resources/views/                    ← Blade pages (frontend)
├── config/app.php                      ← timezone Asia/Manila
└── .env / .env.example                 ← database name at settings
```

| File | Isang linya na paliwanag |
| --- | --- |
| [`routes/web.php`](../routes/web.php) | Listahan ng lahat ng URL at kung anong controller method ang tatakbo |
| [`AuthController.php`](../app/Http/Controllers/AuthController.php) | Login (`Auth::attempt`) at logout |
| [`DashboardController.php`](../app/Http/Controllers/DashboardController.php) | Bilang ngayong araw, chart ng 7 araw, 30-day rate, most absences |
| [`StudentController.php`](../app/Http/Controllers/StudentController.php) | Add, view, edit, delete ng estudyante, may search at filter |
| [`AttendanceController.php`](../app/Http/Controllers/AttendanceController.php) | CRUD ng attendance records + **Take Attendance** ng buong section |
| [`ReportController.php`](../app/Http/Controllers/ReportController.php) | Attendance rate bawat estudyante + download CSV |
| [`PostController.php`](../app/Http/Controllers/PostController.php) | CRUD ng posts (title, content) |
| [`Student.php`](../app/Models/Student.php) | Model ng `students`; may `attendances()` at `full_name` |
| [`Attendance.php`](../app/Models/Attendance.php) | Model ng `attendances`; may `student()`, STATUSES, BADGES |
| [`User.php`](../app/Models/User.php) | Account na pang-login |
| [`Post.php`](../app/Models/Post.php) | Model ng `posts` |

---

## 3. Routes — `routes/web.php`

📄 [`routes/web.php`](../routes/web.php)

```php
// Login / Logout
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Lahat ng page sa ibaba ay kailangang naka-login
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('students', StudentController::class);
    Route::get('attendance/take', [AttendanceController::class, 'take'])->name('attendance.take');
    Route::post('attendance/take', [AttendanceController::class, 'saveTake'])->name('attendance.save');
    Route::resource('attendances', AttendanceController::class)->except('show');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::resource('posts', PostController::class);
});
```

**Paliwanag:**

| Code | Ibig sabihin |
| --- | --- |
| `middleware('guest')` | Para lang sa **hindi pa naka-login** (login page). Kung naka-login na, ibabalik sa dashboard. |
| `middleware('auth')` | Para lang sa **naka-login**. Kung hindi, dadalhin sa `/login`. |
| `throttle:5,1` | **5 beses lang** puwedeng mag-login kada **1 minuto** (laban sa brute force / hula ng password). |
| `->name('dashboard')` | Pangalan ng route; sa view ginagamit ang `route('dashboard')` imbes na isulat ang URL. |
| `Route::resource(...)` | Isang linya = **7 CRUD routes** (index, create, store, show, edit, update, destroy). |
| `->except('show')` | Tinanggal ang `show` route ng attendances (hindi kailangan, may edit page na). |

**Ang 7 routes ng `Route::resource('students', ...)`:**

| Method | URL | Controller method | CRUD |
| --- | --- | --- | --- |
| GET | `/students` | `index` | **R**ead (listahan) |
| GET | `/students/create` | `create` | form ng Add |
| POST | `/students` | `store` | **C**reate |
| GET | `/students/{student}` | `show` | **R**ead (isa) |
| GET | `/students/{student}/edit` | `edit` | form ng Edit |
| PUT | `/students/{student}` | `update` | **U**pdate |
| DELETE | `/students/{student}` | `destroy` | **D**elete |

> 💡 Sa terminal, makikita lahat ng routes: `php artisan route:list`

---

## 4. Controllers — isa-isa

### 4.1 `AuthController.php` — Login at Logout

📄 [`app/Http/Controllers/AuthController.php`](../app/Http/Controllers/AuthController.php)

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        throw ValidationException::withMessages(['email' => 'Incorrect email or password.']);
    }

    $request->session()->regenerate();
    return redirect()->intended(route('dashboard'));
}
```

| Linya | Ginagawa |
| --- | --- |
| `$request->validate([...])` | Sinisigurong may email (tamang format) at password. Kung kulang, babalik sa form na may error. |
| `Auth::attempt($credentials, remember)` | Hinahanap ang user sa `users` table at **kinukumpara ang password sa hash** (bcrypt). `remember` = "Remember me" checkbox. |
| `throw ValidationException...` | Kapag mali, ipapakita ang "Incorrect email or password." |
| `session()->regenerate()` | **Bagong session ID** pagkatapos mag-login — panlaban sa *session fixation*. |
| `redirect()->intended(...)` | Babalik sa page na gustong buksan bago pinilit mag-login; kung wala, sa dashboard. |

```php
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();       // burahin ang session
    $request->session()->regenerateToken();  // bagong CSRF token
    return redirect()->route('login')->with('success', 'You have been logged out.');
}
```

---

### 4.2 `DashboardController.php` — Dashboard

📄 [`app/Http/Controllers/DashboardController.php`](../app/Http/Controllers/DashboardController.php)

Isang method lang: `index()`. Kinukuha nito ang mga numerong ipinapakita sa dashboard.

| Variable | Paano kinukuha | Ipinapakita bilang |
| --- | --- | --- |
| `$todayCounts` | `Attendance::whereDate('date', $today)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total','status')` | Present / Late / Absent / Excused **today** |
| `$totalStudents` | `Student::count()` | Bilang ng estudyante |
| `notRecorded` | `$totalStudents - $recordedToday` | "X students have no attendance yet" |
| `todayRate` | `(present + late) ÷ recordedToday × 100` | % sa donut chart |
| `$week` | bilang bawat status sa huling **7 araw** | bar chart |
| `monthRate` | `(present + late) ÷ lahat ng record sa 30 araw × 100` | "Last 30 days" rate |
| `$mostAbsent` | `Student::withCount(['attendances as absences' => ...])->having('absences','>',0)->take(5)` | Top 5 pinakamaraming absent |
| `$recent` | `Attendance::with('student')->orderByDesc('date')->take(8)` | Recent Records |

**Mga term:**
- **`selectRaw` + `groupBy`** — isang query lang para bilangin bawat status (mas mabilis kaysa apat na query).
- **`withCount`** — nagdadagdag ng bilang mula sa relationship (`attendances`) sa bawat student.
- **`with('student')`** — **eager loading**: kinukuha nang sabay ang student ng bawat record (iwas *N+1 problem*).
- **`(clone $month)`** — kinokopya ang query para magamit nang dalawang beses nang hindi nagagalaw ang orihinal.

---

### 4.3 `StudentController.php` — Students CRUD

📄 [`app/Http/Controllers/StudentController.php`](../app/Http/Controllers/StudentController.php)

| Method | Route | Ginagawa |
| --- | --- | --- |
| `index()` | GET `/students` | Listahan, may **search** (student no. o pangalan) at **filter by section**, 10 kada page |
| `create()` | GET `/students/create` | Ipakita ang Add form |
| `store()` | POST `/students` | I-validate tapos `Student::create(...)` |
| `show(Student $student)` | GET `/students/{id}` | Profile + attendance history + bilang kada status |
| `edit()` | GET `/students/{id}/edit` | Ipakita ang Edit form |
| `update()` | PUT `/students/{id}` | I-validate tapos `$student->update(...)` |
| `destroy()` | DELETE `/students/{id}` | `$student->delete()` — kasamang nabubura ang attendance niya (cascade) |

**Search at filter (index):**

```php
$students = Student::query()
    ->when($search !== '', function ($q) use ($search) {
        $q->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
        });
    })
    ->when($section, fn ($q) => $q->where('section', $section))
    ->orderBy('last_name')->orderBy('first_name')
    ->paginate(10)
    ->withQueryString();
```

- **`->when(kondisyon, function)`** — idinadagdag lang ang `WHERE` kapag may laman ang search/section.
- **`like "%...%"`** — hinahanap kahit saang bahagi ng text.
- **`paginate(10)`** — 10 kada page; **`withQueryString()`** — hindi nawawala ang search kapag lumipat ng page.

**Validation (para sa store at update):**

```php
'student_no' => ['required', 'max:30', Rule::unique('students', 'student_no')->ignore($student)],
'first_name' => 'required|max:100',
'last_name'  => 'required|max:100',
'course'     => 'required|max:50',
'year_level' => 'required|integer|between:1,5',
'section'    => 'required|max:20',
```

- **`Rule::unique(...)->ignore($student)`** — bawal ang dobleng student no.; sa Edit, hindi binibilang ang sarili niyang record.
- **Route model binding** — sa `show(Student $student)`, si Laravel na ang naghahanap ng Student gamit ang id sa URL. Kapag wala, **404**.

---

### 4.4 `AttendanceController.php` — Records + Take Attendance ⭐

📄 [`app/Http/Controllers/AttendanceController.php`](../app/Http/Controllers/AttendanceController.php)

| Method | Route | Ginagawa |
| --- | --- | --- |
| `index()` | GET `/attendances` | Lahat ng records, may filter: **from, to, status, section, search**; 15 kada page |
| `create()` / `store()` | GET/POST | Mag-add ng isang record |
| `edit()` / `update()` | GET/PUT | I-edit ang isang record |
| `destroy()` | DELETE | Burahin ang isang record |
| **`take()`** | GET `/attendance/take` | Ipakita ang **buong section** para sa isang petsa |
| **`saveTake()`** | POST `/attendance/take` | **I-save ang buong section nang sabay** |

**Filters sa index — bakit may `join`?**

```php
Attendance::query()
    ->with('student')
    ->join('students', 'students.id', '=', 'attendances.student_id')
    ->select('attendances.*')
    ->when($filters['section'], fn ($q, $v) => $q->where('students.section', $v))
    ...
```

Ang `section` at pangalan ay nasa **`students`** table, kaya **jina-join** para ma-filter at ma-sort gamit ang mga iyon. Ang `select('attendances.*')` ay para attendance columns lang ang makuha.

**`take()` — pagkuha ng section:**

```php
$students = Student::when($section, fn ($q) => $q->where('section', $section))->orderBy('last_name')->get();
$existing = Attendance::whereDate('date', $date)
    ->whereIn('student_id', $students->pluck('id'))
    ->get()->keyBy('student_id');
```

`$existing` = mga record na **naka-save na** sa araw na iyon, para naka-check na agad ang status nila sa form. `keyBy('student_id')` = madaling hanapin ang record ng bawat estudyante.

**`saveTake()` — ang pinakamahalagang code:**

```php
$data = $request->validate([
    'date'              => 'required|date|before_or_equal:today',
    'records'           => 'required|array',
    'records.*.status'  => ['required', Rule::in(array_keys(Attendance::STATUSES))],
    'records.*.time_in' => 'nullable|date_format:H:i',
    'records.*.remarks' => 'nullable|max:255',
]);

DB::transaction(function () use ($data, $ids) {
    foreach ($ids as $id) {
        $row = $data['records'][$id];
        $timeIn = in_array($row['status'], ['present', 'late']) ? ($row['time_in'] ?? null) : null;

        Attendance::updateOrCreate(
            ['student_id' => $id, 'date' => $data['date']],                          // hanapin ito
            ['status' => $row['status'], 'time_in' => $timeIn, 'remarks' => $row['remarks'] ?? null]  // i-save ito
        );
    }
});
```

| Bahagi | Paliwanag |
| --- | --- |
| `before_or_equal:today` | Bawal mag-attendance sa **future** na petsa. |
| `records.*.status` | Ang `*` = bawat estudyante sa form; dapat isa sa 4 na status. |
| `DB::transaction(...)` | **Sabay-sabay** ang pag-save. Kung may isang pumalya, **walang mase-save** (rollback) — walang kalahating data. |
| `updateOrCreate([hanap], [laman])` | Kung **may record na** ang estudyante sa petsang iyon → **update**. Kung **wala** → **create**. Kaya **hindi nadodoble** kahit i-save ulit. |
| `$timeIn = ... ? ... : null` | Time In ay para lang sa **Present at Late**; Absent/Excused = `null`. |

**`validated()` — para sa isang record (store/update):**

```php
'date' => ['required', 'date', 'before_or_equal:today',
    Rule::unique('attendances')
        ->where(fn ($q) => $q->where('student_id', $request->input('student_id')))
        ->ignore($attendance)],
```

Bawal ang **dalawang record** ng iisang estudyante sa **iisang araw** (may error na "This student already has an attendance record on that date.").

---

### 4.5 `ReportController.php` — Report at CSV

📄 [`app/Http/Controllers/ReportController.php`](../app/Http/Controllers/ReportController.php)

```php
$rows = Student::query()
    ->when($section, fn ($q) => $q->where('section', $section))
    ->withCount([
        'attendances as present' => fn ($q) => $range($q)->where('status', 'present'),
        'attendances as late'    => fn ($q) => $range($q)->where('status', 'late'),
        'attendances as absent'  => fn ($q) => $range($q)->where('status', 'absent'),
        'attendances as excused' => fn ($q) => $range($q)->where('status', 'excused'),
    ])
    ->get()
    ->map(function ($s) {
        $s->days = $s->present + $s->late + $s->absent + $s->excused;
        $s->rate = $this->rate($s->present + $s->late, $s->days);
        return $s;
    });
```

- **Default na petsa:** simula ng buwan hanggang ngayon (`startOfMonth()` → `now()`).
- **`withCount` na apat** — isang query lang para mabilang ang 4 na status ng bawat estudyante.
- **Formula ng rate:**

  **Attendance Rate = (Present + Late) ÷ Total days recorded × 100** (rounded sa 1 decimal)

  Ang **Late** ay bilang na pumasok; ang **Absent** at **Excused** ay hindi.

**CSV export:**

```php
return response()->streamDownload(function () use ($rows, ...) {
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");      // UTF-8 BOM para tama ang ñ sa Excel
    fputcsv($out, ['Student No.', 'Name', ...]);
    foreach ($rows as $r) { fputcsv($out, [...]); }
    fclose($out);
}, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
```

- **`streamDownload`** — ginagawa ang file habang dina-download (hindi na sine-save sa server).
- **`fputcsv`** — tamang CSV format (may quote kapag may comma ang pangalan, hal. "Dela Cruz, Carlo").

---

### 4.6 `PostController.php` — Posts CRUD

📄 [`app/Http/Controllers/PostController.php`](../app/Http/Controllers/PostController.php)

Ang **unang CRUD activity**. Simpleng 7 resource methods; validation: `title` required, max 255; `content` required.
Gumagamit ng `Post::findOrFail($id)` — kung walang ganoong post, **404**.
Kahit `Post::create($request->all())`, ligtas dahil may **`$fillable`** ang model (title at content lang ang tatanggapin).

---

## 5. Models

📁 [`app/Models/`](../app/Models)

### `Student.php`

```php
protected $fillable = ['student_no', 'first_name', 'last_name', 'course', 'year_level', 'section'];

public function attendances() { return $this->hasMany(Attendance::class); }

public function getFullNameAttribute() { return $this->last_name . ', ' . $this->first_name; }
```

| Bahagi | Paliwanag |
| --- | --- |
| `$fillable` | Mga column lang na puwedeng punan mula sa form → proteksyon sa **mass assignment**. |
| `hasMany(Attendance::class)` | **Isang estudyante, maraming attendance.** Gamit: `$student->attendances`. |
| `getFullNameAttribute()` | **Accessor** → `$student->full_name` = "Bautista, Andrea". Wala ito sa database. |

### `Attendance.php`

```php
public const STATUSES = ['present' => 'Present', 'late' => 'Late', 'absent' => 'Absent', 'excused' => 'Excused'];
public const BADGES   = ['present' => 'success', 'late' => 'warning', 'absent' => 'danger', 'excused' => 'secondary'];

protected $fillable = ['student_id', 'date', 'status', 'time_in', 'remarks'];
protected $casts    = ['date' => 'date'];

public function student() { return $this->belongsTo(Student::class); }
public function getStatusLabelAttribute() { ... }   // $record->status_label → "Present"
public function getBadgeAttribute() { ... }         // $record->badge → "success" (kulay)
```

| Bahagi | Paliwanag |
| --- | --- |
| `STATUSES` | Iisang listahan ng 4 na status — ginagamit sa dropdown, validation, at chart. |
| `BADGES` | Kulay ng badge: berde, dilaw, pula, abo. |
| `$casts = ['date' => 'date']` | Nagiging **Carbon date object**, kaya puwede ang `$record->date->format('M d, Y')`. |
| `belongsTo(Student::class)` | Ang record ay **pag-aari ng isang estudyante** (gamit ang `student_id`). |

### `User.php` at `Post.php`

- **User** — extends `Authenticatable` (ginagamit ni `Auth::attempt`). `$hidden = ['password', 'remember_token']` → hindi lumalabas kapag ginawang JSON.
- **Post** — `$fillable = ['title', 'content']`, walang relationship.

**Relationship diagram:**

```
students (1) ──────────< (many) attendances
  id  ◄──────────────────── student_id   (foreign key, cascade on delete)
                            unique (student_id, date)

users  (pang-login lang, walang relationship)
posts  (walang relationship)
```

---

## 6. Database — migrations

📁 [`database/migrations/`](../database/migrations) — gumagawa ng tables gamit ang PHP (`php artisan migrate`).

### `students` — [`2026_11_23_000001_create_students_table.php`](../database/migrations/2026_11_23_000001_create_students_table.php)

```php
$table->id();
$table->string('student_no', 30)->unique();
$table->string('first_name', 100);
$table->string('last_name', 100);
$table->string('course', 50);
$table->unsignedTinyInteger('year_level');
$table->string('section', 20);
$table->timestamps();
```

### `attendances` — [`2026_11_23_000002_create_attendances_table.php`](../database/migrations/2026_11_23_000002_create_attendances_table.php)

```php
$table->id();
$table->foreignId('student_id')->constrained()->cascadeOnDelete();
$table->date('date');
$table->string('status', 10);              // present, late, absent, excused
$table->time('time_in')->nullable();
$table->string('remarks')->nullable();
$table->timestamps();
$table->unique(['student_id', 'date']);    // isa lang kada estudyante kada araw
```

| Code | Paliwanag |
| --- | --- |
| `foreignId('student_id')->constrained()` | Foreign key papunta sa `students.id`. Hindi puwedeng mag-record para sa estudyanteng wala. |
| `->cascadeOnDelete()` | Kapag binura ang estudyante, **kusa ring nabubura** ang attendance niya. |
| `unique(['student_id', 'date'])` | **Database-level** na proteksyon laban sa doble (kahit malusutan ang validation). |
| `nullable()` | Puwedeng walang laman (hal. time_in ng Absent). |
| `timestamps()` | `created_at` at `updated_at`, si Laravel ang nagpupuno. |

**Iba pang tables:** `users`, `posts`, at mga default ni Laravel (`password_resets`, `failed_jobs`, `personal_access_tokens`).

> 💡 Commands: `php artisan migrate` (gumawa ng tables) · `php artisan migrate:fresh --seed` (burahin lahat, gawin ulit, lagyan ng sample data)

---

## 7. Seeders (sample data)

📁 [`database/seeders/`](../database/seeders)

| Seeder | Ginagawa |
| --- | --- |
| [`DatabaseSeeder`](../database/seeders/DatabaseSeeder.php) | Tinatawag ang apat na seeder sa ibaba, sunud-sunod |
| [`UserSeeder`](../database/seeders/UserSeeder.php) | Gumagawa ng demo account (`updateOrCreate`, password ay `Hash::make`) — tingnan ang README para sa login |
| `PostSeeder` | 5 sample posts |
| [`StudentSeeder`](../database/seeders/StudentSeeder.php) | 12 estudyante: 5 sa 4A, 7 sa 4B |
| [`AttendanceSeeder`](../database/seeders/AttendanceSeeder.php) | Attendance ng **huling 5 school days** (walang Sabado/Linggo), gamit ang fixed na pattern para pare-pareho ang resulta |

---

## 8. Config at providers na binago

| File | Binago | Bakit |
| --- | --- | --- |
| [`config/app.php`](../config/app.php) | `'timezone' => 'Asia/Manila'` | Tama ang oras ng Time In at "today" (dati UTC, 8 oras ang layo) |
| [`app/Providers/AppServiceProvider.php`](../app/Providers/AppServiceProvider.php) | `Paginator::useBootstrap()` | Bootstrap 5 ang itsura ng page numbers |
| [`app/Providers/RouteServiceProvider.php`](../app/Providers/RouteServiceProvider.php) | `HOME = '/'` | Dashboard ang bukas pagka-login |
| `.env` / [`.env.example`](../.env.example) | `DB_DATABASE=bsis4b_plaza`, `APP_NAME` | Pangalan ng database at ng app. Ang `.env` ay **hindi ina-upload sa GitHub**. |
| `app/Http/Kernel.php` (default) | `VerifyCsrfToken` sa `web` group | Lahat ng POST/PUT/DELETE ay may CSRF check |

---

## 9. Security — paano protektado ang system

| Banta | Proteksyon | Saan |
| --- | --- | --- |
| Pumasok nang walang login | `auth` middleware | `routes/web.php` |
| Hula-hulang password (brute force) | `throttle:5,1` | `routes/web.php` |
| Ninakaw na password sa database | **bcrypt hash** (`Hash::make`) | `UserSeeder`, `Auth::attempt` |
| Session fixation | `session()->regenerate()` | `AuthController@login` |
| Pekeng form mula sa ibang site (CSRF) | `@csrf` sa form + `VerifyCsrfToken` | views + Kernel |
| SQL injection | Eloquent / query builder (prepared statements) | lahat ng controller |
| XSS (script sa input) | `{{ }}` ng Blade ay nag-e-escape | views |
| Mass assignment | `$fillable` | models |
| Maling data | `$request->validate([...])` | controllers |
| Dobleng attendance | `unique(student_id, date)` + `Rule::unique` + `updateOrCreate` | migration + controller |

---

## 10. Mga tanong sa defense at sagot

| Tanong | Sagot |
| --- | --- |
| **Ano ang MVC?** | Model (data), View (page), Controller (logic). Ang route ang nagpapasa ng request sa controller; ang controller ang gumagamit ng model at nagbabalik ng view. |
| **Ano ang migration?** | PHP file na gumagawa o nagbabago ng table, para mabuo ulit ang database sa kahit anong computer gamit ang `php artisan migrate`. |
| **Ano ang Eloquent?** | ORM ni Laravel: ginagamit ang table na parang PHP class. Hal. `Student::where('section','4B')->get()` imbes na SELECT. |
| **Relationship ng Student at Attendance?** | **One-to-many.** `Student hasMany Attendance`, `Attendance belongsTo Student`, gamit ang `student_id`. |
| **Paano naiiwasan ang dobleng attendance?** | Tatlong layer: `unique(student_id, date)` sa database, `Rule::unique` sa validation, at `updateOrCreate` sa Take Attendance. |
| **Ano ang nangyayari kapag binura ang estudyante?** | Nabubura rin ang attendance niya (`cascadeOnDelete`). May confirm muna bago i-delete. |
| **Paano kinukuwenta ang attendance rate?** | (Present + Late) ÷ total na araw na may record × 100. |
| **Bakit kasama ang Late?** | Pumasok pa rin ang estudyante, nahuli lang. |
| **Bakit may `DB::transaction`?** | Para sabay-sabay ang pag-save ng buong section; kung may pumalya, walang mase-save na kalahati. |
| **Ano ang `$fillable`?** | Listahan ng columns na puwedeng punan mula sa form — proteksyon sa mass assignment. |
| **Ano ang `full_name`? Wala sa table.** | Accessor (`getFullNameAttribute`) na pinagsasama ang last at first name. |
| **Ano ang `@csrf`?** | Lihim na token sa bawat form. Tinatanggihan ni Laravel ang POST na walang token. |
| **Bakit `@method('PUT')` / `('DELETE')`?** | GET at POST lang ang kaya ng HTML form; sinasabi nito kay Laravel ang totoong method. |
| **Paano naka-save ang password?** | Naka-hash (bcrypt); hindi kailanman sine-save ang totoong password. |
| **Ano ang route model binding?** | Sa `/students/{student}`, si Laravel ang naghahanap ng Student; kung wala, 404. |
| **Ano ang N+1 problem?** | Isang query bawat row para sa student. Iniwasan gamit ang `with('student')` (eager loading). |
| **Ano ang `withQueryString()`?** | Hindi nawawala ang search/filter kapag lumipat ng page. |
| **Paano gumagana ang CSV?** | `streamDownload` + `fputcsv`; may UTF-8 BOM para tama ang ñ sa Excel. |
| **Ano ang seeder?** | Class na naglalagay ng sample data (`php artisan db:seed`). |
| **Ano pa ang puwedeng idagdag?** | Roles (admin/teacher), QR o ID scan na check-in, at SMS/email sa magulang kapag absent. |

---

## 11. 1-minute explanation (script)

> "Ang system po namin ay **Attendance Management System** na gawa sa **Laravel 8** at **MySQL**, gamit ang **MVC**.
>
> Lahat ng URL ay nasa **`routes/web.php`**, at lahat ng page maliban sa login ay protektado ng **`auth` middleware**.
>
> May dalawang pangunahing table: **`students`** at **`attendances`**, na **one-to-many** — isang estudyante, maraming attendance — konektado sa **`student_id`** na foreign key na may **cascade on delete**, at may **unique** na `student_id + date` para walang doble.
>
> Ang pinakamahalagang feature ay ang **Take Attendance** sa `AttendanceController@saveTake`: sine-save ang buong section sa loob ng **`DB::transaction`** gamit ang **`updateOrCreate`**, kaya kahit i-save ulit, ina-update lang at hindi nadodoble.
>
> Sa **`ReportController`**, kinukuwenta ang attendance rate: **(Present + Late) ÷ total days × 100**, at puwedeng i-download bilang **CSV**.
>
> Para sa security, may **bcrypt** na password, **CSRF token** sa bawat form, **validation** sa bawat input, at **login throttle** na 5 tries kada minuto."

---

*Repository: [plazaali47-tech/attendance-management-system](https://github.com/plazaali47-tech/attendance-management-system)*
