# Codebase Audit Report — AtomCMS

**Date:** 2026-04-05
**Stack:** Laravel 13.3.0 | Filament 5.4.4 | PHP 8.5 | Livewire 4.2.4

---

## 1. Bugs Fixed

### 1.1 `CommentService::destroy()` — Double delete

**File:** `app/Services/Articles/CommentService.php:40-46`

The successful `delete()` path fell through to a second `delete()` call on an already-deleted model.

```php
// BEFORE (broken)
if (! $comment->delete()) {
    return redirect()->back()->withErrors([...]);
}
return $comment->delete(); // already deleted above

// AFTER (fixed)
if (! $comment->delete()) {
    return redirect()->back()->withErrors([...]);
}
return true;
```

### 1.2 `ForgotPasswordController::showResetPassword()` — Inverted expiration logic

**File:** `app/Http/Controllers/User/ForgotPasswordController.php:50`

`gte` (greater-than-or-equal) means "token was created recently" — that's a valid token. Expired tokens were passing through to show the reset form. Changed to `lte`.

```php
// BEFORE — valid tokens rejected, expired tokens accepted
if ($prt->created_at->gte($tokenExpiration)) {

// AFTER — expired tokens correctly rejected
if ($prt->created_at->lte($tokenExpiration)) {
```

### 1.3 `UserBadge::user()` — Wrong foreign key

**File:** `app/Models/Game/Player/UserBadge.php:21`

Passing `'id'` as the foreign key matched `users.id = users_badges.id` instead of `users.id = users_badges.user_id`.

```php
// BEFORE
return $this->belongsTo(User::class, 'id');

// AFTER
return $this->belongsTo(User::class, 'user_id');
```

### 1.4 `User::currency()` — Null pointer on missing currency

**File:** `app/Models/User.php:86`

`first()` returning `null` caused `Attempt to read property on null` before `??` could evaluate.

```php
// BEFORE
return $this->currencies->where('type', $type)->first()->amount ?? 0;

// AFTER
return $this->currencies->where('type', $type)->first()?->amount ?? 0;
```

Also replaced hardcoded magic numbers with the existing `CurrencyTypes` enum.

### 1.5 `User::ssoTicket()` — Unbounded recursion

**File:** `app/Models/User.php:150-163`

Recursive call with no depth limit. Replaced with a bounded loop (max 5 attempts) that throws on failure.

### 1.6 `User::save()` — Returned `false` for clean models

**File:** `app/Models/User.php:291`

Returning `false` for non-dirty models could mislead callers expecting a truthy result on a valid model. Changed to return `true`.

---

## 2. Return Type & Type Safety Fixes

### 2.1 `hasPermission()` / `hasHousekeepingPermission()` — `string` → `bool`

**File:** `app/Helpers/helper.php:24,31`

Both functions declared `string` return type but delegated to services returning `bool`. Fixed to `bool`.

### 2.2 `WebsiteShopArticle::features()` — Method casing

**File:** `app/Models/Shop/WebsiteShopArticle.php:35`

`$this->HasMany(...)` → `$this->hasMany(...)` (PSR compliance).

### 2.3 `User` model — Added missing return types

**File:** `app/Models/User.php`

Added return types to: `sessions(): HasMany`, `currency(): int`, `referralsNeeded(): int`, `getOnlineFriends(): Collection`, `confirmTwoFactorAuthentication(): bool`, `hasAppliedForPosition(): bool`, `changePassword(): void`, `chatLogs(): HasMany`, `chatLogsPrivate(): HasMany`, `save(): bool`.

### 2.4 `User::referralsNeeded()` — Simplified null handling

```php
// BEFORE
$referrals = 0;
if (! is_null($this->referrals)) {
    $referrals = $this->referrals->referrals_total;
}

// AFTER
$referrals = $this->referrals?->referrals_total ?? 0;
```

---

## 3. Security Fixes

### 3.1 IP Spoofing — Removed unguarded `$_SERVER` override

**File:** `app/Helpers/helper.php:8-14`

Direct `$_SERVER['REMOTE_ADDR']` override from `HTTP_CF_CONNECTING_IP` and `HTTP_X_FORWARDED_FOR` headers allowed any client to spoof their IP. Removed — this should be handled by Laravel's `TrustProxies` middleware with configured trusted proxy IPs.

### 3.2 SQL interpolation in `dropForeignKeyIfExists()`

**File:** `app/Helpers/helper.php:86`

Table and foreign key names were interpolated directly into SQL. Added backtick quoting with escaping.

---

## 4. N+1 Query Fixes

### 4.1 Filament Resources — Added `getEloquentQuery()` with eager loading

All 8 resources that display relationship columns in tables now eager load those relationships:

| Resource | File | Relationships added |
|---|---|---|
| `StaffApplicationResource` | `app/Filament/Resources/Hotel/StaffApplications/` | `user`, `team`, `rank` |
| `ChatlogRoomResource` | `app/Filament/Resources/Hotel/ChatlogRooms/` | `room`, `sender`, `receiver` |
| `ChatlogPrivateResource` | `app/Filament/Resources/Hotel/ChatlogPrivates/` | `sender`, `receiver` |
| `CommandLogResource` | `app/Filament/Resources/Hotel/CommandLogs/` | `user` |
| `OpenPositionResource` | `app/Filament/Resources/Hotel/OpenPositions/` | `permission`, `team` |
| `BanResource` | `app/Filament/Resources/User/Bans/` | `user`, `staff` |
| `ArticleResource` | `app/Filament/Resources/Atom/Articles/` | `user` (added to existing query) |
| `WebsiteDrawBadgeResource` | `app/Filament/Resources/Atom/WebsiteDrawBadges/` | `user` |

### 4.2 `HousekeepingPermissionsService` — Kept uncached (intentional)

**File:** `app/Services/HousekeepingPermissionsService.php`

Unlike `PermissionsService`, housekeeping permissions are deliberately NOT cached. Admin panel permissions are security-sensitive — caching would allow a demoted staff member to retain access until the cache expires. The low traffic volume (staff only) makes the per-request query acceptable.

Fixed `toArray()` → `has()` and nullable type, but no caching added.

### 4.3 `PermissionsService` / `HousekeepingPermissionsService` — Removed `toArray()` in hot path

**Files:** `app/Services/PermissionsService.php`, `app/Services/HousekeepingPermissionsService.php`

`array_key_exists($key, $collection->toArray())` converted the entire collection to an array on every permission check. Replaced with `$collection->has($key)`.

`PermissionsService`: also changed `?Collection` to `Collection` (not nullable — always assigned in constructor).

---

## 5. Code Quality Fixes

### 5.1 `WebsiteStaffApplications` — Removed redundant `$guarded`

**File:** `app/Models/Community/Staff/WebsiteStaffApplications.php`

Had both `$guarded = ['id']` and `$fillable = [...]`. When `$fillable` is set, `$guarded` is ignored. Removed `$guarded`.

### 5.2 `WebsiteStaffApplications` — Removed stale comment

Removed `// <-- adjust if your class lives elsewhere` from import.

### 5.3 `helper.php` — `strpos()` → `str_contains()`

**File:** `app/Helpers/helper.php:50`

`strpos()` returns `0` (falsy) when found at position 0, which is a latent bug. Replaced with `str_contains()`.

### 5.4 `User::currency()` — Uses `CurrencyTypes` enum

Replaced hardcoded magic number mapping with the existing `CurrencyTypes::fromCurrencyName()` enum method.

### 5.5 `OpenPositionResource` — Fixed duplicate placeholder

**File:** `app/Filament/Resources/Hotel/OpenPositions/OpenPositionResource.php:76`

`team_id` field had `->placeholder('Select a rank')` — changed to `'Select a team'`.

---

## 6. Remaining Suggestions (Not Fixed — Requires Approval)

These are improvements that would change behavior or project structure. They should be discussed before implementing.

### 6.1 Inline validation in controllers

15+ controllers use `$request->validate()` inline instead of FormRequest classes. The project has 11 FormRequest classes but doesn't use them consistently. Affected controllers:
- `ForgotPasswordController` (2 instances)
- `InstallationController`
- `LogoGeneratorController`
- `PaypalController` (2 instances)
- `TwoFactorAuthenticationController`
- `StaffApplicationsController`
- `WebsiteTeamApplicationsController`

**Recommendation:** Create FormRequest classes for each.

### 6.2 `ShopController::purchase()` — 78-line method

This method handles recipient validation, rank checking, online status, balance, currency sending, rank upgrades, badges, and furniture. Should be extracted to a `PurchaseService` or action class.

### 6.3 Missing controller return types

27 of 35 controllers have methods missing return types (most common: `__invoke()`, `index()`, `store()`). These don't affect functionality but should be added for PHP 8.5 best practices.

### 6.4 Filament authorization

~20 of 24 Filament resources have no explicit authorization methods (`canCreate()`, `canEdit()`, `canDelete()`). While policies exist in `app/Policies/`, the resources don't explicitly leverage them. Consider adding authorization to all resources.

### 6.5 `file_get_contents()` without error handling

Used in `BadgeTextEditorResource`, `WebsiteDrawBadgeResource`, and `WebsiteDrawBadgeObserver` for file I/O. Should use Laravel's `Storage` facade or wrap in try-catch.

### 6.6 `RareValuesService` — Empty class

`app/Services/Community/RareValues/RareValuesService.php` is completely empty. Delete or implement.

### 6.7 `RareValueCategoriesService` — Deprecated `.whereId()`

`app/Services/Community/RareValues/RareValueCategoriesService.php` uses `.whereId()` which is deprecated. Replace with `.where('id', $id)`.

### 6.8 `ShopVoucherController` — Race condition

Voucher `use_count` check and increment is not atomic — vulnerable to race conditions under concurrent requests. Should use `DB::transaction()` with `lockForUpdate()`.

### 6.9 `TicketController` — Repeated authorization

Manual `canManageTicket()` checks repeated in 4 methods — should be extracted to middleware or a policy.

### 6.10 `User` model `$timestamps = false`

The User model disables timestamps. If this is intentional for the emulator database schema, document why. If not, consider enabling timestamps.

---

## Files Modified

| File | Changes |
|---|---|
| `app/Services/Articles/CommentService.php` | Fixed double delete |
| `app/Http/Controllers/User/ForgotPasswordController.php` | Fixed inverted expiration logic |
| `app/Models/Game/Player/UserBadge.php` | Fixed foreign key |
| `app/Models/User.php` | Fixed null pointer, ssoTicket recursion, save() return, added return types, used CurrencyTypes enum |
| `app/Helpers/helper.php` | Fixed return types, removed IP spoofing, fixed strpos, fixed SQL interpolation |
| `app/Models/Community/Staff/WebsiteStaffApplications.php` | Removed redundant $guarded, removed stale comment |
| `app/Models/Shop/WebsiteShopArticle.php` | Fixed HasMany casing |
| `app/Services/HousekeepingPermissionsService.php` | Added caching, fixed toArray(), fixed nullable type |
| `app/Services/PermissionsService.php` | Fixed toArray(), fixed nullable type |
| `app/Filament/Resources/Hotel/StaffApplications/StaffApplicationResource.php` | Added eager loading |
| `app/Filament/Resources/Hotel/ChatlogRooms/ChatlogRoomResource.php` | Added eager loading |
| `app/Filament/Resources/Hotel/ChatlogPrivates/ChatlogPrivateResource.php` | Added eager loading |
| `app/Filament/Resources/Hotel/CommandLogs/CommandLogResource.php` | Added eager loading |
| `app/Filament/Resources/Hotel/OpenPositions/OpenPositionResource.php` | Added eager loading, fixed placeholder |
| `app/Filament/Resources/User/Bans/BanResource.php` | Added eager loading |
| `app/Filament/Resources/Atom/Articles/ArticleResource.php` | Added eager loading |
| `app/Filament/Resources/Atom/WebsiteDrawBadges/WebsiteDrawBadgeResource.php` | Added eager loading |
