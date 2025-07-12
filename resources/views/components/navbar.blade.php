<nav class="bg-white shadow-sm border-b mb-8">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-600 rounded-lg p-2 flex items-center justify-center" style="width:40px; height:40px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="font-bold text-xl leading-tight text-accent">DTR System</div>
                <div class="text-sm text-gray-500 leading-tight">Daily Time Record Management</div>
            </div>
        </div>
        <div class="text-right">
            <div id="navbar-time" class="font-bold text-lg text-primary">--:--:-- --</div>
            <div id="navbar-date" class="text-sm text-primary">--/--/----</div>
        </div>
    </div>
    <script>
        function updateNavbarTime() {
            const now = new Date();
            const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            const date = now.toLocaleDateString();
            document.getElementById('navbar-time').textContent = time;
            document.getElementById('navbar-date').textContent = date;
        }
        updateNavbarTime();
        setInterval(updateNavbarTime, 1000);
    </script>
</nav> 