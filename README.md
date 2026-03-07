# Starter Solutions – Inertia Data Table

A structured, full-stack solution for handling server-driven data tables in  
**Laravel + Inertia.js + Vue 3** applications.

This repository serves as the **central hub** for:

- 🐛 Issue tracking
- 📦 Release notes & changelogs
- 📊 Version compatibility
- 📚 Documentation
- 🗺 Roadmap & discussions

---

## 📦 Packages

This project consists of two packages:

### 🖥 Backend (Laravel)

**Repository:**  
https://github.com/starter-solutions/inertia-data-table-laravel

**Composer package:**  
`starter-solutions/inertia-data-table`

Provides:

- Query builder macro
- Pagination handling
- Sorting logic
- Table key isolation
- Inertia response integration

---

### 🎨 Frontend (Vue 3 + Inertia)

**Repository:**  
https://github.com/starter-solutions/inertia-data-table-vue

**NPM package:**  
`@starter-solutions/inertia-data-table-vue`

Provides:

- `useDataTable()` composable
- Optional Vue components
- State synchronization
- Inertia router integration
- TypeScript support

---

## 🧠 Architecture Overview

The Inertia Data Table ecosystem follows a clear separation of responsibilities between backend and frontend.

Each table is identified by a unique **table key**, allowing:

- Multiple tables on one page
- Independent pagination
- Independent sorting
- Clean URL management

### 1️⃣ Backend (Laravel)

- Extends the query builder with a `dataTable()` macro
- Applies pagination and sorting
- Scopes state using a unique **table key**
- Returns structured metadata to Inertia

The backend remains the **single source of truth** for data ordering and limits.

### 2️⃣ Transport Layer (Inertia.js)

- Transfers paginated data and metadata
- Preserves state between visits
- Enables partial reloads
- Keeps URL state predictable

### 3️⃣ Frontend (Vue 3)

- `useDataTable(tableKey)` manages reactive table state
- Syncs with Inertia responses
- Triggers reloads when sorting or paging changes
- Allows multiple independent tables on the same page

---

## 🎯 Project Goals

- Laravel-native developer experience
- Zero manual query-string plumbing
- Clear backend ownership of data ordering
- Predictable frontend state
- Clean separation of concerns
- Type-safe frontend API

---

## 🚀 Usage

Usage instructions live in the individual package repositories.

### Laravel Backend

```php
return Inertia::render(..., [
    // It works with JsonResource wrapping
    'users' => JsonResource::collection(User::dataTable('users')),
    // and also without wrapping
    'users' => User::dataTable('users'),
]);
```

### Vue Frontend

```ts
import { useDataTable } from "@starter-solutions/inertia-data-table-vue";

const userTable = useDataTable<User>("users");

userTable.data; // User[]
userTable.pagination; // NormalizedPagination

// You can use the pagination navigation methods
userTable.firstPage();
userTable.lastPage();
userTable.previousPage();
userTable.nextPage();
userTable.goToPage(3);

// Or use the helper methods to set pagination parameters
userTable.sortBy("name");
userTable.itemsPerPage(25);

// You can also pass all parameters at once
userTable.reloadData({
    page: 3,
    per_page: 15,
    sort_by: "email",
    descending: true,
});
```

---

## 📊 Version Compatibility

| Laravel Package | Vue Package | Laravel | Vue  | Inertia |
| --------------- | ----------- | ------- | ---- | ------- |
| 0.2.x           | 0.3.x       | 10.x+   | 3.3+ | 2.x     |

> Compatibility will be updated as new major versions are released.

---

## 📦 Changelog

Each package maintains its own versioning, but high-level changes are summarized here.

- See backend releases:  
  https://github.com/starter-solutions/inertia-data-table-laravel/releases

- See frontend releases:  
  https://github.com/starter-solutions/inertia-data-table-vue/releases

---

## 🐛 Issues & Support

All issues should be reported in this repository unless they are clearly isolated to a specific package implementation detail.

When opening an issue, please specify:

- Laravel version
- Vue version
- Inertia version
- Package versions
- Expected vs actual behavior

---

## 🗺 Roadmap (Planned)

- Column filtering support
- Server-driven search helpers
- Column configuration API
- Advanced multi-column sorting

---

## 🤝 Contributing

Contributions are welcome.

Please open a discussion or issue before submitting large changes.

---

## 📄 License

MIT © Starter Solutions
