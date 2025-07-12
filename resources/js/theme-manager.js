/**
 * Unified Theme Management System for DTR System
 * Handles light/dark mode switching with persistence and smooth transitions
 */

class ThemeManager {
    constructor() {
        this.currentTheme = 'light';
        this.storageKey = 'dtr-theme';
        this.transitions = true;
        
        this.init();
    }

    /**
     * Initialize the theme manager
     */
    init() {
        // Enable CSS transitions for smooth theme switching
        this.enableTransitions();
        
        // Load saved theme or detect system preference
        this.loadTheme();
        
        // Apply the theme immediately
        this.applyTheme(this.currentTheme);
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Make theme manager globally available
        window.themeManager = this;
    }

    /**
     * Enable smooth CSS transitions for theme switching
     */
    enableTransitions() {
        const style = document.createElement('style');
        style.textContent = `
            *, *::before, *::after {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
            }
        `;
        document.head.appendChild(style);
        
        // Remove transition after initial load to prevent flash
        setTimeout(() => {
            if (this.transitions) {
                style.remove();
                this.addPermanentTransitions();
            }
        }, 100);
    }

    /**
     * Add permanent transitions for theme elements
     */
    addPermanentTransitions() {
        const style = document.createElement('style');
        style.id = 'theme-transitions';
        style.textContent = `
            body, .card, .bg-white, input, select, textarea, button, .btn {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }
            .theme-toggle-btn {
                transition: all 0.3s ease;
            }
            .theme-toggle-btn:hover {
                transform: scale(1.05);
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Load theme from localStorage or detect system preference
     */
    loadTheme() {
        const savedTheme = localStorage.getItem(this.storageKey);
        
        if (savedTheme && ['light', 'dark'].includes(savedTheme)) {
            this.currentTheme = savedTheme;
        } else {
            // Detect system preference
            this.currentTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
    }

    /**
     * Apply theme to the document
     */
    applyTheme(theme) {
        const html = document.documentElement;
        const body = document.body;
        
        // Remove existing theme classes/attributes
        html.classList.remove('dark', 'light');
        html.removeAttribute('data-theme');
        body.classList.remove('dark', 'light');
        
        // Apply new theme
        if (theme === 'dark') {
            html.classList.add('dark');
            html.setAttribute('data-theme', 'dark');
            body.classList.add('dark');
        } else {
            html.classList.add('light');
            html.setAttribute('data-theme', 'light');
            body.classList.add('light');
        }
        
        // Update current theme
        this.currentTheme = theme;
        
        // Save to localStorage
        localStorage.setItem(this.storageKey, theme);
        
        // Update all theme toggle buttons
        this.updateToggleButtons();
        
        // Dispatch custom event for other components
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { theme: this.currentTheme } 
        }));
    }

    /**
     * Toggle between light and dark themes
     */
    toggle() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme);
    }

    /**
     * Set specific theme
     */
    setTheme(theme) {
        if (['light', 'dark'].includes(theme)) {
            this.applyTheme(theme);
        }
    }

    /**
     * Get current theme
     */
    getTheme() {
        return this.currentTheme;
    }

    /**
     * Check if dark mode is active
     */
    isDark() {
        return this.currentTheme === 'dark';
    }

    /**
     * Update all theme toggle buttons in the document
     */
    updateToggleButtons() {
        // Update Alpine.js reactive data if available
        if (window.Alpine && window.Alpine.store) {
            if (window.Alpine.store('theme')) {
                window.Alpine.store('theme').current = this.currentTheme;
            }
        }
        
        // Update traditional toggle buttons
        const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
        toggleButtons.forEach(button => {
            const icon = button.querySelector('[data-theme-icon]');
            if (icon) {
                icon.textContent = this.currentTheme === 'dark' ? '☀️' : '🌙';
            }
            
            // Update SVG icons
            const sunIcon = button.querySelector('[data-sun-icon]');
            const moonIcon = button.querySelector('[data-moon-icon]');
            
            if (sunIcon && moonIcon) {
                if (this.currentTheme === 'dark') {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                } else {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                }
            }
        });
    }

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Only auto-switch if user hasn't manually set a preference
            if (!localStorage.getItem(this.storageKey)) {
                this.applyTheme(e.matches ? 'dark' : 'light');
            }
        });

        // Set up click handlers for theme toggle buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-theme-toggle]')) {
                e.preventDefault();
                this.toggle();
            }
        });

        // Handle page navigation (for SPAs or dynamic content)
        document.addEventListener('DOMContentLoaded', () => {
            this.updateToggleButtons();
        });
    }

    /**
     * Reset theme to system preference
     */
    resetToSystem() {
        localStorage.removeItem(this.storageKey);
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        this.applyTheme(systemTheme);
    }
}

// Initialize theme manager when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new ThemeManager();
    });
} else {
    new ThemeManager();
}

// Export for module usage
export default ThemeManager;
