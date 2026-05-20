# Todo Application Refactoring Plan

## Phase 1: Backend (Laravel) Refactoring

### Step 1: Create Constants/Enums
- [x] `app/Constants/TaskPriority.php`
- [x] `app/Constants/TaskStatus.php`

### Step 2: Create Form Requests
- [x] `app/Http/Requests/StoreTaskRequest.php` (exists)
- [x] `UpdateTaskRequest.php`
- [x] `StoreProjectRequest.php`
- [x] `UpdateProjectRequest.php`

### Step 3: Create Service Layer
- [x] `app/Services/TaskService.php`
- [x] `app/Services/ProjectService.php`

### Step 4: Create Policies
- [x] `app/Policies/TaskPolicy.php`
- [x] `app/Policies/ProjectPolicy.php`

### Step 5: Optimize Controllers
- [x] `TaskController.php` - use service + policies + pagination
- [x] `InboxController.php` - use service
- [x] `TodayController.php` - use service
- [x] `ProjectController.php` - use service + policies

## Phase 2: Frontend (Vue 3) Refactoring

### Step 6: Create Shared Types
- [x] `resources/js/types/index.ts` - shared interfaces

### Step 7: Create Composables
- [x] `resources/js/composables/useTasks.ts`
- [x] `resources/js/composables/useFilters.ts`
- [x] `resources/js/composables/useDateUtils.ts`
- [x] `resources/js/composables/useProjects.ts`

### Step 8: Update Components/Views
- [ ] Update TaskList.vue to use composables
- [ ] Update Tasks/Index.vue to use shared types
- [ ] Update Inbox/Index.vue to use shared types
- [ ] Update Today/Index.vue to use shared types
- [ ] Update TaskFilters.vue to use composables

### Step 9: Performance Optimizations
- [ ] Add query caching in services
- [x] Add pagination to index endpoints
- [x] Optimize eager loading
