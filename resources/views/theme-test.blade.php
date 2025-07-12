@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-4xl font-bold text-primary mb-4">🎨 Theme System Test</h1>
        <p class="text-secondary">Testing the unified light/dark mode implementation</p>
    </div>

    <!-- Theme Status -->
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Current Theme Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-secondary p-4 rounded-lg">
                <h3 class="font-medium text-primary">Theme Manager Status</h3>
                <p class="text-secondary" id="theme-status">Loading...</p>
            </div>
            <div class="bg-secondary p-4 rounded-lg">
                <h3 class="font-medium text-primary">System Preference</h3>
                <p class="text-secondary" id="system-preference">Loading...</p>
            </div>
        </div>
    </div>

    <!-- Color Palette Test -->
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Color Palette</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-primary p-4 rounded-lg">
                <div class="text-inverse font-medium">Primary BG</div>
                <div class="text-inverse text-sm opacity-75">var(--bg-primary)</div>
            </div>
            <div class="bg-secondary p-4 rounded-lg">
                <div class="text-primary font-medium">Secondary BG</div>
                <div class="text-secondary text-sm">var(--bg-secondary)</div>
            </div>
            <div class="bg-tertiary p-4 rounded-lg">
                <div class="text-primary font-medium">Tertiary BG</div>
                <div class="text-secondary text-sm">var(--bg-tertiary)</div>
            </div>
            <div class="card p-4">
                <div class="text-primary font-medium">Card BG</div>
                <div class="text-secondary text-sm">var(--bg-card)</div>
            </div>
        </div>
    </div>

    <!-- Interactive Elements Test -->
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Interactive Elements</h2>
        <div class="space-y-4">
            <!-- Buttons -->
            <div class="flex flex-wrap gap-4">
                <button class="btn-primary px-4 py-2 rounded-lg">Primary Button</button>
                <button class="btn-secondary px-4 py-2 rounded-lg">Secondary Button</button>
                <button class="theme-toggle-btn" onclick="window.themeManager?.toggle()">
                    Toggle Theme
                </button>
            </div>
            
            <!-- Form Elements -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" placeholder="Text Input" class="px-3 py-2 border rounded-lg">
                <select class="px-3 py-2 border rounded-lg">
                    <option>Select Option</option>
                    <option>Option 1</option>
                    <option>Option 2</option>
                </select>
            </div>
            
            <textarea placeholder="Textarea" class="w-full px-3 py-2 border rounded-lg" rows="3"></textarea>
        </div>
    </div>

    <!-- Status Colors Test -->
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Status Colors</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-success p-4 rounded-lg">
                <div class="text-inverse font-medium">Success</div>
                <div class="text-inverse text-sm opacity-75">✓ Operation completed</div>
            </div>
            <div class="bg-warning p-4 rounded-lg">
                <div class="text-inverse font-medium">Warning</div>
                <div class="text-inverse text-sm opacity-75">⚠ Please review</div>
            </div>
            <div class="bg-error p-4 rounded-lg">
                <div class="text-inverse font-medium">Error</div>
                <div class="text-inverse text-sm opacity-75">✗ Something went wrong</div>
            </div>
            <div class="bg-info p-4 rounded-lg">
                <div class="text-inverse font-medium">Info</div>
                <div class="text-inverse text-sm opacity-75">ℹ Additional information</div>
            </div>
        </div>
    </div>

    <!-- Text Utilities Test -->
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Text Utilities</h2>
        <div class="space-y-2">
            <p class="text-primary">Primary text color</p>
            <p class="text-secondary">Secondary text color</p>
            <p class="text-tertiary">Tertiary text color</p>
            <p class="text-muted">Muted text color</p>
            <p class="interactive-primary">Interactive primary color</p>
        </div>
    </div>

    <!-- Theme Controls -->
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Theme Controls</h2>
        <div class="flex flex-wrap gap-4">
            <button onclick="window.themeManager?.setTheme('light')" 
                    class="px-4 py-2 bg-yellow-400 text-black rounded-lg hover:bg-yellow-300">
                ☀️ Force Light
            </button>
            <button onclick="window.themeManager?.setTheme('dark')" 
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                🌙 Force Dark
            </button>
            <button onclick="window.themeManager?.resetToSystem()" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400">
                🔄 Reset to System
            </button>
        </div>
    </div>
</div>

<script>
// Update theme status display
function updateThemeStatus() {
    const themeStatus = document.getElementById('theme-status');
    const systemPreference = document.getElementById('system-preference');
    
    if (window.themeManager) {
        themeStatus.textContent = `Current: ${window.themeManager.getTheme()}`;
    } else {
        themeStatus.textContent = 'Theme Manager not loaded';
    }
    
    const isDarkSystem = window.matchMedia('(prefers-color-scheme: dark)').matches;
    systemPreference.textContent = isDarkSystem ? 'Dark' : 'Light';
}

// Listen for theme changes
window.addEventListener('themeChanged', updateThemeStatus);

// Initial update
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(updateThemeStatus, 100);
});
</script>
@endsection
