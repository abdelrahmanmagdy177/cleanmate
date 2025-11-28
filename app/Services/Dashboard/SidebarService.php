<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SidebarService
{
    public function getMenuItems(): array
    {
        $path = base_path('routes/dashboard');
        $menuItems = [];

        if (File::exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                $filename = $file->getFilenameWithoutExtension();
                
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $label = Str::title($filename);
                $route = url('dashboard/admin/' . $filename); // Assuming standard convention

                $menuItems[] = [
                    'label' => $label,
                    'url' => $route,
                    'active' => request()->is('dashboard/admin/' . $filename . '*'),
                ];
            }
        }

        return $menuItems;
    }
}
