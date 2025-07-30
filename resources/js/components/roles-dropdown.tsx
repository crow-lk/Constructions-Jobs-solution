import { useState, useEffect } from 'react';
import { ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Role } from '@/types';

interface RolesDropdownProps {
    onRoleSelect?: (role: Role) => void;
    selectedRole?: Role | null;
    placeholder?: string;
    className?: string;
}

export default function RolesDropdown({
    onRoleSelect,
    selectedRole,
    placeholder = 'Select a role',
    className = '',
}: RolesDropdownProps) {
    const [roles, setRoles] = useState<Role[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        fetchRoles();
    }, []);

    const fetchRoles = async () => {
        try {
            setLoading(true);
            const response = await fetch('/api/roles/public', {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'include',
            });

            if (!response.ok) {
                throw new Error('Failed to fetch roles');
            }

            const data = await response.json();
            setRoles(data.roles || []);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'An error occurred');
        } finally {
            setLoading(false);
        }
    };

    const handleRoleSelect = (role: Role) => {
        onRoleSelect?.(role);
    };

    if (loading) {
        return (
            <div className={`flex items-center justify-center p-2 ${className}`}>
                <div className="text-sm text-muted-foreground">Loading roles...</div>
            </div>
        );
    }

    if (error) {
        return (
            <div className={`flex items-center justify-center p-2 ${className}`}>
                <div className="text-sm text-destructive">Error: {error}</div>
            </div>
        );
    }

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="outline"
                    className={`justify-between ${className}`}
                    disabled={roles.length === 0}
                >
                    {selectedRole ? selectedRole.name : placeholder}
                    <ChevronDown className="h-4 w-4 opacity-50" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-56">
                {roles.length === 0 ? (
                    <DropdownMenuItem disabled>
                        No roles available
                    </DropdownMenuItem>
                ) : (
                    roles.map((role) => (
                        <DropdownMenuItem
                            key={role.id}
                            onClick={() => handleRoleSelect(role)}
                            className="cursor-pointer"
                        >
                            <div className="flex flex-col">
                                <span className="font-medium">{role.name}</span>
                                {role.description && (
                                    <span className="text-xs text-muted-foreground">
                                        {role.description}
                                    </span>
                                )}
                            </div>
                        </DropdownMenuItem>
                    ))
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );
} 