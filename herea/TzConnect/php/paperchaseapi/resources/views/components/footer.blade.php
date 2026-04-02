<footer class="bg-gray-900 text-gray-300 py-12 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- About -->
            <div>
                <h3 class="text-white font-bold mb-4">About PaperChase</h3>
                <p class="text-sm">Your comprehensive exam management and resource platform for learning and preparation.</p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-bold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                    <li><a href="{{ route('exams.index') }}" class="hover:text-white">Exams</a></li>
                    <li><a href="{{ route('subjects.index') }}" class="hover:text-white">Subjects</a></li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div>
                <h3 class="text-white font-bold mb-4">Resources</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white">Documentation</a></li>
                    <li><a href="#" class="hover:text-white">API Reference</a></li>
                    <li><a href="#" class="hover:text-white">FAQ</a></li>
                </ul>
            </div>
            
            <!-- Support -->
            <div>
                <h3 class="text-white font-bold mb-4">Support</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white">Help Center</a></li>
                    <li><a href="#" class="hover:text-white">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white">Report Issue</a></li>
                </ul>
            </div>
        </div>
        
        <hr class="border-gray-800 my-8">
        
        <div class="flex justify-between items-center">
            <p class="text-sm">&copy; 2024/2025 PaperChase. All rights reserved.</p>
            <div class="flex space-x-6 text-sm">
                <a href="#" class="hover:text-white">Privacy Policy</a>
                <a href="#" class="hover:text-white">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
