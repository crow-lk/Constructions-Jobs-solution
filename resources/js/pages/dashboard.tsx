import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import RolesDropdown from '@/components/roles-dropdown';
import type { Role } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    const [selectedRole, setSelectedRole] = useState<Role | null>(null);

    const handleRoleSelect = (role: Role) => {
        setSelectedRole(role);
        console.log('Selected role:', role);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                {/* Roles Dropdown Example */}
                <div className="flex flex-col gap-2 p-4 border border-sidebar-border/70 rounded-xl">
                    <h2 className="text-lg font-semibold">Roles Management</h2>
                    <div className="flex items-center gap-4">
                        <RolesDropdown
                            onRoleSelect={handleRoleSelect}
                            selectedRole={selectedRole}
                            placeholder="Select a role"
                        />
                        {selectedRole && (
                            <div className="text-sm text-muted-foreground">
                                Selected: {selectedRole.name}
                                {selectedRole.description && (
                                    <span className="ml-2">- {selectedRole.description}</span>
                                )}
                            </div>
                        )}
                    </div>
                </div>

                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
