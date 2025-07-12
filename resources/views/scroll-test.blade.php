@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-4xl font-bold text-primary mb-4">📜 Scroll Test Page</h1>
        <p class="text-secondary">Testing scrolling behavior and layout fixes</p>
    </div>

    <!-- Generate many sections to test scrolling -->
    @for($i = 1; $i <= 20; $i++)
    <div class="card p-6">
        <h2 class="text-2xl font-semibold text-primary mb-4">Section {{ $i }}</h2>
        <p class="text-secondary mb-4">
            This is section {{ $i }} of the scroll test. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
            Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, 
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        </p>
        <p class="text-tertiary mb-4">
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-secondary p-4 rounded-lg">
                <h3 class="font-medium text-primary">Card A</h3>
                <p class="text-secondary text-sm">Some content here</p>
            </div>
            <div class="bg-secondary p-4 rounded-lg">
                <h3 class="font-medium text-primary">Card B</h3>
                <p class="text-secondary text-sm">Some content here</p>
            </div>
            <div class="bg-secondary p-4 rounded-lg">
                <h3 class="font-medium text-primary">Card C</h3>
                <p class="text-secondary text-sm">Some content here</p>
            </div>
        </div>
        
        @if($i % 5 == 0)
        <div class="mt-4 p-4 bg-info rounded-lg">
            <h3 class="font-medium text-inverse">Milestone Section {{ $i }}</h3>
            <p class="text-inverse text-sm">You've scrolled through {{ $i }} sections! Keep going to test the scrolling behavior.</p>
        </div>
        @endif
    </div>
    @endfor

    <!-- Final section -->
    <div class="card p-6 bg-success">
        <h2 class="text-2xl font-semibold text-inverse mb-4">🎉 End of Scroll Test</h2>
        <p class="text-inverse mb-4">
            Congratulations! You've reached the end of the scroll test. If you can see this section and 
            scrolling worked smoothly throughout the page, then the overflow/scrolling issues have been fixed!
        </p>
        <div class="flex flex-wrap gap-4">
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="px-4 py-2 bg-white text-black rounded-lg hover:bg-gray-100">
                🔝 Scroll to Top
            </button>
            <button onclick="window.themeManager?.toggle()" 
                    class="px-4 py-2 bg-white text-black rounded-lg hover:bg-gray-100">
                🎨 Toggle Theme
            </button>
        </div>
    </div>

    <!-- Spacer for bottom padding -->
    <div class="h-20"></div>
</div>

<script>
// Add scroll position indicator
window.addEventListener('scroll', function() {
    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    console.log(`Scroll position: ${scrolled.toFixed(1)}%`);
});

// Test smooth scrolling
function testSmoothScroll() {
    const sections = document.querySelectorAll('.card');
    let currentSection = 0;
    
    function scrollToNext() {
        if (currentSection < sections.length) {
            sections[currentSection].scrollIntoView({ behavior: 'smooth', block: 'center' });
            currentSection++;
            setTimeout(scrollToNext, 2000);
        }
    }
    
    scrollToNext();
}

// Add test button
document.addEventListener('DOMContentLoaded', function() {
    const testButton = document.createElement('button');
    testButton.textContent = '🤖 Auto Scroll Test';
    testButton.className = 'fixed bottom-4 left-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400 z-50';
    testButton.onclick = testSmoothScroll;
    document.body.appendChild(testButton);
});
</script>
@endsection
