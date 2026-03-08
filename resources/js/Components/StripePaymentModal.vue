<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { loadStripe } from '@stripe/stripe-js';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    show: Boolean,
    amount: Number,
    description: String,
    publicKey: { type: String, default: import.meta.env.VITE_STRIPE_KEY } // Fallback to env
});

const emit = defineEmits(['close', 'success']);

const stripe = ref(null);
const elements = ref(null);
const cardElement = ref(null);
const error = ref(null);
const processing = ref(false);
const clientSecret = ref(null);

// Initialize Stripe
const initStripe = async () => {
    const page = usePage();
    // Prioridad: Prop > Compartido por Inertia > Hardcoded (último recurso)
    const key = props.publicKey || page.props.stripe_key || 'pk_test_51NKuNmKXQy79jQM3CdQKSYriL7l6LHRbImgVTIjADbk4cj2M4PWDaRn0SniYLILxYFatmYiqf2lPdmRdw8woqpPN008IHt2ITz';
    
    console.log('Stripe Key using:', key);
    
    if (!key || key === 'pk_test_sample') {
        error.value = 'Configuración de pago incompleta: Clave de Stripe no encontrada.';
        return;
    }

    stripe.value = await loadStripe(key);

    // Create PaymentIntent per transaction
    try {
        const response = await axios.post(route('stripe.payment-intent'), {
            amount: props.amount,
            description: props.description
        });
        clientSecret.value = response.data.clientSecret;

        elements.value = stripe.value.elements({ clientSecret: clientSecret.value, appearance: { theme: 'stripe' } });
        const paymentElement = elements.value.create('payment');
        paymentElement.mount('#payment-element');

    } catch (e) {
        error.value = 'Error al iniciar pago: ' + (e.response?.data?.error || e.message);
    }
};

const handleSubmit = async () => {
    if (!stripe.value || !elements.value) return;

    processing.value = true;
    error.value = null;

    const { error: stripeError, paymentIntent } = await stripe.value.confirmPayment({
        elements: elements.value,
        confirmParams: {
            return_url: window.location.href, // Not used for redirect-less flow generally but required
        },
        redirect: 'if_required'
    });

    if (stripeError) {
        error.value = stripeError.message;
        processing.value = false;
    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
        emit('success', paymentIntent);
        // Do NOT close automatically, let parent handle it
    } else {
        error.value = 'El estado del pago es: ' + (paymentIntent?.status || 'desconocido');
        processing.value = false;
    }
};

onMounted(() => {
    // Wait for show prop or init manually? usually better to init when modal opens
});

// Watch show prop to init
import { watch } from 'vue';
watch(() => props.show, (val) => {
    if (val) {
        // Reset state
        error.value = null;
        processing.value = false;
        // Small delay to ensure DOM is ready
        setTimeout(initStripe, 100);
    } else {
        // cleanup if needed
        if (elements.value) {
            // elements.value.destroy(); 
        }
    }
});
</script>

<template>
    <DialogModal :show="show" @close="emit('close')" :closeable="false">
        <template #title>
            Pago con Tarjeta
        </template>
        <template #content>
            <div class="mb-4">
                <p class="text-gray-600 mb-2">{{ description }}</p>
                <p class="text-2xl font-bold text-gray-900">Bs {{ amount.toFixed(2) }}</p>
            </div>

            <div v-show="!clientSecret && !error" class="text-center py-4 text-gray-500">
                <i class="fas fa-circle-notch fa-spin mr-2"></i> Cargando pasarela...
            </div>

            <!-- Stripe Element Container -->
            <div id="payment-element" class="mb-4 min-h-[50px]"></div>

            <div v-if="error" class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm font-medium">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ error }}
            </div>
        </template>
        <template #footer>
            <SecondaryButton @click="emit('close')" :disabled="processing">
                Cancelar
            </SecondaryButton>

            <button
                class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                @click="handleSubmit" :disabled="processing || !clientSecret" v-if="!error"
                :class="{ 'opacity-75 cursor-not-allowed': processing || !clientSecret, 'opacity-50': !clientSecret && !processing }">
                <i v-if="processing" class="fas fa-circle-notch fa-spin mr-2"></i>
                {{ processing ? 'Procesando...' : 'Pagar Ahora' }}
            </button>
        </template>
    </DialogModal>
</template>
