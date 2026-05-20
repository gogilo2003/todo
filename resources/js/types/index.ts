/**
 * Shared TypeScript types for the Todo application.
 * Provides centralized type definitions to ensure consistency across components.
 */

// ============================================
// Enums & Constants
// ============================================

export enum TaskPriority {
    LOW = 'low',
    MEDIUM = 'medium',
    HIGH = 'high',
}

export enum TaskStatus {
    PENDING = 'pending',
    COMPLETED = 'completed',
}

export const PRIORITY_LABELS: Record<TaskPriority, string> = {
    [TaskPriority.LOW]: 'Low',
    [TaskPriority.MEDIUM]: 'Medium',
    [TaskPriority.HIGH]: 'High',
};

export const STATUS_LABELS: Record<TaskStatus, string> = {
    [TaskStatus.PENDING]: 'Pending',
    [TaskStatus.COMPLETED]: 'Completed',
};

// ============================================
// Interface Definitions
// ============================================

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface Project {
    id: number;
    name: string;
    color: string;
    created_at?: string;
    updated_at?: string;
    tasks_count?: number;
}

export interface Task {
    id: number;
    title: string;
    description: string | null;
    project_id: number | null;
    user_id: number;
    priority: TaskPriority | string;
    due_date: string | null;
    completed: boolean;
    created_at: string;
    updated_at: string;
    project?: Project;
}

export interface TaskFilters {
    status: string;
    priority: string;
    project: string;
    due_date: string;
    search: string;
}

export interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface TaskStats {
    total: number;
    completed: number;
    pending: number;
    completion_rate: number;
}

export type PageProps<T = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
};

// ============================================
// Form Types
// ============================================

export interface TaskFormData {
    title: string;
    description: string;
    project_id: number | null;
    priority: TaskPriority | string;
    due_date: string;
    completed: boolean;
}

export interface ProjectFormData {
    name: string;
    color: string;
}

// ============================================
// Component Props Types
// ============================================

export interface TaskListProps {
    title: string;
    tasks: Task[];
    projects: Project[];
    emptyMessage?: string;
    showProjectColumn?: boolean;
}

export interface TaskFormProps {
    task?: Task | null;
    projects: Project[];
    isOpen: boolean;
}

export interface QuickAddTaskProps {
    placeholder?: string;
    autofocus?: boolean;
}

// ============================================
// Utility Types
// ============================================

export type TaskPriorityStyle = 'low' | 'medium' | 'high';

export interface PriorityStyle {
    class: string;
    textClass: string;
}

export const PRIORITY_STYLES: Record<TaskPriorityStyle, PriorityStyle> = {
    low: {
        class: 'bg-gray-100',
        textClass: 'text-gray-600',
    },
    medium: {
        class: 'bg-yellow-100',
        textClass: 'text-yellow-700',
    },
    high: {
        class: 'bg-red-100',
        textClass: 'text-red-700',
    },
};

// Default project color
export const DEFAULT_PROJECT_COLOR = '#6366f1';

// Available project colors
export const PROJECT_COLORS = [
    '#ef4444', // red
    '#f97316', // orange
    '#eab308', // yellow
    '#22c55e', // green
    '#14b8a6', // teal
    '#3b82f6', // blue
    '#6366f1', // indigo
    '#a855f7', // purple
    '#ec4899', // pink
];
