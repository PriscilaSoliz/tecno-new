<script setup>
import { useNotifications } from '@/Composables/useNotifications';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { notifications, remove, confirmation, handleConfirm } = useNotifications();

const getIcon = (type) => {
    switch (type) {
        case 'success': return 'fas fa-check-circle';
        case 'error': return 'fas fa-exclamation-circle';
        case 'warning': return 'fas fa-exclamation-triangle';
        default: return 'fas fa-info-circle';
    }
};

const getTypeClasses = (type) => {
    switch (type) {
        case 'success': return 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300';
        case 'error': return 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300';
        case 'warning': return 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-300';
        default: return 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300';
    }
};

const getIconColor = (type) => {
    switch (type) {
        case 'success': return 'text-green-500';
        case 'error': return 'text-red-500';
        case 'warning': return 'text-amber-500';
        default: return 'text-blue-500';
    }
};
</script>

<template>
    <!-- TOASTS -->
    <div aria-live="assertive" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-[100] mt-16">
        <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
            <transition-group 
                enter-active-class="transform transition ease-out duration-300"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-for="notification in notifications" :key="notification.id" 
                    :class="[getTypeClasses(notification.type), 'max-w-md w-full shadow-2xl rounded-2xl pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border-l-4']"
                    class="backdrop-blur-md"
                >
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i :class="[getIcon(notification.type), getIconColor(notification.type), 'text-2xl']"></i>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ notification.title }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                    {{ notification.message }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button type="button" @click="remove(notification.id)" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none transition-colors">
                                    <span class="sr-only">Cerrar</span>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-if="notification.duration > 0" class="h-1 bg-black/5 dark:bg-white/5 w-full">
                        <div 
                            class="h-full transition-all linear" 
                            :class="[getIconColor(notification.type), 'bg-current']"
                            :style="{ animation: `shrink ${notification.duration}ms linear forwards` }"
                        ></div>
                    </div>
                </div>
            </transition-group>
        </div>
    </div>

    <!-- CONFIRMATION MODAL -->
    <Modal :show="confirmation.show" @close="handleConfirm(false)" max-width="md">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl overflow-hidden shadow-xl transform transition-all">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-question-circle text-amber-600 dark:text-amber-400 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-6">
                        {{ confirmation.title }}
                    </h3>
                </div>
            </div>
            
            <div class="mt-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ confirmation.message }}
                </p>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row-reverse gap-3">
                <button 
                    @click="handleConfirm(true)"
                    class="sm:flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 shadow-lg shadow-amber-200 transition-all duration-200"
                >
                    Confirmar
                </button>
                <button 
                    @click="handleConfirm(false)"
                    class="sm:flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all duration-200"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
@keyframes shrink {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
