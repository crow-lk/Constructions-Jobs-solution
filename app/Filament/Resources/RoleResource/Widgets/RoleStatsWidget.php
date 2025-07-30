<?php

namespace App\Filament\Resources\RoleResource\Widgets;

use App\Models\Role;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoleStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Roles', Role::count())
                ->description('Number of roles in the system')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),
            
            Stat::make('Total Users', User::count())
                ->description('Number of users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            Stat::make('Users with Roles', User::whereNotNull('role_id')->count())
                ->description('Users assigned to roles')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}
