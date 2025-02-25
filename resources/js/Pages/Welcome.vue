<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome to Wardrobe Manager" />
    
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-indigo-50 to-teal-50 dark:from-gray-900 dark:to-indigo-950">

        <div class="absolute top-20 left-10 w-32 h-32 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob"></div>
        <div class="absolute top-40 right-10 w-40 h-40 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/3 w-36 h-36 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000"></div>
        
        <div class="relative min-h-screen flex flex-col items-center justify-center px-4 sm:px-6">
            <div class="mt-9 mb-8 p-4 bg-white bg-opacity-70 dark:bg-gray-800 dark:bg-opacity-60 rounded-full shadow-lg">
                <div class="text-4xl sm:text-5xl">👔</div>
            </div>
            
            <div class="max-w-2xl w-full text-center ">
                <h1 class="text-4xl sm:text-5xl font-extrabold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-teal-500 dark:from-indigo-400 dark:to-teal-300">
                    Wardrobe Manager
                </h1>
                
                <p class="text-lg sm:text-xl mb-8 text-gray-700 dark:text-gray-300">
                    Organize your clothing collection, create outfits, and elevate your style.
                </p>
                
                <div class="bg-white bg-opacity-80 dark:bg-gray-800 dark:bg-opacity-80 p-6 rounded-xl shadow-lg mb-8 max-w-md mx-auto">
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <Link 
                            href="/login" 
                            class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                        > 
                            Login
                        </Link>
                        <Link 
                            href="/register" 
                            class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5"
                        >
                            Register
                        </Link>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white bg-opacity-70 dark:bg-gray-800 dark:bg-opacity-60 p-4 rounded-lg shadow-md">
                        <div class="text-2xl mb-2">👕</div>
                        <h3 class="font-bold text-indigo-600 dark:text-indigo-400 mb-1">Organize Collection</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Categorize and manage all your clothing items</p>
                    </div>
                    <div class="bg-white bg-opacity-70 dark:bg-gray-800 dark:bg-opacity-60 p-4 rounded-lg shadow-md">
                        <div class="text-2xl mb-2">👗</div>
                        <h3 class="font-bold text-indigo-600 dark:text-indigo-400 mb-1">Create Outfits</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Mix and match to create perfect combinations</p>
                    </div>
                    <div class="bg-white bg-opacity-70 dark:bg-gray-800 dark:bg-opacity-60 p-4 rounded-lg shadow-md">
                        <div class="text-2xl mb-2">📱</div>
                        <h3 class="font-bold text-indigo-600 dark:text-indigo-400 mb-1">Access Anywhere</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Your wardrobe available on all your devices</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-auto text-sm text-gray-500 dark:text-gray-400 pb-4">
                © 2025 Wardrobe Manager. All rights reserved.
            </div>
        </div>
    </div>
</template>
