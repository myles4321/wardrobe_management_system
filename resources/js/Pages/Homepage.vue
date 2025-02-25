<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const successMessage = ref(usePage().props.flash?.success || '');
const errorMessage = ref(usePage().props.flash?.error || '');

const name = ref('');
const category = ref('');
const image = ref(null);
const clothings = ref(usePage().props.clothings || []);
const showModal = ref(false);
const editingItem = ref(null);

const searchQuery = ref('');
const activeFilter = ref('All');

const fetchClothings = () => {
    axios.get(route('clothings.index'))
        .then(response => {
            console.log("Fetched clothing data:", response.data);
            if (response.data && response.data.clothings) {
                clothings.value = response.data.clothings;
            } else {
                console.error("Unexpected response format:", response.data);
            }
        })
        .catch(error => console.error("Error fetching clothes:", error));
};

const submitForm = () => {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('category', category.value);
    if (image.value) {
        formData.append('image', image.value);
    }

    axios.post(route('clothings.store'), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        console.log("Success response:", response.data);
        console.log("Image path:", response.data.clothing.image);
        
        name.value = '';
        category.value = '';
        image.value = null;
        successMessage.value = 'Item added successfully!';
        setTimeout(() => { successMessage.value = ''; }, 3000);

        fetchClothings();
    })
    .catch(error => {
        console.error("Error adding clothing:", error.response ? error.response.data : error.message);
        errorMessage.value = 'Failed to add item. Please try again.';
        setTimeout(() => { errorMessage.value = ''; }, 3000);
    });
};

const editClothing = (item) => {
    editingItem.value = { ...item }; 
    showModal.value = true;
};

const updateClothing = () => {
    if (!editingItem.value) return;

    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('name', editingItem.value.name);
    formData.append('category', editingItem.value.category);
    if (image.value) {
        formData.append('image', image.value);
    }

    axios.post(route('clothings.update', editingItem.value.id), formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        successMessage.value = 'Item updated successfully!';
        setTimeout(() => { successMessage.value = ''; }, 3000);
        
        const index = clothings.value.findIndex(c => c.id === editingItem.value.id);
        if (index !== -1) {
            clothings.value[index] = response.data.clothing;
        }
        
        showModal.value = false;
        image.value = null;
        
        fetchClothings();
    })
    .catch(error => {
        console.error("Error updating clothing:", error);
        errorMessage.value = 'Failed to update item. Please try again.';
        setTimeout(() => { errorMessage.value = ''; }, 3000);
    });
};

const deleteClothing = (id) => {
    if (confirm('Are you sure you want to delete this item?')) {
        axios.delete(`/clothings/${id}`)
            .then(response => {
                successMessage.value = 'Item deleted successfully!';
                setTimeout(() => { successMessage.value = ''; }, 3000);
                clothings.value = clothings.value.filter(item => item.id !== id);
            })
            .catch(error => {
                console.error("Error deleting clothing:", error);
                errorMessage.value = 'Failed to delete item. Please try again.';
                setTimeout(() => { errorMessage.value = ''; }, 3000);
            });
    }
};

onMounted(() => {
    fetchClothings();
});

const categories = computed(() => {
    const uniqueCategories = [...new Set(clothings.value.map(item => item.category))];
    return ['All', ...uniqueCategories];
});

const filteredClothings = computed(() => {
    let filtered = clothings.value;
    
    if (activeFilter.value !== 'All') {
        filtered = filtered.filter(item => item.category === activeFilter.value);
    }
    
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) || 
            item.category.toLowerCase().includes(query)
        );
    }
    
    return filtered;
});

const categorizedClothings = computed(() => {
    const filtered = filteredClothings.value;
    
    return {
        Hoodies: filtered.filter(item => item.category === 'Hoodies'),
        Shirts: filtered.filter(item => item.category === 'Shirts'),
        TShirts: filtered.filter(item => item.category === 'TShirts'),
        Jeans: filtered.filter(item => item.category === 'Jeans'),
        Cargos: filtered.filter(item => item.category === 'Cargos'),
        Sweatpants: filtered.filter(item => item.category === 'Sweatpants'),
        Shoes: filtered.filter(item => item.category === 'Shoes'),
    };
});

const totalFilteredItems = computed(() => {
    return filteredClothings.value.length;
});

const resetFilters = () => {
    searchQuery.value = '';
    activeFilter.value = 'All';
};

const getCategoryIcon = (category) => {
    switch(category) {
        case 'Hoodies': return '🧥';
        case 'Shirts': return '👔';
        case 'TShirts': return '👕';
        case 'Jeans': return '👖';
        case 'Cargos': return '👖';
        case 'Sweatpants': return '👖';
        case 'Shoes': return '👟';
        default: return '👔';
    }
};

const previewImage = ref(null);

const togglePreview = (imagePath) => {
    previewImage.value = imagePath ? `/storage/${imagePath}` : null;
};

const colors = {
    primary: 'bg-indigo-600 hover:bg-indigo-700',
    secondary: 'bg-teal-500 hover:bg-teal-600',
    danger: 'bg-rose-500 hover:bg-rose-600',
    warning: 'bg-amber-500 hover:bg-amber-600',
    neutral: 'bg-slate-500 hover:bg-slate-600',
};
</script>

<template>
    <Head title="My Wardrobe" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <span class="mr-2">👚</span>
                    My Wardrobe
                </h2>
            </div>
        </template>
        
        <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md shadow transition-all duration-500 ease-in-out">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ successMessage }}</p>
                    </div>
                </div>
            </div>
            
            <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md shadow transition-all duration-500 ease-in-out">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ errorMessage }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
                <div class="p-6 bg-gradient-to-r from-indigo-50 to-teal-50">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Item
                    </h3>
                    
                    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <input v-model="name" id="name" type="text" name="name" placeholder="e.g. Blue T-Shirt" 
                                    class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-md shadow-sm transition" required />
                            </div>
                            
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select v-model="category" id="category" name="category" 
                                    class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-md shadow-sm">
                                    <option value="">Select a category</option>
                                    <option value="Hoodies">Hoodies</option>
                                    <option value="Shirts">Shirts</option>
                                    <option value="TShirts">TShirts</option>
                                    <option value="Jeans">Jeans</option>
                                    <option value="Cargos">Cargos</option>
                                    <option value="Sweatpants">Sweatpants</option>
                                    <option value="Shoes">Shoes</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                            <div class="flex items-center">
                                <label class="cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                    <span>Choose file</span>
                                    <input type="file" @change="e => image = e.target.files[0]" class="sr-only" />
                                </label>
                                <span class="ml-3 text-sm text-gray-500" v-if="image">{{ image.name }}</span>
                                <span class="ml-3 text-sm text-gray-500" v-else>No file chosen</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add to Wardrobe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" v-model="searchQuery" placeholder="Search by name or category..." 
                                class="pl-10 pr-4 py-2 border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md shadow-sm transition" />
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-medium text-gray-700">Filter by:</span>
                            <div class="flex flex-wrap gap-2">
                                <button v-for="cat in categories" :key="cat"
                                    @click="activeFilter = cat"
                                    :class="[
                                        'px-3 py-1 text-sm font-medium rounded-full transition',
                                        activeFilter === cat 
                                            ? 'bg-indigo-600 text-white' 
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                    ]">
                                    {{ cat }}
                                </button>
                            </div>
                            
                            <button v-if="activeFilter !== 'All' || searchQuery" 
                                @click="resetFilters"
                                class="ml-2 text-sm text-indigo-600 hover:text-indigo-700 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="searchQuery || activeFilter !== 'All'" class="mt-4 text-sm text-gray-600">
                        Showing {{ totalFilteredItems }} items
                        <span v-if="searchQuery"> matching "{{ searchQuery }}"</span>
                        <span v-if="activeFilter !== 'All'"> in {{ activeFilter }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">My Collection</h2>
                
                <div v-if="filteredClothings.length === 0" class="bg-gray-50 rounded-lg p-8 text-center">
                    <p class="text-gray-500">No items found matching your search.</p>
                    <button @click="resetFilters" class="mt-2 text-indigo-600 hover:text-indigo-700">
                        Clear all filters
                    </button>
                </div>
                
                <div v-for="(items, category) in categorizedClothings" :key="category" class="mb-10">
                    <div v-if="items.length > 0" class="flex items-center mb-4">
                        <span class="text-3xl mr-3">{{ getCategoryIcon(category) }}</span>
                        <h3 class="text-xl font-bold text-gray-800">{{ category }}</h3>
                        <div class="ml-2 px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">
                            {{ items.length }} items
                        </div>
                    </div>

                    <div v-if="items.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="item in items" :key="item.id" 
                            class="bg-white overflow-hidden rounded-lg border border-gray-200 shadow-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1">
                            <div class="relative">
                                <div v-if="item.image" class="h-48 bg-gray-200">
                                    <img :src="`/storage/${item.image}`" class="w-full h-full object-cover" 
                                        @click="togglePreview(item.image)" />
                                </div>
                                <div v-else class="h-48 bg-gray-100 flex items-center justify-center">
                                    <span class="text-4xl">{{ getCategoryIcon(item.category) }}</span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h4 class="font-semibold text-lg text-gray-900 mb-1">{{ item.name }}</h4>
                                <div class="flex items-center text-sm text-gray-500 mb-3">
                                    <span class="mr-1">{{ getCategoryIcon(item.category) }}</span>
                                    <span>{{ item.category }}</span>
                                </div>
                                
                                <div class="flex justify-between mt-4">
                                    <button @click="editClothing(item)" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded text-white bg-amber-500 hover:bg-amber-600 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    
                                    <button @click="deleteClothing(item.id)" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded text-white bg-rose-500 hover:bg-rose-600 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50">
                <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
                
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 z-10 overflow-hidden transform transition-all">
                    <div class="bg-gradient-to-r from-indigo-600 to-teal-500 py-4 px-6">
                        <h2 class="text-xl font-bold text-white">Edit Item</h2>
                    </div>
                    
                    <form @submit.prevent="updateClothing" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                                <input v-model="editingItem.name" id="edit-name" type="text" placeholder="Item Name" 
                                    class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-md shadow-sm" required />
                            </div>
                            
                            <div>
                                <label for="edit-category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select v-model="editingItem.category" id="edit-category" 
                                    class="border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm rounded-md shadow-sm">
                                    <option value="Hoodies">Hoodies</option>
                                    <option value="Shirts">Shirts</option>
                                    <option value="TShirts">TShirts</option>
                                    <option value="Jeans">Jeans</option>
                                    <option value="Cargos">Cargos</option>
                                    <option value="Sweatpants">Sweatpants</option>
                                    <option value="Shoes">Shoes</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="edit-image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                                <div class="flex items-center">
                                    <label class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                        <span>Replace image</span>
                                        <input type="file" id="edit-image" @change="e => image = e.target.files[0]" class="sr-only" />
                                    </label>
                                    <span class="ml-3 text-sm text-gray-500" v-if="image">{{ image.name }}</span>
                                    <span class="ml-3 text-sm text-gray-500" v-else>Keep current image</span>
                                </div>
                            </div>
                            
                            <div v-if="editingItem && editingItem.image" class="mt-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                                <img :src="`/storage/${editingItem.image}`" class="h-32 w-auto object-cover rounded-md border border-gray-200" />
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="showModal = false" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                Cancel
                            </button>
                            
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                Update Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div v-if="previewImage" class="fixed inset-0 flex items-center justify-center z-50" @click="previewImage = null">
                <div class="absolute inset-0 bg-black bg-opacity-75 backdrop-blur-sm transition-opacity"></div>
                <div class="relative z-10 max-w-4xl max-h-screen p-2">
                    <img :src="previewImage" class="max-h-full max-w-full object-contain rounded-lg" />
                    <button @click="previewImage = null" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>